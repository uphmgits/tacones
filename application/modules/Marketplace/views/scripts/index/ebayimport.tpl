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
<?php 

$js2 = '( function($) {
	$(document).ready( function() {
		$("form#ebayimport input[type=text]").each(function() {
			$(this).width($(this).parent().width());
			$(this).css("color", "#ca278c");
			
		});
		
		$("#ebayimport").submit(function(e) {
			$.blockUI({ message: "<br/><h2>Importing your eBay listings.<p>This could take several minutes.</p></h2>Please Wait...<br/><br/><img src=/imgs/spinner_squares_circle.gif>",  css: { border: "none", opacity: "0.7", width: "300px", height: "170px", backgroundColor: "#AA0078", color: "#fff" } });
			return true;
		});
		
		$.validator.addMethod(
	        "regex",
	        function (value, element, regexp) {
	            return this.optional(element) || regexp.test(value);
	        }
		);
	
		$("#ebayinput").validate({
			rules: {
				ebaysellerid: {
					required: true,
					regex: /^([A-Za-z0-9.*_-])*$/ 
				},
				
				postfrom: "required",
				postthru: "required"
			},
			messages: {
				ebaysellerid: "<div class=\"ebayinput-jqerrors-label\">Please enter valid eBay ID</div>",
				postfrom: "<div class=\"ebayinput-jqerrors-label\">Please select From date</div>",
				postthru: "<div class=\"ebayinput-jqerrors-label\">Please select To date</div>"
			},
			errorClass: "ebayinput-jqerrors-label",
			
			 
			submitHandler: function(form) {
			$.blockUI({ message: "<br/><h2>Retrieving your eBay listings.<p>This could take several minutes.</p></h2>Please Wait...<br/><br/><img src=/imgs/spinner_squares_circle.gif>",  css: { border: "none", opacity: "0.6", width: "300px", height: "170px", backgroundColor: "#AA0078", color: "#fff" } });
			form.submit();
            }
		});
        $("#spin").click(function() { 
        	validateInputs();
            //$.blockUI({ message: "<br/><h2>Retrieving your eBay listings.<p>This could take several minutes.</p></h2>Please Wait...<br/><br/><img src=/imgs/spinner_squares_circle.gif>",  css: { border: "none", opacity: "0.6", width: "300px", height: "170px", backgroundColor: "#AA0078", color: "#fff" } }); 
            
        });
        $(function() {
			$("#postfrom").datepicker();
		});
		$(function() {
			$("#postthru").datepicker();
		});
	
        
        function validateInputs() {
        	var ebayrege = /^([A-Za-z0-9.*_-])*$/;
        	var ebaysellerid = $("#ebaysellerid").val();
        	if(ebayrege.test(ebaysellerid)) {
        		alert("matched");
        	}
        	else {
        		alert("invalid id");
        	}
        }
        
    } ) })( jQuery )';


$js = '( function($) {
	$(document).ready( function() {
        $("#spin").click(function() { 
            $.blockUI({ message: "<br/><h2>Retrieving your eBay listings.<p>This could take several minutes.</p></h2>Please Wait...<br/><br/><img src=/imgs/spinner_squares_circle.gif>",  css: { border: "none", opacity: "0.6", width: "300px", height: "170px", backgroundColor: "#AA0078", color: "#fff" } }); 
            
        });

        $("#ebayinput").submit(function() { 
            $.blockUI({ message: "<br/><h2>Retrieving your eBay listings.<p>This could take several minutes.</p></h2>Please Wait...<br/><br/><img src=/imgs/spinner_squares_circle.gif>",  css: { border: "none", opacity: "0.6", width: "300px", height: "170px", backgroundColor: "#AA0078", color: "#fff" } });
            return true; 
            
        }); 
        
    } ) })( jQuery )';


$this->headScript()->prependScript($js2);
 
?>





<div class='layout_common'>

	<div class='layout_middle'>
