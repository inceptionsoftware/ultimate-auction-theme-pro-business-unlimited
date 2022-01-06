<?php

/**
 * Auction history tab
 * 
 * @package Ultimate WooCommerce Auction PRO
 * @author Nitesh Singh 
 * @since 1.0  
 *
 */

if (!defined('ABSPATH')) {
    exit;
}

global $woocommerce, $post, $product;
$datetimeformat = get_option('date_format').' '.get_option('time_format');
$heading = apply_filters('ultimate_woocommerce_auction_total_bids_heading', __( 'Total Bids Placed:', 'woo_ua' ) );
$current_bidder = $product->get_uwa_auction_current_bider();
?>

<h2><?php echo $heading; ?></h2>
<div class="uwa_bids_history_data" data-auction-id="<?php echo $product->get_id(); ?>">  <!-- main container -->
<?php if(($product->is_uwa_expired() === TRUE ) and ($product->is_uwa_live() === TRUE )) : ?>
    
	<p><?php _e('Auction has expired', 'woo_ua') ?></p>
	<?php if ($product->get_uwa_auction_fail_reason() == '1'){
		 _e('Auction Expired because there were no bids', 'woo_ua');
	} elseif($product->get_uwa_auction_fail_reason() == '2'){
		_e('Auction expired without reaching reserve price', 'woo_ua');
	}
	
	if($product->get_uwa_auction_expired() == '3'){?>
		<p><?php _e('Product sold for buy now price', 'woo_ua') ?>: <span><?php echo wc_price($product->get_regular_price()) ?></span></p>
	<?php }elseif($current_bidder){ ?>
		<p><?php _e('Highest bidder was', 'woo_ua') ?>: <span><?php echo uwa_user_display_name($current_bidder);?></span></p>
	<?php } ?>
						
<?php endif; ?>	

<table id="auction-history-table-<?php echo $product->get_id(); ?>" class="auction-history-table">
    <?php 
        
    $uwa_auction_log_history = $product->uwa_auction_log_history();
	
	if ( !empty($uwa_auction_log_history) ): ?>

	    <thead>
	        <tr>
	            <th><?php _e('Bidder Name', 'woo_ua')?></th>
				<th><?php _e('Bidding Time', 'woo_ua')?></th>
	            <th><?php _e('Bid', 'woo_ua') ?></th>
	            <th><?php _e('Auto', 'woo_ua') ?></th>
			   
	        </tr>
	    </thead>

	    <tbody>
			<?php 
			$aelia_addon = "";
			$addons = uwa_enabled_addons();
			if(is_array($addons) && in_array('uwa_currency_switcher', $addons)){
				if($product->uwa_aelia_is_configure() == TRUE){
					$aelia_addon = true;
				}
			}

	        foreach ($uwa_auction_log_history as $history_value) { 

	        	if($aelia_addon == true){
	        		$history_value->bid = $product->uwa_aelia_base_to_active($history_value->bid);
	        	}

	        	?>
				<tr> 
				<?php 
				$user_name = uwa_user_display_name($history_value->userid);
				if ($product->get_uwa_auction_proxy()=="yes"){ 
					$user_name = uwa_proxy_mask_user_display_name($history_value->userid);
				}elseif($product->get_uwa_auction_silent()=="yes"){
					$user_name = uwa_silent_mask_user_display_name($history_value->userid);
				} 			
				?>
	                <td class="bid_username"><?php echo $user_name;?></td>				
					<td class="bid_date"><?php echo mysql2date($datetimeformat ,$history_value->date)?></td>
					<?php
				if ($product->get_uwa_auction_proxy()=="yes"){ ?>			
					<td class="bid_price"><?php echo uwa_proxy_mask_bid_amt($history_value->bid);?></td>
				<?php 				
				}elseif($product->get_uwa_auction_silent()=="yes"){
					?><td class="bid_price"><?php echo uwa_silent_mask_bid_amt($history_value->bid);?>						
					</td>
					<?php
					
				} else { ?>
					
					<td class="bid_price"><?php echo wc_price($history_value->bid);?></td>
				<?php } 
					if ($history_value->proxy == 1) { ?>
						<td class="proxy"><?php _e('Auto', 'woo_ua');?></td>
					<?php } else { ?>
						<td class="proxy"></td>
				<?php } ?>
	           </tr>
			<?php } ?> 
	    </tbody>

	<?php endif;?>
        
	<tr class="start">
        <?php 
		$start_date = $product->get_uwa_auction_start_time(); ?>
		<?php if ($product->is_uwa_live() === TRUE) { ?>
			<td class="started"><?php echo __('Auction started', 'woo_ua');?>
			<?php }   else { ?>									
			<td  class="started"><?php echo __('Auction starting', 'woo_ua');?>		
		<?php } ?></td>	
		<td colspan="3"  class="bid_date"><?php echo mysql2date($datetimeformat,$start_date)?></td>
	</tr>
</table>

</div>