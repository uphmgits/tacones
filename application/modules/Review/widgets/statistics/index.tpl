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

  <ul class="review_distribution_histogram">
    <?php $rating_total = array_sum($this->distributions); ?>
    <?php for ($star = 5; $star >= 1; $star--): ?>
      <?php 
        $rating_count = isset($this->distributions[$star]) ? $this->distributions[$star] : 0;
        $rating_percentage = $rating_count > 0 
          ? (int) ($rating_count * 100 / $rating_total)
          : 0;
      ?>
      <li>
        <span class="review_distribution_histogram_label"><?php echo $this->htmlLink(array('route'=>'review_browse','rating'=>$star), $this->translate(array("%s star", "%s stars", $star), $this->locale()->toNumber($star)));?></span>
        <span class="review_distribution_histogram_bar"><span style="width: <?php echo $rating_percentage?>%"></span></span>
        <span class="review_distribution_histogram_count"><?php echo $rating_count;?></span>
      </li>
    <?php endfor;?>
  </ul>
  
