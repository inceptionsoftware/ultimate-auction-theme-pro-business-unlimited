<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 *
 * WC_Product_Auction Class
 *
 * @class  WC_Product_Auction
 * @package Ultimate WooCommerce Auction PRO
 * @author Nitesh Singh
 * @since 1.0
 *
 */

if ( ! class_exists( 'WC_Product_Auction' ) && class_exists( 'WC_Product' ) ) {	

class WC_Product_Auction extends WC_Product {
		
	/**
	 * Product Type and Post Type     
	 */
	public $post_type = 'product';
	public $product_type = 'auction';

	/**
	 * Stores product data.     
	 * Single product
	 */
	protected $results_data = array();

	/**
	 * Constructor gets the post object and sets the ID for the loaded product.	
	 *
	 * @param $product
	 *
	 */
	public function __construct( $product ) {		
		global $sitepress;		

		if(is_array($this->data))
			$this->data = array_merge( $this->data, $this->results_data );

		
		$this->uwa_auction_item_condition_array = apply_filters( 'ultimate_woocommerce_auction_product_condition',array( 'new' => __('New', 'woo_ua'), 
			'used'=> __('Used', 'woo_ua') ));
		
		parent::__construct( $product );
		$this->is_uwa_expired();
		$this->is_uwa_live();	
	}

	/**
	 * Returns the Single product or unique ID for this object.	
	 *
	 */
	public function get_id() {		
		return $this->id; 
	}

	/**
	 * Get Product Type.
	 *		
	 */
	public function get_type() {
		return 'auction';
	}

	/**
	 * Checks if a product is auction
	 *	
	 */
	function is_auction() {
		return $this->get_type() == 'auction' ? true : false;
	}
	/**
	 * Get Product Auction condition
	 *	
	 */
	function get_uwa_condition() {		
		
		if ($this->get_uwa_auction_item_condition()){
			return  $this->uwa_auction_item_condition_array[$this->get_uwa_auction_item_condition()];
		} else {
			return FALSE;
		}	
	}	

	/**
	 * Get Auction Product Condition
	 *	
	 */
	public function get_uwa_auction_item_condition( $context = 'view' ) {		
	   return get_post_meta( $this->get_uwa_wpml_default_product_id(), 'woo_ua_product_condition', true );
	}	

	/**
	 * Get Auction Product Type
	 *	
	 */
	public function get_uwa_auction_type( $context = 'view' ) {		 
		return get_post_meta( $this->get_uwa_wpml_default_product_id(), 'woo_ua_auction_type', true );		
	}	

	/**
	 * Check Auction Product Reserve Met/Not met
	 *	
	 */
	function is_uwa_reserve_met() {
		$reserved_price = $this->get_uwa_auction_reserved_price();

		if (!empty($reserved_price)){
			if($this->get_uwa_auction_type() == 'reverse' ){
				return ( (float)$this->get_uwa_auction_reserved_price() >= (float)$this->get_uwa_auction_current_bid());
			} else {
				return ( (float)$this->get_uwa_auction_reserved_price() <= (float)$this->get_uwa_auction_current_bid());
			}			
		}
		return TRUE;
	}

	/**
	 * Check Auction Product Has Reserve Price
	 *	
	 */
	function is_uwa_reserved() {

		if ($this->get_uwa_auction_reserved_price()){
			return TRUE;
		} else {
			return FALSE;
		}
	}	

	/**
	 * Get Auction Product Reserve Price
	 *	
	 */
	public function get_uwa_auction_reserved_price( $context = 'view' ) {
		return get_post_meta( $this->get_uwa_wpml_default_product_id(), 'woo_ua_lowest_price', true );
	}	

	/**
	 * Get Auction Product Opening Price
	 *	
	 */
	public function get_uwa_auction_start_price( $context = 'view' ) {
		return get_post_meta( $this->get_uwa_wpml_default_product_id(), 'woo_ua_opening_price', true );
	}	

	/**
	 * Get Auction Product Bid Value
	 *	
	 */
	public function uwa_bid_value() {		
		/*$addons = uwa_enabled_addons();
		if(is_array($addons) && in_array('uwa_currency_switcher', $addons)){
			if($this->uwa_aelia_is_configure() == TRUE){				
				return $this->uwa_aelia_base_to_active($this->uwa_get_bid_value());
			}
		}*/
		return $this->uwa_get_bid_value();
	}

	/**
	 * Get Auction Product Bid Increment Value
	 *	
	 */
	function get_uwa_increase_bid_value() {

		if ($this->get_uwa_auction_bid_increment()){
			return $this->get_uwa_auction_bid_increment();
		} else {
			return FALSE;
		}
	}

	/**
	 * Get Auction Product Bid Increment Value
	 *	
	 */
	public function get_uwa_auction_bid_increment( $context = 'view' ) {
		$ua_inc_price = "";
		$uwa_fixed_inc = get_post_meta($this->get_uwa_wpml_default_product_id(), 'woo_ua_bid_increment', true);
		$uwa_variable_inc_enable = get_post_meta($this->get_uwa_wpml_default_product_id(), 'uwa_auction_variable_bid_increment', true);
		
		$curr_price = $this->get_uwa_current_bid();
	    if(!empty($uwa_fixed_inc)){
			$ua_inc_price = get_post_meta($this->get_uwa_wpml_default_product_id(), 'woo_ua_bid_increment', true);
		}
		elseif($uwa_variable_inc_enable == 'yes'){
		
			$ua_inc_price_range = get_post_meta($this->get_uwa_wpml_default_product_id(), 'uwa_var_inc_price_val', true);
			foreach($ua_inc_price_range as $range){
				if( ($range['start'] <= $curr_price) && ($range['end']>=$curr_price) )
				{
					$ua_inc_price = $range['inc_val'];
					
					break;
				}
				
				if( ($range['start'] <= $curr_price) && ($range['end'] == 'onwards') )
				{  	
					$ua_inc_price = $range['inc_val'];
					break;
				}
			}
		}

		
		return $ua_inc_price;			
		
	}

	/**
	 * Get Auction Product Current bid
	 *	
	 */
	function get_uwa_current_bid() {

		if ($this->get_uwa_auction_current_bid()){
			$current_bid = ((float)$this->get_uwa_auction_current_bid());
			return $current_bid;
		}

		$current_bid = ((float)$this->get_uwa_auction_start_price());
		return $current_bid;
	}

	/**
	 * Get Auction Product Bid Count
	 *	
	 */
	public function get_uwa_auction_bid_count( $context = 'view' ) {
		return get_post_meta( $this->get_uwa_wpml_default_product_id(), 'woo_ua_auction_bid_count', true );
	}

	/**
	 * Get Auction Product Maximum Current Bidder
	 *	
	 */
	public function get_uwa_auction_max_current_bider( $context = 'view' ) {
		return get_post_meta( $this->get_uwa_wpml_default_product_id(), 'woo_ua_auction_max_current_bider', true );
	}

	/**
	 * Get Auction Product Maximum Current Bid	
	 *
	 */
	public function get_uwa_auction_max_bid( $context = 'view' ) {
		return get_post_meta( $this->get_uwa_wpml_default_product_id(), 'woo_ua_auction_max_bid', true );
	}

	/**
	 * Get Auction Product End Time
	 *	
	 */
	function get_uwa_auctions_end_time() {

		if ($this->get_uwa_auction_end_dates()){
			return $this->get_uwa_auction_end_dates();
		} else {
			return FALSE;
		}
	}

	/**
	 * Get Auction Product End Date
	 *	
	 */
	public function get_uwa_auction_end_dates( $context = 'view' ) {
		return get_post_meta( $this->get_uwa_wpml_default_product_id(), 'woo_ua_auction_end_date', true );
	}	

	/**
	 * Get Auction Product Start Time
	 *	
	 */
	function get_uwa_auction_start_time() {

		if ($this->get_uwa_auction_start_dates()){
			return $this->get_uwa_auction_start_dates();
		} else {
			return FALSE;
		}
	}

	/**
	 * Get Auction Product Start Date
	 *	
	 */
	public function get_uwa_auction_start_dates( $context = 'view' ) {
		return get_post_meta( $this->get_uwa_wpml_default_product_id(), 'woo_ua_auction_start_date', true );
	}	

	/**
	 * Get Auction Product Remaining Second Count
	 *  
	 */
	function get_uwa_remaining_seconds() {

		if ($this->get_uwa_auction_end_dates()){
			$second_count = strtotime($this->get_uwa_auction_end_dates())  -  (get_option( 'gmt_offset' )*3600);
			return $second_count;

		} else {
			return FALSE;
		}
	}

	/**
	 * Get Auction Product Remaining Second Count
	 *  
	 */
	function get_uwa_seconds_to_start_auction() {		
		
		if ($this->get_uwa_auction_start_dates()){
				$second_count = strtotime($this->get_uwa_auction_start_dates())  -  (get_option( 'gmt_offset' )*3600);
			return $second_count;

		} else {
			return FALSE;
		}		
	}
	
	/**
	 * Is Auction Product closed
	 *	 
	 */
		function is_uwa_expired() {
		
		$id = $this->get_uwa_wpml_default_product_id();
		$closed_auction = $this->get_uwa_auction_expired();
		if (!empty($closed_auction)){

					return TRUE;

		}else {

			if ($this->is_uwa_completed() && $this->is_uwa_live() ){

				if ( !$this->get_uwa_auction_current_bider() && !$this->get_uwa_auction_current_bid()){
					update_post_meta( $id, 'woo_ua_auction_closed', '1');
					update_post_meta( $id, 'woo_ua_auction_fail_reason', '1');	
					do_action('ultimate_woocommerce_auction_close',  $id);	
					$order_id = FALSE;					
					return FALSE;
				}

				if ($this->is_uwa_reserve_met() == FALSE){

					/* maxbid >= reserve price then set maxbidder is winner */

					if($this->get_uwa_auction_proxy() == "yes"){

						$maxbid = $this->get_uwa_auction_max_bid();
						if($maxbid > 0){
							$maxbid_user = $this->get_uwa_auction_max_current_bider();
							$reserved_price = $this->get_uwa_auction_reserved_price();

							if($maxbid >= $reserved_price){
								//update_post_meta( $id, 'woo_ua_auction_current_bid', $maxbid);
								update_post_meta( $id, 'woo_ua_auction_current_bid', $reserved_price);
								update_post_meta( $id, 'woo_ua_auction_current_bider', $maxbid_user);

								/* insert maxbid as bid to log table */
								$bid_obj = new UWA_Bid;
								//$bid_inserted = $bid_obj->history_bid($id, $maxbid, get_userdata($maxbid_user), 1);
								$bid_inserted = $bid_obj->history_bid($id, $reserved_price, get_userdata($maxbid_user), 1);
									//if($bid_inserted){
									//	$bid_count = (int)$this->get_uwa_auction_bid_count() + 1;
									//	update_post_meta($id, 'woo_ua_auction_bid_count', $bid_count);
									//}
							}
							else{
								update_post_meta( $id, 'woo_ua_auction_closed', '1');
								update_post_meta( $id, 'woo_ua_auction_fail_reason', '2');
								do_action('ultimate_woocommerce_auction_close',  $id);
								$order_id = FALSE;									
								return FALSE;
							}
						}
					}
					else{
							update_post_meta( $id, 'woo_ua_auction_closed', '1');
							update_post_meta( $id, 'woo_ua_auction_fail_reason', '2');
							do_action('ultimate_woocommerce_auction_close',  $id);
							$order_id = FALSE;									
							return FALSE;
					}
				}

				update_post_meta( $id, 'woo_ua_auction_closed', '2');
				add_user_meta( $this->get_uwa_auction_current_bider(), 'woo_ua_auction_win', $id);
				do_action('ultimate_woocommerce_auction_close',  $id);
				
				$winneruser = $this->get_uwa_auction_current_bider();
				if($winneruser){

					/* winner sms */
					do_action('ultimate_woocommerce_auction_winner_sms',  $id);
					
					$call_autodabit = get_post_meta($id, "woo_ua_winner_request_sent_for_autodabit_payment", true);
					if ( $call_autodabit !='1' ){
						add_post_meta($id, 'woo_ua_winner_request_sent_for_autodabit_payment','1');
						do_action('ultimate_woocommerce_auction_autodabit_payment',  $id);
					}
					
					WC()->mailer();			 
			        $mail_sent = get_post_meta($id, "woo_ua_winner_mail_sent", true);
					if ( $mail_sent !='1' ) {  
						  do_action('woo_ua_auctions_won_email_bidder', $id ,$winneruser);
						  do_action('woo_ua_auctions_won_email_bidder_admin', $id ,$winneruser);
					} 					 
					if( $this->get_uwa_auction_proxy()=="yes" || $this->get_uwa_auction_silent() == "yes" ) {
					
						do_action('woo_ua_auctions_loser_email_bidder', $id ,$winneruser);	
						
					}
					
					 /* update winner mail sent meta data  */
					 update_post_meta( $id, 'woo_ua_winner_mail_sent', '1');
				}				
				
				return TRUE;

			} else {
				return FALSE;
			}
		} /* end of else */
			
	}

	/**
	 * Get Auction Product Closed
	 *
	 */
	public function get_uwa_auction_expired( $context = 'view' ) {
		return get_post_meta( $this->get_uwa_wpml_default_product_id(), 'woo_ua_auction_closed', true );
	}

	/**
	 * Is Auction Product Started
	 * 	 
	 */	
	function is_uwa_live() {
		
		$id = $this->get_uwa_wpml_default_product_id();		
		$started_auction = $this->get_uwa_auction_has_started();
	
		if($started_auction === '1' ){			
			return TRUE;
		}

		if ($this->get_uwa_auction_start_dates() != false ){
			
			$date1 = new DateTime($this->get_uwa_auction_start_dates());
			$date2 = new DateTime(current_time('mysql'));
			if ($date1 < $date2){
				update_post_meta( $id, 'woo_ua_auction_has_started', '1');
				delete_post_meta( $id, 'woo_ua_auction_started');	
				do_action('ultimate_woocommerce_auction_started',$id);	
			} else{
				update_post_meta( $id, 'woo_ua_auction_started', '0');
			}
			return ($date1 < $date2);

		} else {			
			return FALSE;
		}
	}

	/**
	 * Get Auction Product Has Started
	 *	
	 */
	public function get_uwa_auction_has_started( $context = 'view' ) {
		return get_post_meta( $this->get_uwa_wpml_default_product_id(), 'woo_ua_auction_has_started', true );
	}

	/**
	 * Is Auction Product Has Finished
	 *	
	 */
	function is_uwa_completed() {
		
		$end_dates = $this->get_uwa_auction_end_dates();
		
		if (!empty($end_dates)){
			
			$date1 = new DateTime($this->get_uwa_auction_end_dates());
			$date2 = new DateTime(current_time('mysql'));

			if( $date1 < $date2){				

		 	    return TRUE;
			 
			} else{
				
			   return FALSE;
			}
			
		} else {
			return FALSE;
		}
	}	

	/**
	 * Check if Auction Product is on user watchlist
	 *	
	 */
	public function is_uwa_user_watching( $user_ID = false){

		$post_id = $this->get_uwa_wpml_default_product_id();

		if(!$user_ID){
			$user_ID = get_current_user_id();
		}

		$users_watching_auction = get_post_meta( $post_id, 'woo_ua_auction_watch', FALSE );

		if(is_array($users_watching_auction) && in_array($user_ID, $users_watching_auction)){
			
			$return =  true;
		  
		} else{
			
			$return =  false;
		}

		return $return;
	}
	/**
	 * Check Bidder is bidding on auction 
	 *	
	 */
    public function is_uwa_user_biding( $auction_id , $user_ID = false){

    	global $wpdb;

		$id = $this->get_uwa_wpml_default_product_id();

		if(!$user_ID){
			$user_ID = get_current_user_id();
		}

		$bid_count = $wpdb->get_var( 'SELECT COUNT(*) 	FROM '.$wpdb->prefix.'woo_ua_auction_log  WHERE auction_id =' .$id .' and userid = '.$user_ID);

		return  apply_filters('ultimate_woocommerce_auction_is_bidder_biding' ,intval($bid_count) , $this );

	}
	/**
	 * Get Auction Product Payed
	 *	
	 */
	public function get_uwa_auction_payed( $context = 'view' ) {
		return get_post_meta( $this->get_uwa_wpml_default_product_id(), 'woo_ua_auction_payed', true );
	}

	/**
	 * Get Auction Product Current Bid
	 *	
	 */
	public function get_uwa_auction_current_bid( $context = 'view' ) {
		return get_post_meta( $this->get_uwa_wpml_default_product_id(), 'woo_ua_auction_current_bid', true );
	}

	/**
	 * Get Auction Product Current Bidder
	 *	
	 */
	public function get_uwa_auction_current_bider( $context = 'view' ) {		 
		 return get_post_meta( $this->get_uwa_wpml_default_product_id(), 'woo_ua_auction_current_bider', true );		
	}

	/**
	 * Get Auction Product Bid logs
	 *	
	 */
	function uwa_auction_log_history($datefrom = FALSE, $user_id = FALSE) {
		global $wpdb;		
		$wheredatefrom ='';

		$id = $this->get_uwa_wpml_default_product_id();	
		$uwa_relist_options = get_option('uwa_relist_options','uwa_relist_start_from_beg');
		$relisteddate = get_post_meta( $id, 'uwa_auction_relisted', true );
        if(!is_admin() && !empty($relisteddate) && $uwa_relist_options=="uwa_relist_start_from_beg"){
            $datefrom = $relisteddate;
        }
		if($datefrom){
			$wheredatefrom =" AND CAST(date AS DATETIME) > '$datefrom' ";
		}

		if($user_id){
			$wheredatefrom =" AND userid = $user_id";
		}
		
		if($this->get_uwa_auction_type() == 'reverse' ){
			$logs = $wpdb->get_results( 'SELECT * 	FROM '.$wpdb->prefix.'woo_ua_auction_log  WHERE auction_id =' . $id . $wheredatefrom.' ORDER BY  `date` desc , `bid`  asc, `id`  desc   ');
		} else {
			$logs = $wpdb->get_results( 'SELECT * 	FROM '.$wpdb->prefix.'woo_ua_auction_log  WHERE auction_id =' . $id . $wheredatefrom.' ORDER BY  `date` desc , `bid`  desc ,`id`  desc  ');
		}
		
		return $logs;
	}
	
	/**
     * Get Auction Product last Bid logs 
     *    
     * @return object
     *
     */
	function uwa_auction_log_history_last($id) {
		global $wpdb;
		$datetimeformat = get_option('date_format').' '.get_option('time_format');	
		$log_data = '';
		$log_value = $wpdb->get_row( 'SELECT * 	FROM '.$wpdb->prefix.'woo_ua_auction_log  WHERE auction_id =' . $id .' ORDER BY  `date` desc ');

		if($log_value){
			$log_data = "<tr>";
				$log_data .= "<td class='bid_username'>".uwa_user_display_name($log_value->userid)."</td>";				
	            $log_data .= "<td class='bid_date'>".mysql2date($datetimeformat ,$log_value->date)."</td>";
	            $log_data .= "<td class='bid_price'>".wc_price($log_value->bid)."</td>";
				if ($log_value->proxy == 1)
	                $log_data .= " <td class='proxy'>".__('Auto', 'woo_ua')."</td>";
	            else
	                $log_data .= " <td class='proxy'></td>";	
	         $log_data .= "</tr>";
	    }     
		return $log_data;
	}
		
	/**
	 * Over write Woocommerce get_price_html for Auction Product
	 *	
	 */
	public function get_price_html( $price = '' ) {
		
		$id = $this->get_uwa_wpml_default_product_id();
		$auction_selling_type = $this->get_uwa_auction_selling_type();
		if($auction_selling_type == "auction" || $auction_selling_type == "both" || 	$auction_selling_type == ""){
		
		if ($this->is_uwa_expired() && $this->is_uwa_live() ){
			
			if ($this->get_uwa_auction_expired() == '3'){
				
				$price = __('<span class="woo-ua-sold-for sold_for">Sold for</span>: ','woo_ua').wc_price($this->get_price());
			}
			else{
				
				if ($this->get_uwa_auction_current_bid()){

					if ( $this->is_uwa_reserve_met() == FALSE){
						
						$price = __('<span class="woo-ua-winned-for reserve_not_met">Reserve price Not met!</span> ','woo_ua');
						
					} else{
						$price = __('<span class="woo-ua-winned-for winning_bid">Winning Bid</span>: ','woo_ua').wc_price($this->get_uwa_auction_current_bid());
					}
				}
				else{
					$price = __('<span class="woo-ua-winned-for expired">Auction Expired</span> ','woo_ua');
				}


			} /* end of else */

		} elseif(!$this->is_uwa_live()){
			
			$price = '<span class="woo-ua-auction-price starting-bid" data-auction-id="'.$id.'" data-bid="'.$this->get_uwa_auction_current_bid().'" data-status="">'.__('<span class="woo-ua-starting auction">Starting bid</span>: ','woo_ua').wc_price($this->get_uwa_current_bid()).'</span>';
			
		} else {
			
			if($this->get_uwa_auction_silent() == 'yes'){
				$price = '<span class="woo-ua-auction-price" data-auction-id="'.$id.'"  data-status="running">'.__('<span class="current auction">This auction is silent bid.</span> ','woo_ua').'</span>';
			} else{
			
				if (!$this->get_uwa_auction_current_bid()){
					$price = '<span class="woo-ua-auction-price starting-bid" data-auction-id="'.$id.'" data-bid="'.$this->get_uwa_auction_current_bid().'" data-status="running">'.__('<span class="woo-ua-current auction">Starting bid</span>: ','woo_ua').wc_price($this->get_uwa_current_bid()).'</span>';
				} else {
					$price = '<span class="woo-ua-auction-price current-bid" data-auction-id="'.$id.'" data-bid="'.$this->get_uwa_auction_current_bid().'" data-status="running">'.__('<span class="woo-ua-current auction">Current bid</span>: ','woo_ua').wc_price($this->get_uwa_current_bid()).'</span>';
				}
			}

		}
		
		} else {
			
			$price = wc_price($this->get_price());
		}

		return apply_filters( 'woocommerce_get_price_html', $price, $this );
	}
	
	/**
	 * Get the Product's Price.
	 *	
	 */
	function get_price($context = 'view') {
		
		if ( version_compare( WC_VERSION, '2.7', '<' ) ) {

			if ($this->is_uwa_expired()){

				if ($this->get_uwa_auction_expired() == '3'){
					
					return apply_filters( 'woocommerce_get_price', $this->regular_price, $this );

				}
				if ($this->is_uwa_reserved()) {

					return apply_filters( 'woocommerce_get_price', $this->woo_ua_auction_current_bid, $this );
				}
			}

			return apply_filters( 'woocommerce_get_price', $this->price, $this );

		} else {

			if ($this->is_uwa_expired()){
				
				$empty_price = $this->get_prop( 'price', $context );

				if(empty($empty_price) OR $this->get_uwa_auction_expired() == '2') {
					
					$price = null;					
					
					$price= get_post_meta( $this->get_uwa_wpml_default_product_id(), 'woo_ua_auction_current_bid', true );

					$this->set_price($price);
				}			

				return $this->get_prop( 'price', $context );
			}

			return apply_filters( 'woocommerce_product_get_price',get_post_meta( $this->get_uwa_wpml_default_product_id(), '_price', true ),$this);

		}
	}

	/**
	 * Get the Product's regular price.
	 *	
	 */
	public function get_regular_price( $context = 'view' ) {
		//return get_post_meta( $this->get_uwa_wpml_default_product_id(), '_regular_price', true );

		$reg_price =  get_post_meta( $this->get_uwa_wpml_default_product_id(), '_regular_price', 
			true );

		$addons = uwa_enabled_addons();
		if(is_array($addons) && in_array('uwa_currency_switcher', $addons)){		
			if($this->uwa_aelia_is_configure() == TRUE){				
				return $this->uwa_aelia_base_to_active($reg_price);
			}
		}
		return $reg_price;
	}

	/**
	 * Get the Add to url used mainly in loops.
	 *
	 */
	public function add_to_cart_url() {		
		$id = $this->get_uwa_wpml_default_product_id();		
		return apply_filters( 'woocommerce_product_add_to_cart_url', get_permalink( $id ), $this );
	}

	/**
	 * Wrapper for get_permalink
	 *
	 */
	public function get_permalink() {		
		$id = $this->get_uwa_wpml_default_product_id();		
		return get_permalink( $id );
	}

	/**
	 * Get Auction Product add to cart button text
	 *	
	 */
	public function add_to_cart_text() {
		$auction_selling_type = $this->get_uwa_auction_selling_type();
		if($auction_selling_type == "auction" || $auction_selling_type == "both" || 	$auction_selling_type == ""){
			if (!$this->is_uwa_completed() && $this->is_uwa_live() ){
				
				$text = __( 'Bid now', 'woo_ua' ) ;
				
			} elseif($this->is_uwa_completed()  ){
				
				$text = __( 'Expired', 'woo_ua' ) ;
				
			} elseif(!$this->is_uwa_completed() && !$this->is_uwa_live()  ){
				
				$text =  __( 'Future', 'woo_ua' ) ;
			}
		} else {
			
			$text = __( 'Buy It Now', 'woo_ua' );
			
		}	

		return apply_filters( 'woocommerce_product_add_to_cart_text', $text, $this );
	}

	/**
	 * Get Auction Product Fail Reason
	 *	
	 */
	public function get_uwa_auction_fail_reason( $context = 'view' ) {
		return get_post_meta( $this->get_uwa_wpml_default_product_id(), 'woo_ua_auction_fail_reason', true );
	}	

	/**
	 * Get Auction Product Order Id
	 *	
	 */
	public function get_uwa_order_id( $context = 'view' ) {
		return get_post_meta( $this->get_uwa_wpml_default_product_id(), 'woo_ua_order_id', true );		
	}

	/**
	 * Get Auction Product User Max Bid
	 *	 
	 */
	public function get_uwa_user_max_bid( $auction_id , $user_ID = false){

		global $wpdb;

		$wheredatefrom ='';
		$datefrom = false;

		$id = $this->get_uwa_wpml_default_product_id();

		if($datefrom){
			$wheredatefrom =" AND CAST(date AS DATETIME) > '$datefrom' ";
		}

		if(!$user_ID){
			$user_ID = get_current_user_id();
		}

		$maxbid = $wpdb->get_var( 'SELECT bid FROM '.$wpdb->prefix.'woo_ua_auction_log  WHERE auction_id =' . $auction_id .' and userid = ' . $user_ID. $wheredatefrom . '  ORDER BY  `bid` desc');

		return $maxbid;

	}
	
	/**
	 * Get is auction is sealed
	 *	 
	 * @return boolean
	 *
	 */
	function is_uwa_silent(){
		$silent  = false;
		
		if ($this->is_uwa_expired()){			
			$silent  = false;
		}
		if ($this->get_uwa_auction_silent() == 'yes') {			
			$silent  =  TRUE;
		}		
		return $silent;
	}
	
	/**
     * Get get_uwa_auction_silent 
	 *    
     */
    public function get_uwa_auction_silent( $context = 'view' ) {
        return get_post_meta( $this->get_uwa_wpml_default_product_id(), 'uwa_auction_silent', true );        
    }
	
	/**
     * Get get_uwa_auction_proxy 
	 *    
     */
    public function get_uwa_auction_proxy( $context = 'view' ) {
        return get_post_meta( $this->get_uwa_wpml_default_product_id(), 'uwa_auction_proxy', true );        
    }

	/**
     * Get get_uwa_auction_relisted
     *     
     */
    public function get_uwa_auction_relisted( $context = 'view' ) {      
        return get_post_meta( $this->get_uwa_wpml_default_product_id(), 'uwa_auction_relisted', true );
    }
	
	public function get_uwa_last_bid( $context = 'view' ) {         
        return get_post_meta( $this->get_uwa_wpml_default_product_id(), 'woo_ua_auction_last_bid', true );        
    }
	
	/**
	 * Get Auction Product Selling Type 
	 *	
	 */
	public function get_uwa_auction_selling_type( $context = 'view' ) {
		// selling type : auction, buyitnow, both 
		return get_post_meta( $this->get_uwa_wpml_default_product_id(), 'woo_ua_auction_selling_type', true );
	}
	
	/**
	 * Get Whole bids history table
	 *	
	 */
	public function get_uwa_bids_history_data($id){
		
		$newdata = "";
		$datetimeformat = get_option('date_format').' '.get_option('time_format');
		$current_bidder = $this->get_uwa_auction_current_bider();

		if(($this->is_uwa_expired() === TRUE ) and ($this->is_uwa_live() === TRUE )){
			
			$newdata .= "<p>". __('Auction has expired', 'woo_ua') ."</p>";
			if ($this->get_uwa_auction_fail_reason() == '1'){
				$newdata .= __('Auction Expired because there were no bids', 'woo_ua');
			} elseif($this->get_uwa_auction_fail_reason() == '2'){
				$newdata .= __('Auction expired without reaching reserve price', 'woo_ua');
			}
			
			if($this->get_uwa_auction_expired() == '3'){
				$newdata .= "<p>". __('Product sold for buy now price', 'woo_ua') .": <span>".wc_price($this->get_regular_price()) ."</span></p>";
			}elseif($current_bidder){
				$newdata .= "<p>". __('Highest bidder was', 'woo_ua') .": <span>". uwa_user_display_name($current_bidder) ."</span></p>";
			} 
								
		} /* end of if */

		
		$productid = $id;
		$newdata .= "<table id='auction-history-table-'".$productid." class='auction-history-table'>";
		$newdata .= "<thead>";
		$newdata .= "<tr>";
		$newdata .= "<th>". __('Bidder Name', 'woo_ua')."</th>";
		$newdata .= "<th>". __('Bidding Time', 'woo_ua')."</th>";
		$newdata .= "<th>". __('Bid', 'woo_ua')."</th>";
		$newdata .= "<th>". __('Auto', 'woo_ua')."</th>";			   
		$newdata .= "</tr>";
		$newdata .= "</thead>";    
			
		$uwa_auction_log_history = $this->uwa_auction_log_history();	
		if ( !empty($uwa_auction_log_history) ){

			/* when currency switcher addon is active */
			$aelia_addon = "";
			$addons = uwa_enabled_addons();
			if(is_array($addons) && in_array('uwa_currency_switcher', $addons)){
				if($this->uwa_aelia_is_configure() == TRUE){
					$aelia_addon = true;
				}
			}	


			$newdata .= "<tbody>";
				
			foreach ($uwa_auction_log_history as $history_value) {

				if($aelia_addon == true){
	        		$history_value->bid = $this->uwa_aelia_base_to_active($history_value->bid);
	        	}

				$newdata .= "<tr>";
				$user_name = uwa_user_display_name($history_value->userid);
				if ($this->get_uwa_auction_proxy()=="yes"){ 
					$user_name = uwa_proxy_mask_user_display_name($history_value->userid);
				}elseif($this->get_uwa_auction_silent()=="yes"){
					$user_name = uwa_silent_mask_user_display_name($history_value->userid);
				} 			
					
				$newdata .= "<td class='bid_username'>".$user_name."</td>";
				$newdata .= "<td class='bid_date'>".mysql2date($datetimeformat,
							$history_value->date)."</td>";
						
				if ($this->get_uwa_auction_proxy() == "yes"){
					$newdata .= "<td class='bid_price'>". uwa_proxy_mask_bid_amt(
						$history_value->bid)."</td>";
					
				}elseif($this->get_uwa_auction_silent()=="yes"){
					$newdata .= "<td class='bid_price'>".uwa_silent_mask_bid_amt($history_value->bid)."</td>";				
						
				}else { 					
					$newdata .= "<td class='bid_price'>".wc_price($history_value->bid)."</td>";
				} 
					if ($history_value->proxy == 1) {
						$newdata .= "<td class='proxy'>". __('Auto', 'woo_ua')."</td>";
					} 
					else { 
						$newdata .= "<td class='proxy'></td>";
					}
					$newdata .= "</tr>";
			} 
			$newdata .= "</tbody>";

		} /* end of if */
        
		$newdata .= "<tr class='start'>";
		$start_date = $this->get_uwa_auction_start_time();
		if ($this->is_uwa_live() === TRUE) {
			$newdata .= "<td class='started'>". __('Auction started', 'woo_ua');
		} 
		else {
			$newdata .= "<td class='started'>". __('Auction starting', 'woo_ua');
		} 
		$newdata .= "</td>";
		$newdata .= "<td colspan='3'  class='bid_date'>".mysql2date($datetimeformat,$start_date)."</td>";
		$newdata .= "</tr>";
		$newdata .= "</table>";
		
		return $newdata;
		
	} /* end of function  */
	
	/**
     * Get Auto Relist get_uwa_auto_renew_enable 
	 *    
     */
	public function get_uwa_auto_renew_enable( $context = 'view' ) {         
        return get_post_meta( $this->get_uwa_wpml_default_product_id(), 'uwa_auto_renew_enable', true );        
    }
	/**
     * Get Auto Relist Recurring Auto Relist
	 *    
     */
	public function get_uwa_recurring_auto_renew_enable( $context = 'view' ) {         
        return get_post_meta( $this->get_uwa_wpml_default_product_id(), 'uwa_auto_renew_recurring_enable', true );        
    }
	/**
     * Get Auto Relist Enable For Not Paid 
	 *    
     */
	public function get_uwa_auto_renew_not_paid_enable( $context = 'view' ) {         
    	return get_post_meta( $this->get_uwa_wpml_default_product_id(), 'uwa_auto_renew_not_paid_enable', true );        
    } 
	/**
     * Get Auto Relist Not Paid hours 
	 *    
     */ 
	public function get_uwa_auto_renew_not_paid_hours( $context = 'view' ) {         
    	return get_post_meta( $this->get_uwa_wpml_default_product_id(), 'uwa_auto_renew_not_paid_hours', true );        
    }	
	/**
     * Get Auto Relist Enable For No Bid Placed
	 *    
     */
	public function get_uwa_auto_renew_no_bids_enable( $context = 'view' ) {         
    	return get_post_meta( $this->get_uwa_wpml_default_product_id(), 'uwa_auto_renew_no_bids_enable', true );        
    } 
	/**
     * Get Auto Relist get_uwa_auto_renew_fail_hours 
	 *    
     */
	public function get_uwa_auto_renew_fail_hours( $context = 'view' ) {         
        return get_post_meta( $this->get_uwa_wpml_default_product_id(), 'uwa_auto_renew_fail_hours', true );        
    }

	/**
     * Get Auto Relist Enable For Reserve Not Met
	 *    
     */
	public function get_uwa_auto_renew_no_reserve_enable( $context = 'view' ) {         
    	return get_post_meta( $this->get_uwa_wpml_default_product_id(), 'uwa_auto_renew_no_reserve_enable', true );        
    } 
	/**
     * Get Auto Relist Reserve Not Met 
	 *    
     */
	public function get_uwa_auto_renew_reserve_fail_hours( $context = 'view' ) {         
        return get_post_meta( $this->get_uwa_wpml_default_product_id(), 'uwa_auto_renew_reserve_fail_hours', true );        
    }
	
	/**
     * Get Auto Relist get_uwa_auto_renew_fail_hours 
	 *    
     */
	public function get_uwa_auto_renew_duration_hours( $context = 'view' ) {         
        return get_post_meta( $this->get_uwa_wpml_default_product_id(), 'uwa_auto_renew_duration_hours', true );        
    }
	
	/**
     * WPML compatibility mode 
	 *    
     */
	
	function get_uwa_wpml_default_product_id(){

        global $sitepress;

        if (function_exists('icl_object_id') && function_exists('pll_default_language')) { 
            $id = icl_object_id($this->id,'product',false, pll_default_language());
        }
        elseif (function_exists('icl_object_id') && method_exists($sitepress, 'get_default_language')) { 
            $id = icl_object_id($this->id,'product',false, $sitepress->get_default_language());
        }
        else {
            $id = $this->id;
        }

        return $id;

    }
	
	
	/**
     * Get auto Debit Status
	 *    
     */
	public function get_uwa_stripe_auto_debit_status( $context = 'view' ) {         
        return get_post_meta( $this->get_uwa_wpml_default_product_id(), '_uwa_stripe_auto_debit_status', true );        
    }
	/**
     * Get auto Debit total amount
	 *    
     */
	public function get_uwa_stripe_auto_debit_total_amt( $context = 'view' ) {         
        return get_post_meta( $this->get_uwa_wpml_default_product_id(), '_uwa_stripe_auto_debit_total_amt', true );        
    }
	/**
     * Get auto Debit won bid amount
	 *    
     */
	public function get_uwa_stripe_auto_debit_bid_amt( $context = 'view' ) {         
        return get_post_meta( $this->get_uwa_wpml_default_product_id(), '_uwa_stripe_auto_debit_amt', true );        
    }	
	/**
     * Get auto Debit buyers premium  amount
	 *    
     */
	public function get_uwa_stripe_auto_debit_bpm_amt( $context = 'view' ) {         
        return get_post_meta( $this->get_uwa_wpml_default_product_id(), '_uwa_stripe_auto_debit_bpm_amt', true );        
    }
	


	/**
	 * Get Auction Product Bid Value
	 *	
	 */
	public function uwa_get_bid_value() {
		
		$auction_bid_increment = ($this->get_uwa_increase_bid_value()) ? $this->get_uwa_increase_bid_value() : 1;

		if ( ! $this->get_uwa_auction_current_bid() ) { 		
			return $this->get_uwa_current_bid();		  
		} else  {
			
			if($this->get_uwa_auction_type() == 'reverse' ) {
				$bid_value = round( wc_format_decimal($this->get_uwa_current_bid()) - wc_format_decimal($auction_bid_increment),wc_get_price_decimals());
			    return $bid_value;

			}else{								
				$bid_value = round( wc_format_decimal($this->get_uwa_current_bid()) + wc_format_decimal($auction_bid_increment),wc_get_price_decimals());
			    return $bid_value;
			}			
		}

		return FALSE;
	}

	public function uwa_aelia_get_base_currency(){

		$addons = uwa_enabled_addons();
		if(is_array($addons) && in_array('uwa_currency_switcher', $addons)){
			if($this->uwa_aelia_is_configure() == TRUE){
				return 	$product_base_currency = Woocommerce_Product_Currency_Helper::get_product_base_currency($this->get_id());
			}
		}
		return get_woocommerce_currency();
	}

	public function uwa_aelia_get_base_currency_symbol(){

		$currency = $this->uwa_aelia_get_base_currency();
		return get_woocommerce_currency_symbol($currency);
	}

	public function uwa_aelia_base_to_active($main_value) {	
		
		$active_currency = get_woocommerce_currency();
		$product_base_currency = Woocommerce_Product_Currency_Helper::get_product_base_currency($this->get_id());					
		$aelia_value = apply_filters('wc_aelia_cs_convert', $main_value, 
			$product_base_currency, $active_currency);
		return $aelia_value;			
	}
	
	public function uwa_aelia_is_configure() {	

		/* aelia plugin is activated */

		$blog_plugins = get_option( 'active_plugins', array() );
		$site_plugins = is_multisite() ? (array) maybe_unserialize( get_site_option(
			'active_sitewide_plugins' ) ) : array();

		if ( in_array( 'woocommerce-aelia-currencyswitcher/woocommerce-aelia-currencyswitcher.php', $blog_plugins ) || isset( $site_plugins['woocommerce-aelia-currencyswitcher/woocommerce-aelia-currencyswitcher.php'] ) ) {
			/* check for multiple currency */

			return TRUE;
		}			
		
		return FALSE;			
	}


	public function get_uwa_total_next_bids() {	
		return get_post_meta( $this->get_uwa_wpml_default_product_id(), 'woo_ua_next_bids', true );
	}


	public function get_uwa_next_bid_options($uwa_bid_value, $bid_inc) {

		$get_nextbids_val = $this->get_uwa_total_next_bids();

	    if($get_nextbids_val > 0){
	    	$total_count = 	$get_nextbids_val;
	    }
	    else{
	    	$total_count = 	10;
	    }
		
		$data = "";
		$storage = array();		
		for($i=1; $i<=$total_count; $i++){
			if($i==1){
				$val = $uwa_bid_value;
				$storage[$i] = $val;
			}
			else{
				$storage[$i] = $storage[$i-1] + $bid_inc;
				$val = $storage[$i];
			}

  			/* --aelia-- */
			//$product_base_currency = $this->uwa_aelia_get_base_currency();
  			//$args = array("currency" => $product_base_currency);

			$display_val = wc_price($val);
			$data .= "<option value=".$val.">".$display_val."</option>";
		}
		return $data;

	} /* end of function */


	public function get_uwa_next_bid_options_proxy($uwa_bid_value, $bid_inc) {
		
		$max_bid = (double)$this->get_uwa_auction_max_bid();

		if($max_bid > 0){
			$user_maxbid = $this->get_uwa_auction_max_current_bider();
			$user_ID = get_current_user_id();
			if($user_maxbid == $user_ID){

				if($max_bid >= $uwa_bid_value){
					$set_value = $max_bid + $bid_inc;
				}
				else if($maxbid < $uwa_bid_value){
					$set_value = $uwa_bid_value;
				}
			}
			else{
				$set_value = $uwa_bid_value;
			}
		}
		else {
			$set_value = $uwa_bid_value;
		}


			$get_nextbids_val = $this->get_uwa_total_next_bids();	    

		    if($get_nextbids_val > 0){
		    	$total_count = 	$get_nextbids_val;
		    }
		    else{
		    	$total_count = 	10;
		    }

			$data = "";
			$storage = array();
			for($i=1; $i<=$total_count; $i++){
				if($i==1){
					$val = $set_value;
					$storage[$i] = $val;
				}
				else{				
					$storage[$i] = $storage[$i-1] + $bid_inc;
					$val = $storage[$i];
				}			

				/* --aelia-- */
				//$product_base_currency = $this->uwa_aelia_get_base_currency();
	  			//$args = array("currency" => $product_base_currency);

				$display_val = wc_price($val);
				$data .= "<option value=".$val.">".$display_val."</option>";
			}

			return $data;

	} /* end of function */


	public function get_uwa_winner_name() {
		
		$winner_name = "";
		$uwa_reserve_met = $this->is_uwa_reserve_met();
		if($uwa_reserve_met == TRUE){
			$bidder_id = $this->get_uwa_auction_current_bider();
			
			if($bidder_id > 0){
				$bidder = get_userdata($bidder_id);
         		if ($bidder !== false) {
             		$winner_name = $bidder->display_name;  // user_login, user_nicename
             		$winner_name = ucwords(strtolower($winner_name));
         		}
			}
		}
		return $winner_name;

	} /* end of function */


	public function get_uwa_winner_text() {

		$winner_text = "";
		$uwa_slient = $this->get_uwa_auction_silent();
		$uwa_reserve_met = $this->is_uwa_reserve_met();

		if($uwa_slient != 'yes'){			
			$winner_name = $this->get_uwa_winner_name();
			$uwa_expired = $this->is_uwa_expired();
			$highest_bid = $this->get_uwa_auction_current_bid();
			$display_bid = wc_price($highest_bid);
			if($winner_name != ""){
				if($uwa_expired === TRUE){
					$winner_text = $winner_name." - ".$display_bid;
				}
				elseif($uwa_expired === FALSE){					
					$winner_text = $winner_name." ".__("is winning...", 'woo_ua');
				}
			}
		}

		return $winner_text;

	} /* end of function */


	public function get_directbid_variable_bid_inc($curr_price) {

		$ua_inc_price = "";

		$uwa_variable_inc_enable = get_post_meta($this->get_uwa_wpml_default_product_id(), 
			'uwa_auction_variable_bid_increment', true);		
	    
		if($uwa_variable_inc_enable == 'yes'){
		
			$ua_inc_price_range = get_post_meta($this->get_uwa_wpml_default_product_id(), 'uwa_var_inc_price_val', true);
			foreach($ua_inc_price_range as $range){
				if( ($range['start'] <= $curr_price) && ($range['end'] >= $curr_price) )
				{
					$ua_inc_price = $range['inc_val'];
					
					break;
				}
				
				if( ($range['start'] <= $curr_price) && ($range['end'] == 'onwards') )
				{  	
					$ua_inc_price = $range['inc_val'];
					break;
				}
			}
		}
		
		return $ua_inc_price;

	} /* end of function */


	public function get_uwa_next_bid_options_variable($uwa_bid_value) {

		$get_nextbids_val = $this->get_uwa_total_next_bids();

	    if($get_nextbids_val > 0){
	    	$total_count = 	$get_nextbids_val;
	    }
	    else{
	    	$total_count = 	10;
	    }
		
		$data = "";
		$storage = array();		
		for($i=1; $i<=$total_count; $i++){
			if($i==1){
				$val = $uwa_bid_value;
				$storage[$i] = $val;
			}
			else{
				$curr_price = $storage[$i-1];
				$new_bid_inc = $this->get_directbid_variable_bid_inc($curr_price);
				$storage[$i] = $storage[$i-1] + $new_bid_inc;
				$val = $storage[$i];
			}

  			/* --aelia-- */
			//$product_base_currency = $this->uwa_aelia_get_base_currency();
  			//$args = array("currency" => $product_base_currency);

			$display_val = wc_price($val);
			$data .= "<option value=".$val.">".$display_val."</option>";
		}

		return $data;

	} /* end of function */


	public function get_uwa_next_bid_options_proxy_variable($uwa_bid_value, $bid_inc) {
		
		$max_bid = (double)$this->get_uwa_auction_max_bid();

		if($max_bid > 0){
			$user_maxbid = $this->get_uwa_auction_max_current_bider();
			$user_ID = get_current_user_id();
			if($user_maxbid == $user_ID){

				if($max_bid >= $uwa_bid_value){
					$set_value = $max_bid + $bid_inc;
				}
				else if($maxbid < $uwa_bid_value){						
					$set_value = $uwa_bid_value;
				}
			}
			else{
				$set_value = $uwa_bid_value;
			}
		}
		else {
			$set_value = $uwa_bid_value;
		}


			$get_nextbids_val = $this->get_uwa_total_next_bids();	    

		    if($get_nextbids_val > 0){
		    	$total_count = 	$get_nextbids_val;
		    }
		    else{
		    	$total_count = 	10;
		    }

			$data = "";
			$storage = array();
			for($i=1; $i<=$total_count; $i++){
				if($i==1){
					$val = $set_value;
					$storage[$i] = $val;
				}
				else{
					$curr_price = $storage[$i-1];
					$new_bid_inc = $this->get_directbid_variable_bid_inc($curr_price);
					$storage[$i] = $storage[$i-1] + $new_bid_inc;
					$val = $storage[$i];
				}			

				/* --aelia-- */
				//$product_base_currency = $this->uwa_aelia_get_base_currency();
	  			//$args = array("currency" => $product_base_currency);

				$display_val = wc_price($val);
				$data .= "<option value=".$val.">".$display_val."</option>";
			}

			return $data;

	} /* end of function */


	public function uwa_display_user_winlose_text(){

		global $wpdb;
		$set_text = "";	
		$display_text = "";			
		  
		if (is_user_logged_in()) {
		
				$user_id  = get_current_user_id();
			    $user_max_bid = $this->get_uwa_user_max_bid($this->get_id(), $user_id);
				$uwa_auction_type = $this->get_uwa_auction_type();

				$uwa_display_wining_text = get_option("uwa_display_wining_text");
				$uwa_display_losing_text = get_option("uwa_display_losing_text");

							
				if ($this->get_uwa_auction_silent() != 'yes') {
				
					if ( $user_id == $this->get_uwa_auction_current_bider() && !$this->get_uwa_auction_expired() ) {

						if($uwa_display_wining_text != ""){
							$set_text = "winner";
							$display_text = $uwa_display_wining_text;
						}

					} elseif( $user_max_bid > 0 && $user_max_bid < $this->get_uwa_auction_current_bid() && !$this->get_uwa_auction_expired() && $uwa_auction_type=='normal') { 

						if($uwa_display_losing_text != ""){
							$set_text = "loser";
							$display_text = $uwa_display_losing_text;
						}
						
					} elseif( $user_max_bid > 0 && $user_max_bid > $this->get_uwa_auction_current_bid() && !$this->get_uwa_auction_expired() && $uwa_auction_type=='reverse') {
					
						if($uwa_display_losing_text != ""){
							$set_text = "loser";
							$display_text = $uwa_display_losing_text;
						}
					}
					
				} /* end of if - slient auction */
						
		}

		//$display_text = "<div><strong>".$text."</strong></div>";
		//return $text;
		return array('set_text' => $set_text, 'display_text' => $display_text);

	} /* end of function */
	

} /* end of class */

} /* end of if - class */