<?php

class Slider_Form_Admin_Slide extends Engine_Form
{

  public function init()
  {
    $this
      ->setMethod('post')
      ->setAttrib('class', 'global_form_box')
      ->setAttrib('enctype', 'multipart/form-data');

    $this->addElement('Text', 'title', array(
      'label' => 'Title',
      'allowEmpty' => true,
      'filters' => array('StringTrim'),
    ));

    $this->addElement('Textarea', 'description', array(
      'label' => 'Description',
      'allowEmpty' => true,
      'filters' => array('StringTrim'),
    ));
    $this->addElement('Text', 'link', array(
                                              'label' => 'Link',
                                              'allowEmpty' => true,
                                              'validators' => array(new Slider_Validate_Uri())
                                           )
                     );
    $this->addElement('Dummy','link-desc', array(
      'description' => "ex. http://webhive.com.ua"
	));
    $settings = Engine_Api::_()->getApi('settings', 'core');
    $this->addElement('File', 'image', array(
      'label' => 'Image',
      'destination' => APPLICATION_PATH . DIRECTORY_SEPARATOR . 'temporary',
      'required' => true,
      'allowEmpty' => false,
      'validators' => array('IsImage',
                            array('validator' => 'Count', 'options' => array(false, 1)),
                            array('validator' => 'Size', 'options' => array(false, 'max' => 2097152)),
                            array('validator' => 'ImageSize', 'options' => array(false,
                                                                                 'minwidth' => $settings->getSetting('slide_width', 640),
                                                                                 'minheight' => $settings->getSetting('slide_height', 480)
                                                                                 )
                                 ),
                           )
    ));
	$this->addElement('Dummy','file-desc', array(
      'description' => "Image minimal width: {$settings->getSetting('slide_width', 640)}px, minimal height: {$settings->getSetting('slide_height', 480)}px."
	));
    $this->addElement('Radio', 'enable_slide', array(
      'multiOptions' => array(
        1 => 'Enable slide.',
        0 => 'Disable slide.'
      ),
      'value' => 1,
    ));
    $tmp_element = $this->getElement('enable_slide');
    $tmp_element->removeDecorator('label');
    // Buttons
    $this->addElement('Button', 'submit', array(
      'label' => 'Add Slide',
      'type' => 'submit',
      'ignore' => true,
      'decorators' => array('ViewHelper')
    ));

    $this->addElement('Cancel', 'cancel', array(
      'label' => 'cancel',
      'link' => true,
      'prependText' => ' or ',
      'href' => '',
      'onClick'=> 'javascript:parent.Smoothbox.close();',
      'decorators' => array(
        'ViewHelper'
      )
    ));
    $this->addDisplayGroup(array('submit', 'cancel'), 'buttons');
    $button_group = $this->getDisplayGroup('buttons');


  }


}
