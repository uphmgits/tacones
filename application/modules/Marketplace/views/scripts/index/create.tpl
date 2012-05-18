<?php
/**
 * 
 *
 * @category   Application_Extensions
 * @package    Marketplace
 * @copyright  Copyright 2010 
 * * 
 * @version    $Id: create.tpl 7244 2010-09-01 01:49:53Z john $
 * 
 */
?>

<div class='layout_common'>
	<div class='layout_left' style="padding: 0;">
	  <div class='marketplaces_gutter'>
		
      <div class="quicklinks">
        <div id="navigation">
          <?php Engine_Api::_()->marketplace()->tree_print_category( $this->a_tree, $this->urls, $this->category_id, 'marketplace_create' ); ?>
        </div>
      </div>

    </div>
  </div>

	<div class='layout_middle'>

    <div class="marketplace-create-container">
        <?php /*
        <style>
          .marketplace-create-container {
            overflow: hidden;
            margin: 0;
          }
          .marketplace-create-photos {
            float: left;
            width: 300px;
          }
          .marketplace-create-mainphoto {
            overflow: hidden;
          }

          .marketplace-create-miniphoto_item_container {
            float: left;
            margin: 0 20px 20px 0px;
            text-align: right;
          }
          
          .marketplace-create-mainphoto,
          .marketplace-create-miniphoto_item {
            border: 1px solid #333;
            padding: 20px;
            width: 222px;
          }
          .marketplace-create-mainphoto {
            margin-bottom: 20px;
          }
          .marketplace-create-miniphoto_item {
            width: 80px;
            height: 80px;
          }
          .marketplace-create-miniphoto_item input {
            width: 100%;
          }
          .marketplace-create-details {
            overflow: hidden;
          }
          .marketplace-create-details > div {
            margin-bottom: 10px;
          }
          .marketplace-create-fields {
            overflow: hidden; 
          }
          .marketplace-create-fields > div {
            float: left;
            width: 130px;
          }
          .marketplace-create-details select,
          .marketplace-create-details input,
          .marketplace-create-details textarea {
            width: 380px;
          }
          .marketplace-create-fields > div select,
          .marketplace-create-fields > div input[type=text],
          .marketplace-create-fields > div textarea {
            width: 110px;
          }
          .marketplace-create-fields > div input[type=radio],
          .marketplace-create-fields > div input[type=checkbox] {
            width: 14px;
          }
          .marketplace-create-container select,
          .marketplace-create-container input,
          .marketplace-create-container textarea {
            background: transparent;
            border: 1px solid #333;
          }

          .marketplace-create-buttons {
            float: right;
          }
          .marketplace-create-buttons > a,
          .marketplace-create-buttons > button,
          .marketplace-create-miniphoto_item_container > a {
            background: none;
            border: none;
            color: #4499bb;
            text-decoration: none;
          }
          .marketplace-create-photofake {
            overflow: hidden;
            width: 80px;
            height: 24px;
            background: #333;
          }
          .marketplace-create-photofake > div {
            padding: 3px;
            margin-bottom: -30px;
            color: #d2d2d2; 
            font-size: 12px;
            text-align: center;
            cursor: pointer;
          }
          .marketplace-create-photofake > input {
            height: 60px;
            width: 200%;
            margin: -50px auto auto -50px;
            -moz-opacity: 0;
            filter: alpha(opacity=0);
            opacity: 0;
            cursor: pointer;
          }
          .marketplace-create-photos .marketplace-create-buttons {
            margin-right: 36px;
          }

        </style> */?> 
        
        <script type="text/javascript">
          function deletePhoto(name) {
            $(name).set('value', '');
            $(name + "-desc").set('html', "<?=$this->translate('Choose photo')?>");
          }
        </script>
    
        <form action="" method="post" enctype="multipart/form-data">
            <div class="marketplace-create-photos">
                <div class="marketplace-create-mainphoto-container">
                  <div class="marketplace-create-mainphoto">
                    <?=$this->translate('Choose photos on your computer to add to this marketplace listing. (2MB maximum)')?>
                    <div class="marketplace-create-photofake">
                      <div id="photo-desc"><?=$this->translate('Choose photo')?></div>
                      <input type="file" name="photo" id="photo" size="1" onchange="document.getElementById('photo-desc').innerHTML = document.getElementById('photo').value;" />
                    </div>
                    
                  </div>
                  <a href="javascript:void(0)" onclick="deletePhoto('photo', '<?=$this->translate('main photo')?>');">
                      <?=$this->translate('clear')?>
                  </a>
                </div>
                
                <div class="marketplace-create-miniphoto">
                
                  <div class="marketplace-create-miniphoto_item_container">
                    <div class="marketplace-create-miniphoto_item">
                      <div><?=$this->translate('photo 2')?></div>
                      <div class="marketplace-create-photofake">
                        <div id="photo1-desc"><?=$this->translate('Choose photo')?></div>
                        <input type="file" name="photo1" id="photo1" size="1" onchange="document.getElementById('photo1-desc').innerHTML = document.getElementById('photo1').value;" />
                      </div>
                    </div>
                    <a href="javascript:void(0)" onclick="deletePhoto('photo1');">
                        <?=$this->translate('clear')?>
                    </a>
                  </div>

                  <div class="marketplace-create-miniphoto_item_container">
                    <div class="marketplace-create-miniphoto_item">
                      <div><?=$this->translate('photo 3')?></div>
                      <div class="marketplace-create-photofake">
                        <div id="photo2-desc"><?=$this->translate('Choose photo')?></div>
                        <input type="file" name="photo2" id="photo2" size="1" onchange="document.getElementById('photo2-desc').innerHTML = document.getElementById('photo2').value;" />
                      </div>
                    </div>
                    <a href="javascript:void(0)" onclick="deletePhoto('photo2');">
                        <?=$this->translate('clear')?>
                    </a>
                  </div>

                  <div class="marketplace-create-miniphoto_item_container">
                    <div class="marketplace-create-miniphoto_item">
                      <div><?=$this->translate('photo 4')?></div>
                      <div class="marketplace-create-photofake">
                        <div id="photo3-desc"><?=$this->translate('Choose photo')?></div>
                        <input type="file" name="photo3" id="photo3" size="1" onchange="document.getElementById('photo3-desc').innerHTML = document.getElementById('photo3').value;" />
                      </div>
                    </div>
                    <a href="javascript:void(0)" onclick="deletePhoto('photo3');">
                        <?=$this->translate('clear')?>
                    </a>
                  </div>
                  
                  <div class="marketplace-create-miniphoto_item_container">
                    <div class="marketplace-create-miniphoto_item">
                      <div><?=$this->translate('photo 5')?></div>
                      <div class="marketplace-create-photofake">
                        <div id="photo4-desc"><?=$this->translate('Choose photo')?></div>
                        <input type="file" name="photo4" id="photo4" size="1" onchange="document.getElementById('photo4-desc').innerHTML = document.getElementById('photo4').value;" />
                      </div>
                    </div>
                    <a href="javascript:void(0)" onclick="deletePhoto('photo4');">
                        <?=$this->translate('clear')?>
                    </a>
                  </div>
                  
                </div>
                <?php /*
                <div class="marketplace-create-buttons">
                      <a href="javascript:void(0)" onclick="$$('.marketplace-create-miniphoto input').set('value', ''); $$('.marketplace-create-photofake div').set('html', '<?=$this->translate('Choose photo')?>');"><?=$this->translate('clear')?></a>
                </div> */?>

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
                  <?=$this->form->shipping->render($this)?>
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
      'customChoices' : true,
      'filterSubset' : true,
      'multiple' : true,
      'injectChoice': function(token){
        var choice = new Element('li', {'class': 'autocompleter-choices', 'value':token.label, 'id':token.id});
        new Element('div', {'html': this.markQueryValue(token.label),'class': 'autocompleter-choice'}).inject(choice);
        choice.inputValue = token;
        this.addChoiceEvents(choice).inject(this.choices);
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
 
<?php if (($this->current_count >= $this->quota) && !empty($this->quota)):?>
  <div class="tip">
    <span>
      <?php echo $this->translate('You have already created the maximum number of marketplace listings allowed.');?>
      <?php echo $this->translate('If you would like to create a new listing, please <a href="%1$s">delete</a> an old one first.', $this->url(array('action' => 'manage'), 'marketplace_extended'));?>
    </span>
  </div>
  <br/>
<?php else:?>
  <?php echo $this->form->render($this);?>
<?php endif; ?>
*/?>

