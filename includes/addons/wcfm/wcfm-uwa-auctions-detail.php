<?php
/**
 * WCFM plugin views
 *
 * Plugin WC UWA Auctions List Views 
 */
global $wp, $WCFM, $WCFMu,$wpdb, $woocommerce,$post;;
$datetimeformat = get_option('date_format').' '.get_option('time_format');
if( !current_user_can( 'auction' ) && !current_user_can( 'auction' ) ) {
	//wcfm_restriction_message_show( "Invalid Auction" );
	//return;
}
  
 
$auction_id = $wp->query_vars['uwa-auction-detail'];

if( $auction_id ) {	
	$author_id = get_post_field ('post_author', $auction_id);	
	$curr_user_id = get_current_user_id();	
	if (current_user_can('administrator') or current_user_can('shop_manager') or $curr_user_id == $author_id) {
		$post = get_post($auction_id);	
	    $auction_product = new WC_Product_Auction( $post->ID );	
		$closed = $auction_product->is_uwa_expired();				
		$started = $auction_product->is_uwa_live();
		$failed = $auction_product->get_uwa_auction_fail_reason();
		
	} else {
		wcfm_restriction_message_show( "Invalid Auction" );
	    return;
	}
	
} else {
	wcfm_restriction_message_show( "Invalid Auction" );
	return;
}
		
           ?>
			<div class="collapse wcfm-collapse" id="wcfm_uwa_auctions_detail">
				<div class="wcfm-page-headig">
					<span class="wcfmfa fa-calendar"></span>
					<span class="wcfm-page-heading-text"><?php _e( 'Auction Detail', 'woo_ua' ); ?></span>
					<?php do_action( 'wcfm_page_heading' ); ?>
				</div>

				<div class="wcfm-collapse-content">
					<div id="wcfm_page_load"></div>
					
					<div class="wcfm-container wcfm-top-element-container">
					<h2><?php _e( 'Auction #', 'woo_ua' ); echo $auction_id; ?></h2>
					<?php 
					echo '<a id="add_new_product_dashboard" class="add_new_wcfm_ele_dashboard text_tip" href="'.get_wcfm_edit_product_url().'" data-tip="' . __('Add New Product', 'wc-frontend-manager') . '"><span class="wcfmfa fa-cube"></span><span class="text">' . __( 'Add New', 'wc-frontend-manager') . '</span></a>';
					?>
						<div class="wcfm_clearfix"></div>
					</div>
					<div class="wcfm-clearfix"></div><br />
					<!-- collapsible -->
					<div class="page_collapsible bookings_details_general" id="wcfm_general_options">
						<?php _e('Auction Overview', 'woo_ua'); ?><span></span>
					</div>
					
					
					<div class="wcfm-container">
						<div id="bookings_details_general_expander" class="wcfm-content">
							<p class="form-field form-field-wide">
							<span for="booked_product" class="wcfm-title wcfm_title"><strong><?php _e( 'Title:', 'wc-frontend-manager' ) ?></strong></span>	
							<a target="_blank" href="<?php echo get_post_permalink($post->ID);?>" ><?php echo get_the_title( $post->ID ); ?></a>
							</p>
							<p class="form-field form-field-wide">
							<span for="booked_product" class="wcfm-title wcfm_title"><strong><?php _e( 'Status:', 'wc-frontend-manager' ) ?></strong></span>
					<?php	if($closed === FALSE && $started === TRUE){ ?>				
						<span style="color:#7ad03a;font-size:18px"><?php _e('Live', 'woo_ua')?></span>
					<?php 
				} elseif($closed === FALSE && $started === FALSE){ ?>					
						<span style="color:orange;font-size:18px"><?php _e('Future', 'woo_ua')?></span>
						</br><span style="color:#0073aa;font-size:10px"><?php _e('Not Started', 'woo_ua')?></span>
					<?php 
				} else { ?>				
					   <span style="color:red;font-size:18px"><?php _e('Expired', 'woo_ua')?></span>
						<?php if ($auction_product->get_uwa_auction_expired() == '3') { ?>
						
							<span style="color:#0073aa;font-size:10px"><?php _e('Sold', 'woo_ua')?></span>
							<?php 
						} elseif ($auction_product->get_uwa_auction_fail_reason() == '1') { ?>
						
							<span style="color:#0073aa;font-size:10px"><?php _e('No Bid', 'woo_ua')?></span>
					
				    		<?php 
						} elseif ($auction_product->get_uwa_auction_fail_reason() == '2') { ?>
				
							<span style="color:#0073aa;font-size:10px"><?php _e('Reserve Not Met', 'woo_ua')?></span>
					
							<?php 
						} else { ?>				
							<span style="color:#0073aa;font-size:10px"><?php _e('Won', 'woo_ua')?></span>			
							<?php
						
						} /* end of else */
				 } ?>		
							</p>

				<p class="form-field form-field-wide">
							<span for="booked_product" class="wcfm-title wcfm_title"><strong></strong></span>		
							<?php
						if ((method_exists( $auction_product, 'get_type') && $auction_product->get_type() == 'auction') && $auction_product->get_uwa_auction_expired() && !$auction_product->get_uwa_auction_payed()) { ?>
						
							
						<a href="#" class="button uwa_force_relist" data-auction_id="<?php echo $post->ID;?>"><?php _e('Relist', 'woo_ua'); ?></a>
							
						
							<div  class="uwa_auction_relist_date_field">
								<?php
								$WCFM->wcfm_fields->wcfm_generate_form_field(
								array(
								"uwa_relist_start_date" => array(
								'label' => __( 'Start Date', 'woo_ua' ),
								'type' => 'text',  // date, datepicker
								//'class' => 'datetimepicker',
								//'value' => $nowdate_for_start,
								'label_class' => 'wcfm_title', 								
								'hints' => __( 'Set the Start date of Auction Product.', 'woo_ua' ),
							),

							/*  no 10 */
							"uwa_relist_end_date" => array(
								'label' => __( 'Ending Date', 'woo_ua' ),
								'type' => 'text', // date, datepicker
								//'class' => 'datetimepicker',
								//'value' => $end_date,
								'label_class' => 'wcfm_title', 								
								'hints' => __( 'Set the end date for the auction', 'woo_ua' ),
							),
							)); 

							?>
				       <a  href="" data-auction_id="<?php echo $post->ID;?>" class="add_new_wcfm_ele_dashboard uwa_relist_now" data-tip="<?php _e('Relist Now', 'woo_ua'); ?>">
					   <?php _e('Relist Now', 'woo_ua'); ?></a>
					   
							</div>

						<?php  } 
						if ((method_exists( $auction_product, 'get_type') && $auction_product->get_type() == 'auction') and ($auction_product->is_uwa_live()  === FALSE )) { ?>
							
							
							<a href="#" class="button uwa_force_make_live" data-auction_id="<?php echo $post->ID;?>"><?php _e('Make It Live', 'woo_ua'); ?></a>
																	
						<?php  } 
						if ((method_exists( $auction_product, 'get_type') && $auction_product->get_type() == 'auction') && $auction_product->is_uwa_expired() === FALSE && ($auction_product->is_uwa_live()  === TRUE )) { ?>
							
						
							<a href="#" class="button uwa_force_end_now" data-auction_id="<?php echo $post->ID;?>"><?php _e('End Now', 'woo_ua'); ?></a>
																	
						<?php  } ?>
							</p>
							
							<div class="wcfm-clearfix"></div>
						</div>
					</div>	

				<div class="wcfm_clearfix"></div>
		<br />
		<!-- collapsible End -->
		
		<!-- collapsible -->
		<div class="page_collapsible bookings_details_booking" id="wcfm_booking_options">
			<?php _e('Bid History', 'wc-frontend-manager'); ?><span></span>
		</div>
		<div id="bookings_details_general_expander" class="wcfm-content">
			<?php
			
			$uwa_auction_relisted = $auction_product->get_uwa_auction_relisted();
			if ( ! empty( $uwa_auction_relisted ) ) {
			?>
			<p class="form-field form-field-wide">
			
			<?php _e( 'Auction has been relisted on:', 'woo_ua' ); ?> <?php echo mysql2date($datetimeformat ,$uwa_auction_relisted)?> </p>
			<?php } ?>
		<?php if (($auction_product->is_uwa_expired() === TRUE) and ($auction_product->is_uwa_live() === TRUE)): ?>				
				<p class="form-field form-field-wide"><?php _e('Auction has expired', 'woo_ua')?></p>
				
				<?php if ($auction_product->get_uwa_auction_fail_reason() == '1') { ?>
				
						<p class="form-field form-field-wide"><?php _e('Auction Expired without any bids.', 'woo_ua')?></p>
					
				<?php } elseif ($auction_product->get_uwa_auction_fail_reason() == '2') { ?>
				
						<p class="form-field form-field-wide"><?php _e('Auction Expired without reserve price met', 'woo_ua')?></p>
							
						<!--<a class="removereserve" href="#" data-postid="<?php echo $post->ID;?>">
						<?php _e('Remove Reserve Price', 'woo_ua'); ?> </a>	-->
					
				<?php }
				
				if ($auction_product->get_uwa_auction_expired() == '3') {?>
				
					<p class="form-field form-field-wide"><?php _e('This Auction Product has been sold for buy now price', 'woo_ua')?>: <span><?php echo wc_price($auction_product->get_regular_price()) ?></span></p>
				
				<?php } elseif ($auction_product->get_uwa_auction_current_bider()) {?>
				
						<?php
							$current_bidder = $auction_product->get_uwa_auction_current_bider();
						?>

						<p class="form-field form-field-wide"><?php _e('Highest bidder was', 'woo_ua')?>: <span class="maxbider"><a href='<?php echo get_edit_user_link($current_bidder)?>'><?php   echo uwa_user_display_name($current_bidder); ?></a></span></p>
						
						<p class="form-field form-field-wide"><?php _e('Highest bid was', 'woo_ua')?>: <span class="maxbid" ><?php echo wc_price($auction_product->get_uwa_current_bid()) ?></span></p>

						<?php if ($auction_product->get_uwa_auction_payed()) {?>
					
							<p class="form-field form-field-wide"><?php _e('Order has been paid, order ID is', 'woo_ua')?>: <span><a href='post.php?&action=edit&post=<?php echo $auction_product->get_uwa_order_id() ?>'><?php echo $auction_product->get_uwa_order_id() ?></a></span></p>
							
						<?php } elseif ($auction_product->get_uwa_order_id()) {
						
								$order = wc_get_order( $auction_product->get_uwa_order_id() );
								if ( $order ){
									$order_status = $order->get_status() ? $order->get_status() : __('unknown', 'woo_ua');?>
									<p class="form-field form-field-wide"><?php _e('Order has been made, order status is', 'woo_ua')?>: <a href='post.php?&action=edit&post=<?php echo $auction_product->get_uwa_order_id() ?>'><?php echo $order_status ?></a><span>
								<?php }
						}?>
				
				<?php }?>

		<?php endif;?>

		
		<?php if (($auction_product->is_uwa_expired() === FALSE) and ($auction_product->is_uwa_live() === TRUE)): ?>
		
		<?php endif;?>

		<?php  $heading = apply_filters('ultimate_woocommerce_auction_total_bids_heading', __( 'Total Bids Placed:', 'woo_ua' ) ); ?>
		<h2><?php echo $heading; ?></h2>

			<div class="woo_ua" id="uwa_auction_log_history" v-cloak>
				<div class="uwa-table-responsive">
						<table class="uwa-admin-table uwa-admin-table-bordered">
							<?php							
							$uwa_auction_log_history = $auction_product->uwa_auction_log_history();

							if ( !empty($uwa_auction_log_history)  ): ?>
							
								<tr>
									<th><?php _e('Bidder Name', 'woo_ua')?></th>
									<th><?php _e('Bidding Time', 'woo_ua')?></th>
									<th><?php _e('Bid', 'woo_ua')?></th>								
									<th><?php _e('Auto', 'woo_ua')?></th>								
									<th class="actions"><?php _e('Actions', 'woo_ua')?></th>
									<th></th>
								</tr>
								<?php foreach ($uwa_auction_log_history as $history_value) { 
								$start_date = $auction_product->get_uwa_auction_start_time();								
								if ( $history_value->date < $auction_product->get_uwa_auction_relisted() && ! isset( $uwa_relisted )) {
							    ?>
									<tr>
									<td><?php echo __( 'Auction relisted', 'woo_ua' );?></td>
									<td colspan="4"  class="bid_date"><?php echo mysql2date($datetimeformat,$start_date)?></td>
									</tr>							
								<?php $uwa_relisted = true; 
								} ?>
									<tr>
										<td class="bid_username"><a href="<?php echo get_edit_user_link($history_value->userid);?>">
										<?php echo uwa_user_display_name($history_value->userid);?></a></td>
										<td class="bid_date"><?php echo mysql2date($datetimeformat ,$history_value->date)?></td>
										<td class="bid_price"><?php echo wc_price($history_value->bid)?></td>
										<?php 
											if ($history_value->proxy == 1) { ?>
												<td class="proxy"><?php _e('Auto', 'woo_ua');?></td>
											<?php } else { ?>
												<td class="proxy"></td>
										<?php } ?>
										
										<td class="bid_action">
											<?php 	/*if ($auction_product->get_uwa_auction_expired() != '2') { */ ?>
											<?php if(!$auction_product->get_uwa_auction_payed()){ ?>
												<a href='#' data-id=<?php echo $history_value->id;?> 
												data-postid=<?php echo $post->ID;?>  ><?php echo __('Delete', 'woo_ua');?></a>
											<?php } ?>
										</td>
										<td class="won_choose">
											
										<?php if (($auction_product->is_uwa_expired() === FALSE) and ($auction_product->is_uwa_live() === TRUE)): ?>
											
											<a href='#' class='button uwa_force_choose_winner' data-bid_id=<?php echo $history_value->id;?> data-bid_user_id=<?php echo $history_value->userid;?>   
											 data-bid_amount=<?php echo $history_value->bid;?> data-auction_id=<?php echo $post->ID;?>  >
											 <?php echo __('Choose Winner', 'woo_ua');?></a>
										
										     <?php endif;?>
										</td>
										
									</tr>
								<?php } ?>	

							<?php endif;?>

							<tr class="start">									
									<?php 
									$start_date = $auction_product->get_uwa_auction_start_time();
									 if ($auction_product->is_uwa_live() === TRUE) { ?>
									<td class="started"><?php echo __('Auction started', 'woo_ua');?>
										<?php }   else { ?>									
									<td  class="started"><?php echo __('Auction starting', 'woo_ua');?>		
										<?php } ?></td>	
										
									<td colspan="5"  class="bid_date"><?php echo mysql2date($datetimeformat,$start_date)?></td>
							</tr>
						</table>
				</div>
			</div>	
			</div>	
			

				</div>  <!--wcfm-collapse-content END -->
			</div>  <!--Main Div END -->