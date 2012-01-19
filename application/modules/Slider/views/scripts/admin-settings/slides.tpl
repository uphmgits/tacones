<?php $this->headScript()->appendFile($this->baseUrl() . '/application/modules/Slider/externals/scripts/SqueezeBox.js') ?>
<?php
    $script = "
            window.addEvent('domready',function(){
                    SqueezeBox.assign($$('a[rel=boxed]'));
                    });";
    $this->headScript()->appendScript($script, $type = 'text/javascript', $attrs = array());
?>
<h2>View Slides</h2>

<?php if( count($this->navigation) ): ?>
<div class='tabs'>
    <?php
    echo $this->navigation()->menu()->setContainer($this->navigation)->render()
    ?>
</div>
<?php endif; ?>

<div class='clear'>
    <?php echo $this->htmlLink(array('route' => 'admin_default', 'module' => 'slider', 'controller' => 'settings', 'action' => 'add-slide'), $this->translate('New Slide'), array(
      'class' => 'smoothbox buttonlink',
      'style' => 'background-image: url(application/modules/Slider/externals/images/admin/add_slide.png);')) ?>
    <?php echo $this->htmlLink(array('route' => 'admin_default', 'module' => 'slider', 'controller' => 'settings', 'action' => 'sort'), $this->translate('Change Slides Order'), array(
      'class' => 'smoothbox buttonlink',
      'style' => 'background-image: url(application/modules/Slider/externals/images/admin/sort_slide.png);')) ?>
  <?php if( count($this->slides) == 0 ): ?>
  
   <div align='center' style='margin-top:20px;'>
    <table cellspacing="0" cellpadding="0">
     <tr>
      <td class="result">
       <div><div class="bulb16"></div><? echo $this->translate('No files were found.') ?></div>
      </td>
     </tr>
    </table>
    <br/>
   </div>
  <?php else:?>
  <br /><br />
   <table class="admin_table">
   <thead>
    <tr>
     <th>#</th>
     <th>Preview</th>
     <th>Details</th>
     <th>Created</th>
     <th>Status</th>
     <th>Action</th>
    </tr>
	</thead>
    <?php $i = 1; ?>
    <?php foreach( $this->slides as $slide ): ?>
    <tr class='background'>
     <td class='item'><?php echo $i++ ?></td>

     <td class='item'>
         <a rel="boxed" href="<?php echo $this->baseUrl() . '/application/modules/Slider/externals/images/slides/slide_' . $slide->slide_id . '.jpg' ?>">
            <img src="<?php echo $this->baseUrl() . '/application/modules/Slider/externals/images/slides/slide_' . $slide->slide_id . '.jpg' ?>" border="0" style="width: 200px;"/>
         </a>
     </td>

     <td class='item' style="text-align: left;">      
      <b>Title:</b><br>
      <?php if( trim($slide->title) ): ?>
      <?php echo nl2br(htmlspecialchars_decode($slide->title)) ?>
      <?php else:?>
      &mdash;
      <?php endif; ?>
      <br>

      <b><br>Description:</b><br>
      <?php if( trim($slide->description) ): ?>
      <?php echo nl2br(htmlspecialchars_decode($slide->description)) ?>
      <?php else:?>
      &mdash;
      <?php endif; ?>
      <br>
      <br>
      <b>Link:</b><br>
      <?php if( trim($slide->link) ): ?>
      <a target="_blank" href="<?php echo $slide->link ?>"><?php echo $slide->link ?></a><br>
      <?php else:?>
      &mdash;
      <?php endif; ?>

     </td>
     <?php 
     // output date and time on different lines
     	$creation_timestamp = strtotime($slide->creation_date);
     	$creation_time = date('H:i', $creation_timestamp);
     	$creation_date = date('M j, Y', $creation_timestamp);
     ?>
     <td style="text-align:right"><?php echo $creation_date.'<br />'.$creation_time ?></td>
     <td><?php echo ($slide->enable_slide) ? 'Enabled' : 'Disabled' ; ?></td>
     <td>
         <?php echo $this->htmlLink(array('route' => 'admin_default', 'module' => 'slider', 'controller' => 'settings', 'action' => 'add-slide', 'slide_id' => $slide->slide_id), 'edit', array('class' => 'smoothbox')) ?>
         |
         <?php echo $this->htmlLink(array('route' => 'admin_default', 'module' => 'slider', 'controller' => 'settings', 'action' => 'delete', 'slide_id' => $slide->slide_id), 'delete', array('class' => 'smoothbox')) ?>
     </td>
    </tr>
    <?php endforeach; ?>
   </table>
  <?php endif; ?>
</div>

