<?php
/**
 * SocialEngine
 *
 * @category   Application_Core
 * @package    Core
 * @copyright  Copyright 2006-2010 Webligo Developments
 * @license    http://www.socialengine.net/license/
 * @version    $Id: AdminMenusController.php 9100 2011-07-23 20:53:08Z shaun $
 * @author     John
 */

/**
 * @category   Application_Core
 * @package    Core
 * @copyright  Copyright 2006-2010 Webligo Developments
 * @license    http://www.socialengine.net/license/
 */
class Core_AdminMenusController extends Core_Controller_Action_Admin
{
  protected $_menus;

  protected $_enabledModuleNames;
  
  public function init()
  {
    // Get list of menus
    $menusTable = Engine_Api::_()->getDbtable('menus', 'core');
    $menusSelect = $menusTable->select();
    $this->view->menus = $this->_menus = $menusTable->fetchAll($menusSelect);

    $this->_enabledModuleNames = Engine_Api::_()->getDbtable('modules', 'core')->getEnabledModuleNames();
  }
  
  public function indexAction()
  {
    $this->view->name = $name = $this->_getParam('name', 'core_main');

    // Get list of menus
    $menus = $this->_menus;

    // Check if selected menu is in list
    $selectedMenu = $menus->getRowMatching('name', $name);
    if( null === $selectedMenu ) {
      throw new Core_Model_Exception('Invalid menu name');
    }
    $this->view->selectedMenu = $selectedMenu;

    // Make select options
    $menuList = array();
    foreach( $menus as $menu ) {
      $menuList[$menu->name] = $this->view->translate($menu->title);
    }
    $this->view->menuList = $menuList;

    // Get menu items
    $menuItemsTable = Engine_Api::_()->getDbtable('menuItems', 'core');
    $menuItemsSelect = $menuItemsTable->select()
      ->where('menu = ?', $name)
      ->order('order');
    if( !empty($this->_enabledModuleNames) ) {
      $menuItemsSelect->where('module IN(?)',  $this->_enabledModuleNames);
    }
    $this->view->menuItems = $menuItems = $menuItemsTable->fetchAll($menuItemsSelect);
  }

  public function createAction()
  {
    $this->view->name = $name = $this->_getParam('name');

    // Get list of menus
    $menus = $this->_menus;

    // Check if selected menu is in list
    $selectedMenu = $menus->getRowMatching('name', $name);
    if( null === $selectedMenu ) {
      throw new Core_Model_Exception('Invalid menu name');
    }
    $this->view->selectedMenu = $selectedMenu;

    // Get form
    $this->view->form = $form = new Core_Form_Admin_Menu_ItemCreate();

    // Check stuff
    if( !$this->getRequest()->isPost() ) {
      return;
    }
    if( !$form->isValid($this->getRequest()->getPost()) ) {
      return;
    }

    // Save
    $values = $form->getValues();
    $label = $values['label'];
    unset($values['label']);

    $menuItemsTable = Engine_Api::_()->getDbtable('menuItems', 'core');

    $menuItem = $menuItemsTable->createRow();
    $menuItem->label = $label;
    $menuItem->params = $values;
    $menuItem->menu = $name;
    $menuItem->module = 'core'; // Need to do this to prevent it from being hidden
    $menuItem->plugin = '';
    $menuItem->submenu = '';
    $menuItem->custom = 1;
    $menuItem->save();

    $menuItem->name = 'custom_' . sprintf('%d', $menuItem->id);
    $menuItem->save();

    $this->view->status = true;
    $this->view->form = null;
  }

  public function createMenuAction()
  {
    // Get form
    $this->view->form = $form = new Core_Form_Admin_Menu_MenuCreate();

    // Check stuff
    if( !$this->getRequest()->isPost() ) {
      return;
    }
    if( !$form->isValid($this->getRequest()->getPost()) ) {
      return;
    }

    // Save
    $values = $form->getValues();

    $menusTable = Engine_Api::_()->getDbtable('menus', 'core');
    
    $menu = $menusTable->createRow();
    $menu->type = 'custom';
    $menu->name = 'custom_temp';
    $menu->title = $values['title'];
    $menu->order = 999;
    $menu->save();

    $menu->name = 'custom_' . sprintf('%d', $menu->id);
    $menu->save();

    $this->view->menu = $menu;
    $this->view->status = true;
    $this->view->form = null;
  }

