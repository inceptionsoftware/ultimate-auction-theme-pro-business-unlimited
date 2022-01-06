<?php

/**
 * 
 * @package Ultimate WooCommerce Auction PRO ADDON buyer premium
 * @author Nitesh Singh 
 * @since 1.0
 * 
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

function uwa_get_buyer_premium_value($product_id, $winner_bid){
        $uwa_buyer_price = 0;

        if($product_id > 0){

        	$level = get_post_meta($product_id, 'woo_ua_buyer_level', true);
        	if($level == "product_level"){

        		/* set buyers premium at product level */

					$uwa_bpm_type = get_post_meta($product_id, 'woo_ua_buyer_type', true);
			        $uwa_bpm_amt = get_post_meta($product_id, 'woo_ua_buyer_fee_amt', true);
			        if(isset($uwa_bpm_amt) && !empty($uwa_bpm_amt)){
			            if(isset($uwa_bpm_type) && !empty($uwa_bpm_type)){
			                if($uwa_bpm_type == 'percentage'){
			                    $uwa_buyer_price = ($winner_bid * $uwa_bpm_amt)/100;

			                    /* calculations for min max buyers premium */
			                    $uwa_bpm_min_val = get_post_meta($product_id, 'woo_ua_buyer_min_amt', true);
			                	$uwa_bpm_max_val = get_post_meta($product_id, 'woo_ua_buyer_max_amt', true);
			                	$min_val = $uwa_bpm_min_val;
								$max_val = $uwa_bpm_max_val;
								
								if($min_val > 0 ){
									if($uwa_buyer_price < $min_val ){
										$uwa_buyer_price = $min_val;
									}
								}

								if($max_val > 0 ){
									if($uwa_buyer_price > $max_val ){
										$uwa_buyer_price = $max_val;
									}
								}

			                }
			                else{
			                    $uwa_buyer_price = $uwa_bpm_amt;
			                }
			            }
			        } /* end of if - isset */

        	}
        	else{

        		/* set buyers premium globally */

			 		$uwa_bpm_type = get_option('uwa_buyers_premium_type');
			        $uwa_bpm_amt = get_option('uwa_buyers_premium_rate');
			        if(isset($uwa_bpm_amt) && !empty($uwa_bpm_amt)){
			            if(isset($uwa_bpm_type) && !empty($uwa_bpm_type)){
			                if($uwa_bpm_type == 'percentage'){
			                    $uwa_buyer_price = ($winner_bid * $uwa_bpm_amt)/100;

			                    /* calculations for min max buyers premium */
			                    $uwa_bpm_min_val = get_option("uwa_buyers_min_premium", "");
			                	$uwa_bpm_max_val = get_option("uwa_buyers_max_premium", "");
			                	$min_val = $uwa_bpm_min_val;
								$max_val = $uwa_bpm_max_val;
								
								if($min_val > 0 ){
									if($uwa_buyer_price < $min_val ){
										$uwa_buyer_price = $min_val;
									}
								}

								if($max_val > 0 ){
									if($uwa_buyer_price > $max_val ){
										$uwa_buyer_price = $max_val;
									}
								}

			                }
			                else{
			                    $uwa_buyer_price = $uwa_bpm_amt;
			                }
			            }
			        } /* end of if - isset */

        	} /* end of else - globally */        	

        } /* end of if - productid */

       
   	return $uwa_buyer_price;   

}

/*function uwa_get_buyer_premium_price(){*/
function uwa_display_buyer_premium_price($product_id){
	global $product;
	$uwa_buyer_price = 0;

		$all_data = uwa_get_buyer_premium_data($product_id);
		$uwa_bpm_type = $all_data['bpm_type'];
        $uwa_bpm_amt = $all_data['bpm_fee_amt'];

        if($uwa_bpm_amt > 0 ){

	        if($uwa_bpm_type == 'percentage'){
	                	$uwa_buyer_price = $uwa_bpm_amt.__("% of Winning bid.", 'woo_ua');
	        }
         	else if($uwa_bpm_type == 'flat'){

                	/* --aelia-- */
					$uwa_bpm_amt = uwa_buyers_premium_display_aelia_value($product, 
						$uwa_bpm_amt);				

                    $uwa_buyer_price = wc_price($uwa_bpm_amt);
                }

        } /* end of if -- amt > 0 */


	return $uwa_buyer_price;	
}

