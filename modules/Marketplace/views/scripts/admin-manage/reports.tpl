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
    if (!inputs[i].disabled) {
      inputs[i].checked = inputs[0].checked;
    }
  }
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
<form id='multimodify_form' method="post" action="" onSubmit="multiModify()">
  <table class='admin_table'>
    <thead>
      <tr>
        <th style='width: 1%;'><input onclick="selectAll()" type='checkbox' class='checkbox'></th>
        <th style='width: 1%;'><a href="javascript:void(0);" onclick="javascript:changeOrder('order_id', 'DESC');"><?php echo $this->translate("ID") ?></a></th>
        <th style='width: 20%;'><a href="javascript:void(0);" onclick="javascript:changeOrder('marketplace_id', 'DESC');"><?php echo $this->translate("Marketplace") ?></a></th>
        <th style='width: 20%;'><a href="javascript:void(0);" onclick="javascript:changeOrder('user_id', 'DESC');"><?php echo $this->translate("Seller") ?></a></th>
        <th style='width: 20%;'><a href="javascript:void(0);" onclick="javascript:changeOrder('owner_id', 'DESC');"><?php echo $this->translate("Who buy?") ?></a></th>
        <th style='width: 10%;'><a href="javascript:void(0);" onclick="javascript:changeOrder('summ', 'DESC');"><?php echo $this->translate("Summ") ?></a></th>
        <th style='width: 10%;'><a href="javascript:void(0);" onclick="javascript:changeOrder('summ', 'DESC');"><?php echo $this->translate("Status") ?></a></th>
        <th style='width: 10%;'><a href="javascript:void(0);" onclick="javascript:changeOrder('count', 'DESC');"><?php echo $this->translate("Count") ?></a></th>
        <th style='width: 20%;'><a href="javascript:void(0);" onclick="javascript:changeOrder('date', 'DESC');"><?php echo $this->translate("Date") ?></a></th>

      </tr>
    </thead>
    <tbody>
      
        <?php foreach( $this->paginator as $item ): ?>
          <tr>
            <td>
              <input <?php if ( $item->to_file_transfer > 0 or $item->status != 1 ) echo 'disabled';?> name="modify_<?=$item->getIdentity()?>" value="<?=$item->getIdentity()?>" type="checkbox" class="checkbox" />
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
            
            <td><?php echo $item->summ; ?></td>
            <td><?php if( $item->to_file_transfer == 0) : ?>
                    <?php switch( $item->status ) {
                          //case 0 : echo $this->translate('In Progress'); break;
                          case 1 : echo '<span style="color:green">'.$this->translate('Sold')."</span>"; break;
                          case 2 : echo '<span style="color:orange">'.$this->translate('Return')."</span>"; break;
                          case 3 : echo '<span style="color:red">'.$this->translate('Not Legitimate')."</span>"; break;
                          case 9 : echo '<span style="color:red; font-weight: bold;">'.$this->translate('Punished')."</span>"; break;
                    } ?>
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
            <td><?php echo $item->date; ?></td>
          </tr>
        <?php endforeach; ?>
     
    </tbody>
  </table>
  <br />
  <div class='buttons'>
    <button type='submit' name="submit_button" value="add_to_file"><?php echo $this->translate("Add to Sold File") ?></button>
  </div>
</form>
</div>
<?php else:?>
      <br/>
      <div class="tip">
      <span><?php echo $this->translate("There are currently no reports.") ?></span>
      </div>
      <?php endif;?>
