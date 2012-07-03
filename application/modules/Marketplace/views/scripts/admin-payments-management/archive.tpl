<h2><?php echo $this->translate("Transactions Archive") ?></h2>

<?php if( count($this->navigation) ): ?>
  <div class='tabs'>
    <?php
      // Render the menu
      echo $this->navigation()->menu()->setContainer($this->navigation)->render()
    ?>
  </div>
<?php endif; ?>
<br>
<?php if( count($this->subNavigation) ): ?>
  <div class='tabs'>
    <?php
      // Render the menu
      echo $this->navigation()->menu()->setContainer($this->subNavigation)->render()
    ?>
  </div>
<?php endif; ?>

<div class='admin_search' style="display: none; height: 20px;">
  <?=$this->formFilter->render($this)?>
</div>
<form method='post' action=''>
  <select name='status_filter'>
    <option value='all'   <?php if( $this->status_filter == 'all' )   echo "selected"?>><?=$this->translate("All")?></option>
    <option value='day'   <?php if( $this->status_filter == 'day' )   echo "selected"?>><?=$this->translate("Day")?></option>
    <option value='week'  <?php if( $this->status_filter == 'week' )  echo "selected"?>><?=$this->translate("Week")?></option>
    <option value='mount' <?php if( $this->status_filter == 'mount' ) echo "selected"?>><?=$this->translate("Mount")?></option>
    <option value='quarter' <?php if( $this->status_filter == 'quarter' ) echo "selected"?>><?=$this->translate("Quarter")?></option>
    <option value='year'  <?php if( $this->status_filter == 'year' )  echo "selected"?>><?=$this->translate("Year")?></option>
  </select>  
  <button type='submit' name="submit_button" value="change_status_filter"><?=$this->translate("Filter")?></button>
</form>

<?php if( count($this->paginator) ): ?>
<div class="admin_table_form">
<?php //<form id='multimodify_form' method="post" action="" > ?>
  <table class='admin_table' width="100%" style="table-layout: fixed;">
    <thead>
      <tr>
        <th style='width: 4%;'>
            <a href="javascript:void(0);" onclick="javascript:changeOrder('order_id', 'DESC');">
                <?=$this->translate("ID")?>
            </a>
        </th>
        <th style='width: 10%;'>
            <a href="javascript:void(0);" onclick="javascript:changeOrder('marketplace_id', 'DESC');">
                <?=$this->translate("Marketplace")?>
            </a>
        </th>
        <th class="wrap">
            <a href="javascript:void(0);" onclick="javascript:changeOrder('user_id', 'DESC');">
                <?=$this->translate("Seller")?>
            </a>
        </th>
        <th class="wrap">
            <a href="javascript:void(0);" onclick="javascript:changeOrder('owner_id', 'DESC');">
                <?=$this->translate("Buyer?") ?>
            </a>
        </th>
        <th class="wrap">
            <a href="javascript:void(0);" onclick="javascript:changeOrder('owner_id', 'DESC');">
                <?=$this->translate("Status") ?>
            </a>
        </th>
        <th style='width: 140px'>
            <a href="javascript:void(0);" onclick="javascript:changeOrder('summ', 'DESC');">
                <?=$this->translate("Summ")?>
            </a>
        </th>
        <th style='width: 15%;'>
            <a href="javascript:void(0);" onclick="javascript:changeOrder('date', 'DESC');">
                <?=$this->translate("Date purchased")?>
            </a>
        </th>
      </tr>
    </thead>
    <tbody>
      <?php foreach( $this->paginator as $item ): ?>
        <?php $itemId = $item->getIdentity(); ?>
        <?php $marketplace = $this->item('marketplace', $item->marketplace_id); ?>
        <?php $owner = $this->item('user', $item->owner_id);?>
        <?php $buyer = $this->item('user', $item->user_id);?>
        <tr>
          <td><?=$item->order_id?></td>
          <td>
            <?php if($marketplace) : ?>
              <?=$this->htmlLink($marketplace->getHref(), $marketplace->getTitle(), array('target' => '_blank'))?>
            <?php else : ?>
              <?=$this->translate('Deleted');?>
            <?php endif; ?>
          </td>
          <td><?=$this->htmlLink($owner->getHref(), $owner->getTitle(), array('target' => '_blank'))?></td>
          <td><?=$this->htmlLink($buyer->getHref(), $buyer->getTitle(), array('target' => '_blank'))?></td>
          <td>
            <?php switch($item->status) {
                      case "done_canceled": echo "Done 'Refund'"; break;
                      case "done_sold"    : echo "Done 'Complete'"; break;
                      case "done_return"  : echo "Done 'Return'"; break;
                      case "done_failed"  : echo "Done 'Failed'"; break;
                   } ?>
          </td>
          <td><?=$item->summ * $item->count?></td>
          <td><?=$item->date?></td>
        </tr>
      <?php endforeach; ?>
    </tbody>
  </table>
<?php //</form> ?>
</div>
<?php endif; ?>
