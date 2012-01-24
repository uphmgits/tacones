<?php
/**
 * 
 *
 * @category   Application_Extensions
 * @package    Marketplace
 * @copyright  Copyright 2010 
 * * 
 * @version    $Id: success.tpl 7244 2010-09-01 01:49:53Z john $
 * 
 */
?>

<div class='global_form'>
  <form method="post" class="global_form">
    <div>
      <div>
      <h3><?php echo $this->translate('Listing Posted');?></h3>
      <p>
        <?php echo $this->translate('Your listing was successfully published. Would you like to add some photos to it?');?>
      </p>
      <br />
      <p>
        <input type="hidden" name="confirm" value="true"/>
        <button type='submit'><?php echo $this->translate('Add Photos');?></button>
        <?php echo $this->translate('or');?> <a href='<?php echo $this->url(array(), 'marketplace_manage', true) ?>'><?php echo $this->translate('continue to my listing');?></a>
      </p>
    </div>
    </div>
  </form>
</div>