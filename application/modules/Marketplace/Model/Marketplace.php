<?php
/**
 * 
 *
 * @category   Application_Extensions
 * @package    Marketplace
 * @copyright  Copyright 2010 
 * * 
 * @version    $Id: Marketplace.php 7244 2010-09-01 01:49:53Z john $
 * 
 */

/**
 * @category   Application_Extensions
 * @package    Marketplace
 * @copyright  Copyright 2010 
 * * 
 */
class Marketplace_Model_Marketplace extends Core_Model_Item_Abstract
{
  // Properties

  protected $_parent_type = 'user';

  protected $_searchColumns = array('title', 'body');

  protected $_parent_is_owner = true;


  // General

  /**
   * Gets an absolute URL to the page to view this item
   *
   * @return string
   */
  public function getHref($params = array())
  {
    $params = array_merge(array('user_id' => $this->owner_id, 'marketplace_id' => $this->marketplace_id), $params);
    return Zend_Controller_Front::getInstance()->getRouter()
      ->assemble($params, 'marketplace_entry_view', true);
  }

  public function getDescription()
  {
    // @todo decide how we want to handle multibyte string functions
    $tmpBody = strip_tags($this->body);
    return ( Engine_String::strlen($tmpBody) > 255 ? Engine_String::substr($tmpBody, 0, 255) . '...' : $tmpBody );
  }

  public function getKeywords($separator = ' ')
  {
    $keywords = array();
    foreach( $this->tags()->getTagMaps() as $tagmap ) {
      $tag = $tagmap->getTag();
      $keywords[] = $tag->getTitle();
    }

    if( null === $separator ) {
      return $keywords;
    }

    return join($separator, $keywords);
  }
  public function setPhotoFromURL($photopath) {
  	$this->setPhoto($photopath, true);
  }
  public function setPhoto($photo, $url=false)
  {
    if( $photo instanceof Zend_Form_Element_File ) {
      $file = $photo->getFileName();
    } else if( is_array($photo) && !empty($photo['tmp_name']) ) {
      $file = $photo['tmp_name'];
    } else if( is_string($photo) && file_exists($photo) ) {
      $file = $photo;
    } else if(is_string($photo) && $url == true) {
    	$file = $photo;
    } 
    else {
      throw new Marketplace_Model_Exception('invalid argument passed to setPhoto');
    }
    if($url == false) {
    	$name = basename($file);
    }
   else {
    	// create a random file name for image being pulled from outside url such as eBay
    	$l = 10;
    	$c = "ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxwz0123456789";
	    for(;$l > 0;$l--) $s .= $c{rand(0,strlen($c))};
	    $name = str_shuffle($s). ".jpg";
    }
    
    $path = APPLICATION_PATH . DIRECTORY_SEPARATOR . 'temporary';
    $params = array(
      'parent_type' => 'marketplace',
      'parent_id' => $this->getIdentity()
    );

    // Save
    $storage = Engine_Api::_()->storage();

    // Resize image (main)
    $image = Engine_Image::factory();
    $image->open($file)
      ->resize(720, 720)
      ->write($path.'/m_'.$name)
      ->destroy();

    // Resize image (profile)
    $image = Engine_Image::factory();
    $image->open($file)
      ->resize(200, 400)
      ->write($path.'/p_'.$name)
      ->destroy();

    // Resize image (normal)
    $image = Engine_Image::factory();
    $image->open($file)
      ->resize(140, 160)
      ->write($path.'/in_'.$name)
      ->destroy();

    // Resize image (icon)
    $image = Engine_Image::factory();
    $image->open($file);

    $size = min($image->height, $image->width);
    $x = ($image->width - $size) / 2;
    $y = ($image->height - $size) / 2;

    $image->resample($x, $y, $size, $size, 48, 48)
      ->write($path.'/is_'.$name)
      ->destroy();

    // Store
    $iMain = $storage->create($path.'/m_'.$name, $params);
    $iProfile = $storage->create($path.'/p_'.$name, $params);
    $iIconNormal = $storage->create($path.'/in_'.$name, $params);
    $iSquare = $storage->create($path.'/is_'.$name, $params);

    $iMain->bridge($iProfile, 'thumb.profile');
    $iMain->bridge($iIconNormal, 'thumb.normal');
    $iMain->bridge($iSquare, 'thumb.icon');

    // Remove temp files
    @unlink($path.'/p_'.$name);
    @unlink($path.'/m_'.$name);
    @unlink($path.'/in_'.$name);
    @unlink($path.'/is_'.$name);


    // Add to album
    $viewer = Engine_Api::_()->user()->getViewer();
    $photoTable = Engine_Api::_()->getItemTable('marketplace_photo');
    $marketplaceAlbum = $this->getSingletonAlbum();
    $photoItem = $photoTable->createRow();
    $photoItem->setFromArray(array(
      'marketplace_id' => $this->getIdentity(),
      'album_id' => $marketplaceAlbum->getIdentity(),
      'user_id' => $viewer->getIdentity(),
      'file_id' => $iMain->getIdentity(),
      'collection_id' => $marketplaceAlbum->getIdentity(),
    ));
    $photoItem->save();

    // Update row
    $this->modified_date = date('Y-m-d H:i:s');
    $this->photo_id = $photoItem->file_id;
    $this->save();

    return $this;
  }

