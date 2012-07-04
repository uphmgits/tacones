<?php
/**
 *
 *
 * @category   Application_Extensions
 * @package    Marketplace
 * @copyright  Copyright 2010
 * *
 * @version    $Id: manifest.php 7250 2010-09-01 07:42:35Z john $
 *
 */
return array(

    'package' => array(
    'type' => 'module',
    'title' => 'Marketplace',
    'author' => 'SocialEngineMarket',
    'name' => 'marketplace',
    'version' => '4.3.0',
    'revision' => '$Revision: 7260 $',
    'path' => 'application/modules/Marketplace',
    'repository' => 'socialengine.net',
    'meta' => array(
      'title' => 'Marketplace',
      'description' => 'Marketplace',
      'author' => 'SocialEngineMarket',
      'changeLog' => array(
        '4.2.5' => array(
          'controllers/AdminManageController.php' => 'Full Version',
        ),
      ),
    ),
    'actions' => array(
       'install',
       'upgrade',
       'refresh',
       'enable',
       'disable',
     ),
    'callback' => array(
      'path' => 'application/modules/Marketplace/settings/install.php',
      'class' => 'Marketplace_Installer',
    ),
    'directories' => array(
      'application/modules/Marketplace',
    ),
    'files' => array(
      'application/languages/en/marketplace.csv',
    ),
  ),
  // Hooks ---------------------------------------------------------------------
  'hooks' => array(
    array(
      'event' => 'onStatistics',
      'resource' => 'Marketplace_Plugin_Core'
    ),
    array(
      'event' => 'onUserDeleteBefore',
      'resource' => 'Marketplace_Plugin_Core',
    ),
  ),
  // Items ---------------------------------------------------------------------
  'items' => array(
    'marketplace',
    'marketplace_order',
    'marketplace_category',
    'marketplace_album',
    'marketplace_photo',
    'marketplace_cart',
    'marketplace_couponcart',
    'marketplace_coupon'
  ),
  // Routes --------------------------------------------------------------------
  'routes' => array(
    'marketplace_coupon' => array(
      'route' => 'marketplaces/coupon/:action/*',
      'defaults' => array(
        'module' => 'marketplaces',
        'controller' => 'coupon',
        'action' => 'index',
      ),
      'reqs' => array(
        'action' => '(index|create|delete|edit)',
      )
    ),
    'marketplace_extended' => array(
      'route' => 'marketplaces/:controller/:action/*',
      'defaults' => array(
        'module' => 'marketplace',
        'controller' => 'index',
        'action' => 'index',
      ),
      'reqs' => array(
        'controller' => '\D+',
        'action' => '\D+',
      )
    ),
    'marketplace_general' => array(
      'route' => 'marketplaces/:action/*',
      'defaults' => array(
        'module' => 'marketplace',
        'controller' => 'index',
        'action' => 'index',
      ),
      'reqs' => array(
        'action' => '(index|ajaxlist|addtocart|deletefromcart|cart|checkout|shippinginfo|canceling|client-shipping-service|set-tracking-number|view-tracking-info)',
      )
    ),
    // Public
    'marketplace_browse' => array(
      'route' => 'marketplaces/browse/:category/:page/*',
      'defaults' => array(
        'module' => 'marketplace',
        'controller' => 'index',
        'action' => 'index',
        'category' => 0,
        'page' => 1
      )
    ),
    'marketplace_view' => array(
      'route' => 'marketplaces/:user_id/*',
      'defaults' => array(
        'module' => 'marketplace',
        'controller' => 'index',
        'action' => 'list'
      ),
      'reqs' => array(
        'user_id' => '\d+'
      )
    ),
    'marketplace_entry_view' => array(
      'route' => 'marketplaces/:user_id/:marketplace_id',
      'defaults' => array(
        'module' => 'marketplace',
        'controller' => 'index',
        'action' => 'view'
      ),
      'reqs' => array(
        'user_id' => '\d+',
        'marketplace_id' => '\d+'
      )
    ),
    'marketplace_comments' => array(
      'route' => 'marketplaces/comments/:marketplace_id',
      'defaults' => array(
        'module' => 'marketplace',
        'controller' => 'index',
        'action' => 'comments'
      ),
      'reqs' => array(
        'marketplace_id' => '\d+'
      )
    ),
    // User
    'marketplace_create' => array(
      'route' => 'marketplaces/create/:category',
      'defaults' => array(
        'module' => 'marketplace',
        'controller' => 'index',
        'action' => 'create',
        'category' => 0
      ),
      'reqs' => array(
        'category' => '\d+',
      )
    ),
    
    'marketplace_ebayimport' => array(
      'route' => 'marketplaces/ebayimport/:category',
      'defaults' => array(
        'module' => 'marketplace',
        'controller' => 'index',
        'action' => 'ebayimport',
        'category' => 0
      ),
      'reqs' => array(
        'category' => '\d+',
      )
    ),
    'marketplace_itempreview' => array(
      'route' => 'marketplaces/itempreview/:marketplace_id',
      'defaults' => array(
        'module' => 'marketplace',
        'controller' => 'index',
        'action' => 'itempreview',
      ),
      'reqs' => array(
        'marketplace_id' => '\d+',
      )
    ),
    'marketplace_reports' => array(
      'route' => 'marketplaces/reports/:page',
      'defaults' => array(
        'module' => 'marketplace',
        'controller' => 'index',
        'action' => 'reports',
        'page' => '1'
      )
    ),
    'marketplace_delete' => array(
      'route' => 'marketplaces/delete/:marketplace_id',
      'defaults' => array(
        'module' => 'marketplace',
        'controller' => 'index',
        'action' => 'delete'
      )
    ),
    'marketplace_close' => array(
      'route' => 'marketplaces/close/:marketplace_id/:closed',
      'defaults' => array(
        'module' => 'marketplace',
        'controller' => 'index',
        'action' => 'close'
      )
    ),
    'marketplace_edit' => array(
      'route' => 'marketplaces/edit/:marketplace_id/:category',
      'defaults' => array(
        'module' => 'marketplace',
        'controller' => 'index',
        'action' => 'edit',
        'category' => 0
      ),
      'reqs' => array(
        'category' => '\d+',
      )
    ),
    'marketplace_manage' => array(
      'route' => 'marketplaces/manage/:page',
      'defaults' => array(
        'module' => 'marketplace',
        'controller' => 'index',
        'action' => 'manage',
        'page' => '1'
      )
    ),
    'marketplace_success' => array(
      'route' => 'marketplaces/success/:marketplace_id',
      'defaults' => array(
        'module' => 'marketplace',
        'controller' => 'index',
        'action' => 'success'
      )
    ),
    'marketplace_paypal' => array(
      'route' => 'marketplaces/paypal/:page',//marketplace_id',
      'defaults' => array(
        'module' => 'marketplace',
        'controller' => 'index',
        'action' => 'paypal'
      )
    ),

    'marketplace_payment' => array(
      'route' => 'marketplaces/payment/:page',//marketplace_id',
      'defaults' => array(
        'module' => 'marketplace',
        'controller' => 'index',
        'action' => 'payment',
        'page'   => 0,
      )
    ),

    'marketplace_paymentnotify' => array(
      'route' => 'marketplaces/paymentnotify/*',//marketplace_id',
      'defaults' => array(
        'module' => 'marketplace',
        'controller' => 'index',
        'action' => 'paymentnotify',
      )
    ),
    'marketplace_paymentreturn' => array(
      'route' => 'marketplaces/paymentreturn/*',//marketplace_id',
      'defaults' => array(
        'module' => 'marketplace',
        'controller' => 'index',
        'action' => 'paymentreturn',
      )
    ),
    /// IPNs ///
    'marketplace_paymentcompletenotify' => array(
      'route' => 'marketplaces/paymentcompletenotify/*',
      'defaults' => array(
        'module' => 'marketplace',
        'controller' => 'index',
        'action' => 'paymentcompletenotify',
      )
    ),
    'marketplace_paymentcompletereturn' => array(
      'route' => 'marketplaces/paymentcompletereturn/*',
      'defaults' => array(
        'module' => 'marketplace',
        'controller' => 'index',
        'action' => 'paymentcompletereturn',
      )
    ),

    'marketplace_paymentrefundnotify' => array(
      'route' => 'marketplaces/paymentrefundnotify/*',
      'defaults' => array(
        'module' => 'marketplace',
        'controller' => 'index',
        'action' => 'paymentrefundnotify',
      )
    ),
    'marketplace_paymentrefundreturn' => array(
      'route' => 'marketplaces/paymentrefundreturn/*',
      'defaults' => array(
        'module' => 'marketplace',
        'controller' => 'index',
        'action' => 'paymentrefundreturn',
      )
    ),

    'marketplace_paymentfailednotify' => array(
      'route' => 'marketplaces/paymentfailednotify/*',
      'defaults' => array(
        'module' => 'marketplace',
        'controller' => 'index',
        'action' => 'paymentfailednotify',
      )
    ),
    'marketplace_paymentfailedreturn' => array(
      'route' => 'marketplaces/paymentfailedreturn/*',
      'defaults' => array(
        'module' => 'marketplace',
        'controller' => 'index',
        'action' => 'paymentfailedreturn',
      )
    ),

    'marketplace_paymentreturnnotify' => array(
      'route' => 'marketplaces/paymentreturnnotify/*',
      'defaults' => array(
        'module' => 'marketplace',
        'controller' => 'index',
        'action' => 'paymentreturnnotify',
      )
    ),
    'marketplace_paymentreturnreturn' => array(
      'route' => 'marketplaces/paymentreturnreturn/*',
      'defaults' => array(
        'module' => 'marketplace',
        'controller' => 'index',
        'action' => 'paymentreturnreturn',
      )
    ),
    /// end of IPNs ///

    'marketplace_style' => array(
      'route' => 'marketplaces/marketplacestyle',
      'defaults' => array(
        'module' => 'marketplace',
        'controller' => 'index',
        'action' => 'marketplacestyle'
      )
    ),

    'marketplace_tag' => array(
      'route' => 'marketplaces/tag',
      'defaults' => array(
        'module' => 'marketplace',
        'controller' => 'index',
        'action' => 'suggest'
      )
    ),

    'marketplace_category' => array(
      'route' => 'admin/marketplace/settings/categories/:category_id',
      'defaults' => array(
        'module' => 'marketplace',
        'controller' => 'admin-settings',
        'action' => 'categories',
        'category_id' => 0
      )
    ),

    'marketplace_admin_manage_level' => array(
      'route' => 'admin/marketplace/level/:level_id',
      'defaults' => array(
        'module' => 'marketplace',
        'controller' => 'admin-level',
        'action' => 'index',
        'level_id' => 1
      )
    ),

    'marketplace_wishes' => array(
      'route' => 'marketplaces/wishes/:action/*',
      'defaults' => array(
        'module' => 'marketplace',
        'controller' => 'wishes',
        'action' => 'index'
      ),
      'reqs' => array(
        'action' => '\D+',
      )
    ),

  ),
);
