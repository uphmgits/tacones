<?php
/**
 * SocialEngine
 *
 * @category   Application_Core
 * @package    Messages
 * @copyright  Copyright 2006-2010 Webligo Developments
 * @license    http://www.socialengine.net/license/
 * @version    $Id: manifest.php 9378 2011-10-13 22:50:30Z john $
 * @author     John
 */
return array(
  // Package -------------------------------------------------------------------
  'package' => array(
    'type' => 'module',
    'name' => 'messages',
    'version' => '4.1.8',
    'revision' => '$Revision: 9378 $',
    'path' => 'application/modules/Messages',
    'repository' => 'socialengine.net',
    'title' => 'Messages',
    'description' => 'Messages',
    'author' => 'Webligo Developments',
    'changeLog' => 'settings/changelog.php',
    'dependencies' => array(
      array(
        'type' => 'module',
        'name' => 'core',
        'minVersion' => '4.1.8',
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
      'path' => 'application/modules/Messages/settings/install.php',
      'class' => 'Messages_Installer',
    ),
    'directories' => array(
      'application/modules/Messages',
    ),
    'files' => array(
      'application/languages/en/messages.csv',
    ),
  ),
  // Hooks ---------------------------------------------------------------------
  // Items ---------------------------------------------------------------------
  'items' => array(
    'messages_message',
    'messages_conversation',
  ),
  // Routes --------------------------------------------------------------------
  'routes' => array(
    'messages_general' => array(
      'route' => 'messages/:action/*',
      'defaults' => array(
        'module' => 'messages',
        'controller' => 'messages',
        'action' => '(inbox|outbox|delete)',
      ),
      'reqs' => array(
        'action' => '\D+',
      )
    ),
  )
) ?>
