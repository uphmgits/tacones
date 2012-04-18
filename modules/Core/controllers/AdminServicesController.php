<?php
/**
 * SocialEngine
 *
 * @category   Application_Core
 * @package    Core
 * @copyright  Copyright 2006-2010 Webligo Developments
 * @license    http://www.socialengine.net/license/
 * @version    $Id: AdminReportController.php 8988 2011-06-15 01:35:25Z john $
 * @author     John Boehr <j@webligo.com>
 */

/**
 * @category   Application_Core
 * @package    Core
 * @copyright  Copyright 2006-2010 Webligo Developments
 * @license    http://www.socialengine.net/license/
 */
class Core_AdminServicesController extends Core_Controller_Action_Admin
{
  public function indexAction()
  {
    /*
    $db = Engine_Db_Table::getDefaultAdapter();
    $serviceLocator = new Engine_ServiceLocator(array(
      'backend' => array(
        'type' => 'DbTable',
        'dbAdapter' => $db,
        'dbTable' => 'engine4_core_services',
      ),
    ));
    $captcha = $serviceLocator->factory('captcha');
    echo $captcha->render();
    die();
    */
    
    // Get paginator
    $db = Engine_Db_Table::getDefaultAdapter();
    $select = $db->select()
        ->from('engine4_core_services')
        ->order('type ASC');
    $this->view->paginator = $paginator = Zend_Paginator::factory($select);
    
    // Get service types
    $stmt = $db->select()
        ->from('engine4_core_servicetypes')
        ->query();
    $serviceTypes = array();
    foreach( $stmt->fetchAll() as $item ) {
      $serviceTypes[$item['type']] = $item;
    }
    $this->view->serviceTypes = $serviceTypes;
    
    // Get service providers
    $stmt = $db->select()
        ->from('engine4_core_serviceproviders')
        ->query();
    $serviceProviders = array();
    foreach( $stmt->fetchAll() as $item ) {
      $serviceProviders[$item['type']][$item['name']] = $item;
    }
    $this->view->serviceProviders = $serviceProviders;
  }
  
  public function createAction()
  {
    // Make form
    $this->view->form = $form = new Core_Form_Admin_Services_Create();
    
    // Add options
    $db = Engine_Db_Table::getDefaultAdapter();
    $serviceProviders = $db->select()
        ->from('engine4_core_serviceproviders', array('serviceprovider_id', 'type', 'engine4_core_serviceproviders.title', 'name'))
        ->joinLeft('engine4_core_servicetypes', 'engine4_core_servicetypes.type=engine4_core_serviceproviders.type', new Zend_Db_Expr('engine4_core_servicetypes.title AS servicetype_title'))
        ->query()
        ->fetchAll();
    
    $multiOptions = array('' => '');
    foreach( $serviceProviders as $serviceProvider ) {
      $multiOptions[$serviceProvider['servicetype_title']][$serviceProvider['serviceprovider_id']] = $serviceProvider['title'];
    }
    asort($multiOptions);
    
    $form->getElement('serviceprovider_id')->setMultiOptions($multiOptions);
    
    // Check
    if( !$this->getRequest()->isPost() ) {
      return;
    }
    
    if( !$form->isValid($this->getRequest()->getPost()) ) {
      return;
    }
    
    // Process
    $values = $form->getValues();
    
    // Get serviceprovider
    $serviceProvider = $db->select()
        ->from('engine4_core_serviceproviders')
        ->where('serviceprovider_id = ?', $values['serviceprovider_id'])
        ->limit(1)
        ->query()
        ->fetch();
    
    // Check if service already exists
    $existingService = $db->select()
        ->from('engine4_core_services')
        ->where('type = ?', $serviceProvider['type'])
        ->where('name = ?', $serviceProvider['name'])
        ->where('profile = ?', ( !empty($values['profile']) ? $values['profile'] : 'default'))
        ->limit(1)
        ->query()
        ->fetch();
    
    if( !empty($existingService) ) {
      return $form->addError('A service with that profile name has already been configured.');
    }
    
    // Insert new service
    $db->insert('engine4_core_services', array(
      'type' => $serviceProvider['type'],
      'name' => $serviceProvider['name'],
      'profile' => ( !empty($values['profile']) ? $values['profile'] : 'default'),
      'enabled' => false,
      'config' => '',
    ));
    
    $serviceIdentity = $db->lastInsertId();
    
    // Redirect to edit page
    return $this->_helper->redirector->gotoRoute(array('action' => 'edit', 'service_id' => $serviceIdentity));
  }
  
