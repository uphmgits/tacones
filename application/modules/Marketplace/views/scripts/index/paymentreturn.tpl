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

<?php 
	if($this->result == 'error'){
		echo $this->translate('Error due payment.'); 
	}else{
		echo $this->translate('Success. Thank you for your order.'); 
	}
	
	if(!empty($this->cartContent)){
?>
	<p>
		<strong>
			<?=$this->translate('You have some products in ').$this->htmlLink(array('route' => 'marketplace_general', 'action' => 'cart'), 'cart').'. '.$this->translate('Please go to ').$this->htmlLink(array('route' => 'marketplace_general', 'action' => 'checkout'), 'checkout').'.'?>
		</strong>
	</p>
<?php
	}
?>