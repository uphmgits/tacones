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
    //parent::fieldCreateAction();

    // copy from fieldCreateAction()
    if( $this->_requireProfileType || $this->_getParam('option_id') ) {
      $option = Engine_Api::_()->fields()->getOption($this->_getParam('option_id'), $this->_fieldType);
    } else {
      $option = null;
    }

    // Check type param and get form class
    $cfType = $this->_getParam('type');
    $adminFormClass = null;
    if( !empty($cfType) ) {
      $adminFormClass = Engine_Api::_()->fields()->getFieldInfo($cfType, 'adminFormClass');
    }
    if( empty($adminFormClass) || !@class_exists($adminFormClass) ) {
      $adminFormClass = 'Fields_Form_Admin_Field';
    }

    // Create form
    $this->view->form = $form = new $adminFormClass();
    $form->setTitle(null /*'Add Profile Question'*/);

    // Create alt form
    $this->view->formAlt = $formAlt = new Fields_Form_Admin_Map();
    $formAlt->setAction($this->view->url(array('action' => 'map-create')));

    // Get field data for auto-suggestion
    $fieldMaps = Engine_Api::_()->fields()->getFieldsMaps($this->_fieldType);
    $fieldList = array();
    $fieldData = array();
    foreach( Engine_Api::_()->fields()->getFieldsMeta($this->_fieldType) as $field ) {
      if( $field->type == 'profile_type' ) continue;

      // Ignore fields in the same category as we have selected
      foreach( $fieldMaps as $map ) {
        if( ( !$option || !$map->option_id || $option->option_id == $map->option_id ) && $field->field_id == $map->child_id ) {
          continue 2;
        }
      }

      // Add
      $fieldList[] = $field;
      $fieldData[$field->field_id] = $field->label;
    }
    $this->view->fieldList = $fieldList;
    $this->view->fieldData = $fieldData;

    if( count($fieldData) < 1 ) {
      $this->view->formAlt = null;
    } else {
      $formAlt->getElement('field_id')->setMultiOptions($fieldData);
    }

    // Check method/data
    if( !$this->getRequest()->isPost() ) {
      $form->populate($this->_getAllParams());
      //return;
    }
    else {
          if( !$form->isValid($this->getRequest()->getPost()) ) {
            return;
          }
        
          $request = $this->getRequest()->getPost();
          $category_id = isset($request['category_id']) ? (int)$request['category_id'] : 0;

          $field = Engine_Api::_()->fields()->createField($this->_fieldType, array_merge(array(
            'option_id' => ( is_object($option) ? $option->option_id : '0' ), 'category_id' => $category_id,
          ), $form->getValues()));

          // Should get linked in field creation
          //$fieldMap = Engine_Api::_()->fields()->createMap($field, $option);

          $this->view->status = true;
          $this->view->field = $field->toArray();
          $this->view->option = is_object($option) ? $option->toArray() : array('option_id' => '0');
          $this->view->form = null;

          // Re-render all maps that have this field as a parent or child
          $maps = array_merge(
            Engine_Api::_()->fields()->getFieldsMaps($this->_fieldType)->getRowsMatching('field_id', $field->field_id),
            Engine_Api::_()->fields()->getFieldsMaps($this->_fieldType)->getRowsMatching('child_id', $field->field_id)
          );
          $html = array();
          foreach( $maps as $map ) {
            $html[$map->getKey()] = $this->view->adminFieldMeta($map);
          }
          $this->view->htmlArr = $html;
    }
    // end of fieldCreateAction()


    // remove stuff only relavent to profile questions
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

      $a_tree = Engine_Api::_()->marketplace()->tree_list_load_all();
      $categoriesList = array(0 => '');
      foreach( $a_tree as $cat) {
          $categoriesList[$cat['k_item']] = $cat['s_name'];
      }
      $metaTable = Engine_Api::_()->fields()->getTable('marketplace', 'meta');
      $currentCategory = $metaTable->select()
                                   ->where( "field_id = {$this->_getParam('field_id', 0)}" )
                                   ->query()
                                   ->fetch()
      ;
      $form->addElement('Select', 'category_id', array( 
		    'label' => 'Category', 
		    'multiOptions' => $categoriesList, 
	    ));
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
      $categoriesList = array(0 => '');
      foreach( $a_tree as $cat) {
          $categoriesList[$cat['k_item']] = $cat['s_name'];
      }
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
