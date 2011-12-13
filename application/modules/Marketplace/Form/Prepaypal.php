<?php

/**
 * 
 *
 * @category   Application_Extensions
 * @package    Marketplace
 * @copyright  Copyright 2010 

 * @category   Application_Extensions
 * @package    Marketplace
 * @copyright  Copyright 2010 
 * * 
 */
class Marketplace_Form_Prepaypal extends Engine_Form
{
  public function init()
  {
    $this->setTitle('Buyer Info')
      ->setDescription('Enter your e-mail for purchase.')
      ->setAttrib('name', 'marketplaces_prepaypal');

    $this->addElement('Text', 'marketplaces_email', array(
      'required' => true,
      'allowEmpty' => false,
      'filters' => array(
        'StringTrim',
      ),
      'validators' => array(
        'EmailAddress'
      ),
      'tabindex' => 1,
      'autofocus' => 'autofocus',
    ));

    // Init submit
    $this->addElement('Button', 'submit', array(
      'label' => 'I want to buy',
      'type' => 'submit',
      'ignore' => true,
      'tabindex' => 2,
    ));
  }

}
