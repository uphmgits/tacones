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
      <h3><?=$this->translate('You can post a review for these users:')?></h3>
      <?php foreach( $this->marketplaceList as $item) : ?>
          <?php $owner = $item->getOwner(); ?>
          <?=$this->htmlLink(array('route' => 'review_user', 'id' => $owner->getIdentity()), $owner->getTitle())?><br/>
      <?php endforeach; ?>
  </div>
  <br/>    
<?php endif; ?>
	
<?php	if(!empty($this->cartContent)) : ?>
	<p>
		<strong>
			<?=$this->translate('You have some products in ').$this->htmlLink(array('route' => 'marketplace_general', 'action' => 'cart'), 'cart').'. '.$this->translate('Please go to ').$this->htmlLink(array('route' => 'marketplace_general', 'action' => 'checkout'), 'checkout').'.'?>
		</strong>
	</p>
<?php endif; ?>
