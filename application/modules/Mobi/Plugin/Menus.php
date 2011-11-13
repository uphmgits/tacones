<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Mobi
 * @copyright  Copyright 2006-2010 Webligo Developments
 * @license    http://www.socialengine.net/license/
 * @version    $Id: Menus.php 9361 2011-10-10 20:41:54Z shaun $
 * @author     Charlotte
 */

/**
 * @category   Application_Extensions
 * @package    Mobi
 * @copyright  Copyright 2006-2010 Webligo Developments
 * @license    http://www.socialengine.net/license/
 */
class Mobi_Plugin_Menus
{
  
  // core_footer

  public function onMenuInitialize_CoreFooterMobile($row)
  {

    $session = new Zend_Session_Namespace('mobile');
    $router = Zend_Controller_Front::getInstance()->getRouter();

    // Mobile version visible
    if( $session->mobile ) {
        $route = array(
        'uri' => $router->assemble(array()).'?mobile=0',
        'enabled' => 1,
        'label' => "Full Site");
        
    // Full site visible
    } else {
        $route = array(
        'uri' => $router->assemble(array()).'?mobile=1',
        'enabled' => 1,
        'label' => "Mobile Site");
    }

    return $route;
  }


  // mobile_footer

  public function onMenuInitialize_MobiFooterMobile($row)
  {

    $session = new Zend_Session_Namespace('mobile');
    $router = Zend_Controller_Front::getInstance()->getRouter();

    // Mobile version visible
    if( $session->mobile ) {
        $route = array(
        'uri' => $router->assemble(array()).'?mobile=0',
        'enabled' => 1,
        'label' => "Full Site");

    // Full site visible
    } else {
        $route = array(
        'uri' => $router->assemble(array()).'?mobile=1',
        'enabled' => 1,
        'label' => "Mobile Site");
    }

    return $route;
  }

  public function onMenuInitialize_MobiFooterAuth($row)
  {
    $viewer = Engine_Api::_()->user()->getViewer();
    if( $viewer->getIdentity() )
    {
      return array(
        'label' => 'Sign Out',
        'route' => 'user_logout'
      );
    }

    else
    {
      return array(
        'label' => 'Sign In',
        'route' => 'user_login',
        'params' => array(
          // Nasty hack
          'return_url' => '64-' . base64_encode($_SERVER['REQUEST_URI']),
        ),
      );
    }
  }

  public function onMenuInitialize_MobiFooterSignup($row)
  {
    $viewer = Engine_Api::_()->user()->getViewer();
    if( !$viewer->getIdentity() )
    {
      return array(
        'label' => 'Sign Up',
        'route' => 'user_signup'
      );
    }

    return false;
  }


  // core_main

  public function onMenuInitialize_MobiMainHome($row)
  {
    $viewer  = Engine_Api::_()->user()->getViewer();
    $request = Zend_Controller_Front::getInstance()->getRequest();
    $route   = array(
      'route' => 'default',
    );

    if( $viewer->getIdentity() ) {
      $route['route']  = 'user_general';
      $route['params'] = array(
        'action' => 'home',
      );
      if( 'user'  == $request->getModuleName() && 
          'index' == $request->getControllerName() && 
          'home'  == $request->getActionName() ) {
        $route['active'] = true;
      }
    }
    return $route;
  }

  public function onMenuInitialize_MobiMainProfile($row)
  {
    $request = Zend_Controller_Front::getInstance()->getRequest();
    $viewer = Engine_Api::_()->user()->getViewer();
    $subject = null;
    if( Engine_Api::_()->core()->hasSubject() )
    {
       $subject = Engine_Api::_()->core()->getSubject();
    }


    if( $viewer->getIdentity() )
    {
      $route = array(
        'label' => $row->label,
        'uri' => $viewer->getHref(),
      );

      if(  'mobi'  == $request->getModuleName()
        && 'index' == $request->getControllerName()
        && 'profile'  == $request->getActionName()
        && $subject !== false )
      {
        if( $viewer->getIdentity() == $subject->getIdentity() ) { $route['active'] = true; }
      }

      return $route;
    }
    return false;
  }
  

