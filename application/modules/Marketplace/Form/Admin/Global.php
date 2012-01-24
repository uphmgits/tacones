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

    // Add submit button
    $this->addElement('Button', 'submit', array(
      'label' => 'Save Changes',
      'type' => 'submit',
      'ignore' => true
    ));
  }
}
