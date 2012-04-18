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
 
 
 
class Review_Model_DbTable_Reviews extends Engine_Db_Table
{
  protected $_rowClass = "Review_Model_Review";
  
  
  public function selectParamBuilder(Zend_Db_Select $select, $params = array())
  {
    $tablename = $this->info('name');
    if (isset($params['user'])) 
    {
      $user = Engine_Api::_()->user()->getUser($params['user']);
      $select->where($tablename.'.user_id = ?', $user->getIdentity());
    }
    if (isset($params['owner'])) 
    {
      $owner = Engine_Api::_()->user()->getUser($params['owner']);
      $select->where($tablename.'.owner_id = ?', $owner->getIdentity());
    }
    if (isset($params['featured'])) 
    {
      $select->where($tablename.'.featured = ?', $params['featured'] ? 1 : 0);
    }
    if (isset($params['rating']) && $params['rating'] > 0) 
    {
      $select->where($tablename.'.rating = ?', (int)$params['rating']);
    }
    if (isset($params['recommend'])) 
    {
      $select->where($tablename.'.recommend = ?', $params['recommend'] ? 1 : 0);
    }    
    if( !empty($params['keyword']) )
    {
      $select->where($tablename.".title LIKE ? OR ".$tablename.".body LIKE ? OR ".$tablename.".pros LIKE ? OR ".$tablename.".cons LIKE ?", '%'.$params['keyword'].'%');
    }

    if (isset($params['order'])) 
    {
      switch ($params['order'])
      {
        case 'random':
          $order_expr = new Zend_Db_Expr('RAND()');
          break;
        case 'recent':
          $order_expr = $tablename.".creation_date DESC";
          break;
        case 'ratingdesc':
          $order_expr = $tablename.".rating DESC";
          break;
        case 'ratingasc':
          $order_expr = $tablename.".rating ASC";
          break;
        case 'mostcommented':
          $order_expr = $tablename.".comment_count DESC";
          break;
        case 'mostliked':
          $order_expr = $tablename.".like_count DESC";
          break;  
        case 'mosthelpful':
          $order_expr = $tablename.".helpful_count DESC";
          break;
        case 'helpfulnessdesc':
          $order_expr = $tablename.".helpfulness DESC";
          break;   
        default:
          $order = $tablename.'.'.( !empty($params['order']) ? $params['order'] : 'creation_date' );
          $order_direction = !empty($params['order_direction']) ? $params['order_direction'] : 'DESC';
          $order_expr = "$order $order_direction";
          
        unset($params['order']);  
      }

   // print_r($order_expr);
    
      $select->order( $order_expr );
      unset($params['order']);
    }    
    
    return $select;
  }
  
  
  public function getReviewCount($params = array())
  {
    $select = new Zend_Db_Select($this->getAdapter());
    $select->from($this->info('name'), new Zend_Db_Expr('COUNT(*) AS total'));  
    
    $select = $this->selectParamBuilder($select, $params);
       
    return $select->query()->fetchColumn(0);   
  }
  
  
  public function getSumRating($params = array())
  {
    return $this->getSumColumn('rating', $params);
  }
  
  
  public function getSumColumn($column, $params)
  {
    $select = new Zend_Db_Select($this->getAdapter());
    $select->from($this->info('name'), new Zend_Db_Expr("SUM($column) AS total"));
           
    $select = $this->selectParamBuilder($select, $params);
    
    return $select->query()->fetchColumn(0);
  }
  
  
  public function getAverageRating($params = array())
  {
    $count = $this->getReviewCount($params);
    $sum = $this->getSumRating($params);
    return ($count) ? $sum / $count : 0;
  }
  
  
  public function getAverageRatingUsers($params = array())
  {
    $total_review = $this->getReviewCount();
    $sum_ratings = $this->getSumRating();
    
    $params = array_merge($params, array('total_expression'=>"($sum_ratings + sum(rating)) / ($total_review + count(*))"));
    
    return $this->_getColumnsReviewCount('user_id', $params);
  }
  
  protected function _getColumnsReviewCount($column, $params = array())
  {
    $total_expression = 'COUNT(*)';
    if (isset($params['total_expression'])) {
      $total_expression = $params['total_expression'];
      unset($params['total_expression']);
    }
    
    $select = new Zend_Db_Select($this->getAdapter());
    $select->from($this->info('name'), array('key' => $column, 'total' => new Zend_Db_Expr($total_expression)));
    $select->group($column);
    
    if (isset($params['order'])) {
      $select->order($params['order']);
      unset($params['order']);
    }
    else {
      $select->order('total desc');
    }
    
    if (isset($params['limit'])) {
      $select->limit($params['limit']);
      unset($params['limit']);
    }
    
    $select = $this->selectParamBuilder($select, $params);
    
    $rows = $select->query()->fetchAll();
    
    $result = array();
    foreach ($rows as $row) {
      $result[$row['key']] = $row['total'];
    }
    
    return $result;
  }
  
  
  public function getDistributions($params = array())
  {
    return $this->_getColumnsReviewCount('rating', $params);
  }    
  
  
  public function getUsersReviewCount($params = array())
  {
    return $this->_getColumnsReviewCount('user_id', $params);
  }
  
  
  public function getOwnersReviewCount($params = array())
  {
    return $this->_getColumnsReviewCount('owner_id', $params);
  }
  
  
  public function hasReview($params = array())
  {
    $count = $this->getReviewCount($params);
    return $count > 0;
  }
  
  public function getReview($params = array())
  {
    $select = $this->selectParamBuilder($this->select(), $params);
    $select->limit(1);
    $review = $this->fetchRow($select);
    return $review;
  }
  
 
}