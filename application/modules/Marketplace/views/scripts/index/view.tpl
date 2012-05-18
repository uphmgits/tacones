<?php if( !$this->marketplace): ?>
  <?php echo $this->translate('The marketplace you are looking for does not exist or has been deleted.');?>
  <?php return; // Do no render the rest of the script in this mode
endif; ?>

<?php $likeCount = $this->marketplace->getLikeCount(); ?>

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


<script type="text/javascript">
  var pageAction =function(page){
    $('page').value = page;
    $('filter_form').submit();
  }

  var likeInProgress = false;

  en4.core.runonce.add(function(){
    $$('#filter_form input[type=text]').each(function(f) {
        if (f.value == '' && f.id.match(/\min$/)) {
            new OverText(f, {'textOverride':'min','element':'span'});
            
        }
        if (f.value == '' && f.id.match(/\max$/)) {
            new OverText(f, {'textOverride':'max','element':'span'});
            
        }
    });

  });

  function marketplaceLike( mid ) {
      if( likeInProgress ) return;
      var url = '<?php echo $this->url(array('module' => 'marketplace', 'controller' => 'index', 'action' => 'ajaxlike'),'default', true) ?>';
      var counterEl = $('marketplacelike_' + mid);
      likeInProgress = true;
      var request = new Request.HTML({
        url : url,
        data : {
          format : 'html',
          'marketplace_id' : mid
        },
        onSuccess : function(responseTree, responseElements, responseHTML, responseJavaScript) {
            var dx = parseInt(counterEl.get('param'));
            if( dx ) {
              $('marketplacelike_' + mid).innerHTML = parseInt($('marketplacelike_' + mid).innerHTML) + dx;
              counterEl.set('param', -dx);
            }
            likeInProgress = false;
        }
      });
      request.send();
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

<div class="header_img_sd_home" style="display:block;">
  <?php
    $mycatid = $this->mycatid; 
    if($mycatid=='1') $myimg='marketplace_shoes_header.jpg';
    elseif($mycatid=='3') $myimg='marketplace_cloth_header.jpg';
    elseif($mycatid=='5') $myimg='marketplace_bags_header.jpg';
    elseif($mycatid=='8') $myimg='marketplace_accessories_header.jpg';
    elseif($mycatid=='13') $myimg='marketplace_brands_header.jpg';
    else $myimg='marketplace_home_header.jpg';
  
    if($myimg=='marketplace_home_header.jpg') echo "<a href='#'><img src='/public/header-imgs/$myimg'/></a>";
    else echo "<img src='/public/header-imgs/$myimg'/>";
  ?>
</div>
<br/>

<div class='layout_common'>
	<div class='layout_left'>
	  <div class='marketplaces_gutter'>
		
      <div class="quicklinks">
        <div id="navigation">
          <?php Engine_Api::_()->marketplace()->tree_print_category( $this->a_tree, $this->urls, $this->marketplace->category_id ); ?>
        </div>
      </div>
             
      <form id='filter_form' class='global_form_box' method='post' action='<?php echo $this->url(array('module' => 'marketplace', 'controller' => 'index', 'action' => 'index'), 'default', true) ?>' style='display: none;'>
		    <input type="hidden" id="tag" name="tag" value=""/>
		    <input type="hidden" id="category" name="category" value=""/>
		    <input type="hidden" id="start_date" name="start_date" value="<?php if ($this->start_date) echo $this->start_date;?>"/>
		    <input type="hidden" id="end_date" name="end_date" value="<?php if ($this->end_date) echo $this->end_date;?>"/>
		  </form>

      <?php /* if (count($this->userCategories )):?>
		    <h4><?php echo $this->translate('Categories');?></h4>
		    <ul>
			    <li> <a href='javascript:void(0);' onclick='javascript:categoryAction(0);'><?php echo $this->translate('All Categories');?></a></li>
			    <?php foreach ($this->userCategories as $category): ?>
				  <li> <a href='javascript:void(0);' onclick='javascript:categoryAction(<?php echo $category->category_id?>);'><?php echo $this->translate($category->category_name) ?></a></li>
			    <?php endforeach; ?>
		    </ul>		  
		  <?php endif; */ ?>

		  <?php
		    $this->tagstring = "";
		    if (count($this->userTags )) {
		      foreach ($this->userTags as $tag) {
			      if (!empty($tag->text)){
			        $this->tagstring .= " <a href='javascript:void(0);'onclick='javascript:tagAction({$tag->tag_id})' >#$tag->text</a> ";
			      }
		      }
		    }
		  ?>

		  <?php if ($this->tagstring ):?>
		    <h4><?=$this->translate('%1$s\'s Tags', $this->user($this->marketplace->owner_id)->getTitle())?></h4>
		    <ul>
			    <?=$this->tagstring?>
		    </ul>
		  <?php endif; ?>

    </div>

    <?php if( $this->likeList->getTotalItemCount() ) : ?>
    
      <ul class="likelist quicklinks">
        <li><h3><?=$this->translate('Item loved by')?></h3></li>
        <?php foreach( $this->likeList as $item ): ?>
          <li>
              <?php $owner = $item->getOwner(); ?>
              <table width=100%>
                <tr>
                  <td class="likelist-photo"><?=$this->htmlLink($owner->getHref(), $this->itemPhoto($owner, 'thumb.icon'))?></td>
                  <td><?=$owner?></td>
                </tr>
              </table>

          </li>
        <?php endforeach; ?>
        <li class="see-more-likes"><?=$this->translate('%s see all', $likeCount)?></li>
      </ul>

    <?php endif; ?>
  
    <?php /*if( $this->viewer()->getIdentity() ) : ?>
      <ul class='marketplaces_gutter_options quicklinks'>

		    <?php if( $this->marketplace->owner_id == $this->viewer->getIdentity() or $this->can_edit ) : ?>
			    <li>
			      <a href='<?php echo $this->url(array('marketplace_id' => $this->marketplace->marketplace_id), 'marketplace_edit', true) ?>' class='buttonlink icon_marketplace_edit'><?php echo $this->translate('Edit This Listing');?></a>
			    </li>
			    <?php if( $this->allowed_upload ) : ?>
			    <li>
			      <?=$this->htmlLink(array(
				      'route' => 'marketplace_extended',
				      'controller' => 'photo',
				      'action' => 'upload',
				      'subject' => $this->marketplace->getGuid(),
				    ), $this->translate('Add Photos'), array(
				      'class' => 'buttonlink icon_marketplace_photo_new'
			      ))?>
			    </li>
			    <?php endif; ?>
			    <li>
			      <a href='<?php echo $this->url(array('marketplace_id' => $this->marketplace->marketplace_id), 'marketplace_delete', true) ?>' class='buttonlink icon_marketplace_delete'><?php echo $this->translate('Delete Listing');?></a>
			    </li>
        <?php endif; ?>

			  <li>
				    <?=$this->htmlLink(array(
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
      </ul>
    <?php endif;*/ ?>     
  </div>

	<div class='layout_middle'>
		<div class='layout_middle_left'>
		  <div class="layout_middle_left_top" style="position: relative; overflow: hidden;">
        <div class='marketplaces_gutter_photo'>
          <?php if( $this->main_photo ) : ?>
              <?=$this->htmlLink($this->main_photo->getPhotoUrl(), $this->itemPhoto($this->main_photo, 'normal'), array('class'=>'smoothbox11', 'rel'=>'milkbox[gall1]', 'title'=> $this->marketplace->getTitle()))?>
          <?php endif; ?>
        </div> 
                
        <div class="marketplace_fieldvalueloop">
          <ul class='marketplaces_entrylist'>
			      <li>
				      <ul class='marketplace_thumbs'>
                <?php $i = 0; ?> 
                <?php $colInRow = 4; ?> 
				        <?php foreach( $this->paginator as $photo ): ?>

					        <?php if( $this->marketplace->photo_id != $photo->file_id ) : ?>
					          <?php if ($i % $colInRow == 0): ?><li><?php endif;?>
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
				    </li>
          </ul>
            

          <table class="product-title-right">
            <tbody><tr>
              <td><?=$this->marketplace->getTitle()?></td>
              <td width="70">$<?=number_format( (($this->marketplace->price - $this->discount_sum) >= 0?$this->marketplace->price - $this->discount_sum:0), 2)?></td>
            </tr></tbody>
          </table>
           
          <div class="cart-like-wishlist">
           
            <?php if( $this->viewer()->getIdentity() ) : ?>
					      <?=$this->htmlLink(array('route' => 'marketplace_general', 'action' => 'addtocart', 'marketplace_id' => $this->marketplace->getIdentity()), 'purchase ', array('class' => 'add_to_cart smoothbox', 'id' => 'add_to_cart'))?>
            <?php else : ?>
              <div id='login-popup' style="d">
                <form name='frm_login_popup' action="<?=$this->loginForm->getAction()?>" method="post">
                  <div class='close-popup' onclick="$('login-popup').hide();"></div>
                  <h3><?=$this->translate('Login')?></h3>

                  <?php $this->loginForm->email->setLabel('name'); ?>
                  <?=$this->loginForm->email->render($this);?>

                  <?php $this->loginForm->email->setLabel('password'); ?>
                  <?=$this->loginForm->password->render($this);?>

                  <div id="button-wrapper" class="form-wrapper">
                    <div id="button-element" class="form-element">
                      <a href="javascript:void(0);" onclick="document.frm_login_popup.submit();"><?=$this->translate('Send')?></a>
                    </div>
                  </div>
                  <?=$this->loginForm->forgot->render($this);?>

                  <div id="button-wrapper" class="form-wrapper">
                    <div id="button-element" class="form-element">
                      <a href="<?=$this->url(array(), 'user_signup')?>"><?=$this->translate('or sign up now')?></a>
                    </div>
                  </div>

                  <?php $goToUrl = $this->url(array('user_id' => $this->marketplace->owner_id, 'marketplace_id' => $this->marketplace->getIdentity() ), 'marketplace_entry_view'); ?>
                  <?php if ($this->loginForm->facebook) : ?>
                    <div id="facebook-wrapper" class="form-wrapper">
                      <div id="facebook-element" class="form-element">
                        <a href="<?=Zend_Controller_Front::getInstance()->getRouter()->assemble(array('module' => 'user', 'controller' => 'auth','action' => 'facebook', 'gotourl' => $goToUrl), 'default', true);?>">
                          <img src="/application/modules/User/externals/images/facebook-sign-in-popup.gif" border="0" alt="Connect with Facebook">
                        </a>
                      </div>
                    </div>
                  <?php endif; ?>

                  <input type="hidden" name="gotourl" value="<?=$goToUrl?>" />
                <form>
              </div>
              <?//=$this->htmlLink(array('route' => 'user_loginpopup', 'format' => 'smoothbox'), 'purchase ', array('class' => 'add_to_cart smoothbox', 'id' => 'add_to_cart'))?>
              <?=$this->htmlLink('javascript:void(0);', 'purchase ', array('class' => 'add_to_cart', 'id' => 'add_to_cart', 'onclick' => '$("login-popup").show();'))?>
            <?php endif;?>
				
            &nbsp;<a href="#" class="wishlist"><?=$this->translate('wishlist')?></a>&nbsp;&nbsp;
            
            <div class="love-info">
                <?php if( $this->viewer()->getIdentity() ) : ?>
                  <span class="like" id="2marketplacelike_<?=$this->marketplace->getIdentity()?>" 
                                     onclick="marketplaceLike(<?=$this->marketplace->getIdentity()?>)" 
                                     param="<?=$this->marketplace->isLike($this->viewer()) ? '-1' : '1'?>"
                  >
                      <?//=$likeCount?>
                      <?=$this->translate('item')?>
                  </span>
                <?php else : ?>
                  <span class="like"><?//=$likeCount?><?=$this->translate('item')?></span>
                <?php endif;?>
            </div>

            <div class="pinterest-button" style="display: inline-block;">
              <a href="http://pinterest.com/pin/create/button/?url=http%3A%2F%2F<?=urlencode($_SERVER['HTTP_HOST'] . $this->url(array('user_id' => $this->marketplace->owner_id, 'marketplace_id' => $this->marketplace->getIdentity() ), 'marketplace_entry_view'))?>" class="pin-it-button" count-layout="none">
                <img border="0" src="//assets.pinterest.com/images/PinExt.png" title="<?=$this->translate('Pin It')?>" />
              </a>
            </div>       
          </div> <!--cart-like-wishlist-->
            
          <br />
           
          <table class="product-details" width="100%">
            <tbody><tr>
              <td width="50%" style="padding-right: 5px;"><?=$this->fieldValueLoop($this->marketplace, $this->fieldStructure)?></td>
              <td width="50%"><?=$this->fieldValueLoop($this->marketplace, $this->fieldStructure)?></td>
            </tr></tbody>
          </table>   
          <?//=$this->fieldValueLoop($this->marketplace, $this->fieldStructure)?>
			
          <br />
            
          <div class="love-info">
            <?php if( $this->viewer()->getIdentity() ) : ?>
              <span class="like" id="marketplacelike_<?=$this->marketplace->getIdentity()?>" 
                                 onclick="marketplaceLike(<?=$this->marketplace->getIdentity()?>)" 
                                 param="<?=$this->marketplace->isLike($this->viewer()) ? '-1' : '1'?>"
              >
                  <?=$likeCount?>
              </span>
            <?php else : ?>
              <span class="like"><?=$likeCount?></span>
            <?php endif;?>
            <span class="comment"><?=$this->marketplace->comment_count?></span>
          </div>
            
          <?php /*
		      <ul style="margin:5px 0;">
			      <li>
				      <span style="font-size:14px;"><?=$this->translate("Price")?>:</span>
				      <span style="font-size:14px;">$<?=number_format( (($this->marketplace->price - $this->discount_sum) >= 0?$this->marketplace->price - $this->discount_sum:0), 2)?></span>
			      </li>
			      <?php if(Engine_Api::_()->marketplace()->upsIsActive()): ?>
				      <?php if($this->product_shipping_fee): ?>
					      <li>
						      <span style="font-size:14px;"><?=$this->translate("Shipping Fee")?>:</span>
						      <span style="font-size:14px;">$<?=number_format($this->product_shipping_fee, 2)?></span>
					      </li>
				      <?php endif; ?>
			      <?php endif; ?>
            <?php if( $inspectionEnable ) : ?>
              <li>
                <input type="checkbox" class="checkbox" name="inspection_option" id="inspection_option" onchange="setInspection(this);"/>
				        <span style="font-size:14px;"><?=$this->translate("Inspection")?>:</span>
				        <span style="font-size:14px; margin-left:-18px;" id='inspection_fee'>$<?=number_format(0, 2)?></span>
			        </li>
            <?php endif; ?>
            <li>
				      <span style="font-size:16px;"><?=$this->translate("Total Price")?>:</span>
				      <span style="font-size:16px;" id='total_price'>$<?=number_format($this->total_amount, 2)?></span>
			      </li>
		      </ul>
          */?>
  
			    <?php if(Engine_Api::_()->marketplace()->cartIsActive() ) : ?>
				      <br /><br />
                    
			        <?php if(!empty($this->already_in_cart)): ?>
				        <div class="tip">
				          <span>
					        <?=$this->translate('Already in my cart')?>
				          </span>
				        </div>
			        <?php endif; ?>

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

		  </div> <!-- marketplace_fieldvalueloop -->
            
    </div> <!-- layout_middle_left_top -->
          

	  <?php if( $this->coupon_error == 1 ) : ?>
			<div class="tip">
			  <span>
				<?=$this->translate('Wrong coupon code')?>
			  </span>
			</div>
		<?php elseif( $this->coupon_error == 2 ) : ?>
			<div class="tip">
			  <span>
				<?=$this->translate('Coupon have been succesfully activated')?>
			  </span>
			</div>
		<?php endif; ?>

		<?php if( $this->marketplace->closed == 1 ) : ?>
		  <br />
			<div class="tip">
			  <span>
			    <?=$this->translate('This marketplace listing has been closed by the poster.')?>
			  </span>
			</div>
			<br/>
    <?php endif; ?>
    <h3 class="product-desc"><?=$this->translate('Product Description')?></h3>
		<div class="marketplace_entrylist_entry_date">
      <div class="marketplace_thumb_icon">
			  <?=$this->htmlLink($this->owner->getHref(), $this->itemPhoto($this->owner,'thumb.profile'))?>
        <?=$this->htmlLink($this->marketplace->getParent(), $this->marketplace->getParent()->getTitle())?>

        <?php
            $total_review = Engine_Api::_()->review()->getUserReviewCount($this->owner);
            $average_rating = Engine_Api::_()->review()->getUserAverageRating($this->owner);
            $total_recommend = Engine_Api::_()->review()->getUserRecommendCount($this->owner);	
        ?>
        <div class="review_profile_rating">
          <a href="<?=$this->url(array('id'=>$this->owner->getIdentity()), 'review_user', true)?>">
            <span class="review_rating_star_small"><span style="width: <?=$average_rating * 20?>%"></span></span>
          </a>
          <div class="review_summary_average">
            <?php if ($total_review): ?>
              <?php $text_num_reviews = $this->htmlLink(array('route'=>'review_user','id'=>$this->owner->getIdentity()), $this->translate(array("review %s","reviews %s", $total_review), $total_review)); ?> 
              <?=$text_num_reviews?>
            <?php else: ?>
              <?=$this->translate('No Rated Yet.')?>
            <?php endif; ?>
          </div>
        </div>
      </div>

      <div class="marketplace_product_desc">
        <h2><?=$this->marketplace->getTitle()?></h2>
        <div class="marketplace_entrylist_entry_body">
          <?=nl2br($this->marketplace->body)?>
        </div>
      </div>
      <div class="clear"></div>
    </div>

    <?php $this_script = 'http://'.$_SERVER['HTTP_HOST']; ?>

    <div class="comments-header">
      <div class="comments-stats">
        <span><?=$this->translate("%s comments", $this->marketplace->comment_count)?></span>

        <span><?=$this->htmlLink(array('route' => 'marketplace_comments', 'marketplace_id' => $this->marketplace->getIdentity() ), 
                           $this->translate("all comments")
                          )?>
        </span>
      </div>
      <h3 class="comments-desc"><?=$this->translate("Comments")?></h3>
    </div>
                    
	  <?=$this->action("list", "comment", "core", array("type"=>"marketplace", "id"=>$this->marketplace->getIdentity()))?>
         
          
		</div>
		  
	</div>
</div>
