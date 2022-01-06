<?php

/**
 * Ultimate WooCommerce Auction Pro General Setting Tab
 *
 * @package Ultimate WooCommerce Auction Pro
 * @author Nitesh Singh 
 * @since 1.0 
 *
 */


 ?>

<div class="uwa_main_setting_content">		  
	<table class="form-table">
				<tbody>							
					<tr class="uwa_heading">
						<th colspan="2"><?php _e('Cron Job Setting','woo_ua'); ?></th>
					</tr>
					<tr>
						<th scope="row"><?php _e('Auction Status:','woo_ua'); ?></th>							 
						<td class="uwaforminp">
						<?php 
							echo '<p>' . sprintf( __( '<b>%1$s/?ua-auction-cron=process-auction</b>', 'woo_ua' ), get_bloginfo( 'url' ), esc_attr( add_query_arg( 'uwa_process_auction_cron_ignore_notice', '0' ) ) ) . '</p>'; 
							echo '<p style="font-style: italic;">' . sprintf( __( 'This cron job will check auction for expiration and then send winning emails | We recommend to set it to every minute', 'woo_ua' ) ) . '</p>'; 
						?>
						    
						</td>
					</tr> 
					
					<tr>
						<th scope="row"><?php _e('Ending soon emails:','woo_ua'); ?></th>							 
						<td class="uwaforminp">
						<?php 
							echo '<p>' . sprintf( __( '<b>%1$s/?ua-auction-cron=ending-soon-email</b>', 'woo_ua' ), get_bloginfo( 'url' ), esc_attr( add_query_arg( 'uwa_process_auction_cron_ignore_notice', '0' ) ) ) . '</p>'; 
							echo '<p style="font-style: italic;">' . sprintf( __( 'This cron job will check which auction products are going to end soon (hours setting is inside email). We recommend to set it to one hour', 'woo_ua' ) ) . '</p>'; 
						?>
						</td>
					</tr> 
					
					<tr>
						<th scope="row"><?php _e('Payment Reminder:','woo_ua'); ?></th>							 
						<td class="uwaforminp">
						<?php 
							echo '<p>' . sprintf( __( '<b>%1$s/?ua-auction-cron=ending-soon-email</b>', 'woo_ua' ), get_bloginfo( 'url' ), esc_attr( add_query_arg( 'uwa_process_auction_cron_ignore_notice', '0' ) ) ) . '</p>'; 
							echo '<p style="font-style: italic;">' . sprintf( __( 'This cron job will send payment reminder email to winner.  We recommend to set it to one hour.', 'woo_ua' ) ) . '</p>'; 
						?>
						</td>
					</tr> 
					
					<tr>
						<th scope="row"><?php _e('Automatic Relisting:','woo_ua'); ?></th>							 
						<td class="uwaforminp">
						<?php 
							echo '<p>' . sprintf( __( '<b>%1$s/?ua-auction-cron=ending-soon-email</b>', 'woo_ua' ), get_bloginfo( 'url' ), esc_attr( add_query_arg( 'uwa_process_auction_cron_ignore_notice', '0' ) ) ) . '</p>'; 
							echo '<p style="font-style: italic;">' . sprintf( __( 'This cron job will check which auction product is due for automatic relist. We recommend to set it to one hour.', 'woo_ua' ) ) . '</p>'; 
						?>
						</td>
					</tr> 
					
				</tbody>						
			</table> 
</div>	   