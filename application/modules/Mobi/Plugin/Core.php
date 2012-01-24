<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Mobi
 * @copyright  Copyright 2006-2010 Webligo Developments
 * @license    http://www.socialengine.net/license/
 * @version    $Id: Core.php 8868 2011-04-13 01:52:00Z john $
 * @author     Charlotte
 */

/**
 * @category   Application_Extensions
 * @package    Mobi
 * @copyright  Copyright 2006-2010 Webligo Developments
 * @license    http://www.socialengine.net/license/
 */
class Mobi_Plugin_Core extends Zend_Controller_Plugin_Abstract
{
  public function routeShutdown(Zend_Controller_Request_Abstract $request)
  {
      // CHECK IF ADMIN
      if(substr($request->getPathInfo(), 1, 5) == "admin") { return; }

      $mobile = $request->getParam("mobile");
      $session = new Zend_Session_Namespace('mobile');

      if($mobile == "1") {
        $mobile = true;
        $session->mobile = true;
      } elseif($mobile == "0") {
        $mobile = false;
        $session->mobile = false;
      } else {
        if( isset($session->mobile) ) {
          $mobile = $session->mobile;
        } else {
          // CHECK TO SEE IF MOBILE
          if( Engine_Api::_()->mobi()->isMobile() ) {
            $mobile = true;
            $session->mobile = true;
          } else {
            $mobile = false;
            $session->mobile = false;
          }
        }
      }

      if(!$mobile) { return; }

      $module = $request->getModuleName();
      $controller = $request->getControllerName();
      $action = $request->getActionName();
      if($module == "core") {
        if($controller == "index" && $action == "index") {
          $request->setModuleName('mobi');
          $request->setControllerName('index');
          $request->setActionName('index');
        }
      } elseif($module == "user") {
        if($controller == "index" && $action == "home") {
          $request->setModuleName('mobi');
          $request->setControllerName('index');
          $request->setActionName('userhome');
        } elseif($controller == "profile" && $action == "index") {
          $request->setModuleName('mobi');
          $request->setControllerName('index');
          $request->setActionName('profile');
        }
      } elseif($module == "group") {
        if($controller == "profile" && $action == "index") {
          $request->setModuleName('mobi');
          $request->setControllerName('group');
          $request->setActionName('profile');
        }

      } elseif($module == "event") {
        if($controller == "profile" && $action == "index") {
          $request->setModuleName('mobi');
          $request->setControllerName('event');
          $request->setActionName('profile');
        }

      }

      // Create layout
      $layout = Zend_Layout::startMvc();

      // Set options
      $layout->setViewBasePath(APPLICATION_PATH . "/application/modules/Mobi/layouts", 'Core_Layout_View')
        ->setViewSuffix('tpl')
        ->setLayout(null);
  }
}