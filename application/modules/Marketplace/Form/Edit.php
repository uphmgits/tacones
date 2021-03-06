<?php
/**
 * 
 *
 * @category   Application_Extensions
 * @package    Marketplace
 * @copyright  Copyright 2006-2010
 * * 
 * @version    $Id: Edit.php 7244 2010-09-01 01:49:53Z john $
 * 
 */

/**
 * @category   Application_Extensions
 * @package    Marketplace
 * @copyright  Copyright 2006-2010
 * * 
 */
class Marketplace_Form_Edit extends Marketplace_Form_Create
{
  public $_error = array();
  protected $_item;

  public function getItem()
  {
    return $this->_item;
  }

  public function setItem(Core_Model_Item_Abstract $item)
  {
    $this->_item = $item;
    return $this;
  }

  public function init()
  {
    parent::init();
    //print "<pre>";print_r($this->_item);exit;
    if($this->_item->isImported()) {
    	$this->_setFieldBody();
    }
    $this->setTitle('Edit Marketplace Listing')
         ->setDescription('Edit your listing below, then click \"Save Changes\" to save your listing.');

    $this->category_id->setAttrib('onchange', 'location.replace("' . "" . "/marketplaces/edit/" . $this->_item->getIdentity() .
                            '/"+this.value)');

    $this->addElement('Radio', 'cover', array(
      'label' => 'Album Cover',
    ));

    $content = Zend_Registry::get('Zend_Translate')->_("<span><a href='%s'>Add Photos</a></span>");
    $content= sprintf($content, Zend_Controller_Front::getInstance()->getRouter()->assemble(array('controller' => 'photo', 'action' => 'upload', 'subject' => $this->_item->getGuid()), 'marketplace_extended'));
    // Init forgot password link
    $this->addElement('Dummy', 'addphotos', array(
      'content' => $content,
    ));
    $this->submit->setLabel('Save Changes');
  }
  
private function _setFieldBody() {
	$this->removeElement('body');
  	$this->addElement('Textarea', 'body', array(
      'label' => 'Description',
      'filters' => array(
        /*'StripTags',
        new Engine_Filter_HtmlSpecialChars(),
        new Engine_Filter_EnableLinks(),*/
        new Engine_Filter_Censor(),
      )
    ));
  }
}