function uwa_get_buyer_premium_data($product_id){

	    $arr_data = array();
	    $arr_per = array();

        if($product_id > 0){
        	$bpm_level = get_post_meta($product_id, 'woo_ua_buyer_level', true);
        	if($bpm_level == "product_level"){ 

        		/* get buyers premium product level */ 
        			$p_givento =  get_post_meta($product_id, 'woo_ua_buyer_given_to', true);
					$p_type = get_post_meta($product_id, 'woo_ua_buyer_type', true);
			        $p_fee_amt = get_post_meta($product_id, 'woo_ua_buyer_fee_amt', true);
		            if($p_type == 'percentage'){			                    
		                $p_min_val = get_post_meta($product_id, 'woo_ua_buyer_min_amt', true);
		            	$p_max_val = get_post_meta($product_id, 'woo_ua_buyer_max_amt', true);
		            	$arr_per['bpm_min'] = $p_min_val;
		            	$arr_per['bpm_max'] = $p_max_val;
		            }


		    	$arr_data = array('bpm_level' => $bpm_level,
		 						'bpm_givento' => $p_givento,
		 					    'bpm_type' => $p_type,
		 					    'bpm_fee_amt' => $p_fee_amt) + $arr_per;

        	}
        	else{

        		/* get buyers premium globally */
        			$g_givento = get_option('uwa_buyers_premium_for', "uwa_admin");
			 		$g_type = get_option('uwa_buyers_premium_type');
			        $g_fee_amt = get_option('uwa_buyers_premium_rate');
			        
	                if($g_type == 'percentage'){
	                    $g_min_val = get_option("uwa_buyers_min_premium", "");
	                	$g_max_val = get_option("uwa_buyers_max_premium", "");
	                	$arr_per['bpm_min'] = $g_min_val;
		            	$arr_per['bpm_max'] = $g_max_val;
	                }			              
			     
		    		$arr_data = array('bpm_level' => $bpm_level,
		 						'bpm_givento' => $g_givento,
		 					    'bpm_type' => $g_type,
		 					    'bpm_fee_amt' => $g_fee_amt) + $arr_per;


        	} /* end of else - globally */        	

        } /* end of if - productid */

      
   	return $arr_data;   		
}


add_action( 'ultimate_woocommerce_auction_close', 'uwa_buyer_premium_add_to_product'); 

function uwa_buyer_premium_add_to_product( $auction_id ) {
	    global $wpdb, $woocommerce, $post;
		$uwa_stripe_charge_type = get_option('uwa_stripe_charge_type');	
		$product = wc_get_product($auction_id);	
		$w_current_userid = $product->get_uwa_auction_current_bider();
		$expired_auct = $product->get_uwa_auction_expired();
		if ( !empty($w_current_userid) &&  $expired_auct == '2'  ) {
			$product_id =  $product->get_id();
			$w_product_price = $product->get_uwa_auction_current_bid();			
			//$buyer_premium_amt = uwa_get_buyer_premium_value($w_product_price);
			$buyer_premium_amt = uwa_get_buyer_premium_value($product_id, $w_product_price);
			update_post_meta($product_id, '_uwa_buyer_premium_amt', $buyer_premium_amt);
		}
}

/* Display buyers premium in cart subtotal. each product */
add_filter( 'woocommerce_cart_item_subtotal','uwa_add_buyers_premium_to_cart_subtotal', 99, 3 ); 

function uwa_add_buyers_premium_to_cart_subtotal( $subtotal, $cart_item, $cart_item_key ){
	global $woocommerce, $product;
	$pro_type = $cart_item['data']->get_type();
	if($pro_type == 'auction'){
		$w_current_userid = $cart_item['data']->get_uwa_auction_current_bider();
		$expired_auct = $cart_item['data']->get_uwa_auction_expired();	
		$auto_debit_bpm_amt = $cart_item['data']->get_uwa_stripe_auto_debit_bpm_amt();
		if(empty($auto_debit_bpm_amt)) {
			if ( !empty($w_current_userid) &&  $expired_auct == '2') {
				$buyer_premium_in_amt = get_post_meta($cart_item['data']->get_id(),'_uwa_buyer_premium_amt', true);
				$by_pre_txt = __( "Buyer's Premium.", "woo_ua" );

					/* --aelia-- */
					$buyer_premium_in_amt = uwa_buyers_premium_display_aelia_value($product, 
						$buyer_premium_in_amt);
				
				$get_win_price = wc_price($buyer_premium_in_amt);
				$newsubtotal = "</br> + ".$get_win_price.' '.$by_pre_txt;
				$subtotal = sprintf( '%s %s', $subtotal, $newsubtotal ); 
			}
		}
	}	
	return $subtotal;
}


