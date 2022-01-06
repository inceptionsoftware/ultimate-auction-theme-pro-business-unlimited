<?php

/**
 * Extra Functions file
 *
 * @package Ultimate WooCommerce Auction PRO - business- addon - offline dealing
 * @author Nitesh Singh 
 * @since 1.0
 * 
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if(isset($_POST['uwa-settings-submit']) == 'Save Changes'){
	
		if(isset($_POST['offline_dealing_enabled_my_account'])){
			update_option('offline_dealing_enabled_my_account', "yes");
		}
		else{
			update_option('offline_dealing_enabled_my_account', "no");
		}

		if(isset($_POST['offline_dealing_field_firstlast_name'])){
			update_option('offline_dealing_field_firstlast_name', "yes");
		} 
		else{
			update_option('offline_dealing_field_firstlast_name', "no");
		}

		if(isset($_POST['offline_dealing_field_address'])){
			update_option('offline_dealing_field_address', "yes");
		} 
		else{
			update_option('offline_dealing_field_address', "no");
		}

		if(isset($_POST['offline_dealing_field_country'])){	
			update_option('offline_dealing_field_country', "yes");
		} 
		else{
			update_option('offline_dealing_field_country', "no");
		}

		if(isset($_POST['offline_dealing_field_phone'])){
			update_option('offline_dealing_field_phone', "yes");
		} 
		else{
			update_option('offline_dealing_field_phone', "no");
		}

		if(isset($_POST['offline_dealing_field_state'])){
			update_option('offline_dealing_field_state', "yes");
		} 
		else{
			update_option('offline_dealing_field_state', "no");
		}

		/*if(isset($_POST['offline_dealing_field_email'])){
			update_option('offline_dealing_field_email', "yes");
		} 
		else{
			update_option('offline_dealing_field_email', "no");
		}*/

		if(isset($_POST['offline_dealing_display_contact_detail'])){
			update_option('offline_dealing_display_contact_detail', "yes");
		} 
		else{
			update_option('offline_dealing_display_contact_detail', "no");
		}
		
		if(isset($_POST['offline_dealing_display_in_mail'])){
			update_option('offline_dealing_display_in_mail', "yes");
		} 
		else{
			update_option('offline_dealing_display_in_mail', "no");
		}

} /* end of if - save changes */


	$offline_dealing_enabled_my_account = get_option(
		'offline_dealing_enabled_my_account', "no");

	$offline_dealing_enabled_my_account == "yes" ?
	$offline_dealing_enabled_my_account_checked = "checked" :
	$offline_dealing_enabled_my_account_checked = "";

	// ----------------------

	$offline_dealing_field_firstlast_name = get_option(
		'offline_dealing_field_firstlast_name', "no");

	$offline_dealing_field_firstlast_name  == "yes"  ?
	$offline_dealing_field_firstlast_name_checked = "checked" :
	$offline_dealing_field_firstlast_name_checked = "";

	// ----------------------

	$offline_dealing_field_address = get_option(
		'offline_dealing_field_address', "no");

	$offline_dealing_field_address == "yes"  ?
	$offline_dealing_field_address_checked = "checked" :
	$offline_dealing_field_address_checked = "";

	// ----------------------

	$offline_dealing_field_country = get_option(
		'offline_dealing_field_country', "no");

	$offline_dealing_field_country == "yes"  ?
	$offline_dealing_field_country_checked = "checked" :
	$offline_dealing_field_country_checked = "";

	// ----------------------

	$offline_dealing_field_phone = get_option(
		'offline_dealing_field_phone', "no");

	$offline_dealing_field_phone == "yes"  ?
	$offline_dealing_field_phone_checked = "checked" :
	$offline_dealing_field_phone_checked = "";

	$offline_dealing_field_state = get_option(
		'offline_dealing_field_state', "no");

	$offline_dealing_field_state == "yes"  ?
	$offline_dealing_field_state_checked = "checked" :
	$offline_dealing_field_state_checked = "";


	// ----------------------

	/*$offline_dealing_field_email = get_option(
		'offline_dealing_field_email', "no");

	$offline_dealing_field_email == "yes" ?
	$offline_dealing_field_email_checked = "checked" :
	$offline_dealing_field_email_checked = "";*/

	// ----------------------

	$offline_dealing_display_contact_detail = get_option(
		'offline_dealing_display_contact_detail', "no");

	$offline_dealing_display_contact_detail  == "yes" ?
	$offline_dealing_display_contact_detail_checked = "checked" :
	$offline_dealing_display_contact_detail_checked = "";

	// ----------------------

	$offline_dealing_display_in_mail = get_option(
		'offline_dealing_display_in_mail', "no");

	$offline_dealing_display_in_mail == "yes"  ?
	$offline_dealing_display_in_mail_checked = "checked" :
	$offline_dealing_display_in_mail_checked = "";

	
	?>
		<div class="uwa_main_setting_content">
			<form  method='post' class='uwa_auction_setting_style'>
				<table class="form-table">
					<tbody>
						<tr class="uwa_heading">
							<th colspan="2"><?php _e("Collect contact details", 
								'woo_ua' ); ?>
							</th>
						</tr>
					<tbody>
				</table>

				<table class="form-table">
					<tbody>
						<tr>
							<th scope="row"></th>
							<td>
								<input <?php 
									echo $offline_dealing_enabled_my_account_checked;
									?> value="1" type="checkbox"
									name="offline_dealing_enabled_my_account">
								<?php _e( 'Enable contact fields on my account page', 
									'woo_ua' ); ?>
							</td>
						</tr>

						<tr>
							<th scope="row">Enable Contact fields:</th>	 
							<td>
								<input <?php echo 
									$offline_dealing_field_firstlast_name_checked;
									?> value="1" type="checkbox"	
									name="offline_dealing_field_firstlast_name">
								<?php _e( 'First name and last name fields', 'woo_ua' ); ?>
							</td>
						</tr>

						<tr>
							<th scope="row"></th>	 
							<td>							
								<input <?php echo $offline_dealing_field_address_checked;
									?> value="1" type="checkbox"	
									name="offline_dealing_field_address">
								<?php _e( 'Address fields', 'woo_ua' ); ?>						
							</td>
						</tr>

						<tr>
							<th scope="row"></th>	 
							<td>							
								<input <?php echo $offline_dealing_field_country_checked;
									?> value="1" type="checkbox"	
									name="offline_dealing_field_country">
								<?php _e( 'Country field', 'woo_ua' ); ?>						
							</td>
						</tr>

						<tr>
							<th scope="row"></th>	 
							<td>							
								<input <?php echo $offline_dealing_field_state_checked;
									?> value="1" type="checkbox"	
									name="offline_dealing_field_state">
								<?php _e( 'State field', 'woo_ua' ); ?>						
							</td>
						</tr>

						<tr>
							<th scope="row"></th>	 
							<td>
								<input <?php echo $offline_dealing_field_phone_checked;
									?> value="1" type="checkbox"	
									name="offline_dealing_field_phone">
								<?php _e( 'Phone field', 'woo_ua' ); ?>
							</td>
						</tr>

						<!-- <tr>
							<th scope="row"></th>	 
							<td>
								<input <?php echo $offline_dealing_field_email_checked;
									?> value="1" type="checkbox"	
									name="offline_dealing_field_email">
								<?php _e( 'Email address field', 'woo_ua' ); ?>
							</td>
						</tr> --> 

						<tr class="uwa_heading">
							<th colspan="2"><?php _e("Display contact details", 'woo_ua' ); ?>
							</th>
						</tr>

						<tr>
							<th scope="row"></th>	 
							<td>
								<input <?php echo 
									$offline_dealing_display_contact_detail_checked;
									?> value="1" type="checkbox"	
									name="offline_dealing_display_contact_detail">
								<?php _e( 'Display contact details on product detail page', 'woo_ua' ); ?>
							</td>
						</tr>

						<tr>
							<th scope="row"></th>	 
							<td>
								<input <?php echo $offline_dealing_display_in_mail_checked;
									?> value="1" type="checkbox"	
									name="offline_dealing_display_in_mail">
								<?php _e( 'Display contact details in winner mail', 'woo_ua' ); ?>
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