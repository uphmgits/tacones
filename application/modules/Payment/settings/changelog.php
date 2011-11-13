<?php
/**
 * SocialEngine
 *
 * @category   Application_Core
 * @package    Payment
 * @copyright  Copyright 2006-2010 Webligo Developments
 * @license    http://www.socialengine.net/license/
 * @version    $Id: changelog.php 9409 2011-10-19 00:14:13Z john $
 * @author     John Boehr <j@webligo.com>
 */
return array(
  '4.1.8' => array(
    '/application/languages/en/payment.csv' => 'Added phrases',
    'controllers/AdminPackageController.php' => 'Added default plan support',
    'controllers/SubscriptionController.php' => 'Added default plan support',
    'externals/.htaccess' => 'Updated with far-future expires headers for static resources',
    'externals/styles/main.css' => 'Style tweaks',
    'Form/Admin/Gateway/Testing.php' => 'Added svn:keywords',
    'Form/Admin/Package/Create.php' => 'Added default plan support',
    'Form/Signup/Subscription.php' => 'Added default plan support',
    'Model/DbTable/Subscriptions.php' => 'Added default plan support',
    'Model/Subscription.php' => 'Fixed errors when gateway is missing',
    'Plugin/Gateway/PayPal.php' => 'Fixed issue with recurring payments when inital payment amount is zero',
    'Plugin/Gateway/Testing.php' => 'Added svn:keywords',
    'settings/changelog.php' => 'Incremented version',
    'settings/manifest.php' => 'Incremented version',
    'settings/my-upgrade-4.1.7-4.1.8.sql' => 'Added',
    'settings/my.sql' => 'Incremented version',
    'views/scripts/_signupSubscription.tpl' => 'Added default plan support',
    'views/scripts/admin-index/raw-order-detail.tpl' => 'Added missing translation',
    'views/scripts/admin-index/raw-transaction-detail.tpl' => 'Added missing translation',
    'views/scripts/admin-package/delete.tpl' => 'Added missing translation',
    'views/scripts/admin-package/index.tpl' => 'Added missing translation',
    'views/scripts/settings/confirm.tpl' => 'Added missing translation',
  ),
  '4.1.7' => array(
    '/application/languages/en/payment.csv' => 'Added phrases',
    'controllers/AdminGatewayController.php' => 'Minor optimizations',
    'controllers/SettingsController.php' => 'Removing deprecated usage of $this->_helper->api()',
    'Form/Admin/Gateway/Testing.php' => 'Added',
    'Plugin/Gateway/Testing.php' => 'Added',
    'settings/changelog.php' => 'Incremented version',
    'settings/manifest.php' => 'Incremented version',
    'settings/my-upgrade-4.1.6p3-4.1.7.sql' => 'Added',
    'settings/my.sql' => 'Incremented version',
    'views/scripts/admin-subscription/detail.tpl' => 'Fixed error when level was missing or not assigned',
    'views/scripts/admin-subscription/index.tpl' => 'Fixed error when level was missing or not assigned',
  ),
  '4.1.6p3' => array(
    'controllers/IpnController.php' => 'Enhanced logging to facilitate troubleshooting',
    'Plugin/Gateway/2Checkout.php' => 'Added missing IPN hook',
    'Plugin/Gateway/PayPal.php' => 'Added missing IPN hook',
    'settings/changelog.php' => 'Incremented version',
    'settings/manifest.php' => 'Incremented version',
    'settings/my.sql' => 'Incremented version',
  ),
  '4.1.6' => array(
    'Plugin/Gateway/PayPal.php' => 'Added GiroPay parameters',
    'settings/changelog.php' => 'Incremented version',
    'settings/manifest.php' => 'Incremented version',
    'settings/my.sql' => 'Incremented version',
  ),
  '4.1.5' => array(
    'Model/Subscription.php' => 'Fixed issue with accounts being disabled',
    'Plugin/Core.php' => 'Fixed issue with accounts being disabled',
    'Plugin/Gateway/PayPal.php' => 'Fixed currency issue with non-default currencies',
    'settings/changelog.php' => 'Incremented version',
    'settings/manifest.php' => 'Incremented version',
    'settings/my.sql' => 'Incremented version',
  ),
  '4.1.4' => array(
    'externals/styles/main.css' => 'Added svn:keywords Id',
    'externals/styles/mobile.css' => 'Added',
    'Plugin/Core.php' => 'Fixed issue with accounts being disabled',
    'Plugin/Gateway/PayPal.php' => 'Fixed issues with non-default currencies',
    'settings/changelog.php' => 'Incremented version',
    'settings/manifest.php' => 'Incremented version',
    'settings/my.sql' => 'Incremented version',
  ),
  '4.1.3' => array(
    'controllers/SubscriptionController.php' => 'Fixed issues with showing signup activity feed item when member had not yet paid',
    'Model/Subscription.php' => 'Fixed issues with showing signup activity feed item when member had not yet paid',
    'Plugin/Core.php' => 'Fixed issues with showing signup activity feed item when member had not yet paid; fixed issue caused by deleting a level that a package had as it\'s level',
    'Plugin/Gateway/PayPal.php' => 'Fixed bug that could cause double-billing at the beginning of a recurring payment profile',
    'settings/changelog.php' => 'Incremented version',
    'settings/manifest.php' => 'Incremented version',
    'settings/my.sql' => 'Incremented version',
  ),
  '4.1.2' => array(
    'controllers/AdminPackageController.php' => 'Fixed incorrect member count',
    'Form/Admin/Package/Create.php' => 'Added length limit to package description',
    'Plugin/Gateway/PayPal.php' => 'Added missing IPN types',
    'settings/changelog.php' => 'Incremented version',
    'settings/manifest.php' => 'Incremented version',
    'settings/my-upgrade-4.1.1-4.1.2.sql' => 'Added',
    'settings/my.sql' => 'Incremented version',
  ),
  '4.1.1' => array(
    'controllers/AdminPackageController.php' => 'Added filter form',
    'controllers/SubscriptionController.php' => 'Added language and region to gateway params',
    'externals/.htaccess' => 'Added keywords; removed deprecated code',
    'Form/Admin/Package/Filter.php' => 'Added',
    'Model/Package.php' => 'Different',
    'Plugin/Gateway/2Checkout.php' => 'Fixed issue with missing amount in recurring payments',
    'Plugin/Gateway/PayPal.php' => 'Fixed issue with checking expired payments',
    'settings/changelog.php' => 'Incremented version',
    'settings/manifest.php' => 'Incremented version',
    'settings/my.sql' => 'Incremented version',
    'views/scripts/admin-index/detail.tpl' => 'Fixed localization of currency and amount',
    'views/scripts/admin-index/index.tpl' => 'Fixed localization of currency and amount',
    'views/scripts/admin-package/index.tpl' => 'Added filter form',
    'views/scripts/admin-subscription/detail.tpl' => 'Fixed localization of currency and amount',
    'views/scripts/admin-subscription/index.tpl' => 'Fixed localization of currency and amount',
  ),
  '4.1.0' => array(
    '*' => 'Added',
  ),
) ?>