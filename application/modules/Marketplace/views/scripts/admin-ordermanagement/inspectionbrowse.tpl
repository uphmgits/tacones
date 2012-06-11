<?php
/**
 * @category   Application_Extensions
 * @package    Marketplace
 * @copyright  Copyright 2011 SocialEngineMarket
 * @license    http://www.socialenginemarket.com
 */
?>

<h2><?php echo $this->translate("Inspection Orders") ?></h2>

<?php if( count($this->navigation) ): ?>
  <div class='tabs'>
    <?php
      // Render the menu
      echo $this->navigation()->menu()->setContainer($this->navigation)->render()
    ?>
  </div>
<?php endif; ?>

<p>
  <?php echo $this->translate("MARKETPLACES_INSPECTION_BROWSE_DESCRIPTION") ?>
</p>

<?php if( count($this->subNavigation) ): ?>
  <div class='tabs'>
    <?php
      // Render the menu
      echo $this->navigation()->menu()->setContainer($this->subNavigation)->render()
    ?>
  </div>
<?php endif; ?>

<br />
<?php if( $this->file_name ) : ?>
  <h1><a href="<?=$this->file_name?>" target='_blank'><?=$this->translate('Download File')?></a></h1>
<?php endif; ?>


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
    return confirm('<?php echo $this->string()->escapeJavascript($this->translate("Are you sure you want to add to paypal file the selected orders?")) ?>');
  }
}

function selectAll()
{
  var i;
  var multimodify_form = $('multimodify_form');
  var inputs = multimodify_form.elements;
  for (i = 1; i < inputs.length - 1; i++) {
    if (!inputs[i].disabled && inputs[i].type == 'checkbox' ) {
      inputs[i].checked = inputs[0].checked;
    }
  }
}
function startInspectionNotify(value) {
    $('notifyStart').value = value;
    $('frmNotifyStart').submit();
}
function finishInspectionNotify(value) {
    $('notifyFinish').value = value;
    $('frmNotifyFinish').submit();
}
function createInvoice(value) {
    $('createInvoice').value = value;
    $('frmCreateInvoice').submit();
}
</script>

<form action="" method="post" id="frmNotifyStart">
  <input type="hidden" id="notifyStart" name="notifyStart" value="" />
</form>
<form action="" method="post" id="frmNotifyFinish">
  <input type="hidden" id="notifyFinish" name="notifyFinish" value="" />
</form>
<form action="" method="post" id="frmCreateInvoice">
  <input type="hidden" id="createInvoice" name="createInvoice" value="" />
</form>

<div class='admin_search' style="display: none; height: 20px;">
  <?php echo $this->formFilter->render($this) ?>
</div>
<form method='post' action=''>
  <select name='status_filter'>
    <option value='all'      <?php if( $this->status_filter == 'all' )      echo "selected"?>><?=$this->translate("All")?></option>
    <option value='pending'  <?php if( $this->status_filter == 'pending' )  echo "selected"?>><?=$this->translate("In Progress")?></option>
    <option value='sold'     <?php if( $this->status_filter == 'sold' )     echo "selected"?>><?=$this->translate("Sold")?></option>
    <option value='return'   <?php if( $this->status_filter == 'return' )   echo "selected"?>><?=$this->translate("Return")?></option>
    <option value='notlegit' <?php if( $this->status_filter == 'notlegit' ) echo "selected"?>><?=$this->translate("Not Legitimate")?></option>
    <option value='punished' <?php if( $this->status_filter == 'punished' ) echo "selected"?>><?=$this->translate("Punished")?></option>
  </select>  
  <button type='submit' name="submit_button" value="change_status_filter"><?php echo $this->translate("Filter") ?></button>
</form>


<br />

<div class='admin_results'>
  <div>
    <?php echo $this->paginationControl($this->paginator, null, null, array('query' => array('status_filter' => $this->status_filter))); ?>
  </div>
</div>

