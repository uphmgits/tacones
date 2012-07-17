<?php
/**
 * @category   Application_Extensions
 * @package    Marketplace
 * @copyright  Copyright 2012
 */
?>
<?=$this->content()->renderWidget('marketplace.topbanner', array('pageName' => 'cart'))?>

<div class="cart-container">
  <?php if(!empty($this->cartitems) && count($this->cartitems)): ?>
	  <?=$this->form->render($this)?>

    <?php $total_amount = 0; $inspection_fee = 0; ?>
    <?php foreach($this->cartitems as $cartitem): ?>
        <?php $marketplace = Engine_Api::_()->getItem('marketplace', $cartitem['marketplace_id']); ?>
        <?php $inspection_fee += Engine_Api::_()->marketplace()->getInspectionFee($marketplace->price) * $cartitem['count']; ?>
        <?php $total_amount += $marketplace->price * $cartitem['count']; ?>
    <?php endforeach; ?>
    <?php $total_amount_full = $total_amount + $inspection_fee; ?>

    <hr/>
    <div class="cart-total-container"> 
      <span><?=$this->translate('SUBTOTAL')?></span>
      <span>$<?=number_format($total_amount_full, 2);?></span>
    </div>
    <div class="cart-total-container"> 
      <span><?=$this->translate('SHIPPING AND HANDLING')?></span>
      <span>$<?=number_format(0, 2)?></span>
    </div>
    <hr/>
    <div class="cart-total-container"> 
      <span><?=$this->translate('TOTAL')?></span>
      <span>$<?=number_format($total_amount_full, 2);?></span>
    </div>
    <hr/>
    <div style="float: right;">
  	  <?=$this->htmlLink("javascript:void(0);", $this->translate('Next'), array('class' => 'button', "onclick" => "$('marketplaces_shippinginfo').submit();"))?>
    </div>
 	  <?=$this->htmlLink(array('route' => 'marketplace_general', 'action' => 'cart'), $this->translate('back'), array('class' => 'fmarketplace_button'))?>
  <?php else:?>
	  <h2><?=$this->translate('Cart is Empty');?></h2>
  <?php endif;?>
</div>
