<?php
/**
 * 
 *
 * @category   Application_Extensions
 * @package    Marketplace
 * @copyright  Copyright 2010 
 * * 
 * @version    $Id: create.tpl 7244 2010-09-01 01:49:53Z john $
 * 
 */
?>
<style>
    .marketplace-preview-miniphoto_item {
        display: inline-block;
        *display: inline;
        max-height: 100px;
        width: 102px;
        overflow: hidden;
        vertical-align: top;
        border: 1px solid #999;
        line-height: 0;
    }
    .marketplace-preview-miniphoto_item + .marketplace-preview-miniphoto_item {
        margin-left: 20px;
    }
    .marketplace-preview-miniphoto_item img {
        width: 100px;
    }
    .marketplace-preview-mainphoto img {
        width: 160px;
    }
    .marketplace-create-container > table {
        table-layout: fixed;
    }
    .marketplace-preview-content td {
        vertical-align: top;
        padding: 0 20px 20px 0;
    }
    
    .marketplace-preview-options {
        overflow: hidden;
        margin-top: 10px;
    }
    .marketplace-preview-options a {
        float: right;
        padding-left: 20px;
        color: #39c;
        text-transform: uppercase;
    }
   
</style>


<div class='layout_common'>
	<div class='layout_left' style="padding: 0;">
	  <div class='marketplaces_gutter'>
		
      <div class="quicklinks">
        <div id="navigation">
          <?php Engine_Api::_()->marketplace()->tree_print_category( $this->a_tree, $this->urls, $this->marketplace->category_id ); ?>
        </div>
      </div>

    </div>
  </div>

	<div class='layout_middle'>
    <div class="marketplace-create-container">
      <h2><?=$this->translate('Upload Complete')?></h2>
      <table class="marketplace-preview-content" width="100%">
          <tr>
              <td style="width: 180px">
                <div class="marketplace-preview-mainphoto">
                  <?=$this->itemPhoto($this->marketplace, 'normal')?></td>
                </div>
              <td>
                <div class="marketplace-preview-miniphoto">
                  <?php $i = 0; ?>
                  <?php foreach( $this->paginator as $photo ): ?>
                    <?php if( $photo->getIdentity() == $this->marketplace->photo_id ) continue; ?>
                    <?php $i++ ?>
                    <?php $name = "photo" . $i; ?>
                      <div class="marketplace-preview-miniphoto_item">
                        <?=$this->itemPhoto($photo, 'thumb.normal')?>
                      </div>
                    <?php if( $i == 4 ) break; ?>
                  <?php endforeach; ?>
                </div>
              </td>
          </tr>
          <tr>
              <td>
                <h2 style="word-wrap:break-word;"><?=$this->marketplace->getTitle()?> $<?=$this->marketplace->price?></h2>
                <?=$this->fieldValueLoop($this->marketplace, $this->fieldStructure)?>
              </td>
              <td>                
                <div style="word-wrap:break-word;">
                    <?=$this->marketplace->body?>
                <div>
              </td>
          </tr>
      </table>  
    </div>
    
    <div class="marketplace-preview-options">
        <?=$this->htmlLink( array('route' => 'marketplace_entry_view', 
                                    'marketplace_id' => $this->marketplace->getIdentity(),
                                    'user_id' => $this->marketplace->owner_id),
                            $this->translate('Back to Marketplace')
                          )?>
        <?=$this->htmlLink( array('route' => 'marketplace_create'), 
                            $this->translate('Post Another Item')
                          )?>
        <?=$this->htmlLink( array('route' => 'marketplace_edit', 
                                    'marketplace_id' => $this->marketplace->getIdentity(),
                                    'category' => 0),
                            $this->translate('Edit')
                          )?>
    </div>

  </div>
</div>
