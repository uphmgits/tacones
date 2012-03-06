<?php
/**
 * 
 *
 * @category   Application_Extensions
 * @package    Marketplace
 * @copyright  Copyright 2010 
 * * 
 * @version    $Id: AdminFieldsController.php 7244 2010-09-01 01:49:53Z john $
 * 
 */

/**
 * @category   Application_Extensions
 * @package    Marketplace
 * @copyright  Copyright 2010 
 * * 
 */
class Marketplace_AdminFieldsController extends Fields_Controller_AdminAbstract
{
  protected $_fieldType = 'marketplace';

  protected $_requireProfileType = false;

  public function indexAction()
  {
    // Make navigation
    $this->view->navigation = $navigation = Engine_Api::_()->getApi('menus', 'core')
      ->getNavigation('marketplace_admin_main', array(), 'marketplace_admin_main_fields');

    parent::indexAction();
  }

  public function fieldCreateAction(){
    parent::fieldCreateAction();


    // remove stuff only relavent to profile questions
    $form = $this->view->form;

    if($form){
      $form->setTitle('Add Marketplace Question');

      $display = $form->getElement('display');
      $display->setLabel('Show on marketplace page?');
      $display->setOptions(array('multiOptions' => array(
          1 => 'Show on marketplace page',
          0 => 'Hide on marketplace page'
        )));

      $search = $form->getElement('search');
      $search->setLabel('Show on the search options?');
      $search->setOptions(array('multiOptions' => array(
          0 => 'Hide on the search options',
          1 => 'Show on the search options'
        )));
    }
  }

  public function fieldEditAction() {

    $metaTable = Engine_Api::_()->fields()->getTable('marketplace', 'meta');
    if( $request = $this->getRequest()->getPost() ) {
        if( isset($request['category_id']) ) {
            $metaTable->update( array( 'category_id' => (int)$request['category_id'] ), "field_id = {$this->_getParam('field_id', 0)}" );
        }
    }
  

    parent::fieldEditAction();

    // remove stuff only relavent to profile questions
    $form = $this->view->form;

    if($form){
      $form->setTitle('Edit Marketplace Question');

      $display = $form->getElement('display');
      $display->setLabel('Show on marketplace page?');
      $display->setOptions(array('multiOptions' => array(
          1 => 'Show on marketplace page',
          0 => 'Hide on marketplace page'
        )));

      $search = $form->getElement('search');
      $search->setLabel('Show on the search options?');
      $search->setOptions(array('multiOptions' => array(
          0 => 'Hide on the search options',
          1 => 'Show on the search options'
        )));

      $a_tree = Engine_Api::_()->marketplace()->tree_list_load_all();
      Engine_Api::_()->marketplace()->tree_select($a_tree,'',1);
      $categoriesList = Engine_Api::_()->marketplace()->gettemp();
      $categoriesList = array_merge(array(0 => ''), $categoriesList);

      $currentCategory = $metaTable->select()
                                   ->where( "field_id = {$this->_getParam('field_id', 0)}" )
                                   ->query()
                                   ->fetch()
      ;
      $form->addElement('Select', 'category_id', array( 
		    'label' => 'Category', 
		    'multiOptions' => $categoriesList, 
	    ));
      if( !empty($currentCategory) ) $form->category_id->setValue( $currentCategory['category_id'] );

    }
  }
}
