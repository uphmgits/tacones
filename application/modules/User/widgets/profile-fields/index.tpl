<?php
/**
 * SocialEngine
 *
 * @category   Application_Core
 * @package    User
 * @copyright  Copyright 2006-2010 Webligo Developments
 * @license    http://www.socialengine.net/license/
 * @version    $Id: index.tpl 7244 2010-09-01 01:49:53Z john $
 * @author     John
 */
?>
<style>
    #profile_photo_holder {
      float: left;
      margin-right: 10px;
    }
    #profile_fields_details {
      overflow: hidden;
    }
    #profile_fields_details .profile_fields {
      margin-top: 0;
    }
    #profile_fields_details .profile_fields > ul > li > span + span {
      width: 300px;
    }
    #profile_fields_details h4 {
      padding: 0;
      margin: 0;
      border: none;
    }
    #profile_fields_details h4 > span {
      position: static;
    }
    #profile_fields_details .profile_fields > ul > li + li {
      margin-top: 0;
    }
</style>

<div id='profile_photo_holder'>
  <?php echo $this->itemPhoto($this->subject()) ?>
</div>
<div id='profile_fields_details'>
  <?php echo $this->fieldValueLoop($this->subject(), $this->fieldStructure) ?>
</div>
