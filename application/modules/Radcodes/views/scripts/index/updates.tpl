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

<h2><?php echo $this->translate("Radcodes") ?></h2>

<div class="admin_home_wrapper">

  <div class="admin_home_right">
    <?php echo $this->content()->renderWidget('radcodes.admin-modules') ?>
  </div>

  <div class="admin_home_middle">
    <?php echo $this->content()->renderWidget('radcodes.admin-news') ?>
  </div>

</div>
