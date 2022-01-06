<?php

if ( ! class_exists( '\Stripe\Stripe' ) ) {
	include_once ('init.php');
}
	global $wpdb;
	$arr_stripe = get_option("woocommerce_stripe_settings");	
	if(is_array($arr_stripe) && count($arr_stripe)){

		$is_enabled = $arr_stripe['enabled'];
		if($is_enabled == 'yes'){

			$is_testmode = $arr_stripe['testmode'];
			if($is_testmode == 'yes'){
				$mode = "Test";
				$publishable_key = $arr_stripe['test_publishable_key'];				
				$secret_key = $arr_stripe['test_secret_key'];
			}
			elseif ($is_testmode == 'no') {
				$mode = "Live";
				$publishable_key = $arr_stripe['publishable_key'];				
				$secret_key = $arr_stripe['secret_key'];				
			}

			 
			\Stripe\Stripe::setApiKey($secret_key);
			$pubkey = $publishable_key;
			
			
			if(!empty($customer_id)){
				$sources_id = $token;
				$customer = \Stripe\Customer::create(array(
								'source' => $sources_id,
								'email' => strip_tags(trim($_POST['email'])),
								));
							
				if (!empty($customer->id)) {
					/*update_user_meta( $customer_id, '_stripe_customer_id', $customer->id );*/	

					//$wpdb->prefix
					update_user_meta( $customer_id, $wpdb->prefix.'_stripe_customer_id', 
						$customer->id );

						
					// Store payment method
					$woocommerce_payment_tokens = $wpdb->insert(
						$wpdb-> prefix.'woocommerce_payment_tokens', array(
							'gateway_id' => "stripe", 
							'token' => $sources_id, 
							'user_id' => $customer_id, 
							'type' => "CC", 
							'is_default' => 1 ), 
						array('%s', '%s', '%d', '%s' , '%d'));

					$token_lastid = $wpdb->insert_id;			
					
					$exp_month = $customer->sources->data[0]->card->exp_month;
					$exp_year = $customer->sources->data[0]->card->exp_year;
					$last4 = $customer->sources->data[0]->card->last4;
					$brand = $customer->sources->data[0]->card->brand;				
				
					$exp_year_meta = $wpdb->insert(
						$wpdb->prefix.'woocommerce_payment_tokenmeta', array(
							'payment_token_id' => $token_lastid,
							'meta_key' => "expiry_year",
							'meta_value' => $exp_year ),
						array('%d','%s','%s'));
					
					$exp_month_meta = $wpdb->insert(
						$wpdb->prefix.'woocommerce_payment_tokenmeta', array(
							'payment_token_id' => $token_lastid,
							'meta_key' => "expiry_month",
							'meta_value' => $exp_month ),
						array('%d','%s','%s'));
					
					$exp_month_meta = $wpdb->insert(
						$wpdb->prefix.'woocommerce_payment_tokenmeta', array(
							'payment_token_id' => $token_lastid,
							'meta_key' => "card_type",
							'meta_value' => $brand ),
						array('%d','%s','%s'));
					
					$last4_meta = $wpdb->insert(
						$wpdb->prefix.'woocommerce_payment_tokenmeta', array(
							'payment_token_id' => $token_lastid,
							'meta_key' => "last4",
							'meta_value' => $last4 ),
						array('%d','%s','%s'));
					
				} /* end of if - empty $customer->id */	
				
			} /* end of if - customer_id */
		
		} /* end of if - is_enabled = yes */
		
	} /* end of if - arr_stripe */
	

