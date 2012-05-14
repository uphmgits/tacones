<?php
/**
 * Radcodes - SocialEngine Module
 *
 * @category   Application_Extensions
 * @package    Radcodes
 * @copyright  Copyright (c) 2009-2010 Radcodes LLC (http://www.radcodes.com)
 * @license    http://www.radcodes.com/license/
 * @version    $Id$
 * @author     Vincent Van <vincent@radcodes.com>
 */

return array(
  // Package -------------------------------------------------------------------
  'package' => array(
    'type' => 'module',
    'name' => 'radcodes',
    'version' => '4.0.3',
    'path' => 'application/modules/Radcodes',
    'repository' => 'radcodes.com',
    'meta' => array(
      'title' => 'Radcodes',
      'description' => 'This module is Radcodes Core Library, and is required by all SocialEngine Modules developed by Radcodes.',
      'author' => 'Radcodes Developments',
    ),
    'actions' => array(
       'install',
       'upgrade',
       'refresh',
       'enable',
       'disable',
     ),
    'callback' => array(
      'class' => 'Engine_Package_Installer_Module',
    ),
    'dependencies' => array(
      'core' => array(
        'type' => 'module',
        'name' => 'core',
        'minVersion' => '4.0.4'
      )
    ),    
    'directories' => array(
      'application/modules/Radcodes',
    ),
    'files' => array(
      'application/languages/en/radcodes.csv',
    ),
  ),
  // Hooks ---------------------------------------------------------------------
  'hooks' => array(
    array(
      'event' => 'getAdminNotifications',
      'resource' => 'Radcodes_Plugin_Core',
    )  
  ),
  // Items ---------------------------------------------------------------------
  'items' => array(
    'location',
  ),
  // Routes --------------------------------------------------------------------
  'routes' => array(
    'radcodes_extended' => array(
      'route' => 'radcodes/:controller/:action/*',
      'defaults' => array(
        'module' => 'radcodes',
        'controller' => 'index',
        'action' => 'index',
      ),
      'reqs' => array(
        'controller' => '\D+',
        'action' => '\D+',
      )
    ),
    'radcodes_general' => array(
      'route' => 'radcodes/:action/*',
      'defaults' => array(
        'module' => 'radcodes',
        'controller' => 'index',
        'action' => 'browse',
      ),
      'reqs' => array(
        'action' => '(browse|list|manage|store|updates)',
      )
    ),
  )
) ?>