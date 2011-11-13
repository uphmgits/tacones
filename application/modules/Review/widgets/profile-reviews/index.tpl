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
<div class="review_profile_reviews_highlight">
  <?php // print_r($this->distribution)?>
  <div class="review_rating_summary">
    <h3><?php echo $this->translate('Average Member Rating') ?></h3>
    <a href="<?php echo $this->url(array('id'=>$this->subject()->getIdentity()), 'review_user', true)?>">
      <span class="review_rating_star_big"><span style="width: <?php echo $this->average_rating * 20 ?>%"></span></span>
    </a>
    <div class="review_rating_summary_average">
      <?php if ($this->total_review): ?>
        <?php $text_num_reviews = $this->htmlLink(array('route'=>'review_user','id'=>$this->subject()->getIdentity()), $this->translate(array("%s review","%s reviews", $this->total_review), $this->total_review)); ?>
        <?php echo $this->translate('Rating: %1$s out of 5 stars (%2$s)', number_format($this->average_rating,1), $text_num_reviews); ?>  
      <?php else: ?>
        <?php echo $this->translate('No rating has been casted yet.')?>
      <?php endif; ?>
    </div>
    <div class="review_rating_summary_actions">
      <?php if( $this->viewer()->getIdentity()): ?>
      
        <?php if ($this->user_review): ?>
          <?php echo $this->translate(array('You reviewed this member with rating of %1s star on %2s.','You has reviewed this member with rating of %1s stars on %2s.', $this->user_review->rating), $this->user_review->rating, $this->timestamp($this->user_review->creation_date)); ?>
          <br/><?php echo $this->htmlLink($this->user_review->getHref(),
              $this->translate('Read your review')
          );?>
        <?php elseif ($this->can_review && !$this->subject()->isSelf($this->viewer())): ?>  
          <?php echo $this->translate('Share your thoughts about this member with others?'); ?>
          <br />
            <?php echo $this->htmlLink(array('route' => 'review_create', 'to' => $this->subject()->getIdentity()),
              $this->translate('Write a Review')
            )?>
        <?php endif; ?>  
      <?php else: ?>  
        <?php // echo $this->translate('Share your thoughts about this review with others?'); ?>
      <?php endif;?>
    </div>
  </div>
  <div class="review_distribution">
	  <h3><?php echo $this->translate('Rating Breakdown'); ?></h3>
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
          <span class="review_distribution_histogram_label"><?php echo $this->htmlLink(array('route'=>'review_user','id'=>$this->subject()->getIdentity(),'rating'=>$star), $this->translate(array("%s star", "%s stars", $star), $this->locale()->toNumber($star)));?></span>
          <span class="review_distribution_histogram_bar"><span style="width: <?php echo $rating_percentage?>%"></span></span>
          <span class="review_distribution_histogram_count"><?php echo $rating_count;?></span>
        </li>
      <?php endfor;?>
    </ul>
  </div>

</div>


