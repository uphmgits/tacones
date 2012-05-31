<?php
/**
 * 
 *
 * @category   Application_Extensions
 * @package    Marketplace
 * @copyright  Copyright 2010 
 * * 
 * @version    $Id: content.php 7244 2010-09-01 01:49:53Z john $
 * @author     John
 */
return array(
  array(
    'title' => 'Profile Marketplace',
    'description' => 'Displays a member\'s marketplace items on their profile.',
    'category' => 'Marketplace',
    'type' => 'widget',
    'name' => 'marketplace.profile-marketplaces',
    'defaultParams' => array(
      'title' => 'Marketplace',
      'titleCount' => true,
    ),
  ),
  array(
    'title' => 'Top Banner',
    'description' => 'Displays a category banner.',
    'category' => 'Marketplace',
    'type' => 'widget',
    'name' => 'marketplace.topbanner',
    'adminForm' => array(
      'elements' => array(
        array(
          'Text',
          'pageName',
          array(
            'label' => 'Page Name'
          )
        ),
      )
    ),
  ),
) ?>