function uwa_winner_make_strip_payment_vendor($cuserid, $vendor_amt, $total_amt, $product_id,
	$uwa_price_vendor, $uwa_buyers_price_vendor, $vendor_stripe_id){

		global $wpdb, $woocommerce, $post, $WCFM;

		$product = wc_get_product($product_id);	

		/* -- aelia -- */
		$product_base_currency = $product->uwa_aelia_get_base_currency();
		//$currency_symbol = $product->uwa_aelia_get_base_currency_symbol();

		//$currency = get_woocommerce_currency();
		$currency = $product_base_currency;

		$auction_title = get_the_title($product_id);
		$wcfm_withdrawal_options = get_option( 'wcfm_withdrawal_options', array() );
		$stripe_customer_id = get_user_meta( $cuserid, '_stripe_customer_id', true);

			if(!$stripe_customer_id){
				$stripe_customer_id = get_user_meta( $cuserid, $wpdb->prefix.'_stripe_customer_id', true);
			}

		$stripe_token_id = get_user_meta($cuserid, '_uwa_stripe_token_id',true);	
		$withdrawal_payment_methods  = $wcfm_withdrawal_options['payment_methods'];

		if(!empty($vendor_stripe_id)){
			$withdrawal_stripe_split_pay_mode = $wcfm_withdrawal_options[
				'stripe_split_pay_mode'];

			$withdrawal_test_mode = $wcfm_withdrawal_options['test_mode'];
			$is_testmode = $arr_stripe['testmode'];

			if($withdrawal_test_mode == 'yes'){
				$mode = "Test";
				$client_id = $wcfm_withdrawal_options['stripe_test_client_id'];
			    $withdrawal_stripe_published_key =  $wcfm_withdrawal_options[
			    	'stripe_test_published_key'];
			    $withdrawal_stripe_secret_key =  $wcfm_withdrawal_options[
			    	'stripe_test_secret_key'];
			}
			else {
				$mode = "Live";
				$client_id = $wcfm_withdrawal_options['stripe_client_id'];
				$withdrawal_stripe_published_key      = $wcfm_withdrawal_options[
					'stripe_published_key'];
				$withdrawal_stripe_secret_key         = $wcfm_withdrawal_options[
					'stripe_secret_key'];			
			}
			
			if ( ! class_exists( '\Stripe\Stripe' ) ) {
				include_once ('init.php');
			}

			\Stripe\Stripe::setApiKey($withdrawal_stripe_secret_key);
		
			$vednor_paided_amt = 0;       		
			if($vendor_amt > 0) {

				/* cloning customer to vendor account */
				try {					  
					$v_sourceobj = \Stripe\Source::create(
					array("customer" => $stripe_customer_id,),
					array("stripe_account" => $vendor_stripe_id ));
				} 
				catch (Exception $ex) {													
					uwa_create_log("Cloning Source to Connected Account Error: " . $ex->getMessage()." Auction ID=".$product_id);
				}
					
				/* Transfer vendor amount to vendor account  */
				if($v_sourceobj->id){			
					
					try {
						
						$vendorobj = \Stripe\PaymentIntent::create(array(
							"amount" => ($vendor_amt * 100),
							"currency" => $currency,			
							"source" => $v_sourceobj->id,
							'payment_method_types' => array('card'),
							'off_session' => true,
							'confirm' => true,
							'description' => 'Auto Debit For Auction#'.$auction_title,
							), array("stripe_account" => $vendor_stripe_id ));	
					
						
						if ($vendorobj['status'] =="succeeded") {							
							if( isset( $vendorobj['amount'] ) ) {
								$vednor_paided_amt = $vendorobj['amount']/100;
							}
						}
					
					} 
					catch (\Stripe\Error\Base $e) {
						  // Code to do something with the $e exception object when an error occurs						 
						  uwa_create_log("Transfer Vendor Amount Error: " . $e->getMessage()." Auction ID=".$product_id);
					}
					catch (\Stripe\Exception\CardException $e) {						
						 uwa_create_log("Transfer Vendor Amount Error: " .  $e->getMessage()." Auction ID=".$product_id);
					}

				} /* end of if - v_sourceobj */
					
			} /* end of if - vendor_amt > 0 */

			/* Remaining Amount Pay to Admin */			
			$remaining_amount = $total_amt - $vednor_paided_amt;			

			if( $remaining_amount ) {
				try {
						
					$admin_obj = \Stripe\PaymentIntent::create(array(
						   "amount" => ($remaining_amount * 100),
						   "currency" => $currency,
						   "customer" => $stripe_customer_id,
						   'payment_method_types' => array('card'),
						   'off_session' => true,
						   'confirm' => true,
						   'description' => 'Auto Debit For Auction#'.$auction_title,
					));
					
					if ($admin_obj['status'] =="succeeded") {
						update_option("_uwa_w_s_charge_".$product_id."_".$cuserid,$total_amt);
						if($total_amt){
								$stripe_total_amt_metakey = "_uwa_stripe_auto_debit_total_amt";
								update_post_meta($product_id, $stripe_total_amt_metakey,$total_amt);
						}		
						if($uwa_price_vendor){
							 $stripe_metakey = "_uwa_stripe_auto_debit_amt";
							 update_post_meta($product_id, $stripe_metakey, 
							 	$uwa_price_vendor);
						}
						if($uwa_buyers_price_vendor){
							$stripe_buyer_metakey = "_uwa_stripe_auto_debit_bpm_amt";
							update_post_meta($product_id, $stripe_buyer_metakey, 
								$uwa_buyers_price_vendor);
						}
						
						$stripe_status_metakey = "_uwa_stripe_auto_debit_status";
						update_post_meta($product_id, $stripe_status_metakey,"paid");
						
						$uwa_stripe_auto_debit_date_metakey = "_uwa_stripe_auto_debit_date";
						add_post_meta($product_id, $uwa_stripe_auto_debit_date_metakey,get_uwa_now_date());
						
						

					} /* end of if - admin_obj status */							
						
				} 
				
				catch (\Stripe\Error\Base $e) {
						  // Code to do something with the $e exception object when an error occurs						 
						  uwa_create_log("Transfer Admin Amount Error: " . $e->getMessage()." Auction ID=".$product_id);
				}
				
				catch (\Stripe\Exception\CardException $e) {				
					uwa_create_log("Transfer Admin Amount Error: " .  $e->getMessage()." Auction ID=".$product_id);
				}
				
			} /* end of if - remaining_amount */	 
			
		} /* end of if - vendor_stripe_id */
		else {		
			uwa_winner_make_strip_payment($cuserid, $total_amt, $product_id,
				$uwa_buyers_price_vendor, $uwa_buyers_price_vendor);
		}
}

