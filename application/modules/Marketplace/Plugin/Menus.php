<?php
/**
 * SocialEngineMarket
 *
 * @category   Application_Core
 * @package    Marketplace
 * @copyright  Copyright 2012 SocialEngineMarket
 * @license    http://www.socialenginemarket.com
 */

class Marketplace_Plugin_Menus
{
  // core_mini

  public function onMenuInitialize_CoreMiniMarketplaceManage($row)
  {
    // @todo check perms
    $viewer = Engine_Api::_()->user()->getViewer();
    if( $viewer->getIdentity() ) {
      return array(
        'route' => 'marketplace_manage',
      );
    }
    return false;
  }

  public function onMenuInitialize_CoreMiniMarketplaceReports($row)
  {
    // @todo check perms
    $viewer = Engine_Api::_()->user()->getViewer();
    if( $viewer->getIdentity() ) {
      return array(
        'route' => 'marketplace_reports',
      );
    }
    return false;
  }
}
