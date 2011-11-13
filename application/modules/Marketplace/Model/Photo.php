<?php
/**
 * 
 *
 * @category   Application_Extensions
 * @package    Marketplace
 * @copyright  Copyright 2010 
 * * 
 * @version    $Id: Photo.php 7244 2010-09-01 01:49:53Z john $
 * 
 */

/**
 * @category   Application_Extensions
 * @package    Marketplace
 * @copyright  Copyright 2010 
 * * 
 */
class Marketplace_Model_Photo extends Core_Model_Item_Collectible
{
  protected $_parent_type = 'marketplace_album';

  protected $_owner_type = 'user';

  protected $_collection_type = 'marketplace_album';

  public function getHref($params = array())
  {
    $params = array_merge(array(
      'route' => 'marketplace_extended',
      'reset' => true,
      'controller' => 'photo',
      'action' => 'view',
      'marketplace_id' => $this->getCollection()->getOwner()->getIdentity(),
      'photo_id' => $this->getIdentity(),
    ), $params);
    $route = $params['route'];
    $reset = $params['reset'];
    unset($params['route']);
    unset($params['reset']);
    return Zend_Controller_Front::getInstance()->getRouter()
      ->assemble($params, $route, $reset);
  }

  public function getPhotoUrl($type = null)
  {
    if( empty($this->file_id) )
    {
      return null;
    }

    $file = Engine_Api::_()->getApi('storage', 'storage')->get($this->file_id, $type);
    if( !$file )
    {
      return null;
    }

    return $file->map();
  }

  public function getMarketplace()
  {
    return Engine_Api::_()->getItem('marketplace', $this->marketplace_id);
  }

  public function isSearchable()
  {
    $collection = $this->getCollection();
    if( !$collection instanceof Core_Model_Item_Abstract )
    {
      return false;
    }
    return $collection->isSearchable();
  }

  public function getAuthorizationItem()
  {
    return $this->getParent('marketplace');
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

  /**
   * Gets a proxy object for the tags handler
   *
   * @return Engine_ProxyObject
   **/
  public function tags()
  {
    return new Engine_ProxyObject($this, Engine_Api::_()->getDbtable('tags', 'core'));
  }

  protected function _postDelete()
  {
    if( $this->_disableHooks ) return;

    // This is dangerous, what if something throws an exception in postDelete
    // after the files are deleted?
    try
    {
      $file = Engine_Api::_()->getApi('storage', 'storage')->get($this->file_id);
      $file->remove();
      $file = Engine_Api::_()->getApi('storage', 'storage')->get($this->file_id, 'thumb.normal');
      $file->remove();

      $album = $this->getCollection();

      if( (int) $album->photo_id == (int) $this->getIdentity() )
      {
	$album->photo_id = $this->getNextCollectible()->getIdentity();
	$album->save();
      }
    }
    catch( Exception $e )
    {
      // @todo completely silencing them probably isn't good enough
      //throw $e;
    }
  }
}