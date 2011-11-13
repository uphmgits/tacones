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
<?php echo $this->partial('index/_js_fields_search.tpl', 'review', array())?>

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

<div class='layout_right'>
  <?php echo $this->form->render($this) ?>
  <?php if( count($this->quickNavigation) > 0 ): ?>
    <div class="quicklinks">
      <?php
        // Render the menu
        echo $this->navigation()
          ->menu()
          ->setContainer($this->quickNavigation)
          ->render();
      ?>
    </div>
  <?php endif; ?>
</div>

<div class='layout_middle review_layout_middle'>

  <?php if( $this->tag || $this->search || $this->rating || $this->recommend):?>
    <div class="reviews_result_filter_details">
      <?php echo $this->translate('Showing reviews posted'); ?>
      <?php if ($this->tag): ?>
        <?php echo $this->translate('tagging #%s', $this->htmlLink(
          $this->url(array('tag'=>$this->tag), 'review_browse', true),
          $this->tagObject ? $this->tagObject->text : $this->tag
        ));?>
      <?php endif; ?>
      <?php if ($this->search): ?>
        <?php echo $this->translate('with keyword %s', $this->htmlLink(
          $this->url(array('search'=>$this->search), 'review_browse', true),
          $this->search
        ));?>
      <?php endif; ?> 
      <?php if ($this->rating): ?>
        <?php echo $this->translate(array('having %s star','having %s stars',$this->rating), $this->htmlLink(
          $this->url(array('rating'=>$this->rating), 'review_browse', true),
          $this->rating
        ));?>
      <?php endif; ?> 
      <?php if ($this->recommend): ?>
        <?php echo $this->translate('that has members reviewers %s', 
          $this->htmlLink(array('recommend'=>$this->recommend,'route'=>'review_browse'),
            $this->translate($this->recommend ? 'recommend' : 'do not recommend')
        )); ?>
      <?php endif;?>
      <a href="<?php echo $this->url(array(), 'review_browse', true) ?>">(x)</a>
    </div>
  <?php endif; ?>
  
  <?php if( $this->paginator->getTotalItemCount() > 0 ): ?>
  
      <ul class='reviews_browse'>
        <?php foreach ($this->paginator as $review): $user = $review->getUser(); ?>
          <li>
            <div class="review_photo">
              <?php echo $this->htmlLink($user->getHref(), $this->itemPhoto($user, 'thumb.icon'));?>
            </div>
            <div class="review_main">
              <div class="review_user_title"><?php echo $this->htmlLink($user->getHref(), $user->getTitle()) ?></div>
              <div class="review_rating_star"><span style="width: <?php echo $review->rating * 20?>%"></span></div> 
              <div class="review_link"><?php echo $this->htmlLink(array('route'=>'review_user','id'=>$review->user_id), $this->translate('All reviews'))?></div>
            </div>
            <div class="review_content">
              <?php if ($review->vote_count): ?>
                <div class="review_helpful_stat">
                  <?php echo $this->translate(array('%1$s of %2$s person found the following review helpful:','%1$s of %2$s people found the following review helpful:',$review->vote_count), $review->helpful_count, $review->vote_count)?>
                </div>
              <?php endif; ?>
              <?php echo $this->htmlLink($review->getHref(), $review->getTitle(), array('class'=>'review_title')); ?>
              <div class="review_info_meta">
                <span class="review_owner"><?php echo $this->translate('Reviewed by %s', $review->getOwner()->__toString())?></span>
                (<?php echo $this->htmlLink(array('route'=>'review_owner','id'=>$review->owner_id), $this->translate('See all my reviews'))?>)
                <span class="review_info_meta_date"><?php echo $this->timestamp(strtotime($review->creation_date)) ?></span>
                -
                <?php echo $this->translate(array("%s comment", "%s comments", $review->comment_count), $this->locale()->toNumber($review->comment_count)); ?>
                -
                <?php echo $this->translate(array('%1$s like', '%1$s likes', $review->like_count), $this->locale()->toNumber($review->like_count)); ?>
              </div>
              <div class="review_desc">
                <?php echo $this->radcodes()->text()->truncate($review->body, 255); ?>
              </div>
              <?php if ($review->recommend): ?>
              <div class="review_info_recommend">
                <?php echo $this->translate("I would recommend this member to a friend!")?>
              </div>
              <?php endif; ?>
            </div>
          </li>
        <?php endforeach; ?>  
      </ul>

  <?php elseif($this->tag || $this->search || $this->rating || $this->recommend): ?>
    <div class="tip">
      <span>
        <?php echo $this->translate('Nobody has posted any reviews with that criteria.');?>
      </span>
    </div>
  <?php else:?>
    <div class="tip">
      <span>
        <?php echo $this->translate('Nobody has posted any reviews yet.');?>
        <?php if ($this->can_create): ?>
          <?php echo $this->translate('Get started by <a href=\'%1$s\'>posting</a> a new review.', $this->url(array(), 'review_create'));?>
        <?php endif; ?>
      </span>
    </div>
  <?php endif; ?>

    <?php echo $this->paginationControl($this->paginator, null, null, array(
      'query' => $this->formValues
    )); ?>    

</div>
