<?php
/**
 * 
 *
 * @category   Application_Extensions
 * @package    Marketplace
 * @copyright  Copyright 2010 
 * * 
 * @version    $Id: Global.php 7244 2010-09-01 01:49:53Z john $
 * 
 */

/**
 * @category   Application_Extensions
 * @package    Marketplace
 * @copyright  Copyright 2010 
 * * 
 */
class Marketplace_Form_Admin_Global extends Engine_Form
{
  public function init()
  {
    $this
      ->setTitle('Global Settings')
      ->setDescription('These settings affect all members in your community.');
    
    $this->addElement('Text', 'marketplace_mainpaypal', array(
      'label' => 'Paypal Account',
      'description' => 'Main paypal account',
      'value' => Engine_Api::_()->getApi('settings', 'core')->getSetting('marketplace.mainpaypal', ''),
    ));

    $this->addElement('Text', 'marketplace_page', array(
      'label' => 'Listings Per Page',
      'description' => 'How many marketplace listings will be shown per page? (Enter a number between 1 and 999)',
      'value' => Engine_Api::_()->getApi('settings', 'core')->getSetting('marketplace.page', 10),
    ));

	if(Engine_Api::_()->marketplace()->authorizeIsActive()){
		$this->addElement('Checkbox', 'marketplace_authorize_testmode', array(
		  'label' => '',
		  'description' => 'Authorize.net Test Mode',
		  'value' => Engine_Api::_()->getApi('settings', 'core')->getSetting('marketplace.authorize.testmode', '0'),
		));
	}

	if(Engine_Api::_()->marketplace()->upsIsActive()){
		$this->addElement('Text', 'marketplace_ups_license', array(
		  'label' => 'Ups License Number',
		  'description' => 'Please enter Ups License Number.',
		  'value' => Engine_Api::_()->getApi('settings', 'core')->getSetting('marketplace.ups.license', ''),
		));

		$this->addElement('Text', 'flat_shipping_rate', array(
		  'label' => 'Flat Shipping Rate',
		  'description' => 'Please enter Flat Shipping Rate without "$" sign. Only integer or float value.',
		  'value' => Engine_Api::_()->getApi('settings', 'core')->getSetting('flat.shipping.rate', 0),
		));
	}
    // UPS
    $this->addElement('Text', 'marketplace_ups_access', array(
      'label' => 'UPS Access',
      'value' => Engine_Api::_()->getApi('settings', 'core')->getSetting('marketplace.ups.access'),
    ));
    $this->addElement('Text', 'marketplace_ups_userid', array(
      'label' => 'UPS User ID',
      'value' => Engine_Api::_()->getApi('settings', 'core')->getSetting('marketplace.ups.userid'),
    ));
    $this->addElement('Text', 'marketplace_ups_password', array(
      'label' => 'UPS Password',
      'value' => Engine_Api::_()->getApi('settings', 'core')->getSetting('marketplace.ups.password'),
    ));

    // FedEx
    $this->addElement('Text', 'marketplace_fedex_acckey', array(
      'label' => 'FedEx Acckey',
      'value' => Engine_Api::_()->getApi('settings', 'core')->getSetting('marketplace.fedex.acckey'),
    ));
    $this->addElement('Text', 'marketplace_fedex_accpass', array(
      'label' => 'FedEx Accpass',
      'value' => Engine_Api::_()->getApi('settings', 'core')->getSetting('marketplace.fedex.accpass'),
    ));
    $this->addElement('Text', 'marketplace_fedex_accnum', array(
      'label' => 'FedEx Accnum',
      'value' => Engine_Api::_()->getApi('settings', 'core')->getSetting('marketplace.fedex.accnum'),
    ));
    $this->addElement('Text', 'marketplace_fedex_accmeter', array(
      'label' => 'FedEx Accmeter',
      'value' => Engine_Api::_()->getApi('settings', 'core')->getSetting('marketplace.fedex.accmeter'),
    ));

    // Add submit button
    $this->addElement('Button', 'submit', array(
      'label' => 'Save Changes',
      'type' => 'submit',
      'ignore' => true
    ));
  }
}
