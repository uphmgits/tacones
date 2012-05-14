<?php
/**
 * 
 *
 * @category   Application_Core
 * @package    User
 * @copyright  Copyright 2010 Webligo Developments
 * * 
 * @version    $Id: Filter.php 7354 2010-09-11 05:52:11Z john $
 * @author     John
 */

/**
 * @category   Application_Core
 * @package    User
 * @copyright  Copyright 2010 Webligo Developments
 * * 
 */
class Marketplace_Form_Filter extends Engine_Form
{
  public function init()
  {
    $this
      ->clearDecorators()
      ->addDecorator('FormElements')
      ->addDecorator('Form')
      ->addDecorator('HtmlTag', array('tag' => 'div', 'class' => 'search'))
      ->addDecorator('HtmlTag2', array('tag' => 'div', 'class' => 'clear'))
      ;

    $this
      ->setAttribs(array(
        'id' => 'filter_form',
        'class' => 'global_form_box',
        'style' => 'display: none;'
      ));

    $this->addElement('Hidden', 'order', array(
      'order' => 10001,
    ));

    $this->addElement('Hidden', 'order_direction', array(
      'order' => 10002,
    ));

    // Set default action
    $this->setAction(Zend_Controller_Front::getInstance()->getRouter()->assemble(array()));
  }
}