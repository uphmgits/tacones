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

<div class="headline">
  <h2>
    <?php echo $this->translate('Reviews');?>
  </h2>
  <div class="tabs">
    <?php
      // Render the menu
      echo $this->navigation()
        ->menu()
        ->setContainer($this->navigation)
        ->render();
    ?>
  </div>
</div>

<div class='layout_right reviews_toplist_layout_right'>
  <?php if( count($this->listNavigation) > 0 ): ?>
    <div class="quicklinks">
    <?php
      // Render the menu
      echo $this->navigation()
        ->menu()
        ->setContainer($this->listNavigation)
        ->render();
    ?>
    </div>
  <?php endif; ?>  
</div>

<div class='layout_middle review_layout_middle reviews_toplist_layout_middle'>

  <h2><?php echo $this->translate('Top Rated Members')?></h2>
  <div><?php echo $this->translate('Below are our top rated members sorted by average weighted ratings.')?></div>
<?php if ($this->stats): ?>
  <ol class="reviews_toplist">
    <?php foreach( $this->stats as $user_id => $total ): ?>
    <?php 
        $user = $this->user($user_id);
        $total_recommend = Engine_Api::_()->review()->getUserRecommendCount($user);
        $total_review = Engine_Api::_()->review()->getUserReviewCount($user);
        $percent_recommend = number_format(($total_recommend / $total_review) * 100);
        $distributions = Engine_Api::_()->review()->getUserReviewDistributions($user); 
    ?>
      <li>
        <div class="review_photo">
          <?php echo $this->htmlLink($user->getHref(), $this->itemPhoto($user, 'thumb.icon')); ?>
        </div>
        <div class="review_info">
          <div class="review_user"><?php echo $this->htmlLink($user->getHref(), $user->getTitle());?></div>
          <div class="review_rating_star_big"><span style="width: <?php echo number_format(Engine_Api::_()->review()->getUserAverageRating($user) * 20,0) ?>%"></span></div>
        </div>
        <div class="review_stat_review">
        <?php echo $this->htmlLink(array('route'=>'review_user', 'id'=>$user_id),
            $this->translate(array('%s review','%s reviews',$total_review), $total_review)
            );
        ?>
        </div>
        <div class="review_stat_recommend">
          <?php echo $this->htmlLink(array('route'=>'review_user', 'id'=>$user_id, 'recommend' => 1),
            $this->translate(array('%s recommend','%s recommends',$total_recommend), $total_recommend)
            );?>
        </div>
        <div class="review_breakdown">
          <ul class="review_distribution_histogram">
            <?php $rating_total = array_sum($this->distributions); ?>
            <?php for ($star = 5; $star >= 1; $star--): ?>
              <?php 
                $rating_count = isset($distributions[$star]) ? $distributions[$star] : 0;
                $rating_percentage = $rating_count > 0 
                  ? (int) ($rating_count * 100 / $total_review)
                  : 0;
              ?>
              <li>
                <span class="review_distribution_histogram_label"><?php echo $this->htmlLink(array('route'=>'review_user','id'=>$user->getIdentity(),'rating'=>$star), $this->translate(array("%s star", "%s stars", $star), $this->locale()->toNumber($star)));?></span>
                <span class="review_distribution_histogram_bar"><span style="width: <?php echo $rating_percentage?>%"></span></span>
                <span class="review_distribution_histogram_count"><?php echo $rating_count;?></span>
              </li>
            <?php endfor;?>
          </ul>
        </div>
      </li>  
    <?php endforeach; ?>
  </ol>
<?php else: ?>  
    <div class="tip">
      <span>
        <?php echo $this->translate('Nobody has posted any reviews yet.');?>
      </span>
    </div>
<?php endif; ?>

</div>
