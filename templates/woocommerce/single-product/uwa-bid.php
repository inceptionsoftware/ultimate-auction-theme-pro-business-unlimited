<?php

/* NOTE -- don't change class name and structure as it used in ajax place bid */

/**
 * Auction Product Bid Area
 * 
 * @package Ultimate WooCommerce Auction PRO
 * @author Nitesh Singh
 * @since 1.0
 *
 */

if (!defined('ABSPATH')) {
	exit;
}

global $woocommerce, $product, $post;

if(!(method_exists( $product, 'get_type') && $product->get_type() == 'auction')){
	return;
}

$uwa_placebid_ajax_enable = get_option('woo_ua_auctions_placebid_ajax_enable', "no");

if($uwa_placebid_ajax_enable == "yes"){
	$button = "button";
}elseif($uwa_placebid_ajax_enable == "no"){
	$button = "submit";
}

/* -- aelia -- */
$product_base_currency = $product->uwa_aelia_get_base_currency();	
$currency_symbol = $product->uwa_aelia_get_base_currency_symbol();
$args = array("currency" => $product_base_currency);


$uwa_auction_type = $product->get_uwa_auction_type();
$uwa_expired = $product->is_uwa_expired();
$uwa_started = $product->is_uwa_live();
$uwa_reserved = $product->is_uwa_reserved();
$uwa_proxy  = $product->get_uwa_auction_proxy();
$uwa_reserve_met = $product->is_uwa_reserve_met();
$uwa_silent = $product->get_uwa_auction_silent();
$uwa_countdown_format = get_option( 'woo_ua_auctions_countdown_format' );
$uwa_remaining_seconds = $product->get_uwa_remaining_seconds();
$uwa_ending_time = $product->get_uwa_auctions_end_time();
$uwa_starting_time = $product->get_uwa_auction_start_time();
$uwa_start_price = $product ->get_uwa_auction_start_price();
$uwa_bid_value = $product->uwa_bid_value();
$curent_bid = $product->get_uwa_auction_current_bid();
$current_user = wp_get_current_user();
$product_id =  $product->get_id();
$user_max_bid = $product->get_uwa_user_max_bid($product_id ,$current_user->ID );
$reverse_bid_text = $uwa_auction_type == 'reverse' ? __( 'Your Minimum Bid is', 'woo_ua' ) : __( 'Your Maximum Bid is', 'woo_ua' );
$date_format = get_option( 'date_format' );
$time_format = get_option( 'time_format' );
$gmt_offset = get_option('gmt_offset') > 0 ? '+'.get_option('gmt_offset') : get_option('gmt_offset');
$timezone_string = get_option('timezone_string') ? get_option('timezone_string') : __('UTC ','woo_ua').$gmt_offset;
/*$silent_bid_text = $uwa_auction_type == 'reverse' ? sprintf(__( "Minimum bid for this auction is %s.", 'woo_ua' ), wc_price($uwa_start_price)) : sprintf(__( "Maximum bid for this auction is %s.", 'woo_ua' ), wc_price($uwa_start_price));*/

$silent_bid_text = sprintf(__( "Opening price is %s.", 'woo_ua' ), 
	wc_price($uwa_start_price, $args));

$uwa_enable_bid_place_warning = get_option('uwa_enable_bid_place_warning');
$auction_selling_type = $product->get_uwa_auction_selling_type();

$bid_inc = $product->get_uwa_auction_bid_increment();
$uwa_variable_inc_enable = get_post_meta($product->get_uwa_wpml_default_product_id(), 
			'uwa_auction_variable_bid_increment', true);

?>

<div id="auction-product-type"  
	data-auction-id="<?php echo esc_attr( $product_id ); ?>">

<?php 
if(get_option('uwa_winner_live_product') == 'yes'){	
	if($uwa_expired == FALSE){  /* live auctions */
		?>
		<div class="winner-name" data-auction_id="<?php echo esc_attr( $product_id ); ?>">
			<?php
		  
			$winner_text = $product->get_uwa_winner_text(); 
			if($winner_text){ ?>

				<span style="color:green;font-size:20px;" ><?php echo $winner_text; ?></span><br>
			
				<?php
			}
			?>
		</div>
		<?php
	}
}


if(get_option('uwa_hide_product_condition_field', 'no') == 'no'){
$uwa_auction_condition = $product->get_uwa_condition();
	?>
	<p class="uwa_auction_condition">
		<strong>
			<?php _e('Item condition:', 'woo_ua'); ?>
		</strong>
		<span class="uwa_auction_current_condition"> 
			<?php _e($uwa_auction_condition,'woo_ua' )  ?>			
		</span>
	</p>
	<?php } ?>
