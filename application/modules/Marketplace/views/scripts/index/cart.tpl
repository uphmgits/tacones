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
<h2>
	<?php //echo $this->translate('%1$s\'s Cart', $this->htmlLink($this->viewer()->getHref(), $this->viewer()->getTitle()))?>
	<?=$this->translate('Product in Cart')?>
</h2>
<?php $inspectionEnable = Engine_Api::_()->getApi('settings', 'core')->getSetting('marketplace.inspectionenable', 0); ?>

<?php if(!empty($this->cartitems) && count($this->cartitems)): ?>
  <?php $i = 0; $colInRow = 5; $shipping_fee = 0; $inspection_fee = 0; $total_amount = 0; ?> 
  <?php $this->addHelperPath(APPLICATION_PATH . '/application/modules/Fields/View/Helper', 'Fields_View_Helper'); ?>

  <?php foreach($this->cartitems as $cartitem): ?>

    	<?php if( $i % $colInRow == 0 ) echo '<ul class="cart-item-row">'; ?>
	    <li>
          <?php $marketplace = Engine_Api::_()->getItem('marketplace', $cartitem['marketplace_id']); ?>
          <div class="cart-item-photo">
            <?=$this->htmlLink($marketplace->getHref(), $this->itemPhoto($marketplace, 'normal'))?>
          </div>
          <table class="product-title-right">
            <tbody><tr>
              <td><?=$marketplace->getTitle()?></td>
              <td width="20">
                $<?=number_format($marketplace->price, 2)?>
                <div style="color:#93C;text-transform:none;">x<?=$cartitem['count']?></div>
              </td>
            </tr></tbody>
          </table>
          <div class="cart-item-fields">
              <?php $fieldStructure = Engine_Api::_()->fields()->getFieldsStructurePartial($marketplace); ?>
              <?=$this->fieldValueLoop($marketplace, $fieldStructure)?>
          </div>
          <div class="cart-item-options">
              <?=$this->htmlLink( array('route' => 'marketplace_general', 'action' => 'deletefromcart', 'marketplace_id' => $cartitem['marketplace_id']), 
                                  $this->translate('delete'), 
                                  array('class' => 'smoothbox')
                                )?>
              <?=$this->htmlLink( 'javascript:void(0);', 
                                  $this->translate('move to wishlist'),
                                  array('class' => 'cart-item-wishlist')
                                )?>
          </div>
      </li>
	    <?php if( $i++ % $colInRow == $colInRow - 1 ) echo "</ul>"; ?> 

      <?php $shipping_fee += $marketplace->shipping * $cartitem['count']; ?>
      <?php $inspection_fee += Engine_Api::_()->marketplace()->getInspectionFee($marketplace->price) * $cartitem['count']; ?>
      <?php $total_amount += $marketplace->price * $cartitem['count']; ?>
  <?php endforeach; ?>

  <?php if( $i % $colInRow != 0 ) echo "</ul>"; ?>
  <?php $total_amount_full = $total_amount + $shipping_fee + $inspection_fee; ?>

  <hr/>
  <div class="cart-total-container"> 
    <span><?=$this->translate('SUBTOTAL')?></span>
    <span>$<?=number_format($total_amount, 2);?></span>
  </div>
  <div class="cart-total-container"> 
    <span><?=$this->translate('SHIPPING')?></span>
    <span>$<?=number_format($shipping_fee, 2)?></span>
  </div>
  <div class="cart-total-container"> 
    <span><?=$this->translate('INSPECTION')?></span>
    <span>$<?=number_format($inspection_fee, 2)?></span>
  </div>
  <hr/>
  <div class="cart-total-container"> 
    <span><?=$this->translate('TOTAL')?></span>
    <span>$<?=number_format($total_amount_full, 2);?></span>
    <?=$this->htmlLink(array('route' => 'marketplace_general', 'action' => 'shippinginfo'), $this->translate('checkout'), array('class' => 'button'))?>
  </div>
  
  <?php /*
	<form method="post" id="cart_form">
		<table cellpadding="8" cellspacing="8" class="cart_table">
			<thead>
				<tr>
					<td><h3><?=$this->translate('Listing')?></h3></td>
					<td><h3><?=$this->translate('Price')?></h3></td>
					<td><h3><?=$this->translate('Quantity')?></h3></td>
          <?php if( $inspectionEnable ) : ?>
  					<td><h3><?=$this->translate('Inspection')?></h3></td>
          <?php endif; ?>
					<td><h3><?=$this->translate('Option')?></h3></td>
				</tr>
			</thead>
			<tbody>
				<?php
					$total_amount = 0;
					$shipping_fee = 0;
				?>
				<?php foreach($this->cartitems as $cartitem): ?>
				<?php 
					$marketplace = Engine_Api::_()->getItem('marketplace', $cartitem['marketplace_id']);
					if(empty($marketplace)){
						$cartTable = Engine_Api::_()->getDbtable('cart', 'marketplace');
						$cartTable->delete(array('marketplace_id = ?' => $cartitem['marketplace_id']));
						continue;
					}

					$total_amount += $marketplace->price * $cartitem['count'];
					//$product_shipping_fee = Engine_Api::_()->marketplace()->getShipingFee($cartitem['marketplace_id'], $this->viewer()->getIdentity());
					$shipping_fee += $marketplace->shipping * $cartitem['count'];
				?>
					<tr>
						<td><?=$this->htmlLink($marketplace->getHref(), $marketplace->getTitle())?></td>
						<td>$<?=$marketplace->price?></td>
						<td><input type="text" name="marketplaces_count[<?=$cartitem['marketplace_id']?>]" value="<?=$cartitem['count']?>" maxlength="3" style="width:30px;" onchange="return;this.form.submit();" /></td>
            <?php if( $inspectionEnable ) : ?>
              <td><input type="checkbox" name="marketplaces_inspection[<?=$cartitem['marketplace_id']?>]" <?php if( $cartitem['inspection']) echo 'checked'?> onchange="return;this.form.submit();" /></td>
            <?php endif; ?>
						<td><?=$this->htmlLink(array('route' => 'marketplace_general', 'action' => 'deletefromcart', 'marketplace_id' => $cartitem['marketplace_id']), 'Delete', array('class' => 'smoothbox'))?></td>
					</tr>
				<?php endforeach; ?>
				<?php 
					$total_amount_full = $total_amount + $shipping_fee;
				?>
			</tbody>
		</table>
		<div style="width: 434px;text-align: right;">
			<ul style="margin:15px 0;">
			<?php if($shipping_fee && 0): ?>
				<li>
					<span style="font-size:13px;">Shipping Fee:</span>
					<span style="font-size:13px;">$<?php echo number_format($shipping_fee, 2);?></span>
				</li>
			<?php endif; ?>
			<?php if(0): ?>
				<li>
					<span style="font-size:18px;">Total Amount:</span>
					<span style="font-size:18px;">$<?php echo number_format($total_amount_full, 2);?></span>
				</li>
			<?php endif; ?>
			</ul>
		</div>
		<?=$this->htmlLink('javascript:void(0);', 'Checkout', array('class' => 'marketplace_button', 'onclick' => '$("redirect").value="1";$("cart_form").submit();'))?>
		<?=$this->htmlLink('javascript:void(0);', 'Update', array('class' => 'marketplace_button', 'onclick' => '$("cart_form").submit();'))?>
		<?=$this->htmlLink('javascript:void(0);', 'Continue Shopping', array('class' => 'marketplace_button', 'onclick' => '$("redirect").value="2";$("cart_form").submit();'))?>
		<input type="hidden" id="redirect" name="redirect" value="0" />
	</form>
  */?>
<?php else:?>
	<h2><?=$this->translate('Cart is Empty');?></h2>
<?php endif;?>
</div>
