<?php
/**
 * SocialEngine
 *
 * @category   Application_Core
 * @package    Core
 * @copyright  Copyright 2006-2010 Webligo Developments
 * @license    http://www.socialengine.net/license/
 * @version    $Id: delete-menu.tpl 9382 2011-10-14 00:41:45Z john $
 * @author     John
 */
?>

<?php if( $this->form ): ?>

  <?php echo $this->form->setAttrib('class', 'global_form_popup')->render($this) ?>

<?php else: ?>

  <div><?php echo $this->translate("Deleted") ?></div>

  <script type="text/javascript">
    setTimeout(function() {
      parent.window.location.href = '<?php echo $this->url(array('action' => 'index', 'name' => null)) ?>';
      parent.Smoothbox.close();
    }, 500);
  </script>

<?php endif; ?>