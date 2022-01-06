<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Ultimate WooCommerce Auction Pro - ADDON
 *
 * @package Ultimate WooCommerce Auction Pro
 * @author Nitesh Singh 
 * @since 1.0
 *
 */ 

/**
 * Include WCFM addons
 *
 */
$blog_plugins = get_option( 'active_plugins', array() );
$site_plugins = is_multisite() ? (array) maybe_unserialize( get_site_option(
	'active_sitewide_plugins' ) ) : array();


/* For Free */
if ( in_array( 'wc-frontend-manager/wc_frontend_manager.php', $blog_plugins ) || isset( 
	$site_plugins['wc-frontend-manager/wc_frontend_manager.php'] ) ) {

	require_once ( UW_AUCTION_PRO_ADDONS . 'wcfm/wcfm-uwa-auctions-support.php' );

}

/* For Pro Ultimate */
if ( in_array( 'wc-frontend-manager-ultimate/wc_frontend_manager_ultimate.php', 
	$blog_plugins ) || isset( $site_plugins['wc-frontend-manager-ultimate/wc_frontend_manager_ultimate.php'] ) ) {

	require_once ( UW_AUCTION_PRO_ADDONS . 'wcfm/wcfm-uwa-auctions-support.php' );
}

/**
 * Enable disable Addons via ajax 
 *
 */ 
add_action("wp_ajax_uwa_admin_activate_inactivate_addons",
	"uwa_admin_activate_inactivate_addons_callback");

function uwa_admin_activate_inactivate_addons_callback() {

	global $wpdb;
	$addons = "";
	$addons = explode("&", $_REQUEST['addons']);
	$addons_arr = array();

	if($_REQUEST['addons'] != ""){
		foreach( $addons as $addonsval){
			$valget = explode("=", $addonsval);
			$addons_arr[] = $valget[1];
		}
	}

	$update_addon = update_option( 'uwa_addons_options_enabled', $addons_arr );
	if( $update_addon ){
		$response['status'] = 1;
		$response['success_message'] = __('Addon active successfully.','woo_ua');
	}
	else {
		$response['status'] = 0;
		$response['error_message'] = __('Sorry, this Addon cannot be activate. Try Again!',
			'woo_ua');
	}
	
	echo json_encode( $response );
	exit;	
}