<?php if(($uwa_expired === FALSE ) and ($uwa_started  === TRUE )) : ?>


	<?php

	/* display winning or losing text */

	if(get_option('uwa_display_wining_losing_text') == 'yes'){

			$current_userid = get_current_user_id();

			$arr_getdata = $product->uwa_display_user_winlose_text();
			$set_text = $arr_getdata['set_text'];
			$display_data = "";

			/* display above auction image */
			echo '<span class="uwa_imgtext" data-auction_id="'.$product_id.'" data-user_id="'.$current_userid.'">';
				
			if($set_text != ""){
				$display_text = $arr_getdata['display_text'];
				
				if($set_text == "winner"){					
					echo '<span class="uwa_winning">'.$display_text.'</span>';
				}
				elseif($set_text == "loser"){
					echo '<span class="uwa_losing">'.$display_text.'</span>';
				}
			}
			echo '</span>';

			
			/* display above timer */
			echo '<p class="uwa_detailtext" data-auction_id="'.$product_id.'" data-user_id="'.$current_userid.'">';

			if($set_text != ""){
				$display_text = $arr_getdata['display_text'];
				
				if($set_text == "winner"){				
					echo '<span class="uwa_winning_detail">'.stripslashes($display_text).'</span>';
				}
				elseif($set_text == "loser"){					
					echo '<span class="uwa_losing_detail">'.stripslashes($display_text).'</span>';
				}
			}
			echo "</p>";
				
	} /* end of if - wining losing */

	?>

	
	<?php if(get_option('uwa_hide_timer_field', 'no') == 'no'){ 
	$uwa_remaining_seconds  =  wp_date('Y-m-d H:i:s',$product->get_uwa_remaining_seconds(),get_uwa_wp_timezone());
			$uwa_time_zone =  (array)wp_timezone();
			$sinceday  =  wp_date('M j, Y H:i:s O',time(),get_uwa_wp_timezone());
			
		 
	?>
	<script>
			var servertime='<?php echo $sinceday;?>';
			</script>
	<div class="uwa_auction_time" id="uwa_auction_countdown">
			<strong>
				<?php _e('Time Left:', 'woo_ua'); ?>
			</strong>
			<div class="uwa-main-auction-product uwa_auction_product_countdown" data-time="<?php echo $uwa_remaining_seconds;?>" data-auction-id="<?php echo esc_attr( $product_id ); ?>" 
			data-format="<?php echo $uwa_countdown_format; ?>"  data-timezonetype="<?php echo $uwa_time_zone['timezone_type']; ?>" data-zone="<?php echo $uwa_time_zone['timezone']; ?>"  ></div>
	</div>		
	<?php } ?>
	
	<div class='uwa_auction_product_ajax_change'>
		<!--<p class="uwa_more_details"><a href='#'><?php _e('More Details', 'woo_ua'); ?></a></p> 
		<div class='uwa_more_details_display' style="display:none;"> -->
	
		<?php if(get_option('uwa_hide_ending_on_field', 'no') == 'no'){ ?>
		<p class="uwa_auction_end_time">
			<strong><?php _e('Ending On:', 'woo_ua'); ?></strong>			
			<?php echo  date_i18n( get_option( 'date_format' ),  strtotime( $uwa_ending_time ));  ?>  
			<?php echo  date_i18n( get_option( 'time_format' ),  strtotime( $uwa_ending_time ));  ?>		
		</p>
		<?php } ?>
		<?php if(get_option('uwa_hide_timezone_field', 'no') == 'no'){ ?>
		<p class="uwa_auction_product_timezone">
			<strong><?php _e('Timezone:', 'woo_ua'); ?></strong>
			<?php echo $timezone_string; ?>
		</p>
		<?php } ?>	
<?php 
/* ------  selling type - more details start  ------ */

