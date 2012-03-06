<?php
/**
 * 
 *
 * @category   Application_Extensions
 * @package    Marketplace
 * @copyright  Copyright 2010 
 * * 
 * @version    $Id: _formButtonCancel.tpl 7244 2010-09-01 01:49:53Z john $
 * 
 */
?>

<div id="submit-wrapper" class="form-wrapper">
  <div id="submit-label" class="form-label"> </div>
  <div id="submit-element" class="form-element">
    <button type="submit" id="done" name="done">
      <?php echo ( $this->element->getLabel() ? $this->element->getLabel() : $this->translate('Save Changes')) ?>
    </button>
      <?php echo $this->translate('or');?>
   <?php echo $this->htmlLink(array('route' => 'marketplace_manage', 'action' => 'manage'), $this->translate('cancel')) ?>
  </div>
</div>