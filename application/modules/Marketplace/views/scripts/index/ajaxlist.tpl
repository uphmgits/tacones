<?php $viewer = $this->viewer(); ?>
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
        <span><?=$this->translate('Price')?>:</span>&nbsp;
        <span>$<?=$item->price + Engine_Api::_()->marketplace()->getInspectionFee($item->price)?></span>
        &nbsp;&nbsp;
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

    </div>
  </li>
<?php endforeach; ?>