  public function changeAction()
  {
    // Get service identity
    if( !($serviceIdentity = $this->_getParam('service_id')) ) {
      return;
    }
    
    // Get service
    $db = Engine_Db_Table::getDefaultAdapter();
    $service = $db->select()
        ->from('engine4_core_services')
        ->where('service_id = ?', $serviceIdentity)
        ->limit(1)
        ->query()
        ->fetch();
    if( !$service ) {
      return;
    }
    
    // Get service provider info
    $serviceProvider = $db->select()
        ->from('engine4_core_serviceproviders')
        ->where('type = ?', $service['type'])
        ->where('name = ?', $service['name'])
        ->limit(1)
        ->query()
        ->fetch();
    if( !$serviceProvider ) {
      return;
    }
    
    // Get service type info
    $serviceType = $db->select()
        ->from('engine4_core_servicetypes')
        ->where('type = ?', $service['type'])
        ->limit(1)
        ->query()
        ->fetch();
    if( !$serviceType ) {
      return;
    }
    
    
    // Make form
    $this->view->form = $form = new Core_Form_Admin_Services_Change();
    
    // Add options
    $db = Engine_Db_Table::getDefaultAdapter();
    $serviceProviders = $db->select()
        ->from('engine4_core_serviceproviders', array('serviceprovider_id', 'type', 'engine4_core_serviceproviders.title', 'name'))
        ->joinLeft('engine4_core_servicetypes', 'engine4_core_servicetypes.type=engine4_core_serviceproviders.type', new Zend_Db_Expr('engine4_core_servicetypes.title AS servicetype_title'))
        ->where('engine4_core_serviceproviders.type = ?', $service['type'])
        ->query()
        ->fetchAll();
    
    $multiOptions = array('' => '');
    foreach( $serviceProviders as $serviceProvider ) {
      $multiOptions[$serviceProvider['servicetype_title']][$serviceProvider['serviceprovider_id']] = $serviceProvider['title'];
    }
    asort($multiOptions);
    
    $form->getElement('serviceprovider_id')->setMultiOptions($multiOptions);
    
    // Check
    if( !$this->getRequest()->isPost() ) {
      return;
    }
    
    if( !$form->isValid($this->getRequest()->getPost()) ) {
      return;
    }
    
    // Process
    $values = $form->getValues();
    
    // Get serviceprovider
    $newServiceProvider = $db->select()
        ->from('engine4_core_serviceproviders')
        ->where('serviceprovider_id = ?', $values['serviceprovider_id'])
        ->limit(1)
        ->query()
        ->fetch();
    
    // Insert new service
    $db->update('engine4_core_services', array(
      //'type' => $newServiceProvider['type'],
      'name' => $newServiceProvider['name'],
      //'profile' => ( !empty($values['profile']) ? $values['profile'] : 'default'),
      'enabled' => false,
      'config' => '',
    ), array(
      'service_id = ?' => $serviceIdentity,
    ));
    
    // Redirect to edit page
    return $this->_helper->redirector->gotoRoute(array('action' => 'edit', 'service_id' => $serviceIdentity));
  }
  
  public function editAction()
  {
    // Get service identity
    if( !($serviceIdentity = $this->_getParam('service_id')) ) {
      return;
    }
    
    // Get service
    $db = Engine_Db_Table::getDefaultAdapter();
    $service = $db->select()
        ->from('engine4_core_services')
        ->where('service_id = ?', $serviceIdentity)
        ->limit(1)
        ->query()
        ->fetch();
    if( !$service ) {
      return;
    }
    
    // Get service provider info
    $serviceProvider = $db->select()
        ->from('engine4_core_serviceproviders')
        ->where('type = ?', $service['type'])
        ->where('name = ?', $service['name'])
        ->limit(1)
        ->query()
        ->fetch();
    if( !$serviceProvider ) {
      return;
    }
    
    // Get service type info
    $serviceType = $db->select()
        ->from('engine4_core_servicetypes')
        ->where('type = ?', $service['type'])
        ->limit(1)
        ->query()
        ->fetch();
    if( !$serviceType ) {
      return;
    }
    
    // Get plugin
    $class = $serviceProvider['class'];
    if( !class_exists($class, true) ) {
      throw new Exception('Unable to load class: ' . $class);
    }
    $instance = new $class(array(
      'view' => $this->view,
    ));
    
    // Call init hook
    $instance->onInit();
    
    // Assign form
    $this->view->form = $form = $instance->getForm();
    
    // Initialize some stuff
    $form->setTitle($serviceType['title'] . ': ' . $serviceProvider['title']);
    
    // Call view hook
    if( !empty($service['config']) &&
        is_array($tmp = Zend_Json::decode($service['config'])) ) {
      $instance->onView($tmp);
    } else {
      $instance->onView();
    }
    
    // Check method
    if( !$this->getRequest()->isPost() ) {
      return;
    }
    
    if( !$form->isValid($this->getRequest()->getPost()) ) {
      return;
    }
    
    // Call submit hook
    $instance->onSubmit((array) $form->getValues());
    
    // Call process hook
    $config = $instance->onProcess();
    if( !is_array($config) ) {
      return $form->addError('Service plugin did not return an array.');
    }
    
    // Save configuration
    $enabled = (bool) $form->getElement('enabled')->getValue();
    
    $count = $db->update('engine4_core_services', array(
      'config' => Zend_Json::encode($config),
      'enabled' => $enabled,
    ), array(
      'service_id = ?' => $serviceIdentity,
    ));
    
    if( !$count ) {
      $form->addError('Unable to save changes.');
    } else {
      $form->addNotice('Your changes have been saved.');
    }
  }
}