if($auction_selling_type == "auction" || $auction_selling_type == "both" || 
	$auction_selling_type == ""){

/* ------  selling type - more details end  ------ */
?>

		
		<?php if ($uwa_silent != 'yes'){ ?>	
		<?php if(get_option('uwa_show_reserve_price', 'no') == 'yes'){ ?>
			<div class="checkreserve">
			 <?php $reserve_price = wc_price($product->get_uwa_auction_reserved_price(), $args); ?>
			 
			<?php if(($uwa_reserved === TRUE) &&( $uwa_reserve_met === FALSE )  ) { ?>
						<p class="uwa_auction_reserve_not_met">
						<strong><?php printf(__('Reserve price (%s) has not been met.','woo_ua') , $reserve_price);?></strong>
						</p>
		    <?php } ?>   
			
			<?php if(($uwa_reserved === TRUE) &&( $uwa_reserve_met === TRUE )  ) { ?>
				<p class="uwa_auction_reserve_met">
				<strong><?php printf(__('Reserve price (%s) has been met.','woo_ua') , $reserve_price);?></strong>
				</p>
			<?php } ?>
			</div>
		<?php } ?>
		
			<?php if(get_option('uwa_hide_reserve_field', 'no') == 'no' && get_option('uwa_show_reserve_price', 'no') == 'no'){ ?>
				<div class="checkreserve">
					<?php if(($uwa_reserved === TRUE) &&( $uwa_reserve_met === FALSE )  ) { ?>
						<?php $reserve_text = __( "price has not been met.", 'woo_ua' ); ?>
						<p class="uwa_auction_reserve_not_met">
							<strong><?php printf(__('Reserve %s','woo_ua') , $reserve_text);?></strong>
						</p>	
					<?php } ?>

					<?php if(($uwa_reserved === TRUE) &&( $uwa_reserve_met === TRUE )  ) { ?>
						<?php $reserve_text = __( "price has been met.", 'woo_ua' ); ?>
						<p class="uwa_auction_reserve_met">
							<strong><?php printf(__('Reserve %s','woo_ua') , $reserve_text);?></strong>
						</p>
					<?php } ?>
				</div>
			<?php } ?>		

<?php } elseif($uwa_silent == 'yes'){?>

	<p class="uwa_sealed_text">				
				<?php echo apply_filters('ultimate_woocommerce_auction_silent_bid_text', __( "This auction is silent-bid.", 'woo_ua' )); ?><a href="" class="uwa_fields_tooltip" onclick="return false"><strong>?</strong>
		<span ><?php _e( "A silent bid auction is a type of auction process in which all bidders simultaneously submit sealed bids to the auctioneer, so that no bidder knows how much the other auction participants have bid. The highest bidder is usually declared the winner of the bidding process", 'woo_ua' ) ?>
		</span></a>				
	</p>

		<?php 
				if (!empty($uwa_start_price)) {?>
					
				<p class="uwa_silent_bid_text"><?php echo $silent_bid_text; ?></p>
						
				<?php } ?>	
				
<?php } ?>
	
<?php 
	if($uwa_auction_type == 'reverse' ) {	
			if(get_option('uwa_hide_reverse_text', 'no') == 'no'){
				$reverse_text = get_option('uwa_reverse_text');
				if(!empty($reverse_text)){ ?>
					<p class="uwa_reverse_text">
						<strong><?php _e($reverse_text, 'woo_ua');  ?></strong>
					</p>
					<?php
				}
			}
	}
?>

<?php 
	if($uwa_proxy == 'yes' ) { 	
			if(get_option('uwa_hide_proxy_text', 'no') == 'no'){
				$proxy_text = get_option('uwa_proxy_text');
				if(!empty($proxy_text)){ ?>
					<p class="uwa_proxy_text">
						<?php _e('<strong>'.$proxy_text.'</strong>', 'woo_ua');  ?>
					</p>
					<?php
				}
			}
	}
?>	

			<p class="max-bid">
			<?php if ($uwa_proxy == 'yes' &&  $product->get_uwa_auction_max_current_bider() && get_current_user_id() == $product->get_uwa_auction_max_current_bider()) {
				?>
				<?php  _e( $reverse_bid_text , 'woo_ua' ) ?> <?php echo wc_price($product->get_uwa_auction_max_bid(), $args); ?>
			<?php } ?>
			</p>

<?php 
/* ------  selling type - more details start  ------ */
} /* end of if auction selling type */
/* ------  selling type - more details end  ------ */

?>
	
	<!--</div> --End More Details> --->


	<?php do_action('ultimate_woocommerce_auction_before_bid_form'); ?>
		

<?php 
/* ------  selling type - place bid start  ------ */

