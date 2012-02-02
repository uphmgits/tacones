<?php
/**
 * 
 *
 * @category   Application_Extensions
 * @package    Marketplace
 * @copyright  Copyright 2010 
 * * 
 * @version    $Id: view.tpl 7244 2010-09-01 01:49:53Z john $
 * 
 */
?>

<?php if( !$this->marketplace): ?>
<?php echo $this->translate('The marketplace you are looking for does not exist or has been deleted.');?>
<?php return; // Do no render the rest of the script in this mode
endif; ?>

<?php $inspectionEnable = Engine_Api::_()->getApi('settings', 'core')->getSetting('marketplace.inspectionenable', 0); ?>

<link rel="stylesheet" href="application/modules/Marketplace/externals/milkbox/css/milkbox/milkbox.css" type="text/css" media="screen" />
<script type="text/javascript" src="application/modules/Marketplace/externals/milkbox/js/mootools-1.2.5.1-more.js"></script>
<script type="text/javascript" src="application/modules/Marketplace/externals/milkbox/js/milkbox.js"></script>

<script type="text/javascript">
  var categoryAction =function(category){
    $('category').value = category;
    $('filter_form').submit();
  }
  var tagAction =function(tag){
    $('tag').value = tag;
    $('filter_form').submit();
  }
  var dateAction = function(start_date, end_date){
    $('start_date').value = start_date;
    $('end_date').value = end_date;
    $('filter_form').submit();
  }
  <?php if( $inspectionEnable ) : ?>
  function setInspection(el){
    var insp = document.getElementById('inspection_fee');
    var total_price = document.getElementById('total_price');
    var add_to_cart = document.getElementById('add_to_cart');

    add_to_cart.href = add_to_cart.href.replace("\/inspection\/1", ''); 
    if( el.checked ) {
      insp.innerHTML = "$<?=number_format($this->inspection_fee, 2)?>";
      total_price.innerHTML = "$<?=number_format($this->total_amount + $this->inspection_fee, 2)?>";
      add_to_cart.href = add_to_cart.href + "/inspection/1";
    }
    else {
      insp.innerHTML = "$0.00";
      total_price.innerHTML = "$<?=number_format($this->total_amount, 2)?>";
    }
    <?php endif; ?>
  }
</script>

<style>
  .marketplace_fieldvalueloop {
    width: 220px;
  }
  .marketplace_fieldvalueloop > ul > li {
    overflow: hidden;
    width: auto;
  }
</style>

