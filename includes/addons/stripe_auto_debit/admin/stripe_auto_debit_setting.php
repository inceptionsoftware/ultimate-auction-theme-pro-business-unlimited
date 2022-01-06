<?php

/** 
 *
 * @package Ultimate WooCommerce Auction PRO - business stripe add on
 * @author Nitesh Singh 
 * @since 1.0
 * 
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if (isset($_POST['uwa_auto_stripe_autodebit']) == 'Save Changes'){
	
	if(isset($_POST['uwa_stripe_card_register_page']) == "1"){							
		update_option('uwa_stripe_card_register_page', "yes");
	}
	else{
		update_option('uwa_stripe_card_register_page', "no");
	}
	if(isset($_POST['uwa_stripe_card_myaccount_page']) == "1"){				
		update_option('uwa_stripe_card_myaccount_page', "yes");
	} 
	else{
		update_option('uwa_stripe_card_myaccount_page', "no");
	}
    
	if(isset($_POST['uwa_stripe_charge_type'])){							
		update_option('uwa_stripe_charge_type', sanitize_text_field($_POST['uwa_stripe_charge_type']));
	}
	if(isset($_POST['uwa_stripe_charge_type_partially_type'])){
		update_option('uwa_stripe_charge_type_partially_type', 
			$_POST['uwa_stripe_charge_type_partially_type']);
	}
	
	if(isset($_POST['uwa_stripe_charge_type_partially_amt'])){
		update_option('uwa_stripe_charge_type_partially_amt', 
			$_POST['uwa_stripe_charge_type_partially_amt']);
	}
		
	if(isset($_POST['uwa_stripe_buyers_premium_enable']) == "1"){
		update_option('uwa_stripe_buyers_premium_enable', "yes");
	} 
	else{
		update_option('uwa_stripe_buyers_premium_enable', "no");
	}	
		
	if(isset($_POST['uwa_auto_stripe_text'])){
		update_option('uwa_auto_stripe_text', sanitize_text_field($_POST['uwa_auto_stripe_text']));
	}
	if(isset($_POST['uwa_auto_stripe_payment_text'])){
		update_option('uwa_auto_stripe_payment_text', 
			$_POST['uwa_auto_stripe_payment_text']);
	}	

	if(isset($_POST['uwa_wc_billing_myaccount_page']) == "1"){							
		update_option('uwa_wc_billing_myaccount_page', "yes");
	}
	else{
		update_option('uwa_wc_billing_myaccount_page', "no");
	}
		
	/* vendor */	
	if(isset($_POST['uwa_stripe_charge_type_vendor'])){
		update_option('uwa_stripe_charge_type_vendor', 
			$_POST['uwa_stripe_charge_type_vendor']);
	}

	if(isset($_POST['uwa_stripe_charge_type_partially_type_vendor'])){
		update_option('uwa_stripe_charge_type_partially_type_vendor', 
			$_POST['uwa_stripe_charge_type_partially_type_vendor']);
	}
	
	if(isset($_POST['uwa_stripe_charge_type_partially_amt_vendor'])){
		update_option('uwa_stripe_charge_type_partially_amt_vendor', 
			$_POST['uwa_stripe_charge_type_partially_amt_vendor']);
	}	
		
	if(isset($_POST['uwa_stripe_buyers_premium_vendor_enable']) == "1"){				
		update_option('uwa_stripe_buyers_premium_vendor_enable', "yes");
	} 
	else{
		update_option('uwa_stripe_buyers_premium_vendor_enable', "no");
	}		
}

    $uwa_stripe_charge_type_partially_amt = "";
	$uwa_stripe_charge_type_partially_type = "";
	$is_card_register_page = get_option('uwa_stripe_card_register_page', "no");
	$is_card_myaccount_page = get_option('uwa_stripe_card_myaccount_page', "yes");
	$is_uwa_stripe_buyers_premium = get_option('uwa_stripe_buyers_premium_enable', "no");
	$uwa_stripe_charge_type = get_option('uwa_stripe_charge_type');
	$uwa_stripe_charge_type_partially_type = get_option(
		'uwa_stripe_charge_type_partially_type');
	$uwa_stripe_charge_type_partially_amt = get_option(
		'uwa_stripe_charge_type_partially_amt');
	
	$is_card_register_page == "yes" ? $register_chk = "checked": $register_chk = "";
	$is_card_myaccount_page == "yes" ? $myaccount_chk = "checked": $myaccount_chk = "";
	$is_uwa_stripe_buyers_premium == "yes" ? $uwa_stripe_buyers_chk = "checked" : 
		$uwa_stripe_buyers_chk = "";
	
	$uwa_auto_stripe_text = get_option('uwa_auto_stripe_text',"Auto Debit Via Stripe");
	$uwa_auto_stripe_payment_text = get_option('uwa_auto_stripe_payment_text',
		"Your payment is already done!! please place the order");	
	
	$is_billing_myaccount_page = get_option('uwa_wc_billing_myaccount_page', "no");
	$is_billing_myaccount_page == "yes" ? $my_bill_chk = "checked": $my_bill_chk = "";
	
	//vendor setting 
	$uwa_stripe_charge_type_partially_amt_vendor = "";
	$uwa_stripe_charge_type_partially_type_vendor = "";
	$uwa_stripe_charge_type_vendor = get_option('uwa_stripe_charge_type_vendor');
	$uwa_stripe_charge_type_partially_type_vendor = get_option(
		'uwa_stripe_charge_type_partially_type_vendor');
	$uwa_stripe_charge_type_partially_amt_vendor = get_option(
		'uwa_stripe_charge_type_partially_amt_vendor');
	$is_uwa_stripe_buyers_premium_vendor = get_option(
		'uwa_stripe_buyers_premium_vendor_enable', "no");
	$is_uwa_stripe_buyers_premium_vendor == "yes" ? $uwa_stripe_buyers_vendor_chk = "checked": $uwa_stripe_buyers_vendor_chk = "";
	
	$uwa_enabled_addons_list = uwa_enabled_addons();
