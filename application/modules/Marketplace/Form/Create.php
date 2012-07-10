<?php

/**
 * 
 *
 * @category   Application_Extensions
 * @package    Marketplace
 * @copyright  Copyright 2010 
 * * 
 * @version    $Id: Create.php 7244 2010-09-01 01:49:53Z john $
 * 
 */

/**
 * @category   Application_Extensions
 * @package    Marketplace
 * @copyright  Copyright 2010 
 * * 
 */
class Marketplace_Form_Create extends Engine_Form
{
  public function init()
  {
    $this->setTitle('Post New Listing')
      ->setDescription('Compose your new marketplace listing below, then click "Post Listing" to publish the listing.')
      ->setAttrib('name', 'marketplaces_create');

    $user = Engine_Api::_()->user()->getViewer();
    $user_level = Engine_Api::_()->user()->getViewer()->level_id;

    $a_tree = Engine_Api::_()->marketplace()->tree_list_load_all(); // tree_list_load_array(array(0));
    Engine_Api::_()->marketplace()->tree_select($a_tree,'',1);

    $categoryId = Zend_Controller_Front::getInstance()->getRequest()->getParam('category', 0);

    $newcategories = Engine_Api::_()->marketplace()->gettemp();
    // prepare categories
    $categories = Engine_Api::_()->marketplace()->getCategories();
    if( count($categories) != 0 ) {
      $categories_prepared[0] = "";
      foreach( $newcategories as $k=>$e) {//$category ) {
          $categories_prepared[$k] = $e;
      }

      // category field
      $this->addElement('Select', 'category_id', array(
        'label' => 'Category',
        'multiOptions' => $categories_prepared,
        'attribs' => array( 'onchange' => 'location.replace("' . 
                              "" . "/marketplaces/create/" . 
                            '"+this.value)' ),
        'value' => $categoryId
      ));
      
    }

    $this->addElement('Text', 'title', array(
      'label' => 'Listing Title',
      'allowEmpty' => false,
      'required' => true,
      'filters' => array(
        'StripTags',
        new Engine_Filter_Censor(),
        new Engine_Filter_StringLength(array('max' => '63')),
      ),
    ));

    $sh = Engine_Api::_()->marketplace()->getInspectionFee(100);
    $this->addElement('Text', 'price', array(
      'label' => 'Price',
      'allowEmpty' => false,
      'description' => "Considering the cost of shipping and handling the final price will be increased for {$sh}%",
      'required' => true,
      'validators' => array(
        array('NotEmpty', true),
    		array('Float', true),
      ),
    ));
    $this->price->getDecorator('Description')->setOptions(array('placement' => 'APPEND'));

    $this->addElement('Textarea', 'body', array(
      'label' => 'Description',
      'filters' => array(
        'StripTags',
        new Engine_Filter_HtmlSpecialChars(),
        new Engine_Filter_EnableLinks(),
        new Engine_Filter_Censor(),
      ),
    ));

    

    $allowed_upload = Engine_Api::_()->authorization()->getPermission($user_level, 'marketplace', 'photo');
    if( $allowed_upload ) {
      $this->addElement('File', 'photo', array(
        'label' => 'Main Photo'
      ));
      $this->photo->addValidator('Extension', false, 'jpg,png,gif');
    }

	
	if(Engine_Api::_()->marketplace()->authorizeIsActive()){
		$this->addElement('Text', 'authorize_login', array(
		  'label' => 'Authorize.net Login',
		  'allowEmpty' => false,
		  'required' => true,
		  'filters' => array(
			'StripTags',
			new Engine_Filter_Censor(),
			new Engine_Filter_StringLength(array('max' => '63')),
		  ),
		  'validators' => array(
			array('NotEmpty', true),
		   ),
		));
		$this->addElement('Text', 'authorize_key', array(
		  'label' => 'Authorize.net Secret Key',
		  'allowEmpty' => false,
		  'required' => true,
		  'filters' => array(
			'StripTags',
			new Engine_Filter_Censor(),
			new Engine_Filter_StringLength(array('max' => '63')),
		  ),
		  'validators' => array(
			array('NotEmpty', true),
		   ),
		));
	}else{
		$this->addElement('Text', 'business_email', array(
		  'label' => 'Business Email',
		  'allowEmpty' => false,
		  'required' => true,
		  'config'=>'{"unit":"USD"}',
		  'filters' => array(
			'StripTags',
			new Engine_Filter_Censor(),
			new Engine_Filter_StringLength(array('max' => '63')),
		  ),
			  'validators' => array(
			array('NotEmpty', true),
			array('EmailAddress', true),
		   ),
		));
		$this->business_email->getDecorator('Description')->setOption('placement', 'append');
	}

/*
    $this->addElement('Text', 'weight', array(
      'label' => 'Weight',
      'description' => 'in lbs.',
      'allowEmpty' => false,
      'required' => true,
    ));
    $this->weight->getDecorator('Description')->setOption('placement', 'append');

    $this->addElement('Text', 'length', array(
      'label' => 'Length',
      'description' => 'in inches.',
      'allowEmpty' => false,
      'required' => true,
    ));
    $this->length->getDecorator('Description')->setOption('placement', 'append');

    $this->addElement('Text', 'width', array(
      'label' => 'Width',
      'description' => 'in inches.',
      'allowEmpty' => false,
      'required' => true,
    ));
    $this->width->getDecorator('Description')->setOption('placement', 'append');

    $this->addElement('Text', 'height', array(
      'label' => 'Height',
      'description' => 'in inches.',
      'allowEmpty' => false,
      'required' => true,
    ));
    $this->height->getDecorator('Description')->setOption('placement', 'append');
*/

    // Privacy
    $availableLabels = array(
      'everyone' => 'Everyone',
      'owner_network' => 'Friends and Networks',
      'owner_member_member' => 'Friends of Friends',
      'owner_member' => 'Friends Only',
      'owner' => 'Just Me'
    );
    // Add subforms
    if( !$this->_item ) {
      $customFields = new Marketplace_Form_Custom_Fields();
    } else {
      $customFields = new Marketplace_Form_Custom_Fields(array(
        'item' => $this->getItem()
      ));
    }


    // only category profile question
    $categoryTree = Engine_Api::_()->marketplace()->tree_list_load_path($categoryId); 
    $mainParentCategory = !empty($categoryTree) ? $categoryTree[0]['k_item'] : $categoryId;
    $deletedElemets = array();
    foreach($customFields as $field) {
        if( $field->category_id != $mainParentCategory) {
          $deletedElemets[] = $field->getName();
        }
    }
    foreach($deletedElemets as $name) {
        $customFields->removeElement($name);
    }


    $this->addSubForms(array(
      'fields' => $customFields
    ));
    // View
    $view_options = (array) Engine_Api::_()->authorization()->getAdapter('levels')->getAllowed('marketplace', $user, 'auth_view');
    $view_options = array_intersect_key($availableLabels, array_flip($view_options));

    if( count($view_options) >= 1 ) {
      $this->addElement('Select', 'auth_view', array(
        'label' => 'Privacy',
        'description' => 'Who may see this marketplace listing?',
        'multiOptions' => $view_options,
        'value' => key($view_options),
      ));
      $this->auth_view->getDecorator('Description')->setOption('placement', 'append');
    }

    // Comment
    $comment_options = (array) Engine_Api::_()->authorization()->getAdapter('levels')->getAllowed('marketplace', $user, 'auth_comment');
    $comment_options = array_intersect_key($availableLabels, array_flip($comment_options));

    if( count($comment_options) >= 1 ) {
      $this->addElement('Select', 'auth_comment', array(
        'label' => 'Comment Privacy',
        'description' => 'Who may post comments on this marketplace listing?',
        'multiOptions' => $comment_options,
        'value' => key($comment_options),
      ));
      $this->auth_comment->getDecorator('Description')->setOption('placement', 'append');
    }

    $this->addElement('Button', 'submit', array(
      'label' => 'Post Listing',
      'type' => 'submit',
      /*'decorators' => array(array('ViewScript', array(
        'viewScript' => '_formButtonCancel.tpl',
        'class' => 'form element'
      )))*/
    ));
  }

}
