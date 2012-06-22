<div class="headline">
  <h2>
    <?php echo $this->translate('Thank you for your payment');?>
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

<?php if($this->result == 'error') : ?>
		<?=$this->translate('Error due payment.')?>
<?php else : ?>
		<?=$this->translate('Success. Thank you for your order.')?>
    <?php if( $this->fbconnect ) : ?>
      <form action="" method="post">
        <button type="submit" name="fbpost"><?=$this->translate('Add this purchase on facebook.')?></button>
      </form>
    <?php else : ?>
  		<?=$this->translate('If you want to post your purchase on Facebook, you first need to establish a connection')?>
      <form action="" method="post">
        <button type="submit" name="fbconnect"><?=$this->translate('Connect to Facebook.')?></button>
      </form>
    <?php endif; ?>
<?php endif; ?>
<br/>    

<?php if( !empty($this->marketplaceList) ) : ?>
  <div>
      <h3><?=$this->translate('You can post a review for these users:')?></h3><br/>
      <?php foreach( $this->marketplaceList as $item) : ?>
          <?php $owner = $item->getOwner(); ?>
          <?=$this->htmlLink(array('route' => 'review_user', 'id' => $owner->getIdentity()), $owner->getTitle())?><br/>
      <?php endforeach; ?>
  </div>
  <br/>    

  <?php $i = 0; $colInRow = 5; ?> 
  <?php $this->addHelperPath(APPLICATION_PATH . '/application/modules/Fields/View/Helper', 'Fields_View_Helper'); ?>

  <div>
    <h3><?=$this->translate('You just bought these items:')?></h3><br/>
    <?php foreach($this->marketplaceList as $marketplace): ?>

      	<?php if( $i % $colInRow == 0 ) echo '<ul class="cart-item-row">'; ?>
	      <li>
            <div class="cart-item-photo">
              <?=$this->htmlLink($marketplace->getHref(), $this->itemPhoto($marketplace, 'normal'))?>
            </div>
            <table class="product-title-right">
              <tbody><tr>
                <td><?=$marketplace->getTitle()?></td>
                <td width="20">
                  $<?=number_format($marketplace->price, 2)?>
                  <div style="color:#93C;text-transform:none;"></div>
                </td>
              </tr></tbody>
            </table>
            <div class="cart-item-fields">
                <?php $fieldStructure = Engine_Api::_()->fields()->getFieldsStructurePartial($marketplace); ?>
                <?=$this->fieldValueLoop($marketplace, $fieldStructure)?>
            </div>
        </li>
	      <?php if( $i++ % $colInRow == $colInRow - 1 ) echo "</ul>"; ?> 
    <?php endforeach; ?>
  </div>
  <br/>
  <br/>

<?php endif; ?>
	
<?php	if(!empty($this->cartContent)) : ?>
	<p>
		<strong>
			<?=$this->translate('You have some products in ').$this->htmlLink(array('route' => 'marketplace_general', 'action' => 'cart'), 'cart').'. '.$this->translate('Please go to ').$this->htmlLink(array('route' => 'marketplace_general', 'action' => 'checkout'), 'checkout').'.'?>
		</strong>
	</p>
<?php endif; ?>
