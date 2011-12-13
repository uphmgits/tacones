<?php return array (
  'package' => 
  array (
    'type' => 'theme',
    'name' => 'sooraj',
    'version' => NULL,
    'revision' => '$Revision: 8879 $',
    'path' => 'application/themes/sooraj',
    'repository' => 'socialengine.net',
    'title' => 'sooraj',
    'thumb' => 'slipstream.jpg',
    'author' => 'Upheels',
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
      0 => 'application/themes/slipstream',
    ),
    'description' => '',
  ),
  'files' => 
  array (
    0 => 'theme.css',
    1 => 'constants.css',
  ),
  'nophoto' => 
  array (
    'user' => 
    array (
      'thumb_icon' => 'application/themes/slipstream/images/nophoto_user_thumb_icon.png',
      'thumb_profile' => 'application/themes/slipstream/images/nophoto_user_thumb_profile.png',
    ),
    'group' => 
    array (
      'thumb_normal' => 'application/themes/slipstream/images/nophoto_event_thumb_normal.jpg',
      'thumb_profile' => 'application/themes/slipstream/images/nophoto_event_thumb_profile.jpg',
    ),
    'event' => 
    array (
      'thumb_normal' => 'application/themes/slipstream/images/nophoto_event_thumb_normal.jpg',
      'thumb_profile' => 'application/themes/slipstream/images/nophoto_event_thumb_profile.jpg',
    ),
  ),
); ?>