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

<div class="radcodes_popular_tags reviews_popular_tags">
  <ul>
  <?php foreach ($this->tags as $k => $tag): ?>
    <li><?php echo $this->htmlLink(array(
                'route' => 'review_browse',
                'tag' => $tag->tag_id),
      $tag->text, 
      array('class'=> "tag_x tag_$k")
    )?>
    <sup><?php echo $tag->total; ?></sup>
    </li>
  <?php endforeach; ?>
  </ul>
  <?php echo $this->htmlLink(array('route'=>'review_general','action'=>'tags'), $this->translate('All Review Tags'), array('class'=>'buttonlink icon_viewmore'))?>
</div>

  
