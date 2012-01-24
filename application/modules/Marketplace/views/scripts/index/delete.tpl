<?php
/**
 * 
 *
 * @category   Application_Extensions
 * @package    Marketplace
 * @copyright  Copyright 2010 
 * * 
 * @version    $Id: delete.tpl 7244 2010-09-01 01:49:53Z john $
 * 
 */
?>

<div class="headline">
  <h2>
    <?php echo $this->translate('Marketplace Listings');?>
  </h2>
  <div class="tabs">
    <?php
      // Render the menu
      echo $this->navigation()
        ->menu()
        ->setContainer($this->navigation)
        ->render();
    ?>
  </div>
</div>

<div class='global_form'>
  <form method="post" class="global_form">
    <div>
      <div>
      <h3><?php echo $this->translate('Delete Marketplace Listing?');?></h3>
      <p>
        <?php echo $this->translate('Are you sure that you want to delete the marketplace listing with the title "%1$s" last modified %2$s? It will not be recoverable after being deleted.', $this->marketplace->title,$this->timestamp($this->marketplace->modified_date)); ?>
      </p>
      <br />
      <p>
        <input type="hidden" name="confirm" value="true"/>
        <button type='submit'><?php echo $this->translate('Delete');?></button>
        <?php echo $this->translate('or');?> <a href='<?php echo $this->url(array(), 'marketplace_manage', true) ?>'><?php echo $this->translate('cancel');?></a>
      </p>
    </div>
    </div>
  </form>
</div>