?>

<div class="uwa_main_setting_content">
	<form  method='post' class='uwa_auction_setting_style'>
		<table class="form-table">
			<tbody>
			<tr class="uwa_heading">
				<th colspan="2"><?php _e('Stripe Configuration', 'woo_ua' ); ?>
				</th>
			</tr>
			<tr>
				<th scope="row"></th>									 
				<td>
					<?php

						$wc_stripe_plugin_file = 'woocommerce-gateway-stripe/woocommerce-gateway-stripe.php';
						if ( file_exists( WP_PLUGIN_DIR . '/' . $wc_stripe_plugin_file ) && !class_exists( 'WC_Stripe' ) ) { 						
							
							echo sprintf( __( 'You just need to activate the <strong>%s</strong> to make it functional.', 'woo_ua' ), 
								'WooCommerce Stripe Gateway' );
							
						} 
						elseif( class_exists( 'WC_Stripe' )){

							$arr_stripe = get_option("woocommerce_stripe_settings");
							if(is_array($arr_stripe) && count($arr_stripe)){

								$is_enabled = $arr_stripe['enabled'];
								if($is_enabled == 'yes'){

									$is_testmode = $arr_stripe['testmode'];
									if($is_testmode == 'yes'){
										echo  __( "Stripe Test Mode Enable", "woo_ua" );

										$mode = "Test";
										$pk_test = $arr_stripe['test_publishable_key'];		
										$sk_test = $arr_stripe['test_secret_key'];
									}
									elseif ($is_testmode == 'no') {
										echo  __( "Stripe Live Mode Enable", "woo_ua" );
									}

									echo "<br>";
									/* echo sprintf( __( "We are used  %sWooCommerce Stripe Gateway%s for auto debit. you can manage here.", 
										"woo_ua" ), '<a target="_blank" 
										href="admin.php?page=wc-settings&tab=checkout&section=stripe">', '</a>' ); */
										
									echo sprintf( __( "We are using WooCommerce Stripe Gateway for auto debit. Please %sgo here and configure.%s", "woo_ua" ), '<a target="_blank"  href="admin.php?page=wc-settings&tab=checkout&section=stripe">', '</a>' );	
												
								} /* end of if - is_enabled = yes */ 
								else {
									echo sprintf( __( "You need to enable the %sEnable Stripe%s and insert stripe detail to make it functional.", 
										"woo_ua" ), '<a target="_blank" 
										href="admin.php?page=wc-settings&tab=checkout&section=stripe">', '</a>' );

								} /* end of else */
									
							} /* end of if - arr_stripe */						
						} 
						else {	
							
							echo sprintf( __( "To use this feature you need to install  %sWooCommerce Stripe Gateway%s. It is free. Once you have installed it, Configure your Stripe Settings in it.", "woo_ua" ),'<a target="_blank" href="https://wordpress.org/plugins/woocommerce-gateway-stripe/">', '</a>' );
							
						} /* end of else */
						
					?>						
						
				</td>
			</tr>				
			<tr class="uwa_heading">
				<th colspan="2"><?php _e('Display Settings for Credit Card', 'woo_ua' ); ?>
				</th>
			</tr>					
			<tr>
				<th scope="row"></th>									 
				<td>
					<input <?php echo $register_chk; ?> value="1" 
						name="uwa_stripe_card_register_page" type="checkbox">
						<?php _e( 'Display Credit Card Details on default Wordpress Register form.', 'woo_ua' ); ?>
				</td>
			</tr>
			<tr>
				<th scope="row"></th>							 
				<td class="uwaforminp">
					<input <?php echo $myaccount_chk; ?> value="1" 
						name="uwa_stripe_card_myaccount_page" type="checkbox">								
					<?php 
						$uwa_woo_acc_url = admin_url(
							'admin.php?page=wc-settings&tab=account');

						printf( __( 'Display Credit Card Details on WooCommerce My Account Page. Please enable <a href="%s">Registration on WooCommerce My Account</a> page for this setting to work.', 'woo_ua' ), 
							$uwa_woo_acc_url); 
					?>	
				</td>
			</tr> 					
			<tr>
				<th scope="row"></th>
				<td class="uwaforminp">
					<input <?php echo $my_bill_chk; ?> value="1" 
						name="uwa_wc_billing_myaccount_page" type="checkbox">								
					<?php _e( 'Capture Billing Address of Users while registering on WooCommerce My Account Page.', 'woo_ua' ); ?>
				</td>
			</tr> 
			<tr class="uwa_heading">
				<th colspan="2">
					<?php _e('Auto Debit Options for Admin Auction', 'woo_ua' ); ?></th>
			</tr>
			<tr>
				<th scope="row"><?php _e('Auto Debit Options', 'woo_ua' ); ?></th>					 
				<td>
			    	<input type="radio" <?php echo ($uwa_stripe_charge_type == 
			    		'uwa_stripe_charge_type_full') ?  "checked" : "" ;  ?> 
			    		name="uwa_stripe_charge_type" id="uwa_stripe_charge_type_full" 
			    		value="uwa_stripe_charge_type_full"> 
		     		<?php _e('Full Bid Amount', 'woo_ua');  ?>
					<span style="margin-right:20px;"></span>

					<input type="radio" <?php echo ($uwa_stripe_charge_type == 
						'uwa_stripe_charge_type_partially') ?  "checked" : "" ;  ?> 
						name="uwa_stripe_charge_type" id="uwa_stripe_charge_type_partially" value="uwa_stripe_charge_type_partially" > 
				 	<?php _e('Partial Bid Amount', 'woo_ua');  ?>				 
					<span style="margin-right:20px;"></span> 

					<input type="radio" <?php echo ($uwa_stripe_charge_type == 
						'uwa_stripe_charge_type_no') ?  "checked" : "" ;  ?> 
						name="uwa_stripe_charge_type" id="uwa_stripe_charge_type_no" 
						value="uwa_stripe_charge_type_no" > 
					<?php _e('No Auto Debit. Collect Payment on checkout page.', 'woo_ua');?>
				</td>
			</tr>
					
			<?php 
				$uwa_ctm_style = "";
				if($uwa_stripe_charge_type != 'uwa_stripe_charge_type_partially'){
					$uwa_ctm_style = 'style="display: none;"';
				}  
			?>
				
			<tr class="uwa_stripe_charge_type_partially_rates" <?php echo $uwa_ctm_style;?>>
				<th scope="row"></th>									 
				<td>
					<select name="uwa_stripe_charge_type_partially_type" 
						id="uwa_stripe_charge_type_partially_type">
						<option value="flatrate" <?php selected( 
							$uwa_stripe_charge_type_partially_type, "flatrate" ); ?> >
							<?php _e("Flat Rate", "woo_ua"); ?> </option>
						<option value="percentage" <?php selected( 
							$uwa_stripe_charge_type_partially_type, "percentage" ); ?>>
							<?php _e("Percentage", "woo_ua"); ?></option>
					</select>  
					<?php _e("Partially bid amount type.", "woo_ua"); ?>
				</td>
			</tr>

			<tr class="uwa_stripe_charge_type_partially_rates"  <?php echo $uwa_ctm_style;?>>
				<th scope="row"></th>
				<td class="uwaforminp">	
					<a href="" class="uwa_fields_tooltip" onclick="return false">
						<strong>?</strong>
						<span>
						<?php _e("If you choose 'Percentage' then the this entered value is treated as percentage of the total bid amount of the product, otherwise as fixed amount.", 'woo_ua');  ?>
						</span>
					</a>	
					
					<input name="uwa_stripe_charge_type_partially_amt"  type="number" 
						id="uwa_stripe_charge_type_partially_amt" value="<?php echo 
						$uwa_stripe_charge_type_partially_amt;?>" size="14"><?php printf( 
						__( "Enter Partially amount (in %s) or percentage.", 'woo_ua' ),
						get_woocommerce_currency_symbol()); ?>
				</td>
			</tr>

			<?php 				
				if(in_array("uwa_buyers_premium_addon", $uwa_enabled_addons_list)) { 
				?>
				<tr>
					<th scope="row"></th>									 
					<td>
						<input <?php echo $uwa_stripe_buyers_chk; ?> value="1" 
						name="uwa_stripe_buyers_premium_enable" type="checkbox">
						<?php _e( "Enable automatic charge of buyer's premium.", 'woo_ua' ); ?>
					</td>
				</tr>
				<?php } 
			?>
				 
			<tr class="uwa_heading">
				<th colspan="2"><?php _e('Auto Debit Options for Vendor Auction', 
					'woo_ua' ); ?></th>
			</tr>
			<tr>
				<th scope="row"></th>
				<td>
					<?php
						$wc_stripe_plugin_file = 'wc-multivendor-marketplace/wc-multivendor-marketplace.php';
						if ( file_exists( WP_PLUGIN_DIR . '/' . $wc_stripe_plugin_file ) && !class_exists( 'WCFMmp' ) ) { 
					
							echo sprintf( __( 'You just need to activate the <strong>%s</strong> to make it functional.', 'woo_ua' ), 
								'WCFM - WooCommerce Multivendor Marketplace' );
					
						} 
						elseif( class_exists( 'WCFMmp' ) ){
							$wcfm_withdrawal_options = get_option( 
								'wcfm_withdrawal_options', array());

							$wcfm_withdrawal_options = get_option( 
								'wcfm_withdrawal_options', array());

							if(isset($wcfm_withdrawal_options['payment_methods'])) {

								$withdrawal_payment_methods = $wcfm_withdrawal_options['payment_methods'];
							}
							else{
								$withdrawal_payment_methods = array();
							}

							$pages = get_option("wcfm_page_options");
							$store_url_setting = get_the_permalink($pages['wc_frontend_manager_page_id']).'settings/';
							if(is_array($withdrawal_payment_methods) &&
								in_array("stripe_split", $withdrawal_payment_methods)){									
								$is_enabled = $arr_stripe['enabled'];
								$withdrawal_test_mode = $wcfm_withdrawal_options['test_mode'];
								if($withdrawal_test_mode == 'yes'){								
									if($withdrawal_test_mode == 'yes'){
										echo  __( "Stripe Split Pay Test Mode Enable", 
											"woo_ua" );

										$mode = "Test";
										$pk_test = $arr_stripe['test_publishable_key'];
										$sk_test = $arr_stripe['test_secret_key'];
									}
									elseif ($is_testmode == 'no') {
										echo  __( "Stripe Split Pay Live Mode Enable", 
											"woo_ua" );
												
									} /* end of if - testmode */

									echo "<br>";								
									echo sprintf( __( "We are used  %sWCFM - WooCommerce Multivendor Marketplace%s for auto debit. you can manage here.", "woo_ua" ), '<a target="_blank" href="'.$store_url_setting.'">', '</a>' );	
										
								} /* end of if - withdrawal_test_mode */ 								
							} /* end of if - stripe_split */ 					
							else {
									
								echo sprintf( __( "You need to enable the %sStripe Split Pay%s in WCFM Marketplace and insert stripe detail to make it functional.", "woo_ua" ), '<a target="_blank" href="'.$store_url_setting.'">', 
									'</a>' );
							}
					
						} /* end of elseif */
						else {
					
							echo sprintf( __( "You just need to install the %sWCFM - WooCommerce Multivendor Marketplace%s to make it functional.", "woo_ua" ), '<a target="_blank" href="https://wordpress.org/plugins/wc-multivendor-marketplace/">', '</a>' );
						}
					
					?>
					
				</td>
			</tr>			 
			<tr>
				<th scope="row"><?php _e('Auto Debit Options', 'woo_ua' ); ?></th>		
				<td>
				    <input type="radio" <?php echo ($uwa_stripe_charge_type_vendor == 
				    	'uwa_stripe_charge_type_full_vendor') ?  "checked" : "" ;  ?> name="uwa_stripe_charge_type_vendor" 
				    	id="uwa_stripe_charge_type_full_vendor" 
				    	value="uwa_stripe_charge_type_full_vendor" > 
			     	<?php _e('Full Bid Amount', 'woo_ua');  ?>
					<span style="margin-right:20px;"></span> 	

					<input type="radio" <?php echo ($uwa_stripe_charge_type_vendor == 
						'uwa_stripe_charge_type_partially_vendor') ?  "checked" : "" ;  ?> name="uwa_stripe_charge_type_vendor" 
						id="uwa_stripe_charge_type_partially_vendor" 
						value="uwa_stripe_charge_type_partially_vendor" > 
					<?php _e('Partial Bid Amount', 'woo_ua');  ?>						 
					<span style="margin-right:20px;"></span>

					<input type="radio" <?php echo ($uwa_stripe_charge_type_vendor == 
						'uwa_stripe_charge_type_no_vendor') ?  "checked" : "" ;  ?> 
						name="uwa_stripe_charge_type_vendor" 
						id="uwa_stripe_charge_type_no_vendor" 
						value="uwa_stripe_charge_type_no_vendor" > 
						<?php _e('No Auto Debit. Collect Payment on checkout page.', 
							'woo_ua');  ?>
				</td>
			</tr> 
				
			<?php 
				$uwa_ctm_style_vendor = "";
				if($uwa_stripe_charge_type_vendor != 
					'uwa_stripe_charge_type_partially_vendor'){
					$uwa_ctm_style_vendor = 'style="display: none;"';
				}  
			?>
				
			<tr class="uwa_stripe_charge_type_partially_rates_vendor" 
				<?php echo $uwa_ctm_style_vendor;?>>
				<th scope="row"></th>									 
				<td>
					<select name="uwa_stripe_charge_type_partially_type_vendor" 
						id="uwa_stripe_charge_type_partially_type_vendor">
						<option value="flatrate" <?php selected( 
							$uwa_stripe_charge_type_partially_type_vendor, "flatrate" );?>><?php _e("Flat Rate", "woo_ua"); ?> </option>
						<option value="percentage" <?php selected( 
							$uwa_stripe_charge_type_partially_type_vendor, "percentage" );?>><?php _e("Percentage", "woo_ua"); ?></option>
					</select>  
					<?php _e("Partially bid amount type.", "woo_ua"); ?>	
				</td>
			</tr>
			<tr class="uwa_stripe_charge_type_partially_rates_vendor"  
				<?php echo $uwa_ctm_style_vendor;?>>
				<th scope="row"></th>
				<td class="uwaforminp">	
					<a href="" class="uwa_fields_tooltip" onclick="return false">
						<strong>?</strong>
						<span>
						<?php _e("If you choose 'Percentage' then the this entered value is treated as percentage of the total bid amount of the product, otherwise as fixed amount.", 'woo_ua');  ?>
						</span>
					</a>	
					
					<input name="uwa_stripe_charge_type_partially_amt_vendor"  
						type="number" id="uwa_stripe_charge_type_partially_amt_vendor" 
						value="<?php echo $uwa_stripe_charge_type_partially_amt_vendor;?>" size="14"><?php printf( __( "Enter Partially amount (in %s) or percentage.", 'woo_ua' ),get_woocommerce_currency_symbol()); ?>
				</td>
			</tr> 

			<?php 
				
				if(in_array("uwa_buyers_premium_addon", $uwa_enabled_addons_list)) { ?>
					<tr>
						<th scope="row"></th>									 
						<td>
							<input <?php echo $uwa_stripe_buyers_vendor_chk; ?> 
								value="1" name="uwa_stripe_buyers_premium_vendor_enable" 
								type="checkbox">
							<?php _e( "Enable automatic charge of buyer's premium.", 
							'woo_ua' ); ?>
						</td>
					</tr>
				 <?php } 
			?>
				 
			<tr class="uwa_heading">
				<th colspan="2"><?php _e('Other Settings', 'woo_ua' ); ?></th>
			</tr>

			<tr>
				<th scope="row"></th>
				<td class="uwaforminp">	
					<input type="text" name="uwa_auto_stripe_text" class="regular-text" 
					value="<?php echo $uwa_auto_stripe_text;?>" id="uwa_auto_stripe_text">
					<?php _e( "This text will shown on check out page in Your order section.", 'woo_ua' ); ?>											
				</td>
			</tr>
				
			<tr>
				<th scope="row"></th>
				<td class="uwaforminp">	
					<input type="text" name="uwa_auto_stripe_payment_text" 
					class="regular-text" value="<?php echo $uwa_auto_stripe_payment_text;?>" 
					id="uwa_auto_stripe_payment_text">					
				  	<?php _e( "This text will show on check out page before billing form.", 
				   		'woo_ua' ); ?>											
		   		</td>
			</tr>
				
			<tr class="submit">
				<th colspan="2">
					<input type="submit" id="uwa_auto_stripe_autodebit"  
						name="uwa_auto_stripe_autodebit" class="button-primary" 
						value="<?php _e('Save Changes','woo_ua');?>" />
				</th>
			</tr>

			</tbody>						
		</table>
	</form>
