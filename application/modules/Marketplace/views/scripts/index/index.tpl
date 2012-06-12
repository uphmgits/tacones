<style>
    #marketplace-content { height: 500px; max-width: 970px; margin-right: -30px;}
    #marketplace-content .vscroll-correct { right: 24px;}
    #marketplace-content .hscroller { display: none !important; }
    #global_page_marketplace-index-index .login-popup { left: 0; }
    #global_page_marketplace-index-index ul.marketplaces_browse { position: relative; width: 100% !important; }
    #global_page_marketplace-index-index li.marketplaces_browse_item { position: absolute; display: none; }
    #global_page_marketplace-index-index #global_content { max-width: 940px; overflow: visible; width: auto; padding: 0 10px; }
    #global_page_marketplace-index-index .header_img_sd_home { text-align: center; }
    #global_page_marketplace-index-index .marketplaces_browse_photo a { display: block; }
    #global_page_marketplace-index-index .marketplaces_browse_info_title a { font-family: arial; letter-spacing: -2px }
    #global_page_marketplace-index-index li.correct_font { position: absolute; display: block; }
    #global_page_marketplace-index-index .correct_font .marketplaces_browse_info_title a { font-family: Univers LT Std; letter-spacing: 0; }
    #global_page_marketplace-index-index .last-column-login-popup { right: 0; left: auto }
</style>

<script type="text/javascript">
  var pageAction = function(page){
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

    $$('.login-popup').each(function(el) {
        console.log(el.get('left'));
    });

  });

  function marketplaceLike( mid ) {
      if( likeInProgress ) return;
      var url = '<?php echo $this->url(array('module' => 'marketplace', 'controller' => 'index', 'action' => 'ajaxlike'), 'default', true) ?>';
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

  function marketplaceComment( mid ) {
      var form = $('comment-form-' + mid);
      if( form ) {
          if( form.style.display ) { 
              form.style.display = '';
              form.body.focus();
          } else {
              form.style.display = 'none';
          }
      }
  }
</script>


<script type="text/javascript">

    function refreshMarketplaceList(ref) {
        var width = jQuery('#marketplace-content').width();
        var columnsWidth = 194;
        var columns = Math.floor( width / columnsWidth);
        oldColumns = jQuery("ul.marketplaces_browse").attr("data-column");
        if( width < 970 ) jQuery('#marketplace-content .vscroller').addClass('vscroll-correct');
        else jQuery('#marketplace-content .vscroller').removeClass('vscroll-correct');

        if( !oldColumns || (oldColumns && columns != oldColumns) || ref ) {
        
            var columnsHeight = new Array(columns);
            for (i = 0; i < columns; i++) { columnsHeight[i] = 0; }
            
            list = jQuery("li.marketplaces_browse_item");
            list.find('div.login-popup').removeClass('last-column-login-popup'); 

            list.each(function(i){
                var colNum = i % columns;
                var element = jQuery(this);

                popup = element.find('div.login-popup');
                if( colNum == columns - 1 ) popup.addClass('last-column-login-popup');

                img = element.find('img.item_photo_marketplace');
                if( img && !img.height()) {
                    jQuery("<img/>").attr("src", jQuery(img).attr("src")).load(function() {
                        h = element.outerHeight();
                        element.addClass('correct_font');
                        element.css('top', columnsHeight[colNum] + 'px');
                        element.css('left', colNum * columnsWidth + 'px');
                        columnsHeight[colNum] += (h + 30);
                        heightBlock = Math.max.apply( Math, columnsHeight );
                        jQuery('ul.marketplaces_browse').height(heightBlock);
                    });
                } else {
                  h = element.outerHeight();
                  element.addClass('correct_font');
                  element.css('top', columnsHeight[colNum] + 'px');
                  element.css('left', colNum * columnsWidth + 'px');
                  columnsHeight[colNum] += (h + 30);
                }
            });
            
            heightBlock = Math.max.apply( Math, columnsHeight );
            jQuery('ul.marketplaces_browse').height(heightBlock);
            jQuery('ul.marketplaces_browse').attr("data-column", columns);
        }
    }

    jQuery(document).ready(function () {
        refreshMarketplaceList();
        if( isMobile ) {
          btn = "<button type='button' onclick='viewMoreMarketplaces();'><?=$this->translate('View More')?></button>";
          jQuery('#marketplace-content').append(btn);
        } else {
          jQuery("#marketplace-content").marketplaceScrollbars("<?=$this->baseUrl()?>", <?=$this->category?>, <?=$this->brand_id?>, <?=$this->never_worn?>);
        }
    });

    jQuery(window).bind('resize', function() { 
        refreshMarketplaceList();
    });

    function viewMoreMarketplaces() {
      if( !parseInt(jQuery('#update-wait').attr('params')) ) {
        pageEl = jQuery('#next-page');
        nextPage = parseInt(pageEl.attr('params'));
        jQuery('#update-wait').attr('params', 1);
        jQuery.ajax({
            url: '<?=$this->baseUrl()?>' + '/marketplaces/ajaxlist',
            type: 'post',
            dataType: "html",
            data: { 'page':nextPage, 'category_id':<?=$this->category?>, 'brand_id':<?=$this->brand_id?>, 'neverworn':<?=$this->never_worn?> },
            success: function(data) {
                if( data ) {
                  jQuery('#marketplace-content .marketplaces_browse').append(data);
                  refreshMarketplaceList(true);
                  pageEl.attr('params', nextPage + 1);  
                }
                jQuery('#update-wait').attr('params', 0);
            }
        });
      }
    }
</script>


<style type="text/css">
.browse-separator-wrapper{
	display: none !important;
}
#done-wrapper{
	margin-top: 10px;
}
span.like:hover{
  cursor: pointer;
  text-decoration: none;
}
</style>


