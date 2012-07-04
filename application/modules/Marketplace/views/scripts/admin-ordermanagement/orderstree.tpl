
<?php $this->headLink()->prependStylesheet($this->baseUrl().'/externals/tree/tree.css'); ?>
<script src="<?=$this->baseUrl()?>/externals/jquery/jquery-1.7.1.min.js"></script>
<script type="text/javascript">
  jQuery.noConflict()
  en4.core.runonce.add(function() { 
     tree("tree", "<?=$this->url(array('module' => 'marketplace', 'controller' => 'ordermanagement', action => 'ajax-orderstree-item'), 'admin_default')?>");
  });
</script>
<script src="<?=$this->baseUrl()?>/externals/tree/tree.js"></script>
<style>
    #global_content {
      overflow-y: scroll;
    }
    #tree { 
      position: relative; 
    }
    .ajaxContentResult {
      display: block;
      position: absolute;
      left: 200px;
      top: 0;
    }
    .ajaxContentResultHidden {
      display: none;
    }
    .divTotal {
      font-size: 18px;
      font-weight: bold;
      margin-bottom: 10px;
    }
</style>


<h2><?php echo $this->translate("Orders Tree") ?></h2>

<?php if( count($this->navigation) ): ?>
  <div class='tabs'>
    <?php
      // Render the menu
      echo $this->navigation()->menu()->setContainer($this->navigation)->render()
    ?>
  </div>
<?php endif; ?>

<?php if( count($this->subNavigation) ): ?>
  <div class='tabs'>
    <?php
      // Render the menu
      echo $this->navigation()->menu()->setContainer($this->subNavigation)->render()
    ?>
  </div>
<?php endif; ?>

Periods:
<ul class="Container" id="tree">
  <?php for($i = date('Y'); $i >= 2010; $i-- ) : ?>
  <li class="Node IsRoot IsLast ExpandClosed" id="<?=$i?>">
    <div class="Expand"></div>
    <div class="Content"><?=$i?></div>
    <ul class="Container">
    </ul>
  </li>
  <?php endfor; ?>
</ul>