function uwa_payment_chk($cuserid, $product_id, $total_amt)
{
	global $wpdb;
	$transaction_status = "";

	$refunresult = $wpdb->get_results("SELECT status FROM " . $wpdb->prefix . 'auction_direct_payment' . "   WHERE pid='" . $product_id . "' ORDER BY `id` DESC ");
	if (!empty($refunresult)) {
		if (!empty($refunresult[0]->status)) {
			$transaction_status = $refunresult[0]->status;
			if ($transaction_status == 'succeeded') {

				$dataup = ['uid' => $cuserid, 'status' => 'inprograsss', 'transaction_amount' => $total_amt, 'main_amount' => $total_amt];

				$whereup = ['pid' => $product_id];
				$wpdb->update($wpdb->prefix . 'auction_direct_payment', $dataup, $whereup);
			}
		}
	} else {
		$data = array();
		$data['pid'] = $product_id;
		$data['uid'] = $cuserid;
		$data['debit_type'] =  'direct payment';
		$data['debit_amount_type'] = '0';
		$data['amount_or_percentage'] = '0';
		$data['transaction_amount'] = $total_amt;
		$data['main_amount'] = $total_amt;
		$data['status'] =  'inprograsss';
		$data['response_json'] = json_encode($Jdata);

		$format = array('%s', '%d');
		$wpdb->insert($wpdb->prefix . 'auction_direct_payment', $data, $format);
		$transaction_status = 'add';
	}
	return $transaction_status;
}
function uwa_winner_make_strip_payment($cuserid, $total_amt, $product_id, 
	$uwa_stripe_product_price, $uwa_stripe_buyer_price){
		
		$status = uwa_payment_chk($cuserid, $product_id, $total_amt);
	if ($status == 'inprograsss') {
		return;
	}

	global $woocommerce, $wpdb;

	$product = wc_get_product($product_id);	

	/* -- aelia -- */
	$product_base_currency = $product->uwa_aelia_get_base_currency();
	//$currency_symbol = $product->uwa_aelia_get_base_currency_symbol();

	//$currency = get_woocommerce_currency();
	$currency = $product_base_currency;
	
	$auction_title = get_the_title($product_id);
	$arr_stripe = get_option("woocommerce_stripe_settings");

	if(is_array($arr_stripe) && count($arr_stripe)){

		$stripe_customer_id = get_user_meta( $cuserid, '_stripe_customer_id', true);

			if(!$stripe_customer_id){
				$stripe_customer_id = get_user_meta( $cuserid, $wpdb->prefix.'_stripe_customer_id', true);
			}

		$is_enabled = $arr_stripe['enabled'];

		if($is_enabled == 'yes'){
			$is_testmode = $arr_stripe['testmode'];

			if($is_testmode == 'yes'){
				$publishable_key = $arr_stripe['test_publishable_key'];				
				$secret_key = $arr_stripe['test_secret_key'];
			}
			elseif ($is_testmode == 'no') {				
				$publishable_key = $arr_stripe['publishable_key'];				
				$secret_key = $arr_stripe['secret_key'];				
			}		
			
			if(!empty($stripe_customer_id)){  

					//e.g. winner_strip_charge_409_10
					$charged = get_option("_uwa_w_s_charge_".$product_id."_".$cuserid, 
						false);

					if($charged == "added"){
						
						if ( ! class_exists( '\Stripe\Stripe' ) ) {
							include_once ('init.php');
						}

						\Stripe\Stripe::setApiKey($secret_key);
						
						try {

							$PaymentIntentobj = \Stripe\PaymentIntent::create(array(
							   "amount" => ($total_amt * 100),
							   "currency" => $currency,
							   "customer" => $stripe_customer_id,
							   'payment_method_types' => array('card'),
							   'off_session' => true,
							   'confirm' => true,
							   'description' => 'Auto Debit For Auction#'.$auction_title,
							));
						
							if ($PaymentIntentobj['status'] == "succeeded") {	
							
							$dataup = ['status' => 'succeeded', 'response_json' => json_encode($PaymentIntentobj)];

							$whereup = ['pid' => $product_id];
							$wpdb->update($wpdb->prefix . 'auction_direct_payment', $dataup, $whereup);
							
								update_option("_uwa_w_s_charge_".$product_id."_".$cuserid,$total_amt);
								if($total_amt){
									$stripe_total_amt_metakey = "_uwa_stripe_auto_debit_total_amt";
									update_post_meta($product_id, $stripe_total_amt_metakey,$total_amt);
								}	
								if($uwa_stripe_product_price){
									$stripe_metakey = "_uwa_stripe_auto_debit_amt";
									update_post_meta($product_id, $stripe_metakey, 
										$uwa_stripe_product_price);
								}								
								if($uwa_stripe_buyer_price){
									$stripe_buyer_metakey = "_uwa_stripe_auto_debit_bpm_amt";
									update_post_meta($product_id, $stripe_buyer_metakey, 
										$uwa_stripe_buyer_price);
								}	

								$stripe_status_metakey = "_uwa_stripe_auto_debit_status";
								update_post_meta($product_id, $stripe_status_metakey,"paid");
								
								$uwa_stripe_auto_debit_date_metakey = "_uwa_stripe_auto_debit_date";
								add_post_meta($product_id, $uwa_stripe_auto_debit_date_metakey,get_uwa_now_date());
								
								 
							} /* end of if - PaymentIntentobj */
						
						
						} 						
						catch (\Stripe\Error\Base $e) {
							$dataup = ['status' => 'fail', 'response_json' => json_encode($PaymentIntentobj)];

						$whereup = ['pid' => $product_id];
						$wpdb->update($wpdb->prefix . 'auction_direct_payment', $dataup, $whereup);
						  // Code to do something with the $e exception object when an error occurs						 
						  uwa_create_log("Admin Stripe Auto Debit  Charge Error: " . $e->getMessage()." Auction ID=".$product_id);
						}
						catch (\Stripe\Exception\CardException $e) {	

						$dataup = ['status' => 'fail', 'response_json' => json_encode($PaymentIntentobj)];

						$whereup = ['pid' => $product_id];
						$wpdb->update($wpdb->prefix . 'auction_direct_payment', $dataup, $whereup);						
							uwa_create_log("Admin Stripe Auto Debit  Charge Error: " . $e->getMessage()." Auction ID=".$product_id);
							
						}
						
						
					} /* end of if - charged */					

			} /* end of if - $stripe_customer_id */

						/* -----------stripe payment------------ */

		} /* end of if - is_enabled = yes */
		
	} /* end of if - arr_stripe */


} /* end of function */