<div class='layout_common'>
	<div class='layout_left'>
	  <div class='marketplaces_gutter'>
		<div class='marketplaces_gutter_photo'>
		  <?php if($this->main_photo):?>
			  <?php echo $this->htmlLink($this->main_photo->getPhotoUrl(), $this->itemPhoto($this->main_photo, 'normal'), array('class'=>'smoothbox11', 'rel'=>'milkbox[gall1]', 'title'=> $this->marketplace->getTitle())) ?>
		  <?php endif; ?>
		</div>
		<a href='<?php echo $this->url(array('id' => $this->marketplace->owner_id), 'user_profile') ?>' class="marketplaces_gutter_name"><?php echo $this->user($this->marketplace->owner_id)->getTitle() ?></a>
		<ul class='marketplaces_gutter_options'>

		  <?php if ($this->marketplace->owner_id == $this->viewer->getIdentity()||$this->can_edit):?>
			<li>
			  <a href='<?php echo $this->url(array('marketplace_id' => $this->marketplace->marketplace_id), 'marketplace_edit', true) ?>' class='buttonlink icon_marketplace_edit'><?php echo $this->translate('Edit This Listing');?></a>
			</li>
			<?php if( $this->allowed_upload ): ?>
			<li>
			  <?php echo $this->htmlLink(array(
				  'route' => 'marketplace_extended',
				  'controller' => 'photo',
				  'action' => 'upload',
				  'subject' => $this->marketplace->getGuid(),
				), $this->translate('Add Photos'), array(
				  'class' => 'buttonlink icon_marketplace_photo_new'
			  )) ?>
			</li>
			<?php endif; ?>
			<li>
			  <a href='<?php echo $this->url(array('marketplace_id' => $this->marketplace->marketplace_id), 'marketplace_delete', true) ?>' class='buttonlink icon_marketplace_delete'><?php echo $this->translate('Delete Listing');?></a>
			</li>
		  <?php endif; ?>
            <?php if( $this->viewer()->getIdentity() ): ?>
				<li>
					  <?php echo $this->htmlLink(array(
						'route' => 'default',
						'module' => 'core',
						'controller' => 'report',
						'action' => 'create',
						'subject' => $this->marketplace->getGuid(),
						'format' => 'smoothbox',
					  ), $this->translate('Report'), array(
						'class' => 'buttonlink icon_report smoothbox',
					  )) ?>
				</li>
            <?php endif; ?>
		</ul>

		<form id='filter_form' class='global_form_box' method='post' action='<?php echo $this->url(array('module' => 'marketplace', 'controller' => 'index', 'action' => 'index'), 'default', true) ?>' style='display: none;'>
		  <input type="hidden" id="tag" name="tag" value=""/>
		  <input type="hidden" id="category" name="category" value=""/>
		  <input type="hidden" id="start_date" name="start_date" value="<?php if ($this->start_date) echo $this->start_date;?>"/>
		  <input type="hidden" id="end_date" name="end_date" value="<?php if ($this->end_date) echo $this->end_date;?>"/>
		</form>

		<?php if (count($this->userCategories )):?>
		  <h4><?php echo $this->translate('Categories');?></h4>
		  <ul>
			  <li> <a href='javascript:void(0);' onclick='javascript:categoryAction(0);'><?php echo $this->translate('All Categories');?></a></li>
			  <?php foreach ($this->userCategories as $category): ?>
				<li> <a href='javascript:void(0);' onclick='javascript:categoryAction(<?php echo $category->category_id?>);'><?php echo $this->translate($category->category_name) ?></a></li>
			  <?php endforeach; ?>
		  </ul>

		  
		<?php endif; ?>

		<?php
		$this->tagstring = "";
		if (count($this->userTags )){
		  foreach ($this->userTags as $tag){
			if (!empty($tag->text)){
			  $this->tagstring .= " <a href='javascript:void(0);'onclick='javascript:tagAction({$tag->tag_id})' >#$tag->text</a> ";
			}
		  }
		}
		?>

		<?php if ($this->tagstring ):?>
		  <h4><?php echo $this->translate('%1$s\'s Tags', $this->user($this->marketplace->owner_id)->getTitle())?></h4>
		  <ul>
			<?php echo $this->tagstring;?>
		  </ul>
		<?php endif; ?>

	  

	  </div>
	</div>

	<div class='layout_middle'>
		<div class='layout_middle_left'>
		  <h2>
			<?php echo $this->translate('%1$s\'s Marketplace Listing', $this->htmlLink($this->owner->getHref(), $this->owner->getTitle()))?>
		  </h2>

		  <?php if($this->coupon_error == 1): ?>
			<div class="tip">
			  <span>
				<?php echo $this->translate('Wrong coupon code');?>
			  </span>
			</div>
		  <?php elseif($this->coupon_error == 2): ?>
			<div class="tip">
			  <span>
				<?php echo $this->translate('Coupon have been succesfully activated');?>
			  </span>
			</div>
		  <?php endif; ?>

		  <ul class='marketplaces_entrylist'>
			<li>
				<ul class='marketplace_thumbs'>
          <?php $i = 0; ?> 
          <?php $colInRow = 8; ?> 
				  <?php foreach( $this->paginator as $photo ): ?>
					<?php if($this->marketplace->photo_id != $photo->file_id):?>
					  <?php if ($i % $colInRow == 0): ?><li> <?php endif;?>
            <?php if($photo->getPhotoUrl()) : ?>
						  <a  href="<?=$photo->getPhotoUrl()?>" class="smoothbox11" rel="milkbox[gall1]" title="<?=$this->marketplace->getTitle()?>">
                <?=$this->itemPhoto($photo, 'thumb.icon', array('id' => 'media_photo', 'style'=>'text-align: center; width: 100px;'))?>
              </a>
            <?php endif;?>

					  <?php if( ($i++ % $colInRow == $colInRow - 1) ) echo "</li>"; ?> 
					<?php endif; ?>

				  <?php endforeach;?>
          <?php if( ($i % $colInRow != 0) ) echo "</li>"; ?>
				</ul>

			  <h3>
				<?php echo $this->marketplace->getTitle() ?>
			  </h3>

			  <?php if ($this->marketplace->closed == 1):?>
				<br />
				<div class="tip">
				  <span>
					<?php echo $this->translate('This marketplace listing has been closed by the poster.');?>
				  </span>
				</div>
				<br/>
			  <?php endif; ?>

			  <div class="marketplace_entrylist_entry_date">
				<?php echo $this->translate('Posted by');?> <?php echo $this->htmlLink($this->marketplace->getParent(), $this->marketplace->getParent()->getTitle()) ?>
				<?php echo $this->timestamp($this->marketplace->creation_date) ?>
				<?php if ($this->category):?>- <?php echo $this->translate('Filed in');?> <a href='javascript:void(0);' onclick='javascript:categoryAction(<?php echo $this->category->category_id?>);'><?php echo $this->translate($this->category->category_name) ?></a> <?php endif; ?>
			  </div>

				<?php $this_script = 'http://'.$_SERVER['HTTP_HOST']; ?>

				<div class="marketplace_entrylist_entry_body">
					<?php echo nl2br($this->marketplace->body) ?>
				</div>


				
			</li>
		  <?php echo $this->action("list", "comment", "core", array("type"=>"marketplace", "id"=>$this->marketplace->getIdentity())) ?>
		</div>
		  <div class="marketplace_fieldvalueloop">
			<?php echo $this->fieldValueLoop($this->marketplace, $this->fieldStructure) ?>
			<br />
			<ul style="margin:15px 0;">
				<li>
					<span style="font-size:13px;"><?=$this->translate("Price")?>:</span>
					<span style="font-size:13px;">$<?=number_format( (($this->marketplace->price - $this->discount_sum) >= 0?$this->marketplace->price - $this->discount_sum:0), 2)?></span>
				</li>
				<?php if(Engine_Api::_()->marketplace()->upsIsActive()): ?>
					<?php if($this->product_shipping_fee): ?>
						<li>
							<span style="font-size:13px;"><?=$this->translate("Shipping Fee")?>:</span>
							<span style="font-size:13px;">$<?=number_format($this->product_shipping_fee, 2)?></span>
						</li>
					<?php endif; ?>
				<?php endif; ?>
        <?php if( $inspectionEnable ) : ?>
          <li>
            <input type="checkbox" class="checkbox" name="inspection_option" id="inspection_option" onchange="setInspection(this);"/>
					  <span style="font-size:13px;"><?=$this->translate("Inspection")?>:</span>
					  <span style="font-size:13px;" id='inspection_fee'>$<?=number_format(0, 2)?></span>
				  </li>
        <?php endif; ?>
        <li>
					<span style="font-size:18px;"><?=$this->translate("Total Price")?>:</span>
					<span style="font-size:18px;" id='total_price'>$<?=number_format($this->total_amount, 2)?></span>
				</li>
			</ul>
			<?php if(Engine_Api::_()->marketplace()->cartIsActive()/* and $this->viewer()->getIdentity()*/ ): ?>
				<br /><br />
				<?php if(!empty($this->already_in_cart)): ?>
					<div class="tip">
					  <span>
						<?php echo $this->translate('Already in my cart');?>
					  </span>
					</div>
				<?php endif; ?>
				<div style="text-align: left;margin-bottom: 10px;">
					<?=$this->htmlLink(array('route' => 'marketplace_general', 'action' => 'addtocart', 'marketplace_id' => $this->marketplace->getIdentity()), 'Add to Cart', array('class' => 'add_to_cart smoothbox', 'id' => 'add_to_cart'))?>
				</div>
			<?php else: ?>
				<?php if(Engine_Api::_()->marketplace()->couponIsActive()): ?>
					<br /><br />
					<?php if(empty($this->coupon_res)): ?>
						<div style="margin: 20px 0;">
							<form method="post">
								<?=$this->translate('Enter a coupon code: ')?> <input name="coupon_code" value="" style="border: 1px solid #000;" />
								<br/><br/>
								<button id="submit" type="submit" name="submit"><?=$this->translate('Apply')?></button>
							</form>
						</div>
					<?php elseif(!empty($this->discount)): ?>
						<?=$this->translate('Your discount: ').$this->discount.'%';?>
					<?php endif; ?>
				<?php endif; ?>
				<br /><br />
			<?php endif; ?>

		  </div>
	</div>
</div>
