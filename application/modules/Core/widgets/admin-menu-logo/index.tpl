<?php
/**
 * SocialEngine
 *
 * @category   Application_Core
 * @package    Core
 * @copyright  Copyright 2006-2010 Webligo Developments
 * @license    http://www.socialengine.net/license/
 * @version    $Id: index.tpl 9325 2011-09-27 00:11:15Z john $
 * @author     John
 */
?>

<div id='global_header_logo'>
  <a href='<?php echo $this->url(array(), 'admin_default', true) ?>'>
    <?php echo $this->htmlImage($this->layout()->staticBaseUrl . 'application/modules/Core/externals/images/socialengine_logo_admin.png', $this->translate('SocialEngine Control Panel')) ?>
  </a>
</div>