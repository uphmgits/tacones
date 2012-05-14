<?php


/**
 * Radcodes - SocialEngine Module
 *
 * @category   Application_Extensions
 * @package    Review
 * @copyright  Copyright (c) 2009-2010 Radcodes LLC (http://www.radcodes.com)
 * @license    http://www.radcodes.com/license/
 * @version    $Id$
 * @author     Vincent Van <vincent@radcodes.com>
 */
 
 
?>

  <form method="post" class="global_form_popup">
    <div>
      <h3><?php echo $this->translate("Featured Review?") ?></h3>
      <p>
        <?php echo $this->translate("Would you like to mark this review as featured?") ?>
      </p>
      <br />
      <p>
        <input type="hidden" name="confirm" value="<?php echo $this->review_id?>"/>

        <button type='submit' name="featured" value="yes"><?php echo $this->translate("Yes") ?></button>
        <button type='submit' name="featured" value="no"><?php echo $this->translate("No") ?></button>

        <?php echo $this->translate("or") ?>
        <a href='javascript:void(0);' onclick='javascript:parent.Smoothbox.close()'>
        <?php echo $this->translate("cancel") ?></a>
      </p>
    </div>
  </form>

<?php if( @$this->closeSmoothbox ): ?>
<script type="text/javascript">
  TB_close();
</script>
<?php endif; ?>
