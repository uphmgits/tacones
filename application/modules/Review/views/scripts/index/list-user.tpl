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
      <?php echo $this->htmlLink($this->user->getHref(),
        $this->itemPhoto($this->user, 'thumb.profile')
      )?>
    </div>  
  
    <div class='reviews_gutter_owner_name'>
      <?php echo $this->htmlLink($this->user->getHref(), $this->user->getTitle()) ?>    
    </div>  
  
    <a href="<?php echo $this->url(array('id'=>$this->subject()->getIdentity()), 'review_user', true)?>">
      <span class="review_rating_star_big"><span style="width: <?php echo $this->average_rating * 20 ?>%"></span></span>
    </a>
    
    <div class="review_summary">
      <?php if ($this->total_review): ?>
        <?php //echo $this->translate(array("Average %s with %s review","Average %s with %s reviews", $this->total_review), number_format($this->average_rating,1), $this->total_review); ?>

        <ul>
          <li><?php echo $this->translate(array('Total: %s review','Total: %s reviews', $this->total_review), $this->total_review)?></li>
          <li><?php echo $this->translate(array('Average Rating: %s star','Average Rating: %s stars', $this->average_rating), number_format($this->average_rating,1))?></li>
          <li><?php $recommend_total = $this->translate(array('%s member','%s members', $this->total_recommend), $this->total_recommend); ?>
              <?php echo $this->translate('Recommended By: %1$s (%2$s%%)', 
                $this->htmlLink(array('route'=>'review_user','id'=>$this->subject()->getIdentity(), 'recommend'=>1), $recommend_total),
                number_format($this->total_recommend / $this->total_review * 100, 0)
              )?>
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
          <span class="review_distribution_histogram_label"><?php echo $this->htmlLink(array('route'=>'review_user','id'=>$this->user->getIdentity(),'rating'=>$star), $this->translate(array("%s star", "%s stars", $star), $this->locale()->toNumber($star)));?></span>
          <span class="review_distribution_histogram_bar"><span style="width: <?php echo $rating_percentage?>%"></span></span>
          <span class="review_distribution_histogram_count"><?php echo $rating_count;?></span>
        </li>
      <?php endfor;?>
    </ul>
    
    
    <?php if ($this->viewer_review): ?>
      <ul class="reviews_gutter_options">
          <li>
            <?php echo $this->translate(array('You rated %1$s star on %2$s.','You rated %1$s stars on %2$s.', $this->viewer_review->rating), $this->viewer_review->rating, $this->timestamp(strtotime($this->viewer_review->creation_date)))?>
          </li>        
        <?php if ($this->can_edit): ?>
          <li><?php echo $this->htmlLink($this->viewer_review->getEditHref(), 'Edit Your Review', array('class'=>'buttonlink icon_review_edit'))?></li>
        <?php endif;?>

      </ul>     
    <?php elseif ($this->can_create): ?>
      <ul class="reviews_gutter_options">
        <li><?php echo $this->htmlLink(array('route'=>'review_create','to'=>$this->user->getIdentity()), 'Write Your Review', array('class'=>'buttonlink icon_review_new'))?></li>
      </ul>
    <?php endif; ?>  
      
    <?php if( $this->paginatorOwnerReview->getTotalItemCount() > 0 ): ?>
      <h4><?php echo $this->translate('%s\'s Reviewed Members', $this->user->getTitle())?></h4>
      <ul class="reviews_list">
        <?php foreach ($this->paginatorOwnerReview as $review): ?>
        <li>
          <div class="review_photo">
            <?php echo $this->htmlLink($review->getUser()->getHref(), $this->itemPhoto($review->getUser(), 'thumb.icon')); ?>
          </div>
          <div class="review_info">
            <div class="review_user"><?php echo $this->htmlLink($review->getUser()->getHref(), $review->getUser()->getTitle());?></div>
            <div class="review_rating_star_small"><span style="width: <?php echo $review->rating * 20 ?>%"></span></div>
            <div class="review_title"><?php echo $this->htmlLink($review->getHref(), $this->radcodes()->text()->truncate($review->getTitle(), 46)); ?></div>
          </div>
        </li>  
        <?php endforeach; ?>
      </ul>
      <div class="reviews_gutter_reviewer_action">
        <?php echo $this->htmlLink(array('route'=>'review_owner','id'=>$this->user->getIdentity()), $this->translate('View all reviewed members &raquo;'))?>
      </div>
    <?php endif; ?>
  </div>
</div>

