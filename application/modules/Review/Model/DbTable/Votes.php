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
 
 
 
class Review_Model_DbTable_Votes extends Engine_Db_Table
{
  protected $_rowClass = "Review_Model_Vote";
  
  
  public function getVoteTable()
  {
    return $this;
  }
 

  public function updatePreferenceCountKeys(Review_Model_Review $review)
  {
    $select = new Zend_Db_Select($this->getVoteTable()->getAdapter());
    $select
      ->from($this->getVoteTable()->info('name'), array(
      'vote_count' => new Zend_Db_Expr('COUNT(*)'), 
      'helpful_count' => new Zend_Db_Expr('SUM(helpful)')
    ));

    $select->where('review_id = ?', $review->getIdentity());

    $table = $this->getVoteTable();
    $row = $select->query()->fetch();
    
    if (is_array($row))
    {
      $review->vote_count = $row['vote_count'] ? $row['vote_count'] : 0;
      $review->helpful_count = $row['helpful_count'] ? $row['helpful_count'] : 0;
    }
    else 
    {
      $review->vote_count = 0;
      $review->helpful_count = 0;
    }
    
    if ($review->vote_count) {
      $review->helpfulness = (int) ($review->helpful_count / $review->vote_count * 100 * $review->helpful_count - ($review->vote_count - $review->helpful_count));
    }
    else {
      $review->helpfulness = 0;
    }
    
    $review->save();
    
  }
  
  
  public function addVote(Review_Model_Review $review, User_Model_User $user, $helpful)
  {
    $row = $this->getVote($review, $user);
    if( null !== $row )
    {
      throw new Review_Model_Exception('Already voted');
    }

    $table = $this->getVoteTable();
    $row = $table->createRow();

    $row->review_id = $review->getIdentity();
    $row->user_id = $user->getIdentity();
    $row->helpful = $helpful ? 1 : 0;
    $row->save();

    /*
    if( isset($review->vote_count) || ($helpful && isset($review->helpful_count)))
    {
      if (isset($review->vote_count))
      {
        $review->vote_count++;
      }
      if ($helpful && isset($review->helpful_count))
      {
        $review->helpful_count++;
      }
      
      $review->save();
    }
		*/
    $this->updatePreferenceCountKeys($review);
    
    return $row;
  }

  public function removeVote(Review_Model_Review $review, User_Model_User $user)
  {
    $row = $this->getVote($review, $user);
    if( null === $row )
    {
      throw new Review_Model_Exception('No vote to remove');
    }
    
    $row->delete();

    /*
    if( isset($review->vote_count) || ($helpful && isset($review->helpful_count)))
    {
      if (isset($review->vote_count))
      {
        $review->vote_count--;
      }
      if ($helpful && isset($review->helpful_count))
      {
        $review->helpful_count--;
      }
      
      $review->save();
    }
    */
    $this->updatePreferenceCountKeys($review);
    
    return $this;
  }

  
  
  public function isVote(Review_Model_Review $review, User_Model_User $user)
  {
    return ( null !== $this->getVote($review, $user) );
  }

  public function getVote(Review_Model_Review $review, User_Model_User $user)
  {
    $table = $this->getVoteTable();
    $select = $this->getVoteSelect($review)
      ->where('user_id = ?', $user->getIdentity())
      ->limit(1);

    return $table->fetchRow($select);
  }

  public function getVoteSelect(Review_Model_Review $review)
  {
    $select = $this->getVoteTable()->select();

    $select
      ->where('review_id = ?', $review->getIdentity())
      ->order('vote_id ASC');

    return $select;
  }

  public function getVotePaginator(Review_Model_Review $review)
  {
    $paginator = Zend_Paginator::factory($this->getVoteSelect($review));
    $paginator->setItemCountPerPage(3);
    $paginator->count();
    $pages = $paginator->getPageRange();
    $paginator->setCurrentPageNumber($pages);
    return $paginator;
  }

  public function getVoteCount(Review_Model_Review $review, $helpful = null)
  {
    if( isset($review->vote_count) )
    {
      return $review->vote_count;
    }

    $select = new Zend_Db_Select($this->getVoteTable()->getAdapter());
    $select
      ->from($this->getVoteTable()->info('name'), new Zend_Db_Expr('COUNT(1) as count'));

    $select->where('review_id = ?', $review->getIdentity());
    if ($helpful !== null)
    {
      $select->where('helpful = ?', $helpful ? 1 : 0);
    }
    
    $data = $select->query()->fetchAll();
    return (int) $data[0]['count'];
  }

  public function getHelpfulVoteCount(Review_Model_Review $review)
  {
    if( isset($review->helpful_count) )
    {
      return $review->helpful_count;
    }

    return $this->getVoteCount($review, 1);
  }
  
  
  public function getNotHelpfulVoteCount(Review_Model_Review $review)
  {
    return $this->getVoteCount($review) - $this->getHelpfulVoteCount($review);
  }
  
  
  public function getHelpfulness(Review_Model_Review $review)
  {
    if( isset($review->helpfulness) )
    {
      return $review->helpfulness;
    }
    
    $total = $this->getVoteCount($review);
    $helpful = $this->getHelpfulVoteCount($review);
    if ($total)
    {
      $helpfulness = (int) ($helpful / $total * 100);
    }
    else
    {
      $helpfulness = 0;
    }
    
    return $helpfulness;
  }
  
  
  public function getAllVotes(Review_Model_Review $review)
  {
    return $this->getVoteTable()->fetchAll($this->getVoteSelect($review));
  }

  
  public function getAllVotesUsers(Review_Model_Review $review, $helpful = null)
  {
    $table = $this->getVoteTable();
    $select = new Zend_Db_Select($table->getAdapter());
    $select->from($table->info('name'), array('user_id', 'vote'));

    $select->where('review_id = ?', $review->getIdentity());
    if ($helpful !== null)
    {
      $select->where('helpful = ?', $helpful ? 1 : 0);
    }
    
    $users = array();
    foreach( $select->query()->fetchAll() as $data )
    {
      $users[] = $data['user_id'];
    }
    $users = array_values(array_unique($users));

    return Engine_Api::_()->getItemMulti('user', $users);
  }
  
  
}