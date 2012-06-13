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

    <?php $total_amount = 0; $shipping_fee = 0; $inspection_fee = 0; ?>
    <?php foreach($this->cartitems as $cartitem): ?>
        <?php $marketplace = Engine_Api::_()->getItem('marketplace', $cartitem['marketplace_id']); ?>
        <?php $shipping_fee += $marketplace->shipping * $cartitem['count']; ?>
        <?php $inspection_fee += Engine_Api::_()->marketplace()->getInspectionFee($marketplace->price); ?>
        <?php $total_amount += $marketplace->price * $cartitem['count']; ?>
    <?php endforeach; ?>
    <?php $total_amount_full = $total_amount + $shipping_fee + $inspection_fee; ?>

    <hr/>
    <div class="cart-total-container"> 
      <span><?=$this->translate('SUBTOTAL')?></span>
      <span>$<?=number_format($total_amount, 2);?></span>
    </div>
    <div class="cart-total-container"> 
      <span><?=$this->translate('SHIPPING')?></span>
      <span>$<?=number_format($shipping_fee)?></span>
    </div>
    <div class="cart-total-container"> 
      <span><?=$this->translate('INSPECTION')?></span>
      <span>$<?=number_format($inspection_fee)?></span>
    </div>
    <hr/>
    <div class="cart-total-container"> 
      <span><?=$this->translate('TOTAL')?></span>
      <span>$<?=number_format($total_amount_full, 2);?></span>
    </div>

  <?php else:?>
	  <h2><?=$this->translate('Cart is Empty');?></h2>
  <?php endif;?>
</div>
