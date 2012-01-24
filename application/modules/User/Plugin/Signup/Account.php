<?php
/**
 * SocialEngine
 *
 * @category   Application_Core
 * @package    User
 * @copyright  Copyright 2006-2010 Webligo Developments
 * @license    http://www.socialengine.net/license/
 * @version    $Id: Account.php 9094 2011-07-22 02:21:01Z shaun $
 * @author     John
 */

/**
 * @category   Application_Core
 * @package    User
 * @copyright  Copyright 2006-2010 Webligo Developments
 * @license    http://www.socialengine.net/license/
 */
class User_Plugin_Signup_Account extends Core_Plugin_FormSequence_Abstract
{
  protected $_name = 'account';

  protected $_formClass = 'User_Form_Signup_Account';

  protected $_script = array('signup/form/account.tpl', 'user');

  protected $_adminFormClass = 'User_Form_Admin_Signup_Account';

  protected $_adminScript = array('admin-signup/account.tpl', 'user');

  public $email = null;

  public function onView()
  {
    if( !empty($_SESSION['facebook_signup']) || 
        !empty($_SESSION['twitter_signup']) ) {
           
      // Attempt to preload information
      if( !empty($_SESSION['facebook_signup']) ) {
        try {
          $facebookTable = Engine_Api::_()->getDbtable('facebook', 'user');
          $facebook = $facebookTable->getApi();
          $settings = Engine_Api::_()->getDbtable('settings', 'core');
          if( $facebook && $settings->core_facebook_enable ) {
            // Get email address
            $apiInfo = $facebook->api('/me'); // @TODO: Temporarily store FB user data session

            // General
            $form = $this->getForm();
          
            if( ($emailEl = $form->getElement('email')) && !$emailEl->getValue() ) {
              $emailEl->setValue($apiInfo['email']);
            }
            if( ($usernameEl = $form->getElement('username')) && !$usernameEl->getValue() ) {
              $usernameEl->setValue(preg_replace('/[^A-Za-z]/', '', $apiInfo['name']));
            }
          
            // Locale
            $localeObject = new Zend_Locale($apiInfo['locale']);
            if( ($localeEl = $form->getElement('locale')) && !$localeEl->getValue() ) {
              $localeEl->setValue($localeObject->toString());
            }
            if( ($languageEl = $form->getElement('language')) && !$languageEl->getValue() ) {
              if( isset($languageEl->options[$localeObject->toString()]) ) {
                $languageEl->setValue($localeObject->toString());
              } else if( isset($languageEl->options[$localeObject->getLanguage()]) ) {
                $languageEl->setValue($localeObject->getLanguage());
              }
            }
          }
        } catch( Exception $e ) {
          // Silence?
        }
      }
    
      // Attempt to preload information
      if( !empty($_SESSION['twitter_signup']) ) {
        try {
          $twitterTable = Engine_Api::_()->getDbtable('twitter', 'user');
          $twitter = $twitterTable->getApi();
          $settings = Engine_Api::_()->getDbtable('settings', 'core');
          if( $twitter && $settings->core_twitter_enable ) {
            $accountInfo = $twitter->account->verify_credentials();
            
            // General
            $this->getForm()->populate(array(
              //'email' => $apiInfo['email'],
              'username' => preg_replace('/[^A-Za-z]/', '', $accountInfo->name), // $accountInfo->screen_name
              // 'timezone' => $accountInfo->utc_offset, (doesn't work)
              'language' => $accountInfo->lang,
            ));
          }
        } catch( Exception $e ) {
          // Silence?
        }
      }
    }
  }
  
