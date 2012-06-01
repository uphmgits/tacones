<div id="logo-sd">
   <a href="<?=$this->baseUrl()?>" title="Home"></a>
</div>

<div class="header-sd-right">
<?php if( $this->viewer->getIdentity()) :?> 
  <div id="mini-nav-container">
    <ul id="mini-nav-ul" >
      <li>
        <?=$this->translate('Welcome %s!', $this->viewer()->getTitle()); ?>
      </li>

      <li style="position: relative;">
        <a class="item9" id="psettings" href="javascript:togglesett();">
          <?=$this->translate("your account")?><span class='menu-arrow'>&nbsp;&nbsp;&nbsp;</span>
        </a>
        <div id="settings-menu-sd">
          <?php
            $count = count($this->navigation_mini);
            foreach( $this->navigation_mini->getPages() as $item ) $item->setOrder(--$count);
          ?>
          <ul>
            <?php foreach( $this->navigation_mini as $item ): ?>
              <li><?php echo $this->htmlLink($item->getHref(), $this->translate($item->getLabel()), array_filter(array(
                'class' => ( !empty($item->class) ? $item->class : null ),
                'alt' => ( !empty($item->alt) ? $item->alt : null ),
                'target' => ( !empty($item->target) ? $item->target : null ),
              ))) ?></li>
            <?php endforeach; ?> 
          </ul>
        </div>
      </li>

      <li>
        <?=$this->htmlLink(array('route' => 'marketplace_create'), $this->translate("Sell"))?>
      </li>

      <li style="padding-left: 0;">    
        <div id='core_menu_mini_menuq'>
          <ul>
          <?php if( $this->viewer->getIdentity()) :?>
          <li id='core_menu_mini_menu_update'>
            <span onclick="toggleUpdatesPulldown(event, this, '4');" style="display: inline-block;" class="updates_pulldown">
              <div class="pulldown_contents_wrapper">
                <div class="pulldown_contents">
                  <ul class="notifications_menu" id="notifications_menu">
                    <div class="notifications_loading" id="notifications_loading">
                      <img src='<?php echo $this->layout()->staticBaseUrl ?>application/modules/Core/externals/images/loading.gif' style='float:left; margin-right: 5px;' />
                      <?php echo $this->translate("Loading ...") ?>
                    </div>
                  </ul>
                </div>
                <div class="pulldown_options">
                  <?php echo $this->htmlLink(array('route' => 'default', 'module' => 'activity', 'controller' => 'notifications'),
                     $this->translate('View All Updates'),
                     array('id' => 'notifications_viewall_link')) ?>
                  <?php echo $this->htmlLink('javascript:void(0);', $this->translate('Mark All Read'), array(
                    'id' => 'notifications_markread_link',
                  )) ?>
                </div>
              </div>
              <a href="javascript:void(0);" id="updates_toggle" <?php if( $this->notificationCount ):?> class="new_updates"<?php endif;?>><?php echo $this->translate(array('%s Update', '%s Updates', $this->notificationCount), $this->locale()->toNumber($this->notificationCount)) ?></a>
            </span>
          </li>
          <?php endif; ?>
          </ul>
        </div>
      </li>

      <li>
        <a href="/invite"><?=$this->translate("invite friends")?></a>
      </li>
    </ul>
  </div>