</div>

<script type="text/javascript">
	jQuery("document").ready(function($){

		$('input:radio[name=uwa_stripe_charge_type]').change(function() {
			if (this.value == 'uwa_stripe_charge_type_partially') {
				$('.uwa_stripe_charge_type_partially_rates').css("display", 
					"table-row");
			}
			else {
				$('.uwa_stripe_charge_type_partially_rates').css("display", 
					"none");
			}
		});

		$('input:radio[name=uwa_stripe_charge_type_vendor]').change(function() {
			if (this.value == 'uwa_stripe_charge_type_partially_vendor') {
				$('.uwa_stripe_charge_type_partially_rates_vendor').css("display", 
				 	"table-row");
			}
			else {
				$('.uwa_stripe_charge_type_partially_rates_vendor').css("display", 
				 	"none");
			}
		});

		$("#uwa_auto_stripe_autodebit").click(function(){

			var p_mode =  $("input[name='uwa_stripe_charge_type']:checked").val();	

			if(p_mode == 'uwa_stripe_charge_type_partially'){			
				var p_rate = $("#uwa_stripe_charge_type_partially_amt").val();
				var premium_type = $('#uwa_stripe_charge_type_partially_type').val();

				if(!p_rate){
					alert("Please Enter Partially amount or percentage.");
					return false;
				}

				if(isNaN(p_rate)){
					alert("Please enter only numeric values in Partially amount");
					return false;
				}

				if(p_rate <= 0){
					alert("Please enter values more than 0 in Partially amount");
					return false;
				}
				
				if(premium_type == "percentage"){
					if(p_rate > 100){
						alert("In Partially rate More than 100 is not allowed");
						$("#uwa_stripe_charge_type_partially_amt").val("");
						return false;
					}
				}				
			}
		});
	});
</script>