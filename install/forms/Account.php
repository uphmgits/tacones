<?php
/**
 * SocialEngine
 *
 * @category   Application_Core
 * @package    Install
 * @copyright  Copyright 2006-2010 Webligo Developments
 * @license    http://www.socialengine.net/license/
 * @version    $Id: Account.php 8400 2011-02-04 21:29:50Z john $
 * @author     John
 */

/**
 * @category   Application_Core
 * @package    Install
 * @copyright  Copyright 2006-2010 Webligo Developments
 * @license    http://www.socialengine.net/license/
 */
class Install_Form_Account extends Engine_Form
{
  public function init()
  {
    // init site title
    $this->addElement('Text', 'site_title', array(
      'label' => 'Community Title',
      'required' => true,
      'description' => 'Provide a brief, descriptive title for your community.',
      'value' => 'My Community',
      'class' => 'long',
      'validators' => array(
        array('NotEmpty', true),
      ),
    ));
    $this->site_title->getDecorator('Description')->setOption('placement', 'APPEND');
    $this->site_title->getValidator('NotEmpty')
      ->setMessage('Please fill in the Community Title.', 'isEmpty');

    // init email
    $this->addElement('Text', 'email', array(
      'label' => 'Admin Email Address',
      'required' => true,
      'allowEmpty' => false,
      'description' => 'You will sign in with this email address.',
      'validators' => array(
        array('NotEmpty', true),
        array('EmailAddress', true),
      ),
      'filters' => array(
        'StringTrim',
      ),
    ));
    $this->email->getDecorator('Description')->setOption('placement', 'APPEND');
    $this->email->getValidator('NotEmpty')
      ->setMessage('Please fill in the Email Address.', 'notEmptyInvalid')
      ->setMessage('Please fill in the Email Address.', 'isEmpty');

    // init password
    $this->addElement('Password', 'password', array(
      'label' => 'Admin Password',
      'required' => true,
      'allowEmpty' => false,
      'description' => 'You will sign in with this password.',
      'validators' => array(
        array('NotEmpty', true),
        array('StringLength', false, array(6, 32)),
      ),
    ));
    $this->password->getDecorator('Description')->setOption('placement', 'APPEND');
    $this->password->renderPassword = true;
    $this->password->getValidator('NotEmpty')
      ->setMessage('Please fill in the Password.', 'notEmptyInvalid')
      ->setMessage('Please fill in the Password.', 'isEmpty');

    // init password again
    $this->addElement('Password', 'password_conf', array(
      'label' => 'Admin Password Again',
      'required' => true,
      'allowEmpty' => false,
      'description' => 'Enter the same password again to confirm.',
      'validators' => array(
        array('NotEmpty', true),
      ),
    ));
    $this->password_conf->getDecorator('Description')->setOption('placement', 'APPEND');
    $this->password_conf->renderPassword = true;
    $this->password_conf->getValidator('NotEmpty')
      ->setMessage('Please fill in the Password Again.', 'notEmptyInvalid')
      ->setMessage('Please fill in the Password Again.', 'isEmpty');

    // init username
    $this->addElement('Text', 'username', array(
      'label' => 'Admin Profile Address',
      'required' => true,
      'allowEmpty' => false,
      'description' => 'Choose what the end of your profile URL will look like. For example, if you enter "admin", your profile URL will look something like "www.yoursite.com/profile/admin"',
      'validators' => array(
        array('NotEmpty', true),
        array('Alnum', true),
        array('StringLength', true, array(4, 64)),
        array('Regex', true, array('/^[a-z0-9]/i')),
      ),
    ));
    $this->username->getDecorator('Description')->setOption('placement', 'APPEND');
    $this->username->getValidator('NotEmpty')
      ->setMessage('Please fill in the Profile Address.', 'notEmptyInvalid')
      ->setMessage('Please fill in the Profile Address.', 'isEmpty');

    $this->addElement('Select', 'timezone', array(
      'label' => 'Timezone',
      'value' => 'US/Pacific',
      'multiOptions' => array(
        'US/Pacific' => '(UTC-8) Pacific Time (US & Canada)',
        'US/Mountain' => '(UTC-7) Mountain Time (US & Canada)',
        'US/Central' => '(UTC-6) Central Time (US & Canada)',
        'US/Eastern' => '(UTC-5) Eastern Time (US & Canada)',
        'America/Halifax' => '(UTC-4)  Atlantic Time (Canada)',
        'America/Anchorage' => '(UTC-9)  Alaska (US & Canada)',
        'Pacific/Honolulu' => '(UTC-10) Hawaii (US)',
        'Pacific/Samoa' => '(UTC-11) Midway Island, Samoa',
        'Etc/GMT-12' => '(UTC-12) Eniwetok, Kwajalein',
        'Canada/Newfoundland' => '(UTC-3:30) Canada/Newfoundland',
        'America/Buenos_Aires' => '(UTC-3) Brasilia, Buenos Aires, Georgetown',
        'Atlantic/South_Georgia' => '(UTC-2) Mid-Atlantic',
        'Atlantic/Azores' => '(UTC-1) Azores, Cape Verde Is.',
        'Europe/London' => 'Greenwich Mean Time (Lisbon, London)',
        'Europe/Berlin' => '(UTC+1) Amsterdam, Berlin, Paris, Rome, Madrid',
        'Europe/Athens' => '(UTC+2) Athens, Helsinki, Istanbul, Cairo, E. Europe',
        'Europe/Moscow' => '(UTC+3) Baghdad, Kuwait, Nairobi, Moscow',
        'Iran' => '(UTC+3:30) Tehran',
        'Asia/Dubai' => '(UTC+4) Abu Dhabi, Kazan, Muscat',
        'Asia/Kabul' => '(UTC+4:30) Kabul',
        'Asia/Yekaterinburg' => '(UTC+5) Islamabad, Karachi, Tashkent',
        'Asia/Dili' => '(UTC+5:30) Bombay, Calcutta, New Delhi',
        'Asia/Katmandu' => '(UTC+5:45) Nepal',
        'Asia/Omsk' => '(UTC+6) Almaty, Dhaka',
        'India/Cocos' => '(UTC+6:30) Cocos Islands, Yangon',
        'Asia/Krasnoyarsk' => '(UTC+7) Bangkok, Jakarta, Hanoi',
        'Asia/Hong_Kong' => '(UTC+8) Beijing, Hong Kong, Singapore, Taipei',
        'Asia/Tokyo' => '(UTC+9) Tokyo, Osaka, Sapporto, Seoul, Yakutsk',
        'Australia/Adelaide' => '(UTC+9:30) Adelaide, Darwin',
        'Australia/Sydney' => '(UTC+10) Brisbane, Melbourne, Sydney, Guam',
        'Asia/Magadan' => '(UTC+11) Magadan, Soloman Is., New Caledonia',
        'Pacific/Auckland' => '(UTC+12) Fiji, Kamchatka, Marshall Is., Wellington',
      )
    ));

    // Submit
    $this->addElement('Button', 'submit', array(
      'label' => 'Continue',
      'type' => 'submit',
      'ignore' => true,
    ));

    //$this->addDisplayGroup(array('submit'), 'buttons');

    // Modify decorators
    $this->loadDefaultDecorators();
    $this->getDecorator('FormErrors')->setSkipLabels(true);
  }
}