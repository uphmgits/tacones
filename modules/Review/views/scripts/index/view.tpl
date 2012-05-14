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
        <?php echo $this->translate(array("Average %s with %s review","Average %s with %s reviews", $this->total_review), $this->average_rating, $this->total_review); ?>
      <?php else: ?>
        <?php echo $this->translate('No review has been posted yet.')?>
      <?php endif; ?>
    </div>

    <?php
      // Render the menu
      echo $this->navigation()
        ->menu()
        ->setContainer($this->gutterNavigation)
        ->setUlClass('navigation reviews_gutter_options')
        ->render();
    ?>

    <h4><?php echo $this->translate('Reviewer') ?></h4>
    <div class="reviews_gutter_reviewer">
      <?php echo $this->htmlLink($this->owner->getHref(),
        $this->itemPhoto($this->owner, 'thumb.icon')
      )?>
      <div class="reviews_gutter_reviewer_info">
        <div class="review_owner"><?php echo $this->htmlLink($this->owner->getHref(), $this->owner->getTitle()) ?></div>
        <ul>
          <li><?php echo $this->translate(array("reviewed %s member", "reviewed %s members", $this->owner_total_review), $this->owner_total_review); ?></li>
          <li><?php echo $this->translate(array("average rated %s star", "average rated %s stars", $this->owner_average_rating), $this->owner_average_rating); ?></li>
        </ul>
      </div>
      <div class="reviews_gutter_reviewer_action"><?php echo $this->htmlLink(array('route'=>'review_owner','id'=>$this->owner->getIdentity()), $this->translate('See all reviewed members &raquo;'))?></div>
    </div>


  </div>
</div>

<div class='layout_middle review_layout_middle'>
  <div class='review_view<?php if ($this->review->featured):?> review_featured_entry<?php endif;?>'>
    <h2>
      <?php echo $this->translate('%s\'s Review', $this->user->__toString())?>
    </h2>
  
    <h3>
      <?php echo $this->review->getTitle() ?>
    </h3>
  
    <div class="review_rating_star"><span style="width: <?php echo $this->review->rating * 20?>%"></span></div>
  
    <div class="review_meta">
      <span class="review_owner"><?php echo $this->translate('Reviewed by %s', $this->owner->__toString())?></span>
      
      (<?php echo $this->htmlLink(array('route'=>'review_owner','id'=>$this->review->owner_id), $this->translate('See all my reviews'))?>)
      
      <?php echo $this->timestamp($this->review->creation_date) ?>
      - <?php echo $this->translate(array("%s view", "%s views", $this->review->view_count), $this->review->view_count); ?>
      <?php if (count($this->reviewTags )):?>
      - <?php echo $this->translate('Tags:')?>
        <?php foreach ($this->reviewTags as $tag): ?>
        <?php if (!empty($tag->getTag()->text)):?>
          #<?php echo $this->htmlLink(array('route'=>'review_browse', 'tag'=>$tag->getTag()->tag_id), $tag->getTag()->text)?>
        <?php endif; ?>
        <?php endforeach; ?>
      <?php endif; ?>
    </div>
    <div class="review_body">
      <?php echo nl2br($this->review->body) ?>
      <?php if ($this->review->pros): ?>
        <div class="review_info_details_header"><?php echo $this->translate('Pros:')?></div>
        <div class="review_info_details_pros">
          <?php echo nl2br($this->review->pros);?>
        </div>
      <?php endif;?>
      <?php if ($this->review->cons): ?>
        <div class="review_info_details_header"><?php echo $this->translate('Cons:')?></div>
        <div class="review_info_details_cons">
          <?php echo nl2br($this->review->cons);?>
        </div>
      <?php endif;?>
    </div> 
    
   
    
    <?php if ($review_field_values = $this->fieldValueLoop($this->review, $this->fieldStructure)): ?>
    <div class="profile_fields">
      <h4>
        <span><?php echo $this->translate('Review Details');?></span>
      </h4>
    	<?php echo $review_field_values; ?>
    </div>
    <?php endif; ?>
    
    <?php if ($this->review->recommend): ?>
      <div class="review_info_recommend">
        <?php echo $this->translate("I would recommend this member to a friend!")?>
      </div>
    <?php endif; ?> 
    
    <?php echo $this->partial('index/feedback.tpl', 'review', array('review'=>$this->review))?>
  
    <div class="review_tool_links">
      <?php echo $this->htmlLink(Array('module'=> 'activity', 'controller' => 'index', 'action' => 'share', 'route' => 'default', 'type' => 'review', 'id' => $this->review->getIdentity(), 'format' => 'smoothbox'), $this->translate("Share"), array('class' => 'smoothbox')); ?>
      - <?php echo $this->htmlLink(Array('module'=> 'core', 'controller' => 'report', 'action' => 'create', 'route' => 'default', 'subject' =>  $this->review->getGuid(), 'format' => 'smoothbox'), $this->translate("Report"), array('class' => 'smoothbox')); ?>
    </div>
       	  
    <a name="comments"></a>
    <?php echo $this->action("list", "comment", "core", array("type"=>"review", "id"=>$this->review->getIdentity())) ?>
  </div>
</div>