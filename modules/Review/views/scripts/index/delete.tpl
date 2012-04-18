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

<div class='global_form'>
  <form method="post" class="global_form">
    <div>
      <div>
      <h3><?php echo $this->translate('Delete Review?');?></h3>
      <p>
        <?php echo $this->translate('Are you sure that you want to delete the review with the title "<a href="%3$s">%1$s</a>" last modified %2$s? It will not be recoverable after being deleted.', $this->review->title,$this->timestamp($this->review->modified_date),$this->review->getHref()); ?>
      </p>
      <br />
      <p>
        <input type="hidden" name="confirm" value="true"/>
        <button type='submit'><?php echo $this->translate('Delete');?></button>
        <?php echo $this->translate('or');?> <a href='<?php echo $this->url(array(), 'review_manage', true) ?>'><?php echo $this->translate('cancel');?></a>
      </p>
    </div>
    </div>
  </form>
</div>