if($auction_selling_type == "auction" || $auction_selling_type == "both" || 
	$auction_selling_type == ""){

/* ------  selling type - place bid end  ------ */
?>


<?php /* ==================== form1 - direct bid start =================== */ ?>


<?php if(get_option('uwa_show_direct_bid', 'no') == 'yes'){ ?>
	<?php if($uwa_silent != "yes"){ ?>
		<form id="uwa_auction_form_direct"
			class="uwa_auction_form cart" method="post" enctype='multipart/form-data' data-product_id="<?php echo $product_id; ?>">
				<?php do_action('ultimate_woocommerce_auction_before_bid_button'); 

					$get_directbid_label = get_option('uwa_label_direct_bid', "");
					if($get_directbid_label){											
						$directbid_label = 	__($get_directbid_label, 'woo_ua');
					}
					else{											
						$directbid_label = 	__("Directly Bid", 'woo_ua');
					}

				?>

				<?php if($uwa_auction_type == 'reverse' ) { ?>
				
						<div class="quantity buttons_added">
							<span class="uwa_currency"><?php echo $currency_symbol;?></span>		
							<input type="number" name="uwa_bid_value" id="uwa_bid_value_direct"  readonly
								data-auction-id="<?php echo esc_attr( $product_id ); ?>"  
							value="<?php echo $uwa_bid_value; ?>"
							<?php if ($uwa_silent != 'yes'){ ?> min="1" max="<?php echo $uwa_bid_value  ?>" <?php } ?>   
							<?php if ($uwa_silent == 'yes'){ ?> min="1" 
							<?php } ?>   
							step="any" size="<?php echo strlen($product->get_uwa_current_bid())+2 ?>" title="bid"  class="input-text  qty bid text left">					
						</div>	

						<button type="<?php echo $button; ?>" id="placebidbutton_direct" class="bid_button button alt">
						 <?php echo apply_filters('ultimate_woocommerce_auction_bid_button_text', $directbid_label, 
						 	$product); ?></button>

						
				<?php } else { ?>

					<?php /* --- form1 normal auctions  ---- */
					   
					    //echo $product->get_uwa_next_bid_options($uwa_bid_value, $bid_inc);

						//echo $product->get_uwa_next_bid_options_proxy($uwa_bid_value, $bid_inc, $total_count);
						//echo $product->get_uwa_next_bid_options_variable($uwa_bid_value);
						//if($uwa_variable_inc_enable == "yes"){
								//echo "in if";
						//}

					?>


				<div class="quantity buttons_added">
					<select name="uwa_bid_value" id="uwa_bid_value_direct" 
						data-auction-id="<?php echo esc_attr( $product_id ); ?>"
						title="bid" class="input" style="width: 100%; padding: 9px;">
						<?php

							/* variable increment */

							if($uwa_variable_inc_enable == "yes"){

								if($uwa_proxy == "yes"){
									echo $product->get_uwa_next_bid_options_proxy_variable($uwa_bid_value, $bid_inc);
								}
								else{
									echo $product->get_uwa_next_bid_options_variable($uwa_bid_value);
								}
							}
							else{   /* bid increment */

								if($uwa_proxy == "yes"){
									echo $product->get_uwa_next_bid_options_proxy($uwa_bid_value, $bid_inc);
								}
								else{
									echo $product->get_uwa_next_bid_options($uwa_bid_value, $bid_inc);
								}
							}

						?>
					</select>
				</div>

					<!-- <div class="quantity buttons_added">
						<span class="uwa_currency"><?php echo $currency_symbol;?></span>
						<input type="number" name="uwa_bid_value"  
						id="uwa_bid_value_direct" readonly
						data-auction-id="<?php echo esc_attr( $product_id ); ?>"
						value="<?php echo $uwa_bid_value; ?>"
						<?php if ($uwa_silent != 'yes'){ ?>  min="<?php echo $uwa_bid_value  ?>"  <?php } ?> 
						<?php if ($uwa_silent == 'yes'){ ?>  min="1"  
						<?php } ?> 
						step="any" size="<?php echo strlen($product->get_uwa_current_bid())+2 ?>" title="bid"  class="input-text qty  bid text left">
					</div> -->
					

				<button type="<?php echo $button; ?>" id="placebidbutton_direct"  class="bid_button button alt">
					<?php echo apply_filters('ultimate_woocommerce_auction_bid_button_text', $directbid_label, $product); ?></button>

				
				<?php } /* end of else */ ?>

				<?php $loader_imgurl = UW_AUCTION_PRO_URL . "assets/images/ajax_loader.gif"; ?>
					
					<span class="ajax-loader-placebid_direct" style="display:none;">
						<img class='loaderimg_direct' 
							src="<?php echo $loader_imgurl; ?>"
							style="visibility: hidden;margin-left:5px;" />
					</span>
					
				<input type="hidden" name="bid" value="<?php echo esc_attr( $product_id ); ?>" />		
				<input type="hidden" id="uwa_place_bid_direct" name="uwa-place-bid" value="<?php echo $product_id; ?>" />
				<input type="hidden" name="product_id" value="<?php echo esc_attr( $product_id ); ?>" />
				<?php if ( is_user_logged_in() ) { ?>
					<input type="hidden" name="user_id" value="<?php echo  get_current_user_id(); ?>" />
				<?php  } ?> 
				
			<?php do_action('ultimate_woocommerce_auction_after_bid_button'); ?>
				
			</form>

		<?php } /* end of if slient auction */ ?>
		<?php /* ==================== form1 - direct bid end =================== */ ?>
<?php }  ?>

<?php /* ==================== form2 - custom bid start =================== */ ?>

<?php if(get_option('uwa_show_custom_bid') == 'yes' || get_option('uwa_show_custom_bid') == false){ ?>
<form id ="uwa_auction_form"
	class="uwa_auction_form cart" method="post" enctype='multipart/form-data' data-product_id="<?php echo $product_id; ?>">
		<?php do_action('ultimate_woocommerce_auction_before_bid_button'); 

				$get_custombid_label = get_option('uwa_label_custom_bid', "");
				if($get_custombid_label){										
					$custombid_label = 	__($get_custombid_label, 'woo_ua');
				}
				else{										
					$custombid_label = 	__("Custom Bid", 'woo_ua');
				}
		?>

			<?php if($uwa_silent != "yes"){ ?>			
				<p></p>				
			<?php } /* end of if slient auction */
			else { 
				echo "<br>"; 
			} ?>


		<?php if($uwa_auction_type == 'reverse' ) { ?>
		
				<div class="quantity buttons_added">
					<span class="uwa_currency"><?php echo $currency_symbol;?></span>		
					<input type="number" name="uwa_bid_value" 
					id="uwa_bid_value" 
						data-auction-id="<?php echo esc_attr( $product_id ); ?>"value="" 
					<?php if ($uwa_silent != 'yes'){ ?> min="1" max="<?php echo $uwa_bid_value  ?>" <?php } ?>   
					<?php if ($uwa_silent == 'yes'){ ?> min="1" 
					<?php } ?>   
					step="any" size="<?php echo strlen($product->get_uwa_current_bid())+2 ?>" title="bid"  class="input-text  qty bid text left">					
				</div>	

				<button type="<?php echo $button; ?>" id="placebidbutton" class="bid_button button alt">
				 <?php echo apply_filters('ultimate_woocommerce_auction_bid_button_text', $custombid_label, 
				 	$product); ?></button>

				<?php if ($uwa_silent != 'yes'){ ?>
					 <div class="uwa_inc_price_hint" >		
					 <small class="uwa_inc_price">(<?php _e('Enter less than or equal to', 'woo_ua') ?> : </small>
					 <small class="uwa_inc_latest_price uwa_inc_price_ajax_<?php echo $product_id; ?>">
					 <?php echo wc_price($uwa_bid_value, $args);?> )</small>		
					</div>
					<?php if($uwa_proxy == 'yes' ) { ?>	
					 <br><small class="uwa_inc_price">(<?php _e('This will set your max bid.', 'woo_ua') ?> ) </small> <br><br>
					<?php } ?>
				<?php } ?>

				<?php $loader_imgurl = UW_AUCTION_PRO_URL . "assets/images/ajax_loader.gif"; ?>
			
				<span class="ajax-loader-placebid" style="display:none;">
	  				<img class='loaderimg' src="<?php echo $loader_imgurl; ?>" 
	  					style="visibility: hidden;margin-left:5px;" />
	  			</span>
	 			
		<?php } else { ?>
			<div class="quantity buttons_added">
				
				<?php 
				if ( is_user_logged_in() ) {

					if ( isset($_SESSION['id']) && $_SESSION['id'] == $product_id ) {
 					 $bidval = $_SESSION['bid'.$product_id];
					}
				}
				?>

				<span class="uwa_currency"><?php echo $currency_symbol;?></span>	
				<input type="number" name="uwa_bid_value"  id="uwa_bid_value" data-auction-id="<?php echo esc_attr( $product_id ); ?>"
				<?php if(isset($bidval)) { ?>value="<?php echo $bidval; ?>" <?php } ?>
				<?php if ($uwa_silent != 'yes'){ ?>  min="<?php echo $uwa_bid_value  ?>"  <?php } ?> 
				<?php if ($uwa_silent == 'yes'){ ?>  min="1"  
				<?php } ?> 
				step="any" size="<?php echo strlen($product->get_uwa_current_bid())+2 ?>" title="bid"  class="input-text qty  bid text left">
			</div>
			
		<button type="<?php echo $button; ?>" id="placebidbutton" class="bid_button button alt">
			<?php echo apply_filters('ultimate_woocommerce_auction_bid_button_text', $custombid_label, 
				$product); ?></button>

			<?php $loader_imgurl = UW_AUCTION_PRO_URL . "assets/images/ajax_loader.gif"; ?>
			
			<span class="ajax-loader-placebid" style="display:none;">
  				<img class='loaderimg' src="<?php echo $loader_imgurl; ?>" 
  					style="visibility: hidden;margin-left:5px;" />
  			</span>

	<?php if ($uwa_silent != 'yes'){ ?>
		<div class="uwa_inc_price_hint" >		
		 <small class="uwa_inc_price">(<?php _e('Enter more than or equal to', 'woo_ua') ?> : </small>
		 <small class="uwa_inc_latest_price uwa_inc_price_ajax_<?php echo $product_id; ?>">
		 <?php echo wc_price($uwa_bid_value, $args);?> )</small>
		 <?php if($uwa_proxy == 'yes' ) { ?>	
				<br> <small class="uwa_inc_price">(<?php _e('This will set your max bid.', 'woo_ua') ?> ) </small> <Br><Br>
		<?php } ?>	 
		</div>	
	<?php } ?>	
		
		<?php } ?>
		
		<input type="hidden" name="bid" value="<?php echo esc_attr( $product_id ); ?>" />
		<input type="hidden" id="uwa_place_bid" name="uwa-place-bid" value="<?php echo $product_id; ?>" />
		<input type="hidden" name="product_id" value="<?php echo esc_attr( $product_id ); ?>" />
		<?php if ( is_user_logged_in() ) { ?>
			<input type="hidden" name="user_id" value="<?php echo  get_current_user_id(); ?>" />
		<?php  } ?> 
		
	<?php do_action('ultimate_woocommerce_auction_after_bid_button'); ?>
		
</form>
<?php }  ?>
<?php /* ==================== form2 - custom bid end =================== */ ?>


<?php 
/* ------  selling type - place bid start  ------ */

} /* end of if auction_selling_type  */

