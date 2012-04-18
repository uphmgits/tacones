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

<div class='layout_right review_view_layout_right'>
  <div class='reviews_gutter'>
  
    <div class='reviews_gutter_user'>
      <?php echo $this->htmlLink($this->owner->getHref(),
        $this->itemPhoto($this->owner, 'thumb.profile')
      )?>
    </div>  
  
    <div class='reviews_gutter_owner_name'>
      <?php echo $this->htmlLink($this->owner->getHref(), $this->owner->getTitle()) ?>    
    </div>  
  
    <h4><?php echo $this->translate('Contribution Stats'); ?></h4>
    <div class="review_summary">
      <?php if ($this->total_review): ?>
        <ul>
          <li><?php echo $this->translate(array('Posted: %s review','Posted: %s reviews', $this->total_review), $this->total_review)?></li>
          <li><?php echo $this->translate(array('Average Rated: %s star','Average Rated: %s stars', $this->average_rating), number_format($this->average_rating,1))?></li>
          <li><?php echo $this->translate(array('Recommended: <a href=\'%2$s\'>%1$s member</a>','Recommended: <a href=\'%2$s\'>%1$s members</a>', $this->total_recommend), $this->total_recommend,
            $this->url(array('id'=>$this->owner->getIdentity(),'recommend'=>1),'review_owner',true)
          )?></li>
        </ul>
      <?php else: ?>
        <?php echo $this->translate('No review has been posted yet.')?>
      <?php endif; ?>
    </div>
    

    <h4><?php echo $this->translate('Rating Breakdown'); ?></h4>
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
          <span class="review_distribution_histogram_label"><?php echo $this->htmlLink(array('route'=>'review_owner','id'=>$this->owner->getIdentity(),'rating'=>$star), $this->translate(array("%s star", "%s stars", $star), $this->locale()->toNumber($star)));?></span>
          <span class="review_distribution_histogram_bar"><span style="width: <?php echo $rating_percentage?>%"></span></span>
          <span class="review_distribution_histogram_count"><?php echo $rating_count;?></span>
        </li>
      <?php endfor;?>
    </ul>

    <?php if( $this->paginatorUserReview->getTotalItemCount() > 0 ): ?>
      <h4><?php echo $this->translate('%s\'s Reviews', $this->owner->getTitle())?></h4>
      <ul class="reviews_list">
        <?php foreach ($this->paginatorUserReview as $review): ?>
        <li>
          <div class="review_photo">
            <?php echo $this->htmlLink($review->getOwner()->getHref(), $this->itemPhoto($review->getOwner(), 'thumb.icon')); ?>
          </div>
          <div class="review_info">
            <div class="review_user"><?php echo $this->htmlLink($review->getOwner()->getHref(), $review->getOwner()->getTitle());?></div>
            <div class="review_rating_star_small"><span style="width: <?php echo $review->rating * 20 ?>%"></span></div>
            <div class="review_title"><?php echo $this->htmlLink($review->getHref(), $this->radcodes()->text()->truncate($review->getTitle(), 46)); ?></div>
          </div>
        </li>  
        <?php endforeach; ?>
      </ul>
      <div class="reviews_gutter_reviewer_action"><?php echo $this->htmlLink(array('route'=>'review_user','id'=>$this->owner->getIdentity()), $this->translate('See all reviews &raquo;'))?></div>
    <?php endif; ?>
  </div>
</div>

<div class='layout_middle review_layout_middle'>
  <div class='review_list'>
    <h2>
      <?php echo $this->translate('%s\'s Reviewed Members', $this->owner->__toString())?>
    </h2>
    
      <?php echo $this->translate('Below are members whom %s has reviewed.', $this->owner->__toString());?>
          
      <?php echo $this->translate('You can also check out <a href=\'%2$s\'>%1$s\'s reviews</a> posted by other members.', $this->owner->getTitle(), $this->url(array('id'=>$this->owner->getIdentity()), 'review_user', true));?>    

      <?php echo $this->form->render($this); ?>
    
        
    <?php if( $this->paginator->getTotalItemCount() > 0 ): ?>

      <ul class='reviews_rows'>
        <?php foreach ($this->paginator as $review): $review_user = $review->getUser(); ?>
          <li>
            <div class="review_photo">
              <?php echo $this->htmlLink($review_user->getHref(), $this->itemPhoto($review_user, 'thumb.icon'));?>
            </div>
            <div class="review_main">
              <div class="review_user_title"><?php echo $this->htmlLink($review_user->getHref(), $review_user->getTitle()) ?></div>
              <div class="review_rating_star"><span style="width: <?php echo $review->rating * 20?>%"></span></div> 
            </div>
            <div class="review_content">
              <?php if ($review->vote_count): ?>
                <div class="review_helpful_stat">
                  <?php echo $this->translate(array('%1$s of %2$s person found the following review helpful:','%1$s of %2$s people found the following review helpful:',$review->vote_count), $review->helpful_count, $review->vote_count)?>
                </div>
              <?php endif; ?>
              <div class="review_title"><?php echo $this->htmlLink($review->getHref(), $review->getTitle()); ?></div>
              <div class="review_info_meta">
                <span class="review_info_meta_date"><?php echo $this->timestamp(strtotime($review->creation_date)) ?></span>
                -
                <?php echo $this->translate(array("%s comment", "%s comments", $review->comment_count), $this->locale()->toNumber($review->comment_count)); ?>
                -
                <?php echo $this->translate(array('%1$s like', '%1$s likes', $review->like_count), $this->locale()->toNumber($review->like_count)); ?>
              </div>
            </div>
            <div class="review_options">
              <?php if ($review->recommend): ?>
                <div class="review_recommend"><?php echo $this->translate("Recommended")?></div>
              <?php endif; ?> 
            </div>
          </li>
        <?php endforeach; ?>  
      </ul>
      <?php echo $this->paginationControl($this->paginator, null, null, array(
        'query' => $this->formValues
      )); ?>       
    <?php elseif( $this->rating || $this->keyword || isset($this->recommend)):?>
      <div class="tip">
        <span>
          <?php echo $this->translate('This member has not posted any reviews with that criteria.');?>
        </span>
      </div>      
    <?php else: ?>
      <div class="tip">
        <span>
          <?php echo $this->translate('This member has not posted a review yet.');?>
        </span>
      </div>
    <?php endif; ?>
  </div>
</div>