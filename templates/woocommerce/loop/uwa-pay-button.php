<?php/** * Pay button *  * @package Ultimate WooCommerce Auction PRO * @author Nitesh Singh  * @since 1.0   * */if (!defined('ABSPATH')) {	exit;}global $product;if(!(method_exists( $product, 'get_type') && $product->get_type() == 'auction')){	return;}	$user_id  = get_current_user_id();	$checkout_url = esc_attr(add_query_arg("pay-uwa-auction", $product->get_id(), uwa_auction_get_checkout_url()));	if ( $user_id == $product->get_uwa_auction_current_bider() && $product->get_uwa_auction_expired() == '2' && !$product->get_uwa_auction_payed() ) {		/* -------  when offline_dealing_addon is active  ------- */		$addons = uwa_enabled_addons();		if(is_array($addons) && in_array('uwa_offline_dealing_addon', $addons)){				// buyers and stripe both deactive			if(!in_array('uwa_buyers_premium_addon', $addons) && 				!in_array('uwa_stripe_auto_debit_addon', $addons)){				//echo "in 1";							}	// buyers active only			elseif(in_array('uwa_buyers_premium_addon', $addons) && 				!in_array('uwa_stripe_auto_debit_addon', $addons)){				//echo "in 2";				?>					<a href="<?php echo $checkout_url; ?>" class="button alt">						<?php echo apply_filters('ultimate_woocommerce_auction_pay_now_button_text', 							__("Pay Buyer's Premium", 'woo_ua' ), $product); ?></a>				<?php			} // buyers and stripe both active			elseif(in_array('uwa_buyers_premium_addon', $addons) && 				in_array('uwa_stripe_auto_debit_addon', $addons)){				//echo "in 3";			}		}		else {			/* general */			$w_current_userid = $product->get_uwa_auction_current_bider();			$w_product_price = $product->get_uwa_auction_current_bid();				$get_charged_for_winner = get_option(				"_uwa_w_s_charge_".$product->get_id()."_".$w_current_userid, false);			if($get_charged_for_winner == $w_product_price || $get_charged_for_winner > $w_product_price ){ ?>				    	<a href="<?php echo $checkout_url; ?>" class="button alt uwa_pay_now">					<?php echo apply_filters(						'ultimate_woocommerce_auction_pay_now_button_text', 						__( 'Get Item', 'woo_ua' ), $product); ?></a>									<?php 			} 			else { ?>		    	<a href="<?php echo $checkout_url; ?>" class="button alt uwa_pay_now">					<?php echo apply_filters('ultimate_woocommerce_auction_pay_now_button_text',						 __( 'Pay Now', 'woo_ua' ), $product); ?></a>				<?php 			} 						} /* end of else - general */	} /* end of if - auction payed */