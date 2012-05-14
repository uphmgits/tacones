<?php


/**
 * Radcodes - SocialEngine Module
 *
 * @category   Application_Extensions
 * @package    Review
 * @copyright  Copyright (c) 2009-2010 Radcodes LLC (http://www.radcodes.com)
 * @license    http://www.radcodes.com/license/
 * @version    $Id$
 * @author     Vincent Van <vincent@radcodes.com>
 */
 
 
?>

<?php echo $this->partial('index/_js_fields.tpl', 'review', array())?>


<?php

  $this->headScript()
    ->appendFile($this->baseUrl().'/application/modules/Review/externals/scripts/rating.js')
?>

<script type="text/javascript">
  var maxRecipients = 1;
  
  function removeFromToValue(id)
  {
    // code to change the values in the hidden field to have updated values
    // when recipients are removed.
    var toValues = $('toValues').value;
    var toValueArray = toValues.split(",");
    var toValueIndex = "";

    var checkMulti = id.search(/,/);

    // check if we are removing multiple recipients
    if (checkMulti!=-1){
      var recipientsArray = id.split(",");
      for (var i = 0; i < recipientsArray.length; i++){
        removeToValue(recipientsArray[i], toValueArray);
      }
    }
    else{
      removeToValue(id, toValueArray);
    }

    // hide the wrapper for usernames if it is empty
    if ($('toValues').value==""){
      $('toValues-wrapper').setStyle('height', '0');
    }

    $('to').disabled = false;
    $('to').setAttribute("class", "enabled");
  }

  function removeToValue(id, toValueArray){
    for (var i = 0; i < toValueArray.length; i++){
      if (toValueArray[i]==id) toValueIndex =i;
    }

    toValueArray.splice(toValueIndex, 1);
    $('toValues').value = toValueArray.join();
  }

  en4.core.runonce.add(function(){
    new OverText($('to'), {
      'textOverride' : '<?php echo $this->translate('Start typing...') ?>',
      'element' : 'label',
      'positionOptions' : {
        position: ( en4.orientation == 'rtl' ? 'upperRight' : 'upperLeft' ),
        edge: ( en4.orientation == 'rtl' ? 'upperRight' : 'upperLeft' ),
        offset: {
          x: ( en4.orientation == 'rtl' ? -4 : 4 ),
          y: 2
        }
      }
    });
  });
  en4.core.runonce.add(function() {
      //var tokens = <?php echo $this->friends ?>;
      new Autocompleter.Request.JSON('to', '<?php echo $this->url(array('module' => 'user', 'controller' => 'friends', 'action' => 'suggest'), 'default', true) ?>', {
        'minLength': 1,
        'delay' : 250,
        'selectMode': 'pick',
        'autocompleteType': 'message',
        'multiple': false,
        'className': 'message-autosuggest',
        'filterSubset' : true,
        'tokenFormat' : 'object',
        'tokenValueKey' : 'label',
        'injectChoice': function(token){
          if(token.type == 'user'){
            var choice = new Element('li', {'class': 'autocompleter-choices', 'html': token.photo, 'id':token.label});
            new Element('div', {'html': this.markQueryValue(token.label),'class': 'autocompleter-choice'}).inject(choice);
            this.addChoiceEvents(choice).inject(this.choices);
            choice.store('autocompleteChoice', token);
          }
          else {
            var choice = new Element('li', {'class': 'autocompleter-choices friendlist', 'id':token.label});
            new Element('div', {'html': this.markQueryValue(token.label),'class': 'autocompleter-choice'}).inject(choice);
            this.addChoiceEvents(choice).inject(this.choices);
            choice.store('autocompleteChoice', token);
          }
            
        },
        onPush : function(){
          if( $('toValues').value.split(',').length >= maxRecipients ){
            $('to').disabled = true;
            $('to').setAttribute("class", "disabled");
          }
        }
      });

      <?php if( isset($this->toUser) && $this->toUser->getIdentity() ): ?>

      var toID = <?php echo $this->toUser->getIdentity() ?>;
      var name = '<?php echo $this->toUser->getTitle() ?>';
      var myElement = new Element("span");
      myElement.id = "tospan" + toID;
      myElement.setAttribute("class", "tag");
      myElement.innerHTML = name + " <a href='javascript:void(0);' onclick='this.parentNode.destroy();removeFromToValue(\""+toID+"\");'>x</a>";
      //myElement.innerHTML = name;
      $('toValues-element').appendChild(myElement);
      $('toValues-wrapper').setStyle('height', 'auto');
      if( $('toValues').value.split(',').length >= maxRecipients ){
          $('to').disabled = true;
          $('to').value = '';
          $('to').setAttribute("class", "disabled");
      }
      $('toValues').value = toID;
      <?php endif; ?>

      <?php if( isset($this->multi)): ?>

      var multi_type = '<?php echo $this->multi; ?>';
      var toIDs = '<?php echo $this->multi_ids; ?>';
      var name = '<?php echo $this->multi_name; ?>';
      var myElement = new Element("span");
      myElement.id = "tospan_"+name+"_"+toIDs;
      myElement.setAttribute("class", "tag tag_"+multi_type);
      myElement.innerHTML = name + " <a href='javascript:void(0);' onclick='this.parentNode.destroy();removeFromToValue(\""+toIDs+"\");'>x</a>";
      $('toValues-element').appendChild(myElement);
      $('toValues-wrapper').setStyle('height', 'auto');

      <?php endif; ?>

    });


</script>


<script type="text/javascript">
  en4.core.runonce.add(function()
  {
    // convert the selectbox with id 'rating'
    var rating = new radcodesReviewMooRatings(document.id('rating'), {
      showSelectBox : false,
      container : null,
      defaultRating : <?php echo $this->form->rating->getValue();?>
    });

    
  });
</script>

<div class="headline">
  <h2>
    <?php echo $this->translate('Reviews');?>
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

<?php if ($this->current_count >= $this->quota && $this->quota > 0):?>
  <div class="tip">
    <span>
      <?php echo $this->translate('You have already created the maximum number of reviews allowed.');?>
      <?php echo $this->translate('If you would like to create a new review, please <a href="%1$s">delete</a> an old one first.', $this->url(array(), 'review_manage'));?>
    </span>
  </div>
  <br/>
<?php else:?>
  <?php echo $this->form->render($this);?>
<?php endif; ?>
