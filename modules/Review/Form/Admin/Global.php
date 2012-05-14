<?php



/**
 * Radcodes - SocialEngine Module
 *
 * @category   Application_Extensions
 * @package    Review
 * @copyright  Copyright (c) 2009-2010 Radcodes LLC (http://www.radcodes.com)
 * @license    http://www.radcodes.com/license/
 * @version    $Id$
 * @author     Vincent Van <vincent@radcodes.com>
 */
 
 
 
class Review_Form_Admin_Global extends Engine_Form
{
  public function init()
  {
    $this
      ->setTitle('Global Settings')
      ->setDescription('These settings affect all members in your community.');
    
    $this->addElement('Text', 'review_license', array(
      'label' => 'Review License Key',
      'description' => 'Enter the your license key that is provided to you when you purchased this plugin. If you do not know your license key, please contact Radcodes support team.',
      'value' => Engine_Api::_()->getApi('settings', 'core')->getSetting('review.license', 'XXXX-XXXX-XXXX-XXXX'),
      'filters' => array(
        'StringTrim'
      ),
      'allowEmpty' => false,
      'validators' => array(
        new Radcodes_Lib_Validate_License('review'),
      ),
    ));
     
      
    $this->addElement('Text', 'review_perpage', array(
      'label' => 'Reviews Per Page',
      'description' => 'How many reviews will be shown per page? (Enter a number between 1 and 100)',
      'class' => 'short',
      'value' => Engine_Api::_()->getApi('settings', 'core')->getSetting('review.perpage', 10),
      'validators' => array(
        'Digits',
        new Zend_Validate_Between(1,100),
      ),
    ));

    $this->addElement('Text', 'review_toplimit', array(
      'label' => 'Members Per List',
      'description' => 'How many members will be shown per top list? (Enter a number between 1 and 100)',
      'class' => 'short',
      'value' => Engine_Api::_()->getApi('settings', 'core')->getSetting('review.toplimit', 10),
      'validators' => array(
        'Digits',
        new Zend_Validate_Between(1,100),
      ),
    ));    
    
    // Add submit button
    $this->addElement('Button', 'submit', array(
      'label' => 'Save Changes',
      'type' => 'submit',
      'ignore' => true
    ));
  }
}