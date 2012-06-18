<style>td { padding: 5px 10px; }</style>
<div style="padding: 20px;">
  <table>
    <tr>
      <td><?=$this->translate('Name')?></td>
      <td><?=$this->info['name']?></td>
    </tr>
    <tr>
      <td><?=$this->translate('Email')?></td>
      <td><?=$this->info['email']?></td>
    </tr>  
    <tr>
      <td><?=$this->translate('Shipping Address')?></td>
      <td><?=$this->info['shipping_address']?></td>
    </tr>  
    <tr>
      <td><?=$this->translate('Phone')?></td>
      <td><?=$this->info['phone']?></td>
    </tr>  
    <tr>
      <td><?=$this->translate('Business Phone')?></td>
      <td><?=$this->info['business_phone']?></td>
    </tr>  
  </table>
</div>
