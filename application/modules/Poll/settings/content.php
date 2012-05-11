<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Poll
 * @copyright  Copyright 2006-2010 Webligo Developments
 * @license    http://www.socialengine.net/license/
 * @version    $Id: content.php 9283 2011-09-21 01:04:43Z john $
 * @author     John
 */
return array(
  array(
    'title' => 'Home Poll',
    'description' => 'Displays a single selected poll.',
    'category' => 'Polls',
    'type' => 'widget',
    'name' => 'poll.home-poll',
    'autoEdit' => true,
    //'adminForm' => 'Poll_Form_Admin_Widget_HomePoll',
    'defaultParams' => array(
      'title' => 'Poll',
    ),
    'requirements' => array(
      'no-subject',
    ),
  ),
  array(
    'title' => 'Profile Polls',
    'description' => 'Displays a member\'s polls on their profile.',
    'category' => 'Polls',
    'type' => 'widget',
    'name' => 'poll.profile-polls',
    'isPaginated' => true,
    'defaultParams' => array(
      'title' => 'Polls',
      'titleCount' => true,
    ),
    'requirements' => array(
      'subject' => 'user',
    ),
  ),
  array(
    'title' => 'Popular Polls',
    'description' => 'Displays a list of popular polls.',
    'category' => 'Polls',
    'type' => 'widget',
    'name' => 'poll.list-popular-polls',
    'isPaginated' => true,
    'defaultParams' => array(
      'title' => 'Popular Polls',
    ),
    'requirements' => array(
      'no-subject',
    ),
    'adminForm' => array(
      'elements' => array(
        array(
          'Radio',
          'popularType',
          array(
            'label' => 'Popular Type',
            'multiOptions' => array(
              'vote' => 'Votes',
              'view' => 'Views',
              'comment' => 'Comments',
            ),
            'value' => 'vote',
          )
        ),
      )
    ),
  ),
  array(
    'title' => 'Recent Polls',
    'description' => 'Displays a list of recent polls.',
    'category' => 'Polls',
    'type' => 'widget',
    'name' => 'poll.list-recent-polls',
    'isPaginated' => true,
    'defaultParams' => array(
      'title' => 'Recent Polls',
    ),
    'requirements' => array(
      'no-subject',
    ),
    'adminForm' => array(
      'elements' => array(
        array(
          'Radio',
          'recentType',
          array(
            'label' => 'Recent Type',
            'multiOptions' => array(
              'creation' => 'Creation Date',
              'modified' => 'Modified Date',
            ),
            'value' => 'creation',
          )
        ),
      )
    ),
  ),
  
  array(
    'title' => 'Poll Browse Search',
    'description' => 'Displays a search form in the poll browse page.',
    'category' => 'Polls',
    'type' => 'widget',
    'name' => 'poll.browse-search',
    'requirements' => array(
      'no-subject',
    ),
  ),
  array(
    'title' => 'Poll Browse Menu',
    'description' => 'Displays a menu in the poll browse page.',
    'category' => 'Polls',
    'type' => 'widget',
    'name' => 'poll.browse-menu',
    'requirements' => array(
      'no-subject',
    ),
  ),
  array(
    'title' => 'Poll Browse Quick Menu',
    'description' => 'Displays a small menu in the poll browse page.',
    'category' => 'Polls',
    'type' => 'widget',
    'name' => 'poll.browse-menu-quick',
    'requirements' => array(
      'no-subject',
    ),
  ),
) ?>