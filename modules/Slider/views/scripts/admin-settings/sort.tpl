<?php
    $script = "var mySortables;
                function go_sort() {
                    \$(\"sort_slides\").set('value', mySortables.serialize());
                }
            window.addEvent('domready',function(){

                            mySortables = new Sortables('#slides_list',{
                                    clone: true,
                                    onStart: function(element){
                                            element.setStyles({'color':'#eb7070', 'border':'2px dashed #aaaaaa', 'background':'url(./application/modules/Slider/externals/images/admin/up_down.png) no-repeat right'});
                                    },
                                    onComplete: function(element){
                                            element.setStyles({'color':'#000000', 'border':'none', 'background':'none'});
                                    }
                                    });

                    });";
    $this->headScript()->appendScript($script, $type = 'text/javascript', $attrs = array());
?>
<div style="padding:10px 10px 0 13px;">
<h3>Change Positions of Slides</h3>

    <p>Use Drag&Drop to change position of slides.</p>
	<br />
  <ul id="slides_list">
      <?php foreach( $this->slides as $slide ): ?>
      <li id="slide_<?php echo $slide->slide_id ?>" style="cursor: move;">
          <img src="<?php echo $this->baseUrl() . '/application/modules/Slider/externals/images/slides/slide_' . $slide->slide_id . '.jpg' ?>" border="0" style="width: 120px;"/>
          <?php if (trim($slide->title)): ?><span><?php echo $slide->title ?></span><?php endif;?>
      </li>
      <?php endforeach; ?>
  </ul>

  <form method="POST" enctype="multipart/form-data" onsubmit="javascript:go_sort();">
      <input type="hidden" name="sort_slides" value="" id="sort_slides"/>
      <input type="hidden" name="sort_task" value="save" />
      <button type="submit" value="Save" class="button">Save</button>
    or <a href="javascript:void(0);" onclick="javascript:parent.Smoothbox.close();" >Cancel</a>
  </form>

</div>
