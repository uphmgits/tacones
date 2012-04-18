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
<?php if ($this->stats): ?>
  <ul class="reviews_widget_list">
    <?php foreach( $this->stats as $user_id => $total ): $user = $this->user($user_id); ?>
      <li>
        <?php if ($this->showphoto): ?>
          <div class="review_photo">
            <?php echo $this->htmlLink($user->getHref(), $this->itemPhoto($user, 'thumb.icon')); ?>
          </div>
        <?php endif; ?>
        <div class="review_info">
          <div class="review_user"><?php echo $this->htmlLink($user->getHref(), $user->getTitle());?></div>
          <?php if ($this->showstars): ?>
            <div class="review_rating_star_small"><span style="width: <?php echo number_format(Engine_Api::_()->review()->getUserAverageRating($user) * 20,0) ?>%"></span></div>
          <?php endif; ?>
          <?php if ($this->showdetails): ?>
            <div class="review_stat">
            <?php 
              $total_recommend = Engine_Api::_()->review()->getUserRecommendCount($user);
              $total_review = Engine_Api::_()->review()->getUserReviewCount($user);
              $percent_recommend = number_format(($total_recommend / $total_review) * 100);
              echo $this->htmlLink(array('route'=>'review_user', 'id'=>$user_id),
                $this->translate('%1$s out of %2$s members recommended', $total_recommend, $total_review)
                )
            ?>
            </div>
          <?php endif; ?>
        </div>
      </li>  
    <?php endforeach; ?>
  </ul>
<?php endif; ?>