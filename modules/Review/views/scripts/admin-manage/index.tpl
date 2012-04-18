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

var currentOrder = '<?php echo $this->order ?>';
var currentOrderDirection = '<?php echo $this->order_direction ?>';
var changeOrder = function(order, default_direction){
  // Just change direction
  if( order == currentOrder ) {
    $('order_direction').value = ( currentOrderDirection == 'ASC' ? 'DESC' : 'ASC' );
  } else {
    $('order').value = order;
    $('order_direction').value = default_direction;
  }
  $('filter_form').submit();
}


  en4.core.runonce.add(function(){$$('th.admin_table_short input[type=checkbox]').addEvent('click', function(){ $$('input[type=checkbox]').set('checked', $(this).get('checked', false)); })});

  var delectSelected =function(){
    var checkboxes = $$('input[type=checkbox]');
    var selecteditems = [];

    checkboxes.each(function(item, index){
      var checked = item.get('checked', false);
      var value = item.get('value', false);
      if (checked == true && value != 'on'){
        selecteditems.push(value);
      }
    });

    $('ids').value = selecteditems;
    $('delete_selected').submit();
  }

</script>

<h2><?php echo $this->translate("Reviews Plugin") ?></h2>

<?php if( count($this->navigation) ): ?>
  <div class='tabs'>
    <?php
      // Render the menu
      //->setUlClass()
      echo $this->navigation()->menu()->setContainer($this->navigation)->render()
    ?>
  </div>
<?php endif; ?>

<p>
  <?php echo $this->translate("This page lists all of the reviews your users have posted. You can use this page to monitor these reviews and delete offensive material if necessary. Entering criteria into the filter fields will help you find specific reviews. Leaving the filter fields blank will show all the reviews on your social network.") ?>
</p>
<br />

<div class='admin_search'>
  <?php echo $this->formFilter->render($this) ?>
</div>

<br />

<div class='admin_results'>
  <div>
    <?php $reviewCount = $this->paginator->getTotalItemCount() ?>
    <?php echo $this->translate(array("%s review found", "%s reviews found", $reviewCount), ($reviewCount)) ?>
  </div>
  <div>
    <?php // echo $this->paginationControl($this->paginator, null, null, array('params'=>$this->params)); ?>
    
    <?php echo $this->paginationControl($this->paginator, null, null, array(
      'query' => $this->formValues
    )); ?>  
    
  </div>
</div>
<?php //print_r($this->params)?>
<br />

<?php if( count($this->paginator) ): ?>

