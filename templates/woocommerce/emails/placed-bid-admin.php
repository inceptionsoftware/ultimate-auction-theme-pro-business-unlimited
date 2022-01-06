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

<?php if($user_type ==="admin"){ ?>
<p><?php printf( __( "Hi," ,'woo_ua' )); ?></p>
<p><?php printf( __( 'A bid was placed on <a href="%s">%s</a>.', 'woo_ua' ), $auction_url, $auction_title); ?></p>
<p><?php printf( __( "Here are the details : ", 'woo_ua' )); ?></p>
<table>
	<tr>
		<td><?php echo __( 'Image', 'woo_ua' ); ?></td>
		<td><?php echo __( 'Product', 'woo_ua' ); ?></td>
		<td><?php echo __( 'Bid Value', 'woo_ua' ); ?></td>	
	</tr>
    <tr>
		<td><?php echo $thumb_image;?></td>
		<td><a href="<?php echo $auction_url ;?>"><?php echo $auction_title; ?></a></td>
		<td><?php echo $auction_bid_value;  ?></td>
    </tr>
</table>
<?php } ?>


<?php do_action('woocommerce_email_footer', $email); ?>