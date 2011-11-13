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
 
 
 
class Review_Api_Core extends Core_Api_Abstract
{
  const IMAGE_WIDTH = 720;
  const IMAGE_HEIGHT = 720;

  const THUMB_WIDTH = 140;
  const THUMB_HEIGHT = 160;
  

  // Select
  /**
   * Gets a paginator for reviews
   *
   * @param Core_Model_Item_Abstract $user The user to get the messages for
   * @return Zend_Paginator
   */
  public function getReviewsPaginator($params = array(), $options = null)
  {
    $paginator = Zend_Paginator::factory($this->getReviewsSelect($params, $options));
    if( !empty($params['page']) )
    {
      $paginator->setCurrentPageNumber($params['page']);
    }
    if( !empty($params['limit']) )
    {
      $paginator->setItemCountPerPage($params['limit']);
    }
    return $paginator;
  }

  /**
   * Gets a select object for the user's review entries
   *
   * @param Core_Model_Item_Abstract $user The user to get the messages for
   * @return Zend_Db_Table_Select
   */
  public function getReviewsSelect($params = array(), $options = null)
  {
    $table = Engine_Api::_()->getDbtable('reviews', 'review');
    $rName = $table->info('name');

    $tagTable = Engine_Api::_()->getDbtable('TagMaps', 'core')->info('name');
    $searchTable = Engine_Api::_()->fields()->getTable('review', 'search')->info('name');

    if (empty($params['order'])) {
      $params['order'] = 'recent';
    }
    /*
    switch ($params['order'])
    {
      case 'random':
        $order_expr = new Zend_Db_Expr('RAND()');
        break;
      case 'recent':
        $order_expr = $rName.".creation_date DESC";
        break;
      case 'ratingdesc':
        $order_expr = $rName.".rating DESC";
        break;
      case 'ratingasc':
        $order_expr = $rName.".rating ASC";
        break;
      case 'mostcommented':
        $order_expr = $rName.".comment_count DESC";
        break;
      case 'mostliked':
        $order_expr = $rName.".like_count DESC";
        break;        
      default:
        $order = $rName.'.'.( !empty($params['order']) ? $params['order'] : 'creation_date' );
        $order_direction = !empty($params['order_direction']) ? $params['order_direction'] : 'DESC';
        $order_expr = "$order $order_direction";
        
      unset($params['order']);  
    }

   // print_r($order_expr);
    
    $select = $table->select()
      ->order( $order_expr );
		*/
    $select = $table->select();
    $select = $table->selectParamBuilder($select, $params);  
      
    // Build search part of query
    $searchParts = Engine_Api::_()->fields()->getSearchQuery('review', $params);
    if (!empty($searchParts))
    {
      $select = $select
        ->setIntegrityCheck(false)
        ->from($rName)
        ->joinLeft($searchTable, "$searchTable.item_id = $rName.review_id")
        ->group("$rName.review_id");     
      foreach( $searchParts as $k => $v ) 
      {
        $select = $select->where("`{$searchTable}`.{$k}", $v);
      }
    }      
    
    if( !empty($params['tag']) )
    {          
      $select = $select
        ->setIntegrityCheck(false)
        ->from($rName)
        ->joinLeft($tagTable, "$tagTable.resource_id = $rName.review_id")
        ->where($tagTable.'.resource_type = ?', 'review')
        ->where($tagTable.'.tag_id = ?', $params['tag']);
    }

    return $select;
  }

  
  public function filterEmptyParams($values)
  {
    foreach ($values as $key => $value)
    {
      if (is_array($value))
      {
        foreach ($value as $value_k => $value_v)
        {
          if (!strlen($value_v))
          {
            unset($value[$value_k]);
          }
        }
      }
      
      if (is_array($value) && count($value) == 0)
      {
        unset($values[$key]);
      }
      else if (!is_array($value) && !strlen($value))
      {
        unset($values[$key]);
      }
    }
    
    return $values;
  }
  
  public function getPopularTags($options = array())
  {
    $tags = Engine_Api::_()->radcodes()->getPopularTags('review', $options);
    return $tags;
  }  
  
  public function hasReviewed($owner, $user)
  {
    return $this->getReviewTable()->getReviewCount(array('owner'=>$owner, 'user'=>$user)) > 0;
  }
  
  
  public function getOwnerReviewForUser($owner, $user)
  {
    return $this->getReviewTable()->getReview(array('owner'=>$owner, 'user'=>$user));
  }
  
  
  public function getUserReviewCount($user)
  {
    return $this->getReviewTable()->getReviewCount(array('user'=>$user));
  }
  
  public function getUserAverageRating($user)
  {
    return $this->getReviewTable()->getAverageRating(array('user'=>$user));
  }
  
  public function getUserReviewDistributions($user)
  {
    return $this->getReviewTable()->getDistributions(array('user'=>$user));
  }
  
  public function getUserRecommendCount($user)
  {
    return $this->getReviewTable()->getReviewCount(array('user'=>$user,'recommend'=>1));
  }
  
  public function getOwnerReviewCount($owner)
  {
    return $this->getReviewTable()->getReviewCount(array('owner'=>$owner));
  }
  
  public function getOwnerAverageRating($owner)
  {
    return $this->getReviewTable()->getAverageRating(array('owner'=>$owner));
  }
  
  public function getOwnerReviewDistributions($owner)
  {
    return $this->getReviewTable()->getDistributions(array('owner'=>$owner));
  }
  
  public function getOwnerRecommendCount($owner)
  {
    return $this->getReviewTable()->getReviewCount(array('owner'=>$owner,'recommend'=>1));
  }  
  
  /***
   * @return Review_Model_DbTable_Reviews
   */
  public function getReviewTable()
  {
    return Engine_Api::_()->getDbtable('reviews', 'review');
  }
  
}