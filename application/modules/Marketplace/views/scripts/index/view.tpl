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

<link rel="stylesheet" href="application/modules/Marketplace/externals/milkbox/css/milkbox/milkbox.css" type="text/css" media="screen" />

<!--
<script type="text/javascript" src="application/modules/Marketplace/externals/milkbox/js/mootools-1.2.5-core-yc.js"></script>
-->
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
  var dateAction =function(start_date, end_date){
    $('start_date').value = start_date;
    $('end_date').value = end_date;
    $('filter_form').submit();
  }
</script>

<div class='layout_common'>
	<div class='layout_right'>
	  <div class='marketplaces_gutter'>
		<div class='marketplaces_gutter_photo'>
		  <?php if($this->main_photo):?>
			  <?php echo $this->htmlLink($this->main_photo->getPhotoUrl(), $this->itemPhoto($this->main_photo, 'thumb.normal'), array('class'=>'smoothbox11', 'rel'=>'milkbox[gall1]', 'title'=> $this->marketplace->getTitle())) ?>
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
		  <ul class='marketplaces_entrylist'>
			<li>
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

				<ul class='marketplace_thumbs'>
				  <?php $i=0; foreach( $this->paginator as $photo ): ?>
					<?php if($this->marketplace->photo_id != $photo->file_id):?>
					  <?php if ($i == 0 || ($i % 5) == 0): ?><li> <?php endif;?>
						<a  href="<?php echo $photo->getPhotoUrl(); ?>" class="smoothbox11" rel="milkbox[gall1]" title="<?=$this->marketplace->getTitle()?>"><?php echo $this->htmlImage($photo->getPhotoUrl(), $this->itemPhoto($photo, 'thumb.icon'), array(
						  'id' => 'media_photo', 'style'=>'text-align: center; width: 100px;'
						)) ?></a>
					  <?php if (($i % 4) == 0 && $i!=0): ?></li> <?php endif;?>
					<?php endif;  $i++;?>
				  <?php endforeach;?>
				</ul>
				
			</li>
		  </ul>
		  <?php echo $this->action("list", "comment", "core", array("type"=>"marketplace", "id"=>$this->marketplace->getIdentity())) ?>
		</div>
		  <div class="marketplace_fieldvalueloop">
			<?php echo $this->fieldValueLoop($this->marketplace, $this->fieldStructure) ?>
			<br />
			<ul style="margin:15px 0;">
				<li>
					<span style="font-size:18px;">Price:</span>
					<span style="font-size:18px;">$<?php echo $this->marketplace->price;?></span>
				</li>
			</ul>
			<br /><br />
			<?php echo $this->paypalForm; ?>
		  </div>
	</div>
</div>
