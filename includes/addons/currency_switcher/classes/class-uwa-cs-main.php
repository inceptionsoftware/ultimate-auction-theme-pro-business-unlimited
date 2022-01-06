<?php
/**
 * Ultimate Woocommerce Auction Currency Switcher Main 
 *
 * Adds filter or functions for convert price value in multiple currencies
 *
 * @class 	UWA_CS_Addon_Main
 * @version 1.0
 * @author Nitesh Singh
 *
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

// Load the helper class to handle multiple currencies
require_once 'class-currency-helper.php';

class UWA_CS_Addon_Main {

	public function __construct() {

       add_filter( 'woocommerce_product_get_price', array( $this, 'uwa_cs_product_get_price' ),10, 2);
       add_filter( 'woocommerce_get_price_html', array( $this, 'uwa_cs_get_price_html' ),120, 2);
       add_action( 'ultimate_woocommerce_auction_after_bid_form', array( $this, 'uwa_cs_display_text' ), 1, 1);
	}

	/**
	 * filter function for woocommerce_product_get_price for Auction Product
	 *	
	 */
	public function uwa_cs_product_get_price( $price, $product ){
		
		if ($product->get_type() === "auction"){		
			//$new_price = get_post_meta($product->get_id(), "woo_ua_auction_current_bid", true);		
			$active_currency = get_woocommerce_currency();		
			$product_base_currency = Woocommerce_Product_Currency_Helper::get_product_base_currency($product->get_id());		
			$price = apply_filters('wc_aelia_cs_convert', $price, $product_base_currency, 
				$active_currency);	
		}
		return $price;	
		

	} /* end of function */


	/**
	 * filter function for woocommerce_get_price_html for Auction Product
	 *	
	 */
	public function uwa_cs_get_price_html( $price, $product ){

		if ($product->get_type() === "auction"){

			$active_currency = get_woocommerce_currency();		
			$product_base_currency = Woocommerce_Product_Currency_Helper::get_product_base_currency($product->get_id());	

		
			$id = $product->get_uwa_wpml_default_product_id();
			$auction_selling_type = $product->get_uwa_auction_selling_type();
			if($auction_selling_type == "auction" || $auction_selling_type == "both" || 	
				$auction_selling_type == ""){

			
			if ($product->is_uwa_expired() && $product->is_uwa_live() ){
				
				if ($product->get_uwa_auction_expired() == '3'){
					
					$main_price = $product->get_price();
					/*$aelia_price = apply_filters('wc_aelia_cs_convert', $main_price, 
								$product_base_currency, $active_currency);*/
					$price = __('<span class="woo-ua-sold-for sold_for">Sold for</span>: ','woo_ua').wc_price($main_price);
				}
				else{
					
					if ($product->get_uwa_auction_current_bid()){

						if ( $product->is_uwa_reserve_met() == FALSE){
							
							$price = __('<span class="woo-ua-winned-for reserve_not_met">Reserve price Not met!</span> ','woo_ua');
							
						} else{
							$main_price = $product->get_uwa_auction_current_bid();
							$aelia_price = apply_filters('wc_aelia_cs_convert', $main_price, 
								$product_base_currency, $active_currency);
							$price = __('<span class="woo-ua-winned-for winning_bid">Winning Bid</span>: ','woo_ua').wc_price($aelia_price);
						}
					}
					else{
						$price = __('<span class="woo-ua-winned-for expired">Auction Expired</span> ','woo_ua');
					}


				} /* end of else */

			} elseif(!$product->is_uwa_live()){
				
				$main_price = $product->get_uwa_current_bid();
				$aelia_price = apply_filters('wc_aelia_cs_convert', $main_price, 
								$product_base_currency, $active_currency);
				$price = '<span class="woo-ua-auction-price starting-bid" data-auction-id="'.$id.'" data-bid="'.$product->get_uwa_auction_current_bid().'" data-status="">'.__('<span class="woo-ua-starting auction">Starting bid</span>: ','woo_ua').wc_price(
					$aelia_price).'</span>';
				
			} else {

					
				if($product->get_uwa_auction_silent() == 'yes'){
					$price = '<span class="woo-ua-auction-price" data-auction-id="'.$id.'"  data-status="running">'.__('<span class="current auction">product auction is silent bid.</span> ','woo_ua').'</span>';
				} else{
				
					if (!$product->get_uwa_auction_current_bid()){	

						$main_price = $product->get_uwa_current_bid();
						$aelia_price = apply_filters('wc_aelia_cs_convert', $main_price, 
								$product_base_currency, $active_currency);
						$price = '<span class="woo-ua-auction-price starting-bid" data-auction-id="'.$id.'" data-bid="'.$product->get_uwa_auction_current_bid().'" data-status="running">'.__('<span class="woo-ua-current auction">Starting bid</span>: ','woo_ua').wc_price($aelia_price).'</span>';
					} else {						

						$main_price = $product->get_uwa_current_bid();
						$aelia_price = apply_filters('wc_aelia_cs_convert', $main_price, 
								$product_base_currency, $active_currency);
						$price = '<span class="woo-ua-auction-price current-bid" data-auction-id="'.$id.'" data-bid="'.$product->get_uwa_auction_current_bid().'" data-status="running">'.__('<span class="woo-ua-current auction">Current bid</span>: ','woo_ua').wc_price($aelia_price).'</span>';
					}
				}

			}
			
			} else {
				/* -- Don't delete below code -- */
				/*$main_price = $product->get_price();				
				$aelia_price = apply_filters('wc_aelia_cs_convert', $main_price, 
								$product_base_currency, $active_currency);
				$price = wc_price($main_price);*/

				/* ----- changed at here ---- */

				if($auction_selling_type == "buyitnow"){
					// no need to convert in aelia bcz already converted in regular price
					$main_price = $product->get_regular_price();									
					$price = wc_price($main_price);
				}
				
			}


		} /* end of auction type */

		return $price;


	} /* end of function */


	/**
	 * display text
	 *	
	 */
	public function uwa_cs_display_text($product){

		$get_aelia_text = get_option('uwa_aelia_cs_text');
		
		if ($get_aelia_text === false){			
			$aelia_cs_text = __("Enter bid in primary currency", "woo_ua");
		}
		elseif(empty($get_aelia_text)){				
			$aelia_cs_text = "";	
		}
		else{			
			$aelia_cs_text = __($get_aelia_text, "woo_ua");
		}

		if($aelia_cs_text){
			/*echo "<br><div class='uwa_cs_text' style='color:red;'><strong>". $aelia_cs_text ."</strong></div><br>";*/
			echo "<div class='uwa_cs_text' style='color:red; margin-top:11px; margin-bottom:11px;'>
				<strong>". $aelia_cs_text ."</strong></div>";
		}
	}



} /* end of class */

new UWA_CS_Addon_Main();