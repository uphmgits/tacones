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
    
    $this->setTitle('Post New Review')
      ->setDescription('Compose your new review below, then click "Post Review" to publish the review.')
      ->setAttrib('name', 'reviews_create');

    // init to
    $this->addElement('Text', 'to', array(
        'label'=>'Member',
        'autocomplete'=>'off'));

    Engine_Form::addDefaultDecorators($this->to);

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
    
    
    
    Engine_Form::addDefaultDecorators($this->toValues);      
      
    $this->addElement('Select', 'rating', array(
      'label' => 'Rating',
      'description' => 'How do you rate this member?',
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
      'label' => 'Title',
      'description' => 'Enter a title for your review',
      'allowEmpty' => false,
      'required' => true,
      'filters' => array(
        'StripTags',
        new Engine_Filter_Censor(),
        new Engine_Filter_StringLength(array('max' => '127')),
    )));


    // Description
    $this->addElement('Textarea', 'body', array(
      'label' => 'Your Review',
      'description' => 'Type your detailed review about this member in the space below',
      'required' => true,
      'allowEmpty' => false,
      'filters' => array(
        new Engine_Filter_Censor(),
      ),
    ));
    
    
    $this->addElement('Checkbox', 'recommend', array(
      'label' => 'I would recommend this member to a friend!',
      //'value' => 1
    ));     
    
    $this->addElement('Textarea', 'pros', array(
      'label' => 'Pros',
      'description' => 'Please list any pros of this member (optional)',
      'filters' => array(
        new Engine_Filter_Censor(),
      ),
      'attribs' => array('rows'=>3)
    ));
    
    $this->addElement('Textarea', 'cons', array(
      'label' => 'Cons',
      'description' => 'Please list any cons of this member (optional)',
      'filters' => array(
        new Engine_Filter_Censor(),
      ),
      'attribs' => array('rows'=>3)
    ));
    
    $this->addElement('Text', 'keywords',array(
      'label'=>'Tags (Keywords)',
      'autocomplete' => 'off',
      'description' => 'Separate tags with commas.',
      'filters' => array(
        new Engine_Filter_Censor(),
      ),
    ));
    $this->keywords->getDecorator("Description")->setOption("placement", "append");    
   
    // Add subforms
    if (!$this->_item){
      $customFields = new Review_Form_Custom_Fields();
    }
    else $customFields = new Review_Form_Custom_Fields(array('item'=>$this->getItem()));
    
    $this->addSubForms(array(
      'customField' => $customFields
    ));
    
    // View
    $availableLabels = array(
      'everyone'              => 'Everyone',
      'registered'            => 'Registered Members',
      'owner_network'         => 'Friends and Networks',
      'owner_member_member'   => 'Friends of Friends',
      'owner_member'          => 'Friends Only',
      'owner'                 => 'Just Me'
    );
    
    
    $options = (array) Engine_Api::_()->authorization()->getAdapter('levels')->getAllowed('review', $user, 'auth_view');
    $options = array_intersect_key($availableLabels, array_flip($options));

    $this->addElement('Select', 'auth_view', array(
      'label' => 'Privacy',
      'description' => 'Who may see this review?',
      'multiOptions' => $options,
      'value' => 'everyone',
    ));
    $this->auth_view->getDecorator('Description')->setOption('placement', 'prepend');

    $options =(array) Engine_Api::_()->authorization()->getAdapter('levels')->getAllowed('review', $user, 'auth_comment');
    $options = array_intersect_key($availableLabels, array_flip($options));

    // Comment
    $this->addElement('Select', 'auth_comment', array(
      'label' => 'Comment Privacy',
      'description' => 'Who may post comments on this review?',
      'multiOptions' => $options,
      'value' => 'registered',
    ));
    $this->auth_comment->getDecorator('Description')->setOption('placement', 'prepend');

    
    $this->addElement('Checkbox', 'search', array(
      'label' => 'Show this review in search results',
      'value' => 1
    ));
    
    // Buttons
    $this->addElement('Button', 'submit', array(
      'label' => 'Post Review',
      'type' => 'submit',
      'ignore' => true,
      'decorators' => array('ViewHelper')
    ));

    $this->addElement('Cancel', 'cancel', array(
      'label' => 'cancel',
      'link' => true,
      'prependText' => ' or ',
      'decorators' => array(
        'ViewHelper'
      )
    ));
    
    $this->addDisplayGroup(array('submit', 'cancel'), 'buttons');
    $button_group = $this->getDisplayGroup('buttons');
    $button_group->addDecorator('DivDivDivWrapper');    

    
  }

  
  public function validateMember($value)
  {
    // Not string?
    if( !is_string($value) || empty($value) ) {
      $this->toValues->getValidator('Engine_Validate_Callback')->setMessage('Please complete this field - it is required.');
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
    
    
    $review = Engine_Api::_()->review()->getOwnerReviewForUser($viewer, $value);
    
    
    if ($review) {
      
      if ($review->authorization()->isAllowed($viewer, 'edit'))
      {
        $this->toValues->getValidator('Engine_Validate_Callback')->setMessage('You have already reviewed this member. To update your review, please use Edit Review function.');
      }
      else 
      {
        $this->toValues->getValidator('Engine_Validate_Callback')->setMessage('You have already reviewed this member.');
      }
      
      return false; 
    }

    return true;
  }
  
  
}