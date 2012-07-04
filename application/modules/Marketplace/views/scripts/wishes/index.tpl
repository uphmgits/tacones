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
</script>

<div class="cart-container">
<h2>
	<?=$this->translate('Product in Wishlist')?>
</h2>

<?php if( $this->paginator and $this->paginator->getTotalItemCount() > 0 ): ?>

  <?php $this->addHelperPath(APPLICATION_PATH . '/application/modules/Fields/View/Helper', 'Fields_View_Helper'); ?>
  <?php $i = 0; $colInRow = 5; $viewer = $this->viewer(); ?>
  <?php foreach($this->paginator as $item): ?>

    	<?php if( $i % $colInRow == 0 ) echo '<ul class="cart-item-row">'; ?>
	    <li>
          <?php $marketplace = Engine_Api::_()->getItem('marketplace', $item['marketplace_id']); ?>
          <div class="cart-item-photo">
            <?=$this->htmlLink($marketplace->getHref(), $this->itemPhoto($marketplace, 'normal'))?>
          </div>
          <table class="product-title-right">
            <tbody><tr>
              <td><?=$marketplace->getTitle()?></td>
              <td width="20">
                $<?=number_format($marketplace->price, 2)?>
              </td>
            </tr></tbody>
          </table>
          <div class="cart-item-fields">
              <?php $fieldStructure = Engine_Api::_()->fields()->getFieldsStructurePartial($marketplace); ?>
              <?=$this->fieldValueLoop($marketplace, $fieldStructure)?>
          </div>
          <div class="cart-item-options">
              <?php if( $marketplace->inWishlist($viewer) ) : ?>
                <?=$this->htmlLink( 'javascript:void(0);', 
                                  $this->translate('remove from wishlist'),
                                  array('class' => 'cart-item-wishlist', "onclick" => "removeFromWishlist(this, {$item['marketplace_id']});")
                                )?>
              <?php else : ?>
                <?=$this->htmlLink( 'javascript:void(0);', 
                                  $this->translate('move to wishlist'),
                                  array('class' => 'cart-item-wishlist', "onclick" => "addToWishlist(this, {$item['marketplace_id']});")
                                )?>

              <?php endif;?>
          </div>
      </li>
	    <?php if( $i++ % $colInRow == $colInRow - 1 ) echo "</ul>"; ?> 
  <?php endforeach; ?>

  <?php if( $i % $colInRow != 0 ) echo "</ul>"; ?>

<?php else:?>
	<h2><?=$this->translate('Wishlist is empty');?></h2>
<?php endif;?>
</div>