<?php endif; ?>

 <?php if( !$this->viewer->getIdentity()) :?> 
  <div id="mini-nav-container">
     <ul id="mini-nav-ul" >
    <?php foreach( $this->navigation_mini as $item ): ?>
      <li><?php echo $this->htmlLink($item->getHref(), $this->translate($item->getLabel()), array_filter(array(
        'class' => ( !empty($item->class) ? $item->class : null ),
        'alt' => ( !empty($item->alt) ? $item->alt : null ),
        'target' => ( !empty($item->target) ? $item->target : null ),
      ))) ?></li>
    <?php endforeach; ?>
     </ul>
   </div> 
 <?php endif; ?>
 
 <div class="clear"></div>
   
  <?php if( $this->viewer->getIdentity() ) : ?>
    <div class="main-nav-cart">
      <?=$this->htmlLink(array('route' => 'marketplace_general', 'action' => 'cart'), $this->translate('cart ( %s )', $this->cartitems['cnt']))?>
    </div>
  <?php endif; ?>

  <div class="main-nav-search">
    <form action="/search" method="get">
    <input type="text" name="query" class="text" onFocus="if(this.value == 'search') { this.value = ''; }" value="search" />
    <input type="submit" value=" " class="main-nav-search-butn">
    </form>
  </div>
  <div id="main-nav-container">
    <?php 
      $categoryId = Zend_Controller_Front::getInstance()->getRequest()->getParam('category');
      if( is_numeric($categoryId) ) {
        $url = $this->url(array('category' => $categoryId), "marketplace_browse");// . "/{$categoryId}";
        $page = $this->navigation_main->findOneBy( 'uri', addslashes($url) );
        if( $page ) $page->setActive();
      } 
      echo $this->navigation()
                ->menu()
                ->setContainer($this->navigation_main)
                ->setPartial(null)
                ->setUlClass('main-nav-ul')
                ->render();
    ?>
  </div>
</div>

<div class="clear"></div>

<div class="header_img_sd"></div>

<script type='text/javascript'>
  var notificationUpdater;

  en4.core.runonce.add(function(){
    if($('global_search_field')){
      new OverText($('global_search_field'), {
        poll: true,
        pollInterval: 500,
        positionOptions: {
          position: ( en4.orientation == 'rtl' ? 'upperRight' : 'upperLeft' ),
          edge: ( en4.orientation == 'rtl' ? 'upperRight' : 'upperLeft' ),
          offset: {
            x: ( en4.orientation == 'rtl' ? -4 : 4 ),
            y: 2
          }
        }
      });
    }

    if($('notifications_markread_link')){
      $('notifications_markread_link').addEvent('click', function() {
        //$('notifications_markread').setStyle('display', 'none');
        en4.activity.hideNotifications('<?php echo $this->string()->escapeJavascript($this->translate("0 Updates"));?>');
      });
    }

    <?php if ($this->updateSettings && $this->viewer->getIdentity()): ?>
    notificationUpdater = new NotificationUpdateHandler({
              'delay' : <?php echo $this->updateSettings;?>
            });
    notificationUpdater.start();
    window._notificationUpdater = notificationUpdater;
    <?php endif;?>
  });


  var toggleUpdatesPulldown = function(event, element, user_id) {
    if( element.className=='updates_pulldown' ) {
      element.className= 'updates_pulldown_active';
      showNotifications();
    } else {
      element.className='updates_pulldown';
    }
  }

  var showNotifications = function() {
    en4.activity.updateNotifications();
    new Request.HTML({
      'url' : en4.core.baseUrl + 'activity/notifications/pulldown',
      'data' : {
        'format' : 'html',
        'page' : 1
      },
      'onComplete' : function(responseTree, responseElements, responseHTML, responseJavaScript) {
        if( responseHTML ) {
          // hide loading icon
          if($('notifications_loading')) $('notifications_loading').setStyle('display', 'none');

          $('notifications_menu').innerHTML = responseHTML;
          $('notifications_menu').addEvent('click', function(event){
            event.stop(); //Prevents the browser from following the link.

            var current_link = event.target;
            var notification_li = $(current_link).getParent('li');

            // if this is true, then the user clicked on the li element itself
            if( notification_li.id == 'core_menu_mini_menu_update' ) {
              notification_li = current_link;
            }

            var forward_link;
            if( current_link.get('href') ) {
              forward_link = current_link.get('href');
            } else{
              forward_link = $(current_link).getElements('a:last-child').get('href');
            }

            if( notification_li.get('class') == 'notifications_unread' ){
              notification_li.removeClass('notifications_unread');
              en4.core.request.send(new Request.JSON({
                url : en4.core.baseUrl + 'activity/notifications/markread',
                data : {
                  format     : 'json',
                  'actionid' : notification_li.get('value')
                },
                onSuccess : function() {
                  window.location = forward_link;
                }
              }));
            } else {
              window.location = forward_link;
            }
          });
        } else {
          $('notifications_loading').innerHTML = '<?php echo $this->string()->escapeJavascript($this->translate("You have no new updates."));?>';
        }
      }
    }).send();
  };
</script>