/* Total of buyers premium display in cart  */
add_action( 'woocommerce_cart_calculate_fees', 'uwa_add_buyers_premium_to_cart' );

function uwa_add_buyers_premium_to_cart($cart) {
	if(is_checkout() || is_cart()){
 
		global $woocommerce, $product;
		$cart_items = $woocommerce->cart->get_cart();
		
		$i = 0;
		$charged_total = 0;		
		$tax_cms = array();		
		
		foreach($cart_items as $item){				
			$product_id = $item['product_id'];
			$product = wc_get_product( $product_id );
		
			$pro_type = $product->get_type();
			if($pro_type == 'auction'){
				$w_current_userid = $product->get_uwa_auction_current_bider();
				$expired_auct = $product->get_uwa_auction_expired();
				if (!empty($w_current_userid) &&  $expired_auct == '2') {
					$buyer_premium_in_amt = get_post_meta($product_id,'_uwa_buyer_premium_amt', true);
					$w_product_price = $product->get_uwa_auction_current_bid();								
					//$buyer_premium_amt = uwa_get_buyer_premium_value($w_product_price);
					//$buyer_premium_in_amt = uwa_get_buyer_premium_value($product_id, $w_product_price);
					
					
					$tax_cms[] = array('tax_status' => $product->get_tax_status(),
						'tax_class' => $product->get_tax_class(),
						'bp_amt' => $buyer_premium_in_amt,
						'pro_title' => get_the_title($product_id));
					
					if(isset($buyer_premium_in_amt) && !empty($buyer_premium_in_amt)){
						$charged_total += $buyer_premium_in_amt;
					}
				}
				
			} /* end of if pro_type */
		} /* end of foreach */

	
		$tax_status = "";
		$tax_class = "";
		$bp_amt = "";
		$pro_title = "";

		foreach($tax_cms as $tax_cm){

			  	$tax_status = $tax_cm['tax_status'];
			   	$tax_class = $tax_cm['tax_class'];
			   	$bp_amt = $tax_cm['bp_amt'];
			   	$pro_title = $tax_cm['pro_title'];

			   	/* --aelia-- */
				$bp_amt = uwa_buyers_premium_display_aelia_value($product, $bp_amt);

				if($tax_status == "taxable"){
								
					if(!empty($tax_class)){
						
						//$cart->add_fee(__("Buyer's Premium for ".$pro_title, "woo_ua"), $bp_amt, true, $tax_class);		
						$cart->add_fee(__("Buyer's Premium for", "woo_ua")." ".$pro_title, $bp_amt, true, $tax_class);		
					}

					if(empty($tax_class)){
					
						//$cart->add_fee(__("Buyer's Premium for ".$pro_title, "woo_ua"), $bp_amt, true, "standard");
						$cart->add_fee(__("Buyer's Premium for", "woo_ua")." ".$pro_title, $bp_amt, true, "standard");
					}
				}

				if($tax_status == "none"){
				
					//$cart->add_fee(__("Buyer's Premium for ".$pro_title, "woo_ua"), $bp_amt);
					$cart->add_fee(__("Buyer's Premium for", "woo_ua")." ".$pro_title, $bp_amt);
				}
					
		} /* end of foreach */

		$buy_pre_amt = $charged_total;

		if(!empty($buy_pre_amt) && $buy_pre_amt > 0) {

			/* --aelia-- */
			$buy_pre_amt = uwa_buyers_premium_display_aelia_value($product, $buy_pre_amt);
		    // $cart->add_fee(__( "Total Buyer's Premium", "woo_ua" ), $buy_pre_amt);
		   
		}

		
	} /* end of if - is_chekcout */
}
/* buyers premium add to order item  */

add_action( 'woocommerce_add_order_item_meta', 'uwa_add_buyers_premium_to_order_item_meta', 10, 2 );
 