<table class='admin_table'>
  <thead>
    <tr>
      <th class='admin_table_short'><input type='checkbox' class='checkbox' /></th>
      <th class='admin_table_short'><a href="javascript:void(0);" onclick="javascript:changeOrder('review_id', 'DESC');">ID</a></th>
      <th><?php echo $this->translate('Reviewer');?></th>
      <th><a href="javascript:void(0);" onclick="javascript:changeOrder('title', 'ASC');"><?php echo $this->translate("Title") ?></a></th>
      <th><a href="javascript:void(0);" onclick="javascript:changeOrder('rating', 'DESC');"><?php echo $this->translate("Rating")?></a></th>
      <th><?php echo $this->translate('User');?></th>
      <th><a href="javascript:void(0);" onclick="javascript:changeOrder('creation_date', 'DESC');"><?php echo $this->translate("Date") ?></a></th>
      <th><a href="javascript:void(0);" onclick="javascript:changeOrder('view_count', 'DESC');"><?php echo $this->translate("V")?></a></th>
      <th><a href="javascript:void(0);" onclick="javascript:changeOrder('comment_count', 'DESC');"><?php echo $this->translate("C")?></a></th>
      <th><a href="javascript:void(0);" onclick="javascript:changeOrder('like_count', 'DESC');"><?php echo $this->translate("L")?></a></th>
      <th><a href="javascript:void(0);" onclick="javascript:changeOrder('helpful_count', 'DESC');"><?php echo $this->translate("H")?></a></th>
      <th><a href="javascript:void(0);" onclick="javascript:changeOrder('vote_count', 'DESC');"><?php echo $this->translate("T")?></a></th>
      <th><?php echo $this->translate("Icons") ?> [<a href="javascript:void(0);" onclick="Smoothbox.open($('review_icons_legend')); return false;">?</a>]</th>      <th><?php echo $this->translate("Options") ?></th>
    </tr>
  </thead>
  <tbody>
    <?php foreach ($this->paginator as $item): // $this->string()->chunk($item->getTitle(), 5) ?>
      <tr>
        <td><input type='checkbox' class='checkbox' value="<?php echo $item->review_id ?>"/></td>
        <td><?php echo $item->review_id ?></td>
        <td><?php echo $this->htmlLink($item->getOwner()->getHref(), $item->getOwner()->getTitle(), array('target'=>'_blank'))?></td>
        <td style="white-space: normal;"><?php echo $this->htmlLink($item->getHref(), $this->radcodes()->text()->truncate($item->getTitle(),32), array('target' => '_blank')) ?></td>
        <td><span class="review_rating_star_small"><span style="width: <?php echo $item->rating * 20?>%"></span></span></td>
        <td><?php echo $this->htmlLink($item->getUser()->getHref(), $item->getUser()->getTitle(), array('target'=>'_blank'))?></td>
        <td><?php echo $this->timestamp($item->creation_date) ?></td>
        <td><?php echo $this->locale()->toNumber($item->view_count) ?></td>
        <td><?php echo $this->locale()->toNumber($item->comment_count) ?></td>
        <td><?php echo $this->locale()->toNumber($item->like_count) ?></td>
        <td><?php echo $this->locale()->toNumber($item->helpful_count) ?></td>
        <td><?php echo $this->locale()->toNumber($item->vote_count) ?></td>
        <td><?php echo $this->htmlLink(
            array('route' => 'default', 'module' => 'review', 'controller' => 'admin-manage', 'action' => 'featured', 'id' => $item->review_id),
            $this->htmlImage('./application/modules/Review/externals/images/featured'.($item->featured ? "" : "_off").'.png'),
            array('class' => 'smoothbox', 'title' => $this->translate($item->featured ? "Featured" : "Not Featured"))) ?>
            <?php echo $this->htmlImage('./application/modules/Review/externals/images/recommend'.($item->recommend ? "" : "_off").'.png', array('title'=>($item->recommend ? "Recommend On" : "No Recommend"))) ?>
        </td>
        <td>
          <a href="<?php echo $item->getEditHref(); ?>" target="_blank">
            <?php echo $this->translate("edit") ?>
          </a>
          |
          <?php echo $this->htmlLink(
            array('route' => 'default', 'module' => 'review', 'controller' => 'admin-manage', 'action' => 'delete', 'id' => $item->review_id),
            $this->translate("delete"),
            array('class' => 'smoothbox')) ?>
        </td>
      </tr>
    <?php endforeach; ?>
  </tbody>
</table>

<br />

<div class='buttons'>
  <button onclick="javascript:delectSelected();" type='submit'>
    <?php echo $this->translate("Delete Selected") ?>
  </button>
</div>

<form id='delete_selected' method='post' action='<?php echo $this->url(array('action' =>'deleteselected')) ?>'>
  <input type="hidden" id="ids" name="ids" value=""/>
</form>
<br/>

<?php //print_r($this->params)?>
<?php else:?>
  <div class="tip">
    <span>
      <?php echo $this->translate("There are no reviews posted by your members yet.") ?>
    </span>
  </div>
<?php endif; ?>


<div style="display: none">
    
  <ul class="radcodes_admin_icons_legend" id="review_icons_legend">
    <li><?php echo $this->htmlImage('./application/modules/Review/externals/images/featured.png');?><?php echo $this->translate('Featured')?></li>
    <li><?php echo $this->htmlImage('./application/modules/Review/externals/images/featured_off.png');?><?php echo $this->translate('Not Featured')?></li>
    <li><?php echo $this->htmlImage('./application/modules/Review/externals/images/recommend.png');?><?php echo $this->translate('Reviewer would recommend this member to a friend')?></li>
    <li><?php echo $this->htmlImage('./application/modules/Review/externals/images/recommend_off.png');?><?php echo $this->translate('Reviewer left recommend box unchecked')?></li>
    <li>
  <p><?php echo $this->translate('Stats:')?>
  <br /><?php echo $this->translate('V = total views')?>
  <br /><?php echo $this->translate('C = total comments')?>
  <br /><?php echo $this->translate('L = total likes')?>
  <br /><?php echo $this->translate('H = total voted helpful')?>
  <br /><?php echo $this->translate('T = total votes (helpful + not helpful)')?>
  </p>
    </li>
  </ul>
  
</div>
