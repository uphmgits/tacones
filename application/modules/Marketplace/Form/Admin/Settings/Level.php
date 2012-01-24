<?php
/**
 * 
 *
 * @category   Application_Extensions
 * @package    Marketplace
 * @copyright  Copyright 2010 
 * * 
 * @version    $Id: Level.php 7244 2010-09-01 01:49:53Z john $
 * 
 */

/**
 * @category   Application_Extensions
 * @package    Marketplace
 * @copyright  Copyright 2010 
 * * 
 */
class Marketplace_Form_Admin_Settings_Level extends Authorization_Form_Admin_Level_Abstract
{
  public function init()
  {
    parent::init();

    // My stuff
    $this
      ->setTitle('Member Level Settings')
      ->setDescription("MARKETPLACE_FORM_ADMIN_LEVEL_DESCRIPTION");

    // Element: view
    $this->addElement('Radio', 'view', array(
      'label' => 'Allow Viewing of Marketplace Items?',
      'description' => 'Do you want to let members view marketplace items? If set to no, some other settings on this page may not apply.',
      'multiOptions' => array(
        2 => 'Yes, allow viewing of all marketplace items, even private ones.',
        1 => 'Yes, allow viewing of marketplace items.',
        0 => 'No, do not allow marketplace to be viewed.',
      ),
      'value' => ( $this->isModerator() ? 2 : 1 ),
    ));
    if( !$this->isModerator() ) {
      unset($this->view->options[2]);
    }

    if( !$this->isPublic() ) {

      // Element: create
      $this->addElement('Radio', 'create', array(
        'label' => 'Allow Creation of Marketplace Items?',
        'description' => 'MARKETPLACE_FORM_ADMIN_LEVEL_CREATE_DESCRIPTION',
        'multiOptions' => array(
          1 => 'Yes, allow creation of marketplace items.',
          0 => 'No, do not allow marketplace items to be created.'
        ),
        'value' => 1,
      ));

      // Element: edit
      $this->addElement('Radio', 'edit', array(
        'label' => 'Allow Editing of Marketplace Items?',
        'description' => 'Do you want to let members edit marketplace items? If set to no, some other settings on this page may not apply.',
        'multiOptions' => array(
          2 => 'Yes, allow members to edit all marketplace items.',
          1 => 'Yes, allow members to edit their own marketplace items.',
          0 => 'No, do not allow members to edit their marketplace items.',
        ),
        'value' => ( $this->isModerator() ? 2 : 1 ),
      ));
      if( !$this->isModerator() ) {
        unset($this->edit->options[2]);
      }

      // Element: delete
      $this->addElement('Radio', 'delete', array(
        'label' => 'Allow Deletion of Marketplace Items?',
        'description' => 'Do you want to let members delete marketplace items? If set to no, some other settings on this page may not apply.',
        'multiOptions' => array(
          2 => 'Yes, allow members to delete all marketplace items.',
          1 => 'Yes, allow members to delete their own marketplace items.',
          0 => 'No, do not allow members to delete their marketplace items.',
        ),
        'value' => ( $this->isModerator() ? 2 : 1 ),
      ));
      if( !$this->isModerator() ) {
        unset($this->delete->options[2]);
      }

      // Element: comment
      $this->addElement('Radio', 'comment', array(
        'label' => 'Allow Commenting on Marketplace?',
        'description' => 'Do you want to let members of this level comment on marketplace?',
        'multiOptions' => array(
          2 => 'Yes, allow members to comment on all marketplace items, including private ones.',
          1 => 'Yes, allow members to comment on marketplace.',
          0 => 'No, do not allow members to comment on marketplace.',
        ),
        'value' => ( $this->isModerator() ? 2 : 1 ),
      ));
      if( !$this->isModerator() ) {
        unset($this->comment->options[2]);
      }

      // Element: photo
      $this->addElement('Radio', 'photo', array(
        'label' => 'Allow Uploading of Photos?',
        'description' => 'Do you want to let members upload photos to a marketplace listing? If set to no, the option to upload photos will not appear.',
        'multiOptions' => array(
          1 => 'Yes, allow photo uploading to marketplace.',
          0 => 'No, do not allow photo uploading.'
        ),
        'value' => 1,
      ));

      // Element: auth_view
      $this->addElement('MultiCheckbox', 'auth_view', array(
        'label' => 'Marketplace Listing Privacy',
        'description' => 'MARKETPLACE_FORM_ADMIN_LEVEL_AUTHVIEW_DESCRIPTION',
        'multiOptions' => array(
          'everyone'            => 'Everyone',
          'owner_network'       => 'Friends and Networks',
          'owner_member_member' => 'Friends of Friends',
          'owner_member'        => 'Friends Only',
          'owner'               => 'Just Me'
        ),
        'value' => array('everyone', 'owner_network','owner_member_member', 'owner_member', 'owner')
      ));

      // Element: auth_comment
      $this->addElement('MultiCheckbox', 'auth_comment', array(
        'label' => 'Marketplace Comment Options',
        'description' => 'MARKETPLACE_FORM_ADMIN_LEVEL_AUTHCOMMENT_DESCRIPTION',
        'description' => '',
        'multiOptions' => array(
          'everyone'            => 'Everyone',
          'owner_network'       => 'Friends and Networks',
          'owner_member_member' => 'Friends of Friends',
          'owner_member'        => 'Friends Only',
          'owner'               => 'Just Me'
        ),
        'value' => array('everyone', 'owner_network','owner_member_member', 'owner_member', 'owner')
      ));

      // Element: max
      $this->addElement('Text', 'max', array(
        'label' => 'Maximum Allowed Marketplace Items',
        'description' => 'Enter the maximum number of allowed marketplace items. The field must contain an integer, use zero for unlimited.',
        'validators' => array(
          array('Int', true),
          new Engine_Validate_AtLeast(0),
        ),
      ));
      
    }
  }
}