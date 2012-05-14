<?php

class Core_Form_Admin_Services_Change extends Engine_Form
{
  public function init()
  {
    $this->setTitle('Change Service Provider');
    
    $this->addElement('Select', 'serviceprovider_id', array(
      'label' => 'Service Type',
      'required' => true,
      'allowEmpty' => false,
    ));
    
    $this->addElement('Button', 'execute', array(
      'label' => 'Change',
      'type' => 'submit',
      'ignore' => true,
    ));
  }
}
