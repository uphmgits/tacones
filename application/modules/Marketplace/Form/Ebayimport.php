<?php

/**
 * 
 *
 * @category   Application_Extensions
 * @package    Marketplace
 * @copyright  Copyright 2010 
 * * 
 * @version    $Id: Create.php 7244 2010-09-01 01:49:53Z john $
 * 
 */

/**
 * @category   Application_Extensions
 * @package    Marketplace
 * @copyright  Copyright 2010 
 * * 
 */
class Marketplace_Form_Ebayimport extends Engine_Form
{
public function init()
  {
    $this->setTitle('Eby Import')
      ->setDescription('Start here to get going with your eBay import')
      ->setAttrib('name', 'marketplaces_ebayreq');

    $user = Engine_Api::_()->user()->getViewer();
    $user_level = Engine_Api::_()->user()->getViewer()->level_id;
       
    $this->addElement('Text', 'ebaysellerid', array(
      'label' => 'Your Ebay ID',
      'allowEmpty' => false,
      'required' => true,
      'filters' => array(
        'StripTags',
        new Engine_Filter_StringLength(array('max' => '33')),
      ),
    ));
    
    
      $this->addElement('Text', 'postfrom', array(
      'label' => 'Listings Posted From',
      'allowEmpty' => false,
      'required' => true
      ));
      
      $this->addElement('Text', 'postthru', array(
      'label' => 'Listings Posted Thru',
      'allowEmpty' => false,
      'required' => true
      ));
    

  $this->addElement('Button', 'clear', array(
      'label' => 'Clear Form',
      'type' => 'input'
      
    ));
    
    $this->addElement('Button', 'retrieve', array(
      'label' => 'Start Retrieving',
      'type' => 'submit'
    ));
    
    $this->addElement('Button', 'import', array(
      'label' => 'Start Import',
      'type' => 'submit'
    ));
  
    /*
    $this->addElement('Button', 'import', array(
      'label' => 'Start Importing',
      'type' => 'submit',
      'decorators' => array(array('ViewScript', array(
        'viewScript' => '_formButtonCancel.tpl',
        'class' => 'form element'
      )))
    ));
    */
  }

}
