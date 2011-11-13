<?php
/**
 * 
 *
 * @category   Application_Extensions
 * @package    Marketplace
 * @copyright  Copyright 2010 
 * * 
 * @version    $Id: AdminSettingsController.php 7244 2010-09-01 01:49:53Z john $
 * 
 */

/**
 * @category   Application_Extensions
 * @package    Marketplace
 * @copyright  Copyright 2010 
 * * 
 */
class Marketplace_AdminSettingsController extends Core_Controller_Action_Admin
{
  public function indexAction()
  {
    $this->view->navigation = $navigation = Engine_Api::_()->getApi('menus', 'core')
      ->getNavigation('marketplace_admin_main', array(), 'marketplace_admin_main_settings');

    $this->view->form = $form = new Marketplace_Form_Admin_Global();

    if( $this->getRequest()->isPost()&& $form->isValid($this->getRequest()->getPost()))
    {
      $values = $form->getValues();

      foreach ($values as $key => $value){
        Engine_Api::_()->getApi('settings', 'core')->setSetting($key, $value);
      }

    }
  }

  public function categoriesAction()
  {
    $this->view->navigation = $navigation = Engine_Api::_()->getApi('menus', 'core')
      ->getNavigation('marketplace_admin_main', array(), 'marketplace_admin_main_categories');
	$item = $this->_getParam('category_id', 0);
	$this->view->category_id = $item;
    $this->view->categories = Engine_Api::_()->marketplace()->getCategoriesTree($item);//getCategories();
    $a_tree = Engine_Api::_()->marketplace()->tree_list_load_path($item);
	//  print_r($a_tree);//Engine_Api::_()->marketplace()->tree_list_load_path($item));
	$this->view->a_tree =  $a_tree;//
	$this->view->url = $this->_helper->url;
	//Engine_Api::_()->marketplace()->tree_print($a_tree,$this->_helper->url);

	//$this->tree_print($a_tree);
   
  }

     

//  public function tree_print(&$a_tree,$k_parent=0)
//  {
//    //условие завершения рекурсии
//    //Условие, при котором функция никогда не вызывает сама себя
//
//    //функция empty() - вернет ложь во всех нужных нам случаях:
//    // - элемент массива не определен
//    // - элемент массива определен, но является пустым массивом
//
//    if(empty($a_tree[$k_parent])) return;
//
//    //echo "<ul>";
//    for($i=0;$i<count($a_tree[$k_parent]);$i++)
//    {
//     // echo "<li>".$a_tree[$k_parent][$i]['s_name'];
//      echo $a_tree[$k_parent][$i]['k_item']."_".$a_tree[$k_parent][$i]['s_name'].'=>';
//      //рекурсивный вызов - список всех дочерних элементов нужно вставить
//      //  именно в этом месте:
//      //  <li>название
//      //     ** тут список дочерних элементов, он показывается рекурсивным вызовом **
//      //  </li>
//      $this->tree_print($a_tree,$a_tree[$k_parent][$i]['k_item']);
//     // echo "</li>";
//    }
//   // echo "</ul>";
//  }

  public function addCategoryAction()
  {
    // In smoothbox
    $this->_helper->layout->setLayout('admin-simple');

    // Generate and assign form
    $form = $this->view->form = new Marketplace_Form_Admin_Category();
    $form->setAction($this->getFrontController()->getRouter()->assemble(array()));
    // Check post
    if( $this->getRequest()->isPost() && $form->isValid($this->getRequest()->getPost()) )
    {
      // we will add the category
      $values = $form->getValues();

      $db = Engine_Db_Table::getDefaultAdapter();
      $db->beginTransaction();

      try
      {
        // add category to the database
        // Transaction
        $table = Engine_Api::_()->getDbtable('categories', 'marketplace');

        // insert the marketplace category entry into the database
        $row = $table->createRow();
        $row->user_id   =  1;
        $row->parent_id   =  $this->_getParam('category_id');
        $row->category_name = $values["label"];
        $row->save();

        // change the category of all the marketplaces using that category

        $db->commit();
      }

      catch( Exception $e )
      {
        $db->rollBack();
        throw $e;
      }
      $this->_forward('success', 'utility', 'core', array(
          'smoothboxClose' => 10,
          'parentRefresh'=> 10,
          'messages' => array('')
      ));
    }

    // Output
    $this->renderScript('admin-settings/form.tpl');
  }

  public function deleteCategoryAction()
  {
    // In smoothbox
    $this->_helper->layout->setLayout('admin-simple');
    $id = $this->_getParam('id');
    $this->view->marketplace_id=$id;
    // Check post
    if( $this->getRequest()->isPost())
    {
      $db = Engine_Db_Table::getDefaultAdapter();
      $db->beginTransaction();

      try
      {
        // go through logs and see which marketplace used this category and set it to ZERO
        $marketplaceTable = $this->_helper->api()->getDbtable('marketplaces', 'marketplace');
        $select = $marketplaceTable->select()->where('category_id = ?', $id);
        $marketplaces = $marketplaceTable->fetchAll($select);

        // create permissions
        foreach( $marketplaces as $marketplace )
        {
          //this is not working
          $marketplace->category_id = 0;
          $marketplace->save();
        }

        $row = Engine_Api::_()->marketplace()->getCategory($id);
        // delete the marketplace category in the database
        $row->delete();

        $db->commit();
      }

      catch( Exception $e )
      {
        $db->rollBack();
        throw $e;
      }
      $this->_forward('success', 'utility', 'core', array(
          'smoothboxClose' => 10,
          'parentRefresh'=> 10,
          'messages' => array('')
      ));
    }

    // Output
    $this->renderScript('admin-settings/delete.tpl');
  }

  public function editCategoryAction()
  {
    // In smoothbox
    $this->_helper->layout->setLayout('admin-simple');
    $form = $this->view->form = new Marketplace_Form_Admin_Category();
    $form->setAction($this->getFrontController()->getRouter()->assemble(array()));

    // Check post
    if( $this->getRequest()->isPost() && $form->isValid($this->getRequest()->getPost()) )
    {
      // Ok, we're good to add field
      $values = $form->getValues();

      $db = Engine_Db_Table::getDefaultAdapter();
      $db->beginTransaction();

      try
      {
        // edit category in the database
        // Transaction
        $row = Engine_Api::_()->marketplace()->getCategory($values["id"]);

        $row->category_name = $values["label"];
        $row->save();
        $db->commit();
      }

      catch( Exception $e )
      {
        $db->rollBack();
        throw $e;
      }
      $this->_forward('success', 'utility', 'core', array(
          'smoothboxClose' => 10,
          'parentRefresh'=> 10,
          'messages' => array('')
      ));
    }

    // Must have an id
    if( !($id = $this->_getParam('id')) )
    {
      throw new Zend_Exception('No identifier specified');
    }

    // Generate and assign form
    $category = Engine_Api::_()->marketplace()->getCategory($id);
    $form->setField($category);

    // Output
    $this->renderScript('admin-settings/form.tpl');
  }
}