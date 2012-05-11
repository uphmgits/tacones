<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Poll
 * @copyright  Copyright 2006-2010 Webligo Developments
 * @license    http://www.socialengine.net/license/
 * @version    $Id: Poll.php 9093 2011-07-22 02:06:14Z john $
 * @author     Steve
 */

/**
 * @category   Application_Extensions
 * @package    Poll
 * @copyright  Copyright 2006-2010 Webligo Developments
 * @license    http://www.socialengine.net/license/
 */
class Poll_Model_Poll extends Core_Model_Item_Abstract
{
  protected $_parent_type = 'user';

  protected $_parent_is_owner = true;

  
  // Interfaces
  /**
   * Gets an absolute URL to the page to view this item
   *
   * @return string
   */
  public function getHref($params = array())
  {
    $params = array_merge(array(
      'route' => 'poll_view',
      'reset' => true,
      'user_id' => $this->user_id,
      'poll_id' => $this->poll_id,
      'slug' => $this->getSlug(),
    ), $params);
    $route = $params['route'];
    $reset = $params['reset'];
    unset($params['route']);
    unset($params['reset']);
    return Zend_Controller_Front::getInstance()->getRouter()
      ->assemble($params, $route, $reset);
  }

  public function getHiddenSearchData()
  {
    $optionsTable = Engine_Api::_()->getDbTable('options', 'poll');
    $options = $optionsTable
      ->select()
      ->from($optionsTable->info('name'), 'poll_option')
      ->where('poll_id = ?', $this->getIdentity())
      ->query()
      ->fetchAll(Zend_Db::FETCH_COLUMN);

    return join(' ', $options);
  }

  public function getRichContent()
  {
    $view = Zend_Registry::get('Zend_View');
    $view = clone $view;
    $view->clearVars();
    $view->addScriptPath('application/modules/Poll/views/scripts/');
    
    $content = '';
    $content .= '
      <div class="feed_poll_rich_content">
        <div class="feed_item_link_title">
          ' . $view->htmlLink($this->getHref(), $this->getTitle()) . '
        </div>
        <div class="feed_item_link_desc">
          ' . $view->viewMore($this->getDescription()) . '
        </div>
    ';

    // Render the thingy
    $view->poll = $this;
    $view->owner = $owner = $this->getOwner();
    $view->viewer = $viewer = Engine_Api::_()->user()->getViewer();
    $view->pollOptions = $this->getOptions();
    $view->hasVoted = $this->viewerVoted();
    $view->showPieChart = Engine_Api::_()->getApi('settings', 'core')->getSetting('poll.showpiechart', false);
    $view->canVote = $this->authorization()->isAllowed(null, 'vote');
    $view->canChangeVote = Engine_Api::_()->getApi('settings', 'core')->getSetting('poll.canchangevote', false);
    $view->hideLinks = true;

    $content .= $view->render('_poll.tpl');

    /* $content .= '
    <div class="poll_stats">
    '; */

    $content .= '
      </div>
    ';
    return $content;
  }
  
  public function getOptions()
  {
    return Engine_Api::_()->getDbtable('options', 'poll')->fetchAll(array(
      'poll_id = ?' => $this->getIdentity(),
    ));
  }
  
  public function getOptionsAssoc()
  {
    $optionTable = Engine_Api::_()->getDbtable('options', 'poll');
    $data = $optionTable
        ->select()
        ->from($optionTable, array('option_id', 'poll_option'))
        ->where('poll_id = ?', $this->getIdentity())
        ->query()
        ->fetchAll();
    
    $options = array();
    foreach( $data as $datum ) {
      $options[$datum['option_id']] = $datum['poll_option'];
    }
    
    return $options;
  }

  public function hasVoted(User_Model_User $user)
  {
    $table = Engine_Api::_()->getDbtable('votes', 'poll');
    return (bool) $table
      ->select()
      ->from($table, 'COUNT(*)')
      ->where('poll_id = ?', $this->getIdentity())
      ->where('user_id = ?', $user->getIdentity())
      ->query()
      ->fetchColumn(0)
      ;
  }

  public function getVote(User_Model_User $user)
  {
    $table = Engine_Api::_()->getDbtable('votes', 'poll');
    return $table
      ->select()
      ->from($table, 'poll_option_id')
      ->where('poll_id = ?', $this->getIdentity())
      ->where('user_id = ?', $user->getIdentity())
      ->query()
      ->fetchColumn(0)
      ;
  }