  public function onProcess()
  {
    $settings = Engine_Api::_()->getApi('settings', 'core');
    $random = ($settings->getSetting('user.signup.random', 0) == 1);
    $data = $this->getSession()->data;

    // Add email and code to invite session if available
    $inviteSession = new Zend_Session_Namespace('invite');
    if( isset($data['email']) ) {
      $inviteSession->signup_email = $data['email'];
    }
    if( isset($data['code']) ) {
      $inviteSession->signup_code = $data['code'];
    }

    if( $random ) {
      $data['password'] = Engine_Api::_()->user()->randomPass(10);
    }

    // Create user
    // Note: you must assign this to the registry before calling save or it
    // will not be available to the plugin in the hook
    $this->_registry->user = $user = Engine_Api::_()->getDbtable('users', 'user')->createRow();
    $user->setFromArray($data);
    $user->save();
    
    Engine_Api::_()->user()->setViewer($user);

    // Increment signup counter
    Engine_Api::_()->getDbtable('statistics', 'core')->increment('user.creations');
    
    if( $user->verified && $user->enabled ) {
      // Create activity for them
      Engine_Api::_()->getDbtable('actions', 'activity')->addActivity($user, $user, 'signup');
      // Set user as logged in if not have to verify email
      Engine_Api::_()->user()->getAuth()->getStorage()->write($user->getIdentity());
    }

    $mailType = null;
    $mailParams = array(
      'host' => $_SERVER['HTTP_HOST'],
      'email' => $user->email,
      'date' => time(),
      'recipient_title' => $user->getTitle(),
      'recipient_link' => $user->getHref(),
      'recipient_photo' => $user->getPhotoUrl('thumb.icon'),
      'object_link' => Zend_Controller_Front::getInstance()->getRouter()->assemble(array(), 'user_login', true),
    );

    // Add password to email if necessary
    if( $random ) {
      $mailParams['password'] = $data['password'];
    }

    // Mail stuff
    switch( $settings->getSetting('user.signup.verifyemail', 0) ) {
      case 0:
        // only override admin setting if random passwords are being created
        if( $random ) {
          $mailType = 'core_welcome_password';
        }
        break;

      case 1:
        // send welcome email
        $mailType = ($random ? 'core_welcome_password' : 'core_welcome');
        break;

      case 2:
        // verify email before enabling account
        $verify_table = Engine_Api::_()->getDbtable('verify', 'user');
        $verify_row = $verify_table->createRow();
        $verify_row->user_id = $user->getIdentity();
        $verify_row->code = md5($user->email
            . $user->creation_date
            . $settings->getSetting('core.secret', 'staticSalt')
            . (string) rand(1000000, 9999999));
        $verify_row->date = $user->creation_date;
        $verify_row->save();
        
        $mailType = ($random ? 'core_verification_password' : 'core_verification');
        
        $mailParams['object_link'] = Zend_Controller_Front::getInstance()->getRouter()->assemble(array(
              'action' => 'verify',
              'email' => $user->email,
              'verify' => $verify_row->code
            ), 'user_signup', true);
        break;

      default:
        // do nothing
        break;
    }
    
    if( $mailType ) {
      Engine_Api::_()->getApi('mail', 'core')->sendSystem(
        $user,
        $mailType,
        $mailParams
      );
    }
    
    // Attempt to connect facebook
    if( !empty($_SESSION['facebook_signup']) ) {
      try {
        $facebookTable = Engine_Api::_()->getDbtable('facebook', 'user');
        $facebook = $facebookTable->getApi();
        $settings = Engine_Api::_()->getDbtable('settings', 'core');
        if( $facebook && $settings->core_facebook_enable ) {
          $facebookTable->insert(array(
            'user_id' => $user->getIdentity(),
            'facebook_uid' => $facebook->getUser(),
            'access_token' => $facebook->getAccessToken(),
            //'code' => $code,
            'expires' => 0, // @todo make sure this is correct
          ));
        }
      } catch( Exception $e ) {
        // Silence
        if( 'development' == APPLICATION_ENV ) {
          echo $e;
        }
      }
    }
    
    // Attempt to connect twitter
    if( !empty($_SESSION['twitter_signup']) ) {
      try {
        $twitterTable = Engine_Api::_()->getDbtable('twitter', 'user');
        $twitter = $twitterTable->getApi();
        $twitterOauth = $twitterTable->getOauth();
        $settings = Engine_Api::_()->getDbtable('settings', 'core');
        if( $twitter && $twitterOauth && $settings->core_twitter_enable ) {
          $accountInfo = $twitter->account->verify_credentials();
          $twitterTable->insert(array(
            'user_id' => $user->getIdentity(),
            'twitter_uid' => $accountInfo->id,
            'twitter_token' => $twitterOauth->getToken(),
            'twitter_secret' => $twitterOauth->getTokenSecret(),
          ));
        }
      } catch( Exception $e ) {
        // Silence?
        if( 'development' == APPLICATION_ENV ) {
          echo $e;
        }
      }
    }
  }

  public function onAdminProcess($form)
  {
    $settings = Engine_Api::_()->getApi('settings', 'core');
    $values = $form->getValues();
    $settings->user_signup = $values;
    if( $values['inviteonly'] == 1 ) {
      $step_table = Engine_Api::_()->getDbtable('signup', 'user');
      $step_row = $step_table->fetchRow($step_table->select()->where('class = ?', 'User_Plugin_Signup_Invite'));
      $step_row->enable = 0;
    }

    $form->addNotice('Your changes have been saved.');
  }

}