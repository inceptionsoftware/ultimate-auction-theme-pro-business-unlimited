<?php


/**
 * Ultimate WooCommerce Auction Pro Importer
 *
 * @package Ultimate WooCommerce Auction Pro
 * @author Nitesh Singh 
 * @since 1.1.1 
 *
 */ 

		/* import auction product */	
add_filter( 'woocommerce_csv_product_import_mapping_options','uwa_add_column_to_importer');
		
add_filter( 'woocommerce_csv_product_import_mapping_default_columns','uwa_add_column_to_mapping_screen');
		
add_filter( 'woocommerce_product_import_pre_insert_product_object','uwa_process_import', 10, 2 );

/**
 * Register the 'Custom Column' column in the importer.
 *
 * @param array $options
 * @return array $options
 */
function uwa_add_column_to_importer( $options ) {

		/* check for auction product only */

	    // column slug in list  => column name in list
	    $options['woo_ua_auction_selling_type'] = 'Auction - Selling Type';
	    $options['woo_ua_product_condition'] = 'Auction - Product Condition';
	    $options['woo_ua_auction_type'] = 'Auction - Auction Type';
	    $options['uwa_auction_proxy'] = 'Auction - Enable Proxy Bid';
	    $options['uwa_auction_silent'] = 'Auction - Enable Slient Bid';

	    $options['woo_ua_opening_price'] = 'Auction - Opening Price';
	    $options['woo_ua_lowest_price'] = 'Auction - Lowest Price to Accept';
	    $options['woo_ua_bid_increment'] = 'Auction - Bid Increment';
	    $options['uwa_auction_variable_bid_increment'] = 'Auction - Enable Variable Increment'; 
	    $options['uwa_var_inc_price_val'] = 'Auction - Variable Increment'; 
	    
	    $options['_regular_price'] = 'Auction - Buy Now Price';
	    $options['woo_ua_auction_start_date'] = 'Auction - Start Date';
	    $options['woo_ua_auction_end_date'] = 'Auction - End Date';     

	    $options['uwa_auto_renew_enable'] = 'Auction - Enable AutoRelist'; 
	    $options['uwa_auto_renew_recurring_enable'] = 'Auction - Enable Recurring AutoRelist'; 

	    $options['uwa_auto_renew_not_paid_enable'] = 'Auction - Enable AutoRelist If WinnerNotPaid'; 
	    $options['uwa_auto_renew_not_paid_hours'] = 'Auction - If WinnerNotPaid Relist After (hours)'; 
	    $options['uwa_auto_renew_no_bids_enable'] = 'Auction - Enable AutoRelist If NoBids'; 
	    $options['uwa_auto_renew_fail_hours'] = 'Auction - If NoBids Relist After (hours)'; 

	    $options['uwa_auto_renew_no_reserve_enable'] = 'Auction - Enable AutoRelist If ReservePriceNotMet'; 
	    $options['uwa_auto_renew_reserve_fail_hours'] = 'Auction - If ReservePriceNotMet Relist After (hours)'; 

	    $options['uwa_auto_renew_duration_hours'] = 'Auction - AutoRelist Duration (hours)'; 	
	    
	    return $options;
}


/**
 * Add automatic mapping support for 'Custom Column'. 
 * This will automatically select the correct mapping for columns named 'Custom Column' or 'custom
  	column'.
 *
 * @param array $columns
 * @return array $columns
 */
