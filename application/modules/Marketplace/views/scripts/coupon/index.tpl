<?php
/**
 * 
 *
 * @category   Application_Extensions
 * @package    Marketplace
 * @copyright  Copyright 2010 
 * * 
 * @version    $Id: index.tpl 7250 2010-09-01 07:42:35Z john $
 * 
 */
?>

<div class="headline">
	<h2><?php echo $this->translate("Marketplace Coupons") ?></h2>

	<?php if( count($this->navigation) ): ?>
	  <div class='tabs'>
		<?php
		  // Render the menu
		  echo $this->navigation()->menu()->setContainer($this->navigation)->render()
		?>
	  </div>
	<?php endif; ?>
</div>

<div style="margin: 20px 0;">
	<?php echo $this->htmlLink(
		array('route' => 'default', 'module' => 'marketplace', 'controller' => 'coupon', 'action' => 'create'),
		$this->translate("Create New Coupon"),
		array('class' => 'smoothbox marketplace_button')) ?>
</div>
<?php if( count($this->paginator) ): ?>

<table class='admin_table'>
  <thead>
    <tr>
      <th><?php echo $this->translate("Title") ?></th>
      <th><?php echo $this->translate("Coupon Code") ?></th>
      <th><?php echo $this->translate("Discount") ?></th>
      <th><?php echo $this->translate("Options") ?></th>
    </tr>
  </thead>
  <tbody>
    <?php foreach ($this->paginator as $item): ?>
      <tr>
        <td><?php echo $item->getTitle() ?></td>
        <td><?php echo $item->code ?></td>
        <td><?php echo $item->percent ?>%</td>
        <td>
          <?php echo $this->htmlLink(
            array('route' => 'default', 'module' => 'marketplace', 'controller' => 'coupon', 'action' => 'edit', 'id' => $item->getIdentity()),
            $this->translate("edit"),
            array('class' => 'smoothbox')) ?>
          |
          <?php echo $this->htmlLink(
            array('route' => 'default', 'module' => 'marketplace', 'controller' => 'coupon', 'action' => 'delete', 'id' => $item->getIdentity()),
            $this->translate("delete"),
            array('class' => 'smoothbox')) ?>
        </td>
      </tr>
    <?php endforeach; ?>
  </tbody>
</table>

<br/>
<div>
  <?php echo $this->paginationControl($this->paginator); ?>
</div>

<?php else:?>
  <div class="tip">
    <span>
      <?php echo $this->translate("There are no coupons.") ?>
    </span>
  </div>
<?php endif; ?>
