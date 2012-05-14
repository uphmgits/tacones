<?php
/**
 * @category   Application_Extensions
 * @package    Marketplace
 * @copyright  Copyright 2011 SocialEngineMarket
 * @license    http://www.socialenginemarket.com
 */
?>

<h2><?php echo $this->translate("Marketplace Inspection") ?></h2>

<?php if( count($this->navigation) ): ?>
  <div class='tabs'>
    <?php
      // Render the menu
      echo $this->navigation()->menu()->setContainer($this->navigation)->render()
    ?>
  </div>
<?php endif; ?>

<p>
  <?php echo $this->translate("MARKETPLACES_INSPECTION_SETTINGS_DESCRIPTION") ?>
</p>

<?php if( count($this->subNavigation) ): ?>
  <div class='tabs'>
    <?php
      // Render the menu
      echo $this->navigation()->menu()->setContainer($this->subNavigation)->render()
    ?>
  </div>
<?php endif; ?>


<div class='clear'>
  <div class='settings'>
    <?php echo $this->form->render($this) ?>
  </div>
</div>
