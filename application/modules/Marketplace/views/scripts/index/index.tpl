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
 if($myimg=='marketplace_home_header.jpg') echo "<a href='#'><img src='/public/header-imgs/$myimg'/></a>";
  else echo "<img src='/public/header-imgs/$myimg'/>";
 ?>
</div>




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

<div class='layout_left'>
    <div class="quicklinks">
     <div id="navigation">
     <?php Engine_Api::_()->marketplace()->tree_print_category($this->a_tree,$this->urls,$this->category); ?>
    </div>
    </div>

    <p>&nbsp;</p>
  <?php echo $this->form->render($this) ?>

  <?php if( $this->can_create_NOT): ?>
    <div class="quicklinks">
      <ul>
        <li>
          <a href='<?php echo $this->url(array(), 'marketplace_create', true) ?>' class='buttonlink icon_marketplace_new'><?php echo $this->translate('Post New Listing');?></a>
        </li>
      </ul>
    </div>
  <?php endif; ?>
</div>

<div class='layout_middle'>
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
      if($myprodcount >= 17) $myrowsize=5;
      elseif($myprodcount >= 14) $myrowsize=4;
      elseif($myprodcount >= 9) $myrowsize=3;
      elseif($myprodcount >= 6) $myrowsize=2;
      else $myrowsize=1;
    ?>
    <ul class="marketplaces_browse">
      <?php $myi=0; ?>
      <?php foreach( $this->paginator as $item ): ?>
        <?php $myi++; ?>
        <li>
          <div class='marketplaces_browse_photo'>
            <?php echo $this->htmlLink($item->getHref(), $this->itemPhoto($item, 'normal')) ?>
          </div>
          <div class='marketplaces_browse_info'>
            <div class='marketplaces_browse_info_title'>
              <div class="love-info">
                  <?php if( $this->viewer()->getIdentity() ) : ?>
                    <span class="like" id="marketplacelike_<?=$item->getIdentity()?>" 
                                       onclick="marketplaceLike(<?=$item->getIdentity()?>)" 
                                       param="<?=$item->isLiked($this->viewer()) ? '-1' : '1'?>"
                    >
                        <?=$item->getLikesCount()?>
                    </span>
                  <?php else : ?>
                    <span class="like"><?=$item->getLikesCount()?></span>
                  <?php endif;?>
                  <span class="comment"><?php echo $item->comment_count; ?></span>
              </div>
              <br />
            
              <?php echo $this->htmlLink($item->getHref(), $item->getTitle()); ?>
               
              <?php if( $item->closed ): ?>
                <img src='application/modules/Marketplace/externals/images/close.png'/>
              <?php endif;?>
            </div>

            <div class='marketplaces_browse_info_date'></div>

            <div class='marketplaces_browse_info_blurb'>
              <span style="font-weight:bold;font-size:12px;"><?=$this->translate('Price')?>:</span> <span style="font-size:12px;">$<?php echo $item->price; ?></span>
              &nbsp;&nbsp;
             
              <?php
                // Not mbstring compat
                echo substr(strip_tags($item->body), 0, 90); if (strlen($item->body)>89) echo "...";
              ?>              
              <br />

              <span style="float:left; width:24px; padding: 5px 5px 0px 0px;">
                <?php echo $this->htmlLink($this->user($item->owner_id)->getHref(), $this->itemPhoto($this->user($item->owner_id), 'thumb.icon', $this->user($item->owner_id)->getTitle()), array('title'=>$this->user($item->owner_id)->getTitle())) ?>
              </span>
              <span style="float:left; text-align:left; overflow:hidden; margin-top: 3px; width: 130px; max-height:17px; border: none;">
                <?php // echo $this->user($item->owner_id)->getHref(); ?> see all items by
              </span>
              <span style="float:left; text-align:left; overflow:hidden; margin-top: -3px; width: 130px; max-height:16px; border: none;">
                <?php echo $this->user($item->owner_id)->getTitle(); ?>
              </span>
            </div>
          </div>
        </li>
        
        <?php if($myi==$myrowsize) { echo "</ul><ul class='marketplaces_browse'>"; $myi = 0;} ?>        
      <?php endforeach; ?>
    </ul>
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
