<?php

class Core_Form_Admin_Services_Create extends Engine_Form
{
  public function init()
  {
    $this->setTitle('Create Service');
    
    $this->addElement('Select', 'serviceprovider_id', array(
      'label' => 'Service Type',
      'required' => true,
      'allowEmpty' => false,
    ));
    
    $this->addElement('Text', 'profile', array(
      'label' => 'Profile Name',
    ));
    
    $this->addElement('Button', 'execute', array(
      'label' => 'Add',
      'type' => 'submit',
      'ignore' => true,
    ));
  }
}
