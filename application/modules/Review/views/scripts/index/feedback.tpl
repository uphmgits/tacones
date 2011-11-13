<?php
/**
 * Radcodes - SocialEngine Module
 *
 * @category   Application_Extensions
 * @package    Business
 * @copyright  Copyright (c) 2009-2010 Radcodes LLC (http://www.radcodes.com)
 * @license    http://www.radcodes.com/license/
 * @version    $Id$
 * @author     Vincent Van <vincent@radcodes.com>
 */
?>

<?php
$viewer = $this->viewer();
$review = $this->review; 
$vote = $review->votes()->getVote($viewer);
?>

<div class="review_vote_feedback" id="review_vote_feedback_review_<?php echo $this->review->getIdentity();?>">
  <div class="review_vote_about"><?php echo $this->translate('Help other members find the most helpful reviews')?></div>
  <span class="review_vote_question">
    <?php echo $this->translate('Was this review helpful to you?')?>
  </span>
  <?php if ($vote): ?>
    <span class="review_voted_actions">
    <?php if ($vote->helpful): ?>
      <a href="javascript:void(0)" onclick="en4.review.unvote(<?php echo $review->getIdentity()?>)" class="review_voted_yes">
        <span class="review_vote_label"><?php echo $this->translate('helpful')?></span>
        <span class="review_vote_count"><?php echo $review->votes()->getHelpfulVoteCount()?></span>
      </a>
      <a class="review_vote_no">
        <span class="review_vote_label"><?php echo $this->translate('not helpful')?></span>
        <span class="review_vote_count"><?php echo $review->votes()->getNotHelpfulVoteCount()?></span> 
      </a>
    <?php else: ?>
      <a class="review_vote_yes">
        <span class="review_vote_label"><?php echo $this->translate('helpful')?></span>
        <span class="review_vote_count"><?php echo $review->votes()->getHelpfulVoteCount()?></span>
      </a> 
      <a href="javascript:void(0)" onclick="en4.review.unvote(<?php echo $review->getIdentity()?>)" class="review_voted_no">
        <span class="review_vote_label"><?php echo $this->translate('not helpful')?></span>
        <span class="review_vote_count"><?php echo $review->votes()->getNotHelpfulVoteCount()?></span>
      </a>
    <?php endif; ?>
    </span> 
  <?php else: ?>
    <span class="review_vote_actions">
      <a href="javascript:void(0)" onclick="en4.review.vote(<?php echo $review->getIdentity()?>, 1)" class="review_vote_yes">
        <span class="review_vote_label"><?php echo $this->translate('helpful')?></span>
        <span class="review_vote_count"><?php echo $review->votes()->getHelpfulVoteCount()?></span>
      </a> 
      
      <a href="javascript:void(0)" onclick="en4.review.vote(<?php echo $review->getIdentity()?>, 0)" class="review_vote_no">
        <span class="review_vote_label"><?php echo $this->translate('not helpful')?></span>
        <span class="review_vote_count"><?php echo $review->votes()->getNotHelpfulVoteCount()?></span>
      </a>
    </span>
  <?php endif; ?>
  
  <?php if ($this->message): ?>
    <span class="review_vote_message <?php $this->error ? 'review_vote_error' : 'review_vote_notice' ?>">
      <?php echo $this->translate($this->message)?>
    </span>
  <?php endif; ?> 
</div>


