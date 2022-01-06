<?php

/**
 *
 * @package Ultimate WooCommerce Auction ADDON buyer preminum setting tab
 * @author Nitesh Singh 
 * @since 1.0
 * 
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if(isset($_POST['uwa-buyers_premium-submit']) == 'Save Changes'){

		if (isset($_POST['uwa_buyers_premium_for'])) {
			update_option('uwa_buyers_premium_for', sanitize_text_field($_POST['uwa_buyers_premium_for']));	
		}

		if (isset($_POST['uwa_buyers_premium_type'])) {
			update_option('uwa_buyers_premium_type', sanitize_text_field($_POST['uwa_buyers_premium_type']));	
		}		

		if (isset($_POST['uwa_buyers_premium_rate'])) {
			update_option('uwa_buyers_premium_rate', ($_POST['uwa_buyers_premium_rate']));	// save float value
		}

		if (isset($_POST['uwa_buyers_min_premium'])) {
			update_option('uwa_buyers_min_premium', ($_POST['uwa_buyers_min_premium']));	
		}

		if (isset($_POST['uwa_buyers_max_premium'])) {
			update_option('uwa_buyers_max_premium', ($_POST['uwa_buyers_max_premium']));	
		}

		/*
		if(isset($_POST['uwa_buyers_premium_text'])){
			update_option('uwa_buyers_premium_text', sanitize_text_field($_POST['uwa_buyers_premium_text']));
		}*/
		
		if(isset($_POST['uwa_buyers_premium_tab_hide']) == "1"){							
			update_option('uwa_buyers_premium_tab_hide', "yes");
		}
		else{
			update_option('uwa_buyers_premium_tab_hide', "no");
		}
		
		if(isset($_POST['uwa_buyers_premium_aft_btn']) == "1"){							
			update_option('uwa_buyers_premium_aft_btn', "yes");
		}
		else{
			update_option('uwa_buyers_premium_aft_btn', "no");
		}		

} /* end of if - save changes */

	$uwa_buyers_premium_rate = "";
	$uwa_buyers_premium_for = get_option('uwa_buyers_premium_for', "uwa_admin");
	$uwa_buyers_premium_type = get_option('uwa_buyers_premium_type', "flat");
	$uwa_buyers_premium_rate = get_option('uwa_buyers_premium_rate', "");	

	$uwa_buyers_min_premium = get_option('uwa_buyers_min_premium', "");	
	$uwa_buyers_max_premium = get_option('uwa_buyers_max_premium', "");	
	
	//$uwa_buyers_premium_text = get_option('uwa_buyers_premium_text', "Buyer's Premium");	
	$is_hide_tab = get_option('uwa_buyers_premium_tab_hide', "yes");
	$uwa_aft_btn = get_option('uwa_buyers_premium_aft_btn', "no");
	$is_hide_tab == "yes" ? $is_hide_tab = "checked": $is_hide_tab = "";
	$uwa_aft_btn == "yes" ? $uwa_aft_btn = "checked": $uwa_aft_btn = "";

?>

