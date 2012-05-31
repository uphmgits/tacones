<?php
/**
 * @package    Marketplace
 * @copyright  Copyright 2012 SocialEngineMarket
 * @license    http://www.socialenginemarket.com/
 */

class Marketplace_Widget_TopbannerController extends Engine_Content_Widget_Abstract
{
  public function indexAction()
  {
    $this->view->pageName = $pageName = $this->_getParam('pageName', '');
    if( $pageName ) {
        switch( $pageName ) {
            case 'community':
                $this->view->prevBanner    = 'marketplace_accessories_header.jpg';   
                $this->view->currentBanner = 'community_header.jpg';
                $this->view->nextBanner    = 'blogs_header.jpg';  
                $this->view->prevUrl    = $this->view->url(array('category' => '8'), 'marketplace_browse');
                $this->view->currentUrl = $this->view->url(array('action' => 'home'), 'user_general');
                $this->view->nextUrl    = $this->view->url(array(), 'user_blog');
                break;
            case 'blog':
                $this->view->prevBanner    = 'community_header.jpg';   
                $this->view->currentBanner = 'blogs_header.jpg';
                $this->view->nextBanner    = 'marketplace_cart_header.jpg';   
                $this->view->prevUrl    = $this->view->url(array('action' => 'home'), 'user_general');
                $this->view->currentUrl = $this->view->url(array(), 'user_blog');
                $this->view->nextUrl    = $this->view->url(array('action' => 'cart'), 'marketplace_general');
                break;
            case 'cart' : 
                $this->view->prevBanner    = 'blogs_header.jpg';   
                $this->view->currentBanner = 'marketplace_cart_header.jpg';
                $this->view->nextBanner    = 'marketplace_home_header.jpg';  
                $this->view->prevUrl    = $this->view->url(array(), 'user_blog');
                $this->view->currentUrl = $this->view->url(array('action' => 'cart'), 'marketplace_general');
                $this->view->nextUrl    = $this->view->url(array(), 'marketplace_browse');
                break;
            case 'marketplace': 
                $categoryId = (int)$this->_getParam('categoryId', 0);
                $this->view->currentUrl = $this->view->url(array('category' => $categoryId), 'marketplace_browse');
                switch( $categoryId ) {
                  // clothes
                  case 3:  $this->view->prevBanner    = 'marketplace_home_header.jpg';   
                           $this->view->currentBanner = 'marketplace_cloth_header.jpg';
                           $this->view->nextBanner    = 'marketplace_shoes_header.jpg'; 
                           $this->view->prevUrl = $this->view->url(array(), 'marketplace_browse');
                           $this->view->nextUrl = $this->view->url(array('category' => '1'), 'marketplace_browse');
                           break;
                  // shoes
                  case 1:  $this->view->prevBanner    = 'marketplace_cloth_header.jpg';   
                           $this->view->currentBanner = 'marketplace_shoes_header.jpg';
                           $this->view->nextBanner    = 'marketplace_bags_header.jpg'; 
                           $this->view->prevUrl = $this->view->url(array('category' => '3'), 'marketplace_browse');
                           $this->view->nextUrl = $this->view->url(array('category' => '5'), 'marketplace_browse');
                           break;
                  // bags
                  case 5:  $this->view->prevBanner    = 'marketplace_shoes_header.jpg';   
                           $this->view->currentBanner = 'marketplace_bags_header.jpg';
                           $this->view->nextBanner    = 'marketplace_accessories_header.jpg'; 
                           $this->view->prevUrl = $this->view->url(array('category' => '1'), 'marketplace_browse');
                           $this->view->nextUrl = $this->view->url(array('category' => '8'), 'marketplace_browse');
                           break;
                  // accessories
                  case 8:  $this->view->prevBanner    = 'marketplace_bags_header.jpg';   
                           $this->view->currentBanner = 'marketplace_accessories_header.jpg';
                           $this->view->nextBanner    = 'community_header.jpg'; 
                           $this->view->prevUrl = $this->view->url(array('category' => '5'), 'marketplace_browse');
                           $this->view->nextUrl = $this->view->url(array('action' => 'home'), 'user_general');
                           break;
                  //case 13: $this->view->banner = 'marketplace_brands_header.jpg'; break;
                  default: $this->view->prevBanner    = 'marketplace_cart_header.jpg';   
                           $this->view->currentBanner = 'marketplace_home_header.jpg';
                           $this->view->nextBanner    = 'marketplace_cloth_header.jpg';
                           $this->view->prevUrl = $this->view->url(array('action' => 'cart'), 'marketplace_general');
                           $this->view->nextUrl = $this->view->url(array('category' => '3'), 'marketplace_browse');
                }
                break; 

            default : return $this->setNoRender();
        }
    }
    else return $this->setNoRender();
  }
}
