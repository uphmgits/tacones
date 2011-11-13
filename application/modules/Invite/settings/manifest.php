<?php
/**
 * SocialEngine
 *
 * @category   Application_Core
 * @package    Invite
 * @copyright  Copyright 2006-2010 Webligo Developments
 * @license    http://www.socialengine.net/license/
 * @version    $Id: manifest.php 9378 2011-10-13 22:50:30Z john $
 * @author     Steve
 */
return array(
  // Package -------------------------------------------------------------------
  'package' => array(
    'type' => 'module',
    'name' => 'invite',
    'version' => '4.1.8',
    'revision' => '$Revision: 9378 $',
    'path' => 'application/modules/Invite',
    'repository' => 'socialengine.net',
    'title' => 'Invite',
    'description' => 'Invite',
    'author' => 'Webligo Developments',
    'changeLog' => 'settings/changelog.php',
    'dependencies' => array(
      array(
        'type' => 'module',
        'name' => 'core',
        'version' => '4.1.8',
      ),
    ),
    'actions' => array(
       'install',
       'upgrade',
       'refresh',
       //'enable',
       //'disable',
     ),
    'callback' => array(
      'path' => 'application/modules/Invite/settings/install.php',
      'class' => 'Invite_Installer',
    ),
    'directories' => array(
      'application/modules/Invite',
    ),
    'files' => array(
      'application/languages/en/invite.csv',
    ),
  ),
  // Hooks ---------------------------------------------------------------------
  'hooks' => array(
    array(
      'event' => 'onUserCreateAfter',
      'resource' => 'Invite_Plugin_Signup',
    ),
  ),
  // Items ---------------------------------------------------------------------
  'items' => array(
    'invite'
  ),
  // Routes --------------------------------------------------------------------
);