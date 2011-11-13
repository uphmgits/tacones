<?php
/**
 * SocialEngine
 *
 * @category   Application_Core
 * @package    Core
 * @copyright  Copyright 2006-2010 Webligo Developments
 * @license    http://www.socialengine.net/license/
 * @version    $Id: Action.php 9382 2011-10-14 00:41:45Z john $
 * @author     John Boehr <j@webligo.com>
 */

/**
 * @category   Application_Core
 * @package    Install
 * @copyright  Copyright 2006-2010 Webligo Developments
 * @license    http://www.socialengine.net/license/
 */
class Core_Form_Admin_Report_Action extends Engine_Form
{
  public function init()
  {
    $this
      ->setTitle('Take Action')
      ->setDescription('What would you like to do?')
      ->setAction($_SERVER['REQUEST_URI']);

    $this->addElement('Checkbox', 'action', array(
      'label' => 'Delete Content?',
      'value' => 0,
    ));
    $this->getElement('action')->removeDecorator('Description');

    $this->addElement('Radio', 'action_poster', array(
      'label' => 'Poster Action',
      'multiOptions' => array(
        'none' => 'Nothing',
        'disable' => 'Disable Member',
        'delete' => 'Delete Member',
      ),
      'value' => 'none',
    ));

    $this->addElement('Checkbox', 'ban', array(
      'label' => 'Ban Poster IP Address?',
      'value' => 0,
    ));
    $this->getElement('ban')->removeDecorator('Description');

    $this->addElement('Checkbox', 'dismiss', array(
      'label' => 'Dismiss Report?',
      'value' => 1,
    ));
    $this->getElement('dismiss')->removeDecorator('Description');

    $this->addElement('Hash', 'token');

    $this->addElement('Button', 'execute', array(
      'type' => 'submit',
      'label' => 'Submit',
      'decorators' => array(
        'ViewHelper',
      ),
    ));

    $this->addElement('Cancel', 'cancel', array(
      'link' => true,
      'prependText' => ' or ',
      'label' => 'cancel',
      'href' => 'javascript:parent.Smoothbox.close();',
      'decorators' => array(
        'ViewHelper',
      ),
    ));

    $this->addDisplayGroup(array('execute', 'cancel'), 'buttons');
  }
}