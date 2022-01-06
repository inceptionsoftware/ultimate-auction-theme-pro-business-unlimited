<?php

/**
 * Ultimate WooCommerce Auction Pro Display Setting Tab
 *
 * @package Ultimate WooCommerce Auction Pro
 * @author Nitesh Singh 
 * @since 1.0 
 *
 */
 if(isset($_POST['uwa-settings-submit']) == 'Save Changes')
	{	
		if(isset($_POST['uwa_badge_image_url'])) {
			update_option('uwa_badge_image_url', sanitize_text_field($_POST['uwa_badge_image_url']));
		}
		if(isset($_POST['uwa_shop_enabled']) && absint($_POST['uwa_shop_enabled']) =="1"){
			update_option('woo_ua_show_auction_pages_shop', "yes");
		} else{
			update_option('woo_ua_show_auction_pages_shop', "no");
		}
		if(isset($_POST['uwa_search_enabled']) && absint($_POST['uwa_search_enabled']) =="1"){
			update_option('woo_ua_show_auction_pages_search', "yes");
		} else{
			update_option('woo_ua_show_auction_pages_search', "no");
		}
		if(isset($_POST['uwa_cat_enabled']) && absint($_POST['uwa_cat_enabled']) =="1"){
			update_option('woo_ua_show_auction_pages_cat', "yes");
		} else{
			update_option('woo_ua_show_auction_pages_cat', "no");
		}
		if(isset($_POST['uwa_tag_enabled']) && absint($_POST['uwa_tag_enabled']) =="1"){
			update_option('woo_ua_show_auction_pages_tag', "yes");
		} else{
			update_option('woo_ua_show_auction_pages_tag', "no");
		}
		if(isset($_POST['uwa_expired_enabled']) && absint($_POST['uwa_expired_enabled']) =="1"){
			update_option('woo_ua_expired_auction_enabled', "yes");
		} else{
			update_option('woo_ua_expired_auction_enabled', "no");
		}
		if(isset($_POST['uwa_schedule_enabled']) && absint($_POST['uwa_schedule_enabled']) == 1){ 
			update_option('uwa_schedule_enabled', "yes");
		} else{
			update_option('uwa_schedule_enabled', "no");
		}
		if (isset($_POST['uwa_countdown_format'])) {
			update_option('woo_ua_auctions_countdown_format', sanitize_text_field($_POST['uwa_countdown_format']));
		} else {
			update_option('woo_ua_auctions_countdown_format', 'yowdHMS');
		}
		if (isset($_POST['uwa_hide_compact_enable'])) {			
			update_option('uwa_hide_compact_enable', "yes");
		} else {
			update_option('uwa_hide_compact_enable', "no");
		}
		if(isset($_POST['uwa_private_message']) &&  absint($_POST['uwa_private_message']) ==1){	
			update_option('woo_ua_auctions_private_message', "yes");
		} else{
			update_option('woo_ua_auctions_private_message', "no");
		}
		if(isset($_POST['uwa_bids_tab']) && absint($_POST['uwa_bids_tab']) ==1){	
			update_option('woo_ua_auctions_bids_section_tab', "yes");
		} else{
			update_option('woo_ua_auctions_bids_section_tab', "no");
		}
		if(isset($_POST['uwa_watchlists_tab']) && absint($_POST['uwa_watchlists_tab']) ==1){
			update_option('woo_ua_auctions_watchlists', "yes");
		} else{
			update_option('woo_ua_auctions_watchlists', "no");
		}	
		
		if(isset($_POST['uwa_hide_reverse_text']) && absint($_POST['uwa_hide_reverse_text']) ==1){
			update_option('uwa_hide_reverse_text', "yes");
		} else{
			update_option('uwa_hide_reverse_text', "no");
		}

		if(isset($_POST['uwa_hide_proxy_text']) && absint($_POST['uwa_hide_proxy_text']) ==1){
            update_option('uwa_hide_proxy_text', "yes");
        } else{
            update_option('uwa_hide_proxy_text', "no");
        }	
        
		if(isset($_POST['uwa_reverse_text'])){
			update_option('uwa_reverse_text', sanitize_text_field($_POST['uwa_reverse_text']));
		}
		if(isset($_POST['uwa_proxy_text'])){
			update_option('uwa_proxy_text', sanitize_text_field($_POST['uwa_proxy_text']));
		}
		
		if(isset($_POST['uwa_show_timer_on_shoppage']) && absint($_POST['uwa_show_timer_on_shoppage']) ==1){
            update_option('uwa_show_timer_on_shoppage', "yes");
        } else{
            update_option('uwa_show_timer_on_shoppage', "no");
        }
		if(isset($_POST['uwa_hide_product_condition_field'])){	
			update_option('uwa_hide_product_condition_field', "yes");
		} else{
			update_option('uwa_hide_product_condition_field', "no");
		}	
		
		if(isset($_POST['uwa_hide_timer_field'])){	
			update_option('uwa_hide_timer_field', "yes");
		} else{
			update_option('uwa_hide_timer_field', "no");
		}	
		
		if(isset($_POST['uwa_hide_ending_on_field'])){	
			update_option('uwa_hide_ending_on_field', "yes");
		} else{
			update_option('uwa_hide_ending_on_field', "no");
		}	
		if(isset($_POST['uwa_hide_start_on_field'])){	
			update_option('uwa_hide_start_on_field', "yes");
		} else{
			update_option('uwa_hide_start_on_field', "no");
		}	
		
		if(isset($_POST['uwa_hide_timezone_field'])){	
			update_option('uwa_hide_timezone_field', "yes");
		} else{
			update_option('uwa_hide_timezone_field', "no");
		}	
		
		if(isset($_POST['uwa_hide_reserve_field'])){	
			update_option('uwa_hide_reserve_field', "yes");
		} else{
			update_option('uwa_hide_reserve_field', "no");
		}

		if(isset($_POST['uwa_show_reserve_price']) && absint($_POST['uwa_show_reserve_price']) ==1){
            update_option('uwa_show_reserve_price', "yes");
        } else{
            update_option('uwa_show_reserve_price', "no");
        }
		
		if(isset($_POST['uwa_show_direct_bid']) && absint($_POST['uwa_show_direct_bid']) ==1){
            update_option('uwa_show_direct_bid', "yes");
        } else{
            update_option('uwa_show_direct_bid', "no");
        }


        if(isset($_POST['uwa_display_wining_losing_text'])){
			$is_updated = update_option('uwa_display_wining_losing_text', sanitize_text_field($_POST['uwa_display_wining_losing_text']));
			
		}
		if(isset($_POST['uwa_display_wining_text'])){
			update_option('uwa_display_wining_text', sanitize_text_field($_POST['uwa_display_wining_text']));
		}
		if(isset($_POST['uwa_display_losing_text'])){
			update_option('uwa_display_losing_text', sanitize_text_field($_POST['uwa_display_losing_text']));
		}


        if(isset($_POST['uwa_label_direct_bid']) && $_POST['uwa_label_direct_bid'] !=  ""){
        	$directbid_label = trim($_POST['uwa_label_direct_bid']);
            update_option('uwa_label_direct_bid', $directbid_label);
        } else{
            update_option('uwa_label_direct_bid', "");
        }

        if(isset($_POST['uwa_show_custom_bid']) && absint($_POST['uwa_show_custom_bid']) ==1){
            update_option('uwa_show_custom_bid', "yes");
        } else{
            update_option('uwa_show_custom_bid', "no");
        }
		
		if(isset($_POST['uwa_label_custom_bid']) && $_POST['uwa_label_custom_bid'] !=  ""){
        	$custombid_label = trim($_POST['uwa_label_custom_bid']);
            update_option('uwa_label_custom_bid', $custombid_label);
        } else{
            update_option('uwa_label_custom_bid', "");
        }

        if(isset($_POST['uwa_winner_live_shop']) && absint($_POST['uwa_winner_live_shop']) ==1){
            update_option('uwa_winner_live_shop', "yes");
        } else{
            update_option('uwa_winner_live_shop', "no");
        }

        if(isset($_POST['uwa_winner_live_product']) && absint($_POST['uwa_winner_live_product']) ==1){
            update_option('uwa_winner_live_product', "yes");
        } else{
            update_option('uwa_winner_live_product', "no");
        }

        if(isset($_POST['uwa_winner_live_widget']) && absint($_POST['uwa_winner_live_widget']) ==1){
            update_option('uwa_winner_live_widget', "yes");
        } else{
            update_option('uwa_winner_live_widget', "no");
        }

          if(isset($_POST['uwa_winner_expired_shop']) && absint($_POST['uwa_winner_expired_shop']) ==1){
            update_option('uwa_winner_expired_shop', "yes");
        } else{
            update_option('uwa_winner_expired_shop', "no");
        }

        if(isset($_POST['uwa_winner_expired_product']) && absint($_POST['uwa_winner_expired_product']) ==1){
            update_option('uwa_winner_expired_product', "yes");
        } else{
            update_option('uwa_winner_expired_product', "no");
        }

        if(isset($_POST['uwa_winner_expired_widget']) && absint($_POST['uwa_winner_expired_widget']) ==1){
            update_option('uwa_winner_expired_widget', "yes");
        } else{
            update_option('uwa_winner_expired_widget', "no");
        }

	}

	/**
	 * Get All Details From DB.
	 */
	$uwa_badge_image_url = get_option('uwa_badge_image_url');	
	$shop_enable = get_option('woo_ua_show_auction_pages_shop');	
	$shop_checked_enable = "";
	if($shop_enable == "yes"){
		$shop_checked_enable = "checked";
	}	
	$search_enable = get_option('woo_ua_show_auction_pages_search');
	$search_checked_enable="";
	if($search_enable =="yes"){
		$search_checked_enable = "checked";
	}	
	$cat_enable = get_option('woo_ua_show_auction_pages_cat');
	$cat_checked_enable="";
	if($cat_enable =="yes"){
		$cat_checked_enable = "checked";
	}
	$tag_enable = get_option('woo_ua_show_auction_pages_tag');
	$tag_checked_enable="";
	if($tag_enable =="yes"){
		$tag_checked_enable = "checked";
	}
	$expired_enable = get_option('woo_ua_expired_auction_enabled');		
	$expired_checked_enable="";
	if($expired_enable =="yes"){
		$expired_checked_enable = "checked";
	}
	$uwa_schedule_enable = get_option('uwa_schedule_enabled');
	$schedule_checked_enable="";
		if($uwa_schedule_enable =="yes"){
		$schedule_checked_enable = "checked";
	}
	$countdown_format = get_option('woo_ua_auctions_countdown_format'); 
	$private_tab_enable = get_option('woo_ua_auctions_private_message');
	$private_checked_enable="";
	if($private_tab_enable =="yes"){
		$private_checked_enable = "checked";
	}
	$bids_tab_enable = get_option('woo_ua_auctions_bids_section_tab');
	$bids_checked_enable="";
	if($bids_tab_enable =="yes"){
		$bids_checked_enable = "checked";
	}
	$watchlists_tab_enable = get_option('woo_ua_auctions_watchlists');
	$watchlists_checked_enable="";
	if($watchlists_tab_enable =="yes"){
		$watchlists_checked_enable = "checked";
	}
	$uwa_hide_compact_enable= get_option('uwa_hide_compact_enable');
	$uwa_hide_compact_checked_enable="";
	if($uwa_hide_compact_enable =="yes"){
		$uwa_hide_compact_checked_enable = "checked";
	}
	$reverse_text_enable = get_option('uwa_hide_reverse_text',"no");
	$reverse_text_checked_enable="";
	if($reverse_text_enable =="yes"){
		$reverse_text_checked_enable = "checked";
	}
	$uwa_hide_proxy_text = get_option('uwa_hide_proxy_text',"no");
	$uwa_hide_proxy_text_checked_enable="";
	if($uwa_hide_proxy_text =="yes"){
		$uwa_hide_proxy_text_checked_enable = "checked";
	}
	$uwa_show_timer_on_shoppage = get_option('uwa_show_timer_on_shoppage');
	$uwa_show_timer_on_shoppage_checked_enable="";
	if($uwa_show_timer_on_shoppage =="yes"){
		$uwa_show_timer_on_shoppage_checked_enable = "checked";
	}
	
	$uwa_hide_product_condition_field = get_option('uwa_hide_product_condition_field',"no");
	$uwa_hide_product_condition_field_enable="";
		if($uwa_hide_product_condition_field =="yes"){
			$uwa_hide_product_condition_field_enable = "checked";
		}	
	$uwa_reverse_text = get_option('uwa_reverse_text',"This is reverse auction.");
	$uwa_proxy_text = get_option('uwa_proxy_text',"This auction is under proxy bidding.");

	$uwa_hide_timer_field = get_option('uwa_hide_timer_field',"no");
	$uwa_hide_timer_field_enable="";
		if($uwa_hide_timer_field =="yes"){
			$uwa_hide_timer_field_enable = "checked";
		}
		
	$uwa_hide_ending_on_field = get_option('uwa_hide_ending_on_field',"no");
	$uwa_hide_ending_on_field_enable="";
		if($uwa_hide_ending_on_field =="yes"){
			$uwa_hide_ending_on_field_enable = "checked";
		}
	$uwa_hide_start_on_field = get_option('uwa_hide_start_on_field',"no");
	$uwa_hide_start_on_field_enable="";
		if($uwa_hide_start_on_field =="yes"){
			$uwa_hide_start_on_field_enable = "checked";
		}
		
	$uwa_hide_timezone_field = get_option('uwa_hide_timezone_field',"no");;
	$uwa_hide_timezone_field_enable="";
		if($uwa_hide_timezone_field =="yes"){
			$uwa_hide_timezone_field_enable = "checked";
		}	
	$uwa_hide_reserve_field = get_option('uwa_hide_reserve_field',"no");
	$uwa_hide_reserve_field_enable="";
		if($uwa_hide_reserve_field =="yes"){
			$uwa_hide_reserve_field_enable = "checked";
		}	
	$uwa_show_reserve_price = get_option('uwa_show_reserve_price',"no");
	$uwa_show_reserve_price_field_enable ="";
		if($uwa_show_reserve_price =="yes"){
			$uwa_show_reserve_price_field_enable = "checked";
		}		
	$uwa_show_direct_bid = get_option('uwa_show_direct_bid',"no");
	$uwa_show_direct_bid_field_enable ="";
		if($uwa_show_direct_bid =="yes"){
			$uwa_show_direct_bid_field_enable = "checked";
		}
	$uwa_label_direct_bid = get_option('uwa_label_direct_bid', "");

	$uwa_show_custom_bid = get_option('uwa_show_custom_bid');
	$uwa_show_custom_bid_field_enable ="checked";
		if($uwa_show_custom_bid =="yes"){
			$uwa_show_custom_bid_field_enable = "checked";
		}elseif($uwa_show_custom_bid =="no"){
			$uwa_show_custom_bid_field_enable = "";
		}
	$uwa_label_custom_bid = get_option('uwa_label_custom_bid', "");

	$uwa_winner_live_shop = get_option('uwa_winner_live_shop',"no");
	$uwa_winner_live_shop_field_enable ="";
		if($uwa_winner_live_shop =="yes"){
			$uwa_winner_live_shop_field_enable = "checked";
		}

	$uwa_winner_live_product = get_option('uwa_winner_live_product',"no");
	$uwa_winner_live_product_field_enable ="";
		if($uwa_winner_live_product =="yes"){
			$uwa_winner_live_product_field_enable = "checked";
		}

	$uwa_winner_live_widget = get_option('uwa_winner_live_widget',"no");
	$uwa_winner_live_widget_field_enable ="";
		if($uwa_winner_live_widget =="yes"){
			$uwa_winner_live_widget_field_enable = "checked";
		}
	
	$uwa_winner_expired_shop = get_option('uwa_winner_expired_shop',"no");
	$uwa_winner_expired_shop_field_enable ="";
		if($uwa_winner_expired_shop =="yes"){
			$uwa_winner_expired_shop_field_enable = "checked";
		}

	$uwa_winner_expired_product = get_option('uwa_winner_expired_product',"no");
	$uwa_winner_expired_product_field_enable ="";
		if($uwa_winner_expired_product =="yes"){
			$uwa_winner_expired_product_field_enable = "checked";
		}	

	$uwa_winner_expired_widget = get_option('uwa_winner_expired_widget',"no");
	$uwa_winner_expired_widget_field_enable ="";
		if($uwa_winner_expired_widget =="yes"){
			$uwa_winner_expired_widget_field_enable = "checked";
		}



	$display_yes_checked = "";
	$display_no_checked = "";
	$uwa_display_wining_losing_text = get_option("uwa_display_wining_losing_text");
	
	if($uwa_display_wining_losing_text == "yes"){
		$display_yes_checked = "checked";
	}
	else if($uwa_display_wining_losing_text == "no" || $uwa_display_wining_losing_text == "" ){ 
		$display_no_checked = "checked";
	}

	$uwa_display_wining_text = get_option('uwa_display_wining_text', "You are Winning!");
	$uwa_display_losing_text = get_option('uwa_display_losing_text', "You are Losing!");

 ?>
	<div class="uwa_main_setting_content">
		<form  method='post' class='uwa_auction_setting_style'>
			<table class="form-table">
				<tbody>							
					<tr class="uwa_heading">
						<th colspan="2"><?php _e('Shop Page Setting', 'woo_ua' ); ?></th>
					</tr>
					<tr>
						<th scope="row"><?php _e( 'Badge image URL', 'woo_ua' ); ?></th>							 
						<td class="uwaforminp"> <a href="" class="uwa_fields_tooltip" onclick="return false"><strong>?</strong>
						    <span>			
							<?php _e('You can overright Auctions Badge Image. put image url here.', 'woo_ua');?>
							</span></a>
							<input type="url" size="100" class="regular-text" name="uwa_badge_image_url"  id="uwa_badge_image_url" value="<?php echo $uwa_badge_image_url; ?>">							
							<a  target="_blank"   href="<?php echo UW_AUCTION_PRO_ASSETS_URL."images/woo_ua_auction_big.png";?>"/>
							<?php _e('Default is', 'woo_ua');?>.</a>
						</td>
					</tr> 
					<tr>
						<th scope="row"><?php _e( 'Show Auctions on:', 'woo_ua' ); ?></th>							 
						<td>
							<input <?php echo $shop_checked_enable; ?> value="1" name="uwa_shop_enabled" type="checkbox">
							<?php _e( 'On Shop Page.', 'woo_ua' ); ?>
						</td>
					</tr>
					<tr>
						<th scope="row"></th>							 
						<td class="uwaforminp">
							<input <?php echo $search_checked_enable; ?> value="1" name="uwa_search_enabled" type="checkbox">
							<?php _e( 'On Product Search Page.', 'woo_ua' ); ?>
						</td>
					</tr> 
					<tr>
						<th scope="row"></th>							 
						<td class="uwaforminp">
							<input <?php echo $cat_checked_enable; ?> value="1" name="uwa_cat_enabled" type="checkbox">
							<?php _e( 'On Product Category Page.', 'woo_ua' ); ?>
						</td>
					</tr>
					<tr>
						<th scope="row"></th>							 
						<td class="uwaforminp">
							<input <?php echo $tag_checked_enable; ?> value="1" name="uwa_tag_enabled" type="checkbox"> <?php _e( 'On Product Tag Page.', 'woo_ua' ); ?>
						</td>
					</tr>
					<tr>
						<th scope="row"><?php _e( 'Shop Page:', 'woo_ua' ); ?></th>
						<td class="uwaforminp">
							<input <?php echo $expired_checked_enable; ?> value="1" name="uwa_expired_enabled" type="checkbox">
							<?php _e( 'Show Expired Auctions.', 'woo_ua' ); ?>
						</td>
					</tr>
					<tr>
						<th scope="row"></th>							 
						<td>
							<input <?php echo $schedule_checked_enable; ?> value="1" name="uwa_schedule_enabled" type="checkbox">
							<?php _e( 'Show Future Auctions.', 'woo_ua' ); ?>
						</td>
					</tr>	
					<tr>				
						<th scope="row"></th>
						<td>
							<input <?php echo $uwa_show_timer_on_shoppage_checked_enable; ?> value="1" name="uwa_show_timer_on_shoppage" type="checkbox">
							<?php _e('Enable Timer.','woo_ua');?>	
					    </td>
					</tr>
									
					<tr class="uwa_heading">
						<th colspan="2"><?php _e('Auction Detail Page Setting', 'woo_ua' ); ?></th>
					</tr>	
					<tr>				
						<th scope="row">
							<label for="uwa_countdown_format"><?php _e( 'Timer', 'woo_ua' ); ?></label>
						</th>
						<td class="uwaforminp">	<a href="" class="uwa_fields_tooltip" onclick="return false"><strong>?</strong>
								<span ><?php _e("Use the following characters (in order) to indicate which periods you want to display: 'Y' for years, 'O' for months, 'W' for weeks, 'D' for days, 'H' for hours, 'M' for minutes, 'S' for seconds.	Use upper-case characters for mandatory periods, or the corresponding lower-case characters for optional periods, i.e. only display if non-zero. Once one optional period is shown, all the ones after that are also shown.", 'woo_ua');
								?>	
								</span>
							</a>								
							<input type="text" name="uwa_countdown_format" class="regular-number" id="uwa_countdown_format" value="<?php echo $countdown_format; ?>"><?php _e( 'The format for the countdown display. Default is yowdHMS', 'woo_ua' ); ?>
							
					    </td>
					</tr>
					
					<tr>				
						<th scope="row"></th>
						<td>
							<input <?php echo $uwa_hide_compact_checked_enable; ?> value="1" name="uwa_hide_compact_enable" type="checkbox">
							<?php _e('Enable simple format and Hide compact countdown format.','woo_ua');?>	
					    </td>
					</tr>
					
					<tr>
						<th scope="row"></th>
						<td>
							<input type="checkbox" <?php echo $uwa_hide_timer_field_enable;?> name="uwa_hide_timer_field"  id="uwa_hide_timer_field" value="1"><?php _e('Disable Timer on Auction Detail Page.', 'woo_ua');  ?>
						</td>
					</tr>
					
					
					<tr>
					<th>
						<label for="uwa_hide_product_condition_field"><?php _e( 'Disable Specific field:', 'woo_ua' ); ?></label>
					</th>
					<td class="uwaforminp">									
						<input type="checkbox" <?php echo $uwa_hide_product_condition_field_enable;?> name="uwa_hide_product_condition_field"  id="uwa_hide_product_condition_field" value="1"><?php _e('Product(item) Condition field.', 'woo_ua');  ?>			
								
					</td>
				  </tr>
				
				
				<!--NEW VERSION FROM 2.0.1-->
				
				<tr>
					<th scope="row"></th>
					<td>
						<input type="checkbox" <?php echo $uwa_hide_ending_on_field_enable;?> name="uwa_hide_ending_on_field"  id="uwa_hide_ending_on_field" value="1"><?php _e('Ending On date For Live Auction and Future Auction.', 'woo_ua');  ?>
					</td>
				</tr>
				
				<tr>
					<th scope="row"></th>
					<td>
						<input type="checkbox" <?php echo $uwa_hide_start_on_field_enable;?> name="uwa_hide_start_on_field"  id="uwa_hide_start_on_field" value="1"><?php _e('Start On date For Future Auction.', 'woo_ua');  ?>
					</td>
				</tr>
				
				
				<tr>
					<th scope="row"></th>
					<td>
						<input type="checkbox" <?php echo $uwa_hide_timezone_field_enable;?> name="uwa_hide_timezone_field"  id="uwa_hide_timezone_field" value="1"><?php _e('Timezone.', 'woo_ua');  ?>
					</td>
				</tr>
				
				<tr>
					<th scope="row"></th>
					<td>
						<input type="checkbox" <?php echo $uwa_hide_reserve_field_enable;?> name="uwa_hide_reserve_field"  id="uwa_hide_reserve_field" value="1"><?php _e('Reserve Price Text.', 'woo_ua');  ?>
					</td>
				</tr>
				
				<tr>
					<th scope="row"></th>
					<td>
						<input <?php echo $uwa_hide_proxy_text_checked_enable; ?> value="1" name="uwa_hide_proxy_text"
						id="uwa_hide_proxy_text" type="checkbox"><?php _e( 'Proxy Auction Text.', 'woo_ua' ); ?>
					   </td>
					</tr>
					
					<tr>
						<th scope="row"></th>
						<td >	
					   <input type="text" class="regular-text" name="uwa_proxy_text" value="<?php echo $uwa_proxy_text;?>" id="uwa_proxy_text">	
						<?php _e( 'Enter Text for Proxy Auction.', 'woo_ua' ); ?>
					   </td>
					</tr>
				
				<tr>					
						<th scope="row"></th>
						<td>
						<input <?php echo $reverse_text_checked_enable; ?> value="1" name="uwa_hide_reverse_text" type="checkbox"><?php _e( 'Reverse Auction Text.', 'woo_ua' ); ?>						
						</td>
					</tr>
					<tr>
						<th scope="row"></th>
						<td>
						<input type="text" class="regular-text" name="uwa_reverse_text" value="<?php echo $uwa_reverse_text;?>" id="uwa_reverse_text">
						<?php _e( 'Enter Text for Reverse Auction.', 'woo_ua' ); ?>
					   </td>
					</tr>
					
				<tr>
					<th>
						<label for="uwa_show_reserve_price"><?php _e( 'Enable Specific field:', 'woo_ua' ); ?></label>
					</th>
					<td class="uwaforminp">
						<input type="checkbox" <?php echo $uwa_show_reserve_price_field_enable;?> 
							name="uwa_show_reserve_price"  id="uwa_show_reserve_price" value="1">
							<?php _e('Reserve Price.', 'woo_ua');  ?>
					</td>
				  </tr>		
				<tr>
					<th>
						<label for="uwa_show_direct_bid"></label>
					</th>
					<td class="uwaforminp">									
						<input type="checkbox" <?php echo $uwa_show_direct_bid_field_enable;?> name="uwa_show_direct_bid"  id="uwa_show_direct_bid" value="1"><?php _e('Direct bid button.', 'woo_ua');  ?>
					</td>
				</tr>

				<tr>
					<th scope="row"></th>
						<td>
						   <input type="text" class="regular-text" name="uwa_label_direct_bid" value="<?php echo 
						   		$uwa_label_direct_bid;?>" id="uwa_label_direct_bid">	
							<?php _e( 'Enter label for direct bid button or left blank to set to default', 'woo_ua' ); ?>
					   </td>
				</tr>

				<tr>
					<th>
						<label for="uwa_show_custom_bid"></label>
					</th>
					<td class="uwaforminp">									
						<input type="checkbox" <?php echo $uwa_show_custom_bid_field_enable;?> name="uwa_show_custom_bid"  id="uwa_show_custom_bid" value="1"><?php _e('Custom bid button.', 'woo_ua');  ?>
					</td>
				</tr>

				<tr>
					<th scope="row"></th>
						<td>
						   <input type="text" class="regular-text" name="uwa_label_custom_bid" value="<?php echo 
						   		$uwa_label_custom_bid;?>" id="uwa_label_custom_bid">	
							<?php _e( 'Enter label for custom bid button or left blank to set to default', 'woo_ua' ); ?>
					   </td>
				</tr>
				
				
				<!--end NEW VERSION FROM 2.0.1-->
				
					<tr>
						<th scope="row"><?php _e( 'Enable Specific Tabs:', 'woo_ua' ); ?></th>
						<td>
							<input <?php echo $private_checked_enable; ?> value="1" name="uwa_private_message" type="checkbox">
							<?php _e('Enable Send Private message.','woo_ua');?>
						</td>
					</tr>
					<tr>
						<th scope="row"></th>
						<td>
							<input <?php echo $bids_checked_enable; ?> value="1" name="uwa_bids_tab" type="checkbox">
							<?php _e('Enable Bids section.','woo_ua');?>
						</td>
					</tr>
					<tr>
						<th scope="row"></th>
						<td>
							<input <?php echo $watchlists_checked_enable; ?> value="1" name="uwa_watchlists_tab" type="checkbox"><?php _e( 'Enable Watchlists.', 'woo_ua' ); ?>
						</td>
					</tr>	


					<tr>
						<th scope="row">
					   		<label for="uwa_display_wining_losing_text">
					   		<?php _e("Do you want to show winning/losing message to user", "woo_ua"); ?></label>
						</th>
						<td>
							<a href="" class="uwa_fields_tooltip" onclick="return false"><strong>?</strong>
							<span><?php _e("If yes, it will display message on shop page and detail page", 
								"woo_ua"); ?> </span></a>
							
							<input type="radio" name="uwa_display_wining_losing_text" id="uwa_display_wining_losing_text_yes" value="yes"  
								<?php echo $display_yes_checked; ?>> 
								<span class="description"><?php _e("Yes", "woo_ua");  ?></span>
								<span style="margin-right:20px;"></span> 
							<input type="radio" name="uwa_display_wining_losing_text" id="uwa_display_wining_losing_text_no" value="no" 
								<?php echo $display_no_checked; ?>> 
								<span class="description"><?php _e("No", "woo_ua");  ?></span>
							<br /><br />
						</td> 
					</tr> 
					<tr>
						<th scope="row"></th>
						<td>
						<input type="text" name="uwa_display_wining_text" value="<?php echo stripslashes($uwa_display_wining_text);?>" id="uwa_display_wining_text">
						<?php _e("Enter Text for Auction Winning User.", "woo_ua"); ?>
					   </td>
					</tr>
					<tr>
						<th scope="row"></th>
						<td>
						<input type="text" name="uwa_display_losing_text" value="<?php echo stripslashes($uwa_display_losing_text);?>" id="uwa_display_losing_text">
						<?php _e("Enter Text for Auction Losing User.", "woo_ua"); ?>
					   </td>
					</tr>
					



					<tr class="uwa_heading">
						<th colspan="2"><?php _e('Display Winner', 'woo_ua' ); ?></th>
					</tr>				

					<tr>
						<th scope="row"><?php _e('In Live Auctions:', 'woo_ua' ); ?></th>
						<td class="uwa_winner_live">
							<input <?php echo $uwa_winner_live_shop_field_enable;?> name="uwa_winner_live_shop" 
								type="checkbox" value="1"> <?php _e('Shop page','woo_ua');?>
							<input  <?php echo $uwa_winner_live_product_field_enable;?> name="uwa_winner_live_product" 
								type="checkbox" value="1"> <?php _e('Product detail page','woo_ua');?>
							<input <?php echo $uwa_winner_live_widget_field_enable;?> name="uwa_winner_live_widget" 
								type="checkbox" value="1"><?php _e('Widgets','woo_ua');?>							
						</td>
					</tr>
					<tr>
						<th scope="row"><?php _e('In Expired Auctions:', 'woo_ua' ); ?></th>
						<td class="uwa_winner_expired">
							<input <?php echo $uwa_winner_expired_shop_field_enable;?> name="uwa_winner_expired_shop" 
								type="checkbox" value="1"> <?php _e('Shop page','woo_ua');?>
							<input <?php echo $uwa_winner_expired_product_field_enable;?> name="uwa_winner_expired_product" 
								type="checkbox" value="1"> <?php _e('Product detail page','woo_ua');?>
							<input <?php echo $uwa_winner_expired_widget_field_enable;?> name="uwa_winner_expired_widget" 
								type="checkbox" value="1"><?php _e('Widgets','woo_ua');?>						
						</td>
					</tr>		
					<tr class="submit">
						<th colspan="2">
							<input type="submit" id="uwa-settings-submit" name="uwa-settings-submit" class="button-primary" value="<?php _e('Save Changes','woo_ua');?>" />
						</th>
					</tr>


				</tbody>						
			</table>
		</form>
	</div>

	<style>
		.uwa_main_setting .uwa_main_setting_content table tr td.uwa_winner_live input:not(:first-child),
		.uwa_main_setting .uwa_main_setting_content table tr td.uwa_winner_expired input:not(:first-child) {
    		margin-left: 30px;
		}
	</style>