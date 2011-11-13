<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Mobi
 * @copyright  Copyright 2006-2010 Webligo Developments
 * @license    http://www.socialengine.net/license/
 * @version    $Id: changelog.php 9409 2011-10-19 00:14:13Z john $
 * @author     Charlotte
 */
return array(
  '4.1.8' => array(
    'controllers/IndexController.php' => 'Removed deprecated method calls',
    'externals/.htaccess' => 'Updated with far-future expires headers for static resources',
    'Plugin/Menus.php' => 'Removed deprecated routes',
    'settings/changelog.php' => 'Incremented version',
    'settings/install.php' => 'Reformatted code',
    'settings/manifest.php' => 'Incremented version',
    'settings/my.sql' => 'Incremented version',
    'widgets/mobi-footer/Controller.php' => 'Added optional built-in affiliate banner',
    'widgets/mobi-footer/index.tpl' => 'Added optional built-in affiliate banner',
  ),
  '4.1.7' => array(
    'settings/changelog.php' => 'Incremented version',
    'settings/manifest.php' => 'Incremented version',
    'settings/my.sql' => 'Incremented version',
    'widgets/mobi-menu-main/index.tpl' => 'Fixed issue with active class',
  ),
  '4.1.6' => array(
    'settings/changelog.php' => 'Incremented version',
    'settings/manifest.php' => 'Incremented version',
    'settings/my.sql' => 'Incremented version',
  ),
  '4.1.5p1' => array(
    'settings/changelog.php' => 'Incremented version',
    'settings/manifest.php' => 'Incremented version',
    'settings/my.sql' => 'Incremented version',
    'widgets/mobi-profile-options/index.tpl' => 'Fixed issue with profile page not rendering',
  ),
  '4.1.5' => array(
    'Api/Core.php' => 'Fixed notices',
    'controllers/IndexController.php' => 'Fixed issues with member home page being accessible by the public',
    'settings/changelog.php' => 'Incremented version',
    'settings/manifest.php' => 'Incremented version',
    'settings/my.sql' => 'Incremented version',
    'views/scripts/browse/browse.tpl' => 'Fixed notices',
    'widgets/mobi-menu-main/index.tpl' => 'Removed short php tags; Fixed notices being logged',
    'widgets/mobi-profile-options/index.tpl' => 'Fixed notices being logged',
    'widgets/mobi-switch/index.tpl' => 'Removed short php tags',
  ),
) ?>