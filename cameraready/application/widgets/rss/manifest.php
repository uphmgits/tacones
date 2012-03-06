<?php
/**
 * SocialEngine
 *
 * @category   Application_Widget
 * @package    Rss
 * @copyright  Copyright 2006-2010 Webligo Developments
 * @license    http://www.socialengine.net/license/
 * @author     John
 */
return array(
  'package' => array(
    'type' => 'widget',
    'name' => 'rss',
    'version' => '4.1.8',
    'revision' => '$Revision: 9378 $',
    'path' => 'application/widgets/rss',
    'repository' => 'socialengine.net',
    'title' => 'RSS Feed',
    'description' => 'Displays an RSS feed.',
    'author' => 'Webligo Developments',
    'changeLog' => array(
      '4.1.8' => array(
        'Controller.php' => 'Added caching support',
        'index.tpl' => 'Added caching support',
        'manifest.php' => 'Incremented version',
      ),
      '4.0.3' => array(
        'Controller.php' => 'Fixed issue with getting link',
        'index.tpl' => 'Fixed issue with getting link; added link enabling; added option to not strip HTML',
        'manifest.php' => 'Incremented version; added option to not strip HTML',
      ),
      '4.0.2' => array(
        'index.tpl' => 'Added styles',
        'manifest.php' => 'Incremented version',
      ),
    ),
    'directories' => array(
      'application/widgets/rss',
    ),
  ),

  // Backwards compatibility
  'type' => 'widget',
  'name' => 'rss',
  'version' => '4.0.2',
  'revision' => '$Revision: 9378 $',
  'title' => 'RSS',
  'description' => 'Displays an RSS feed.',
  'category' => 'Widgets',
  'defaultParams' => array(
    'timeout' => 900,
  ),
  'adminForm' => array(
    'elements' => array(
      array(
        'Text',
        'title',
        array(
          'label' => 'Title'
        )
      ),
      array(
        'Text',
        'url',
        array(
          'label' => 'URL'
        )
      ),
      array(
        'Text',
        'timeout',
        array(
          'label' => 'Cache TTL',
          'description' => 'How long would you like to cache results before ' .
              'they are fetched again? Leave empty to disable caching.',
          'validators' => array(
            array('Int')
          ),
        ),
        'value' => 900,
      ),
      array(
        'Radio',
        'strip',
        array(
          'label' => 'Strip HTML?',
          'multiOptions' => array(
            1 => 'Yes',
            0 => 'No',
          ),
          'value' => 1,
        )
      ),
    ),
  ),
) ?>