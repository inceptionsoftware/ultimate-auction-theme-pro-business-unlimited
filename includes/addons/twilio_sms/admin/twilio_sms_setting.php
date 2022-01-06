<?php

/**
 * Extra Functions file
 *
 * @package Ultimate WooCommerce Auction PRO - business- addon - twilio sms
 * @author Nitesh Singh 
 * @since 1.0
 * 
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if(isset($_POST['uwa-settings-submit']) == 'Save Changes'){	
	
		if(isset($_POST['uwa_twilio_sms_enabled_register_page'])){	
			update_option('uwa_twilio_sms_enabled_register_page', "yes");
		} 
		else{
			update_option('uwa_twilio_sms_enabled_register_page', "no");
		}

		if(isset($_POST['uwa_twilio_sms_enabled_myaccount_page'])){	
			update_option('uwa_twilio_sms_enabled_myaccount_page', "yes");
		} 
		else{
			update_option('uwa_twilio_sms_enabled_myaccount_page', "no");
		}

		if($_POST['uwa_twilio_sms_sid']){
			update_option('uwa_twilio_sms_sid', sanitize_text_field($_POST['uwa_twilio_sms_sid']));
		}
		else {
			delete_option('uwa_twilio_sms_sid');
		}

		if($_POST['uwa_twilio_sms_token']){
			update_option('uwa_twilio_sms_token', sanitize_text_field($_POST['uwa_twilio_sms_token']));
		}
		else {
			delete_option('uwa_twilio_sms_token');
		}

		if($_POST['uwa_twilio_sms_from_number']){
			update_option('uwa_twilio_sms_from_number', 
				$_POST['uwa_twilio_sms_from_number']);
		}
		else {
			delete_option('uwa_twilio_sms_from_number');
		}	
		
		if(isset($_POST['uwa_twilio_sms_placed_bid_enabled'])){	
			update_option('uwa_twilio_sms_placed_bid_enabled', "yes");
		} 
		else{
			update_option('uwa_twilio_sms_placed_bid_enabled', "no");
		}
		
		if(isset($_POST['uwa_twilio_sms_outbid_enabled'])){	
			update_option('uwa_twilio_sms_outbid_enabled', "yes");
		} 
		else{
			update_option('uwa_twilio_sms_outbid_enabled', "no");
		}
		
		if(isset($_POST['uwa_twilio_sms_won_enabled'])){	
			update_option('uwa_twilio_sms_won_enabled', "yes");
		} 
		else{
			update_option('uwa_twilio_sms_won_enabled', "no");
		}
		
		if(isset($_POST['uwa_twilio_sms_ending_soon_enabled'])){
			update_option('uwa_twilio_sms_ending_soon_enabled', "yes");
		} 
		else{
			update_option('uwa_twilio_sms_ending_soon_enabled', "no");
		}
		
		if(isset($_POST['uwa_twilio_sms_placed_bid_template'])){
			update_option('uwa_twilio_sms_placed_bid_template', 
				sanitize_text_field($_POST['uwa_twilio_sms_placed_bid_template']));
		}
		if(isset($_POST['uwa_twilio_sms_outbid_template'])){
			update_option('uwa_twilio_sms_outbid_template', 
				sanitize_text_field($_POST['uwa_twilio_sms_outbid_template']));
		}
		if(isset($_POST['uwa_twilio_sms_won_template'])){
			update_option('uwa_twilio_sms_won_template', 
				sanitize_text_field($_POST['uwa_twilio_sms_won_template']));
		}
		if(isset($_POST['uwa_twilio_sms_placed_bid_template'])){
			update_option('uwa_twilio_sms_ending_soon_template', 
				sanitize_text_field($_POST['uwa_twilio_sms_ending_soon_template']));
		}		
		if(isset($_POST['uwa_twilio_sms_ending_soon_time'])){
			update_option('uwa_twilio_sms_ending_soon_time', 
				absint($_POST['uwa_twilio_sms_ending_soon_time']));
		}

} /* end of if - save changes */


	$uwa_twilio_sms_enabled_register_page = get_option(
		'uwa_twilio_sms_enabled_register_page', "no");
	$uwa_twilio_sms_enabled_register_page == "yes" ? 
		$uwa_twilio_sms_enabled_register_page_checked = "checked": 
		$uwa_twilio_sms_enabled_register_page_checked = "";
	$uwa_twilio_sms_enabled_myaccount_page = get_option( 
		'uwa_twilio_sms_enabled_myaccount_page', "yes" );
	$uwa_twilio_sms_enabled_myaccount_page == "yes" ? 
		$uwa_twilio_sms_enabled_myaccount_page_checked = "checked": 
		$uwa_twilio_sms_enabled_myaccount_page_checked = "";
	
	$uwa_twilio_sms_sid = get_option('uwa_twilio_sms_sid');
	$uwa_twilio_sms_token = get_option('uwa_twilio_sms_token');
	$uwa_twilio_sms_from_number = get_option('uwa_twilio_sms_from_number');
	
	$uwa_twilio_sms_placed_bid_enabled = get_option('uwa_twilio_sms_placed_bid_enabled');	
	$uwa_twilio_sms_outbid_enabled = get_option('uwa_twilio_sms_outbid_enabled');	
	$uwa_twilio_sms_won_enabled = get_option('uwa_twilio_sms_won_enabled');	
	$uwa_twilio_sms_ending_soon_enabled = get_option('uwa_twilio_sms_ending_soon_enabled');	
	
	$uwa_twilio_sms_placed_bid_enabled == "yes" ? $uwa_twilio_sms_placed_bid_checked = 
		"checked": $uwa_twilio_sms_placed_bid_checked = "";	
	$uwa_twilio_sms_outbid_enabled == "yes" ? $uwa_twilio_sms_outbid_checked = "checked": 
		$uwa_twilio_sms_outbid_checked = "";	
	$uwa_twilio_sms_won_enabled == "yes" ? $uwa_twilio_sms_won_checked = "checked": 
		$uwa_twilio_sms_won_checked = "";	
	$uwa_twilio_sms_ending_soon_enabled == "yes" ? $uwa_twilio_sms_ending_soon_checked = 
		"checked": $uwa_twilio_sms_ending_soon_checked = "";	
		
	$uwa_twilio_sms_placed_bid_template = get_option('uwa_twilio_sms_placed_bid_template',
		"New bid of amount {bid_value} has been placed for product id {product_id}");

	$uwa_twilio_sms_outbid_template = get_option('uwa_twilio_sms_outbid_template',"You have been outbid on product id {product_id}, title {product_name}. The current highest bid is {bid_value}. Open {link} and place your bid.");

	$uwa_twilio_sms_won_template = get_option('uwa_twilio_sms_won_template',"You have won auction product id {product_id}, title {product_name}. Click {this_pay_link} to pay.");

	$uwa_twilio_sms_ending_soon_template = get_option('uwa_twilio_sms_ending_soon_template',"Auction id {product_id}, title {product_name} will be expiring soon. Place your highest bid to win it.");
	
	$uwa_twilio_sms_ending_soon_time = get_option('uwa_twilio_sms_ending_soon_time', 1);

	?>
		<div class="uwa_main_setting_content">
			<form  method='post' class='uwa_auction_setting_style'>
				<table class="form-table">
					<tbody>							
					<tr class="uwa_heading">
						<th colspan="2"><?php _e("Collect Users mobile number during registration", 'woo_ua' ); ?></th>
					</tr>
					
					<tr>
						<th scope="row"></th>									 
						<td>
							<input <?php echo $uwa_twilio_sms_enabled_register_page_checked;
								?> value="1" type="checkbox"
								name="uwa_twilio_sms_enabled_register_page">
							<?php _e( 'Display Country and Mobile number on default Wordpress Register form.', 'woo_ua' ); ?>
						</td>
					</tr>

					<tr>
						<th scope="row"></th>
						<td class="uwaforminp">
							<input type="checkbox" value="1" 
								<?php echo $uwa_twilio_sms_enabled_myaccount_page_checked; ?>
								name="uwa_twilio_sms_enabled_myaccount_page" >
							<?php 
								$uwa_woo_acc_url = admin_url(
									'admin.php?page=wc-settings&tab=account');
								
								printf( __( 'Display Country and Mobile number on WooCommerce My Account Page. Please enable <a href="%s">WooCommerce My Account registration</a> first for this setting to work.', 'woo_ua' ), $uwa_woo_acc_url); 
							?>								
						</td>
					</tr> 
					
					<tr class="uwa_heading">
						<th colspan="2"><?php _e('Twilio Connection', 'woo_ua' ); ?></th>
					</tr>
					
					<tr>
						<th scope="row"><?php _e( 'Account SID', 'woo_ua' ); ?></th>					 
						<td class="uwaforminp">
							<a href="" class="uwa_fields_tooltip" onclick="return false">
								<strong>?</strong>
								<span><?php _e('Log into your Twilio Account to find your Account SID.', 'woo_ua');?>	
								</span>
							</a>						
							<input type="text" name="uwa_twilio_sms_sid" 
								class="regular-text" id="uwa_twilio_sms_sid" value="<?php 
								echo $uwa_twilio_sms_sid; ?>">
						</td>
					</tr>

					<tr>
						<th scope="row"><?php _e( 'Auth Token', 'woo_ua' ); ?></th>							 
						<td class="uwaforminp">
							<a href="" class="uwa_fields_tooltip" onclick="return false">
								<strong>?</strong>
								<span><?php _e('Log into your Twilio Account to find your Account SID.', 'woo_ua');?>	
								</span>
							</a>						
							<input type="text" name="uwa_twilio_sms_token" 
								class="regular-text" id="uwa_twilio_sms_token" value="<?php 
								echo $uwa_twilio_sms_token; ?>">
						</td>
					</tr>

					<tr>
						<th scope="row"><?php _e( 'From Number', 'woo_ua' ); ?></th>			 
						<td class="uwaforminp">
							<a href="" class="uwa_fields_tooltip" onclick="return false">
								<strong>?</strong>
								<span><?php _e('Enter the number to send SMS messages from. This must be a purchased number from Twilio.', 
								'woo_ua');?></span>
							</a>						
							<input type="text" name="uwa_twilio_sms_from_number" 
								class="regular-text" id="uwa_twilio_sms_from_number" 
								value="<?php echo $uwa_twilio_sms_from_number; ?>">
						</td>
					</tr>					
					
					<tr class="uwa_heading">
						<th colspan="2"><?php _e('Send Test SMS', 'woo_ua' ); ?></th>
					</tr>

					<tr>
						<th scope="row"><?php _e( 'Mobile Number', 'woo_ua' ); ?></th>
						<td class="uwaforminp">											
							<input type="text" name="uwa_twilio_sms_test_number" 
							class="regular-text" id="uwa_twilio_sms_test_number" value="">
						</td>
					</tr>

					<tr>
						<th scope="row"><?php _e( 'Message', 'woo_ua' ); ?></th>	 
						<td class="uwaforminp">											
							<textarea name="uwa_twilio_sms_test_template" 
								id="uwa_twilio_sms_test_template" style="min-width:500px;" class="" placeholder=""></textarea>
						</td>
					</tr>

					<tr>
						<th scope="row"></th>							 
						<td class="uwaforminp">	
							<a href="#" 
							class="uwa_twilio_sms_test_sms_button button">Send</a>		
						</td>
					</tr>
					
					<tr class="uwa_heading">
						<th colspan="2"><?php _e('Customer Notifications SMS', 'woo_ua' ); ?></th>
					</tr>
						
					<tr>
						<th scope="row"><?php _e( 'Enable Place Bid SMS:', 'woo_ua' ); ?>
						</th>							 
						<td>
							<input <?php echo $uwa_twilio_sms_placed_bid_checked;?>
								value="1" name="uwa_twilio_sms_placed_bid_enabled" 
								type="checkbox">
						</td>
					</tr>

					<tr>
						<th scope="row"><?php _e( 'Place Bid SMS Message:', 'woo_ua' ); ?>
						</th>
						<td>
							<textarea name="uwa_twilio_sms_placed_bid_template" 
								id="uwa_twilio_sms_placed_bid_template" 
								style="min-width:500px;" class="" placeholder=""><?php echo 
								$uwa_twilio_sms_placed_bid_template;?></textarea><br>
							<span class="description"><?php _e('Use these tags to customize your message: {product_name}, {bid_value}, {product_id}, {link}. Remember that SMS messages may be limited to 160 characters or less.', 'woo_ua');  ?>.</span>
						</td>
					</tr>

					<tr>
						<th scope="row"><?php _e( 'Enable Outbid SMS:', 'woo_ua' ); ?></th>	
						<td>
							<input <?php echo $uwa_twilio_sms_outbid_checked;?> value="1" 
								name="uwa_twilio_sms_outbid_enabled" type="checkbox">
						</td>
					</tr>

					<tr>
						<th scope="row"><?php _e( 'Outbid SMS Message:', 'woo_ua' ); ?></th>							 
						<td>
							<textarea name="uwa_twilio_sms_outbid_template" 
								id="uwa_twilio_sms_outbid_template" style="min-width:500px;" class="" placeholder=""><?php echo 
								$uwa_twilio_sms_outbid_template;?></textarea><br>
							<span class="description"><?php _e('Use these tags to customize your message: {product_name}, {bid_value}, {product_id}, {link}. Remember that SMS messages may be limited to 160 characters or less.', 'woo_ua');  ?>.</span>
						</td>
					</tr>
										
					<tr>
						<th scope="row"><?php _e( 'Enable Won SMS:', 'woo_ua' ); ?></th>						 
						<td>
							<input <?php echo $uwa_twilio_sms_won_checked;?> value="1" 
								name="uwa_twilio_sms_won_enabled" type="checkbox">
						</td>
					</tr>

					<tr>
						<th scope="row"><?php _e( 'Won SMS Message:', 'woo_ua' ); ?></th>							 
						<td>
							<textarea name="uwa_twilio_sms_won_template" 
								id="uwa_twilio_sms_won_template" 
								style="min-width:500px;" class="" placeholder=""><?php echo 
								$uwa_twilio_sms_won_template;?></textarea><br>
							<span class="description"><?php _e('Use these tags to customize your message: {product_name}, {product_id}, {this_pay_link}. Remember that SMS messages may be limited to 160 characters or less.', 'woo_ua');  ?>.</span>
						</td>
					</tr>
					
					<tr>
						<th scope="row"><?php _e( 'Enable Ending Soon SMS:', 'woo_ua' ); ?>
						</th>							 
						<td>
							<input <?php echo $uwa_twilio_sms_ending_soon_checked;?> 
								value="1" name="uwa_twilio_sms_ending_soon_enabled" 
								type="checkbox">
						</td>
					</tr>
					
					<tr>
						<th scope="row"><?php _e( 'Mention time left for auction to close:', 
						'woo_ua' ); ?></th>
						<td class="uwaforminp">
							<input type="number" name="uwa_twilio_sms_ending_soon_time" 
								class="regular-number" id="uwa_twilio_sms_ending_soon_time" value="<?php echo $uwa_twilio_sms_ending_soon_time ?>"><br>
							<span class="description"><?php _e('Mention time left for auction to expire auction, default is 1 hour.This SMS will send to all Bidders those have bid on auction.', 'woo_ua');
							  	?>.</span>							 
						</td>
					</tr>	
					
					<tr>
						<th scope="row"><?php _e( 'Ending Soon SMS Message:', 'woo_ua' ); ?></th>							 
						<td>
							<textarea name="uwa_twilio_sms_ending_soon_template" 
								id="uwa_twilio_sms_ending_soon_template" 
								style="min-width:500px;" class="" placeholder=""><?php echo 
								$uwa_twilio_sms_ending_soon_template;?></textarea><br>
							<span class="description"><?php _e('Use these tags to customize 
								your message: {product_name}, {product_id}, {link}. Remember 
								that SMS messages may be limited to 160 characters or less.',
								'woo_ua');  ?>.</span>
						</td>
					</tr>
					
					<tr class="submit">
						<th colspan="2">
							<input type="submit" id="uwa-settings-submit" 
								name="uwa-settings-submit" class="button-primary" 
								value="<?php _e('Save Changes','woo_ua');?>" />
						</th>
					</tr>

					</tbody>						
				</table>
			</form>
		</div>