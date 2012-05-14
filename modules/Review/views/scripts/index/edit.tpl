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

<?php echo $this->form->render($this);?>
