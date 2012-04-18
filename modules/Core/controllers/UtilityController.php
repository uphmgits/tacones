<?php
/**
 * SocialEngine
 *
 * @category   Application_Core
 * @package    Core
 * @copyright  Copyright 2006-2010 Webligo Developments
 * @license    http://www.socialengine.net/license/
 * @version    $Id: UtilityController.php 9076 2011-07-21 02:11:10Z john $
 * @author     John
 */

/**
 * @category   Application_Core
 * @package    Core
 * @copyright  Copyright 2006-2010 Webligo Developments
 * @license    http://www.socialengine.net/license/
 */
class Core_UtilityController extends Core_Controller_Action_Standard
{
  public function successAction()
  {
    // Use specified layout
    $layout = $this->_getParam('layout', null);
    if( $layout )
    {
      $this->_helper->layout->setLayout($layout);
    }

    // Get messages
    $messages = array();
    $messages = array_merge($messages, (array) $this->_getParam('messages', null));
    $messages = array_merge($messages, (array) $this->_helper->flashMessenger->getMessages());

    // Default message "success"
    if( empty($messages) )
    {
      $messages[] = Zend_Registry::get('Zend_Translate')->_('Success');
    }

    // Assign
    $this->view->smoothboxClose = $this->_getParam('smoothboxClose');
    $this->view->parentRefresh = $this->_getParam('parentRefresh');
    $this->view->parentRedirect = $this->_getParam('parentRedirect');
    $this->view->parentRedirectTime = $this->_getParam('parentRedirectTime');
    $this->view->redirect = $this->_getParam('redirect');
    $this->view->redirectTime = $this->_getParam('redirectTime');
    $this->view->messages = $messages;
  }

  public function localeAction()
  {
    $locale = $this->_getParam('locale');
    $language = $this->_getParam('language');
    $return = $this->_getParam('return', $this->_helper->url->url(array(), 'default', true));
    $viewer = Engine_Api::_()->user()->getViewer();

    if( !empty($locale) ) {
      try {
        $locale = Zend_Locale::findLocale($locale);
      } catch( Exception $e ) {
        $locale = null;
      }
    }
    if( !empty($language) ) {
      try {
        $language = Zend_Locale::findLocale($language);
      } catch( Exception $e ) {
        $language = null;
      }
    }

    if(  $language && !$locale ) $locale = $language;
    if( !$language &&  $locale ) $language = $locale;
    
    if( $language && $locale ) {
      // Set as cookie
      setcookie('en4_language', $language, time() + (86400*365), '/');
      setcookie('en4_locale',   $locale,   time() + (86400*365), '/');
      // Set as database
      if( $viewer && $viewer->getIdentity() ) {
        $viewer->locale = $locale;
        $viewer->language = $language;
        $viewer->save();
      }
    }

    return $this->_helper->redirector->gotoUrl($return, array('prependBase' => false));
  }

  public function tasksAction()
  {
    // Make sure we don't crash the server
    defined('ENGINE_TASK_NOTRIGGER') || define('ENGINE_TASK_NOTRIGGER', true);

    // Execute tasks
    $tasksTable = Engine_Api::_()->getDbtable('tasks', 'core');
    try {
      $tasksTable->execute($this->_getAllParams());
      $status = true;
    } catch( Exception $e ) {
      $status = false;
      $tasksTable->getLog()->log($e->__toString(), Zend_Log::ERR);
    }

    // Response
    switch( $this->_getParam('mode') ) {
      case 'js':
        header('Content-Type: text/javascript');
        echo '// ' . sprintf('%1$s : %2$d', gmdate('c'), $status);
        break;
      default:
        printf('%1$s NOTICE (0): Cron Execute Result: %2$d', gmdate('c'), $status);
        break;
    }

    // Quit
    exit();
  }

  public function languageAction()
  {
    $translate = Zend_Registry::get('Zend_Translate');
    $this->view->vars = $translate->getMessages();
  }

  public function advertisementAction()
  {
    $table = Engine_Api::_()->getDbtable('adcampaigns', 'core');
    $db = $table->getAdapter();
    $db->beginTransaction();

    try {
      if( ($adcampaign_id = $this->_getParam('adcampaign_id')) ) {
        Engine_Api::_()->getDbtable('adcampaigns', 'core')->update(array(
          'clicks' => new Zend_Db_Expr('clicks + 1'),
        ), array(
          'adcampaign_id = ?' => $adcampaign_id,
        ));
      }
      
      if( ($ad_id = $this->_getParam('ad_id')) ) {
        Engine_Api::_()->getDbtable('ads', 'core')->update(array(
          'clicks' => new Zend_Db_Expr('clicks + 1'),
        ), array(
          'ad_id = ?' => $ad_id,
        ));
      }

      $db->commit();
    } catch( Exception $e ) {
      $db->rollBack();
      throw $e;
    }
  }

  public function verifyAction()
  {
    if( !$this->getRequest()->isPost() ) {
      $this->view->status = false;
      if( APPLICATION_ENV == 'development' ) {
        $this->view->code = 1;
      }
      return;
    }
    
    $token = $this->_getParam('token');
    
    if( null === $token || !is_string($token) || strlen($token) != 40 ) {
      $this->view->status = false;
      if( APPLICATION_ENV == 'development' ) {
        $this->view->code = 2;
        $this->view->token = $token;
      }
      return;
    }

    if( $token !== Engine_Api::_()->getApi('settings', 'core')->core_license_token ) {
      $this->view->status = false;
      if( APPLICATION_ENV == 'development' ) {
        $this->view->token = $token;
        $this->view->actual = Engine_Api::_()->getApi('settings', 'core')->core_license_token;
      }
      return;
    }
    
    $this->view->status = true;
  }

  public function themeAction()
  {
    if( ($selectThemeId = $this->_getParam('theme_id')) ) {
      $themesTable = Engine_Api::_()->getDbtable('themes', 'core');
      $theme = $themesTable->find((int) $selectThemeId)->current();
      if( $theme ) {
        $_SESSION['theme'] = $_COOKIE['theme'] = $theme['theme_id'];
        setcookie('theme', $theme['theme_id'], 0, Zend_Controller_Front::getInstance()->getBaseUrl());
      }
    }

    if( ($returnUrl = $this->_getParam('return_url')) ) {
      if( ($tmp = base64_decode($returnUrl)) ) {
        return $this->_helper->redirector->gotoUrl($tmp, array('prependBase' => false));
      } else {
        return $this->_helper->redirector->gotoUrl($returnUrl, array('prependBase' => false));
      }
    } else {
      return $this->_helper->redirector->gotoRoute(array(), 'default', true);
    }
  }
}