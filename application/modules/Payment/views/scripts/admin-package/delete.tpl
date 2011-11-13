<?php
/**
 * SocialEngine
 *
 * @category   Application_Core
 * @package    Payment
 * @copyright  Copyright 2006-2010 Webligo Developments
 * @license    http://www.socialengine.net/license/
 * @version    $Id: delete.tpl 9256 2011-09-14 00:13:43Z shaun $
 * @author     John Boehr <j@webligo.com>
 */
?>

<?php if( $this->status ): ?>

  <?php echo $this->translate('Plan Deleted'); ?>
  <script type="text/javascript">
    parent.window.location.reload();
  </script>
<?php else: ?>
  <?php echo $this->form->render($this) ?>
<?php endif; ?>
