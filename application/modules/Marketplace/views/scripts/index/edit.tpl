<?php
/**
 * 
 *
 * @category   Application_Extensions
 * @package    Marketplace
 * @copyright  Copyright 2010 
 * * 
 * @version    $Id: edit.tpl 7250 2010-09-01 07:42:35Z john $
 * 
 */
?>

<?php
  $this->headScript()
    ->appendFile($this->baseUrl().'/externals/autocompleter/Observer.js')
    ->appendFile($this->baseUrl().'/externals/autocompleter/Autocompleter.js')
    ->appendFile($this->baseUrl().'/externals/autocompleter/Autocompleter.Local.js')
    ->appendFile($this->baseUrl().'/externals/autocompleter/Autocompleter.Request.js');
?>

<script type="text/javascript">
  en4.core.runonce.add(function()
  {
    new Autocompleter.Request.JSON('tags', '<?php echo $this->url(array('controller' => 'tag', 'action' => 'suggest'), 'default', true) ?>', {
      'postVar' : 'text',

      'minLength': 1,
      'selectMode': 'pick',
      'autocompleteType': 'tag',
      'className': 'tag-autosuggest',
      'filterSubset' : true,
      'multiple' : true,
      'injectChoice': function(token){
        var choice = new Element('li', {'class': 'autocompleter-choices', 'value':token.label, 'id':token.id});
        new Element('div', {'html': this.markQueryValue(token.label),'class': 'autocompleter-choice'}).inject(choice);
        choice.inputValue = token;
        this.addChoicemarketplaces(choice).inject(this.choices);
        choice.store('autocompleteChoice', token);
      }
    });
  });
</script>

<div class="headline">
  <h2>
    <?php echo $this->translate('Marketplace Listings');?>
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

<form action="<?php echo $this->escape($this->form->getAction()) ?>" method="<?php echo $this->escape($this->form->getMethod()) ?>" class="global_form marketplaces_browse_filters">
  <div>
    <div>
		  <h3>
			<?php echo $this->translate($this->form->getTitle()) ?>
		  </h3>
			<?php
				$formErrors = $this->form->getMessages();
			?>
			<?php if(!empty($formErrors)): ?>
				<?php foreach($formErrors as $form_el_name => $error): ?>
					<?php if(!empty($error)): ?>
						<?php if(is_array($error)): ?>
							<div class="tip">
								<span>
									<?php 
										$keys = array_keys($error);
										echo $this->form->$form_el_name->getLabel().' - '.$this->translate(array_shift($error));
									?>
								</span>
							</div>
						<?php else: ?>
							<div class="tip">
								<span>
									<?php echo $this->translate($error);?>
								</span>
							</div>
						<?php endif; ?>
					<?php endif; ?>
				<?php endforeach; ?>
			<?php endif; ?>
		  <div class="form-elements">
			<?php
				$formElements = $this->form->getElements();
				if(!empty($formElements)):
					foreach($formElements as $formElement):
						if($formElement->getName() != 'submit')
							echo $formElement->render();
					endforeach;
				endif;
			?>
		  </div>
		  <?php echo $this->form->marketplace_id; ?>
		  <ul class='marketplaces_editphotos'>        
			<?php foreach( $this->paginator as $photo ): ?>
			  <li>
				<div class="marketplaces_editphotos_photo">
				  <?php echo $this->itemPhoto($photo, 'thumb.normal')  ?>
				</div>
				<div class="marketplaces_editphotos_info">
				  <?php
					$key = $photo->getGuid();
					echo $this->form->getSubForm($key)->render($this);
				  ?>
				  <div class="marketplaces_editphotos_cover">
					<input type="radio" name="cover" value="<?php echo $photo->getIdentity() ?>" <?php if( $this->marketplace->photo_id == $photo->file_id ): ?> checked="checked"<?php endif; ?> />
				  </div>
				  <div class="marketplaces_editphotos_label">
					<label><?php echo $this->translate('Main Photo');?></label>
				  </div>
				</div>
			  </li>
			<?php endforeach; ?>
		  </ul>
		  <?php echo $this->form->submit->render(); ?>
    </div>
  </div>
</form>


<?php if( $this->paginator->count() > 0 ): ?>
  <br />
  <?php echo $this->paginationControl($this->paginator); ?>
<?php endif; ?>