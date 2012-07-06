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
<script type="text/javascript">
  function addToWishlist(el, id) {
      var url = "<?=$this->url(array('action' => 'add'), 'marketplace_wishes', true)?>" + "/item/" + id;
      var request = new Request.HTML({
        url : url,
        data : { format : 'html' },
        onSuccess : function(responseTree, responseElements, responseHTML, responseJavaScript) {
            el.innerHTML = '<?=$this->translate("remove from wishlist")?>';
            el.setAttribute( "onClick", "javascript: removeFromWishlist(this," + id + ");" );
        }
      });
      request.send();
  }
  function removeFromWishlist(el, id) {
      var url = "<?=$this->url(array('action' => 'remove'), 'marketplace_wishes', true)?>" + "/item/" + id;
      var request = new Request.HTML({
        url : url,
        data : { format : 'html' },
        onSuccess : function(responseTree, responseElements, responseHTML, responseJavaScript) {
            el.innerHTML = '<?=$this->translate("move to wishlist")?>';
            el.setAttribute( "onClick", "javascript: addToWishlist(this," + id + ");" );
        }
      });
      request.send();
  }
  function changeQuantity(el, id) {
      var url = "<?=$this->url(array('action' => 'ajax-count-update'), 'marketplace_general', true)?>";
      var request = new Request.JSON({
        url : url,
        data : { 'id' : id, 'count': el.get('value')},
        onSuccess : function(responseJSON, responseText) {
          el.set('value', responseJSON.count);
          $("cart-subtotal").innerHTML = responseJSON.subtotal;
          $("cart-sh").innerHTML = responseJSON.sh;
          $("cart-total").innerHTML = responseJSON.total;
        }
      });
      request.send();
  }
</script>

<?=$this->content()->renderWidget('marketplace.topbanner', array('pageName' => 'cart'))?>

<div class="cart-container">
<h2>
	<?php //echo $this->translate('%1$s\'s Cart', $this->htmlLink($this->viewer()->getHref(), $this->viewer()->getTitle()))?>
	<?=$this->translate('Product in Cart')?>
</h2>
<?php $inspectionEnable = Engine_Api::_()->getApi('settings', 'core')->getSetting('marketplace.inspectionenable', 0); ?>

<?php if(!empty($this->cartitems) && count($this->cartitems)): ?>
  <?php $i = 0; $colInRow = 5; $inspection_fee = 0; $total_amount = 0; ?> 
  <?php $this->addHelperPath(APPLICATION_PATH . '/application/modules/Fields/View/Helper', 'Fields_View_Helper'); ?>
  <?php $viewer = $this->viewer(); ?>
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
                <div style="color:#93C;text-transform:none;">
                  x
                  <select id="cart-count-<?=$cartitem['marketplace_id']?>" onChange="changeQuantity(this, <?=$cartitem['cart_id']?>);">
                    <?php $maxCount = ($cartitem['count'] > 20) ? $cartitem['count'] : 20; ?>
                    <?php for($i = 1; $i <= $maxCount; $i++) : ?>
                      <option value="<?=$i?>" <?php if( $i == $cartitem['count']) echo "selected"?>><?=$i?></option>
                    <?php endfor;?>
                  </select>
                  <?php/*<input type="text" id="cart-count-<?=$cartitem['marketplace_id']?>" 
                                      <?php /*onChange="changeQuantity(this, <?=$cartitem['marketplace_id']?>);" ?>
                                      onkeyup="changeQuantity(this, <?=$cartitem['marketplace_id']?>);"
                                      value="<?=$cartitem['count']?>" />*/?>
                </div>
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

              <?php if( $marketplace->inWishlist($viewer) ) : ?>
                <?=$this->htmlLink( 'javascript:void(0);', 
                                  $this->translate('remove from wishlist'),
                                  array('class' => 'cart-item-wishlist', "onclick" => "removeFromWishlist(this, {$cartitem['marketplace_id']});")
                                )?>
              <?php else : ?>
                <?=$this->htmlLink( 'javascript:void(0);', 
                                  $this->translate('move to wishlist'),
                                  array('class' => 'cart-item-wishlist', "onclick" => "addToWishlist(this, {$cartitem['marketplace_id']});")
                                )?>

              <?php endif;?>
          </div>
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
    <span id="cart-subtotal">$<?=number_format($total_amount, 2);?></span>
  </div>
  <div class="cart-total-container"> 
    <span><?=$this->translate('SHIPPING AND HANDLING')?></span>
    <span id="cart-sh">$<?=number_format($inspection_fee, 2)?></span>
  </div>
  <hr/>
  <div class="cart-total-container" > 
    <span><?=$this->translate('TOTAL')?></span>
    <span id="cart-total">$<?=number_format($total_amount_full, 2);?></span>
    <?=$this->htmlLink(array('route' => 'marketplace_general', 'action' => 'shippinginfo'), $this->translate('checkout'), array('class' => 'button'))?>
  </div>

<?php else:?>
	<h2><?=$this->translate('Cart is Empty');?></h2>
<?php endif;?>
</div>
