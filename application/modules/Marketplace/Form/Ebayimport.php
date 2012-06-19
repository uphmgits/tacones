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
  	$this->addElement('Button', 'submit', array(
      'label' => 'Import Listings',
      'type' => 'submit',
      /*'decorators' => array(array('ViewScript', array(
        'viewScript' => '_formButtonCancel.tpl',
        'class' => 'form element'
      )))*/
    ));
    
    $this->getElement('submit')->setDecorators(array('ViewHelper', array(array('td'=>'HtmlTag'), array('tag'=>'td', 'colspan'=>3)), array(array('tr'=>'HtmlTag'), array('tag'=>'tr'))));
	$this->getElement('submit')->setOrder(100);
  }

}
