<?php
/**
 * SocialEngine
 *
 * @category   Application_Core
 * @package    Invite
 * @copyright  Copyright 2006-2010 Webligo Developments
 * @license    http://www.socialengine.net/license/
 * @version    $Id: changelog.php 9381 2011-10-14 00:37:49Z john $
 * @author     Steve
 */
return array(
  '4.1.8' => array(
    '/application/languages/en/invite.csv' => 'Added phrases',
    'controllers/IndexController.php' => 'Added pages to the layout editor',
    'externals/.htaccess' => 'Updated with far-future expires headers for static resources',
    'Model/DbTable/Invites.php' => 'Fixed issue with incorrect URL in invite emails',
    'settings/changelog.php' => 'Incremented version',
    'settings/install.php' => 'Added',
    'settings/manifest.php' => 'Incremented version',
    'settings/my.sql' => 'Incremented version',
  ),
  '4.1.3' => array(
    'Plugin/Signup.php' => 'Friendship requests now considers network friendship settings',
    'settings/changelog.php' => 'Incremented version',
    'settings/manifest.php' => 'Incremented version',
    'settings/my.sql' => 'Incremented version',
  ),
  '4.1.2' => array(
    'settings/changelog.php' => 'Incremented version',
    'settings/manifest.php' => 'Incremented version',
    'settings/my.sql' => 'Incremented version',
    'views/scripts/index/sent.tpl' => 'Fixed incorrect profile URL',
  ),
  '4.1.1' => array(
    'externals/.htaccess' => 'Added keywords; removed deprecated code',
    'settings/changelog.php' => 'Incremented version',
    'settings/manifest.php' => 'Incremented version',
    'settings/my.sql' => 'Incremented version',
  ),
  '4.1.0' => array(
    '/application/languages/en/invite.csv' => 'Fixed phrases with stray double-quotes',
    'controllers/AdminController.php' => 'Removed',
    'Form/AdminSettings.php' => 'Removed',
    'Form/Invite.php' => 'Added missing translation; removed deprecated code',
    'settings/changelog.php' => 'Incremented version',
    'settings/manifest.php' => 'Incremented version',
    'settings/my.sql' => 'Incremented version',
  ),
  '4.0.3' => array(
    'controllers/IndexController.php' => 'Added missing beginTransaction',
    'Plugin/Signup.php' => 'Friendship requests are not sent if friendships are disabled',
    'settings/changelog.php' => 'Added',
    'settings/manifest.php' => 'Incremented version',
    'settings/my.sql' => 'Incremented version',
    '/application/languages/en/invite.csv' => 'Added missing phrases',
  ),
  '4.0.2' => array(
    'Api/Core.php' => 'Removed deprecated code',
    'controllers/IndexController.php' => 'Uses common method',
    'controllers/SignupController.php' => 'Refactored and improved',
    'Form/Invite.php' => 'Updated email params',
    'Model/DbTable/Invites.php' => 'Added common sendInvites method; fixes incorrect link in invite message',
    'Plugin/Signup.php' => 'Refactored and improved',
    'settings/manifest.php' => 'Incremented version',
    'settings/my-upgrade-4.0.1-4.0.2.sql' => 'Added',
    'settings/my.sql' => 'Incremented version',
    '/application/languages/en/invite.csv' => 'Added missing phrases',
  ),
  '4.0.1' => array(
    'controllers/IndexController.php' => 'Users could send invites even if disabled',
    'settings/manifest.php' => 'Incremented version',
  ),
) ?>