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

<?php $totalReviews = $this->paginator->getTotalItemCount() ?>
<?php if( $totalReviews > 0 ): ?>

  <div class="reviews_profile_actions_container">
    <div class="reviews_profile_actions">
      <?=$this->translate(array('%1$d rewiew', '%1$d rewiews', $totalReviews), $totalReviews);?>
      <?php echo $this->htmlLink(array('route'=>'review_user', 'id'=>$this->subject()->getIdentity()), 
        $this->translate('View All Reviews'),
        array('class'=>'buttonlink')
      );?>
    </div>
  </div>
  <ul class='reviews_profile'>
    <?php foreach( $this->paginator as $review ):
      $review_owner = $review->getOwner();
      ?>
      <li id="review_review_<?php echo $review->getIdentity() ?>"<?php if ($review->featured):?> class="review_featured_entry"<?php endif;?>>
        <div class="review_photo">
          <?php echo $this->htmlLink($review_owner->getHref(), $this->itemPhoto($review_owner, 'thumb.icon'));?>
        </div>
        <div class="review_date">
            <?=$this->timestamp($this->user_review->creation_date)?>
        </div>
        <div class="review_info">
          <div class="review_owner">
            <?php echo $this->htmlLink($review_owner->getHref(), $review_owner->getTitle());?>
          </div>
          <?php if ($review->vote_count): ?>
            <div class="review_helpful_stat">
              <?php echo $this->translate(array('%1$s of %2$s person found the following review helpful:','%1$s of %2$s people found the following review helpful:',$review->vote_count), $review->helpful_count, $review->vote_count)?>
            </div>
          <?php endif; ?>
          <div class="review_info_header">
            <span class="review_rating_star"><span style="width: <?php echo $review->rating * 20?>%"></span></span>
            <?php echo $this->htmlLink($review->getHref(), $this->radcodes()->text()->truncate($review->getTitle(),80)); ?>
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
<?php endif; ?>  
