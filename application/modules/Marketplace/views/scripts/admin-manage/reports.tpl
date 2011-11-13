<?php
/**
 * 
 *
 * @category   Application_Core
 * @package    User
 * @copyright  Copyright 2010 Webligo Developments
 * * 
 * @version    $Id: index.tpl 7606 2010-10-08 00:15:43Z bryan $
 * @author     John
 */
?>

<div class="headline">
  <h2>
    <?php echo $this->translate('Marketplace Reports');?>
  </h2>
<?php if( count($this->navigation) ): ?>
  <div class='tabs'>
    <?php
      // Render the menu
      echo $this->navigation()->menu()->setContainer($this->navigation)->render()
    ?>
  </div>
<?php endif; ?>
</div>

<br />

<script type="text/javascript">
  var currentOrder = '<?php echo $this->order ?>';
  var currentOrderDirection = '<?php echo $this->order_direction ?>';
  var changeOrder = function(order, default_direction){
    // Just change direction
    if( order == currentOrder ) {
      $('order_direction').value = ( currentOrderDirection == 'ASC' ? 'DESC' : 'ASC' );
    } else {
      $('order').value = order;
      $('order_direction').value = default_direction;
    }
    $('filter_form').submit();
  }

function multiModify()
{
  var multimodify_form = $('multimodify_form');
  if (multimodify_form.submit_button.value == 'delete')
  {
    return confirm('<?php echo $this->string()->escapeJavascript($this->translate("Are you sure you want to delete the selected user accounts?")) ?>');
  }
}

function selectAll()
{
  var i;
  var multimodify_form = $('multimodify_form');
  var inputs = multimodify_form.elements;
  for (i = 1; i < inputs.length - 1; i++) {
    if (!inputs[i].disabled) {
      inputs[i].checked = inputs[0].checked;
    }
  }
}

function loginAsUser(id) {
  if( !confirm('<?php echo $this->translate('Note that you will be logged out of your current account if you click ok.') ?>') ) {
    return;
  }
  var url = '<?php echo $this->url(array('action' => 'login')) ?>';
  var baseUrl = '<?php echo $this->url(array(), 'default', true) ?>';
  (new Request.JSON({
    url : url,
    data : {
      format : 'json',
      id : id
    },
    onSuccess : function() {
      window.location.replace( baseUrl );
    }
  })).send();
}
</script>

<div class='admin_search' style="display: none; height: 20px;">
  <?php echo $this->formFilter->render($this) ?>
</div>

<br />

<div class='admin_results'>
  <div>
    <?php echo $this->paginationControl($this->paginator); ?>
  </div>
</div>

<br />
<?php if( count($this->paginator) ): ?>
<div class="admin_table_form">
  <table class='admin_table'>
    <thead>
      <tr>
        <th style='width: 1%;'><a href="javascript:void(0);" onclick="javascript:changeOrder('order_id', 'DESC');"><?php echo $this->translate("ID") ?></a></th>
        <th style='width: 20%;'><a href="javascript:void(0);" onclick="javascript:changeOrder('marketplace_id', 'DESC');"><?php echo $this->translate("Marketplace") ?></a></th>
        <th style='width: 20%;'><a href="javascript:void(0);" onclick="javascript:changeOrder('user_id', 'DESC');"><?php echo $this->translate("Seller") ?></a></th>
        <th style='width: 20%;'><a href="javascript:void(0);" onclick="javascript:changeOrder('owner_id', 'DESC');"><?php echo $this->translate("Who buy?") ?></a></th>
        <th style='width: 10%;'><a href="javascript:void(0);" onclick="javascript:changeOrder('summ', 'DESC');"><?php echo $this->translate("Price") ?></a></th>
        <th style='width: 20%;'><a href="javascript:void(0);" onclick="javascript:changeOrder('date', 'DESC');"><?php echo $this->translate("Date") ?></a></th>

      </tr>
    </thead>
    <tbody>
      
        <?php foreach( $this->paginator as $item ): ?>
          <tr>
            <td><?php echo $item->order_id ?></td>
            <td class='admin_table_user'><?php echo $this->htmlLink($this->item('marketplace', $item->marketplace_id)->getHref(), $this->item('marketplace', $item->marketplace_id)->title, array('target' => '_blank')) ?></td>
                        <td class='admin_table_bold'>
              <?php
                $display_name = $this->item('user', $item->owner_id)->getTitle();
                echo $this->htmlLink($this->item('user', $item->owner_id)->getHref(), $display_name, array('target' => '_blank'))
              ?>
            </td>
            <td class='admin_table_bold'>
              <?php
                $display_name = $this->item('user', $item->user_id)->getTitle();
                echo $this->htmlLink($this->item('user', $item->user_id)->getHref(), $display_name, array('target' => '_blank'))
              ?>
            </td>
            
            <td><?php echo $item->summ; ?></td>
            <td><?php echo $item->date; ?></td>
          </tr>
        <?php endforeach; ?>
     
    </tbody>
  </table>
  <br />
</div>
<?php else:?>
      <br/>
      <div class="tip">
      <span><?php echo $this->translate("There are currently no reports.") ?></span>
      </div>
      <?php endif;?>