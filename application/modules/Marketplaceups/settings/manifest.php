<?php return array (
  'package' => 
  array (
    'type' => 'module',
    'name' => 'marketplaceups',
    'version' => '4.3.0',
    'path' => 'application/modules/Marketplaceups',
    'title' => 'Marketplace Ups',
    'description' => '',
    'author' => 'SocialEngineMarket',
    'callback' => 
    array (
      'path' => 'application/modules/Marketplaceups/settings/install.php',
      'class' => 'Marketplaceups_Installer',
    ),
    'dependencies' => array(
      array(
        'type' => 'module',
        'name' => 'marketplace',
        'minVersion' => '4.3.0',
      ),
    ),
    'actions' => 
    array (
      0 => 'install',
      1 => 'upgrade',
      2 => 'refresh',
      3 => 'enable',
      4 => 'disable',
    ),
    'directories' => 
    array (
      0 => 'application/modules/Marketplaceups',
    ),
    'files' => 
    array (
      0 => 'application/languages/en/marketplaceups.csv',
    ),
  ),
); ?>