/* ------  selling type - place bid end  ------ */
	
?>


	
	<?php do_action('ultimate_woocommerce_auction_after_bid_form'); ?>


	</div>
<?php elseif (($uwa_expired === FALSE ) and ($uwa_started  === FALSE )):?>
  
		<p class="uwa_auction_product_schedule_time">
			<strong><?php _e('Auction has not been started yet.', 'woo_ua'); ?></strong>
		</p>

	<?php if(get_option('uwa_hide_start_on_field', 'no') == 'no'){ ?>
		<p class="uwa_auction_product_schedule_time">
		<strong><?php _e('Start On:', 'woo_ua'); ?></strong>		
			<?php echo  date_i18n( get_option( 'date_format' ),  strtotime( $uwa_starting_time ));  ?>  
			<?php echo  date_i18n( get_option( 'time_format' ),  strtotime( $uwa_starting_time ));  ?>				
		</p>
	<?php } ?>	
		<?php if(get_option('uwa_hide_timer_field', 'no') == 'no'){ 
		$starting_time  =  wp_date('Y-m-d H:i:s',$product->get_uwa_seconds_to_start_auction(),get_uwa_wp_timezone());
		$uwa_time_zone =  (array)wp_timezone();
		$sinceday  =  wp_date('M j, Y H:i:s O',time(),get_uwa_wp_timezone());
		?>
		<script>
			var servertime='<?php echo $sinceday;?>';
			</script>
		<div class="uwa_auction_product_time scheduled" id="uwa_auction_countdown">
			<strong>
				<?php _e('Starting Time Left:', 'woo_ua'); ?>
			</strong>
			<div class="main-auction-product uwa_auction_product_countdown scheduled" data-time="<?php echo $starting_time; ?>" data-auction-id="<?php echo esc_attr( $product_id ); ?>" 
			data-format="<?php echo get_option( 'woo_ua_auctions_countdown_format' ) ?>" data-timezonetype="<?php echo $uwa_time_zone['timezone_type']; ?>" data-zone="<?php echo $uwa_time_zone['timezone']; ?>"></div>
	    </div>
		<?php } ?>
	<?php if(get_option('uwa_hide_ending_on_field', 'no') == 'no'){ ?>		  
	<p class="uwa_auction_product_end_time">
			<strong><?php _e('Ending On:', 'woo_ua'); ?></strong>			
			<?php echo  date_i18n( get_option( 'date_format' ),  strtotime( $uwa_ending_time ));  ?>  
			<?php echo  date_i18n( get_option( 'time_format' ),  strtotime( $uwa_ending_time ));  ?>				
	</p>
	<?php } ?>
