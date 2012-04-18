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

<?php 

$content_photo = $this->htmlLink($this->item->getHref(), $this->itemPhoto($this->item, 'thumb.icon'));

$content_creation_date = $this->timestamp(strtotime($this->item->creation_date));

$content_owner = $this->translate('by')
        . ' ' . $this->htmlLink($this->item->getOwner()->getHref(), $this->item->getOwner()->getTitle());

$content_comments = $this->translate(array("%s comment", "%s comments", $this->item->comment_count), $this->item->comment_count);
$content_views = $this->translate(array("%s view", "%s views", $this->item->view_count), $this->item->view_count);

?>

<div id="radcodes_infowindow_pop_<?php echo $this->item->getGuid()?>" class="radcodes_infowindow_pop">
  <div class="radcodes_infowindow_pop_title"><?php echo $this->htmlLink($this->item->getHref(), $this->item->getTitle(), array('class'=>"radcodes_title")) ?></div>
  <div class="radcodes_infowindow_pop_address"><?php echo $this->location->formatted_address; ?></div>
  <div class="radcodes_infowindow_pop_photo"><?php echo $content_photo; ?></div>
  <div class="radcodes_infowindow_pop_meta">
    <span class="radcodes_infowindow_pop_meta_owner"><?php echo $content_owner; ?></span>
    <span class="radcodes_infowindow_pop_meta_date"><?php echo $content_creation_date; ?></span>
    <span class="radcodes_infowindow_pop_meta_stat"><?php echo $content_comments; ?> | <?php echo $content_views; ?></span>
  </div>
</div>
