<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Mobi
 * @copyright  Copyright 2006-2010 Webligo Developments
 * @license    http://www.socialengine.net/license/
 * @version    $Id: index.tpl 8929 2011-05-12 20:22:49Z jung $
 * @author     Charlotte
 */
?>
<?php if( $this->mobile ) { ?>
  <?php echo $this->htmlLink($this->url().'?mobile=0', $this->translate('Full Site'))?>
<?php } else { ?>
  <?php echo $this->htmlLink($this->url().'?mobile=1', $this->translate('Mobile Site'))?>
<?php } ?>