function uwa_all_addons_list() {
	$uwa_all_addons_list =  array('uwa_addons' =>
		array(
			'slug' => _x( 'uwa_buyers_premium_addon', 'Addon Slug', 'woo_ua' ),
			'name' => _x( "Buyer's Premium", 'Addon Name', 'woo_ua' ),
			'description' => _x( "Charge a premium amount over and above Bid Amount for admin or auction owner.", 'Addon Description', 'woo_ua' ),
			'thumbnail' => _x( 'uwa_buyers_premium_addon.jpg', 'Addon Thumbnail', 
				'woo_ua' )),
			 
		array(
			'slug' => _x( 'uwa_stripe_auto_debit_addon', 'Addon Slug', 'woo_ua' ),
			'name' => _x( 'Credit Card Auto Debit', 'Addon Name', 'woo_ua' ),
			'description' => _x( 'Collect User Credit Card on registration and 
				automatically debit winning amount and transfer to Stripe Account of auction 
				owner.', 'Addon Description', 'woo_ua' ),
			'thumbnail' => _x( 'stripe.jpg', 'Addon Thumbnail', 'woo_ua' )),
			 
		array(
			'slug' => _x( 'uwa_twilio_sms_addon', 'Addon Slug', 'woo_ua' ),
			'name' => _x( 'SMS Notification', 'Addon Name', 'woo_ua' ),
			'description' => _x( 'Send SMS notification for bid, outbid, won and ending soon using Twilio.', 'Addon Description', 'woo_ua' ),
			'thumbnail' => _x( 'Twilio_SMS.jpg', 'Addon Thumbnail', 'woo_ua' )),
	

		array(
			'slug' => _x( 'uwa_offline_dealing_addon', 'Addon Slug', 'woo_ua' ),
			'name' => _x( 'Offline Dealing for Buyer & Seller', 'Addon Name', 'woo_ua' ),
			'description' => _x( 'Exchange contact details of each other and settle your auction offline.', 'woo_ua' ),
			'thumbnail' => _x( 'offline_dealing.jpg', 'Addon Thumbnail', 'woo_ua' )),


		array(
			'slug' => _x( 'uwa_currency_switcher', 'Addon Slug', 'woo_ua' ),
			'name' => _x( 'Currency Switcher With Aelia', 'Addon Name', 'woo_ua' ),
			'description' => _x( 'Our Addons show auction prices in multiple currencies using Aelia Currency Switcher plugin', 'woo_ua' ),
			'thumbnail' => _x( 'aelia_cs.jpg', 'Addon Thumbnail', 'woo_ua' ))
		);
		 
    return $uwa_all_addons_list;
}


/**
 * Enable addons list 
 * return array
 *
 */
function uwa_enabled_addons() {
	global $wpdb;
	$uwa_enabled_addons_list = get_option('uwa_addons_options_enabled');
	return $uwa_enabled_addons_list;
}

add_filter( 'uwa_admin_default_setting_tabs', 'uwa_pro_stripe_settings_tab');
function uwa_pro_stripe_settings_tab($uwa_default_setting_tabs) {
	$uwa_enabled_addons = uwa_enabled_addons(); 
	if(!empty($uwa_enabled_addons)){
		$stripe_tab =  array( 
			'slug' => 'uwa_addons_setting',
			'label' => __('Addons', 'woo_ua'));
		array_push($uwa_default_setting_tabs, $stripe_tab);
	}	
	return $uwa_default_setting_tabs;
}

add_action( 'uwa_admin_after_default_setting_tabs', 'uwa_pro_stripe_settings_includes');
function uwa_pro_stripe_settings_includes($active_tab){
	if( $active_tab == 'uwa_addons_setting' ) {
		   require_once ( UW_AUCTION_PRO_ADDONS .'uwa_addons_admin_setting.php');
	}
}
	
/* include main file for front use */
$uwa_enabled_addons_list = uwa_enabled_addons();
if(!empty($uwa_enabled_addons_list)){
	foreach( $uwa_enabled_addons_list as $key => $active_addon ){
		
		$addons_folder_name = str_replace(["uwa_", "_addon"], '', $active_addon);
		$addons_file  = $addons_folder_name.'/'.$addons_folder_name.'.php';
		include_once( UW_AUCTION_PRO_ADDONS.$addons_file);	
	}
}

/* If auto debit done */

add_action('woocommerce_before_checkout_form', 'uwa_stripe_addon_check_payment_status');

function uwa_stripe_addon_check_payment_status(){
	global $woocommerce;
	if(is_checkout()){ 

		$uwa_auto_stripe_payment_text = get_option('uwa_auto_stripe_payment_text',
			"Your payment is already done!! please place the order");

		// check payment is made or not if yes then disable payment methods
		$w_current_userid = get_current_user_id();
		$cart_subtotal = WC()->cart->subtotal;		
		$cart_items = $woocommerce->cart->get_cart();
		
		$paid_total = 0;
		$charged_total = 0;
		foreach($cart_items as $item){
			$product_id = $item['product_id'];
			$product = wc_get_product( $product_id );
			$pro_type = $product->get_type();
			if($pro_type == 'auction'){
				$w_product_price = $product->get_uwa_auction_current_bid();
				  
				if(!empty($w_product_price) && $w_product_price > 0) {
					$paid_total += $w_product_price;
				}
					
				$get_charged_for_winner = get_option("_uwa_w_s_charge_".$product_id."_".$w_current_userid, false);
					
				if(!empty($get_charged_for_winner) && $get_charged_for_winner > 0) {
					/*$charged_total += (int)$get_charged_for_winner;*/
					$charged_total += (float)$get_charged_for_winner;
				}
			}

		} /* end of foreach */

		/*if($cart_subtotal == $charged_total ){*/
		if($cart_subtotal == $charged_total && $charged_total  > 0){
			echo "<h3>".$uwa_auto_stripe_payment_text."</h3>";			
		}
		
	} /* end of if - is_checkout */
}


add_action( 'woocommerce_cart_calculate_fees', 'uwa_stripe_addon_calculate_cart_payment' );
function uwa_stripe_addon_calculate_cart_payment($cart) {
	global $woocommerce, $product, $post;
	$uwa_auto_stripe_text = get_option('uwa_auto_stripe_text',"Total Auto Debit Via Stripe");

	if(is_checkout() || is_cart()){	

		// check payment is made or not if yes then disable payment methods
		$w_current_userid = get_current_user_id();
		$cart_subtotal = WC()->cart->subtotal;		
		$cart_items = $woocommerce->cart->get_cart();
		$paid_total = 0;
		$charged_total = 0;		
		foreach($cart_items as $item){
			$product_id = $item['product_id'];
			$product = wc_get_product( $product_id );
			$pro_type = $product->get_type();
			if($pro_type == 'auction'){
				$w_product_price = $product->get_uwa_auction_current_bid();
				if(!empty($w_product_price) && $w_product_price > 0) {					
					$paid_total += $w_product_price;
				}
				
				$get_charged_for_winner = get_option("_uwa_w_s_charge_".$product_id."_".$w_current_userid, false);
				
				if(!empty($get_charged_for_winner) && $get_charged_for_winner > 0) {
					/*$charged_total += (int)$get_charged_for_winner;*/
					$charged_total += (float)$get_charged_for_winner;
				}
			}		
		} /* end of foreach */
		
		if(!empty($charged_total) && $charged_total > 0) {

			/* --aelia-- */
			$charged_total = uwa_stripe_display_aelia_value($product, $charged_total);

			/* Don't change name of fee, it's id used in function uwa_stripe_exclude_tax_from_price */
			$cart->add_fee( __( $uwa_auto_stripe_text, 'woo_ua' ) , - $charged_total );
		}

	} /* end of if - is_checkout() */
}

add_filter( 'woocommerce_cart_item_subtotal','uwa_stripe_addon_subtracting_to_cart_subtotal', 99, 3 );
 
function uwa_stripe_addon_subtracting_to_cart_subtotal( $subtotal, $cart_item,$cart_item_key ){

	global $woocommerce;
	$pro_type = $cart_item['data']->get_type();

	if($pro_type == 'auction'){

		$w_current_userid = $cart_item['data']->get_uwa_auction_current_bider();
		$expired_auct = $cart_item['data']->get_uwa_auction_expired();	
		$product_id = $cart_item['data']->get_id();	
		if (!empty($w_current_userid) &&  $expired_auct == '2' && $w_current_userid == get_current_user_id() ) {
			$newsubtotal = "";
			$get_charged_for_winner = get_option(
				"_uwa_w_s_charge_".$product_id."_".$w_current_userid, false);
			$stripe_amt = get_post_meta($cart_item['data']->get_id(), 
				"_uwa_stripe_auto_debit_amt", true);
			$stripe_buyer_amt = get_post_meta($cart_item['data']->get_id(), 
				'_uwa_stripe_auto_debit_bpm_amt', true);
		
			if(!empty($stripe_buyer_amt) && $stripe_buyer_amt > 0) {			
				$by_pre_txt1 = __( "Auto Debit Buyer's Premium.", "woo_ua" );

					/* --aelia-- */
					$stripe_buyer_amt = uwa_stripe_display_aelia_value($cart_item['data'], 
							$stripe_buyer_amt);
				
				$get_win_price1 = wc_price($stripe_buyer_amt);	   
				$newsubtotal .= "</br> - ".$get_win_price1.' '.$by_pre_txt1;
			}
		
			if(!empty($stripe_amt) && $stripe_amt > 0) {			
				$by_pre_txt2 = __( "Auto Debit via Credit card", "woo_ua" );

					/* --aelia-- */
					$stripe_amt = uwa_stripe_display_aelia_value($cart_item['data'], 
							$stripe_amt);
				
				$get_win_price2 = wc_price($stripe_amt);	   
				$newsubtotal .= "</br> - ".$get_win_price2.' '.$by_pre_txt2;
			}
				
			if(!empty($get_charged_for_winner) && $get_charged_for_winner > 0) {
				$debit_txt = __( "Total Debit via Credit card.", "woo_ua" );

					/* --aelia-- */
					$get_charged_for_winner = uwa_stripe_display_aelia_value($cart_item['data'], 
							$get_charged_for_winner);
				
				$get_debit_price = wc_price($get_charged_for_winner);	   
				$newsubtotal .= "</br> - ".$get_debit_price.' '.$debit_txt;  	  
				$subtotal = sprintf( '%s %s', $subtotal, $newsubtotal ); 
			}

	  	} /* end of if - w_current_userid */
	}	
	return $subtotal;
}

/* Front side my account order detail page. */
add_filter( 'woocommerce_order_formatted_line_subtotal','uwa_stripe_addon_subtracting_to_myaccount_thank_you_order_detail', 10, 3 );
function uwa_stripe_addon_subtracting_to_myaccount_thank_you_order_detail( $subtotal, 
	$item, $order ) {

	global $woocommerce;
	$product_id = $item['product_id'];
	$product = wc_get_product( $product_id );
    $pro_type = $product->get_type();
	if($pro_type == 'auction'){
		$w_current_userid = $product->get_uwa_auction_current_bider();
		$expired_auct = $product->get_uwa_auction_expired();

		if ( !empty($w_current_userid) &&  $expired_auct == '2' && $w_current_userid == get_current_user_id()) {
			$newsubtotal = "";
			$get_charged_for_winner = get_option(
				"_uwa_w_s_charge_".$item['product_id']."_".$w_current_userid, false);		
			$stripe_amt = get_post_meta($item['product_id'], "_uwa_stripe_auto_debit_amt", true);
			$stripe_buyer_amt = get_post_meta($item['product_id'], 
				'_uwa_stripe_auto_debit_bpm_amt', true);
		
			if(!empty($stripe_buyer_amt) && $stripe_buyer_amt > 0) {
				$by_pre_txt1 = __( "Auto Debit For Buyer's Premium.", "woo_ua" );

					/* --aelia-- */
					$stripe_buyer_amt = uwa_stripe_display_aelia_value($product, 
							$stripe_buyer_amt);
				
				$get_win_price1 = wc_price($stripe_buyer_amt);	   
				$newsubtotal .= "</br> - ".$get_win_price1.' '.$by_pre_txt1;
			}		
			if(!empty($stripe_amt) && $stripe_amt > 0) {			
				$by_pre_txt2 = __( "Auto Debit for Won Bid", "woo_ua" );

					/* --aelia-- */
					$stripe_amt = uwa_stripe_display_aelia_value($product, 
							$stripe_amt);
				
				$get_win_price2 = wc_price($stripe_amt);	   
				$newsubtotal .= "</br> - ".$get_win_price2.' '.$by_pre_txt2;
			}		
			if(!empty($get_charged_for_winner) && $get_charged_for_winner > 0) {
				$debit_txt = __( "Total Auto Debit via Credit card.", "woo_ua" );

					/* --aelia-- */
					$get_charged_for_winner = uwa_stripe_display_aelia_value($product, 
							$get_charged_for_winner);
				
				$get_debit_price = wc_price($get_charged_for_winner);	   
				$newsubtotal .= "</br> - ".$get_debit_price.' '.$debit_txt;  	  
				$subtotal = sprintf( '%s %s', $subtotal, $newsubtotal ); 
			}

	  	} /* end of if - empty w_current_userid */	  
	}   
    return $subtotal;
}

add_action( 'woocommerce_add_order_item_meta','uwa_add_stripe_auto_debit_to_order_item_meta', 10, 2 );

function uwa_add_stripe_auto_debit_to_order_item_meta( $item_id, $cart_item ) {
	global $woocommerce;

	if(is_checkout()){

			$product_id = $cart_item['product_id'];
			$product = wc_get_product( $product_id );
		    $pro_type = $product->get_type();
		       
		    /*$product = new WC_Product( $product_id );
			$pro_type = $product->get_type();*/
			if($pro_type == 'auction'){
				$w_current_userid = $product->get_uwa_auction_current_bider();
				$stripe_auto_debit_amt = get_option(
					"_uwa_w_s_charge_".$product_id."_".$w_current_userid, false);
				$stripe_amt = get_post_meta($product_id, "_uwa_stripe_auto_debit_amt", true);
				$stripe_buyer_amt = get_post_meta($product_id, '_uwa_stripe_auto_debit_bpm_amt', 
					true);

				// if not empty, update order item meta
				if( !empty( $stripe_auto_debit_amt ) ) {
					wc_update_order_item_meta( $item_id, '_uwa_stripe_charge_order_total_amt', 
						$stripe_auto_debit_amt );
				}
				if( ! empty( $stripe_amt ) ) {
					wc_update_order_item_meta( $item_id, '_uwa_stripe_charge_order_amt', 
						$stripe_amt );
				}
				
				if( ! empty( $stripe_buyer_amt ) ) {
					wc_update_order_item_meta( $item_id, '_uwa_stripe_charge_buyer_premium_order_amt', $stripe_buyer_amt );
				}

			} /* end of if - pro_type = auction */
			
	} /* end of if - is_checkout */
}


/* Hide custom field in admin order item */
add_filter( 'woocommerce_hidden_order_itemmeta','uwa_stripe_auto_debit_woocommerce_hidden_order_itemmeta', 10, 1 ); 
function uwa_stripe_auto_debit_woocommerce_hidden_order_itemmeta( $array ) { 
	$array[] = '_uwa_stripe_charge_order_total_amt';
	$array[] = '_uwa_stripe_charge_order_amt';
	$array[] = '_uwa_stripe_charge_buyer_premium_order_amt';
    return $array;
} 


/*  Display order item meta in admin side */
add_action( 'woocommerce_admin_order_item_headers','uwa_stripe_auto_debit_action_woocommerce_admin_order_item_headers', 10, 3 );
function uwa_stripe_auto_debit_action_woocommerce_admin_order_item_headers( $order ) {
    echo '<th class="item_buyercost sortable" data-sort="float" style="text-align: right;">Auto Debit</th>';
}

/* Display order item meta in admin side */
add_action( 'woocommerce_admin_order_item_values','uwa_stripe_auto_debit_action_woocommerce_admin_order_item_values', 10, 3 );
function uwa_stripe_auto_debit_action_woocommerce_admin_order_item_values( $null, $item, 
	$absint ) {

    $total_auto_debit_amt = $item->get_meta('_uwa_stripe_charge_order_total_amt', true);
    $auto_debit_amt = $item->get_meta('_uwa_stripe_charge_order_amt', true);
    $auto_debit_buyer_amt = $item->get_meta('_uwa_stripe_charge_buyer_premium_order_amt', true);
    ?>
    	<td class="item_buyercost" data-sort-value="<?php echo $total_auto_debit_amt; ?>">
        <div class="view" style="text-align: right; padding-right: 10px;">
			<?php   
				if($auto_debit_buyer_amt > 0){ ?>Buyer's Premium :
				<?php  

					/* --aelia-- */
					$product = wc_get_product($item->get_product_id());
					$auto_debit_buyer_amt = uwa_stripe_display_aelia_value($product, 
							$auto_debit_buyer_amt);				

					echo wc_price($auto_debit_buyer_amt) ; ?> </br> <?php }   ?>
	        <?php   
	         	if($auto_debit_amt > 0){ ?> Won Bid :
	         	<?php  

	         		/* --aelia-- */
					$product = wc_get_product($item->get_product_id());
					$auto_debit_amt = uwa_stripe_display_aelia_value($product, 
							$auto_debit_amt);					

	         		echo wc_price ($auto_debit_amt) ; ?>  </br><?php }  ?>
		    <?php   
		    	if($total_auto_debit_amt > 0){ ?> Total :
		    	<?php  

		    		/* --aelia-- */
		    		$product = wc_get_product($item->get_product_id());
					$total_auto_debit_amt = uwa_stripe_display_aelia_value($product, 
							$total_auto_debit_amt);				

		    		echo wc_price($total_auto_debit_amt) ; ?>  <?php }   ?>      
        </div>
    	</td>
    <?php
}


function uwa_stripe_display_aelia_value($product, $amount){	
	
	$addons = uwa_enabled_addons();
	if(is_array($addons) && in_array('uwa_currency_switcher', $addons)){		
		if($product->uwa_aelia_is_configure() == TRUE){
    		$amount = $product->uwa_aelia_base_to_active($amount);	        	
		}
	}

	return $amount;
}

/*  Exclude tax from autodebit winning bid values when tax is enabled for checkout page */
function uwa_stripe_exclude_tax_from_price($taxes, $fee){
	
	/* Check -- Display prices during cart and checkout option is enabled or not */
	$tax_display = get_option('woocommerce_tax_display_cart');

	if ('incl' === $tax_display || 'excl' === $tax_display) {

			if(is_object($fee)){

				foreach($fee as $single_fee){		

					if(is_object($single_fee)){

						if($single_fee->id == "auto-debit-via-stripe"){
							//unset($fee->taxes, $fee->tax_class, $fee->total_tax, $fee->taxable);
							//return;	
							//unset($taxes);
							return[];														
						}			
					} 

				} /* end of foreach */		

			} /* end of if  */
	
	}
  	return $taxes;
}

add_action('woocommerce_cart_totals_get_fees_from_cart_taxes', 'uwa_stripe_exclude_tax_from_price', 10, 2);