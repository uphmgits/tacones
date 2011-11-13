<?php
/**
 * SocialEngine
 *
 * @category   Application_Widget
 * @package    Rss
 * @copyright  Copyright 2006-2010 Webligo Developments
 * @license    http://www.socialengine.net/license/
 * @version    $Id: Controller.php 9339 2011-09-29 23:03:01Z john $
 * @author     John
 */

/**
 * @category   Application_Widget
 * @package    Rss
 * @copyright  Copyright 2006-2010 Webligo Developments
 * @license    http://www.socialengine.net/license/
 */
class Widget_RssController extends Engine_Content_Widget_Abstract
{
  public function indexAction()
  {
    // Zend_Feed requires DOMDocument
    if( !class_exists('DOMDocument', false) ) {
      return $this->setNoRender();
    }

    // Get params
    $url = $this->_getParam('url');
    if( !$url ) {
      return $this->setNoRender();
    }
    $this->view->url = $url;
    $this->view->max = $max = $this->_getParam('max', 4);
    $this->view->strip = $strip = $this->_getParam('strip', true);
    $cacheTimeout = (int) $this->_getParam('timeout');

    // Caching
    $cache = Zend_Registry::get('Zend_Cache');
    if( $cache instanceof Zend_Cache_Core &&
        $cacheTimeout > 0 ) {
      $cacheId = get_class($this) . md5($url . $max . $strip);
      $channel = $cache->load($cacheId);
      if( !is_array($channel) || empty($channel) ) {
        $channel = null;
      } else if( time() > $channel['fetched'] + $cacheTimeout ) {
        $channel = null;
      }
    } else {
      $cacheId = null;
      $channel = null;
    }

    if( !$channel ) {
      // Parse feed
      $rss = Zend_Feed::import($url);

      // Prepare channel info
      $channel = array(
        'title'       => $rss->title(),
        'link'        => null,
        'description' => $rss->description(),
        'items'       => array(),
        'fetched'     => time(),
      );

      // Get link
      $link = $rss->link('self');
      if( $link ) {
        if( $link instanceof DOMElement ) {
          $channel['link'] = $link->nodeValue;
        } else if( is_array($link) ) {
          foreach( $link as $subLink ) {
            $channel['link'] = $subLink->nodeValue;
            if( !empty($channel['link']) ) {
              break;
            }
          }
        }
      }
      
      // Loop over each channel item and store relevant data
      $count = 0;
      foreach( $rss as $item ) {
        if( $count++ >= $max ) break;
        $channel['items'][] = array(
          'title'       => $item->title(),
          'link'        => $item->link(),
          'description' => $item->description(),
          'pubDate'     => $item->pubDate(),
          'guid'        => $item->guid(),
        );
      }
      $this->view->isCached = false;

      // Caching
      if( $cacheId && !empty($channel) ) {
        $cache->save($channel, $cacheId);
      }
    } else {
      $this->view->isCached = true;
    }
    
    $this->view->channel = $channel;
  }
}