<?php return array (
  'package' => 
  array (
    'type' => 'theme',
    'name' => 'aldana1',
    'version' => NULL,
    'revision' => '$Revision: 8879 $',
    'path' => 'application/themes/aldana1',
    'repository' => 'socialengine.net',
    'title' => 'aldana1',
    'thumb' => 'digita.jpg',
    'author' => 'Fashbay',
    'actions' => 
    array (
      0 => 'install',
      1 => 'upgrade',
      2 => 'refresh',
      3 => 'remove',
    ),
    'callback' => 
    array (
      'class' => 'Engine_Package_Installer_Theme',
    ),
    'directories' => 
    array (
      0 => 'application/themes/digita',
    ),
    'description' => 'the first aldana theme',
  ),
  'files' => 
  array (
    0 => 'theme.css',
    1 => 'constants.css',
  ),
); ?>