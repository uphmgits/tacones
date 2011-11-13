<?php
/**
 * SocialEngine
 *
 * @category   Application_Core
 * @package    Fields
 * @copyright  Copyright 2006-2010 Webligo Developments
 * @license    http://www.socialengine.net/license/
 * @version    $Id: manifest.php 9378 2011-10-13 22:50:30Z john $
 * @author     John
 */
return array(
  // Package -------------------------------------------------------------------
  'package' => array(
    'type' => 'module',
    'name' => 'fields',
    'version' => '4.1.8',
    'revision' => '$Revision: 9378 $',
    'path' => 'application/modules/Fields',
    'repository' => 'socialengine.net',
    'title' => 'Fields',
    'description' => 'Fields',
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
      'class' => 'Engine_Package_Installer_Module',
      'priority' => 3500,
    ),
    'directories' => array(
      'application/modules/Fields',
    ),
    'files' => array(
      'application/languages/en/fields.csv',
    ),
  ),
  // Hooks ---------------------------------------------------------------------
  // Items ---------------------------------------------------------------------
  // Routes --------------------------------------------------------------------
) ?>