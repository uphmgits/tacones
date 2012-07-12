<?php
/**
 * @category   Application_Extensions
 * @package    Marketplace
 * @copyright  Copyright 2012
 */
?>

<form method="post" class="global_form_popup">
  <div>
    <h3><?php echo $this->translate("Hide Marketplace Listing?") ?></h3>
    <br />
    <p>
      <input type="hidden" name="confirm" value="<?=$this->marketplace_id?>"/>
      <button type='submit'><?=$this->translate("Hide")?></button>
      <?=$this->translate("or") ?>
      <a href='javascript:void(0);' onclick='parent.Smoothbox.close()'>
      <?=$this->translate("cancel") ?></a>
    </p>
  </div>
</form>

<?php/* if( @$this->closeSmoothbox ): ?>
  <script type="text/javascript">
    TB_close();
  </script>
<?php endif;*/ ?>
