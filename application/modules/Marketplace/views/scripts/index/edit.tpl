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

<div class='layout_common'>
	<div class='layout_left' style="padding: 0;">
	  <div class='marketplaces_gutter'>
		
      <div class="quicklinks">
        <div id="navigation">
          <?php Engine_Api::_()->marketplace()->tree_print_category( $this->a_tree, 
                                                                     $this->urls, 
                                                                     $this->category_id, 
                                                                     "marketplace_edit",
                                                                     array('marketplace_id' => $this->marketplace->getIdentity())
                                                                   ); ?>
        </div>
      </div>

    </div>
  </div>

	<div class='layout_middle'>

    <script type="text/javascript">
      function deletePhoto(name, phrase) {
        $(name).set('value', '');
        if( $(name + "-preview") ) {
          $(name + "-preview").set('class', '');
          $(name + "-preview").set('html', phrase);
        }
        $(name + "-desc").set('html', "<?=$this->translate('Choose photo')?>");
        if( $("delete-" + name) ) {
          $("delete-" + name).set('value', 1);
        }
      }
    </script>
    
    <div class="marketplace-create-container">
        <form action="" method="post" enctype="multipart/form-data">
            <div class="marketplace-create-photos">
                <div class="marketplace-create-mainphoto-container">
                    <div class="marketplace-create-mainphoto">
                      <?php if( $this->marketplace->photo_id ) : ?>
                        <div class="marketplace-create-mainphoto-holder" id="photo-preview">
                          <?=$this->itemPhoto($this->marketplace, 'normal')?>
                        </div>
                      <?php endif; ?>
                      <div class="marketplace-create-photofake">
                        <div id="photo-desc"><?=$this->translate('Choose photo')?></div>
                        <input type="file" name="photo" id="photo" size="1" onchange="document.getElementById('photo-desc').innerHTML = document.getElementById('photo').value;" />
                        <input type="hidden" id="delete-photo" name="delete_photo" value="0" />
                      </div>
                    </div>
                    
                    <a href="javascript:void(0)" onclick="deletePhoto('photo', '<?=$this->translate('main photo')?>');">
                        <?=$this->translate('clear')?>
                    </a>
                </div>

                <div class="marketplace-create-miniphoto">
                  <?php $i = 0; ?>
                  <?php foreach( $this->paginator as $photo ): ?>
                  
                    <?php if( $photo->getIdentity() == $this->marketplace->photo_id ) continue; ?>
                    <?php $i++ ?>
                    <?php $name = "photo" . $i; ?>
                    <div class="marketplace-create-miniphoto_item_container">
                      <div class="marketplace-create-miniphoto_item">
                        <div class="marketplace-create-miniphoto-holder" id="<?=$name?>-preview">
                            <?=$this->itemPhoto($photo, 'thumb.normal')?>
                        </div>
                        <div class="marketplace-create-photofake">
                          <div id="<?=$name?>-desc"><?=$this->translate('Choose photo')?></div>
                          <input type="file" name="photo_<?=$photo->getIdentity()?>" id="<?=$name?>" size="1" onchange="document.getElementById('<?=$name?>-desc').innerHTML = document.getElementById('<?=$name?>').value;" />
                          <input type="hidden" id="delete-<?=$name?>" name="delete_photo_<?=$photo->getIdentity()?>" value="0" />
                        </div>
                      </div>
                      <a href="javascript:void(0)" onclick="deletePhoto('<?=$name?>', '<?=$this->translate('photo %s', $i)?>');">
                        <?=$this->translate('clear')?>
                      </a>
                    </div>
                    <?php if( $i == 4 ) break; ?>
                  <?php endforeach; ?>

                  <?php if( $i < 4 ) : ?>
                    <?php for( $j = $i + 1; $j <= 4; $j++ ): ?>
                      <?php $name = "photo" . $j; ?>
                      <div class="marketplace-create-miniphoto_item_container">
                        <div class="marketplace-create-miniphoto_item">
                          <div><?=$this->translate('photo %s', $j)?></div>
                          <div class="marketplace-create-photofake">
                            <div id="<?=$name?>-desc"><?=$this->translate('Choose photo')?></div>
                            <input type="file" name="<?=$name?>" id="<?=$name?>" size="1" onchange="document.getElementById('<?=$name?>-desc').innerHTML = document.getElementById('<?=$name?>').value;" />
                          </div>
                        </div>
                        <a href="javascript:void(0)" onclick="$('<?=$name?>').set('value', ''); $('<?=$name?>-desc').set('html', '<?=$this->translate('Choose photo')?>');">
                          <?=$this->translate('clear')?>
                        </a>
                    </div>
                    <?php endfor; ?>
                  <?php endif; ?>
                </div>

                </div>
            <div class="marketplace-create-info">
              <div class="marketplace-create-details">
                  <div><?=$this->translate('details')?></div>
                  <div><?=$this->translate('details description')?></div>
                  <div class="marketplace-create-fields">
                      <?=$this->form->fields->render($this)?>
                  </div>
                  <?=$this->form->price->render($this)?>
                  <?=$this->form->title->setLabel('headline')->render($this)?>
                  <?=$this->form->body->render($this)?>
                  <?=$this->form->business_email->render($this)?>
                  <div class="marketplace-create-buttons">
                      <a href="javascript:void(0)" onclick="$$('.marketplace-create-details input').set('value', ''); $$('.marketplace-create-details select').set('value', ''); $$('.marketplace-create-details textarea').set('value', '');"><?=$this->translate('clear')?></a>
                      <button name="submit" id="submit" type="submit"><?=$this->translate('save')?></button>
                  </div>
            </div>
            <input type="hidden" name="category_id" id="category_id" value="<?=$this->category_id?>" />
        </form>
    </div>
  </div>
  <?php foreach( $this->form->getElements() as $element ): ?>
      <?php $error = $element->getMessages();?>
      <?php if( !empty($error) ) : ?>
        <?php foreach( $error as $key => $value ): ?>
          <div class="error_message">
              <span><?=$this->translate('Error:')?> <?=$element->getLabel()?> - <?=$value?></span>
          </div>
        <?php endforeach; ?>
      <?php endif; ?>
  <?php endforeach; ?>
  <?php foreach( $this->form->fields->getElements() as $element ): ?>
      <?php $error = $element->getMessages();?>
      <?php if( !empty($error) ) : ?>
        <?php foreach( $error as $key => $value ): ?>
          <div class="error_message">
              <span><?=$this->translate('Error:')?> <?=$element->getLabel()?> - <?=$value?></span>
          </div>
        <?php endforeach; ?>
      <?php endif; ?>
  <?php endforeach; ?>
</div>

<?php /*
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
										//echo $this->form->$form_el_name->getLabel().' - '.$this->translate(array_shift($error));
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
        echo $this->form->fields->render();
        //foreach($this->form->fields->getElements() as $formElement) $formElement->render();
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
<?php endif; */?>
