<?php return array (
  'package' => 
  array (
    'type' => 'module',
    'name' => 'marketplacecart',
    'version' => '4.3.0',
    'path' => 'application/modules/Marketplacecart',
    'title' => 'Marketplace Cart',
    'description' => '',
    'author' => 'SocialEngineMarket',
    'callback' => 
    array (
      'path' => 'application/modules/Marketplacecart/settings/install.php',
      'class' => 'Marketplacecart_Installer',
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
      0 => 'application/modules/Marketplacecart',
    ),
    'files' => 
    array (
      0 => 'application/languages/en/marketplacecart.csv',
    ),
  ),
); ?>