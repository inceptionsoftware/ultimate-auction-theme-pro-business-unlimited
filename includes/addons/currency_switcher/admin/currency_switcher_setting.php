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

if(isset($_POST['uwa-cs-addon-submit']) == 'Save Changes'){

		if (isset($_POST['uwa_aelia_cs_text'])) {
			update_option('uwa_aelia_cs_text', trim($_POST['uwa_aelia_cs_text']));
		}

} /* end of if - save changes */

	$get_aelia_text = get_option('uwa_aelia_cs_text');

	//var_dump($get_aelia_text);
	if ($get_aelia_text === false){		
		//$aelia_cs_text = __("Enter bid in primary currency", "woo_ua");
		$aelia_cs_text = "Enter bid in primary currency";
	}
	elseif(empty($get_aelia_text)){			
		$aelia_cs_text = "";	
	}
	else{		
		//$aelia_cs_text = __($get_aelia_text, "woo_ua");
		$aelia_cs_text = $get_aelia_text;
	}

?>
<div class='wrap'>
<div id='icon-tools' class='icon32'></br></div>	
	<form id="uwa_buyers_premium_form" class="auction_settings_section_style" action="" 
		method="POST">

		<table class="form-table">
		
			<tr class="uwa_heading">
				<th colspan="2"><?php _e("Aelia Configuration", "woo_ua" ); ?></th> 
			</tr> 

			<tr>				
				<td colspan="4">				
					<?php _e("If you are using Aelia Currency Switcher plugin then you can enable this addon to display auction product and bid prices in multiple currencies. Please do note that this addon will only display the prices in multiple currencies and placing bids will only work in primary currency set by the admin for his website.", "woo_ua"); ?>
								
				</td>				
			</tr>		
			<tr>				
				<td colspan="4">	

					<?php echo sprintf( __( "Make sure you configure %sAelia Currency Switcher settings%s for this addon to work properly.", "woo_ua" ), '<a target="_blank" href="admin.php?page=aelia_cs_options_page">', '</a>' ); ?>
								
				</td>				
			</tr>

			<tr class="uwa_heading">
				<th colspan="2"><?php _e("Currency Switcher Configuration", "woo_ua" ); ?></th> 
			</tr> 

			<tr valign="top">
				<th scope="row">
					<label for="uwa_aelia_cs_text">
						<?php _e("Show this text below bid field", "woo_ua"); ?>
					</label>
				</th>
				<td>
					<textarea  name="uwa_aelia_cs_text" id="uwa_aelia_cs_text" rows="4" cols="50"><?php echo strip_tags(trim($aelia_cs_text)); ?></textarea>
				</td>				
			</tr>
			
			<tr>
				<td colspan="3" valign="top" scope="row">								
					<input type="submit" id="uwa-cs-addon-submit" 
						name="uwa-cs-addon-submit" class="button-primary" 
						value="<?php _e('Save Changes', "woo_ua");?>" />
				</td>
			</tr>

		</table>
	</form>
</div>