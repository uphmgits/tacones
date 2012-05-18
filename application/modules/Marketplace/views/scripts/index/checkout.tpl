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

<div class="cart-container">

  <h2><?=$this->translate('RECEIPT')?></h2>

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
    <?php $i = 0; $colInRow = 5; $shipping_fee = 0; $total_amount = 0; ?> 
    <?php $this->addHelperPath(APPLICATION_PATH . '/application/modules/Fields/View/Helper', 'Fields_View_Helper'); ?>

    <?php foreach($this->cartitems as $cartitem): ?>

      	<?php if( $i % $colInRow == 0 ) echo '<ul class="cart-item-row">'; ?>
	      <li>
            <?php $marketplace = Engine_Api::_()->getItem('marketplace', $cartitem['marketplace_id']); ?>
            <table class="product-title-right">
              <tbody><tr>
                <td><?=$marketplace->getTitle()?></td>
                <td width="20">$<?=number_format($marketplace->price, 2)?></td>
              </tr></tbody>
            </table>
        </li>
	      <?php if( $i++ % $colInRow == $colInRow - 1 ) echo "</ul>"; ?> 

        <?php $shipping_fee += $marketplace->shipping * $cartitem['count']; ?>
        <?php $total_amount += $marketplace->price * $cartitem['count']; ?>

    <?php endforeach; ?>

    <?php if( $i % $colInRow != 0 ) echo "</ul>"; ?>
    <?php $total_amount_full = $total_amount + $shipping_fee; ?>

    <hr/>
    <div class="cart-total-container"> 
      <span><?=$this->translate('SUBTOTAL')?></span>
      <span>$<?=number_format($total_amount, 2);?></span>
    </div>
    <div class="cart-total-container"> 
      <span><?=$this->translate('SHIPPING')?></span>
      <span>$<?=number_format($shipping_fee)?></span>
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

<?php /*
  <?php $inspectionEnable = Engine_Api::_()->getApi('settings', 'core')->getSetting('marketplace.inspectionenable', 0); ?>

  <?php if($this->coupon_error): ?>
	  <div class="tip">
	    <span>
			  <?php if($this->coupon_error == 1): ?>
					  <?php echo $this->translate('Wrong coupon code');?>
			  <?php elseif($this->coupon_error == 2): ?>
					  <?php echo $this->translate('Coupon have been succesfully activated');?>
			  <?php elseif($this->coupon_error == 3): ?>
					  <?php echo $this->translate('Coupon for this seller have been already activated');?>
			  <?php else: ?>
					  <?php echo $this->translate('Coupon Error');?>
			  <?php endif; ?>
	    </span>
	  </div>
  <?php endif; ?>

  <?php if(!empty($this->cartitems) && count($this->cartitems)): ?>
	  <?php
		  $first_marketplace = Engine_Api::_()->getItem('marketplace', $this->cartitems[0]['marketplace_id']);
		  $first_stage_owner = $first_marketplace->getOwner()->getIdentity();
	  ?>
	  <h2>
		  <?php echo $this->translate('Checkout')?> (<?=$this->htmlLink($first_marketplace->getOwner()->getHref(), $first_marketplace->getOwner()->getTitle())?> <?=$this->translate('listings')?>)
	  </h2>

		  <table cellpadding="8" cellspacing="8" class="cart_table">
			  <thead>
				  <tr>
					  <td><h3><?=$this->translate('Listing')?></h3></td>
					  <td><h3><?=$this->translate('Seller')?></h3></td>
					  <td><h3><?=$this->translate('Price')?></h3></td>
					  <td><h3><?=$this->translate('Quantity')?></h3></td>
            <?php if( $inspectionEnable ) : ?>
					      <td><h3><?=$this->translate('Inspection')?></h3></td>
            <?php endif; ?>
				  </tr>
			  </thead>
			  <tbody>
				  <?php
					  $total_amount = 0;
					  $shipping_fee = 0;
					  $discount_amount = 0;
					  $total_inspection = 0;
				  ?>
				  <?php foreach($this->cartitems as $cartitem): ?>
				  <?php 
					  $marketplace = Engine_Api::_()->getItem('marketplace', $cartitem['marketplace_id']);
					  //if($first_stage_owner != $marketplace->getOwner()->getIdentity())
					  //	continue;
					
            $inspection = ( $cartitem['inspection'] and $inspectionEnable ) ? (Engine_Api::_()->marketplace()->getInspectionFee($marketplace->price) * $cartitem['count']) : 0;
            $total_inspection += $inspection;
					  $total_amount += $marketplace->price * $cartitem['count'];
					  //$product_shipping_fee = Engine_Api::_()->marketplace()->getShipingFee($cartitem['marketplace_id'], $this->viewer()->getIdentity());
					  $shipping_fee += $marketplace->shipping * $cartitem['count'];
            
					
					  $coupon_discount = Engine_Api::_()->marketplace()->getDiscount($cartitem['marketplace_id'], $this->viewer()->getIdentity());
					  $discount_amount += $coupon_discount * $cartitem['count'];
				  ?>
					  <tr>
						  <td><?=$this->htmlLink($marketplace->getHref(), $marketplace->getTitle())?></td>
						  <td><?=$this->htmlLink($marketplace->getOwner()->getHref(), $marketplace->getOwner()->getTitle())?></td>
						  <td>$<?=$marketplace->price?></td>
						  <td style="text-align: center;" ><?=$cartitem['count']?></td>
              <?php if( $inspectionEnable ) : ?>  
                  <td style="text-align: center;" ><?=$inspection?></td>
              <?php endif; ?>
					  </tr>
				  <?php endforeach; ?>
				  <?php 
					  $total_amount_full = $total_amount + $shipping_fee + $total_inspection - $discount_amount;
				  ?>
			  </tbody>
		  </table>
		  <?php if(Engine_Api::_()->marketplace()->couponIsActive()): ?>
			  <?php if(!$this->coupon_res): ?>
			  <?php else: ?>
			  <?php endif; ?>
				  <div style="margin: 20px 0;">
					  <form method="post">
						  <?=$this->translate('Enter a coupon code: ')?> <input name="coupon_code" id="coupon_code" value="" />
						  <button id="submit" type="submit" name="submit"><?=$this->translate('Apply')?></button>
					  </form>
				  </div>
			  <?php if(!empty($discount_amount)): ?>
				  <?=$this->translate('Your discount: ').'$'.$discount_amount;?>
			  <?php endif; ?>
		  <?php endif; ?>
		  <div style="width: 314px;text-align: left;">
			  <ul style="margin:15px 0;">
			  <?php if($shipping_fee): ?>
				  <li>
					  <span style="font-size:13px;">Shipping Fee:</span>
					  <span style="font-size:13px;">$<?php echo number_format($shipping_fee, 2);?></span>
				  </li>
			  <?php endif; ?>
				  <li>
					  <span style="font-size:18px;">Total Amount:</span>
					  <span style="font-size:18px;">$<?php echo number_format($total_amount_full, 2);?></span>
				  </li>
			  </ul>
		  </div>
		  <div style="float: left;">
			  <?=$this->paymentForm; ?>
		  </div>
		  <div style="padding-top: 6px;">
			  <?=$this->htmlLink(array('route' => 'marketplace_general', 'action' => 'cart'), 'Back to Cart', array('class' => 'fmarketplace_button', 'style' => 'margin: 5px 0 0 10px;'))?>
		  </div>
  <?php else:?>
	  <h2><?=$this->translate('Cart is Empty');?></h2>
  <?php endif;?>
*/?>
