<?php
/**
 * 
 *
 * @category   Application_Extensions
 * @package    Marketplace
 * @copyright  Copyright 2010 
 * * 
 * @version    $Id: cart.tpl 7244 2010-09-01 01:49:53Z john $
 * 
 */
?>
<?=$this->content()->renderWidget('marketplace.topbanner', array('pageName' => 'cart'))?>

<div class="cart-container">

  <h2><?=$this->translate('ORDER REVIEW')?></h2>

  <table class="checkout-info-table" width="100%">
    <tr>
      <th><?=$this->translate('Name')?></th>
      <th><?=$this->translate('Billing Address')?></th>
      <th><?=$this->translate('Shipping Address')?></th>
      <th><?=$this->translate('Phone Number')?></th>
      <th><?=$this->translate('Email')?></th>
    </tr>
    <tr>
      <td><?=$this->shippingInfo['name']?></td>
      <td><?=nl2br($this->shippingInfo['billing_address'])?></td>
      <td><?=nl2br($this->shippingInfo['shipping_address'])?></td>
      <td><?=$this->shippingInfo['phone']?></td>
      <td><?=$this->shippingInfo['email']?></td>
    </tr>
  </table>
  <hr/>

  <?php if(!empty($this->cartitems) && count($this->cartitems)): ?>
    <?php $i = 0; $colInRow = 5; $inspection_fee = 0; $total_amount = 0; ?> 
    <?php $this->addHelperPath(APPLICATION_PATH . '/application/modules/Fields/View/Helper', 'Fields_View_Helper'); ?>

    <?php foreach($this->cartitems as $cartitem): ?>

      	<?php if( $i % $colInRow == 0 ) echo '<ul class="cart-item-row">'; ?>
	      <li>
            <?php $marketplace = Engine_Api::_()->getItem('marketplace', $cartitem['marketplace_id']); ?>
            <table class="product-title-right">
              <tbody><tr>
                <td><?=$marketplace->getTitle()?></td>
                <td width="20">
                  $<?=number_format($marketplace->price + Engine_Api::_()->marketplace()->getInspectionFee($marketplace->price), 2)?>
                  <div style="color:#93C;text-transform:none;">x<?=$cartitem['count']?></div>
                </td>
              </tr></tbody>
            </table>
        </li>
	      <?php if( $i++ % $colInRow == $colInRow - 1 ) echo "</ul>"; ?> 

        <?php $inspection_fee += Engine_Api::_()->marketplace()->getInspectionFee($marketplace->price) * $cartitem['count']; ?>
        <?php $total_amount += $marketplace->price * $cartitem['count']; ?>

    <?php endforeach; ?>

    <?php if( $i % $colInRow != 0 ) echo "</ul>"; ?>
    <?php $total_amount_full = $total_amount + $inspection_fee; ?>

    <hr/>
    <div class="cart-total-container"> 
      <span><?=$this->translate('SUBTOTAL')?></span>
      <span>$<?=number_format($total_amount, 2);?></span>
    </div>
    <div class="cart-total-container"> 
      <span><?=$this->translate('SHIPPING AND HANDLING')?></span>
      <span>$<?=number_format($inspection_fee, 2)?></span>
    </div>
    <hr/>
    <div class="cart-total-container"> 
      <span><?=$this->translate('TOTAL')?></span>
      <span>$<?=number_format($total_amount_full, 2);?></span>
    </div>
    <hr/>
    <div style="float: right;">
  	  <?=$this->paymentForm; ?>
    </div>
 	  <?=$this->htmlLink(array('route' => 'marketplace_general', 'action' => 'shippinginfo'), $this->translate('back'), array('class' => 'fmarketplace_button'))?>

  <?php else:?>
	  <h2><?=$this->translate('Cart is Empty');?></h2>
  <?php endif;?>
</div>
