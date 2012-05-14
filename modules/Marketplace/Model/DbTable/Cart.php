<?php
/**
 * @category   Application_Extensions
 * @package    Marketplace
 * @copyright  Copyright 2010 
 * * 
 */
class Marketplace_Model_DbTable_Cart extends Engine_Db_Table
{
	protected $_rowClass = 'Marketplace_Model_Cart';
	protected $_name = 'marketplace_cart';
	protected $_cookieName = 'marketplacecart';

	public function productIsAlreadyInCart($user_id = 0, $marketplace_id = 0) {
		if($user_id == 0 || $marketplace_id == 0)
			return false;
		
		$select = $this->select()
			->where('marketplace_id = ?', $marketplace_id)
			->where('user_id = ?', $user_id)
		;
		return $this->fetchRow($select);
	}

  public function getCookieCart($sorted = true) {
		$cookie = Zend_Controller_Front::getInstance()->getRequest()->getCookie($this->_cookieName);
    $res = array();
    if( !empty($cookie) ) {
      $items = explode('i', $cookie);
      $tmpres = array();

      foreach($items as $item) {
        $attribs = explode('_', $item);
        if(count($attribs) != 2 )  continue;
        if( !preg_match("/([0-9]+)(c)?/i", $attribs[1], $matches) ) continue;
        $tmpres[$attribs[0]] = array('marketplace_id' => $attribs[0], 'count' => $matches[1], 'inspection' => ((!empty($matches[2])) ? true : false));
      }

      if( $sorted ) foreach($tmpres as $row) $res[] = $row;
      else $res = $tmpres;

    }
    return $res;
	}

  public function productAttribsInCookieCart( $marketplace_id = 0 ) {
    if( $marketplace_id == 0 ) return false;

		$cookie = Zend_Controller_Front::getInstance()->getRequest()->getCookie($this->_cookieName);

    $res = false;
    if( preg_match("/[$marketplace_id]_([0-9]+)(c)?/i", $cookie, $matches) ) {
      $res = array(
          'count' => $matches[1],
          'inspection' => (!empty($matches[2])) ? true : false
      );
    }
    return $res;
	}

	public function productIsAlreadyInCookieCart( $marketplace_id = 0 ) {
    $res = $this->productAttribsInCookieCart( $marketplace_id );
    return (!empty($res)) ? $res['count'] : false;
	}

  public function addToCookieCart( $marketplace_id = 0, $inspection = 0 ) {
		if( $marketplace_id == 0 ) return false;

    $itemsInCookieCart = $this->productIsAlreadyInCookieCart( $marketplace_id );

    if( !$itemsInCookieCart ) $this->updateCookieCartItem( $marketplace_id, -1, $inspection );
    else $this->updateCookieCartItem( $marketplace_id, $itemsInCookieCart + 1, $inspection );
  }

  public function updateCookieCartItem( $marketplace_id = 0, $count, $inspection ) {
		if( $marketplace_id == 0 ) return false;

    // 'c' - 'admin Control, inspection'
    // 'i' - just separator
    $inspectionStr = $inspection ? 'c' : '';
    $cookie = Zend_Controller_Front::getInstance()->getRequest()->getCookie($this->_cookieName);

    if( $count >= 0 and preg_match("/(?<=".$marketplace_id."_)[0-9c]+/i", $cookie, $matches) ) {
      $value = preg_replace("/(?<=".$marketplace_id."_)[0-9c]+/i", $count.$inspectionStr, $cookie);
    }
    else {
      if( empty($cookie) ) $value = $marketplace_id."_1".$inspectionStr;
      else $value = $cookie."i".$marketplace_id."_1".$inspectionStr;
    }

    setcookie($this->_cookieName, $value, time() + 86400, "/"); 
  }

  public function updateCookieCartItemArray( $mids, $inspections ) {
		if( !is_array( $mids ) or empty( $mids ) ) return false;

    $value = Zend_Controller_Front::getInstance()->getRequest()->getCookie($this->_cookieName);
    foreach($mids as $marketplace_id => $count) {

      $inspectionStr = isset($inspections[$marketplace_id]) ? 'c' : '';
      if( $count >= 0 and preg_match("/(?<=".$marketplace_id."_)[0-9c]+/i", $value, $matches) ) {
          $value = preg_replace("/(?<=".$marketplace_id."_)[0-9c]+/i", $count.$inspectionStr, $value);
      }
    }
    setcookie($this->_cookieName, $value, time() + 86400, "/"); 
  }

              

  public function deleteCookieCartItem( $marketplace_id = 0 ) {
    if( $marketplace_id == 0 ) return false;

    $cookie = Zend_Controller_Front::getInstance()->getRequest()->getCookie($this->_cookieName);

    $value = preg_replace("/i?[$marketplace_id]_[0-9c]+/i", '', $cookie);

    setcookie($this->_cookieName, $value, time() + 86400, "/"); 
  }

  public function deleteCookieCartItemArray( $mids ) {
    if( !is_array( $mids ) or empty( $mids ) ) return false;

    $value = Zend_Controller_Front::getInstance()->getRequest()->getCookie($this->_cookieName);
    foreach( $mids as $mid ) {
      $value = preg_replace("/i?[$mid]_[0-9c]+/i", '', $value);
    }

    setcookie($this->_cookieName, $value, time() + 86400, "/"); 
  }

}
