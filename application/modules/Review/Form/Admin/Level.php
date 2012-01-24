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
 
 
 
class Review_Form_Admin_Level extends Authorization_Form_Admin_Level_Abstract
{
  
  public function init()
  {
    parent::init();
    
    $this
      ->setTitle('Member Level Settings')
      ->setDescription("These settings are applied on a per member level basis. Start by selecting the member level you want to modify, then adjust the settings for that level below.");
      
    $this->addElement('Radio', 'view', array(
      'label' => 'Allow Viewing of Reviews?',
      'description' => 'Do you want to let members view reviews? If set to no, some other settings on this page may not apply.',
      'multiOptions' => array(
        2 => 'Yes, allow viewing of all reviews, even private ones.',
        1 => 'Yes, allow viewing of reviews.',
        0 => 'No, do not allow reviews to be viewed.',
      ),
      'value' => ( $this->isModerator() ? 2 : 1 ),
    ));
    if( !$this->isModerator() ) {
      unset($this->view->options[2]);
    }
    
    if( !$this->isPublic() ) 
    {
      
	    $this->addElement('Radio', 'reviewed', array(
	      'label' => 'Allow to Be Reviewed?',
	      'description' => 'Would you like members to be reviewed by others? If set to no, others would not be able to write review for members belong to this level.',
	      'multiOptions' => array(
	        1 => 'Yes, let others post reviews.',
	        0 => 'No, do not let others post reviews.'
	      ),
	      'value' => 1,
	    ));        
      
	    $this->addElement('Radio', 'create', array(
	      'label' => 'Allow Creation of Reviews?',
	      'description' => 'Do you want to let members post reviews? If set to no, some other settings on this page may not apply.',
	      'multiOptions' => array(
	        1 => 'Yes, allow creation of reviews.',
	        0 => 'No, do not allow reviews to be created.'
	      ),
	      'value' => 1,
	    ));    
	    
	    $this->addElement('Radio', 'edit', array(
	      'label' => 'Allow Editing of Reviews?',
	      'description' => 'Do you want to let members edit reviews? If set to no, some other settings on this page may not apply.',
	      'multiOptions' => array(
	        2 => 'Yes, allow members to edit all reviews.',
	        1 => 'Yes, allow members to edit their own reviews.',
	        0 => 'No, do not allow members to edit their reviews.',
	      ),
	      'value' => ( $this->isModerator() ? 2 : 1 ),
	    ));
      if( !$this->isModerator() ) {
        unset($this->edit->options[2]);
      }
      
	    $this->addElement('Radio', 'delete', array(
	      'label' => 'Allow Deletion of Reviews?',
	      'description' => 'Do you want to let members delete reviews? If set to no, some other settings on this page may not apply.',
	      'multiOptions' => array(
	        2 => 'Yes, allow members to delete all reviews.',
	        1 => 'Yes, allow members to delete their own reviews.',
	        0 => 'No, do not allow members to delete their reviews.',
	      ),
	      'value' => ( $this->isModerator() ? 2 : 1 ),
	    ));
      if( !$this->isModerator() ) {
        unset($this->delete->options[2]);
      }
	
      // Element: comment
      $this->addElement('Radio', 'comment', array(
        'label' => 'Allow Commenting on Reviews?',
        'description' => 'Do you want to let members of this level comment on reviews?',
        'multiOptions' => array(
          2 => 'Yes, allow members to comment on all reviews, including private ones.',
          1 => 'Yes, allow members to comment on reviews.',
          0 => 'No, do not allow members to comment on reviews.',
        ),
        'value' => ( $this->isModerator() ? 2 : 1 ),
      ));
      if( !$this->isModerator() ) {
        unset($this->comment->options[2]);
      }
      
	    $this->addElement('Radio', 'featured', array(
	      'label' => 'Featured on Creation?',
	      'description' => 'Would you like to mark review as Featured when created?',
	      'multiOptions' => array(
	        1 => 'Yes, mark review as Featured when created.',
	        0 => 'No, do not mark review as Featured when created.'
	      ),
	      'value' => 0,
	    ));      
      
	    // PRIVACY ELEMENTS
	    $this->addElement('MultiCheckbox', 'auth_view', array(
	      'label' => 'Reviews View Privacy',
	      'description' => 'Your members can choose from any of the options checked below when they decide who can see their reviews. If you do not check any options, everyone will be allowed to view reviews.',
	        'multiOptions' => array(
	          'everyone'            => 'Everyone',
	          'registered'          => 'Registered Members',
	          'owner_network'       => 'Friends and Networks',
	          'owner_member_member' => 'Friends of Friends',
	          'owner_member'        => 'Friends Only',
	          'owner'               => 'Just Me'
	        ),
	        'value' => array('everyone', 'registered', 'owner_network','owner_member_member', 'owner_member', 'owner')
	    ));
	
	    $this->addElement('MultiCheckbox', 'auth_comment', array(
	      'label' => 'Review Comment Options',
	      'description' => 'Your members can choose from any of the options checked below when they decide who can post comments on their reviews. If you do not check any options, everyone will be allowed to post comments on reviews.',
	      'description' => '',
	        'multiOptions' => array(
	          'registered'          => 'Registered Members',
	          'owner_network'       => 'Friends and Networks',
	          'owner_member_member' => 'Friends of Friends',
	          'owner_member'        => 'Friends Only',
	          'owner'               => 'Just Me'
	        ),
	        'value' => array('registered', 'owner_network','owner_member_member', 'owner_member', 'owner')
	    ));	    

      
    } // end isPublic()
  }

}