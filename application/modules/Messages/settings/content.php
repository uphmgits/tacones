<?php
/**
 * SocialEngine
 *
 * @category   Application_Core
 * @package    Messages
 * @copyright  Copyright 2006-2010 Webligo Developments
 * @license    http://www.socialengine.net/license/
 * @version    $Id: content.php 9308 2011-09-22 00:36:41Z john $
 * @author     John Boehr <j@webligo.com>
 */
return array(
  array(
    'title' => 'Recent Messages',
    'description' => 'Displays a list of the signed in user\'s recent messages.',
    'category' => 'Messages',
    'type' => 'widget',
    'name' => 'messages.home-messages',
    'isPaginated' => true,
    'defaultParams' => array(
      'title' => 'Recent Messages',
    ),
    'requirements' => array(
      'viewer',
      'no-subject',
    ),
  ),
  
  array(
    'title' => 'Messages Menu',
    'description' => 'Displays a menu in messages inbox, output, and compose pages.',
    'category' => 'Videos',
    'type' => 'widget',
    'name' => 'messages.menu',
    'requirements' => array(
      'no-subject',
    ),
  ),
) ?>