<?php 

$js2 = '( function($) {

	$(document).ready( function() {
	//alert("ready");
		$("#signin").validate({
			rules: {	
				email: {
					required: true,
					email: true
				}
			},
			messages: {
				email: {
					email:    "valid email needed",
					required: "valid email needed"
				}
			},
			errorClass: "signin_error",
			
		});
    } ) })( jQuery )';


$this->headScript()->prependScript($js2);
 
?>
<div id="welcome_container">
	<div id="welcome_wrapper">
		<div id="welcome_header-wrapper">
			<div id="welcome_logo">logo</div>
		<div id="header">
			<div class="welcome-note">
				<div class="email">
					<form name="request" action="go.php" method="post">
					<label class="label" for="email">Request invitation</label>
					<input type="text" value="email address" onfocus="if(this.value == 'email address') { this.value = ''; }" name="email">
					<input type="image" border="0" src="/imgs/submit-bg.png" name="submit-invite">
					</form>
				</div>
				<p class="welcome-para">
					You live for fashion and you love to change it up.
					<br>
					You're ready to sell your YSL Ostrich bag to make room for the Herm&#232;s "Constance" you've had your eye on. 
					Follow trend setters and browse their closets to snap up that exclusive item at a steal. 
					Share tips and trends with a clothes-minded community.
					<br>
					Keep your closet a step above. Welcome to Upheels.
				</p>
			</div>
		</div>
		</div>
	<div class="clear"></div>
	<br/><br/>
	<div class="welcome-note">
		<div class="email">
		<h2>Already an Upheeler?</h2><h2>Simply Sign-In to begin your Upheeling!</h2>
		<br/>
			<form id = "signin" name="signin" action="/login" method="post">
			 <label id="email_label" class="label" for="email">Email Address</label> 
			
			<input type="text" value="email address" onfocus="if(this.value == 'email address') { this.value = ''; }" name="email">
			<!-- <label for="email" class="error" generated="true"></label>   -->
			<br/><br/>
			<label class="label" for="password">Password</label>
			
			<input type="password" name="password">
			
			<input type="image" border="0" src="/imgs/submit-bg.png" name="submit-signin">
			</form>
		</div>
		
	</div>
		
		
	</div>
	
	</div>
	
</div>
