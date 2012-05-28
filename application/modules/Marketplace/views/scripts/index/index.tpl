<?php $this->headLink()->prependStylesheet("{$this->baseUrl()}/externals/jqscroll/css/scrollbars.css");?>
<?php $this->headLink()->prependStylesheet("{$this->baseUrl()}/externals/jqscroll/css/scrollbars-black.css");?>

<style>
    #marketplace-content { height: 500px; }
    #global_page_marketplace-index-index ul.marketplaces_browse { position: relative; width: 916px !important; }
    #global_page_marketplace-index-index ul.marketplaces_browse > li { position: absolute; }
    #global_page_marketplace-index-index #global_content { max-width: 940px; width: auto; padding: 0 10px; }
    #global_page_marketplace-index-index .header_img_sd_home { text-align: center; }
    #global_page_marketplace-index-index .marketplaces_browse_photo > a { display: block; }
    #global_page_marketplace-index-index .marketplaces_browse_info_title > a { font-family: arial; letter-spacing: -2px }
    #global_page_marketplace-index-index .correct_font .marketplaces_browse_info_title > a{ font-family: Univers LT Std; letter-spacing: 0; }
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

  //contentEl.removeEvents('scroll').addEvent('scroll', function(event) {
      /*var scrollTop = contentEl.scrollTop;
      if( scrollTop > 0 )  {
          var tickerSize = contentEl.getSize();
          var scrollSize = contentEl.getScrollSize();
          var tmp = 0.8 * ( scrollSize.y - tickerSize.y);
          console.log("top: "+scrollTop+" - tmp: "+tmp+" - scrollWait: "+$('activity-ticker').get('params')+" - next_id: "+next_id);
          if( scrollTop > tmp && !$('activity-ticker').get('params') ) {
              $('activity-ticker').set('params', 'scrollwait');
              tickerViewMore(next_id, subject_guid);
          }
      }*/
  //});
  jQuery('#marketplace-content .scrollcontent').scroll(function() {
    //console.log($(this).scrollTop());
    alert('aa');
  });

</script>


<script type="text/javascript">

    function refreshMarketplaceList(ref) {
        var width = jQuery('.layout_common').width();
        var columnsWidth = 180;
        var columns = Math.floor(width / columnsWidth);
        $oldColumns = jQuery("ul.marketplaces_browse").attr("data-column");

        if( !$oldColumns || ($oldColumns && columns != $oldColumns) || ref ) {
        
            var columnsHeight = new Array(columns);
            for (i = 0; i < columns; i++) { columnsHeight[i] = 0; }
            
            list = jQuery("ul.marketplaces_browse > li");

            list.each(function(i){
                var colNum = i % columns;
                var element = jQuery(this);
                img = element.find('img.item_photo_marketplace');
                if( img && !img.height()) {
                    jQuery("<img/>").attr("src", jQuery(img).attr("src")).load(function() {
                        h = element.outerHeight();
                        element.addClass('correct_font');
                        element.css('top', columnsHeight[colNum] + 'px');
                        element.css('left', colNum * columnsWidth + 'px');
                        columnsHeight[colNum] += (h + 10);
                        
                        heightBlock = Math.max.apply( Math, columnsHeight );
                        jQuery('ul.marketplaces_browse').height(heightBlock);
                        jQuery('ul.marketplaces_browse').attr("data-column", columns);
                    });
                } else {
                  h = element.outerHeight();
                  element.addClass('correct_font');
                  element.css('top', columnsHeight[colNum] + 'px');
                  element.css('left', colNum * columnsWidth + 'px');
                  columnsHeight[colNum] += (h + 10);
                }
            });
            
            heightBlock = Math.max.apply( Math, columnsHeight );
            jQuery('ul.marketplaces_browse').height(heightBlock);
            jQuery('ul.marketplaces_browse').attr("data-column", columns);
        }
    }

    jQuery(document).ready(function () {
        refreshMarketplaceList();
        jQuery("#marketplace-content").scrollbars();
    });

    jQuery(window).bind('resize', function() { 
        refreshMarketplaceList();
    });
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



<div class="header_img_sd_home" style="display:block;">
<?php
 $mycatid = $this->category; 
 if($mycatid=='1') $myimg='marketplace_shoes_header.jpg';
 elseif($mycatid=='3') $myimg='marketplace_cloth_header.jpg';
 elseif($mycatid=='5') $myimg='marketplace_bags_header.jpg';
 elseif($mycatid=='8') $myimg='marketplace_accessories_header.jpg';
 elseif($mycatid=='13') $myimg='marketplace_brands_header.jpg';
 else $myimg='marketplace_home_header.jpg';