function uwa_add_column_to_mapping_screen( $columns ) {
	    
	    // potential column name in file  => column slug in list
	    
	    $columns['Auction - Selling Type'] = 'woo_ua_auction_selling_type';
	    $columns['Auction - Product Condition'] = 'woo_ua_product_condition';
	    $columns['Auction - Auction Type'] = 'woo_ua_auction_type';
	    $columns['Auction - Enable Proxy Bid'] = 'uwa_auction_proxy';	    
	    $columns['Auction - Enable Slient Bid'] = 'uwa_auction_silent';
	    
	    $columns['Auction - Opening Price'] = 'woo_ua_opening_price';
	    $columns['Auction - Lowest Price to Accept'] = 'woo_ua_lowest_price';
	    $columns['Auction - Bid Increment'] = 'woo_ua_bid_increment';
	    $columns['Auction - Enable Variable Increment'] = 'uwa_auction_variable_bid_increment';
	    $columns['Auction - Variable Increment'] = 'uwa_var_inc_price_val';

	    $columns['Auction - Buy Now Price'] = '_regular_price'; 
	   	    
	    $columns['Auction - Start Date'] = 'woo_ua_auction_start_date';
	    $columns['Auction - End Date'] = 'woo_ua_auction_end_date';

	    $columns['Auction - Enable AutoRelist'] = 'uwa_auto_renew_enable';
	    $columns['Auction - Enable Recurring AutoRelist'] = 'uwa_auto_renew_recurring_enable';

	    $columns['Auction - Enable AutoRelist If WinnerNotPaid'] = 'uwa_auto_renew_not_paid_enable';
	    $columns['Auction - If WinnerNotPaid Relist After (hours)'] = 'uwa_auto_renew_not_paid_hours';

	    $columns['Auction - Enable AutoRelist If NoBids'] = 'uwa_auto_renew_no_bids_enable';
	    $columns['Auction - If NoBids Relist After (hours)'] = 'uwa_auto_renew_fail_hours';

	    $columns['Auction - Enable AutoRelist If ReservePriceNotMet'] = 
	    	'uwa_auto_renew_no_reserve_enable';
	    $columns['Auction - If ReservePriceNotMet Relist After (hours)'] = 
	    	'uwa_auto_renew_reserve_fail_hours';

	    $columns['Auction - AutoRelist Duration (hours)'] = 'uwa_auto_renew_duration_hours';
	
	    return $columns;
}

/**
 * Process the data read from the CSV file.
 * This just saves the value in meta data, but you can do anything you want here with the data.
 *
 * @param WC_Product $object - Product being imported or updated.
 * @param array $data - CSV data read for the product.
 * @return WC_Product $object
 */
