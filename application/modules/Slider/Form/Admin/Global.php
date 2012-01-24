<?php

class Slider_Form_Admin_Global extends Engine_Form
{
  public function init()
  {
    
    $this
      ->setTitle('Settings')
      ->setDescription('These settings are used for slider output.');

    $this->addElement('Text', 'time_delete', array(
      'label' => 'Time Delay',
      'description' => 'Seconds between slides transition (Number between 1 and 999)',
      'value' => Engine_Api::_()->getApi('settings', 'core')->getSetting('time_delete', 10),
      'required' => true,
      'validators' => array('Digits', array('validator' => 'Between', 'options' => array(1, 999)))
    ));

    $this->addElement('Text', 'slide_width', array(
      'label' => 'Slide Width',
      'description' => 'Image width in pixels',
      'value' => Engine_Api::_()->getApi('settings', 'core')->getSetting('slide_width', 640),
      'required' => true,
      'validators' => array('Digits')
    ));

    $this->addElement('Text', 'slide_height', array(
      'label' => 'Slide Height',
      'description' => 'Image height in pixels',
      'value' => Engine_Api::_()->getApi('settings', 'core')->getSetting('slide_height', 480),
      'required' => true,
      'validators' => array('Digits')
    ));
    $this->addElement('Text', 'quality', array(
      'label' => 'Image Quality',
      'description' => 'Quality of slides after resizing (between 0 and 100)',
      'value' => Engine_Api::_()->getApi('settings', 'core')->getSetting('quality', 100),
      'required' => true,
      'validators' => array('Digits', array('validator' => 'Between', 'options' => array(0, 100)))
    ));
// Add submit button
    $this->addElement('Button', 'submit', array(
      'label' => 'Save Changes',
      'type' => 'submit',
      'ignore' => true
    ));

  }
}
