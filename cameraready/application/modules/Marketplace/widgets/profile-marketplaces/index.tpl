<?php
/**
 * 
 *
 * @category   Application_Extensions
 * @package    Marketplace
 * @copyright  Copyright 2010 
 * * 
 * @version    $Id: index.tpl 7244 2010-09-01 01:49:53Z john $
 * 
 */
?>

<?php foreach( $this->cats as $cat ): ?>
	<?php
		$marketplace_items = Engine_Api::_()->marketplace()->getUserItemsFromCategories($cat['owner_id'],$cat['category_id'])->toArray();
	?>
	<?php if( $marketplace_items ): ?>
		<h4><?=$cat['category_name']?></h4>
		<ul class="marketplaces_profile_tab">
		  <?php foreach( $marketplace_items as $mi ): ?>
		  <?php $item = Engine_Api::_()->getItem('marketplace', $mi['marketplace_id']); ?>
			<li>
			  <div class='marketplaces_profile_tab_photo'>
				<?php echo $this->htmlLink($item->getHref(), $this->itemPhoto($item, 'thumb.normal')) ?>
			  </div>
			  <div class='marketplaces_profile_tab_info'>
				<div class='marketplaces_profile_tab_title'>
				  <?php echo $this->htmlLink($item->getHref(), $item->getTitle()) ?>
				  <?php if( $item->closed ): ?>
					<img src='application/modules/Marketplace/externals/images/close.png'/>
				  <?php endif;?>
				</div>
				<div class='marketplaces_browse_info_date'>
				  <?php echo $this->timestamp(strtotime($item->creation_date)) ?>
				</div>
				<div class='marketplaces_browse_info_price'>
				  <span style="font-size:14px;font-weight:bold;">Price:</span> <span style="font-size:14px;font-weight:bold;">$<?php echo $item->price; ?></span>
				</div>
				<div class='marketplaces_browse_info_blurb'>
				  <?php
					// Not mbstring compat
					echo substr(strip_tags($item->body), 0, 350); if (strlen($item->body)>349) echo $this->translate("...");
				  ?>
				</div>
			  </div>
			</li>
		  <?php endforeach; ?>
		</ul>
	<?php endif; ?>
<?php endforeach; ?>