function uwa_process_import( $object, $data ) {	

if($data['type'] == "auction"){

	$arr_selling_type = array('both', 'auction', 'buyitnow');
	$arr_proxy = array("yes", "0");
	$arr_slient = array("yes", "0");
	$arr_product_condition = array("new", "used");
	$arr_auction_type = array("normal", "reverse");


	/*  ------------------  selling type  --------------------  */
	if(isset($data['woo_ua_auction_selling_type'])){
		$i_selling_type = strtolower(trim($data['woo_ua_auction_selling_type']));

		if (in_array($i_selling_type, $arr_selling_type)) {
	        $object->update_meta_data( 'woo_ua_auction_selling_type', $i_selling_type );
	    } 
	    else {
	    	$object->update_meta_data( 'woo_ua_auction_selling_type', "both" );	    	
	    	/* "you have entered wrong selling type " */	    	
	    }
	} /* end of isset */


	/*  ------------------  product condition  --------------------  */   
    /* product condition can be anything */
    if(isset($data['woo_ua_product_condition'])){
	    $i_product_condition = strtolower(trim($data['woo_ua_product_condition']));
	    
	    //if ( ! empty( $i_product_condition ) ) {
	    if(in_array($i_product_condition, $arr_product_condition)){    
	        $object->update_meta_data( 'woo_ua_product_condition', $i_product_condition);
	    }
	    else{
	    	/* "you have entered wrong product condition " */
	    	$object->update_meta_data( 'woo_ua_product_condition', "new");
	    }
	} /* end of isset */

    
    /*  ------------------  auction type  --------------------  */   
    if(isset($data['woo_ua_auction_type'])){
	    $i_auction_type = strtolower(trim($data['woo_ua_auction_type']));    

	    if(in_array($i_auction_type, $arr_auction_type)){    
	        $object->update_meta_data( 'woo_ua_auction_type', $i_auction_type);
	    }
	    else{
	    	/* "you have entered wrong auction type " */
	    	$object->update_meta_data( 'woo_ua_auction_type', "normal");
	    }
	} /* end of isset */

    
    /*  ------------------  proxy and slient  --------------------  */   
    if(isset($data['uwa_auction_proxy']) && isset($data['uwa_auction_silent'])){
	    $i_proxy = strtolower(trim($data['uwa_auction_proxy']));
	    $i_slient = strtolower(trim($data['uwa_auction_silent']));
	    
	    if( $i_proxy == "yes" && $i_slient == "yes") {
	    	/* error - Both can not enabled at the same time */
	    	$object->update_meta_data( 'uwa_auction_proxy', "0" );
	    	$object->update_meta_data( 'uwa_auction_silent', "0" );
	    }
		else{
				if (in_array($i_proxy, $arr_proxy)) {
			        $object->update_meta_data( 'uwa_auction_proxy', $i_proxy );
			    }
			    else{
			    	/* "you have entered wrong proxy value "  or set as 0 */
			    	$object->update_meta_data( 'uwa_auction_proxy', "0" );
			    }

			    if (in_array($i_slient, $arr_slient)) {
			        $object->update_meta_data( 'uwa_auction_silent', $i_slient );
			    }
			    else{
			    	/* "you have entered wrong slient value "  or set as 0 */
			    	$object->update_meta_data( 'uwa_auction_silent', "0" );
			    }

		} /* end of else */
	} /* end of isset */
    

        /*  ------------------  opening price  --------------------  */ 
	if(isset($data['woo_ua_opening_price'])){
		$i_opening_price = $data['woo_ua_opening_price'];	

	    if ( $i_opening_price > 0 ) {
	        $object->update_meta_data( 'woo_ua_opening_price', $i_opening_price );
	    }
	    else{
	    	/* error - "you have entered wrong opening price " */
	    	$object->update_meta_data( 'woo_ua_opening_price', "" );
	    }
	} /* end of isset */

    
    /*  ------------------  lowest price  --------------------  */ 
   	if(isset($data['woo_ua_lowest_price'])){
	    $i_lowest_price = $data['woo_ua_lowest_price'];

	    if ( $i_lowest_price >= 0 ) {
	        $object->update_meta_data( 'woo_ua_lowest_price', $i_lowest_price );
	    }
	    else{
	    	/* error - "you have entered wrong lowest price " */	
	    	$object->update_meta_data( 'woo_ua_lowest_price', "" );
	    }
	} /* end of isset */


	/*  ------------------  buy now price  --------------------  */ 
	if(isset($data['_regular_price'])){
		$i_buynow_price = $data['_regular_price'];
		$object->update_meta_data( 'uwa_auction_via_importing', "yes" );
	    if ( $i_buynow_price >= 0 ) {
	        $object->update_meta_data( '_regular_price', $i_buynow_price );
	        $object->update_meta_data( '_price', $i_buynow_price );
	    }
	} /* end of isset */

    
    /*  ------------------  start date and end date  --------------------  */ 
	if(isset($data['woo_ua_auction_start_date']) && isset($data['woo_ua_auction_end_date'])){

	    $i_start_date = $data['woo_ua_auction_start_date'];
	    $i_end_date = $data['woo_ua_auction_end_date'];

	    if(!empty($i_start_date) && !empty($i_end_date)){	    	
			
			$sd_timestamp = strtotime($i_start_date);
			$ed_timestamp = strtotime($i_end_date);
			if($sd_timestamp != false && $ed_timestamp != false ){

			    	if($sd_timestamp < $ed_timestamp){

			    		/* startdate */		    		
			    		$new_startdt_format = date('Y-m-d H:i:s', $sd_timestamp);    	
				        $object->update_meta_data( 'woo_ua_auction_start_date', $new_startdt_format );
		    		
			    		/* enddate */
			    		$new_enddt_format = date('Y-m-d H:i:s', $ed_timestamp);
	        			$object->update_meta_data( 'woo_ua_auction_end_date', $new_enddt_format );

			    	}
			    	else{
			    		/* "end date must be greater than start date" */	
			    	}
			}
			else{
				/* "you have entered wrong start or end date" */
			}
	    }
	    else{
	    	/* error - "start date or end date is blank" */
	    }   
	} /* end of isset */



	/*  ------------------  Auto Relist  --------------------  */ 
	if(isset($data['uwa_auto_renew_enable'])){
    
	    $i_enable_autorelist = strtolower(trim($data['uwa_auto_renew_enable']));
	    if($i_enable_autorelist == "yes" || $i_enable_autorelist == ""){

		    if($i_enable_autorelist == "yes"){

		    	$object->update_meta_data( 'uwa_auto_renew_enable', $i_enable_autorelist );

		    	/* uwa_auto_renew_recurring_enable */
		    	$i_enable_recurring = strtolower(trim($data['uwa_auto_renew_recurring_enable']));
		    	if($i_enable_recurring == "yes"){
		    		$object->update_meta_data( 'uwa_auto_renew_recurring_enable', $i_enable_recurring );
		    	}

		    	/* duration hours */
		    	$i_relist_duration_hrs = $data['uwa_auto_renew_duration_hours'];
		    	if($i_relist_duration_hrs >= 0 || $i_relist_duration_hrs = "" ){
		    		$object->update_meta_data( 'uwa_auto_renew_duration_hours', $i_relist_duration_hrs );
		    	}
		    	else{
		    			/* gives error for hrs if needed */
		    		$object->update_meta_data( 'uwa_auto_renew_duration_hours', "" );
		    	}
	   	
		    	
		    	/* winner not paid */	    	
		    	$i_enable_notpaid = strtolower(trim($data['uwa_auto_renew_not_paid_enable']));	    	
		    	if($i_enable_notpaid == "yes"){

		    		$object->update_meta_data( 'uwa_auto_renew_not_paid_enable', 
		    			$i_enable_notpaid );

		    		$i_notpaid_hrs = $data['uwa_auto_renew_not_paid_hours'];
		    		if($i_notpaid_hrs >= 0 || $i_notpaid_hrs = "" ){
		    			$object->update_meta_data( 'uwa_auto_renew_not_paid_hours', $i_notpaid_hrs );
		    		}
		    		else{
		    			/* gives error for hrs if needed */
		    			$object->update_meta_data( 'uwa_auto_renew_not_paid_hours', "" );
		    		}
		    	}

		    	/* autorelist if no bids placed */	    	
		    	$i_enable_nobidsplaced = strtolower(trim($data['uwa_auto_renew_no_bids_enable']));	    
		    	if($i_enable_nobidsplaced == "yes"){

		    		$object->update_meta_data( 'uwa_auto_renew_no_bids_enable', 
		    			$i_enable_nobidsplaced );

		    		$i_nobidsplaced_hrs = $data['uwa_auto_renew_fail_hours'];
		    		if($i_nobidsplaced_hrs >= 0 || $i_nobidsplaced_hrs = "" ){
		    			$object->update_meta_data( 'uwa_auto_renew_fail_hours', $i_nobidsplaced_hrs );
		    		}
		    		else{
		    			/* gives error for hrs if needed */
		    			$object->update_meta_data( 'uwa_auto_renew_fail_hours', "" );
		    		}
		    	}


		    	/* autorelist if reserve price not met */	    	
		    	$i_enable_reserveprice = strtolower(trim($data['uwa_auto_renew_no_reserve_enable']));   
		    	if($i_enable_reserveprice == "yes"){

		    		$object->update_meta_data( 'uwa_auto_renew_no_reserve_enable', 
		    			$i_enable_reserveprice );

		    		$i_reserveprice_hrs = $data['uwa_auto_renew_reserve_fail_hours'];
		    		if($i_reserveprice_hrs >= 0 || $i_reserveprice_hrs = "" ){
		    			$object->update_meta_data( 'uwa_auto_renew_reserve_fail_hours', $i_reserveprice_hrs );
		    		}
		    		else{
		    			/* gives error for hrs if needed */
		    			$object->update_meta_data( 'uwa_auto_renew_reserve_fail_hours', "" );
		    		}
		    	}
		    	
		    } /* end of autorelist enable */
		}
		else{
			/* you have entered wrong value in Enable autorelist */
		}
	
	} /* end of isset */
	

	/*  ------------------  Variable and Simple Bid Increment  --------------------  */ 
	
	if(isset($data['woo_ua_bid_increment']) || isset($data['uwa_auction_variable_bid_increment'])){

		/*  --- simple bid increment  ---  */	
		$i_bid_increment = $data['woo_ua_bid_increment'];

		if($i_bid_increment != ""){
			if ( $i_bid_increment >= 0 ) {
	        	$object->update_meta_data( 'woo_ua_bid_increment', $i_bid_increment );
			}
			else{
				/* no error "you have entered wrong bid increment" */
			}
		}
		else{

			/*  --- variable bid increment  ---  */
			$i_enable_variable_bidinc = $data['uwa_auction_variable_bid_increment'];
			if($i_enable_variable_bidinc == "yes"){

				/* add variable increment price */
				$i_variable_bid_inc = $data['uwa_var_inc_price_val'];

				if(!empty($i_variable_bid_inc)) {
						$array_data = array();
						$jsondatas = explode("*", $i_variable_bid_inc);				
						$i = 0;
						$count_error = 0;
						foreach ($jsondatas as $jsondata){					
							$variable_val = explode("-", $jsondata);

							if(isset($variable_val[0]) && isset($variable_val[1]) && isset($variable_val[2])){
								$start = $variable_val[0];
								$end = $variable_val[1];
								$inc_val = $variable_val[2];

								if($start > 0 && ($end > 0 || $end == "onwards") && $inc_val > 0 ){
									if($end=="onwards"){
										$i="onwards";
									}

									$array_data[$i] = array("start"=>$start,"end"=>$end,"inc_val"=>$inc_val);
								}
								else{
									$count_error++;
								}
								$i++;

							} /* end of if - isset variable_val */

							else{
								$count_error++;
							}					
							
						} /* end of foreach */


						if($count_error > 0){
							/* Error - you have entered wrong format or value in  variable increment */
						}
						else{

							$exist_onwards = array_key_exists("onwards", $array_data);
							if($exist_onwards == false){
								$array_data['onwards'] = array('start' => "" ,'end'=> 'onwards', 
									'inc_val' => "");

							}

							$arr_i_variable_bid_inc  = $array_data;

							$object->update_meta_data( 'uwa_auction_variable_bid_increment', 
								$i_enable_variable_bidinc );
							$object->update_meta_data( 'uwa_var_inc_price_val', 
								$arr_i_variable_bid_inc );
						}	  
				
				} /* end of if - empty */

			}  /* end of if - enabled variable inc */

		} /* end of else */

	} /* end of isset */


} /* end of main if - type= auction */


    return $object;

}

add_action( 'woocommerce_product_import_inserted_product_object', 'uwa_admin_force_update_buynow_price', 10, 2 );
function uwa_admin_force_update_buynow_price( $object, $data ){
	if($data['type'] == "auction"){
		$product_id = $object->get_id();
		$via_importing = get_post_meta($product_id, 'uwa_auction_via_importing', true);
		$_price = get_post_meta($product_id, '_price', true);
		$_regular_price = get_post_meta($product_id, '_regular_price', true);
		if($via_importing =="yes"){
			
			delete_post_meta($product_id, '_price' );
			update_post_meta($product_id, '_price', wc_format_decimal(wc_clean($_regular_price)))	;
			//delete_post_meta($product_id, 'uwa_auction_via_importing' );	
		}
	}
}
