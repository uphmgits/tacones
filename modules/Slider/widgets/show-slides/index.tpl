<?php $this->headScript()->appendFile($this->baseUrl() . '/application/modules/Slider/externals/scripts/slider.js') ?>
<?php
    $settings = Engine_Api::_()->getApi('settings', 'core');
    $script = "window.addEvent('domready', function() {
                      var first_des = { 
                       href: \"{$this->paginator[0]->link}\",
                       title: \"{$this->paginator[0]->title}\",
                       description: \"".addslashes(str_replace("\r\n", '<br/>',htmlspecialchars_decode($this->paginator[0]->description))) ."\"
                        };
                      myslider = new Slider ({
                       slides:[{$this->slides_str}],
                       delay:" . $settings->getSetting('time_delete', 10)*1000 . ",
                       slider_url:'./application/modules/Slider/externals/images/slides/slide_',
                       url: '{$this->url(array('module'=>'slider','controller'=>'ajax','action'=>'getslide'), 'default') }',
                       first_description: first_des
                      });
                     });";
    $this->headScript()->appendScript($script, $type = 'text/javascript', $attrs = array());
?>




<div class="slider_body" style="width:<?php echo $settings->getSetting('slide_width', 640) ?>px; height:<?php echo $settings->getSetting('slide_height', 480) ?>px">
<div class="slider_nav">

<div id="slider_content" class="slider_content" style="display: <?php echo (trim($this->paginator[0]->title) or trim($this->paginator[0]->description)) ? 'block' : 'none' ?>;">
	<h1 id="slider_title"><?php echo $this->paginator[0]->title ?></h1>
	<span id="slider_description"><?php echo str_replace("\r\n", '<br/>',htmlspecialchars_decode($this->paginator[0]->description)) ?></span>
</div>

    <div class="nav_bgr">
        <?php echo $this->slides_links ?>
        <div style="float:right; margin-top:3px;"> <div style="display: none;" id='slider_loading'><img src='<?php echo './application/modules/Slider/externals/images/load.gif' ?>' style="margin:-8px 0" border='0'></div>
            <a id='slider_play_control_pause' href='javascript:void(0);' onclick="javascript: myslider.pause();"><img align="left" border="0" alt="" src="<?php echo './application/modules/Slider/externals/images/slider_pause.png' ?>" class="home_slideshow_class3"/>PAUSE</a>
            <a id='slider_play_control_play' href='javascript:void(0);' onclick="javascript: myslider.play();" style="display:none;"><img border="0" align="left" alt="" src="<?php echo './application/modules/Slider/externals/images/slider_play.png' ?>" class="home_slideshow_class3"/>PLAY</a>
        </div>
    </div>
		
</div>




  <div id="thework">
   <div id="slide_<?php echo $this->paginator[0]->slide_id ?>">
   <?php if (trim($this->paginator[0]->link)): ?><a href="<?php echo $this->paginator[0]->link ?>"> <?php endif ?>
       <img class="slider" alt="" src="<?php echo './application/modules/Slider/externals/images/slides/slide_' . $this->paginator[0]->slide_id . '.jpg'?>"/>
    <?php if (trim($this->paginator[0]->link)): ?></a><?php endif ?>
   </div>
  </div>
</div>