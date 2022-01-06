<?php 
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 *
 * Front Side Class
 *
 * Handles generic Front functionality and AJAX requests.
 *
 * @class  UWA_Front
 * @package Ultimate WooCommerce Auction PRO
 * @author Nitesh Singh
 * @since 1.0
 *
 */
 
function uwa_auctions_download_csv() {
	// Check for current user privileges 
	if( !current_user_can( 'manage_options' ) ){ return false; }
	// Check if we are in WP-Admin
	if( !is_admin() ){ return false; }
	ob_start();
	$sitename = sanitize_key( get_bloginfo( 'name' ) );
	if ( ! empty( $sitename ) )
		$sitename .= '-';
		$filename = $sitename . 'expiry-auctions-' . date( 'Y-m-d-H-i-s' ) . '.csv';	
		$header_row = array(
						'Auction Name', 
						'Bidder Name',
						'Bidder Email',
						'Highest Bid ',
						'Auction End Date',
						'Expiry Reason',
		);
		$data_rows = array();
		global $wpdb;
		$curr_user_id = get_current_user_id();
		$args = array(
			'post_type'	=> 'product',
			'post_status' => 'publish',
			'posts_per_page' =>'-1',
			'tax_query' => array(array('taxonomy' => 'product_type' , 'field' => 'slug', 'terms' => 'auction')),
			'auction_arhive' => TRUE
		 );	
		 
		if(!empty($_REQUEST['users_auctions']) && $_REQUEST['users_auctions']=='true'){
			$args['author__not_in']=$curr_user_id;
		}else{
			$args['author']=$curr_user_id;
		}
		
	 	$args['meta_query']= array(
					'relation' => 'AND',
						array(			     
							'key' => 'woo_ua_auction_closed',
							'value' => array('1','2','3','4'),
							'compare' => 'IN',
						),							
					);
		
	
		$query = new WP_Query( $args );
		if ( $query->have_posts() ) {
			while ( $query->have_posts() ) {
				$query->the_post();
					$auction_ID = get_the_ID();
					$ending_date = get_post_meta(get_the_ID(), 'woo_ua_auction_end_date', true);
					$auction_name = get_the_title();
					$highest_bid = $wpdb->get_var( 'SELECT bid FROM '.$wpdb->prefix.'woo_ua_auction_log  WHERE auction_id =' . $auction_ID .'  ORDER BY  `bid` DESC limit 1');
					$row['expiry_reason'] = "";
					$bidder_name="";
					$bidder_email="";
					$fail_reason = get_post_meta($auction_ID, 'woo_ua_auction_fail_reason', true); 
					$reason_closed = get_post_meta($auction_ID, 'woo_ua_auction_closed', true); 
					$current_bidder = get_post_meta($auction_ID, 'woo_ua_auction_current_bider', true);
					$order_id = get_post_meta($auction_ID, 'woo_ua_order_id', true);				
					if($current_bidder){		
						$obj_user = get_userdata($current_bidder);	
						$user_name = "";
						if($obj_user){
							$bidder_name = $obj_user->display_name;
							$bidder_email = $obj_user->user_email;
						}

					}
					if($fail_reason == 1){	
					
						$row['expiry_reason'] =''.__('No Bid', 'woo_ua').'';
						
					} elseif($fail_reason == 2) {
						
						$row['expiry_reason'] = ''.__('Reserve Not Met', 'woo_ua').'';
					}elseif($reason_closed == 3){

						$row['expiry_reason'] = ''.__('Sold', 'woo_ua').'';									
						if ( $order_id ){						
							$row['expiry_reason'] .='';
							$row['expiry_reason'] .='
							'.$order_id.'">'.__('Order ID: ', 'woo_ua').$order_id.'';
						}
					}else {

						$row['expiry_reason'] .=  __('Won', 'woo_ua').'  ';
						$row['expiry_reason'] .= __('Highest bidder was', 'woo_ua').' ';
						$row['expiry_reason'] .= $bidder_name;					
						if ( $order_id ){
							$row['expiry_reason'] .=''.$order_id.'">'.__('Order ID: ', 'woo_ua').$order_id.'';
						}
					}
				// Restore original Post Data
				wp_reset_postdata();
				$row = array(
					$auction_name,
					$bidder_name,
					$bidder_email,
					$highest_bid,
					$ending_date,
					$row['expiry_reason'],
				);
				$data_rows[] = $row;
    	}
		} else {
			echo "No Posts Found";
		}
		
	$__csvoutput = @fopen( 'php://output', 'w' );
	//fprintf( $fh, chr(0xEF) . chr(0xBB) . chr(0xBF) );
	header( 'Cache-Control: must-revalidate, post-check=0, pre-check=0' );
	header( 'Content-Description: File Transfer' );
	header( 'Content-type: text/csv' );
	header( "Content-Disposition: attachment; filename={$filename}" );
	header( 'Expires: 0' );
	header( 'Pragma: public' );
	fputcsv( $__csvoutput, $header_row );
	foreach ( $data_rows as $data_row ) {
		fputcsv( $__csvoutput, $data_row );
	}
	fclose( $__csvoutput );
	ob_end_flush();
	die();
}	