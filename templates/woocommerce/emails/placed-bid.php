<?php

/**
 * Bidder placed a bid email notification (HTML)
 * 
 * @package Ultimate WooCommerce Auction PRO
 * @author Nitesh Singh 
 * @since 1.0  
 *
 */

if (!defined('ABSPATH')) {
    exit;
}

?>
<?php do_action('woocommerce_email_header', $email_heading, $email); ?>
<?php

global $wpdb;
	$user_type = $email->object['user_type'];
	$product = $email->object['product'];
	
	$product_base_currency = $product->uwa_aelia_get_base_currency();
	$args = array("currency" => $product_base_currency);

	$auction_url = $email->object['url_product'];
	$user_name = $email->object['user_name'];
	$auction_title = $product->get_title();
	$auction_bid_value = wc_price($product->get_uwa_current_bid(), $args);
	//$auction_bid_value = $product->get_uwa_current_bid();
	$uwa_silent = $product->get_uwa_auction_silent();
	$thumb_image = $product->get_image( 'thumbnail' );
	$uwa_proxy  = $product->get_uwa_auction_proxy();
	$product_id = $product->get_id(); 

	//$user_id = get_current_user_id();	
	$user_id = $email->object['placebid_userid'];

	if ($uwa_silent == 'yes'){
		$auction_bid_value = wc_price($product->get_uwa_last_bid(), $args);		
	}
	if($uwa_proxy == "yes"){
		$auction_type = $product->get_uwa_auction_type();
		if($auction_type == "normal"){
			// get last bid of user
			/* SELECT bid FROM `wp_woo_ua_auction_log` WHERE `auction_id`=210   AND  `userid`=3 order by id DESC LIMIT 1 */

			$auction_bid_value  = $wpdb->get_var('SELECT bid FROM '.$wpdb->prefix.'woo_ua_auction_log  WHERE auction_id = ' .$product_id .' AND 
				userid = '.$user_id .' ORDER BY id DESC LIMIT 1');

			$auction_bid_value = wc_price($auction_bid_value, $args);

		}
		if($auction_type == "reverse"){
			// same as above
			$auction_bid_value  = $wpdb->get_var('SELECT bid FROM '.$wpdb->prefix.'woo_ua_auction_log  WHERE auction_id = ' .$product_id .' AND 
				userid = '.$user_id .' ORDER BY id DESC LIMIT 1');

			$auction_bid_value = wc_price($auction_bid_value, $args);
		}		
	}

	
?>
<?php if($user_type ==="bidder"){ 

	?>
	
	<p><?php printf( __( "Hi %s,", 'woo_ua' ), $user_name); ?></p>
	<p>
	<?php 
			
			$cur_userid = get_current_user_id();
			$sentmail = get_user_meta($cur_userid, "uwa_samemaxbid_sent_mail", true);
			if($sentmail == "yes"){
				printf( __( "An user has placed a bid which matched your maximum bid and due to this, we have placed a new bid on your behalf with an amount same as your 'Maximum Bid' and declined the other user's bid. on <a href='%s'>%s</a>.", "woo_ua" ), 
					$auction_url, $auction_title);

				delete_user_meta($cur_userid, "uwa_samemaxbid_sent_mail");

			}
			else{
				printf( __( 'You recently placed a bid on <a href="%s">%s</a>.', 'woo_ua' ), 
					$auction_url, $auction_title);
			}

	?>
	</p>

	<p><?php printf( __( "Here are the details : ", 'woo_ua' )); ?></p>
	<table>
		<tr>
			<td><?php echo __( 'Image', 'woo_ua' ); ?></td>
			<td><?php echo __( 'Product', 'woo_ua' ); ?></td>
			<td><?php echo __( 'Your bid', 'woo_ua' ); ?></td>	
			<?php
				if ($uwa_proxy == 'yes' &&  $product->get_uwa_auction_max_current_bider() && get_current_user_id() == $product->get_uwa_auction_max_current_bider()) {
					?>
					<td>
						<?php echo __( 'Auto', 'woo_ua' ); ?>
					</td>

					<?php
				}
			?>
		</tr>
	    <tr>
			<td><?php echo $thumb_image;?></td>
			<td><a href="<?php echo $auction_url; ?>"><?php echo $auction_title; ?></a></td>
			<td><?php echo $auction_bid_value; ?></td>
			<?php
				if ($uwa_proxy == 'yes' &&  $product->get_uwa_auction_max_current_bider() && get_current_user_id() == $product->get_uwa_auction_max_current_bider()) {
					?>
					<td>
						<?php 
							$max_bid_price = $product->get_uwa_auction_max_bid();
							if($max_bid_price){
								$formatted_max_bid_price = wc_price($max_bid_price, $args);
							}
							else{
								$formatted_max_bid_price = " --- ";
							}
							echo $formatted_max_bid_price; 
						?>
					</td>

					<?php
				}
			?>
	    </tr>
	</table>
<?php } ?>



<?php do_action('woocommerce_email_footer', $email); ?>