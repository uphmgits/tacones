<?php
/**
 * 
 *
 * @category   Application_Extensions
 * @package    Marketplace
 * @copyright  Copyright 2010 
 * * 
 * @version    $Id: categories.tpl 7244 2010-09-01 01:49:53Z john $
 * 
 */
?>

<h2><?php echo $this->translate("Marketplace Plugin") ?></h2>

<?php if( count($this->navigation) ): ?>
  <div class='tabs'>
    <?php
      // Render the menu
      echo $this->navigation()->menu()->setContainer($this->navigation)->render()
    ?>
  </div>
<?php endif; ?>

  <div class='clear'>
    <div class='settings'>
    <form class="global_form">
      <div>
        <h3><?php echo $this->translate("Marketplace Listing Categories") ?> </h3>
        <p class="description">
          <?php echo $this->translate("MARKETPLACES_VIEWS_SCRIPTS_ADMINSETTINGS_CATEGORIES_DESCRIPTION") ?>
        </p>
        <p>

            <b> <a href="<?php echo $this->url(array(), 'marketplace_category', true) ?>">Categories</a> => <?php Engine_Api::_()->marketplace()->tree_print($this->a_tree,$this->url); ?></b>
        </p> <br />
          <?php if(count($this->categories)>0):?>

         <table class='admin_table'>
          <thead>

            <tr>
              <th><?php echo $this->translate("Category Name") ?></th>
              <th><?php echo $this->translate("Owner") ?></th>
              <th><?php echo $this->translate("Number of Times Used") ?></th>
              <th><?php echo $this->translate("Options") ?></th>
            </tr>

          </thead>
          <tbody>

            <?php  foreach ($this->categories as $category): ?>
              <tr>
                <td> <a href="<?php echo $this->url(array('category_id'=>$category->category_id), 'marketplace_category', true) ?>"><?php echo $category->category_name?></a></td>
                <td><?php echo $category->user_id?></td>
                <td><?php echo $category->getUsedCount()?></td>
                <td>
                  <?php echo $this->htmlLink(array('route' => 'admin_default', 'module' => 'marketplace', 'controller' => 'settings', 'action' => 'edit-category', 'id' =>$category->category_id), $this->translate('edit'), array(
                    'class' => 'smoothbox',
                  )) ?>
                  |
                  <?php echo $this->htmlLink(array('route' => 'admin_default', 'module' => 'marketplace', 'controller' => 'settings', 'action' => 'delete-category', 'id' =>$category->category_id), $this->translate('delete'), array(
                    'class' => 'smoothbox',
                  )) ?>

                </td>
              </tr>
            <?php endforeach; ?>
          </tbody>
        </table>
      <?php else:?>
      <br/>
      <div class="tip">
      <span><?php echo $this->translate("There are currently no categories.") ?></span>
      </div>
      <?php endif;?>
        <br/>
        <?php echo $this->htmlLink(array('route' => 'admin_default', 'module' => 'marketplace', 'controller' => 'settings', 'action' => 'add-category','category_id'=>$this->category_id), $this->translate('Add New Category'), array(
          'class' => 'smoothbox buttonlink',
          'style' => 'background-image: url(application/modules/Core/externals/images/admin/new_category.png);')) ?>

        <?php
//        echo $this->htmlLink(array('route' => 'marketplace_addcategory',  'category_id' => $this->category_id), $this->translate('Add New Category'), array(
//          'class' => 'smoothbox buttonlink',
//          'style' => 'background-image: url(application/modules/Core/externals/images/admin/new_category.png);')) ?>
    </div>
    </form>
    </div>
  </div>
     