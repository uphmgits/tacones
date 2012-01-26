<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Album
 * @copyright  Copyright 2006-2010 Webligo Developments
 * @license    http://www.socialengine.net/license/
 * @version    $Id: index.tpl 7991 2010-12-08 18:17:43Z char $
 * @author     Jung
 */
?>
<script type="text/javascript">
function multiApprove()
{
  return confirm("<?php echo $this->translate('Are you sure you want to approve the selected photos?');?>");
}

function multiDelete()
{
  return confirm("<?php echo $this->translate('Are you sure you want to delete the selected photos?');?>");
}

function selectAll()
{
  var i;
  var multidelete_form = $('multidelete_form');
  var inputs = multidelete_form.elements;
  for (i = 1; i < inputs.length - 1; i++) {
    inputs[i].checked = inputs[0].checked;
  }
}
</script>

<h2>
  <?php echo $this->translate('Verify Photos') ?>
</h2>

<?php if( count($this->navigation) ): ?>
  <div class='tabs'>
    <?php
      // Render the menu
      //->setUlClass()
      echo $this->navigation()->menu()->setContainer($this->navigation)->render()
    ?>
  </div>
<?php endif; ?>

<br />
<?php if( count($this->paginator) ): ?>

<form id="multidelete_form" action="<?php echo $this->url();?>" method="POST">
  <table class='admin_table'>
    <thead>
      <tr>
        <th class='admin_table_short'><input onclick="selectAll()" type='checkbox' class='checkbox' /></th>
        <th class='admin_table_short'>ID</th>
        <th><?php echo $this->translate('Preview') ?></th>
        <th><?php echo $this->translate('Owner') ?></th>
        <th><?php echo $this->translate('Date') ?></th>
        <th><?php echo $this->translate('Options') ?></th>
      </tr>
    </thead>
    <tbody>
        <?php foreach ($this->paginator as $item): ?>
          <tr>
            <td><input type='checkbox' class='checkbox' name='checked_<?php echo $item->photo_id;?>' value="<?php echo $item->photo_id ?>"/></td>
            <td><?php echo $item->getIdentity() ?></td>
            <td>
              <a class="thumbs_photo" href="<?=$item->getHref()?>">
                <img src="<?=$item->getPhotoUrl('thumb.normal'); ?>" style="width: 48px"/></td>
              </a>
            <td><?=$this->user($item->user_id)->getTitle() ?></td>
            <td><?php echo $this->locale()->toDateTime($item->creation_date) ?></td>
            <td>
                <?=$this->htmlLink(
                    array('route' => 'default', 'module' => 'marketplace', 'controller' => 'admin-approve', 'action' => 'approve', 'id' => $item->photo_id),
                    $this->translate("Approve"),
                    array('class' => 'smoothbox'))
                ?>
              |
                <?=$this->htmlLink(
                    array('route' => 'default', 'module' => 'marketplace', 'controller' => 'admin-approve', 'action' => 'delete', 'id' => $item->photo_id),
                    $this->translate("delete"),
                    array('class' => 'smoothbox'))
                ?>
            </td>
          </tr>
        <?php endforeach; ?>
    </tbody>
  </table>

  <br/>

  <div class='buttons'>
    <button type='submit' name='approve' onclick="return multiApprove()">
      <?php echo $this->translate('Approve Selected') ?>
    </button>
  </div>
  <br/>
  <div class='buttons'>
    <button type='submit' name='delete' onclick="return multiDelete()">
      <?php echo $this->translate('Delete Selected') ?>
    </button>
  </div>
</form>

<br />

<div>
  <?php echo $this->paginationControl($this->paginator); ?>
</div>

<?php else: ?>
  <div class="tip">
    <span>
      <?php echo $this->translate("There are no photos posted by your members yet.") ?>
    </span>
  </div>
<?php endif; ?>
