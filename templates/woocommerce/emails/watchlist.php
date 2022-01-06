<?php/** * watchlist email *  * @package Ultimate WooCommerce Auction PRO * @author Nitesh Singh  * @since 1.0  * */if (!defined('ABSPATH')) {    exit;}?>	<?php do_action('woocommerce_email_header', $email_heading, $email);  ?><?php	$product = $email->object['product'];		$product_base_currency = $product->uwa_aelia_get_base_currency();		$args = array("currency" => $product_base_currency);	$auction_url = $email->object['url_product'];	$user_name = $email->object['user_name'];		$auction_title = $email->object['product_name'];	$currentbid = $product->get_uwa_current_bid();	$auction_bid_value = wc_price($currentbid, $args);	$thumb_image = $product->get_image( 'thumbnail' );	?><p><?php printf( __( "Hi %s,", 'woo_ua' ), $user_name); ?></p><p><?php printf( __( 'A bid was placed on an auction product which was in your watchlist.', 	'woo_ua' ), $auction_url, $auction_title); ?></p><p><?php printf( __( "Here are the details : ", 'woo_ua' )); ?></p><table>   	<tr>	 		<td><?php echo __( 'Image', 'woo_ua' ); ?></td>		<td><?php echo __( 'Product', 'woo_ua' ); ?></td>		<td><?php echo __( 'Current bid', 'woo_ua' ); ?></td>		</tr>    <tr>		<td><?php echo $thumb_image;?></td>		<td><a href="<?php echo $auction_url ;?>"><?php echo $auction_title; ?></a></td>		<td><?php echo $auction_bid_value;  ?></td>    </tr></table><?php do_action('woocommerce_email_footer', $email);?>