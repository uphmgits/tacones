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
<?php if ($this->paginator->getTotalItemCount()): ?>
  
  <?php if ($this->display_style == 'narrow'): ?>
    <ul class="reviews_list">
      <?php foreach ($this->paginator as $review): ?>
        <li>
          <?php if ($this->showphoto): ?>
          <div class="review_photo">
            <?php echo $this->htmlLink($review->getUser()->getHref(), $this->itemPhoto($review->getUser(), 'thumb.icon')); ?>
          </div>
          <?php endif; ?>
          <div class="review_info">
            <div class="review_user"><?php echo $this->htmlLink($review->getUser()->getHref(), $review->getUser()->getTitle());?></div>
            <div class="review_rating_star_small"><span style="width: <?php echo $review->rating * 20 ?>%"></span></div>
            <div class="review_title"><?php echo $this->htmlLink($review->getHref(), $this->radcodes()->text()->truncate($review->getTitle(), 46)); ?></div>
            <?php if ($this->showdetails): ?>
              <div class="review_info_meta">
                <?php if ($review->recommend): ?>
                  <?php echo $this->translate('Recommended')?>
                  /
                <?php endif; ?>
                <?php echo $this->translate('Reviewed by %s', $review->getOwner()->__toString())?>
                <br />
                <span class="review_info_meta_date"><?php echo $this->timestamp(strtotime($review->creation_date)) ?></span>
              </div>
            <?php endif;?>
          </div>
        </li>  
      <?php endforeach; ?>
    </ul>

  <?php else: ?>
    <ul class="reviews_recent">
    <?php foreach( $this->paginator as $review ): $user = $review->getUser(); ?>
      <li>
        <?php if ($this->showphoto): ?>
        <div class="review_photo">
          <?php echo $this->htmlLink($user->getHref(), $this->itemPhoto($user, 'thumb.icon'));?>
        </div>
        <?php endif; ?>
        <div class="review_main">
          <div class="review_user_title"><?php echo $this->htmlLink($user->getHref(), $user->getTitle()) ?></div>
          <div class="review_rating_star"><span style="width: <?php echo $review->rating * 20?>%"></span></div> 
        </div>  
        <div class="review_content">
          <?php if ($review->vote_count): ?>
            <div class="review_helpful_stat">
              <?php echo $this->translate(array('%1$s of %2$s person found the following review helpful:','%1$s of %2$s people found the following review helpful:',$review->vote_count), $review->helpful_count, $review->vote_count)?>
            </div>
          <?php endif; ?>
          <?php echo $this->htmlLink($review->getHref(), $review->getTitle(), array('class'=>'review_title')); ?>
          <?php if ($this->showdetails): ?>
            <div class="review_info_meta">
              <?php if ($review->recommend): ?>
                <?php echo $this->translate('Recommended')?>
                /
              <?php endif; ?>
              <?php echo $this->translate('Reviewed by %s', $review->getOwner()->__toString())?>
              <br />
              <span class="review_info_meta_date"><?php echo $this->timestamp(strtotime($review->creation_date)) ?></span>
                    -
                    <?php echo $this->translate(array("%s comment", "%s comments", $review->comment_count), $this->locale()->toNumber($review->comment_count)); ?>
                    -
                    <?php echo $this->translate(array('%1$s like', '%1$s likes', $review->like_count), $this->locale()->toNumber($review->like_count)); ?>
            </div>
          <?php endif;?>
        </div>
      </li>
    <?php endforeach; ?>
    </ul>
  <?php endif;?>
<?php endif; ?>