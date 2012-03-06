<?php 

/**
 * Radcodes - SocialEngine Module
 *
 * @category   Application_Extensions
 * @package    Review
 * @copyright  Copyright (c) 2009-2010 Radcodes LLC (http://www.radcodes.com)
 * @license    http://www.radcodes.com/license/
 * @version    $Id$
 * @author     Vincent Van <vincent@radcodes.com>
 */

return array(
  // Package -------------------------------------------------------------------
  'package' => array(
    'type' => 'module',
    'name' => 'review',
    'version' => '4.1.1',
    'path' => 'application/modules/Review',
    'repository' => 'radcodes.com',
    'title' => 'Member Reviews / Recommendations',
    'description' => 'This plugin allow your social network to have a review system between your members. This can help your site to build up trust, enhance quality of connections across your network. Super great for service provider, professional, colleague networking sites. Beside posting general review, it also support additional pros / cons listing, as well as rating stars. Recommendation can also be made by members, and many more etc.',
    'author' => 'Radcodes Developments',
    'actions' => array(
       'install',
       'upgrade',
       'refresh',
       'enable',
       'disable',
     ),
    'callback' => array(
      'path' => 'application/modules/Review/settings/install.php',
      'class' => 'Review_Installer',
    ),
    'dependencies' => array(
      'radcodes' => array(
        'type' => 'module',
        'name' => 'radcodes',
        'minVersion' => '4.0.3'
      )
    ),
    'directories' => array(
      'application/modules/Review',
    ),
    'files' => array(
      'application/languages/en/review.csv',
    ),
  ),
  // Hooks ---------------------------------------------------------------------
  'hooks' => array(
    array(
      'event' => 'onStatistics',
      'resource' => 'Review_Plugin_Core'
    ),
    array(
      'event' => 'onUserDeleteBefore',
      'resource' => 'Review_Plugin_Core',
    ),
  ),
  // Items ---------------------------------------------------------------------
  'items' => array(
    'review',
    'review_vote'
  ),
  // Routes --------------------------------------------------------------------
  'routes' => array(
    'review_extended' => array(
      'route' => 'reviews/:controller/:action/*',
      'defaults' => array(
        'module' => 'review',
        'controller' => 'index',
        'action' => 'index',
      ),
      'reqs' => array(
        'controller' => '\D+',
        'action' => '\D+',
      )
    ),
    'review_general' => array(
      'route' => 'reviews/:action/*',
      'defaults' => array(
        'module' => 'review',
        'controller' => 'index',
        'action' => 'index',
      ),
      'reqs' => array(
        'action' => '(index|browse|create|manage|tags|vote|unvote)',
      )
    ),
    'review_specific' => array(
      'route' => 'reviews/:action/:review_id/*',
      'defaults' => array(
        'module' => 'review',
        'controller' => 'index',
      ),
      'reqs' => array(
        'action' => '(edit|delete)',
        'review_id' => '\d+',
      )
    ),
    'review_profile' => array(
      'route' => 'review/:review_id/:slug/*',
      'defaults' => array(
        'module' => 'review',
        'controller' => 'index',
        'action' => 'view',
        'slug' => ''
      ),
      'reqs' => array(
        'review_id' => '\d+',
      )
    ),
    'review_home' => array(
      'route' => 'reviews',
      'defaults' => array(
        'module' => 'review',
        'controller' => 'index',
        'action' => 'index'
      )
    ),    
    'review_browse' => array(
      'route' => 'reviews/browse/*',
      'defaults' => array(
        'module' => 'review',
        'controller' => 'index',
        'action' => 'browse'
      )
    ),
    'review_manage' => array(
      'route' => 'reviews/manage/*',
      'defaults' => array(
        'module' => 'review',
        'controller' => 'index',
        'action' => 'manage',
      )
    ), 
    'review_create' => array(
      'route' => 'reviews/create/*',
      'defaults' => array(
        'module' => 'review',
        'controller' => 'index',
        'action' => 'create'
      )
    ), 
    'review_user' => array(
      'route' => 'reviews/user/:id/*',
      'defaults' => array(
        'module' => 'review',
        'controller' => 'index',
        'action' => 'list-user',
      ),
      'reqs' => array(
        'id' => '\d+',
      )
    ),
    'review_owner' => array(
      'route' => 'reviews/owner/:id/*',
      'defaults' => array(
        'module' => 'review',
        'controller' => 'index',
        'action' => 'list-owner',
      ),
      'reqs' => array(
        'id' => '\d+',
      )
    ),    
  ),
);