<div class='wrap'>
<div id='icon-tools' class='icon32'></br></div>	
	<form id="uwa_buyers_premium_form" class="auction_settings_section_style" action="" 
		method="POST">

		<table class="form-table">
		
			<tr class="uwa_heading">
				<th colspan="2"><?php _e("Buyer's Premium", 'woo_ua' ); ?></th>
			</tr>
		
			<tr valign="top">
				<th scope="row">
					<label for="uwa_buyers_premium_for">
						<?php _e("Give Buyer's Premium to", "woo_ua"); ?>
					</label>
				</th>
				<td>
					<input type="radio" <?php echo ($uwa_buyers_premium_for== 'uwa_admin') ?  "checked" : "" ;  ?> name="uwa_buyers_premium_for"  
						value="uwa_admin" > 
					<?php _e('Admin', 'woo_ua');  ?>
					<span style="margin-right:20px;"></span> 	
				     	
					<input type="radio" <?php echo ($uwa_buyers_premium_for== 'uwa_owners') ?  "checked" : "" ;  ?> name="uwa_buyers_premium_for" 
						value="uwa_owners" >  
					<?php _e('Auction Owners', 'woo_ua');  ?>				
				</td>				
			</tr>
		
			<tr valign="top">
				<th scope="row">
					<label for="uwa_buyers_premium_type">
						<?php _e("Buyer's Premium Type", "woo_ua"); ?>
					</label>
				</th>
				<td>
					<input type="radio" <?php echo ($uwa_buyers_premium_type== 'flat') ?  
						"checked" : "" ;  ?> name="uwa_buyers_premium_type" 
						id="uwa_buyers_premium_type" value="flat" > 
					<?php _e('Flat Rate', 'woo_ua');  ?>
					<span style="margin-right:20px;"></span>
				     	
					<input type="radio" <?php echo ($uwa_buyers_premium_type== 'percentage') 
						?  "checked" : "" ;  ?> name="uwa_buyers_premium_type" 
						id="uwa_stripe_charge_type_partially" value="percentage" >  
					<?php _e('Percentage', 'woo_ua');  ?>								
				</td>
			</tr>

			<tr valign="top">
				<th scope="row">
					<label for="uwa_buyers_premium_rate"><?php _e("Fee Amount", "woo_ua"); ?></label>
				</th>

				<td>				
					<input name="uwa_buyers_premium_rate" type="text" 
						id="uwa_buyers_premium_rate"  value="<?php echo 
						$uwa_buyers_premium_rate; ?>"  size="14" />
					<?php printf( __("Based on your selection above this field is Amount for Flat Rate or Percentage.", 'woo_ua' ),get_woocommerce_currency_symbol()); ?>
				</td>
			</tr>


			<tr valign="top" class="uwa_min_max">
				<th scope="row">
					<label class="lbl_min_max" for="uwa_buyers_min_premium"><?php _e("Minimum Premium Amount", "woo_ua"); ?></label>
				</th>
				<td><div class="dv_min_max">
					<?php printf( "%s", get_woocommerce_currency_symbol()); ?>
					<input name="uwa_buyers_min_premium" type="number" 
						id="uwa_buyers_min_premium"  min="1"
						title="used for percentage only"
						disabled   value="<?php echo 
						$uwa_buyers_min_premium; ?>"  size="14" />
					<?php printf( __("This amount is minimum buyer's premium amount in unit of currency that will be applicable. If the amount calculated in percentage is below this minimum amount then this amount will be charged.", 'woo_ua' ),get_woocommerce_currency_symbol()); ?>
				</div>
				</td>
			</tr>


			<tr valign="top" class="uwa_min_max">
				<th scope="row">
					<label class="lbl_min_max" for="uwa_buyers_max_premium"><?php _e("Maximum Premium Amount", "woo_ua"); ?></label>
				</th>
				<td><div class="dv_min_max">
					<?php printf( "%s", get_woocommerce_currency_symbol()); ?>				
					<input name="uwa_buyers_max_premium" type="number" 
						id="uwa_buyers_max_premium" min="1"
						title="used for percentage only"
						disabled value="<?php echo 
						$uwa_buyers_max_premium; ?>"  size="14" />
					<?php printf( __("This amount is maximum buyer's premium amount in unit of currency that will be applicable. If the amount calculated in percentage is above this maximum amount then this amount will be charged.", 'woo_ua' ),get_woocommerce_currency_symbol()); ?>
				</div>
				</td>
			</tr>
			
			<tr class="uwa_heading">
				<th colspan="2"><?php _e('Other Settings', 'woo_ua' ); ?></th>
			</tr>

			<!--	
			<tr>
				<th scope="row"></th>
				<td class="uwaforminp">	
				 <input type="text" name="uwa_buyers_premium_text" class="regular-text" value="<?php echo esc_attr($uwa_buyers_premium_text);?>" id="uwa_buyers_premium_text">
				<?php _e( "This text will shown on check out page in Your order section.", 'woo_ua' ); ?>											
			   </td>
			</tr>	-->	

			<tr>
				<th scope="row"></th>
				<td>
					<input <?php echo $is_hide_tab; ?> value="1" 
						name="uwa_buyers_premium_tab_hide" type="checkbox">
					<?php _e( "Hide Buyer's Premium tab on auction detail page.", 
						'woo_ua' ); ?>
				</td>
			</tr>

			<tr>
				<th scope="row"></th>
				<td>
					<input <?php echo $uwa_aft_btn; ?> value="1" 
						name="uwa_buyers_premium_aft_btn" type="checkbox">
					<?php _e( "Display Buyer's Premium after bid button on auction detail page.", 'woo_ua' ); ?>
						
				</td>
			</tr>		
			
			<tr>
				<td colspan="2" valign="top" scope="row">								
					<input type="submit" id="uwa-buyers_premium-submit" 
						name="uwa-buyers_premium-submit" class="button-primary" 
						value="<?php _e('Save Changes','woo_ua');?>" />
				</td>
			</tr>

		</table>
	</form>
</div>

<script type="text/javascript">
	jQuery("document").ready(function($){

		uwa_disabled_min_max();

		$("#uwa-buyers_premium-submit").click(function(){

				var p_rate = $("#uwa_buyers_premium_rate").val();		
				var premium_type = $("input[name='uwa_buyers_premium_type']:checked").val();


				if(!p_rate){
					alert("Please enter premium fee amount.");
					$("#uwa_buyers_premium_rate").focus();
					return false;
				}

				if(isNaN(p_rate)){
					alert("Please enter only numeric values for premium fee amount");
					$("#uwa_buyers_premium_rate").focus();
					return false;
				}
				
				if(p_rate <= 0){
					alert("Please enter values more than 0");
					$("#uwa_buyers_premium_rate").focus();
					return false;
				}

				if(premium_type == "percentage"){

					var min_val = $("#uwa_buyers_min_premium").val();
					var max_val = $("#uwa_buyers_max_premium").val();

					if(p_rate > 100){
						alert("In premium fee amount more than 100 is not allowed");
						$("#uwa_buyers_max_premium").val("");
						return false;
					}
			
					if(max_val != ""){
						if(parseInt(min_val) >= parseInt(max_val)){
							alert("Maximum premium must be greater than Minimum premium");
							$("#uwa_buyers_max_premium").focus();
							return false;
						}
	
					}

				}

		});

		$("input[name='uwa_buyers_premium_type']").click(function(){
			uwa_disabled_min_max();
		});


		function uwa_disabled_min_max(){
	 		var premium_type = $("input[name='uwa_buyers_premium_type']:checked").val();

			if(premium_type == "percentage"){
				$("#uwa_buyers_min_premium").removeAttr("disabled");
				$("#uwa_buyers_max_premium").removeAttr("disabled");
				//$( "tr.uwa_min_max" ).css( "border", "3px double red" );
				$(".lbl_min_max").css("color", "inherit");
				$(".dv_min_max").css("color", "inherit");
				
			}
			else{

				$("#uwa_buyers_min_premium").attr("disabled", "disabled");
				$("#uwa_buyers_max_premium").attr("disabled", "disabled");	
				//$( "tr.uwa_min_max" ).css( "border", "none" );
				$(".lbl_min_max").css("color", "lightgrey");
				$(".dv_min_max").css("color", "lightgrey");

				$("#uwa_buyers_min_premium").val("");
				$("#uwa_buyers_max_premium").val("");
			}

		}

});
</script>