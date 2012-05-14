<?php
return array(
  'package' => array(
    'type' => 'theme',
    'name' => 'default',
    'version' => '4.1.8',
    'revision' => '$Revision: 9378 $',
    'path' => 'application/themes/musharraf',
    'repository' => 'socialengine.net',
    'title' => 'FashBay Theme',
    'thumb' => 'fashbay_theme.jpg',
    'author' => 'Stars Developer',
    'changeLog' => array(
      '4.1.8' => array(
        'manifest.php' => 'Incremented version',
        'mobile.css' => 'Added styles for HTML5 input elements',
        'theme.css' => 'Added styles for HTML5 input elements; added styles for drop-downs in main menu',
      ),
      '4.1.4' => array(
        'mainfest.php' => 'Incremented version',
        'mobile.css' => 'Added new type of stylesheet',
      ),
      '4.1.2' => array(
        'manifest.php' => 'Incremented version; removed deprecated meta key',
        'theme.css' => 'Added styles for liking comments',
      ),
      '4.1.0' => array(
        'manifest.php' => 'Incremented version',
        'theme.css' => 'Style tweaks',
      ),
      '4.0.4' => array(
        'constants.css' => 'Added constant theme_pulldown_contents_list_background_color_active',
        'manifest.php' => 'Incremented version',
        'theme.css' => 'Improved RTL support',
      ),
      '4.0.3' => array(
        'manifest.php' => 'Incremented version',
        'theme.css' => 'Added styles for highlighted text in search',
      ),
      '4.0.2' => array(
        'manifest.php' => 'Incremented version',
        'theme.css' => 'Added styles for delete comment link',
      ),
    ),
    'actions' => array(
      'install',
      'upgrade',
      'refresh',
      'remove',
    ),
    'callback' => array(
      'class' => 'Engine_Package_Installer_Theme',
    ),
    'directories' => array(
      'application/themes/musharraf',
    ),
  ),
  'files' => array(
    'theme.css',
    'constants.css',
    'mobile.css',	
  ),
  'nophoto' => array(
    'user' => array(
      'thumb_icon' => 'application/themes/musharraf/images/nophoto_user_thumb_icon.png',
      'thumb_profile' => 'application/themes/musharraf/images/nophoto_user_thumb_profile.png',
    ),
    'group' => array(
      'thumb_normal' => 'application/themes/musharraf/images/nophoto_event_thumb_normal.jpg',
      'thumb_profile' => 'application/themes/musharraf/images/nophoto_event_thumb_profile.jpg',
    ),
    'event' => array(
      'thumb_normal' => 'application/themes/musharraf/images/nophoto_event_thumb_normal.jpg',
      'thumb_profile' => 'application/themes/musharraf/images/nophoto_event_thumb_profile.jpg',
    ),
  ),
) ?>
