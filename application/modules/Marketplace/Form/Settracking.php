<?php
/**
 * @category   Application_Extensions
 * @package    Marketplace
 * @copyright  Copyright 2012 
 */
class Marketplace_Form_Settracking extends Engine_Form
{
  public function init()
  {    
    $this->setTitle('Set Tracking Namber');

    $this->addElement('Text', 'tracking_fedex', array(
      'label' => 'FedEx tracking number',
      'tabindex' => 1,
      'autofocus' => 'autofocus',
      'filters' => array(
        'StripTags',
        'StringTrim',
        new Engine_Filter_HtmlSpecialChars(),
        new Engine_Filter_EnableLinks(),
        new Engine_Filter_Censor(),
      ),
      'validators' => array(
          array('StringLength', false, array(0, 64)),
      )
    ));

    $this->addElement('Text', 'tracking_ups', array(
      'label' => 'UPS tracking number',
      'tabindex' => 2,
      'filters' => array(
        'StripTags',
        'StringTrim',
        new Engine_Filter_HtmlSpecialChars(),
        new Engine_Filter_EnableLinks(),
        new Engine_Filter_Censor(),
      ),
      'validators' => array(
          array('StringLength', false, array(6, 64)),
      )
    ));

    $this->addElement('Button', 'submit', array(
      'label' => 'Save',
      'type' => 'submit',
      'ignore' => true,
      'tabindex' => 3,
    ));
  }
}
