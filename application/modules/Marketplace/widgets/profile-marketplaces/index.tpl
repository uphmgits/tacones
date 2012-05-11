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
		$marketplace_items = Engine_Api::_()->marketplace()->getUserItemsFromCategories($cat['owner_id'],$cat['category_id'])->toArray(); ?>
       	<?php if( $marketplace_items ): ?> 
        		  <?php foreach( $marketplace_items as $mi ): ?>
                  	<?php $i++; ?>
                  <?php endforeach; ?>
                  
	

				
	<?php endif; ?>
<?php endforeach; ?>
<?php
 $myprodcount = $i; 

 if($myprodcount >= 17) $myrowsize=5;
      elseif($myprodcount >= 14) $mycolsize=4;
      elseif($myprodcount >= 9) $mycolsize=3;
      elseif($myprodcount >= 6) $mycolsize=2;
      else $mycolsize=1;
      
    ?>


<ul class="marketplaces_browse">
<?php $myi=0; ?>
<?php foreach( $this->cats as $cat ): ?>



	<?php
		$marketplace_items = Engine_Api::_()->marketplace()->getUserItemsFromCategories($cat['owner_id'],$cat['category_id'])->toArray();
	?>
    
	<?php if( $marketplace_items ): ?>
		<!--<h4><?=$cat['category_name']?></h4>-->
		
       
        
		  <?php foreach( $marketplace_items as $mi ): ?>
           <?php $myi++; ?>
		  <?php $item = Engine_Api::_()->getItem('marketplace', $mi['marketplace_id']); ?>
			<li>
			  <div class='marketplaces_profile_tab_photo'>
				<?php echo $this->htmlLink($item->getHref(), $this->itemPhoto($item, 'normal')) ?>
			  </div>
              
              
              
              
              
               <div class="love-info">
                  <?php if( $this->viewer()->getIdentity() ) : ?>
                    <span class="like" id="marketplacelike_<?=$item->getIdentity()?>" 
                                       onclick="marketplaceLike(<?=$item->getIdentity()?>)" 
                                       param="<?=$item->isLike($this->viewer()) ? '-1' : '1'?>"
                    >
                        <?=$item->getLikeCount()?>
                    </span>
                  <?php else : ?>
                    <span class="like"><?=$item->getLikeCount()?></span>
                  <?php endif;?>
                  <span class="comment"><?php echo $item->comment_count; ?></span>
              </div>
              
              
              
              
              
			  <div class='marketplaces_profile_tab_info'>
				<div class='marketplaces_profile_tab_title'>
				  <?php echo $this->htmlLink($item->getHref(), $item->getTitle()) ?>
				  <?php if( $item->closed ): ?>
					<img src='application/modules/Marketplace/externals/images/close.png'/>
				  <?php endif;?>
                  <span style="font-size:14px;font-weight:bold;">$<?php echo $item->price; ?></span>
				</div>
				<!--<div class='marketplaces_browse_info_date'>
				  <?php echo $this->timestamp(strtotime($item->creation_date)) ?>
				</div>-->
				<div class='marketplaces_browse_info_price'>
				  <!--<span style="font-size:14px;font-weight:bold;">Price:</span> -->
				</div>
				<div class='marketplaces_browse_info_blurb'>
				  <?php
					// Not mbstring compat
					echo substr(strip_tags($item->body), 0, 350); if (strlen($item->body)>349) echo $this->translate("...");
				  ?>
				</div>
			  </div>
			</li>
         <?php if($myi==$mycolsize) { echo "</ul><ul class='marketplaces_browse'>"; $myi = 0;} ?>             
		  
          <?php endforeach; ?>
		
	<?php endif; ?>
<?php endforeach; ?>
</ul>