<?php if ($this->ebay_initialinput === true || $this->errorEbayInitialInput === true){ ?>
    <div class="marketplace-create-container">
        
    
       <div class="marketplace-ebayimport-input-container">
              
              
              	<div><h2>Start your Ebay Import here</h2>
              	<b>Please enter data below</b>
              	</div>
            
             <div class="marketplace-ebayimport-input-form">
             <form id="ebayinput"   method="post">
                  <?=$this->form->ebaysellerid->render($this)?>
                  
                  <?=$this->form->postfrom->render($this)?>
                  <?=$this->form->postthru->render($this)?>
                  
                  <input type ="hidden" name="ebay_initialinput" value="1">
                  <div class="marketplace-create-buttons">
                
                     <a href="javascript:void(0)" onclick="$$('.marketplace-ebayimport-input-form input').set('value', ''); $$('.marketplace-ebayimport-input-form').set('value', ''); $$('.marketplace-ebayimport-input-form').set('value', '');"><?=$this->form->clear->render($this)?></a> 
                     
                     
                      <?=$this->form->import->render($this)?>
                  </div>
             </form>
              </div>
          
            <div id="ebayimport-info-id" class="ebayimport-info">
            We make it really easy for you to bring your eBay listings over to Upheels with click of a button. <p><br/>Simply enter your eBay sellerID and the date range in which you posted those listings on eBay. 
            <p><br/>Upheels will retrieve your listings, have you an opportunity to review the retrieved data and with a click of a button, those listings would be posted on Upheels.
           
            </div>
         <div class="ebayimport-input-errors">   
            <?php foreach( $this->form->getElements() as $element ): ?>
		      <?php $error = $element->getMessages();?>
		      <?php if( !empty($error) ) : ?>
		        <?php foreach( $error as $key => $value ): ?>
		          <div class="error_message">
		              <span><?=$this->translate('Error:')?> <?=$element->getLabel()?> - <?=$value?></span>
		          </div>
		        <?php endforeach; ?>
		      <?php endif; ?>
		  <?php endforeach; ?>
        </div> 
         </div>
    <?php } else { ?>
    
    
    
	<div class = 'layout_middle'>
	  <h2>Import your eBay Listings</h2>
	
	<br/>
	<div class="ebayreview-row-lid">
	eBay ListingID
	</div>
	
	<div class="ebayreview-row-title">
	Title
	</div>
	<div class="ebayreview-row-price">
	Price
	</div>
	<div class="ebayreview-row-cat">
	Category
	</div>
	
	
	<div class="ebayreview-row-desc">
	Description
	</div>
	
	
	<div class="ebayreview-newrow">
	
	</div>
	
	<form  id= "ebayimport" method="post">
<?php $row=0; foreach($this->details as $eBayListingID => $details) { ?>
	<div class="ebayreview-row-lid">
	<input disabled="disabled" type="text" value="<?php print $eBayListingID ?>" />
	</div>
	<div class="ebayreview-row-title">
	<input name="Rows[<?php echo $row ?>][title]" type="text" value="<?php echo $details['Title'] ?>" />
	</div>
	<div class="ebayreview-row-price">
	<input name="Rows[<?php echo $row ?>][price]" type="text" value="<?php echo $details['uph_price'] ?>" />
	</div>
	<div class="ebayreview-row-cat">
	<input name="Rows[<?php echo $row ?>][uph_category]" disabled="disabled" type="text" value="<?php echo $details['uph_category'] ?>" />
	</div>
	
	
	<div class="ebayreview-row-desc">
	<input name="Rows[<?php echo $row ?>][body]" type="text" value="<?php echo $details['Description'] ?>" />
	</div>
	
	<div class="ebayreview-row-picture-container">
	
	
	<?php $piccnt=0; foreach($details['pictures'] as $picindex => $picurl) { ?>
		<div class="ebayreview-row-picture">
			<img src="<?php echo $details['pictures'][$picindex] ?>" height="132" width="120" />
	  	</div>
	  	<input type="hidden" name="Rows[<?php echo $row ?>][pictures][<?php echo $picindex ?>]" value="<?php echo $picurl ?>" />
	<?php }?>
	<?php $row++;  ?>
	</div>
	
	
	<div class="ebayreview-newrow">	</div>
<?php } ?>

	</div>
	
	
	<div id="import-element" class="form-element">
	<button id="import" type="submit" name="import">Start Importing</button>
	</div>
	</form>
	</div>
    <?php } ?>
  </div>

	