  public function onMenuInitialize_MobiMainMessages($row)
  {
    $request = Zend_Controller_Front::getInstance()->getRequest();
    $viewer = Engine_Api::_()->user()->getViewer();
    if( !$viewer->getIdentity() )
    {
      return false;
    }

    // Get permission setting
    $permission = Engine_Api::_()->authorization()->getPermission($viewer->level_id, 'messages', 'create');
    if( Authorization_Api_Core::LEVEL_DISALLOW === $permission )
    {
      return false;
    }

    $message_count = Engine_Api::_()->messages()->getUnreadMessageCount($viewer);
    $baseUrl = Zend_Controller_Front::getInstance()->getBaseUrl() . '/';

    $route = array(
      'label' => Zend_Registry::get('Zend_Translate')->_($row->label) . ( $message_count ? ' (' . $message_count .')' : '' ),
      'route' => 'messages_general',
      'params' => array(
        'action' => 'inbox'
      )
    );

    if(  'messages'  == $request->getModuleName()
        && 'messages' == $request->getControllerName() )
    {
        $route['active'] = true;
    }

    return $route;
  }




  public function onMenuInitialize_MobiMainBrowse($row)
  {
    $request = Zend_Controller_Front::getInstance()->getRequest();
    $viewer = Engine_Api::_()->user()->getViewer();

    // CHECK PERMISSIONS

    $route = array(
      'label' => Zend_Registry::get('Zend_Translate')->_($row->label),
      'route' => 'mobi_general',
      'params' => array(
        'action' => 'browse'
      )
    );

    if(  'mobi'  == $request->getModuleName()
        && 'browse' == $request->getControllerName() )
    {
        $route['active'] = true;
    }

    return $route;
  }


  public function onMenuInitialize_MobiProfileFriend($row)
  {
    $viewer = Engine_Api::_()->user()->getViewer();
    $subject = Engine_Api::_()->core()->getSubject();

    // Not logged in
    if( !$viewer->getIdentity() || $viewer->getGuid(false) === $subject->getGuid(false) )
    {
      return false;
    }

    // Check if friendship is allowed in the network
    $eligible = (int) Engine_Api::_()->getApi('settings', 'core')->getSetting('user.friends.eligible', 2);
    if( !$eligible ){
      return '';
    }

    // check admin level setting if you can befriend people in your network
    else if( $eligible == 1 ){

      $networkMembershipTable = Engine_Api::_()->getDbtable('membership', 'network');
      $networkMembershipName = $networkMembershipTable->info('name');

      $select = new Zend_Db_Select($networkMembershipTable->getAdapter());
      $select
        ->from($networkMembershipName, 'user_id')
        ->join($networkMembershipName, "`{$networkMembershipName}`.`resource_id`=`{$networkMembershipName}_2`.resource_id", null)
        ->where("`{$networkMembershipName}`.user_id = ?", $viewer->getIdentity())
        ->where("`{$networkMembershipName}_2`.user_id = ?", $subject->getIdentity())
        ;

      $data = $select->query()->fetch();

      if( empty($data) ) {
        return '';
      }
    }

    // One-way mode
    $direction = (int) Engine_Api::_()->getApi('settings', 'core')->getSetting('user.friends.direction', 1);
    if( !$direction ) {
      $viewerRow = $viewer->membership()->getRow($subject);
      $subjectRow = $subject->membership()->getRow($viewer);
      $params = array();

      // Viewer?
      if( null === $subjectRow ) {
        // Follow
        $params[] = array(
          'label' => 'Follow',
          'icon' => 'application/modules/User/externals/images/friends/add.png',
          'class' => 'smoothbox',
          'route' => 'user_extended',
          'params' => array(
            'controller' => 'friends',
            'action' => 'add',
            'user_id' => $subject->getIdentity()
          ),
        );
      } else if( $subjectRow->resource_approved == 0 ) {
        // Cancel follow request
        $params[] = array(
          'label' => 'Cancel Follow Request',
          'icon' => 'application/modules/User/externals/images/friends/remove.png',
          'class' => 'smoothbox',
          'route' => 'user_extended',
          'params' => array(
            'controller' => 'friends',
            'action' => 'cancel',
            'user_id' => $subject->getIdentity()
          ),
        );
      } else {
        // Unfollow
        $params[] = array(
          'label' => 'Unfollow',
          'icon' => 'application/modules/User/externals/images/friends/remove.png',
          'class' => 'smoothbox',
          'route' => 'user_extended',
          'params' => array(
            'controller' => 'friends',
            'action' => 'remove',
            'user_id' => $subject->getIdentity()
          ),
        );
      }
      // Subject?
      if( null === $viewerRow ) {
        // Do nothing
      } else if( $viewerRow->resource_approved == 0 ) {
        // Approve follow request
        $params[] = array(
          'label' => 'Approve Follow Request',
          'icon' => 'application/modules/User/externals/images/friends/add.png',
          'class' => 'smoothbox',
          'route' => 'user_extended',
          'params' => array(
            'controller' => 'friends',
            'action' => 'confirm',
            'user_id' => $subject->getIdentity()
          ),
        );
      } else {
        // Remove as follower?
        $params[] = array(
          'label' => 'Remove as Follower',
          'icon' => 'application/modules/User/externals/images/friends/remove.png',
          'class' => 'smoothbox',
          'route' => 'user_extended',
          'params' => array(
            'controller' => 'friends',
            'action' => 'remove',
            'user_id' => $subject->getIdentity(),
            'rev' => true,
          ),
        );
      }
      if( count($params) == 1 ) {
        return $params[0];
      } else if( count($params) == 0 ) {
        return false;
      } else {
        return $params;
      }
    }

    // Two-way mode
    else {
      $row = $viewer->membership()->getRow($subject);
      if( null === $row ) {
        // Add
        return array(
          'label' => 'Add to My Friends',
          'icon' => 'application/modules/User/externals/images/friends/add.png',
          'class' => 'smoothbox',
          'route' => 'user_extended',
          'params' => array(
            'controller' => 'friends',
            'action' => 'add',
            'user_id' => $subject->getIdentity()
          ),
        );
      } else if( $row->user_approved == 0 ) {
        // Cancel request
        return array(
          'label' => 'Cancel Friend Request',
          'icon' => 'application/modules/User/externals/images/friends/remove.png',
          'class' => 'smoothbox',
          'route' => 'user_extended',
          'params' => array(
            'controller' => 'friends',
            'action' => 'cancel',
            'user_id' => $subject->getIdentity()
          ),
        );
      } else if( $row->resource_approved == 0 ) {
        // Approve request
        return array(
          'label' => 'Approve Friend Request',
          'icon' => 'application/modules/User/externals/images/friends/add.png',
          'class' => 'smoothbox',
          'route' => 'user_extended',
          'params' => array(
            'controller' => 'friends',
            'action' => 'confirm',
            'user_id' => $subject->getIdentity()
          ),
        );
      } else {
        // Remove friend
        return array(
          'label' => 'Remove from Friends',
          'icon' => 'application/modules/User/externals/images/friends/remove.png',
          'class' => 'smoothbox',
          'route' => 'user_extended',
          'params' => array(
            'controller' => 'friends',
            'action' => 'remove',
            'user_id' => $subject->getIdentity()
          ),
        );
      }
    }
  }

