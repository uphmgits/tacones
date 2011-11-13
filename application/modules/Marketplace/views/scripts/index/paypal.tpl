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
    <?php echo $this->translate('Result of payment');?>
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

<?php if ($this->page == 'success') : ?>
    <?php echo $this->translate('Success. Thank you for your order.'); ?>
<?php endif; ?>

<?php if ($this->page == 'cancel') : ?>
    <?php echo $this->translate('Canceled. The order was canceled.'); ?>
<?php endif; ?>

<?php if ($this->page == 'ipn') : ?>

<?php endif; ?>



