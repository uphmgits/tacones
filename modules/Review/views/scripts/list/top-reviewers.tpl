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

  <h2><?php echo $this->translate('Top Reviewers')?></h2>
  <div><?php echo $this->translate('Below are members who has contributed the most number of reviews.')?></div>
<?php if ($this->stats): ?>
  <ol class="reviews_toplist">
    <?php foreach( $this->stats as $owner_id => $total ): ?>
    <?php 
        $owner = $this->user($owner_id);
        $total_recommend = Engine_Api::_()->review()->getOwnerRecommendCount($owner);
        $total_review = Engine_Api::_()->review()->getOwnerReviewCount($owner);
        $percent_recommend = number_format(($total_recommend / $total_review) * 100);
        
        $helpful_count = Engine_Api::_()->getDbtable('reviews', 'review')->getSumColumn('helpful_count',array('owner'=>$owner));
        $vote_count = Engine_Api::_()->getDbtable('reviews', 'review')->getSumColumn('vote_count',array('owner'=>$owner));
        if ($vote_count) {
          $helpful_percent = number_format($helpful_count / $vote_count * 100, 0);
        }
        else {
          $helpful_percent = 0;
        }
    ?>
      <li>
        <div class="review_photo">
          <?php echo $this->htmlLink($owner->getHref(), $this->itemPhoto($owner, 'thumb.icon')); ?>
        </div>
        <div class="review_info">
          <div class="review_user"><?php echo $this->htmlLink($owner->getHref(), $owner->getTitle());?></div>
        </div>
        <div class="review_stat_review">
        <?php echo $this->htmlLink(array('route'=>'review_owner', 'id'=>$owner_id),
            $this->translate(array('%s review','%s reviews',$total_review), $total_review)
            );
        ?>
        </div>
        <div class="review_stat_recommend">
          <?php echo $this->htmlLink(array('route'=>'review_owner', 'id'=>$owner_id, 'recommend' => 1),
            $this->translate(array('%s recommend','%s recommends',$total_recommend), $total_recommend)
            );?>
        </div>
        <div class="review_breakdown">
          <?php if ($vote_count): ?>
            <?php echo $this->translate(array('%1$s%% helpful (%2$s vote)','%1$s%% helpful (%2$s votes)', $vote_count), $helpful_percent, $vote_count)?>
          <?php else: ?>
            <?php echo $this->translate('No vote')?>
          <?php endif;?>
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