  public function onMenuInitialize_MobiProfileBlock($row)
  {
    $viewer = Engine_Api::_()->user()->getViewer();
    $subject = Engine_Api::_()->core()->getSubject();

    // Can't block self or if not logged in
    if( !$viewer->getIdentity() || $viewer->getGuid() == $subject->getGuid() )
    {
      return false;
    }

    if( !Engine_Api::_()->authorization()->isAllowed('user', $viewer, 'block') ) {
      return false;
    }

    if( !$subject->isBlockedBy($viewer) )
    {
      return array(
        'label' => 'Block Member',
        'icon' => 'application/modules/User/externals/images/block.png',
        'class' => 'smoothbox',
        'route' => 'user_extended',
        'params' => array(
          'controller' => 'block',
          'action' => 'add',
          'user_id' => $subject->getIdentity()
        ),
      );
    }

    else
    {
      return array(
        'label' => 'Unblock Member',
        'icon' => 'application/modules/User/externals/images/block.png',
        'class' => 'smoothbox',
        'route' => 'user_extended',
        'params' => array(
          'controller' => 'block',
          'action' => 'remove',
          'user_id' => $subject->getIdentity()
        ),
      );
    }
  }

  public function onMenuInitialize_MobiProfileMessage($row)
  {
    // Not logged in
    $viewer = Engine_Api::_()->user()->getViewer();
    $subject = Engine_Api::_()->core()->getSubject();
    if( !$viewer->getIdentity() || $viewer->getGuid(false) === $subject->getGuid(false) ) {
      return false;
    }

    // Get setting?
    $permission = Engine_Api::_()->authorization()->getPermission($viewer->level_id, 'messages', 'create');
    if( Authorization_Api_Core::LEVEL_DISALLOW === $permission )
    {
      return false;
    }
    $messageAuth = Engine_Api::_()->authorization()->getPermission($viewer->level_id, 'messages', 'auth');
    if( $messageAuth == 'none' ) {
      return false;
    } else if( $messageAuth == 'friends' ) {
      if( isset($viewer->membership()->getRow($subject)->active) && 
        $viewer->membership()->getRow($subject)->active == 0 ) {
        return false;
      }
    }

    return array(
      'label' => "Send Message",
      'icon' => 'application/modules/Messages/externals/images/send.png',
      'route' => 'messages_general',
      'params' => array(
        'action' => 'compose',
         'to' => $subject->getIdentity()
      ),
    );
  }
}