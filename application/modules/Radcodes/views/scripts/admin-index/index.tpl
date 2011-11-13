<?php
/**
 * Radcodes - SocialEngine Module
 *
 * @category   Application_Extensions
 * @package    Radcodes
 * @copyright  Copyright (c) 2009-2010 Radcodes LLC (http://www.radcodes.com)
 * @license    http://www.radcodes.com/license/
 * @version    $Id$
 * @author     Vincent Van <vincent@radcodes.com>
 */
?>

<h2><?php echo $this->translate("Radcodes Core Library") ?></h2>

<?php if( count($this->navigation) ): ?>
  <div class='tabs'>
    <?php
      // Render the menu
      //->setUlClass()
      echo $this->navigation()->menu()->setContainer($this->navigation)->render()
    ?>
  </div>
<?php endif; ?>


<div class="admin_home_wrapper">

  <div class="admin_home_right">
    <?php echo $this->content()->renderWidget('radcodes.admin-modules') ?>
  </div>

  <div class="admin_home_middle">
    <?php echo $this->content()->renderWidget('radcodes.admin-news') ?>
  </div>

</div>
