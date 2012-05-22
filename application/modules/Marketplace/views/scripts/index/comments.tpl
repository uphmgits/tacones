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

    </div>
  </div>

	<div class='layout_middle'>
    <style>div.comments-form-container { display: block; }</style>
    <div class="comments-header">
      <div class="comments-stats">
        <span><?=$this->translate("%s comments", $this->marketplace->comment_count)?></span>

        <span><?=$this->htmlLink(array('route' => 'marketplace_entry_view', 
                                       'user_id' => $this->marketplace->owner_id, 
                                       'marketplace_id' => $this->marketplace->getIdentity()), $this->translate("back"))?>
        </span>

        <span><?=$this->htmlLink('javascript:void(0);', $this->translate('add comment'), array(
              "onclick" => "$('comments-form-container').show();"
            )) ?>
        </span>
      </div>
      <h3 class="comments-desc">
        <?=$this->marketplace->getTitle()?> $<?=number_format($this->marketplace->price, 2)?>
      </h3>
    </div>
	  <?=$this->action("list", "comment", "core", array("type"=>"marketplace", "id"=>$this->marketplace->getIdentity()))?>

    <div class="comments-stats">
      <span>
        <?=$this->htmlLink('javascript:void(0);', $this->translate('view more comments'), array(
              "style" => "padding-left: 10px;",
              'onclick' => 'en4.core.comments.loadComments("'.$this->marketplace->getType().'", "'.$this->marketplace->getIdentity().'", "1")'
            ))?>
      <span>
    </div>

	</div>

</div>