<div class='layout_middle review_layout_middle'>
  <div class='review_list'>
    <h2>
      <?php echo $this->translate('%s\'s Reviews', $this->user->__toString())?>
    </h2>
    
      <?php echo $this->translate('Below are reviews posted by other members for %s.', $this->user->__toString());?>
          
      <?php if ($this->can_create): ?>
        <?php echo $this->translate('Want to contribute?');?>
        <?php echo $this->htmlLink(array('route'=>'review_create','to'=>$this->user->getIdentity()), 'Write your review...')?>
      <?php endif; ?>      
 
      
  
  
      <?php echo $this->form->render($this); ?>
    

    <?php if( $this->paginator->getTotalItemCount() > 0 ): ?>

      <script type="text/javascript">
        //<![CDATA[
        en4.core.runonce.add(function() {
          var initializeReview = function() {
            en4.review.urls.vote = '<?php echo $this->url(array('action' => 'vote'), 'review_general') ?>';
            en4.review.urls.unvote = '<?php echo $this->url(array('action' => 'unvote'), 'review_general') ?>';
            en4.review.urls.login = '<?php echo $this->url(array(), 'user_login') ?>';
          }
      
          // Dynamic loading for feed
          if( $type(en4) == 'object' && 'review' in en4 ) {
        	  initializeReview();
          } else {
            new Asset.javascript('application/modules/Review/externals/scripts/core.js', {
              onload: function() {
            	  initializeReview();
              }
            });
          }
        });
        //]]>
      </script>

      <ul class='reviews_entries'>
        <?php foreach ($this->paginator as $review): $review_owner = $review->getOwner(); ?>
          <li<?php if ($review->featured):?> class="review_featured_entry"<?php endif;?>>
            <div class="review_photo">
              <?php echo $this->htmlLink($review_owner->getHref(), $this->itemPhoto($review_owner, 'thumb.icon'));?>
            </div>
            <div class="review_info">
              <div class="review_info_header">
                <span class="review_rating_star"><span style="width: <?php echo $review->rating * 20?>%"></span></span>
                <?php echo $this->htmlLink($review->getHref(), $review->getTitle()); ?>
              </div>
              <div class="review_info_meta">
                <?php if ($review->recommend): ?>
                  <span class="review_recommend">
                    <?php echo $this->translate("Recommended")?>
                  </span>
                  /
                <?php endif; ?>
                <span class="review_owner"><?php echo $this->translate('Reviewed by %s', $review_owner->__toString())?></span>
                (<?php echo $this->htmlLink(array('route'=>'review_owner','id'=>$review->owner_id), $this->translate('See all my reviews'))?>)
                
                <span class="review_info_meta_date"><?php echo $this->timestamp(strtotime($review->creation_date)) ?></span>
                -
                <?php echo $this->translate(array("%s comment", "%s comments", $review->comment_count), $this->locale()->toNumber($review->comment_count)); ?>
                -
                <?php echo $this->translate(array('%1$s like', '%1$s likes', $review->like_count), $this->locale()->toNumber($review->like_count)); ?>
              </div>
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
              
              <?php if ($review_field_values = $this->fieldValueLoop($review, Engine_Api::_()->fields()->getFieldsStructurePartial($review))): ?>
                <div class="profile_fields">
                  <div class="review_info_details_header"><?php echo $this->translate('Review Details:')?></div>
                	<?php echo $review_field_values; ?>
                </div>
              <?php endif; ?>
              
              <?php if ($review->recommend): ?>
                <div class="review_info_recommend">
                  <?php echo $this->translate("I would recommend this member to a friend!")?>
                </div>  
              <?php endif; ?>            
              
              <?php echo $this->partial('index/feedback.tpl', 'review', array('review'=>$review))?>
              
              <div class="review_tool_links">
                <?php echo $this->htmlLink(array('module'=> 'activity', 'controller' => 'index', 'action' => 'share', 'route' => 'default', 'type' => 'review', 'id' => $review->getIdentity(), 'format' => 'smoothbox'), $this->translate("Share"), array('class' => 'smoothbox')); ?>
               - <?php echo $this->htmlLink(array('module'=> 'core', 'controller' => 'report', 'action' => 'create', 'route' => 'default', 'subject' =>  $review->getGuid(), 'format' => 'smoothbox'), $this->translate("Report"), array('class' => 'smoothbox')); ?>
               - 
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
    
          </li>
        <?php endforeach; ?>  
      </ul>
      <?php echo $this->paginationControl($this->paginator, null, null, array(
        'query' => $this->formValues
      )); ?>       
    <?php elseif( $this->rating || $this->keyword):?>
      <div class="tip">
        <span>
          <?php echo $this->translate('Nobody has posted a review with that criteria.');?>
          <?php if ($this->can_create): ?>
            <?php echo $this->translate('Be the first to <a href=\'%s\'>post</a> one!', $this->url(array(), 'review_create')); ?>
          <?php endif; ?>
        </span>
      </div>      
    <?php else: ?>
      <div class="tip">
        <span>
          <?php echo $this->translate('Nobody has posted a review yet.');?>
          <?php if ($this->can_create): ?>
            <?php echo $this->translate('Be the first to <a href=\'%s\'>post</a> one!', $this->url(array(), 'review_create')); ?>
          <?php endif; ?>
        </span>
      </div>
    <?php endif; ?>
  </div>
</div>