<?php
/**
 * 
 *
 * @category   Application_Extensions
 * @package    Marketplace
 * @copyright  Copyright 2010 
 * * 
 * @version    $Id: create.tpl 7244 2010-09-01 01:49:53Z john $
 * 
 */
?>

<div class='layout_common'>
	<div class = 'layout_middle'>
	  <h2>Import your eBay Listings</h2>
	</div>
	<br/>
	<form  method="post">
	<table border=1>
	  <tr>
		<th>ListingID</th>
		<th>Picture</th>
		<th>Title</th>
		<th>Category</th>
	
		<th>Price</th>
		<th>Specifications</th>
		
		<th>Description</th>
	  </tr>
	  <?php $row=0; foreach($this->details as $eBayListingID => $details) { ?>
	  <tr>
	  <td><input disabled="disabled" type="text" value="<?php echo $eBayListingID ?>" /></td>
	  <td> <img src="<?php echo $details['picture'] ?>" height="132" width="120" /></td>
	  <input type="hidden" name="Rows[<?php echo $row ?>][mainphoto]" value="<?php echo $details['picture'] ?>" /> </td>
	  <td><input name="Rows[<?php echo $row ?>][title]" type="text" value="<?php echo $details['Title'] ?>" /></td>
	  <td><input name="Rows[<?php echo $row ?>][uph_category]" disabled="disabled" type="text" value="<?php echo $details['uph_category'] ?>" /></td>
	  <input name="Rows[<?php echo $row ?>][category_id]" type="hidden" value="<?php echo $details['uph_category_id'] ?>" />
	  <td><input name="Rows[<?php echo $row ?>][price]" type="text" value="<?php echo $details['uph_price'] ?>" /></td>
	  
	  
	  <!-- <td> <input type="file" name="Rows[<?php echo $row ?>][photo]" id="photo"  value="http://i.ebayimg.sandbox.ebay.com/00/s/MjI1WDIyNQ==/$(KGrHqNHJFQE+Tcoj(WrBP3R17QNTQ~~60_1.JPG?set_id=8800004005" /></td>  -->
	  <td>
		 <!--  Category specifications  -->
		 <?php foreach($details['specs'] as $specitem) { ?>
	      <?php foreach($specitem as $specname => $specvalue) { ?>
	    	<input type="text" value="<?php echo $specname ?>" /> 
	    	<input type="text" value="<?php echo $specvalue['option_value'] ?>" /><br />
	    	<input name="Rows[<?php echo $row ?>][fields][0_0_<?php echo $specvalue['field_id'] ?>]" type="hidden" value="<?php echo $specvalue['option_id'] ?>" /> 
	      <?php } ?>
    	 <?php } ?>
	  </td>
	
	 
	 <td><input name="Rows[<?php echo $row++ ?>][body]" type="text" value="<?php echo $details['Description'] ?>" /></td> 
	  <tr> </tr>
	  <?php } ?>
		</tr>
	<input type="submit" value="import" />
	</form>
	</table>
    
  </div>

	