/* $item_id – order item ID */
/* $cart_item[ 'product_id' ] – associated product ID (obviously) */
function uwa_add_buyers_premium_to_order_item_meta( $item_id, $cart_item ) {

	$buyer_premium_in_amt = get_post_meta( $cart_item[ 'product_id' ], 
		'_uwa_buyer_premium_amt', true );
 
 	if( ! empty( $buyer_premium_in_amt ) ) {
		wc_update_order_item_meta( $item_id, '_uwa_buyer_premium_order_amt', 
			$buyer_premium_in_amt );
	} 
}

/* Front side my account order detail page. */
add_filter( 'woocommerce_order_formatted_line_subtotal', 
	'uwa_add_buyers_premium_to_myaccount_thank_you_order_detail', 10, 3 );

function uwa_add_buyers_premium_to_myaccount_thank_you_order_detail( $subtotal, 
	$item, $order ) {

	global $woocommerce, $product;
	
	$buyer_premium_in_amt = $item->get_meta('_uwa_buyer_premium_order_amt', true);	
	$auto_debit_bpm_amt = $item->get_meta('_uwa_stripe_charge_buyer_premium_order_amt', true);	
		if(empty($auto_debit_bpm_amt)) {
			if($buyer_premium_in_amt > 0){

				/* --aelia-- */
				$product = wc_get_product($item->get_product_id());
				$buyer_premium_in_amt = uwa_buyers_premium_display_aelia_value($product, $buyer_premium_in_amt);
				
				$by_pre_txt = __( "Buyer's Premium.", "woo_ua" );
				$get_win_price = wc_price ($buyer_premium_in_amt);	   
				$newsubtotal = "</br> + ".$get_win_price.' '.$by_pre_txt;  	  
				$subtotal = sprintf( '%s %s', $subtotal, $newsubtotal ); 
			}
		}	
    return $subtotal;
}

/* Hide custom field in admin order item */
add_filter( 'woocommerce_hidden_order_itemmeta', 
	'uwa_buyers_premium_woocommerce_hidden_order_itemmeta', 10, 1 ); 

function uwa_buyers_premium_woocommerce_hidden_order_itemmeta( $array ) { 
	$array[] = '_uwa_buyer_premium_order_amt';
    return $array;
}; 

/* Display order item meta in admin side */
//add_action( 'woocommerce_after_order_itemmeta', 'uwa_buyers_premium_order_meta_customized_display', 10, 3 );

function uwa_buyers_premium_order_meta_customized_display( $item_id, $item, $product ) {   

	// ----  note this function not in used ---- 

	$buyer_premium_in_amt = $item->get_meta('_uwa_buyer_premium_order_amt', true); 
	if($buyer_premium_in_amt > 0){

		/* --aelia-- */
		//$product = wc_get_product($item->get_product_id());
		$buyer_premium_in_amt = uwa_buyers_premium_display_aelia_value($product, 
			$buyer_premium_in_amt);


    	_e("Buyer's Premium", "woo_ua");
        echo " : ".wc_price($buyer_premium_in_amt);
	}
}

/* Display order item meta in admin side */
add_action( 'woocommerce_admin_order_item_headers', 
	'uwa_buyers_premium_action_woocommerce_admin_order_item_headers', 10, 3 );

function uwa_buyers_premium_action_woocommerce_admin_order_item_headers( $order ) {
    echo '<th class="item_buyercost sortable" data-sort="float" style="text-align: right;">Buyers Premium</th>';
}

/* Display order item meta in admin side */
add_action( 'woocommerce_admin_order_item_values', 
	'uwa_buyers_premium_action_woocommerce_admin_order_item_values', 10, 3 );
function uwa_buyers_premium_action_woocommerce_admin_order_item_values( $null, $item, 
	$absint ) {

	global $product;

    $buyer_premium_in_amt = $item->get_meta('_uwa_buyer_premium_order_amt', true);
    ?>
	    <td class="item_buyercost" data-sort-value="<?php echo $buyer_premium_in_amt; ?>">
	    	<div class="view" style="text-align: right; padding-right: 10px;">
		       	<?php
					if($buyer_premium_in_amt > 0){

						/* --aelia-- */
						$product = wc_get_product($item->get_product_id());
						$buyer_premium_in_amt = uwa_buyers_premium_display_aelia_value($product, 
							$buyer_premium_in_amt);
						
			    		echo wc_price($buyer_premium_in_amt);
			    	}
		        ?>
	        </div>
	    </td>
    <?php
}

