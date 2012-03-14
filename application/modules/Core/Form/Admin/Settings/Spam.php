<?php
/**
 * SocialEngine
 *
 * @category   Application_Core
 * @package    Core
 * @copyright  Copyright 2006-2010 Webligo Developments
 * @license    http://www.socialengine.net/license/
 * @version    $Id: Spam.php 9099 2011-07-22 21:56:22Z john $
 * @author     John
 */

/**
 * @category   Application_Core
 * @package    Core
 * @copyright  Copyright 2006-2010 Webligo Developments
 * @license    http://www.socialengine.net/license/
 */
class Core_Form_Admin_Settings_Spam extends Engine_Form
{
  protected $_captcha_options = array(
        1 => 'Yes, make members complete the CAPTCHA form.',
        0 => 'No, do not show a CAPTCHA form.',
  );

  public function init()
  {
    // Set form attributes
    //$this->setTitle('Spam & Banning Tools');
    $this->setDescription('CORE_FORM_ADMIN_SETTINGS_SPAM_DESCRIPTION');

    // init ip-range ban
    $translator = $this->getTranslator();
    if( $translator ) {
      $description = sprintf($translator->translate('CORE_FORM_ADMIN_SETTINGS_SPAM_IPBANS_DESCRIPTION'), Engine_IP::normalizeAddress(Engine_IP::getRealRemoteAddress()));
    } else {
      $description = 'CORE_FORM_ADMIN_SETTINGS_SPAM_IPBANS_DESCRIPTION';
    }
    $this->addElement('Textarea', 'bannedips', array(
      'label' => 'IP Address Ban',
      'description' => $description,
    ));

    // init email bans
    $this->addElement('Textarea', 'bannedemails', array(
      'label' => 'Email Address Ban',
      'description' => 'CORE_FORM_ADMIN_SETTINGS_SPAM_EMAILBANS_DESCRIPTION',
    ));

    // init username bans
    $this->addElement('Textarea', 'bannedusernames', array(
      'label' => 'Profile Address Ban',
      'description' => 'CORE_FORM_ADMIN_SETTINGS_SPAM_USERNAMEBANS_DESCRIPTION',
    ));

    // init censored words
    $this->addElement('Textarea', 'bannedwords', array(
      'label' => 'Censored Words',
      'description' => 'CORE_FORM_ADMIN_SETTINGS_SPAM_CENSOR_DESCRIPTION',
    ));

    // init profile
    $this->addElement('Radio', 'comment', array(
      'label' => 'Require users to enter validation code when commenting?',
      'multiOptions' => $this->_captcha_options,
      'value' => 0,
    ));

    $this->addElement('Radio', 'signup', array(
      'label' => 'Require new users to enter validation code when signing up?',
      'multiOptions' => $this->_captcha_options,
      'value' => 0,
    ));

    $this->addElement('Radio', 'invite', array(
      'label' => 'Require users to enter validation code when inviting others?',
      'multiOptions' => $this->_captcha_options,
      'value' => 0,
    ));

    $this->addElement('Radio', 'login', array(
      'label' => 'Require users to enter validation code when signing in?',
      'multiOptions' => $this->_captcha_options,
      'value' => 0,
    ));

    $this->addElement('Radio', 'contact', array(
      'label' => 'Require users to enter validation code when using the contact form?',
      'multiOptions' => array(
        2 => 'Yes, make everyone complete the CAPTCHA form.',
        1 => 'Yes, make visitors complete CAPTCHA, but members are exempt.',
        0 => 'No, do not show a CAPTCHA form to anyone.',
      ),
      'value' => 0,
    ));

    // recaptcha
    if( $translator ) {
      $description = sprintf($translator->translate('You can obtain API credentials at: %1$s'), 
          $this->getView()->htmlLink('https://www.google.com/recaptcha', 
              'https://www.google.com/recaptcha'));
    } else {
      $description = null;
    }
    
    $this->addElement('Text', 'recaptchapublic', array(
      'label' => 'ReCaptcha Public Key',
      'description' => $description,
      'filters' => array(
        'StringTrim',
      ),
    ));
    $this->getElement('recaptchapublic')
        ->getDecorator('Description')
        ->setOption('escape', false);
    
    $this->addElement('Text', 'recaptchaprivate', array(
      'label' => 'ReCaptcha Private Key',
      'description' => $description,
      'filters' => array(
        'StringTrim',
      ),
    ));
    $this->getElement('recaptchaprivate')
        ->getDecorator('Description')
        ->setOption('escape', false);


    $this->addElement('Text', 'commenthtml', array(
      'label' => 'Allow HTML in Comments?',
      'description' => 'CORE_ADMIN_FORM_SETTINGS_SPAM_COMMENTHTML_DESCRIPTION'
    ));

    // init submit
    $this->addElement('Button', 'submit', array(
      'label' => 'Save Changes',
      'type' => 'submit',
      'ignore' => true,
    ));
  }
}
