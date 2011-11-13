<?php
/**
 * SocialEngine
 *
 * @category   Application_Core
 * @package    Core
 * @copyright  Copyright 2006-2010 Webligo Developments
 * @license    http://www.socialengine.net/license/
 * @version    $Id: index.tpl 9382 2011-10-14 00:41:45Z john $
 * @author     John Boehr <j@webligo.com
 */
?>

<h2><?php echo $this->translate("System Services") ?></h2>


<?php if( count($this->navigation) ): ?>
  <div class='tabs'>
    <?php
      // Render the menu
      //->setUlClass()
      echo $this->navigation()->menu()->setContainer($this->navigation)->render()
    ?>
  </div>
<?php endif; ?>


<?php if( 'CORE_VIEWS_SCRIPTS_ADMINSERVICES_INDEX_DESCRIPTION' 
    !== ($desc = $this->translate("CORE_VIEWS_SCRIPTS_ADMINSERVICES_INDEX_DESCRIPTION"))): ?>
  <p>
    <?php echo $desc ?>
  </p>

  <br />
<?php endif ?>


<?php if( !empty($this->formFilter) ): ?>
  <div class='admin_search'>
    <?php echo $this->formFilter->render($this) ?>
  </div>

  <br />
<?php endif ?>


<?php if( $this->paginator->count() > 1 ): ?>
  <?php echo $this->paginationControl($this->paginator) ?>
  <br />
<?php endif; ?>


<table class="admin_table">
  <thead>
    <tr>
      <?php /*
      <th style="width: 1%;">
        <input type="checkbox" onclick="$$('input[type=checkbox][name]').set('checked', this.get('checked'));" />
      </th>
       */ ?>
      <th style="width: 1%;">
        <a href="javascript:void(0)" onclick="handleSort('service_id')">
          <?php echo $this->translate('ID') ?>
        </a>
      </th>
      <th style="width: 1%;">
        <a href="javascript:void(0)" onclick="handleSort('type')">
          <?php echo $this->translate('Type') ?>
        </a>
      </th>
      <th style="width: 1%;">
        <a href="javascript:void(0)" onclick="handleSort('name')">
          <?php echo $this->translate('Adapter') ?>
        </a>
      </th>
      <th style="width: 1%;">
        <a href="javascript:void(0)" onclick="handleSort('profile')">
          <?php echo $this->translate('Profile Name') ?>
        </a>
      </th>
      <th style="width: 1%;">
        <a href="javascript:void(0)" onclick="handleSort('enabled')">
          <?php echo $this->translate('Enabled?') ?>
        </a>
      </th>
      <th>
        <?php echo $this->translate('Options') ?>
      </th>
    </tr>
  </thead>
  <tbody>
    <?php foreach( $this->paginator as $item ):
      $serviceType = $this->serviceTypes[$item['type']];
      $serviceProvider = $this->serviceProviders[$item['type']][$item['name']];
      ?>
      <tr>
        <?php /*
        <td class="nowrap">
          <input type="checkbox" name="selection[]" value="<?php echo $task->task_id ?>" />
        </td>
         */ ?>
        <td class="nowrap">
          <?php echo $this->locale()->toNumber($item['service_id']) ?>
        </td>
        <td class="nowrap">
          <?php echo $this->translate(!empty($serviceType['title']) ? $serviceType['title'] : 'Unknown') ?>
        </td>
        <td class="nowrap">
          <?php echo $this->translate(!empty($serviceProvider['title']) ? $serviceProvider['title'] : 'Unknown') ?>
        </td>
        <td class="nowrap">
          <?php echo $this->translate(!empty($item['profile']) ? $item['profile'] : 'default') ?>
        </td>
        <td class="nowrap">
          <?php echo $this->translate(!empty($item['enabled']) ? 'Yes' : 'No') ?>
        </td>
        <td class="admin_table_options">
          <span class="sep">|</span>
          <?php echo $this->htmlLink(array('reset' => false, 'action' => 'change', 'service_id' => $item['service_id']), $this->translate('change')) ?>
          <span class="sep">|</span>
          <?php echo $this->htmlLink(array('reset' => false, 'action' => 'edit', 'service_id' => $item['service_id']), $this->translate('edit')) ?>
        </td>
      </tr>
    <?php endforeach; ?>
  </tbody>
</table>