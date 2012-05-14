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

<div class="review_profile_rating">
  <a href="<?php echo $this->url(array('id'=>$this->subject()->getIdentity()), 'review_user', true)?>">
    <span class="review_rating_star_big"><span style="width: <?php echo $this->average_rating * 20 ?>%"></span></span>
  </a>
  <div class="review_summary_average">
    <?php if ($this->total_review): ?>
      <?php $text_num_reviews = $this->htmlLink(array('route'=>'review_user','id'=>$this->subject()->getIdentity()), $this->translate(array("%s review","%s reviews", $this->total_review), $this->total_review)); ?>
      <?php echo $this->translate('Rating: %1$s stars (%2$s)', number_format($this->average_rating,1), $text_num_reviews); ?>   
      <br /><?php $recommend_total = $this->translate(array('%s recommend','%s recommends', $this->total_recommend), $this->total_recommend); ?>
              <?php echo $this->translate('with %1$s (%2$s%%)', 
                $this->htmlLink(array('route'=>'review_user','id'=>$this->subject()->getIdentity(), 'recommend'=>1), $recommend_total),
                number_format($this->total_recommend / $this->total_review * 100, 0)
              )?>
    <?php else: ?>
      <?php echo $this->translate('No rating has been casted yet.')?>
    <?php endif; ?>
  </div>
  <?php if ($this->viewer()->getIdentity()): ?>
    <?php if ($this->user_review): ?>
      <div class="review_profile_actions">
        <?php echo $this->htmlLink($this->user_review->getHref(),
          $this->translate('You rated %s/5', $this->user_review->rating)
        );?>
      </div>
    <?php elseif ($this->can_review && !$this->subject()->isSelf($this->viewer())): ?>  
      <div class="review_profile_actions">
      <?php echo $this->htmlLink(array('route' => 'review_create', 'to' => $this->subject()->getIdentity()),
        $this->translate('Review This Member')
      )?>
      </div>
    <?php endif; ?>  
  <?php endif; ?>
  <?php // var_dump($this->subject()->isSelf($this->viewer()))?>
  <?php // var_dump($this->can_review)?>
</div>
