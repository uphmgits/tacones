<?php
/**
 * SocialEngine
 *
 * @category   Application_Core
 * @package    User
 * @copyright  Copyright 2006-2010 Webligo Developments
 * @license    http://www.socialengine.net/license/
 * @version    $Id: Edit.php 8344 2011-01-29 07:46:14Z john $
 * @author     John
 */

/**
 * @category   Application_Core
 * @package    User
 * @copyright  Copyright 2006-2010 Webligo Developments
 * @license    http://www.socialengine.net/license/
 */
class Marketplace_Form_Coupon_Edit extends Engine_Form
{
  public function init()
  {
    $this
      ->setAttrib('id', 'admin_coupon_edit')
      ->setTitle('Edit Coupon')
      ->setAction($_SERVER['REQUEST_URI']);

    // init email
    $this->addElement('Text', 'title', array(
      'label' => 'Coupon Title',
      'required' => true,
      'allowEmpty' => false,
    ));

    // init email
    $this->addElement('Text', 'code', array(
      'label' => 'Coupon Code',
      'allowEmpty' => false,
      'required' => true,
    ));

    // init email
    $this->addElement('Text', 'percent', array(
      'label' => 'Discount (in percent without `%` sign)',
      'required' => true,
      'allowEmpty' => false,
      'validators' => array(
        array('Int', true),
      ),
    ));


    // Buttons
    $this->addElement('Button', 'submit', array(
      'label' => 'Save Changes',
      'type' => 'submit',
      'ignore' => true,
      'decorators' => array('ViewHelper')
    ));

    $this->addElement('Cancel', 'cancel', array(
      'label' => 'cancel',
      'link' => true,
      'prependText' => ' or ',
      'onclick' => 'parent.Smoothbox.close();',
      'decorators' => array(
        'ViewHelper'
      )
    ));

    $this->addDisplayGroup(array('submit', 'cancel'), 'buttons');
    $button_group = $this->getDisplayGroup('buttons');
    $button_group->addDecorator('DivDivDivWrapper');
  }
}