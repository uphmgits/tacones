
<script type="text/javascript">
  var pageAction =function(page){
    $('page').value = page;
    $('filter_form').submit();
  }

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
</script>

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
  <?php echo $this->form->render($this) ?>
  <?php if( $this->can_create): ?>
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
  <?php if (($this->current_count >= $this->quota) && !empty($this->quota)):?>
    <div class="tip">
      <span>
        <?php echo $this->translate('You have already created the maximum number of listings allowed. If you would like to create a new listing, please delete an old one first.');?>
      </span>
    </div>
    <br/>
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
              <h3>
                <?php echo $this->htmlLink($item->getHref(), $item->getTitle()) ?>
                <?php if( $item->closed ): ?>
                  <img alt="close" src='application/modules/Marketplace/externals/images/close.png'/>
                <?php endif;?>
              </h3>
            </div>
            <div class='marketplaces_browse_info_blurb'>
              <span style="font-weight:bold;font-size:12px;">Price:</span> <span style="font-size:12px;">$<?php echo $item->price; ?></span>
              <br />
              <?php
                // Not mbstring compat
                echo substr(strip_tags($item->body), 0, 350); if (strlen($item->body)>349) echo "...";
              ?>
            </div>
          </div>

          <div class='marketplaces_browse_options'>
            <a href='<?php echo $this->url(array('marketplace_id' => $item->marketplace_id), 'marketplace_edit', true) ?>' class='buttonlink icon_marketplace_edit'><?php echo $this->translate('Edit Listing');?></a>
            <?php if( $this->allowed_upload ): ?>
              <?php echo $this->htmlLink(array(
                  'route' => 'marketplace_extended',
                  'controller' => 'photo',
                  'action' => 'upload',
                  'subject' => $item->getGuid(),
                ), $this->translate('Add Photos'), array(
                  'class' => 'buttonlink icon_marketplace_photo_new'
              )) ?>
            <?php endif; ?>

            <a href='<?php echo $this->url(array('marketplace_id' => $item->marketplace_id), 'marketplace_delete', true) ?>' class='buttonlink icon_marketplace_delete'><?php echo $this->translate('Delete Listing');?></a>
          </div>


        </li>
         <?php if($myi==$myrowsize) { echo "</ul><ul class='marketplaces_browse'>"; $myi = 0;} ?>               
      <?php endforeach; ?>
    </ul>

  <?php elseif($this->search): ?>
    <div class="tip">
      <span>
        <?php echo $this->translate('You do not have any marketplace listing that match your search criteria.');?>
      </span>
    </div>
  <?php else:?>
    <div class="tip">
      <span>
        <?php echo $this->translate('You do not have any marketplace listings.');?>
        <?php if ($this->can_create): ?>
          <?php echo $this->translate('Get started by <a href=\'%1$s\'>posting</a> a new listing.', $this->url(array(), 'marketplace_create'));?>
        <?php endif; ?>
      </span>
    </div>
  <?php endif; ?>
  <?php echo $this->paginationControl($this->paginator, null, array("pagination/pagination.tpl","marketplace")); ?>
</div>
