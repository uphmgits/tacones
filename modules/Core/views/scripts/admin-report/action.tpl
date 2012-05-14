<?php
/**
 * SocialEngine
 *
 * @category   Application_Core
 * @package    Core
 * @copyright  Copyright 2006-2010 Webligo Developments
 * @license    http://www.socialengine.net/license/
 * @version    $Id: delete.tpl 7533 2010-10-02 09:42:49Z john $
 * @author     John Boehr <j@webligo.com>
 */
?>

<?php echo $this->form->setAttrib('class', 'global_form_popup')->render($this) ?>

<?php if( @$this->closeSmoothbox ): ?>
  <script type="text/javascript">
    parent.Smoothbox.close();
  </script>
<?php endif; ?>