<?php endif; ?>
	<?php if ($product->get_uwa_auction_fail_reason() == '1'){ ?>
		
	<p class="expired">	<?php  _e('Auction Expired because there were no bids', 'woo_ua');?>  </p>
		 
	 <?php } elseif($product->get_uwa_auction_fail_reason() == '2'){ ?>
		
	<p class="reserve_not_met"> <?php	_e('Auction expired without reaching reserve price', 'woo_ua'); ?> </p>
		
	 <?php } ?>
</p>

</div>

<script type="text/javascript">

	jQuery("document").ready(function($){

		$("#placebidbutton_direct").on('click', function(event){

			var formname = "directbid";			
			retval = bid_check(formname);
			
			if(retval == true || retval == false){				
				return retval;
			}
		});
		
		$("#placebidbutton").on('click', function(event){
			
			var formname = "custombid";
			retval = bid_check(formname);
			
			if(retval == true || retval == false){				
				return retval;
			}
		});

		function bid_check(formname){			

			var id_Bid;

			if(formname == "custombid"){
				id_Bid = "#uwa_bid_value";
			}
			else if(formname == "directbid"){
				id_Bid = "#uwa_bid_value_direct";
			}

				var bidval = parseFloat($(id_Bid).val());

			  	if(bidval){		  		

			  		if(formname == "custombid"){

				  			var minval = parseFloat($(id_Bid).attr("min"));
							var maxval = parseFloat($(id_Bid).attr("max"));
					

							if(minval <= bidval){
								bid_process(formname, id_Bid);
							}
							else{					
								alert("<?php _e('Please enter bid value greater than suggested bid', 'woo_ua');?>");
								return false;
							}
					}
					else if(formname == "directbid"){
						bid_process(formname, id_Bid);						
					}

				} /* end of if - bidval */												
				else{
					alert("<?php _e('Please enter bid value', 'woo_ua');?>");
					return false;
				}

		} /* end of function */


		function bid_process(formname, id_Bid){
				
				<?php 

				$uwa_enable_bid_place_warning = 
					get_option('uwa_enable_bid_place_warning');
				$uwa_placebid_ajax_enable = 
					get_option('woo_ua_auctions_placebid_ajax_enable');

				if($uwa_placebid_ajax_enable == "no" || 
					$uwa_placebid_ajax_enable == ""){ /* using page load */
					if($uwa_enable_bid_place_warning  == "yes"){ ?>

						confirm_bid(formname, id_Bid);

					<?php
					}		
				}elseif($uwa_placebid_ajax_enable == "yes"){ /* using ajax */
					if($uwa_enable_bid_place_warning  == "yes"){ ?>

						var retval = confirm_bid(formname, id_Bid);
						if(retval == true){ 

							/* bid using ajax if confirm yes */
							placebid_ajax_process(formname);
						}
					<?php
					}
					else{?>
						/* bid using ajax */
						placebid_ajax_process(formname);
					<?php
					}
				}
				?>

		} /* end of function */


		function placebid_ajax_process(formname){

			if(formname == "custombid"){
				id_Bid = "#uwa_bid_value";
				id_h_Product = "#uwa_place_bid";
				id_Bid_Button = "#placebidbutton";
				class_ajax_Span = ".ajax-loader-placebid";
				class_ajax_Img	= ".loaderimg";
			}
			else if(formname == "directbid"){
				id_Bid = "#uwa_bid_value_direct";
				id_h_Product = "#uwa_place_bid_direct";
				id_Bid_Button = "#placebidbutton_direct";
				class_ajax_Span = ".ajax-loader-placebid_direct";
				class_ajax_Img	= ".loaderimg_direct";
			}

			var uwa_place_bid = $(id_h_Product).val();
			var uwa_bid_value = $(id_Bid).val();
			var uwa_url = "<?php echo admin_url('admin-ajax.php'); ?>";		
			
			$.ajax({
      			method : "post",  /* don't use 'type' */
				url : "<?php echo admin_url('admin-ajax.php'); ?>",				
				data : 	{action: "uwa_ajax_placed_bid",
						uwa_place_bid : uwa_place_bid,
						uwa_bid_value : uwa_bid_value
						},
				beforeSend: function(){	
					$(class_ajax_Span).css("display", "inline");
    				$(class_ajax_Img).css("visibility", "visible");
    				$('.product-type-auction').css("opacity", "0.7");
    				$("#placebidbutton").attr("disabled", "disabled");
    				$("#placebidbutton_direct").attr("disabled", "disabled");
    				$('.single_add_to_cart_button').attr("disabled", 
    					"disabled");
  				},
  				
				success: function(response) {
					
					var data = $.parseJSON( response );
					
					if(typeof data.allmsg != "undefined"){
						//$(".woocommerce-notices-wrapper").hide();
						//$(".woocommerce-notices-wrapper").html(data.allmsg).fadeIn(1000);
						
						$(".woocommerce-notices-wrapper").html(data.allmsg);
					}

					/* display fields data in detail page */
					//if(data.allstatus == 1){

						var auctionid = uwa_place_bid;
						var newprice = data.alldata_display.uwa_curent_bid;
						var newenterval = data.alldata_display.entervalue;
						var newwinusername = data.alldata_display.winusername;
						var newreservetext = data.alldata_display.reservetext;
						var newmaxmintext = data.alldata_display.maxmintext;
						var newuwabidsalldata = data.alldata_display.uwa_bids_alldata;
						var newbidminval = data.alldata_display.uwa_bid_minval;
						var newbidmaxval = data.alldata_display.uwa_bid_maxval;
						var newtimerval = data.alldata_display.remaining_secs;
						var auctiontype = data.alldata_display.auction_type;

						var newnextbids = data.alldata_display.next_bids;

						var newuwa_imgtext = data.alldata_display.uwa_imgtext;
						var newuwa_detailtext = data.alldata_display.uwa_detailtext;
						

						if(typeof newprice != "undefined"){
							$("p.price").html(newprice); /* + "--done"); */
						}
						
						if(typeof newenterval != 'undefined'){
							newenterval = newenterval + " )";
							/* uwa_inc_latest_price or uwa_inc_price_ajax_492 */
							$("small.uwa_inc_latest_price").html(newenterval);
						}

						if(typeof newreservetext != 'undefined'){
							//strong.uwa_auction_reserve_price							
							$("div.checkreserve").html(newreservetext);
						}

						if(typeof newwinusername != 'undefined'){

							$("div.winner-name").html(newwinusername);
						}

						if(typeof newmaxmintext != 'undefined'){
							$("p.max-bid").html(newmaxmintext);
						}

						if(typeof newtimerval != 'undefined'){
							
							//alert("inif --" + newtimerval);
							/* change value of data-time of timer */
							/*$("div.uwa_auction_product_countdown").data('time', newtimerval);*/
							/* set new time value in div */
							$("div.uwa_auction_product_countdown").attr(
								'data-time', newtimerval);

							time1 = newtimerval;
							//alert(time1);
							//alert(typeof time1);

							$("div.uwa_auction_product_countdown").WooUacountdown('destroy');

							/* here we add new time for timer, check uwa-front.js for all options of timer */

							$("div.uwa_auction_product_countdown").WooUacountdown({until:   $.WooUacountdown.UTCDate(-(new Date().getTimezoneOffset()), new Date(time1*1000))});
							
						}

						if(typeof newuwabidsalldata != 'undefined'){
								/* uwa-front.js */
							/*jQuery("#auction-history-table-" + key +" tbody > tr:first" ).before(value.wua_activity);*/

							/*($(".auction-history-table  tbody > tr:first").before(newuwabidsalldata + "--done");*/

							$("div.uwa_bids_history_data").html(newuwabidsalldata); /* + "--done");*/
						}

						if(typeof newbidminval != 'undefined'){

							/* note : change min value for both direct and custom bid  **** */

							$("#uwa_bid_value").attr("min", newbidminval);
							/*$("#uwa_bid_value_direct").attr("min", 
								newbidminval);*/


							/* set default value for direct bid */
							if(auctiontype == "normal"){
								//$("#uwa_bid_value_direct").val(newbidminval);
								//$("#uwa_bid_value_direct").html("<option value='1'>1</option><option value='2'>2</option>");
								//$("#uwa_bid_value_direct").html(newnextbids);
							}
						}

						if(typeof newnextbids != 'undefined'){

							if(auctiontype == "normal"){

								/* set options for direct bid */
								$("#uwa_bid_value_direct").html(newnextbids);

							}
							
						}

						if(typeof newbidmaxval != 'undefined'){

							/* note : change max value for both direct and custom bid  **** */

							$("#uwa_bid_value").attr("max", newbidmaxval);
							$("#uwa_bid_value_direct").attr("max", 
								newbidmaxval);
								
							/* set default value for direct bid */
							if(auctiontype == "reverse"){
								$("#uwa_bid_value_direct").val(newbidmaxval);
							}

						}
						
						if(typeof newuwa_imgtext != "undefined"){
							$("span.uwa_imgtext").html(newuwa_imgtext); 
						}
						else{							
							$("span.uwa_imgtext").html("");
						}

						if(typeof newuwa_detailtext != "undefined"){							
							$("p.uwa_detailtext").html(newuwa_detailtext);
						}
						else{							
							$("p.uwa_detailtext").html(""); 
						}


						$("#uwa_bid_value").val("");
						

					//}
					
				},
				error: function(){},

				complete: function(){
					$(class_ajax_Span).css("display", "none");
					$(class_ajax_Img).css("visibility", "hidden");
    				$('.product-type-auction').css("opacity", "1");	
    				$("#placebidbutton").removeAttr("disabled");
    				$("#placebidbutton_direct").removeAttr("disabled");
    				$('.single_add_to_cart_button').removeAttr("disabled");
    				

    				/* -------- slider ---------- */

    				var custom_add = 100;

    				<?php  if(get_template() == "Divi"){ ?> 					

    					custom_add = 500;

    				<?php } ?>


						$("html").animate({scrollTop: ($(".woocommerce-notices-wrapper").offset().top)-custom_add}, 1500);
						
						$(".woocommerce-notices-wrapper").hide(); 
						setTimeout(function(){
					       $(".woocommerce-notices-wrapper").fadeIn(2000);
					    }, 1000); 

    				/* -------- slider ---------- */				    


				},
			});

		} /* end of function */


			/* Extra confirmation message on place bid */
		function  confirm_bid(formname, id_Bid) {

			/* Get bid value, format value and then add to confirm message */
			var bidval = jQuery(id_Bid).val();
			var bidval = parseFloat(bidval);

			if (bidval > 0){
				
				var floatbidval = bidval.toFixed(2); /* 2 numbers after decimal point */
				/*var currencyval = "<?php echo html_entity_decode(get_woocommerce_currency_symbol()); ?>";*/

				/* bloginfo( 'charset' ); */

				var currencyval = "<?php echo html_entity_decode(get_woocommerce_currency_symbol($product_base_currency), ENT_COMPAT | ENT_HTML401, 
					'UTF-8'); ?>";

				var finalval = currencyval + floatbidval;


				if(formname == "custombid"){
					var confirm1 = '<?php echo addslashes(__( "Do you really want to bid", "woo_ua" )); ?>';
				}
				else if(formname == "directbid"){
					var confirm1 = '<?php echo addslashes(__( "Do you really want to directly place this bid", "woo_ua" )); ?>';
				}

				
				var confirm_message = confirm1 + ' ' + finalval + ' ?';

				var result_conf = confirm(confirm_message);

				if(result_conf == false){
					event.preventDefault(); /* don't use return it reloads page */
				}
				else{
					return true;
				}
			}
			
		} /* end of function - confirm_bid() */


	}); /* end of document ready */

</script>