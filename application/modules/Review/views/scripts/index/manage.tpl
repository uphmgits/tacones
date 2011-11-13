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
          $this->url(array('tag'=>$this->tag), 'review_manage', true),
          $this->tagObject ? $this->tagObject->text : $this->tag
        ));?>
      <?php endif; ?>
      <?php if ($this->search): ?>
        <?php echo $this->translate('with keyword %s', $this->htmlLink(
          $this->url(array('search'=>$this->search), 'review_manage', true),
          $this->search
        ));?>
      <?php endif; ?> 
      <?php if ($this->rating): ?>
        <?php echo $this->translate(array('having %s star','having %s stars',$this->rating), $this->htmlLink(
          $this->url(array('rating'=>$this->rating), 'review_manage', true),
          $this->rating
        ));?>
      <?php endif; ?> 
      <?php if ($this->recommend): ?>
        <?php echo $this->translate('that have %s', 
          $this->htmlLink(array('recommend'=>$this->recommend,'route'=>'review_manage'),
            $this->translate($this->recommend ? 'recommend' : 'no recommend')
        )); ?>
      <?php endif;?>
      <a href="<?php echo $this->url(array(), 'review_manage', true) ?>">(x)</a>
    </div>
  <?php endif; ?>
  
  <?php if( $this->paginator->getTotalItemCount() > 0 ): ?>
  
      <ul class='reviews_rows'>
        <?php foreach ($this->paginator as $review): $user = $review->getUser(); ?>
          <li>
            <div class="review_photo">
              <?php echo $this->htmlLink($user->getHref(), $this->itemPhoto($user, 'thumb.icon'));?>
            </div>
            <div class="review_main">
              <div class="review_user_title"><?php echo $this->htmlLink($user->getHref(), $user->getTitle()) ?></div>
              <div class="review_rating_star"><span style="width: <?php echo $review->rating * 20?>%"></span></div> 
            </div>
            <div class="review_content">
              <div class="review_title"><?php echo $this->htmlLink($review->getHref(), $review->getTitle()); ?></div>
              <div class="review_info_meta">
                <span class="review_info_meta_date"><?php echo $this->timestamp(strtotime($review->creation_date)) ?></span>
                -
                <?php echo $this->translate(array("%s comment", "%s comments", $review->comment_count), $this->locale()->toNumber($review->comment_count)); ?>
                -
                <?php echo $this->translate(array('%1$s like', '%1$s likes', $review->like_count), $this->locale()->toNumber($review->like_count)); ?>
                -
                <?php echo $this->translate(array("%s helpful", "%s helpfuls", $review->helpful_count), $this->locale()->toNumber($review->helpful_count)); ?>
              </div>
            </div>
            <div class="review_options">
              <?php if ($review->authorization()->isAllowed($this->viewer(), 'edit')): ?>
                <?php echo $this->htmlLink($review->getEditHref(), $this->translate('Edit Review'), array('class'=>'buttonlink icon_review_edit'))?>
              <?php endif; ?>
              <?php if ($review->authorization()->isAllowed($this->viewer(), 'delete')): ?>
                <?php echo $this->htmlLink($review->getDeleteHref(), $this->translate('Delete Review'), array('class'=>'buttonlink icon_review_delete'))?>
              <?php endif; ?>
              <?php if ($review->recommend): ?>
                <div class="review_recommend"><?php echo $this->translate("Recommended")?></div>
              <?php endif; ?> 
            </div>
          </li>
        <?php endforeach; ?>  
      </ul>

  <?php elseif($this->tag || $this->search || $this->rating || $this->recommend): ?>
    <div class="tip">
      <span>
        <?php echo $this->translate('You do not have any review that match your search criteria.');?>
      </span>
    </div>
  <?php else:?>
    <div class="tip">
      <span>
        <?php echo $this->translate('You do not have any reviews.');?>
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
