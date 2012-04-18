<?php
/**
 * SocialEngine
 *
 * @category   Application_Core
 * @package    User
 * @copyright  Copyright 2006-2010 Webligo Developments
 * @license    http://www.socialengine.net/license/
 * @version    $Id: facebook.tpl 8965 2011-06-01 00:11:33Z john $
 * @author     John Boehr <j@webligo.com>
 */
?>

<?php
  echo $this->navigation()
    ->menu()
    ->setContainer($this->navigation)
    ->setUlClass('admin_friends_tabs')
    ->render()
?>

<div class='settings'>
  <?php echo $this->form->render($this) ?>
</div>