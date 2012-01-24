<?php return array (
  'package' => 
  array (
    'type' => 'module',
    'name' => 'slider',
    'version' => '4.0.1',
    'path' => 'application/modules/Slider',
    'meta' => 
    array (
      'title' => 'Slider',
      'description' => 'Add slides to a page.',
      'author' => 'WebHive Team',
    ),
    'callback' => 
    array (
      'class' => 'Engine_Package_Installer_Module',
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
      0 => 'application/modules/Slider',
    ),
    'files' => 
    array (
      0 => 'application/languages/en/slider.csv',
    ),
  ),
 // Items ---------------------------------------------------------------------
  'items' => array(
    'slider_slide'
  ),
); ?>
