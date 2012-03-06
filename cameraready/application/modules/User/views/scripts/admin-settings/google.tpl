<?php
/**
 * SocialEngine
 *
 * @category   Application_Core
 * @package    User
 * @copyright  Copyright 2006-2010 Webligo Developments
 * @license    http://www.socialengine.net/license/
 * @version    $Id: google.tpl 9262 2011-09-16 00:17:25Z john $
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
