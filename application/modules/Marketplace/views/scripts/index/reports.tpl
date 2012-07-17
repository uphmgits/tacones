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
  <?php /*
  <div class="tabs">
    <?php
      // Render the menu
      echo $this->navigation()
        ->menu()
        ->setContainer($this->navigation)
        ->render();
    ?>
  </div>
  */ ?>
</div>

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
    return confirm('<?php echo $this->string()->escapeJavascript($this->translate("Are you sure you want to delete the selected orders?")) ?>');
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

<div class='admin_results'>
  <div>
    <?php $reportCount = $this->paginator->getTotalItemCount() ?>
    <?=$this->translate(array("%s report.", "%s reports.", $reportCount), ($reportCount)) ?>
    <?=$this->translate("All prices a specified taking into shipping and handling cost (%s%%)", 
                         Engine_Api::_()->marketplace()->getInspectionFee(100));?>
  </div>
  <div>
    <?=$this->paginationControl($this->paginator); ?>
  </div>
</div>

<br />
<style>td.order-status > div { padding: 0 3px; text-align: center; }</style>
<div class="admin_table_form">
	<?php
       if($reportCount > 0 ):
	?>
  <style>table.admin_table td { white-space: normal !important; }</style>
  <table class='admin_table' width="100%" style="table-layout: fixed;">
    <thead>
      <tr>
        <th style='width: 5%;'><a href="javascript:void(0);" onclick="javascript:changeOrder('order_id', '<?=(($this->order == 'order_id') && ($this->order_direction == 'ASC')?'DESC':'ASC')?>');"><?php echo $this->translate("ID") ?></a></th>
        <th style='width: 15%;'><a href="javascript:void(0);" onclick="javascript:changeOrder('marketplace_id', '<?=(($this->order == 'marketplace_id') && ($this->order_direction == 'ASC')?'DESC':'ASC')?>');"><?php echo $this->translate("Marketplace") ?></a></th>
        <th style='width: 15%;'><a href="javascript:void(0);" onclick="javascript:changeOrder('owner_id', '<?=(($this->order == 'owner_id') && ($this->order_direction == 'ASC')?'DESC':'ASC')?>');"><?php echo $this->translate("Seller") ?></a></th>
        <th style='width: 15%;'><a href="javascript:void(0);" onclick="javascript:changeOrder('user_id', '<?=(($this->order == 'user_id') && ($this->order_direction == 'ASC')?'DESC':'ASC')?>');"><?php echo $this->translate("Buyer") ?></a></th>
        <th style='width: 20%;'><a href="javascript:void(0);" onclick="javascript:changeOrder('summ', '<?=(($this->order == 'summ') && ($this->order_direction == 'ASC')?'DESC':'ASC')?>');"><?php echo $this->translate("Price") ?></a></th>
        <th style='width: 10%;'><a href="javascript:void(0);" onclick="javascript:changeOrder('date', '<?=(($this->order == 'date') && ($this->order_direction == 'ASC')?'DESC':'ASC')?>');"><?php echo $this->translate("Date") ?></a></th>
        <th style='width: 10%;'><a href="javascript:void(0);" onclick="javascript:changeOrder('date', '<?=(($this->order == 'status') && ($this->order_direction == 'ASC')?'DESC':'ASC')?>');"><?php echo $this->translate("Status") ?></a></th>
        <th style='width: 10%;'><?php echo $this->translate("Options")?></th>
      </tr>
    </thead>
    <tbody>
    
        <?php $viewerId = $this->viewer()->getIdentity(); ?>
        <?php $now = time(); ?>
        <?php $canceltime = 3600 * Engine_Api::_()->getApi('settings', 'core')->getSetting('marketplace.canceltime', 24); ?>

        <?php foreach( $this->paginator as $item ): ?>
        <?php 
			$marketplace = $this->item('marketplace', $item->marketplace_id); 	
			if(!$marketplace){
				$ordersTable = Engine_Api::_()->getDbtable('orders', 'marketplace');
				$ordersTable->delete(array(
					'marketplace_id = ?' => $item->marketplace_id,
				));
				continue;
			}
		?>
          <tr>
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
                $display_name = $marketplace->getOwner()->getTitle();
                echo $this->htmlLink($marketplace->getOwner()->getHref(), $display_name, array('target' => '_blank'))
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
                <tr><td style="font-weight:bold;"><?=$this->translate("Total: ")?></td>
                    <td style="font-weight:bold;"><?=$item->summ * $item->count?></td></tr>
                <tr><td><?=$this->translate("Count: ")?></td><td><?=$item->count?></td></tr>
                <tr><td style="border: none;"><?=$this->translate("Item Total: ")?></td>
                    <td style="border: none;"><?=$item->summ?></td></tr>
              </table>  
            </td>
            <td><?=str_replace(' ', '<br/>', $item->date)?></td>
            <td class="order-status">
              <?php switch($item->status) { 
                case "wait"         : if( $item->tracking_fedex or $item->tracking_ups )
                                        echo "<div style='color: Khaki;'>{$this->translate('Item Sold')} (1/6)</div>"; 
                                      else
                                        echo "<div style='color: PaleGoldenRod;'>{$this->translate('Item sent to Upheels')} (2/6)</div>";
                                      break;
                case "approved"     :
                case "inprogress"   : echo "<div style='color: PaleGreen;'>{$this->translate('Inspection in progress')} (3/6)</div>";
                                      break;
                /*case "approved"     : echo "<div style='color: LightGreen;'>{$this->translate('Upheels passed')} (4/6)</div>"; 
                                      break;*/
                case "admin_sent"   : echo "<div style='color: LimeGreen;'>{$this->translate('Item	sent to buyer')} (5/6)</div>"; 
                                      break;
                case "done_failed"  :
                case "failed"       : echo "<div style='color: lightCoral;'>{$this->translate('Inspection Failed')} (6/6)</div>"; 
                                      break;
                case "cancelrequest":
                case "canceled"     :
                case "done_canceled": echo "<div style='color: lightCoral;'>{$this->translate('Order cancelled')} (6/6)</div>"; 
                                      break;
                case "done_sold"    :
                case "sold"         : echo "<div style='color: Green;'>{$this->translate('Complete')} (6/6)</div>"; 
                                      break;
                case "done_return"  :
                case "return"       : echo "<div style='color: orange;'>{$this->translate('Return')} (6/6)</div>"; 
                                      break;
              } ?>
            </td>
            <td style="font-size: 0.9em">
                <?php if($item->user_id == $viewerId and 
                          ( $now - strtotime($item->date) < $canceltime ) and 
                          ( $item->status != 'canceled' and 
                            $item->status != 'failed' and 
                            $item->status != 'cancelrequest' and
                            strripos($item->status, 'done') === false 
                          )
                         ) : ?>
                  <?=$this->htmlLink(array('route' => 'marketplace_general', 
                                             'action' => 'canceling',
                                             'order_id' => $item->order_id,
                                             'format' => 'smoothbox' ), 
                                      $this->translate('Cancel'),
                                      array('class' => 'smoothbox'))?>
                <?php endif; ?>

                <?php if($item->owner_id == $viewerId and $item->status == 'wait') : ?>
                  <br/>
                  <?=$this->htmlLink(array('route' => 'marketplace_general', 
                                             'action' => 'set-tracking-number',
                                             'order_id' => $item->order_id,
                                             'format' => 'smoothbox' ), 
                                      $this->translate('Set Tracking'),
                                      array('class' => 'smoothbox'))?>
                <?php endif; ?>

                <?php if( ($item->user_id == $viewerId or $item->owner_id == $viewerId) and 
                           ( $item->tracking_fedex or $item->tracking_ups ) ) : ?>
                  <br/>
                  <?=$this->htmlLink(array('route' => 'marketplace_general', 
                                             'action' => 'view-tracking-info',
                                             'order_id' => $item->order_id,
                                             'format' => 'smoothbox' ), 
                                      $this->translate('View Tracking'),
                                      array('class' => 'smoothbox'))?>
                <?php endif; ?>

                <?php if($item->user_id == $viewerId ) : ?>
                    <form method="post" action="" id="frmOrderPdf<?=$item->order_id?>">
                      <input type="hidden" name="get_order_pdf" value="<?=$item->order_id?>" />
                      <a href="javascript:void(0);" onclick="$('frmOrderPdf<?=$item->order_id?>').submit()">
                        <?=$this->translate('Print Invoice')?>
                      </a>
                    </form>
                <?php endif; ?>
            </td>
          </tr>
        <?php endforeach; ?>
     
    </tbody>
  </table>
  <br />
   <?php endif; ?>
</div>