/* hide/show  Buyer's preminum tab on Auction Detail Page */
if( get_option( 'uwa_buyers_premium_tab_hide' ) != 'yes' ) {	
	
	add_action('woocommerce_product_tabs', 'uwa_buyers_premium_tab_display');
 
	function uwa_buyers_premium_tab_display( $tabs ) {
		global $product;
		
		if(method_exists( $product, 'get_type') && $product->get_type() == 'auction') {

			$selling_type = $product->get_uwa_auction_selling_type();
			if($selling_type != "buyitnow"){

				$tabs['uwa_buyers_premium_tab'] = array(
					'title' => __("Buyer's Premium", 'woo_ua'),
					'priority' => 50,
					'callback' => 'uwa_buyers_premium_tab_display_callback',				
				);
				
			}
		}		
		return $tabs;
	}
	
	function uwa_buyers_premium_tab_display_callback($tabs) {
		global $product;

		$heading = __("Buyer's Premium", 'woo_ua');
		$heading_min = __("Minimum Premium", 'woo_ua');
		$heading_max = __("Maximum Premium", 'woo_ua');

		$product_id =  $product->get_id();
		$uwa_buyer_price = uwa_display_buyer_premium_price($product_id);

			/*$uwa_bpm_type = get_option('uwa_buyers_premium_type');
			if($uwa_bpm_type == "percentage"){
				$display_min_val = get_option("uwa_buyers_min_premium", "");
        		$display_max_val = get_option("uwa_buyers_max_premium", "");
        	}
        	else{
        		$display_min_val = "";
        		$display_max_val = "";
        	}*/

        	$all_data = uwa_get_buyer_premium_data($product_id);

        	if($all_data['bpm_type'] == "percentage"){
        		$display_min_val = $all_data['bpm_min'];
        		$display_max_val= $all_data['bpm_max'];
        	}
        	else{
        		$display_min_val = "";
        		$display_max_val = "";
        	}


		?>
			<h2><?php echo $heading; ?></h2>
			<table class="woocommerce-product-attributes shop_attributes">
				<tbody>
					<tr class="woocommerce-product-attributes-item 
						woocommerce-product-attributes-item--weight">
						<th class="woocommerce-product-attributes-item__label">
							<?php echo $heading; ?></th>
						<td class="woocommerce-product-attributes-item__value">
							<?php echo $uwa_buyer_price;?></td>
					</tr>

					<?php if($display_min_val > 0) { 

							/* --aelia-- */
							$display_min_val = uwa_buyers_premium_display_aelia_value($product, 
								$display_min_val);
						

						?>
						
						<tr class="woocommerce-product-attributes-item 
							woocommerce-product-attributes-item--weight">
							<th class="woocommerce-product-attributes-item__label">
								<?php echo $heading_min; ?></th>
							<td class="woocommerce-product-attributes-item__value">
								<?php //printf( "%s", get_woocommerce_currency_symbol()); ?>
								<?php //echo $display_min_val; ?>
								<?php echo wc_price($display_min_val,  array('decimals' => 0));?>
							</td>
						</tr>
					<?php } ?>

					<?php if($display_max_val > 0) { 

							/* --aelia-- */
							$display_max_val = uwa_buyers_premium_display_aelia_value($product, 
								$display_max_val);
							

						?>
						<tr class="woocommerce-product-attributes-item 
							woocommerce-product-attributes-item--weight">
							<th class="woocommerce-product-attributes-item__label">
								<?php echo $heading_max; ?></th>
							<td class="woocommerce-product-attributes-item__value">
								<?php //printf( "%s", get_woocommerce_currency_symbol()); ?>
								<?php //echo $display_max_val;?>
								<?php echo wc_price($display_max_val,  array('decimals' => 0));?>							
							</td>

						</tr>
					<?php } ?>
				
				
				</tbody>
			</table>
		
		<?php		
	}  
}

