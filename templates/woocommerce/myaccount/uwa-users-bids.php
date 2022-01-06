<?php

/**
 * My auctions tab list
 * 
 * @package Ultimate WooCommerce Auction PRO
 * @author Nitesh Singh 
 * @since 1.0  
 *
 */

if (!defined('ABSPATH')) {
	exit;
}

	$user_id  = get_current_user_id();	
	if(isset( $_GET[ 'bid_status' ] )) {
		$active_tab = sanitize_text_field($_GET[ 'bid_status' ]);

	} elseif( isset( $_GET[ 'display' ] )) {
		$active_tab = sanitize_text_field($_GET[ 'display' ]);

	} else {
	 	$active_tab = 'active';
	} 
	
	$my_auction_page_url = wc_get_endpoint_url('uwa-auctions');
	$active_bid_url= esc_attr(add_query_arg(array('bid_status' =>'active'), $my_auction_page_url ));
	$active_won_url= esc_attr(add_query_arg(array('bid_status' =>'won'), $my_auction_page_url ));
	$active_lost_url= esc_attr(add_query_arg(array('bid_status' =>'lost'), $my_auction_page_url ));
	$active_watchlist_url= esc_attr(add_query_arg(array('display' =>'watchlist'), $my_auction_page_url ));
	
	
?>

	<ul class="uwa-user-bid-counts subsubsub">
		
        <li class="<?php echo $active_tab == 'active' ? 'active' : '';?>">
			
            <a href="<?php echo $active_bid_url;?>"> 
                	<?php echo __( 'Bids Active', 'woo_ua' ); ?></a> (<?php echo uwa_front_user_bids_count($user_id, 'active'); ?>) |
        </li>
		<li class="<?php echo $active_tab == 'won' ? 'active' : '';?>">
			<a href="<?php echo $active_won_url;?>" class="<?php echo $manage_auction_tab == 'live' ? 'current' : '';
                ?>"> 
				<?php echo __( 'Bids Won', 'woo_ua' ); ?></a> (<?php echo uwa_front_user_bids_count($user_id, 'won'); ?>) |
        </li>
		<li class="<?php echo $active_tab == 'lost' ? 'active' : '';?>">
		   <a href="<?php echo $active_lost_url;?>"> 
		   		<?php echo __( 'Bids Lost', 'woo_ua' ); ?></a> (<?php echo uwa_front_user_bids_count($user_id, 'lost'); ?>) |
		</li>
		<li class="<?php echo $active_tab == 'watchlist' ? 'active' : '';?>">
		   <a href="<?php echo $active_watchlist_url;?>"> 
		   	<?php echo __( 'Watchlist', 'woo_ua' ); ?></a> (<?php echo uwa_front_user_watchlist_count($user_id); ?>) 
		</li>
		<?php 
		/* <li class="<?php echo $active_tab == 'settings' ? 'active' : '';?>">
		   <a href="<?php echo $my_account_page_url?>uwa-auctions?display=settings"> 
		     <?php echo __( 'Settings', 'woo_ua' ); ?></a> 
		</li> */
		?>
	</ul>
		
<?php 
	
	if( $active_tab == 'active' ) {
		$bid_status = 'active';
		echo uwa_front_user_bid_list($user_id, "active");
	}
	if( $active_tab == 'won' ) {
		$bid_status = 'won';
		echo uwa_front_user_bid_list($user_id, "won");
	}	
	if( $active_tab == 'lost' ) {
		$bid_status = 'lost';
		echo uwa_front_user_bid_list($user_id, "lost");
	}	

	if( $active_tab == 'watchlist' ) {
		echo uwa_front_user_watchlist($user_id);
	}
	if( $active_tab == 'settings' ) {
		echo uwa_front_user_auction_settings($user_id);
	}
?>