<br />
<?php if( count($this->paginator) ): ?>
<div class="admin_table_form">
<form id='multimodify_form' method="post" action="" onSubmit="multiModify()">
  <table class='admin_table'>
    <thead>
      <tr>
        <th style='width: 1%;'><input onclick="selectAll()" type='checkbox' class='checkbox'></th>
        <th style='width: 1%;'><a href="javascript:void(0);" onclick="javascript:changeOrder('order_id', 'DESC');"><?php echo $this->translate("ID") ?></a></th>
        <th style='width: 20%;'><a href="javascript:void(0);" onclick="javascript:changeOrder('marketplace_id', 'DESC');"><?php echo $this->translate("Marketplace") ?></a></th>
        <th style='width: 10%;'><a href="javascript:void(0);" onclick="javascript:changeOrder('user_id', 'DESC');"><?php echo $this->translate("Seller") ?></a></th>
        <th style='width: 10%;'><a href="javascript:void(0);" onclick="javascript:changeOrder('owner_id', 'DESC');"><?php echo $this->translate("Who buy?") ?></a></th>
        <th><a href="javascript:void(0);" onclick="javascript:changeOrder('summ', 'DESC');"><?php echo $this->translate("Summ") ?></a></th>
        <th style='width: 30%;'><?php echo $this->translate("Status") ?></th>
        <th style='width: 1%;'><a href="javascript:void(0);" onclick="javascript:changeOrder('count', 'DESC');"><?php echo $this->translate("Cnt") ?></a></th>
        <th style='width: 10%;'><a href="javascript:void(0);" onclick="javascript:changeOrder('date', 'DESC');"><?php echo $this->translate("Date") ?></a></th>

      </tr>
    </thead>
    <tbody>
      
        <?php foreach( $this->paginator as $item ): ?>
          <?php $itemId = $item->getIdentity(); ?>
          <tr>
            <td>
              <input <?php if ( $item->to_file_transfer > 0 or $this->status_filter == 'all' or $this->status_filter == 'pending') echo 'disabled';?> name="modify_<?=$itemId?>" value="<?=$itemId?>" type="checkbox" class="checkbox" />
            </td>
            <td><?php echo $item->order_id ?></td>
            <td class='admin_table_user'>
              <?php $marketplace = $this->item('marketplace', $item->marketplace_id); ?>
              <?php if($marketplace) : ?>
                <?php echo $this->htmlLink($marketplace->getHref(), $marketplace->getTitle(), array('target' => '_blank')); ?>
              <?php else : ?>
                <?=$this->translate('Deleted');?>
              <?php endif; ?>
            </td>
            <td class='admin_table_bold'>
              <?php
                  $display_name = $this->item('user', $item->owner_id)->getTitle();
                  echo $this->htmlLink($this->item('user', $item->owner_id)->getHref(), $display_name, array('target' => '_blank'));
              ?>
            </td>
            <td class='admin_table_bold'>
              <?php
                if( $item->user_id ) {
                  $display_name = $this->item('user', $item->user_id)->getTitle();
                  echo $this->htmlLink($this->item('user', $item->user_id)->getHref(), $display_name, array('target' => '_blank'));
                } else {
                  echo $this->translate('Unregistered user');
                  if( $item->contact_email ) echo "<div style='font-weight: normal; font-size: 10px;'>".$this->translate('Contact Email:')." ".$item->contact_email."</div>";
                }
              ?>
            </td>
            
            <td>
              <table>
                <tr><td><?=$this->translate("Total: ")?></td><td><?=$item->summ?></td>
                <tr><td><?=$this->translate("Price: ")?></td><td><?=$item->price?></td>
                <tr><td><?=$this->translate("Inspection: ")?></td><td><?=$item->inspection?></td>
                <tr><td><?=$this->translate("Shipping: ")?></td><td><?=$item->shipping?></td>
              </table>
            </td>
            <td>
                <?php if( $item->to_file_transfer == 0) : ?>
                  <?php if( $item->status != 9) : ?>
                  <table>
                    <tr>
                      <td>
                        <input <?php if ( $item->status == 0 ) echo 'checked';?> name="status_modify_<?=$itemId?>" value="0" type="radio" class="radio" />
                        <span style='font-size: 11px;'><?=$this->translate('In Progress')?></span>
                      </td>
                      <td>
                        <input <?php if ( $item->status == 1 ) echo 'checked';?> name="status_modify_<?=$itemId?>" value="1" type="radio" class="radio" />
                        <span style='font-size: 11px;'><?=$this->translate('Sold')?></span>
                      </td>
                    </tr>
                    <tr>
                      <td>
                        <input <?php if ( $item->status == 2 ) echo 'checked';?> name="status_modify_<?=$itemId?>" value="2" type="radio" class="radio" />
                        <span style='font-size: 11px;'><?=$this->translate('Return')?></span>
                      </td>
                      <td>
                        <input <?php if ( $item->status == 3 ) echo 'checked';?> name="status_modify_<?=$itemId?>" value="3" type="radio" class="radio" />
                        <span style='font-size: 11px;'><?=$this->translate('Not Legitimate')?></span>
                      </td>
                    </tr>
                  </table>

                  <div>
                    <button type='submit' name="submit_button" value="change_status" style="font-size:10px; margin-top: 8px;">
                      <?=$this->translate("Update Status")?>
                    </button>
                    <?php if( file_exists( $this->pdfMainPath . $itemId . ".pdf" ) ) : ?>
                      <?=$this->htmlLink($this->pdfMainUrl . $itemId . ".pdf", $this->translate('Download Invoice'))?>
                    <?php else: ?>
                      <button type="button" onclick="createInvoice(<?=$itemId?>)" style="font-size:10px; margin-top: 8px;">
                        <?=$this->translate('Create Invoice')?>
                      </button>
                    <?php endif; ?>
                  </div>

                  <div>
                    <button type="button" onclick="startInspectionNotify(<?=$itemId?>)" style="font-size:10px; margin-top: 8px;">
                      <?=$this->translate('Notify about<br/>start Inspection')?>
                    </button>
                    <button type="button" onclick="finishInspectionNotify(<?=$itemId?>)" style="font-size:10px; margin-top: 8px;">
                      <?=$this->translate('Notify about<br/>approving item')?>
                    </button>
                  </div>
 
                  <?php else : ?>    
                      <?='<span style="color:red; font-weight: bold;">'.$this->translate('Punished')."</span>"?>
                  <?php endif; ?>
                <?php else : ?>
                  <?php if( $item->to_file_transfer == 1) : ?>
                    <?='<span style="color:gray">'.$this->translate('In Sold File')."</span>"?>
                  <?php endif; ?>
                  <?php if( $item->to_file_transfer == 2) : ?>
                    <?='<span style="color:gray">'.$this->translate('In Return File')."</span>"?>
                  <?php endif; ?>
                <?php endif; ?>
            </td>
            <td><?php echo $item->count; ?></td>
            <td><?=str_replace(' ', '<br/>', $item->date)?></td>
          </tr>
        <?php endforeach; ?>
     
    </tbody>
  </table>
  <br />
  <div class='buttons'>
    <button type='submit' name="submit_button" value="change_status"><?=$this->translate("Update Status")?></button>
     <?php if( $this->status_filter == 'sold' ) : ?>
        <button type='submit' name="submit_button" value="add_to_sold_file"><?=$this->translate("Add to Sold File")?></button>
     <?php endif; ?>
     <?php if( $this->status_filter == 'return' ) : ?>
        <button type='submit' name="submit_button" value="add_to_return_file"><?=$this->translate("Add to Return File")?></button>
     <?php endif; ?>
     <?php if( $this->status_filter == 'notlegit' ) : ?>
        <button type='submit' name="submit_button" value="punish"><?=$this->translate("Punish")?></button>
     <?php endif; ?>
  </div>
</form>
</div>
<?php else:?>
      <br/>
      <div class="tip">
      <span><?=$this->translate("There are currently no reports.")?></span>
      </div>
      <?php endif;?>
