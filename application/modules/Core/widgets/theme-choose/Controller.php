<?php
/**
 * SocialEngine
 *
 * @category   Application_Core
 * @package    Core
 * @copyright  Copyright 2006-2010 Webligo Developments
 * @license    http://www.socialengine.net/license/
 * @version    $Id: Controller.php 9382 2011-10-14 00:41:45Z john $
 * @author     John
 */

/**
 * @category   Application_Core
 * @package    Core
 * @copyright  Copyright 2006-2010 Webligo Developments
 * @license    http://www.socialengine.net/license/
 */
class Core_Widget_ThemeChooseController extends Engine_Content_Widget_Abstract
{
  public function indexAction()
  {
    // Get themes
    $themesTable = Engine_Api::_()->getDbtable('themes', 'core');
    $this->view->themes = $themes = $themesTable->select()->query()->fetchAll();

    // Detect active theme
    $activeTheme = null;

    if( !$activeTheme && !empty($_COOKIE['theme']) && is_numeric($_COOKIE['theme']) ) {
      foreach( $themes as $theme ) {
        if( $theme['theme_id'] == $_COOKIE['theme'] ) {
          $activeTheme = $theme;
        }
      }
    }

    if( !$activeTheme ) {
      foreach( $themes as $theme ) {
        if( $theme['active'] ) {
          $activeTheme = $theme;
        }
      }
    }

    $this->view->activeTheme = $activeTheme;
  }
}