<?=$this->content()->renderWidget('marketplace.topbanner', array('pageName' => 'marketplace', 'categoryId' => $this->category))?>
<br/>
<?php /*
<div class="headline">
  <h2>
    <?php echo $this->translate('Marketplace Listings');?>
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
*/?>

<div class='layout_common'>

  <?php /*	
  <div class='layout_left'>
	  <div class='marketplaces_gutter'>
      <div class="quicklinks">
        <div id="navigation">
         <?php Engine_Api::_()->marketplace()->tree_print_category( $this->a_tree, $this->urls, $this->category ); ?>
        </div>
      </div>
    </div>
  </div>
  */?>

  <div class="marketplace-browse-options-container">
    <ul class="marketplace-browse-options">
        <li><?=$this->translate('%s items', $this->paginator->getTotalItemCount())?></li>
        <li><?=$this->htmlLink(array('route' => 'marketplace_browse'), $this->translate('see all'))?></li>

        <?php if( !empty($this->brandOptions) ): ?>
          <li class="brand-container">
            <?=$this->translate('brand')?>
            <ul class="brand-list">
            <?php foreach( $this->brandOptions as $option ) : ?>
              <li><?=$this->htmlLink(array('route' => 'marketplace_browse', 'category' => $this->category, 'brand_id' => $option->option_id), $option->label)?></li>
              <?php if( $option->option_id == $this->brand_id ) $brandLabel = $option->label; ?>
            <?php endforeach; ?>
            </ul>
          </li>
        <?php endif; ?>

        <li><?=$this->htmlLink(array('route' => 'marketplace_browse', 'category' => $this->category, 'neverworn' => 1), $this->translate('never worn'))?></li>
    </ul>
    <div class="filter-options">
      <?php if( $this->never_worn or $this->brand_id ) : ?>
        <?php if( $this->never_worn ) echo "<span>" . $this->translate('NEVER WORN') . "</span>"; ?>
        <?php if( $this->brand_id and $brandLabel ) echo "<span>" . $this->translate('BRAND - %s', $brandLabel) . "</span>";?>
        <?=$this->translate('filter ON')?>
      <?php else: ?>
        <?=$this->translate('filter OFF')?>
      <?php endif; ?>
    </div>
    <div style="clear:both"></div>
  </div>

  <div class='layout_middle' id="marketplace-content">
    <?php if( $this->tag ): ?>
      <h3>
        <?php echo $this->translate('Showing marketplace listings using the tag');?> #<?php echo $this->tag_text;?> <a href="<?php echo $this->url(array('module' => 'marketplace', 'controller' => 'index', 'action' => 'index'), 'default', true) ?>">(x)</a>
      </h3>
    <?php endif; ?>
    
    <?php if( $this->start_date ): ?>
      <?php foreach ($this->archive_list as $archive): ?>
        <h3>
          <?php echo $this->translate('Showing marketplace listings created on');?> <?php if ($this->start_date==$archive['date_start']) echo $archive['label']?> <a href="<?php echo $this->url(array('module' => 'marketplace', 'controller' => 'index', 'action' => 'index'), 'default', true) ?>">(x)</a>
        </h3>
      <?php endforeach; ?>
    <?php endif; ?>

    <?php if( $this->paginator->getTotalItemCount() > 0 ): ?>
      <?php $viewer = $this->viewer(); ?>
      <ul class="marketplaces_browse">
        <?php foreach( $this->paginator as $item ): ?>
          <?php $marketplaceId = $item->getIdentity(); ?>
          <li class="marketplaces_browse_item">
            <div class='marketplaces_browse_photo'>
              <?php echo $this->htmlLink($item->getHref(), $this->itemPhoto($item, 'normal')) ?>
            </div>
            <div class='marketplaces_browse_info'>

              <div class='marketplaces_browse_info_title'>
                <div class="love-info">
                    <?php if( $viewer->getIdentity() ) : ?>
                      <span class="like" id="marketplacelike_<?=$marketplaceId?>" 
                                         onclick="marketplaceLike(<?=$marketplaceId?>)" 
                                         param="<?=$item->isLike($viewer) ? '-1' : '1'?>"
                      >
                          <?=$item->getLikeCount()?>
                      </span>

                      <span class="comment" id="comment-lips-<?=$marketplaceId?>" onclick="marketplaceComment(<?=$marketplaceId?>);">
                          <?=$item->comment_count?>
                      </span>

                      <div class="comment-container" >
                        <?php if( $viewer->getIdentity() ) : ?>
                          <?=$this->action("post", "comment", "core", array("type" => "marketplace", "id" => $marketplaceId))?>
                        <?php endif;?>
                      </div>

                    <?php else : ?>
                      <span class="like" onclick="$$('.login-popup').hide(); $('login-popup-<?=$marketplaceId?>').show();">
                        <?=$item->getLikeCount()?>
                      </span>
                      <span class="comment" id="comment-lips-<?=$marketplaceId?>" onclick="$$('.login-popup').hide(); $('login-popup-<?=$marketplaceId?>').show();">
                          <?=$item->comment_count?>
                      </span>

                      <div id="login-popup-<?=$marketplaceId?>" class="login-popup">
                       <form name="frm_login_popup_<?=$marketplaceId?>" action="<?=$this->loginForm->getAction()?>" method="post">
                          <div class='close-popup' onclick="$('login-popup-<?=$marketplaceId?>').hide();"></div>
                          <h3><?=$this->translate('Login')?></h3>

                          <?php $this->loginForm->email->setLabel('name'); ?>
                          <?=$this->loginForm->email->render($this);?>

                          <?php $this->loginForm->email->setLabel('password'); ?>

                          <?=$this->loginForm->password->render($this);?>
                          <div id="button-wrapper" class="form-wrapper">
                            <div id="button-element" class="form-element">
                              <a href="javascript:void(0);" onclick="document.frm_login_popup_<?=$marketplaceId?>.submit();"><?=$this->translate('Send')?></a>
                            </div>
                          </div>
                          <?=$this->loginForm->forgot->render($this);?>

                          <div id="button-wrapper" class="form-wrapper">
                            <div id="button-element" class="form-element">
                              <a href="<?=$this->url(array(), 'user_signup')?>"><?=$this->translate('or sign up now')?></a>
                            </div>
                          </div>

                          <?php $goToUrl = $this->url(array(), 'marketplace_browse'); ?>
                          <?php if ($this->loginForm->facebook) : ?>
                            <div id="facebook-wrapper" class="form-wrapper">
                              <div id="facebook-element" class="form-element">
                                <a href="<?=Zend_Controller_Front::getInstance()->getRouter()->assemble(array('module' => 'user', 'controller' => 'auth','action' => 'facebook', 'gotourl' => $goToUrl), 'default', true);?>">
                                  <img src="<?=$this->baseUrl()?>/application/modules/User/externals/images/facebook-sign-in-popup.gif" border="0" alt="Connect with Facebook">
                                </a>
                              </div>
                            </div>
                          <?php endif; ?>

                          <input type="hidden" name="gotourl" value="<?=$goToUrl?>" />
                        </form>
                      </div>
                    <?php endif;?>
                
                </div>
                
                <?php echo $this->htmlLink($item->getHref(), $item->getTitle()); ?>
                 
                <?php if( $item->closed ): ?>
                  <img src='application/modules/Marketplace/externals/images/close.png'/>
                <?php endif;?>
              </div>

              <div class='marketplaces_browse_info_date'></div>

              <div class='marketplaces_browse_info_blurb' style="font-size: 11px; color: #999;">
                <span><?=$this->translate('Price')?>:</span> <span>$<?php echo $item->price; ?></span>
                &nbsp;&nbsp;
               
                <?php // echo substr(strip_tags($item->body), 0, 90); if (strlen($item->body)>89) echo "..."; ?>              
                <br />

                <div class="more-seller-items">
                  <div style="width:24px; padding-right: 5px">
                    <?=$this->htmlLink($this->user($item->owner_id)->getHref(), $this->itemPhoto($this->user($item->owner_id), 'thumb.icon', $this->user($item->owner_id)->getTitle()), array('title'=>$this->user($item->owner_id)->getTitle()))?>
                  </div>
        
                  <div style="width:130px;">
                    <?=$this->user($item->owner_id)->getTitle()?><br/>
                    <?=$this->htmlLink(array('route' => 'marketplace_view', 'user_id' => $item->owner_id), $this->translate('see all seller items'))?>
                  </div>
                </div> <!-- more-seller-items -->
              </div> <!-- marketplaces_browse_info_blurb -->
            </div> <!-- marketplaces_browse_info -->
          </li>
        <?php endforeach; ?>
      </ul>
      <div id='update-wait' params="0"></div>
      <div id='next-page' params="2"></div>

      <?php //echo $this->paginationControl($this->paginator); ?>
      <?php //echo $this->paginationControl($this->paginator, null, array("pagination/pagination.tpl","marketplace")); ?>

    <?php elseif( $this->category || $this->show == 2 || $this->search ):?>
      <div class="tip">
        <span>
          <?php echo $this->translate('Nobody has posted a marketplace listing with that criteria.');?>
          <?php if ($this->can_create): ?>
            <?php echo $this->translate('Be the first to %post%2$s one!', '<a href="'.$this->url(array(), 'marketplace_create').'">', '</a>'); ?>
          <?php endif; ?>
        </span>
      </div>
    <?php else:?>
      <div class="tip">
        <span>
          <?php echo $this->translate('Nobody has posted a marketplace listing yet.');?>
          <?php if ($this->can_create): ?>
            <?php echo $this->translate('Be the first to %1$spost%2$s one!', '<a href="'.$this->url(array(), 'marketplace_create').'">', '</a>'); ?>
          <?php endif; ?>
        </span>
      </div>
    <?php endif; ?>
  </div>
</div>

<div class="marketplace_browse_userlist">
  <div style="float: right">
      <?=$this->totalUsers?>
      <span style='padding-left: 10px'>
          <?=$this->htmlLink(array('route' => 'user_general'), $this->translate('see all'))?>
       </span>
  </div>
  <h2><?=$this->translate('Newest Members')?></h2>
  <table width='100%'>
  <tr>
  <?php foreach($this->userList as $user) : ?>
    <td class="marketplace_browse_userlist_item">
      <div class="marketplace_browse_userlist_photo">
        <?=$this->htmlLink($user->getHref(), "<img src='{$user->getPhotoUrl('thumb.profile')}' />")?>
      </div>
      <div class="marketplace_browse_userlist_info">
        <?=$user->getTitle()?>
      </div>
    </td>
  <?php endforeach; ?>
  </tr>
  </table>
</div>