  public function getVoteCount($recheck = false)
  {
    if( $recheck ) {
      $table = Engine_Api::_()->getDbtable('votes', 'poll');
      $voteCount = $table->select()
        ->from($table, 'COUNT(*)')
        ->where('poll_id = ?', $this->getIdentity())
        ->query()
        ->fetchColumn(0)
        ;
      if( $voteCount != $this->vote_count ) {
        $this->vote_count = $voteCount;
        $this->save();
      }
    }
    return $this->vote_count;
  }

  public function viewerVoted()
  {
    $viewer = Engine_Api::_()->user()->getViewer();
    return $this->getVote($viewer);
  }

  public function vote(User_Model_User $user, $option)
  {
    $votesTable = Engine_Api::_()->getDbTable('votes', 'poll');
    $optionsTable = Engine_Api::_()->getDbtable('options', 'poll');

    // Get poll option
    if( is_numeric($option) ) {
      $option = $optionsTable->find((int) $option)->current();
    }
    if( !($option instanceof Zend_Db_Table_Row_Abstract) ) {
      throw new Engine_Exception('Missing or invalid poll option');
    }

    // Check for existing vote
    $vote = $votesTable->fetchRow(array(
      'poll_id = ?' => $this->getIdentity(),
      'user_id = ?' => $user->getIdentity(),
    ));

    // New vote is the same as old vote, ignore
    if( $vote &&
        $option->poll_option_id == $vote->poll_option_id ) {
      return $this;
    }

    if( !$vote ) {
      // Vote did not exist

      // Create vote
      $vote = $votesTable->createRow();
      $vote->setFromArray(array(
        'poll_id' => $this->getIdentity(),
        'user_id' => $user->getIdentity(),
        'poll_option_id' => $option->poll_option_id,
        'creation_date' => date("Y-m-d H:i:s"),
        'modified_date' => date("Y-m-d H:i:s"),
      ));
      $vote->save();

      // Increment new option count
      $optionsTable->update(array(
        'votes' => new Zend_Db_Expr('votes + 1'),
      ), array(
        'poll_id = ?' => $this->getIdentity(),
        'poll_option_id = ?' => $vote->poll_option_id,
      ));

      // Update internal vote count
      $this->vote_count = new Zend_Db_Expr('vote_count + 1');
      $this->save();
    } else {
      // Vote did exist

      // Decrement old option count
      $optionsTable->update(array(
        'votes' => new Zend_Db_Expr('votes - 1'),
      ), array(
        'poll_id = ?' => $this->getIdentity(),
        'poll_option_id = ?' => $vote->poll_option_id,
      ));
      
      // Change vote
      $vote->poll_option_id = $option->poll_option_id;
      $vote->modified_date  = date("Y-m-d H:i:s");
      $vote->save();

      // Increment new option count
      $optionsTable->update(array(
        'votes' => new Zend_Db_Expr('votes + 1'),
      ), array(
        'poll_id = ?' => $this->getIdentity(),
        'poll_option_id = ?' => $vote->poll_option_id,
      ));
    }

    
    // Recheck all options?
    /*
    // Note: this doesn't seem to work because we're in a transaction -_-
    $subselect = $table->select()
      ->from($table, 'COUNT(*)')
      ->where('poll_id = ?', $this->getIdentity())
      ->where('poll_option_id = ?', new Zend_Db_Expr($optionsTable->info('name') . '.poll_option_id'))
      ;
    $optionsTable->update(array(
      'votes' => $subselect,
    ), array(
      'poll_id = ?' => $this->getIdentity(),
    ));
     * 
     */

    return $this;
  }

  protected function _insert()
  {
    if( null === $this->search ) {
      $this->search = 1;
    }

    parent::_insert();
  }

  protected function _delete()
  {
    // delete poll votes
    Engine_Api::_()->getDbtable('votes', 'poll')->delete(array(
      'poll_id = ?' => $this->getIdentity(),
    ));

    // delete poll options
    Engine_Api::_()->getDbtable('options', 'poll')->delete(array(
      'poll_id = ?' => $this->getIdentity(),
    ));

    parent::_delete();
  }

  /**
   * Gets a proxy object for the comment handler
   *
   * @return Engine_ProxyObject
   **/
  public function comments()
  {
    return new Engine_ProxyObject($this, Engine_Api::_()->getDbtable('comments', 'core'));
  }

  /**
   * Gets a proxy object for the like handler
   *
   * @return Engine_ProxyObject
   **/
  public function likes()
  {
    return new Engine_ProxyObject($this, Engine_Api::_()->getDbtable('likes', 'core'));
  }
}