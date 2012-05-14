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
<?php if ($this->review): $review = $this->review; $user = $this->review->getUser(); $owner = $this->review->getOwner(); ?>
  <div class="review_featured">
    <div class="review_photo">
      <?php echo $this->htmlLink($user->getHref(), $this->itemPhoto($user, 'thumb.profile')); ?>
      <div class="review_user"><?php echo $this->htmlLink($user->getHref(), $user->getTitle());?></div>
    </div>
    <div class="review_content">
      <?php echo $this->htmlLink($review->getHref(), 
        $this->radcodes()->text()->truncate($review->getTitle(), 36),
         array('class'=>'review_title')); ?>
      <div class="review_rating_star"><span style="width: <?php echo $review->rating * 20?>%"></span></div> 
      <div class="review_info_meta">
        <?php echo $this->htmlLink($owner->getHref(), $this->itemPhoto($owner, 'thumb.icon')); ?>
        <div class="review_info_meta_author">
          <?php if ($review->recommend): ?>
            <?php echo $this->translate('Recommended')?> / 
          <?php endif;?>
          <?php echo $this->translate('Reviewed by %s', $owner->__toString())?>
          <br>
          <span class="review_info_meta_date"><?php echo $this->timestamp(strtotime($review->creation_date)) ?></span>
        </div>
      </div>
      <div class="review_body">
        <?php echo $this->radcodes()->text()->truncate($review->body, 255); ?>
      </div>
      <div class="review_tools">
        <?php echo $this->htmlLink($review->getHref(), $this->translate('Read More'))?>
        |
       <?php 
         if ($review->comment_count) {
           $comment_text = $this->translate(array("%s comment", "%s comments", $review->comment_count), $this->locale()->toNumber($review->comment_count));
         }
         else {
           $comment_text = $this->translate('Post Comment');
         }
         echo $this->htmlLink($review->getHref().'#comments', $comment_text);
       ?>
      </div>
    </div>
  </div>

<?php endif; ?>