  public function getPhoto($photo_id)
  {
    $photoTable = Engine_Api::_()->getItemTable('marketplace_photo');
    $select = $photoTable->select()
      ->where('file_id = ?', $photo_id)
      ->limit(1);

    $photo = $photoTable->fetchRow($select);
    return $photo;
  }
  

  public function getPhotoUrl($type = null)
  {
    if( empty($this->photo_id) )
    {
      return null;
    }

    $photosTable = Engine_Api::_()->getDbtable('photos', 'marketplace');
    $photo = $photosTable->select()->where('file_id = ?', $this->photo_id)->query()->fetch();

    if( !empty($photo) and isset($photo['approved_photo']) and empty($photo['approved_photo']) ) {
      $viewer = Engine_Api::_()->user()->getViewer();
      if( !$viewer->getIdentity() ) return null;
      if( $viewer->level_id > 2 and $viewer->getIdentity() != $photo['user_id'] ) return null;
    }

    $file = Engine_Api::_()->getApi('storage', 'storage')->get($this->photo_id, $type);
    if( !$file ) {
      return null;
    }

    return $file->map();
  }

  public function getSingletonAlbum()
  {
    $table = Engine_Api::_()->getItemTable('marketplace_album');
    $select = $table->select()
      ->where('marketplace_id = ?', $this->getIdentity())
      ->order('album_id ASC')
      ->limit(1);

    $album = $table->fetchRow($select);

    if( null === $album )
   {
      $album = $table->createRow();
      $album->setFromArray(array(
        'title' => $this->getTitle(),
        'marketplace_id' => $this->getIdentity()
      ));
      $album->save();
    }

    return $album;
  }


  public function isLike( $viewer ) {
      $likesTable = Engine_Api::_()->getDbtable('likes', 'core');
      return $likesTable->isLike( $this, $viewer );
  }

  public function updateLikes() {
      $viewer = Engine_Api::_()->user()->getViewer();
      if( !$viewer ) return;

      $likesTable = Engine_Api::_()->getDbtable('likes', 'core');

      if( $likesTable->isLike( $this, $viewer ) ) {
          $likesTable->removeLike( $this, $viewer );
      }
      else {
          $likesTable->addLike( $this, $viewer );     
      }
  }

  public function getLikeCount() {
      return Engine_Api::_()->getDbtable('likes', 'core')->getLikeCount( $this );
  }

  public function canView( $viewer ) {

      if( $viewer->getIdentity() and ( $viewer->isSelf($this->getOwner()) or $viewer->level_id <= 2) ) return true;

      $photosTable = Engine_Api::_()->getDbtable('photos', 'marketplace');
      $photosTableName = $photosTable->info('name');
      $res = $photosTable->select()
                  ->where('approved_photo = 0')
                  ->where("marketplace_id = {$this->getIdentity()}")
                  ->query()  
                  ->fetch()
      ;
      if( empty($res) ) return true;

      return false;
      
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
  
  protected function _insert()
  {
    if( null === $this->search ) {
      $this->search = 1;
    }

    parent::_insert();
  }


  protected function _delete()
  {
	if(Engine_Api::_()->marketplace()->cartIsActive()){
		// Delete all child posts
		$cartTable = Engine_Api::_()->getDbtable('cart', 'marketplace');
		$cartTable->delete(array('marketplace_id = ?' => $this->getIdentity()));
	}

    parent::_delete();
  }

    
}
