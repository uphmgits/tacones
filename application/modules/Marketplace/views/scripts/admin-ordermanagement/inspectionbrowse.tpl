<?php
/**
 * @category   Application_Extensions
 * @package    Marketplace
 * @copyright  Copyright 2011 SocialEngineMarket
 * @license    http://www.socialenginemarket.com
 */
?>

<style>div.admin-reason-popup { display: none; }</style>

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
function createInvoice(value) {
    $('createInvoice').value = value;
    $('frmCreateInvoice').submit();
}
</script>

<form action="" method="post" id="frmCreateInvoice">
  <input type="hidden" id="createInvoice" name="createInvoice" value="" />
</form>

<div class='admin_search' style="display: none; height: 20px;">
  <?php echo $this->formFilter->render($this) ?>
</div>
<form method='post' action=''>
  <select name='status_filter'>
    <option value='all'        <?php if( $this->status_filter == 'all' )      echo "selected"?>><?=$this->translate("All")?></option>
    <option value='wait'       <?php if( $this->status_filter == 'wait' )     echo "selected"?>><?=$this->translate("Bought")?></option>
    <option value='inprogress' <?php if( $this->status_filter == 'inprogress' )  echo "selected"?>><?=$this->translate("Received")?></option>
    <option value='approved'   <?php if( $this->status_filter == 'approved' ) echo "selected"?>><?=$this->translate("Passed")?></option>
    <option value='admin_sent' <?php if( $this->status_filter == 'approved' ) echo "selected"?>><?=$this->translate("Sent to Buyer")?></option>
    <option value='failed'     <?php if( $this->status_filter == 'done_failed' )   echo "selected"?>><?=$this->translate("For punish")?></option>
    <?php /*<option value='sold' <?php if( $this->status_filter == 'sold' ) echo "selected"?>><?=$this->translate("Complete")?></option>
    <option value='return'     <?php if( $this->status_filter == 'return' )   echo "selected"?>><?=$this->translate("Return")?></option>
    <option value='canceled' <?php if( $this->status_filter == 'canceled' ) echo "selected"?>><?=$this->translate("Canceled")?></option>*/?>
    <option value='cancelrequest' <?php if( $this->status_filter == 'cancelrequest' ) echo "selected"?>><?=$this->translate("Cancel Request")?></option>
    <option value='done_failed' <?php if( $this->status_filter == 'done_failed' ) echo "selected"?>><?=$this->translate("For punish")?></option>
    <option value='punished' <?php if( $this->status_filter == 'punished' ) echo "selected"?>><?=$this->translate("Punished")?></option>
  </select> 

  <select name='period_filter'>
    <option value='all'   <?php if( $this->period_filter == 'all' )   echo "selected"?>><?=$this->translate("All Time")?></option>
    <option value='day'   <?php if( $this->period_filter == 'day' )   echo "selected"?>><?=$this->translate("Day")?></option>
    <option value='week'  <?php if( $this->period_filter == 'week' )  echo "selected"?>><?=$this->translate("Week")?></option>
    <option value='mount' <?php if( $this->period_filter == 'mount' ) echo "selected"?>><?=$this->translate("Mount")?></option>
    <option value='quarter' <?php if( $this->period_filter == 'quarter' ) echo "selected"?>><?=$this->translate("Quarter")?></option>
    <option value='year'  <?php if( $this->period_filter == 'year' )  echo "selected"?>><?=$this->translate("Year")?></option>
  </select>
  
  <button type='submit' name="submit_button" value="change_status_filter"><?php echo $this->translate("Filter") ?></button>
</form>


<br />

<div class='admin_results'>
  <div>
    <?php echo $this->paginationControl($this->paginator, null, null, array('query' => array('status_filter' => $this->status_filter, 'period_filter' => $this->period_filter ))); ?>
  </div>
</div>