  public function editAction()
  {
    $this->view->name = $name = $this->_getParam('name');

    // Get menu item
    $menuItemsTable = Engine_Api::_()->getDbtable('menuItems', 'core');
    $menuItemsSelect = $menuItemsTable->select()
      ->where('name = ?', $name);
    if( !empty($this->_enabledModuleNames) ) {
      $menuItemsSelect->where('module IN(?)',  $this->_enabledModuleNames);
    }
    $this->view->menuItem = $menuItem = $menuItemsTable->fetchRow($menuItemsSelect);

    if( !$menuItem ) {
      throw new Core_Model_Exception('missing menu item');
    }

    // Get form
    $this->view->form = $form = new Core_Form_Admin_Menu_ItemEdit();

    // Make safe
    $menuItemData = $menuItem->toArray();
    if( isset($menuItemData['params']) && is_array($menuItemData['params']) ) {
      $menuItemData = array_merge($menuItemData, $menuItemData['params']);
    }
    if( !$menuItem->custom ) {
      $form->removeElement('uri');
    }
    unset($menuItemData['params']);

    // Check stuff
    if( !$this->getRequest()->isPost() ) {
      $form->populate($menuItemData);
      return;
    }
    if( !$form->isValid($this->getRequest()->getPost()) ) {
      return;
    }

    // Save
    $values = $form->getValues();

    $menuItem->label = $values['label'];
    $menuItem->enabled = !empty($values['enabled']);
    unset($values['label']);
    unset($values['enabled']);

    if( $menuItem->custom ) {
      $menuItem->params = $values;
    } 
    if( !empty($values['target']) ) {
      $menuItem->params = array_merge($menuItem->params, array('target' => $values['target']));
    } else if( isset($menuItem->params['target']) ){
      // Remove the target
      $tempParams = array();
      foreach( $menuItem->params as $key => $item ){
        if( $key != 'target' ){
          $tempParams[$key] = $item;
        }
      }
      $menuItem->params = $tempParams; 
    }
    $menuItem->save();
    
    $this->view->status = true;
    $this->view->form = null;
  }

  public function deleteAction()
  {
    $this->view->name = $name = $this->_getParam('name');

    // Get menu item
    $menuItemsTable = Engine_Api::_()->getDbtable('menuItems', 'core');
    $menuItemsSelect = $menuItemsTable->select()
      ->where('name = ?', $name)
      ->order('order ASC');
    if( !empty($this->_enabledModuleNames) ) {
      $menuItemsSelect->where('module IN(?)',  $this->_enabledModuleNames);
    }
    $this->view->menuItem = $menuItem = $menuItemsTable->fetchRow($menuItemsSelect);

    if( !$menuItem || !$menuItem->custom ) {
      throw new Core_Model_Exception('missing menu item');
    }

    // Get form
    $this->view->form = $form = new Core_Form_Admin_Menu_ItemDelete();
    
    // Check stuff
    if( !$this->getRequest()->isPost() ) {
      return;
    }
    if( !$form->isValid($this->getRequest()->getPost()) ) {
      return;
    }

    $menuItem->delete();

    $this->view->form = null;
    $this->view->status = true;
  }

  public function deleteMenuAction()
  {
    $menusTable = Engine_Api::_()->getDbtable('menus', 'core');
    $menuItemsTable = Engine_Api::_()->getDbtable('menuItems', 'core');

    if( ($menu_id = $this->_getParam('menu_id')) ) {
      $menu = $menusTable->fetchRow($menusTable->select()
          ->where('menu_id = ?', $menu_id)
          ->where('type = ?', 'custom'));
    } else if( ($menu_name = $this->_getParam('name')) ) {
      $menu = $menusTable->fetchRow($menusTable->select()
          ->where('name = ?', $menu_name)
          ->where('type = ?', 'custom'));
    } else {
      $menu = null;
    }

    if( !$menu ) {
      throw new Core_Model_Exception('missing menu');
    }

    // Make form
    $this->view->form = $form = new Core_Form_Confirm(array(
      'title' => 'Delete Menu?',
      'description' => 'Are you sure you want to delete this menu? Please ' . 
          'make sure you have removed it from any widgets that you have ' .
          'added it to in the layout editor.',
      'submitLabel' => 'Delete Menu',
      'cancelHref' => 'parent.Smoothbox.close()',
    ));

    // Check stuff
    if( !$this->getRequest()->isPost() ) {
      return;
    }
    
    if( !$form->isValid($this->getRequest()->getPost()) ) {
      return;
    }


    
    // Process
    $menuItemsTable->delete(array(
      'menu = ?' => $menu->name
    ));

    $menu->delete();


    
    $this->view->form = null;
    $this->view->status = true;
  }

  public function orderAction()
  {
    if( !$this->getRequest()->isPost() ) {
      return;
    }
    
    $table = Engine_Api::_()->getDbtable('menuItems', 'core');
    $menuitems = $table->fetchAll($table->select()->where('menu = ?', $this->getRequest()->getParam('menu')));
    foreach( $menuitems as $menuitem ) {
      $order = $this->getRequest()->getParam('admin_menus_item_'.$menuitem->name);
      if( !$order ){
        $order = 999;
      }
      $menuitem->order = $order;
      $menuitem->save();
    }
    return;
  }

}
