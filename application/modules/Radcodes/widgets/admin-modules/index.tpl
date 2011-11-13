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

<div class="admin_home_news">
  <h3 class="sep">
    <span><?php echo $this->translate("Store") ?></span>
  </h3>

	<table class="admin_home_stats">
	  <thead>
	    <tr>
	      <th colspan="2"><?php echo $this->translate('Radcodes Modules')?></th>
	    </tr>
	  </thead>
	  <tbody>
    <?php if (!empty($this->modules)): ?>
	    <?php foreach ($this->modules as $module): ?>
	      <tr>
	        <td><?php echo $this->htmlLink($module['url'], $module['title'], array('target'=>'_blank'));?></td>
	        <td><?php echo $module['version'];?></td>
	      </tr>
	    <?php endforeach; ?>
    <?php else: ?>
        <tr>
          <td>Coming soon ..</td>
        </tr>
    <?php endif; ?>
	  </tbody>
	</table>

</div>