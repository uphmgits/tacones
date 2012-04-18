<?php
/**
 * 
 *
 * @category   Application_Extensions
 * @package    Marketplace
 * @copyright  Copyright 2010 
 * * 
 * @version    $Id: Album.php 7244 2010-09-01 01:49:53Z john $
 * @author     John
 */

/**
 * @category   Application_Extensions
 * @package    Marketplace
 * @copyright  Copyright 2010 
 * * 
 */
class Marketplace_Model_Album extends Core_Model_Item_Collection
{
  protected $_parent_type = 'marketplace';

  protected $_owner_type = 'marketplace';

  protected $_children_types = array('marketplace_photo');

  protected $_collectible_type = 'marketplace_photo';

  public function getHref($params = array())
  {
    $params = array_merge(array(
      'route' => 'marketplace_profile',
      'reset' => true,
      'id' => $this->getMarketplace()->getIdentity(),
    ), $params);
    $route = $params['route'];
    $reset = $params['reset'];
    unset($params['route']);
    unset($params['reset']);
    return Zend_Controller_Front::getInstance()->getRouter()
      ->assemble($params, $route, $reset);
  }

  public function getMarketplace()
  {
    return $this->getOwner();
  }

  public function getAuthorizationItem()
  {
    return $this->getParent('marketplace');
  }

  protected function _delete()
  {
    // Delete all child posts
    $photoTable = Engine_Api::_()->getItemTable('marketplace_photo');
    $photoSelect = $photoTable->select()->where('album_id = ?', $this->getIdentity());
    foreach( $photoTable->fetchAll($photoSelect) as $marketplacePhoto ) {
      $marketplacePhoto->delete();
    }

    parent::_delete();
  }
}