<style>td { padding: 5px }</style>
<div style="padding:20px;">
  <h1><?=$this->translate('Request for Canceling')?></h1>
  <?php if($this->error) : ?>
    <br/>
    <div class="error_message">
        <span><?=$this->translate('Error:')?> <?=$this->translate('Please, indicate the reason for refusal')?></span>
    </div>
    <br/>
  <?php endif; ?>
  <form action="" method="post" onsubmit="return confirm('Are you sure?');">
    <table>
      <tr>
        <td><?=$this->translate('Order ID')?></td>
        <td><?=$this->order['order_id']?></td>
      </tr>
      <tr>
        <td><?=$this->translate('Item')?></td>
        <td><?=$this->marketplace->getTitle()?></td>
      </tr>
      <tr>
        <td><?=$this->translate('Canceling Reason')?></td>
        <td><textarea name="reason"></textarea></td>
      </tr>
      <tr>
        <td></td>
        <td><button type="submit" name="cancelThis"><?=$this->translate('Cancel order')?></button></td>
      </tr>
    </table>
   </form>
</div>
