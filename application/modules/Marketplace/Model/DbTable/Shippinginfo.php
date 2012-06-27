<?php
/**
 * @category   Application_Extensions
 * @package    Marketplace
 * @copyright  Copyright 2012 
 * * 
 */
class Marketplace_Model_DbTable_Shippinginfo extends Engine_Db_Table {

	protected $_name = 'marketplace_shippinginfo';

  public function saveInfo($values = array()) {
      
      $viewer = Engine_Api::_()->user()->getViewer();
      if( empty($values) and !$viewer->getIdentity() ) return;

      $data = array(
        'name' => isset($values['name']) ? $values['name'] : '',
        'email' => isset($values['email']) ? $values['email'] : '',
        'billing_address' => isset($values['billing_address']) ? $values['billing_address'] : '',
        'shipping_address' => isset($values['shipping_address']) ? $values['shipping_address'] : '',
      );
      if( isset($values['phone']) ) $data['phone'] = $values['phone'];
      if( isset($values['cell_phone']) ) $data['cell_phone'] = $values['cell_phone'];

      $viewerId = $viewer->getIdentity();
      $res = $this->select()->where("user_id = {$viewerId} AND paid = 0")->query()->fetch();
      if( $res ) {
          $this->update($data, "user_id = {$viewerId} and paid = 0");
      } else {
          $data['user_id'] = $viewer->getIdentity();
          $this->insert($data);
          $res = $this->select("user_id = {$viewerId} and paid = 0")->order('shippinginfo_id DESC')->query()->fetch();
      }
      $id = $res['shippinginfo_id'];
      return $id;
  }

  public function getInfo($paid = false) {
      $viewer = Engine_Api::_()->user()->getViewer();
      if( !$viewer->getIdentity() ) return array();

      $paidValue = $paid ? 1 : 0;
      $res = $this->select()->where("user_id = {$viewer->getIdentity()} and paid = {$paidValue}")->order('shippinginfo_id DESC')->query()->fetch();
      if( !$paidValue and !$res ) {
          $res = $this->select()->where("user_id = {$viewer->getIdentity()}")->order('shippinginfo_id DESC')->query()->fetch();
      }
      return $res; 
  }

}
