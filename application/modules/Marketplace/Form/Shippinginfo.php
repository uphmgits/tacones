<?php
/**
 * @category   Application_Extensions
 * @package    Marketplace
 * @copyright  Copyright 2012 
 */
class Marketplace_Form_Shippinginfo extends Engine_Form
{
  public function init()
  {
    $this->setTitle('Shipping Info')
         ->setAttrib('name', 'marketplaces_shippinginfo');

    $this->addElement('Text', 'name', array(
      'required' => true,
      'allowEmpty' => false,
      'label' => 'Name',
      'filters' => array(
        'StringTrim',
      ),
      'tabindex' => 1,
      'autofocus' => 'autofocus',
    ));

    $this->addElement('Text', 'email', array(
      'required' => true,
      'allowEmpty' => false,
      'label' => 'Email',
      'filters' => array(
        'StringTrim',
      ),
      'validators' => array(
        'EmailAddress'
      ),
      'tabindex' => 2,
    ));

    $this->addElement('Textarea', 'billing_address', array(
      'required' => true,
      'allowEmpty' => false,
      'label' => 'Billing Info',
      'filters' => array(
        'StringTrim',
      ),
      'tabindex' => 3,
    ));

    $this->addElement('Textarea', 'shipping_address', array(
      'required' => true,
      'allowEmpty' => false,
      'label' => 'Shipping Info',
      'filters' => array(
        'StringTrim',
      ),
      'tabindex' => 4,
    ));

    $this->addElement('Text', 'phone', array(
      'label' => 'Phone',
      'filters' => array(
        'StringTrim',
      ),
      'tabindex' => 5,
    ));
    $this->addElement('Text', 'cell_phone', array(
      'label' => 'Phone Cell',
      'filters' => array(
        'StringTrim',
      ),
      'tabindex' => 6,
    ));
  
    // Init submit
    $this->addElement('Button', 'submit', array(
      'label' => 'Next',
      'type' => 'submit',
      'ignore' => true,
      'tabindex' => 7,
    ));
  }

}