<br />
<?php if( count($this->paginator) ): ?>
<div class="admin_table_form">
<form id='multimodify_form' method="post" action="" onSubmit="multiModify()">
  <table class='admin_table' width="100%" style="table-layout: fixed;">
    <thead>
      <tr>
        <th style='width: 3%;'><input onclick="selectAll()" type='checkbox' class='checkbox'></th>
        <th style='width: 4%;'><a href="javascript:void(0);" onclick="javascript:changeOrder('order_id', 'DESC');"><?php echo $this->translate("ID") ?></a></th>
        <th style='width: 10%;'><a href="javascript:void(0);" onclick="javascript:changeOrder('marketplace_id', 'DESC');"><?php echo $this->translate("Marketplace") ?></a></th>
        <th class="wrap"><a href="javascript:void(0);" onclick="javascript:changeOrder('user_id', 'DESC');"><?php echo $this->translate("Seller") ?></a></th>
        <th class="wrap"><a href="javascript:void(0);" onclick="javascript:changeOrder('owner_id', 'DESC');"><?php echo $this->translate("Who buy?") ?></a></th>
        <th style='width: 140px'><a href="javascript:void(0);" onclick="javascript:changeOrder('summ', 'DESC');"><?php echo $this->translate("Summ") ?></a></th>
        <th style='width: 240px'><?php echo $this->translate("Status") ?></th>
        <th style='width: 12%;'><a href="javascript:void(0);" onclick="javascript:changeOrder('date', 'DESC');"><?php echo $this->translate("Date purchased") ?></a></th>

      </tr>
    </thead>
    <tbody>
        <?php $disableCheckbox = in_array($this->status_filter, array('sold', 'return', 'failed')) ? false : true; ?>
        <?php foreach( $this->paginator as $item ): ?>
          <?php $itemId = $item->getIdentity(); ?>
          <tr>
            <td>
              <input type="checkbox" <?php if ( $disableCheckbox or $item->to_file_transfer > 0) echo 'disabled';?> 
                     name="modify_<?=$itemId?>" value="<?=$itemId?>" class="checkbox" />
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
            <td class='admin_table_bold wrap'>
              <?php
                  $owner = $this->item('user', $item->owner_id);
                  echo $this->htmlLink($owner->getHref(), $owner->getTitle(), array('target' => '_blank'));
                  echo "<div style='word-wrap: break-word;'>{$owner->email}</div>";
              ?>
            </td>
            <td class='admin_table_bold wrap'>
              <?php if( $item->user_id ) : ?>
                  <?php $buyer = $this->item('user', $item->user_id); ?>
                  <?=$this->htmlLink($buyer->getHref(), $buyer->getTitle(), array('target' => '_blank'));?>
                  <?php if( $item->shipping_info ) :	?>
                    <br/>                  
                    (<a class='smoothbox' href="<?=$this->url(array('action' => 'view-shipping-info', 'siid' => $item->shipping_info	));?>">
                      <?=$this->translate("shipping info")?>
                    </a>)
                  <?php endif;?>
              <?php else : ?>
                  <?=$this->translate('Unregistered user');?>
                  <?php if( $item->contact_email ) : ?>
                    <div style='font-weight: normal; font-size: 10px;'>
                      <?=$this->translate('Contact Email:')." ".$item->contact_email?>
                    </div>
                  <?php endif;?>
              <?php endif;?>
            </td>
            
            <td>
              <table>
                <tr><td style="font-weight:bold;"><?=$this->translate("Total: ")?></td>
                    <td style="font-weight:bold;"><?=$item->summ * $item->count?></td></tr>
                <tr><td><?=$this->translate("Count: ")?></td><td><?=$item->count?></td></tr>
                <tr><td><?=$this->translate("Item Total: ")?></td><td><?=$item->summ?></td></tr>
                <tr><td><?=$this->translate("Price: ")?></td><td><?=$item->price?></td></tr>
                <tr><td><?=$this->translate("SH: ")?></td><td><?=$item->inspection?></td></tr>
              </table>
            </td>

            <td>
              <?php if( $item->to_file_transfer == 0) : ?>

                <?php if( $item->status != 'punished' and $item->status != 'canceled' ) : ?>
                <table width="100%">
                  <?php if( $item->status == 'cancelrequest' ) : ?>
                  <tr>
                    <td colspan="2">
                      <input type="radio" checked name="smod_<?=$itemId?>" value="cancelrequest"/>
                      <span style='font-size: 11px;'><?=$this->translate('Cancel request')?></span>
                    </td>
                  </tr>
                  <?php endif; ?>
                  <tr>
                    <td>
                      <input type="radio" <?=($item->status == 'wait') ? 'checked' : 'disabled';?> 
                             name="smod_<?=$itemId?>" value="wait"/>
                      <span style='font-size: 11px;'><?=$this->translate('Bought')?></span>
                    </td>
                    <td>
                      <input type="radio" <?php if ( $item->status == 'inprogress' ) echo 'checked';?> 
                             name="smod_<?=$itemId?>" <?php if ( $item->status != 'wait' and $item->status != 'cancelrequest' ) echo 'disabled';?> 
                             value="inprogress"/>
                      <span style='font-size: 11px;'><?=$this->translate('Received')?></span>
                    </td>
                  </tr>
                  <tr>
                    <td>
                      <input type="radio" <?php if ( $item->status == 'approved' ) echo 'checked';?> 
                             name="smod_<?=$itemId?>"  <?php if ( $item->status != 'inprogress' ) echo 'disabled';?> 
                             value="approved"/>
                      <span style='font-size: 11px;'><?=$this->translate('Passed')?></span>
                    </td>
                    <td>
                      <input type="radio" <?php if ( $item->status == 'failed' ) echo 'checked';?> 
                             name="smod_<?=$itemId?>" <?php if ( $item->status != 'inprogress' ) echo 'disabled';?> 
                             value="failed"/>
                      <span style='font-size: 11px;'><?=$this->translate('Failed')?></span>
                    </td>
                  </tr>
                  <tr>
                    <td>
                      <input type="radio" name="smod_<?=$itemId?>"
                            <?php if ( $item->status == 'sold' or $item->status == 'return' or 
                                        $item->status == 'failed' or $item->status == 'admin_sent' ) echo 'disabled';?>
                             value="canceled"/>
                      <span style='font-size: 11px;'><?=$this->translate('Canceled')?></span>
                    </td>
                    <td>
                      <input type="radio" <?php if ( $item->status == 'admin_sent' ) echo 'checked';?> 
                             name="smod_<?=$itemId?>" <?php if ( $item->status != 'approved' and $item->status != 'admin_sent' ) echo 'disabled';?>
                             value="admin_sent"/>
                      <span style='font-size: 11px;'><?=$this->translate('Sent to buyer')?></span>
                    </td>
                  </tr>
                  <tr>
                    <td>
                      <input type="radio" <?php if( $item->status == 'sold' ) echo 'checked';?> 
                             name="smod_<?=$itemId?>" <?php if ( $item->status != 'admin_sent' ) echo 'disabled';?> 
                             value="sold"/>
                      <span style='font-size: 11px;'><?=$this->translate('Complete')?></span>
                    </td>
                    <td>
                      <input type="radio" <?php if ( $item->status == 'return' ) echo 'checked';?> 
                             name="smod_<?=$itemId?>" <?php if ( $item->status != 'admin_sent' ) echo 'disabled';?> 
                             value="return"/>
                      <span style='font-size: 11px;'><?=$this->translate('Return')?></span>
                    </td>
                  </tr>
                </table>

                <div>
                  <?php if( $item->status == 'cancelrequest' ) : ?>
                    <div style="text-align: center; padding-top: 5px;">
                      <a href="javascript:void(0);" onclick="Smoothbox.open(document.getElementById('reason_<?=$item->order_id?>'));">
                        <?=$this->translate("view canceling reason")?>
                      </a>
                    </div>
                    <div id="reason_<?=$item->order_id?>" class="admin-reason-popup">
                        <?=$item->cancel_reason?>
                    </div>
                  <?php endif;?>

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

                  <?php // set tracking ?>
                  <?php if( $item->status == 'approved' or 
                             ( $item->status == 'wait' and $item->owner_id == $this->viewer()->getIdentity())
                           ) : ?>
                    <div style="text-align: center; padding-top: 5px;">
                      <?=$this->htmlLink(array('route' => 'marketplace_general', 
                                                 'action' => 'set-tracking-number',
                                                 'order_id' => $item->order_id,
                                                 'refresh' => 1,
                                                 'format' => 'smoothbox' ), 
                                          $this->translate('Set Tracking'),
                                          array('class' => 'smoothbox'))?>
                    </div>
                  <?php endif; ?>

                  <?php // view tracking ?>
                  <?php if( $item->tracking_fedex or $item->tracking_ups ) : ?>
                    <div style="text-align: center; padding-top: 5px;">
                      <?=$this->htmlLink(array('route' => 'marketplace_general', 
                                             'action' => 'view-tracking-info',
                                             'order_id' => $item->order_id,
                                             'format' => 'smoothbox' ), 
                                          $this->translate('View Tracking'),
                                          array('class' => 'smoothbox'))?>
                    </div>
                  <?php endif;?>
                </div>

                <?php else : ?>
                    <div style="text-align:center">
                    <?php if( $item->status == 'punished' ) : ?>  
                        <?='<span style="color:red; font-weight: bold;">'.$this->translate('Punished')."</span>"?>
                    <?php else : ?>
                        <?='<span style="color:orange; font-weight: bold;">'.$this->translate('Canceled')."</span>"?>
                    <?php endif; ?>
                    </div>
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

            <td><?=str_replace(' ', '<br/>', $item->date)?></td>
          </tr>
        <?php endforeach; ?>
     
    </tbody>
  </table>
  <br />
  <div class='buttons'>
    <button type='submit' name="submit_button" value="change_status" style="float:right;"><?=$this->translate("Update Status")?></button>
     <?php /*if( $this->status_filter == 'sold' ) : ?>
        <button type='submit' name="submit_button" value="add_to_sold_file"><?=$this->translate("Add to Sold File")?></button>
     <?php endif; ?>
     <?php if( $this->status_filter == 'return' ) : ?>
        <button type='submit' name="submit_button" value="add_to_return_file"><?=$this->translate("Add to Return File")?></button>
     <?php endif;*/ ?>
     <?php if( $this->status_filter == 'done_failed' ) : ?>
        <button type='submit' name="submit_button" value="punish"><?=$this->translate("Punish")?></button>
     <?php endif; ?>
  </div>
  <input type="hidden" name="period_filter" value="<?=$this->period_filter?>" />
  <input type="hidden" name="status_filter" value="<?=$this->status_filter?>" />
</form>
</div>
<?php else:?>
      <br/>
      <div class="tip">
      <span><?=$this->translate("There are currently no reports.")?></span>
      </div>
      <?php endif;?>
