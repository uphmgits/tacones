<?php
/**
 * SocialEngine
 *
 * @category   Application_Core
 * @package    Core
 * @copyright  Copyright 2006-2010 Webligo Developments
 * @license    http://www.socialengine.net/license/
 * @version    $Id: Controller.php 9033 2011-06-28 21:28:45Z john $
 * @author     John
 */

/**
 * @category   Application_Core
 * @package    Core
 * @copyright  Copyright 2006-2010 Webligo Developments
 * @license    http://www.socialengine.net/license/
 */
class Core_Widget_AdminNewsController extends Engine_Content_Widget_Abstract
{
  public function indexAction()
  {
    // Zend_Feed required DOMDocument
    // @todo add to sanity check
    if( !class_exists('DOMDocument', false) ) {
      $this->view->badPhpVersion = true;
      return;
      //return $this->setNoRender();
    }

    $rss = Zend_Feed::import('http://www.socialengine.net/news_rss.php');
    $channel = array(
      'title'       => $rss->title(),
      'link'        => $rss->link(),
      'description' => $rss->description(),
      'items'       => array()
    );

    $max = $this->_getParam('max', 4);
    $count = 0;

    // Loop over each channel item and store relevant data
    foreach( $rss as $item )
    {
      if( $count++ >= $max ) break;
      $channel['items'][] = array(
        'title'       => $item->title(),
        'link'        => $item->link(),
        'description' => $item->description(),
        'pubDate'     => $item->pubDate(),
        'guid'        => $item->guid(),
      );
    }

    $this->view->channel = $channel;
  }
}