/* hide/show Buyer premium after bid button */
if( get_option( 'uwa_buyers_premium_aft_btn' ) == 'yes' ) {
	add_action( 'ultimate_woocommerce_auction_after_bid_form', 
		'uwa_buyers_premium_text_visible_fun');

    function uwa_buyers_premium_text_visible_fun() { 
    	global $product;

    	$selling_type = $product->get_uwa_auction_selling_type();		
		if($selling_type != "buyitnow"){

			$heading = __("Buyer's Premium", 'woo_ua');	
			$heading_min = __("Minimum Premium", 'woo_ua');
			$heading_max = __("Maximum Premium", 'woo_ua');

			$product_id =  $product->get_id();
			$uwa_buyer_price = uwa_display_buyer_premium_price($product_id);

				/*$uwa_bpm_type = get_option('uwa_buyers_premium_type');
				if($uwa_bpm_type == "percentage"){
					$display_min_val = get_option("uwa_buyers_min_premium", "");
	        		$display_max_val = get_option("uwa_buyers_max_premium", "");
	        	}
	        	else{
	        		$display_min_val = "";
	        		$display_max_val = "";
	        	}*/


	        	$all_data = uwa_get_buyer_premium_data($product_id);

	        	if($all_data['bpm_type'] == "percentage"){
	        		$display_min_val = $all_data['bpm_min'];
	        		$display_max_val= $all_data['bpm_max'];
	        	}
	        	else{
	        		$display_min_val = "";
	        		$display_max_val = "";
	        	}

	   		
			?>
				<p>
					<strong><?php echo $heading; ?></strong> : <?php echo $uwa_buyer_price;?>
				
					<?php if($display_min_val > 0) { 

							/* --aelia-- */
							$display_min_val = uwa_buyers_premium_display_aelia_value($product, $display_min_val);				

						?>

						</br> <strong><?php echo $heading_min; ?></strong> : 
						<?php //printf( "%s", get_woocommerce_currency_symbol()); ?>
						<?php //echo $display_min_val;?>
						<?php echo wc_price($display_min_val,  array('decimals' => 0));?> 
					<?php } ?> 

					<?php if($display_max_val > 0) { 

							/* --aelia-- */
							$display_max_val = uwa_buyers_premium_display_aelia_value($product, $display_max_val);

						?>
						</br> <strong><?php echo $heading_max; ?></strong> : 
						<?php //printf( "%s", get_woocommerce_currency_symbol()); ?>
						<?php //echo $display_max_val;?>
						<?php echo wc_price($display_max_val,  array('decimals' => 0));?>

					<?php } ?>
				</p>

		    <?php

	    } /* end of if - selling type */

	} 

} /* end of if */


/* display aelia value */
function uwa_buyers_premium_display_aelia_value($product, $buyers_premium){	
	
	$addons = uwa_enabled_addons();
	if(is_array($addons) && in_array('uwa_currency_switcher', $addons)){
		if($product->uwa_aelia_is_configure() == TRUE){
    		$buyers_premium = $product->uwa_aelia_base_to_active($buyers_premium);	        	
		}
	}

	return $buyers_premium;
}