//
 if($myimg=='marketplace_home_header.jpg') echo "<a href='#'><img src='{$this->baseUrl()}/public/header-imgs/$myimg'/></a>";
  else echo "<img src='{$this->baseUrl()}/public/header-imgs/$myimg'/>"; 
 ?>
</div>



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

<br/>
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

   <?php
     $myprodcount = $this->paginator->getTotalItemCount(); 
     $y = $this->paginator->getItemCountPerPage();
     $x = ($this->paginator->getCurrentPageNumber() - 1) * $y ;
     $z = $myprodcount - $x;
     if($z  > 0){
       $myprodcount = $z;
     }    
     $columns = 5;
     $myrowsize = ceil( $myprodcount / $columns );
     $myrow = 0;
   ?>
   <?php /*
      <ul class="marketplaces_browse">
        <?php $myi=0; ?>
        <?php foreach( $this->paginator as $item ): ?>
          <?php $myi++; ?>
          <?php $marketplaceId = $item->getIdentity(); ?>
          <li>
            <div class='marketplaces_browse_photo'>
              <?php echo $this->htmlLink($item->getHref(), $this->itemPhoto($item, 'normal')) ?>
            </div>
            <div class='marketplaces_browse_info'>
              <div class='marketplaces_browse_info_title'>
                <div class="love-info">
                    <?php if( $this->viewer()->getIdentity() ) : ?>
                      <span class="like" id="marketplacelike_<?=$marketplaceId?>" 
                                         onclick="marketplaceLike(<?=$marketplaceId?>)" 
                                         param="<?=$item->isLike($this->viewer()) ? '-1' : '1'?>"
                      >
                          <?=$item->getLikeCount()?>
                      </span>
                    <?php else : ?>
                      <span class="like"><?=$item->getLikeCount()?></span>
                    <?php endif;?>
                    <span class="comment" id="comment-lips-<?=$marketplaceId?>" onclick="marketplaceComment(<?=$marketplaceId?>);">
                        <?=$item->comment_count?>
                    </span>

                    <div class="pinterest-button" style="display: inline-block;">
                      <a href="http://pinterest.com/pin/create/button/?url=http%3A%2F%2F<?=urlencode($_SERVER['HTTP_HOST'] . $this->url(array('user_id' => $item->owner_id, 'marketplace_id' => $item->getIdentity() ), 'marketplace_entry_view'))?>" class="pin-it-button" count-layout="none">
                        <img border="0" src="//assets.pinterest.com/images/PinExt.png" title="<?=$this->translate('Pin It')?>" />
                      </a>
                    </div>
                 

                    <div class="comment-container" ><?=$this->action("post", "comment", "core", array("type" => "marketplace", "id" => $marketplaceId))?></div>
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
              </div>
            </div>
          </li>
          
          <?php if($myi==$myrowsize and $myrow < $columns - 1 ) { 
              echo "</ul><ul class='marketplaces_browse'>"; 
              $myi=0; 
              $myrow++;
          } ?>        
        <?php endforeach; ?>
      </ul>
      */ ?>
      
      <?php $viewer = $this->viewer(); ?>
      <ul class="marketplaces_browse">
        <?php foreach( $this->paginator as $item ): ?>
          <?php $marketplaceId = $item->getIdentity(); ?>
          <li>
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
                    <?php else : ?>
                      <span class="like"><?=$item->getLikeCount()?></span>
                    <?php endif;?>
                    <span class="comment" id="comment-lips-<?=$marketplaceId?>" onclick="marketplaceComment(<?=$marketplaceId?>);">
                        <?=$item->comment_count?>
                    </span>
                
                    <div class="comment-container" >
                      <?php if( $viewer->getIdentity() ) : ?>
                        <?=$this->action("post", "comment", "core", array("type" => "marketplace", "id" => $marketplaceId))?>
                      <?php endif;?>
                    </div>
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
              </div>
            </div>
          </li>
        <?php endforeach; ?>
      </ul>
      <div id='update-wait' params="0"></div>;
      <div id='next-page' params="2"></div>;

      <?php echo $this->paginationControl($this->paginator); ?>
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
