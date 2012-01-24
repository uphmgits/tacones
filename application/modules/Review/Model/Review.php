<?php



/**
 * Radcodes - SocialEngine Module
 *
 * @category   Application_Extensions
 * @package    Review
 * @copyright  Copyright (c) 2009-2010 Radcodes LLC (http://www.radcodes.com)
 * @license    http://www.radcodes.com/license/
 * @version    $Id$
 * @author     Vincent Van <vincent@radcodes.com>
 */
 
 
 
class Review_Model_Review extends Core_Model_Item_Abstract
{
  // Properties

  protected $_parent_type = 'user';
  
  protected $_owner_type = 'user';

  protected $_searchTriggers = array('search', 'title', 'body');

  protected $_parent_is_owner = true;

  
  /**
   * Gets an absolute URL to the page to view this item
   *
   * @return string
   */
  public function getHref($params = array())
  {
    $slug = $this->getSlug();
    
    $params = array_merge(array(
      'route' => 'review_profile',
      'reset' => true,
      'review_id' => $this->review_id,
      'slug' => $slug,
    ), $params);
    $route = $params['route'];
    $reset = $params['reset'];
    unset($params['route']);
    unset($params['reset']);
    return Zend_Controller_Front::getInstance()->getRouter()
      ->assemble($params, $route, $reset);
  }

  
  public function getEditHref($params = array())
  {
    $params = array_merge(array(
      'route' => 'review_specific',
      'reset' => true,
      'review_id' => $this->review_id,
      'action' => 'edit'
    ), $params);
    $route = $params['route'];
    $reset = $params['reset'];
    unset($params['route']);
    unset($params['reset']);
    return Zend_Controller_Front::getInstance()->getRouter()
      ->assemble($params, $route, $reset);
  }
  
  public function getDeleteHref($params = array())
  {
    $params = array_merge(array(
      'route' => 'review_specific',
      'reset' => true,
      'review_id' => $this->review_id,
      'action' => 'delete'
    ), $params);
    $route = $params['route'];
    $reset = $params['reset'];
    unset($params['route']);
    unset($params['reset']);
    return Zend_Controller_Front::getInstance()->getRouter()
      ->assemble($params, $route, $reset);
  }  
  
  
  public function getExcerpt($length=255, $truncate_string='...', $truncate_lastspace=false)
  {
  	$text = strip_tags($this->body);
    return Radcodes_Lib_Helper_Text::truncate($text, $length, $truncate_string, $truncate_lastspace);
  }
  
  
  public function getDescription()
  {
  	return $this->getExcerpt();
  }
  
  public function getUser()
  {
    return Engine_Api::_()->user()->getUser($this->user_id);
  }
  
  public function isUser($user)
  {
    return $this->getUser()->isSelf($user);
  }

  // Interfaces
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

  /**
   * Gets a proxy object for the tags handler
   *
   * @return Engine_ProxyObject
   **/
  public function tags()
  {
    return new Engine_ProxyObject($this, Engine_Api::_()->getDbtable('tags', 'core'));
  }
  

  public function votes()
  {
    return new Engine_ProxyObject($this, Engine_Api::_()->getDbtable('votes', 'review'));
  }
  
  protected function _delete()
  {
    if( $this->_disableHooks ) return;

    // Delete all field values
    $values = Engine_Api::_()->fields()->getFieldsValues($this);
    foreach ($values as $value)
    {
      $value->delete();
    }
    
    // Delete search row
    $search = Engine_Api::_()->fields()->getFieldsSearch($this);
    if ($search)
    {
      $search->delete();
    }
    
    // Delete all tags
    $tagmaps = $this->tags()->getTagMaps();
    foreach ($tagmaps as $tagmap)
    {
      $tagmap->delete();
    }
    
    // Delete all likes
    $likes = $this->likes()->getAllLikes();
    foreach ($likes as $like)
    {
    	$like->delete();
    }
    
    // Delete all votes
    $votes = $this->votes()->getAllVotes();
    foreach ($votes as $vote)
    {
      $vote->delete();
    }
    
    parent::_delete();
  }
  
  
}