<?php if( $this->paginator->getTotalItemCount() > 0 ): ?>

  <div class="review_profile_recommend">
    <?php echo $this->htmlLink(array('route'=>'review_user', 'id'=>$this->subject()->getIdentity()), 
      $this->translate('View All Reviews'),
      array('class'=>'review_profile_recommend_link')
    );?>  
    <?php echo $this->translate(array('%1$d out of %2$d (%3$d%%) member would recommend %4$s to a friend.','%1$d out of %2$d (%3$d%%) members would recommend %4$s to a friend.', $this->total_review), 
      $this->total_recommend, $this->total_review, number_format($this->total_recommend / $this->total_review * 100, 0), $this->subject()->getTitle()
    ); ?>
  </div>

  <ul class='reviews_profile'>
    <?php foreach( $this->paginator as $review ):
      $review_owner = $review->getOwner();
      ?>
      <li id="review_review_<?php echo $review->getIdentity() ?>"<?php if ($review->featured):?> class="review_featured_entry"<?php endif;?>>
        <div class="review_photo">
          <?php echo $this->htmlLink($review_owner->getHref(), $this->itemPhoto($review_owner, 'thumb.icon'));?>
        </div>
        <div class="review_info">
          <?php if ($review->vote_count): ?>
            <div class="review_helpful_stat">
              <?php echo $this->translate(array('%1$s of %2$s person found the following review helpful:','%1$s of %2$s people found the following review helpful:',$review->vote_count), $review->helpful_count, $review->vote_count)?>
            </div>
          <?php endif; ?>
          <div class="review_info_header">
            <span class="review_rating_star"><span style="width: <?php echo $review->rating * 20?>%"></span></span>
            <?php echo $this->htmlLink($review->getHref(), $this->radcodes()->text()->truncate($review->getTitle(),80)); ?>
          </div>
          <div class="review_info_meta">
            <?php if (!$this->showdetails): ?>
              <?php if ($review->recommend): ?>
                <span class="review_recommend">
                  <?php echo $this->translate("Recommended")?>
                </span>
                /
              <?php endif; ?>
            <?php endif;?>
            <span class="review_owner"><?php echo $this->translate('Reviewed by %s', $review_owner->__toString())?></span>
            (<?php echo $this->htmlLink(array('route'=>'review_owner','id'=>$review->owner_id), $this->translate('See all my reviews'))?>)
            <span class="review_info_meta_date"><?php echo $this->timestamp(strtotime($review->creation_date)) ?></span>
            -
            <?php echo $this->translate(array("%s comment", "%s comments", $review->comment_count), $this->locale()->toNumber($review->comment_count)); ?>
            -
            <?php echo $this->translate(array('%1$s like', '%1$s likes', $review->like_count), $this->locale()->toNumber($review->like_count)); ?>
          </div>
          <?php if ($this->showdetails): ?>
            <div class="review_info_details">
              <?php echo nl2br($review->body); ?>
              <?php if ($review->pros): ?>
                <div class="review_info_details_header"><?php echo $this->translate('Pros:')?></div>
                <div class="review_info_details_pros">
                  <?php echo nl2br($review->pros);?>
                </div>
              <?php endif;?>
              <?php if ($review->cons): ?>
                <div class="review_info_details_header"><?php echo $this->translate('Cons:')?></div>
                <div class="review_info_details_cons">
                  <?php echo nl2br($review->cons);?>
                </div>
              <?php endif;?>
            </div>
            
            <?php if ($review->recommend): ?>
            <div class="review_info_recommend">
              <?php echo $this->translate("I would recommend this member to a friend!")?>
            </div>
            <?php endif; ?>
            <div class="review_tool_links">
              <?php echo $this->htmlLink(array('module'=> 'activity', 'controller' => 'index', 'action' => 'share', 'route' => 'default', 'type' => 'review', 'id' => $review->getIdentity(), 'format' => 'smoothbox'), $this->translate("Share"), array('class' => 'smoothbox')); ?>
             - <?php echo $this->htmlLink(array('module'=> 'core', 'controller' => 'report', 'action' => 'create', 'route' => 'default', 'subject' =>  $review->getGuid(), 'format' => 'smoothbox'), $this->translate("Report"), array('class' => 'smoothbox')); ?>
             - <?php 
                    if ($review->comment_count) {
                       $comment_text = $this->translate(array("%s comment", "%s comments", $review->comment_count), $this->locale()->toNumber($review->comment_count));
                     }
                     else {
                       $comment_text = $this->translate('Post Comment');
                     }
                     echo $this->htmlLink($review->getHref().'#comments', $comment_text);
                   ?>
            </div>
          <?php else: ?>
            <div class="review_info_details">
              <?php echo $this->radcodes()->text()->truncate($review->body, 198); ?>
            </div>
          <?php endif; ?>
        </div>

      </li>

    <?php endforeach;?>
  </ul>
  <div class="reviews_profile_actions">
    <?php echo $this->htmlLink(array('route'=>'review_user', 'id'=>$this->subject()->getIdentity()), 
      $this->translate('View All Reviews'),
      array('class'=>'buttonlink item_icon_review')
    );?>
    <?php echo $this->htmlLink(array('route'=>'review_owner', 'id'=>$this->subject()->getIdentity()), 
      $this->translate("%s's Reviewed Members", $this->subject()->getTitle()),
      array('class'=>'buttonlink icon_review_member')
    );?>
  </div>
<?php endif; ?>  