/* display buyers text before pay now button in Expired auction only */
add_action('ultimate_woocommerce_auction_before_pay_now_button', 'uwa_calculated_buyers_auction', 200);
function uwa_calculated_buyers_auction($product){

	$product_id =  $product->get_id();

	$all_data = uwa_get_buyer_premium_data($product_id);
	$type = $all_data['bpm_type'];
	$fee_amt = $all_data['bpm_fee_amt'];

	/* --- display text only when buyers premium is set --- */
	if($fee_amt > 0 ) {

			/* --aelia-- */
		  	$product_base_currency = $product->uwa_aelia_get_base_currency();
  			$args = array("currency" => $product_base_currency);

			$check = 0;			
			$txt = "<br><strong>".__("Buyer's Premium - ", "woo_ua")."</strong>";

			if($type == "percentage"){
				
				$dis_type = __("Percentage", "woo_ua");

				$dis_min = $all_data['bpm_min'];
				$dis_max = $all_data['bpm_max'];
				$txt .= "<br>".__("Type :", "woo_ua")." ".$dis_type." -- ".$fee_amt.__("% of Winning Price.", "woo_ua");
				$txt .= "<br>";

					if($dis_min > 0 ){

							/* --aelia-- */
							$dis_min = uwa_buyers_premium_display_aelia_value($product, 
								$dis_min);				                    

						$txt .= "<span>(".__("Minimum -", "woo_ua")." ".wc_price($dis_min, $args).")</span>";
						$check = 1;
					}

					if($dis_max > 0 ){

							/* --aelia-- */
							$dis_max = uwa_buyers_premium_display_aelia_value($product, 
								$dis_max);	
						
						$txt .= "<span>(".__("Maximum -", "woo_ua")." ".wc_price($dis_max, $args).")</span>";
						$check = 1;
					}

			}else{			

					/* --aelia-- */
					$fee_amt = uwa_buyers_premium_display_aelia_value($product, 
						$fee_amt);				                    
								
				$dis_type = __("Flat Rate", "woo_ua");				
				$txt .= "<br>".__("Type :", "woo_ua")." ".$dis_type." -- ".wc_price($fee_amt, $args);
				$check = 1;
			}
			
			$charge_bp = get_post_meta($product_id, '_uwa_buyer_premium_amt', true);
			
					/* --aelia-- */
					$charge_bp = uwa_buyers_premium_display_aelia_value($product, 
						$charge_bp);	
			
			$dis_charge_bp = wc_price($charge_bp, $args);

			if($check == 1){
				$txt .= "<br>";
			}			
			
			$txt .= "<br><strong>".__("Calculated Buyer's Premium", "woo_ua")."</strong>";
			$txt .= "<br>".$dis_charge_bp;
			$txt .= "<br><Br>";
			echo $txt;

	} /* end of if - fee > 0 */	
	
}


/* display buyers text before pay now button in Winner mail user only */
add_action('woocommerce_email_before_pay_button', 'uwa_calculated_buyers_email', 200);
function uwa_calculated_buyers_email($product){

	$product_id =  $product->get_id();

	$all_data = uwa_get_buyer_premium_data($product_id);
	$type = $all_data['bpm_type'];
	$fee_amt = $all_data['bpm_fee_amt'];

	/* --- display text only when buyers premium is set --- */
	if($fee_amt > 0 ) {

			/* --aelia-- */
		  	$product_base_currency = $product->uwa_aelia_get_base_currency();
  			$args = array("currency" => $product_base_currency);

			$check = 0;			
			$txt = "<br><strong>".__("Buyer's Premium - ", "woo_ua")."</strong>";

			if($type == "percentage"){
				
				$dis_type = __("Percentage", "woo_ua");

				$dis_min = $all_data['bpm_min'];
				$dis_max = $all_data['bpm_max'];
				$txt .= "<br>".__("Type :", "woo_ua")." ".$dis_type." -- ".$fee_amt.__("% of Winning Price.", "woo_ua");
				$txt .= "<br>";

					if($dis_min > 0 ){

							/* --aelia-- */
							$dis_min = uwa_buyers_premium_display_aelia_value($product, 
								$dis_min);				                    

						$txt .= "<span>(".__("Minimum -", "woo_ua")." ".wc_price($dis_min, $args).")</span>";
						$check = 1;
					}

					if($dis_max > 0 ){

							/* --aelia-- */
							$dis_max = uwa_buyers_premium_display_aelia_value($product, 
								$dis_max);	
						
						$txt .= "<span>(".__("Maximum -", "woo_ua")." ".wc_price($dis_max, $args).")</span>";
						$check = 1;
					}

			}else{			

					/* --aelia-- */
					$fee_amt = uwa_buyers_premium_display_aelia_value($product, 
						$fee_amt);				                    
								
				$dis_type = __("Flat Rate", "woo_ua");				
				$txt .= "<br>".__("Type :", "woo_ua")." ".$dis_type." -- ".wc_price($fee_amt, $args);
				$check = 1;
			}
			
			$charge_bp = get_post_meta($product_id, '_uwa_buyer_premium_amt', true);
			
					/* --aelia-- */
					$charge_bp = uwa_buyers_premium_display_aelia_value($product, 
						$charge_bp);	
			
			$dis_charge_bp = wc_price($charge_bp, $args);

			if($check == 1){
				$txt .= "<br>";
			}			
			
			$txt .= "<br><strong>".__("Calculated Buyer's Premium", "woo_ua")."</strong>";
			$txt .= "<br>".$dis_charge_bp;
			$txt .= "<br><Br>";
			echo $txt;

	} /* end of if - fee > 0 */	
	
} 