<?php



/**
 * Radcodes - SocialEngine Module
 *
 * @category   Application_Extensions
 * @package    Review
 * @copyright  Copyright (c) 2009-2010 Radcodes LLC (http://www.radcodes.com)
 * @license    http://www.radcodes.com/license/
 * @version    $Id$
 * @author     Vincent Van <vincent@radcodes.com>
 */
 
 
 
class Review_Form_Create extends Engine_Form
{
  public $_error = array();

  public function init()
  {
    $user = Engine_Api::_()->user()->getViewer();
    $user_level = Engine_Api::_()->user()->getViewer()->level_id;    

    $this->setTitle('Add Review')
         ->setAttrib('name', 'reviews_create')
         ->setAttrib('id', 'review_form')
         ->setAttrib('class', 'review_form');
    
     // Init to Values
    $this->addElement('Hidden', 'toValues', array(
      'label' => 'Member',
      'required' => true,
      'allowEmpty' => false,
      'order' => 1,
      'validators' => array(
        'NotEmpty',
        new Engine_Validate_Callback(array($this, 'validateMember')),
      ),
      'filters' => array(
        'HtmlEntities'
      ),
    ));
    //Engine_Form::addDefaultDecorators($this->toValues);      
      
    $this->addElement('Select', 'rating', array(
      'allowEmpty' => false,
      'required' => true,    
      'multiOptions' => array(
        1 => 1,
        2 => 2,
        3 => 3,
        4 => 4,
        5 => 5
      ),
      'value' => 3
    ));
    $this->rating->getDecorator('Description')->setOption('placement', 'prepend');      
      
    $this->addElement('Text', 'title', array(
      'label' => 'Subject',
      'allowEmpty' => false,
      'required' => true,
      'filters' => array(
        'StripTags',
        new Engine_Filter_Censor(),
        new Engine_Filter_StringLength(array('max' => '127')),
    )));


    // Description
    $this->addElement('Textarea', 'body', array(
      'label' => 'Message',
      'required' => true,
      'allowEmpty' => false,
      'filters' => array(
        new Engine_Filter_Censor(),
      ),
    ));
    
    // Buttons
    $this->addElement('Button', 'submit', array(
      'label' => 'post ',
      'type' => 'submit',
      'ignore' => true,
      'decorators' => array('ViewHelper')
    ));

    $this->addElement('Button', 'clear', array(
      'label' => 'clear',
      'attribs' => array('onclick' => "$$('#review-form #title').set('value', ''); $$('#review-form #body').set('value', ''); "),
      'decorators' => array(
        'ViewHelper'
      )
    ));
    
    $this->addDisplayGroup(array('clear', 'submit'), 'buttons');
    $button_group = $this->getDisplayGroup('buttons');
    $button_group->addDecorator('DivDivDivWrapper');    

    
  }

  
  public function validateMember($value)
  {
    // Not string?
    if( !is_string($value) || empty($value) ) {
      $this->toValues->getValidator('Engine_Validate_Callback')->setMessage('This member cannot be reviewed.');
      return false;
    }

    $viewer = Engine_Api::_()->user()->getViewer();
    
    $user = Engine_Api::_()->user()->getUser($value);
    if ($viewer->isSelf($user)) {
      $this->toValues->getValidator('Engine_Validate_Callback')->setMessage('You cannot write a review for yourself.');
      return false;
    }
    
    if( !Engine_Api::_()->authorization()->isAllowed('review', $user, 'reviewed') ) {
      $this->toValues->getValidator('Engine_Validate_Callback')->setMessage('This member cannot be reviewed.');
      return false;
    } 
    
    /*$review = Engine_Api::_()->review()->getOwnerReviewForUser($viewer, $value);
    if ($review) {
      if ($review->authorization()->isAllowed($viewer, 'edit')) {
        $this->toValues->getValidator('Engine_Validate_Callback')->setMessage('You have already reviewed this member. To update your review, please use Edit Review function.');
      } else {
        $this->toValues->getValidator('Engine_Validate_Callback')->setMessage('You have already reviewed this member.');
      }
      return false; 
    }*/

    return true;
  }
  
  
}
