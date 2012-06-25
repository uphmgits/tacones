<h2><?php echo $this->translate("Complete transactions management") ?></h2>

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

<style> table .zend_form dt { display: none; } </style>

<?php if( count($this->paginator) ): ?>
<div class="admin_table_form">
<?php //<form id='multimodify_form' method="post" action="" > ?>
  <table class='admin_table' width="100%" style="table-layout: fixed;">
    <thead>
      <tr>
        <th style='width: 3%;'><input onclick="selectAll()" type='checkbox' class='checkbox'></th>
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
        <th style='width: 140px'>
            <a href="javascript:void(0);" onclick="javascript:changeOrder('summ', 'DESC');">
                <?=$this->translate("Summ")?>
            </a>
        </th>
        <th style='width: 12%;'>
            <a href="javascript:void(0);" onclick="javascript:changeOrder('date', 'DESC');">
                <?=$this->translate("Date purchased")?>
            </a>
        </th>
        <th style='width: 240px'>
            <?=$this->translate("Options") ?>
        </th>

      </tr>
    </thead>
    <tbody>
      <?php foreach( $this->paginator as $item ): ?>
        <?php $itemId = $item->getIdentity(); ?>
        <?php $marketplace = $this->item('marketplace', $item->marketplace_id); ?>
        <?php $owner = $this->item('user', $item->owner_id);?>
        <?php $buyer = $this->item('user', $item->user_id);?>
        <?php $isPPButton = false; ?>

        <?php $paypal = new Marketplace_Api_Payment(true); ?>
        <?php if( $owner and $marketplace and !empty( $marketplace->business_email ) ) : ?>
          <?php $adminAddress = Engine_Api::_()->getApi('settings', 'core')->getSetting('core.mail.from', 'admin@' . $_SERVER['HTTP_HOST']); ?>
          <?php $paypal->setBusinessEmail( $marketplace->business_email ); ?>
          <?php $paypal->setPayer($adminAddress, 0); ?>
          <?php $paypal->setAmount( $item->summ ); ?>
          <?php $paypal->setNumber( $item->order_id ); ?>
          <?php $paypal->addItem(array('item_name' => $owner->getTitle() . "({$marketplace->getTitle()})")); ?>
          <?php $paypal->setControllerUrl("http://" . $_SERVER['HTTP_HOST'] . $this->url(array(), 'marketplace_extended', true) . '/payment'); ?>
          <?php $isPPButton = true; ?>
        <?php endif; ?>

        <tr>
          <td></td>
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
          <td><?=$item->summ * $item->count?></td>
          <td><?=$item->date?></td>
          <td>  
            <?php if( $isPPButton ) echo $paypal->form(); ?>
          </td>
        </tr>
      <?php endforeach; ?>
    </tbody>
  </table>
<?php //</form> ?>
</div>
<?php endif; ?>
