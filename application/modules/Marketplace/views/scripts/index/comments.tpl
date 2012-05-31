<?php if( !$this->marketplace): ?>
  <?php echo $this->translate('The marketplace you are looking for does not exist or has been deleted.');?>
  <?php return; // Do no render the rest of the script in this mode
endif; ?>

<?=$this->content()->renderWidget('marketplace.topbanner', array('pageName' => 'marketplace', 'categoryId' => $this->marketplace->category_id))?>
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
