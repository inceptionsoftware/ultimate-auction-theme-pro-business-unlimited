<?php

if ( ! defined( 'ABSPATH' ) ) {
    exit;
} 

add_filter( 'wcfm_query_vars', 'uwa_wcb_wcfm_query_vars', 20 );
//add_filter( 'wcfm_endpoint_title','uwa_wcb_wcfm_endpoint_title', 20, 2 );
add_action( 'init','uwa_wcb_wcfm_init', 20, 2 );
add_filter( 'wcfm_menus', 'uwa_wcb_wcfm_menus', 20 );
add_filter('wcfm_product_types', 'uwa_vendor_support_add_product_type', 20 );
add_filter( 'wcfm_capability_settings_fields_product_types', 'uwa_vendor_support_auction_tab',20, 3);
add_action( 'wcfm_load_views', 'uwa_wcb_load_views' , 30 );

/* Register globally scripts */
add_action( 'wp_footer',  'uwa_vendor_support_add_scripts');
//add_action( 'wcfm_load_scripts',  'uwa_vendor_support_add_scripts');
//add_action( 'after_wcfm_load_scripts',  'uwa_vendor_support_add_scripts');

function uwa_vendor_support_add_scripts(  ){
	global $WCFM;
	$WCFM->library->load_datatable_lib();
    $WCFM->library->load_daterangepicker_lib();
	wp_register_script( 'wcfm_uwa_auctions_date_picker', plugin_dir_url( __FILE__ ). 'js/date-picker.js', 
		array('jquery'), '1.0.1' );	
	wp_enqueue_script( 'wcfm_uwa_auctions_date_picker' );

	wp_register_script( 'wcfm_uwa_auctions', plugin_dir_url( __FILE__ ). 'js/wcfm_uwa_auctions.js', 
		array('jquery'), '1.0.1' );
	wp_enqueue_script( 'wcfm_uwa_auctions' );
	
	wp_register_script( 'wcfm_uwa_auctions_list', plugin_dir_url( __FILE__ ). 'js/wcfm_uwa_auctions_list.js', 
		array('jquery', 'dataTables_js'), '1.0.1' );	

	wp_localize_script( 'wcfm_uwa_auctions_list', 'uwa_wcfm_params',
            array( 'ajax_url' => admin_url( 'admin-ajax.php' ) ) );
		
	wp_enqueue_script( 'wcfm_uwa_auctions_list' );

	wp_register_script( 'wcfm_uwa_auctions_detail', plugin_dir_url( __FILE__ ). 'js/wcfm_uwa_auctions_detail.js', 
		array('jquery'), '1.0.1' );	

	wp_localize_script( 'wcfm_uwa_auctions_detail', 'uwa_wcfm_params',
            array( 'ajax_url' => admin_url( 'admin-ajax.php' ) ) );
		
	wp_enqueue_script( 'wcfm_uwa_auctions_detail' );

}

// Bookings Load WCFMu Styles
	add_action( 'wcfm_load_styles',  'wcb_uwa_load_styles' , 30 );
	add_action( 'after_wcfm_load_styles',  'wcb_uwa_load_styles' , 30 );

	 function wcb_uwa_load_styles( $end_point ) {
	  global $WCFM, $WCFMu;
		
	  switch( $end_point ) {
	  	
	    case 'uwa-auctionslist':
	    	wp_enqueue_style( 'wcfm_uwa_auctions_css',  plugin_dir_url( __FILE__ ). 'css/wcfm_uwa_auctions.css', array(), '1.0.1' );
		  break;
		case 'uwa-auction-detail':
	    	wp_enqueue_style( 'wcfm_uwa_auctions_css',  plugin_dir_url( __FILE__ ). 'css/wcfm_uwa_auctions.css', array(), '1.0.1' );
		  break; 
	  }
	}


/* Register Auction Product Type */

function uwa_vendor_support_add_product_type($product_types){
	$product_types['auction'] =  __('Auction Product', 'woo_ua');
	return $product_types;	
}
/* Add Auction Product Tab */
add_filter('wcfm_product_type_default_tab', 'uwa_vendor_support_default_tab');
function uwa_vendor_support_default_tab($product_tabs){
	$product_tabs['auction'] =  "wcfm_products_manage_form_auction_options_head";
	return $product_tabs;	
}
/* Add Auction Product In Capability Setting */

function uwa_vendor_support_auction_tab( $product_types, $handler = 'wcfm_capability_options', 
	$wcfm_capability_options = array() ) {
		global $WCFM;
		$uwa_auction = ( isset( $wcfm_capability_options['auction'] ) ) ? $wcfm_capability_options['auction'] : 'no';
		$product_types["auction"] =  array(
				'label' => __('Auction Product', 'woo_ua'), 
				'name' => $handler . '[auction]',
				'type' => 'checkboxoffon', 
				'class' => 'wcfm-checkbox wcfm_ele', 
				'value' => 'yes', 
				'label_class' => 'wcfm_title checkbox_title', 
				'dfvalue' => $uwa_auction
		);		
		return $product_types;
}

/*Auction Ajax Controllers */
add_action( 'after_wcfm_ajax_controller', 'uwa_vendor_support_after_ajax_controller');
function uwa_vendor_support_after_ajax_controller(){
	/* code Here  */
}
/*Auction General Block */
add_action( 'after_wcfm_products_manage_general', 
		'uwa_vendor_support_after_products_manage_general' );
function uwa_vendor_support_after_products_manage_general(){
	/* code Here  */	
}
/*Auction Product Manage View */
add_action( 'end_wcfm_products_manage', 'uwa_vendor_support_end_products_manage', 20);

function uwa_vendor_support_end_products_manage($product_id){
	
		global $WCFM, $woocommerce;		
		$product = wc_get_product($product_id);	
		$auction_product = new WC_Product_Auction( $product_id );		
		 
		$uwa_opening_price = $auction_product->get_uwa_auction_start_price();
		$uwa_bid_increment = $auction_product->get_uwa_auction_bid_increment();

			$default_bid_inc = 1;
			$get_inc_val = $auction_product->get_uwa_auction_bid_increment();
			if($get_inc_val >= 0.1){       // if($get_inc_val >= 1){
				$bid_inc_val = $get_inc_val;
			}
			else{
				$bid_inc_val = $default_bid_inc;	
			}

		$uwa_reserved_price = $auction_product->get_uwa_auction_reserved_price();
		$uwa_buynow_price = $auction_product->get_regular_price();
		$uwa_start_date = $auction_product->get_uwa_auction_start_dates();		
		$nowdate_for_start = $uwa_start_date ?  : get_uwa_now_date(); 
		$uwa_end_date = $auction_product->get_uwa_auction_end_dates();
		$end_date = $uwa_end_date ?  :  wp_date('Y-m-d H:i:s',strtotime('+1 day', time()),get_uwa_wp_timezone());
		$uwa_auction_type_value = $auction_product->get_uwa_auction_type();
		$uwa_auction_types = array('normal' => __('Normal', 'woo_ua'),'reverse' => __('Reverse', 'woo_ua'));		
		$uwa_auction_item_condition_value = $auction_product->get_uwa_auction_item_condition();		
		$uwa_auction_item_condition = array('new' =>__('New', 'woo_ua'),'used' =>__('Used','woo_ua'));		
		
		$uwa_auction_proxy = $auction_product->get_uwa_auction_proxy();					
		$proxy_value =  in_array($uwa_auction_proxy,array( '0', 'yes')) ? $uwa_auction_proxy : get_option('uwa_proxy_bid_enable', 'no');		
		$uwa_auction_silent = $auction_product->get_uwa_auction_silent();					
		$silent_value =  in_array($uwa_auction_silent,array( '0', 'yes')) ? $uwa_auction_silent : get_option('uwa_silent_bid_enable', 'no');
		/* GET auction selling type */					
		$auction_checked = "";
		$buyitnow_checked = "";										
		$selling_type = $auction_product->get_uwa_auction_selling_type();
		if($selling_type == "auction"){
			$auction_checked = "checked";
		}
		elseif($selling_type == "buyitnow"){
			$buyitnow_checked = "checked";
		}
		elseif($selling_type == "both"){
			$auction_checked = "checked";
			$buyitnow_checked = "checked";
		}					
		else{ 
			$auction_checked = "checked";
			$buyitnow_checked = "checked";
		}	
		/* Variable Bid Increment */		
		$var_bid_inc_value =  get_post_meta( $product_id, 'uwa_auction_variable_bid_increment', true);
		$var_bid_inc_value_checked = "";
		if($var_bid_inc_value == "yes"){
			$var_bid_inc_value_checked = "checked";
		}
 	?>

  <!-- collapsible 8 - Auction Product -->
	<div class="page_collapsible products_manage_linked auction" id="wcfm_products_manage_form_auction_options_head">

		<label class="wcfmfa fa-gavel"></label>
			<?php _e('Auction Data', 'woo_ua'); ?>
		<span></span>
       
	</div>

	<div class="wcfm-container auction">
      <style>
	  input.wcfm-checkbox.uwa_wcfm_checkbox{ margin-right: 5px !important;}
	
p.uwa_variable_bid_increment_main {display:none}	
.uwa_custom_field_onwards_main {display:none}	
.uwa_custom_field_main{	float: left;padding-top:1px; max-width: 508px;}
.uwa_custom_field_onwards_main{	float: left;padding-top:1px;}
.uwa_auction_price_fields{width:30%!important;}
	  
      </style>
		<div id="wcfm_products_manage_form_auction_options_expander" class="wcfm-content">
			
			
			    <p class="uwa_auction_selling_type wcfm_title">
			    <strong><?php _e('Selling Type', 'woo_ua'); ?></strong></p>
		  									
				<input class="wcfm-checkbox uwa_wcfm_checkbox" type="checkbox" id="uwa_auction_selling_type_auction" name="uwa_auction_selling_type_auction"	
									<?php echo $auction_checked; ?> /> <?php _e('Auction', 'woo_ua'); ?> 										  
									
								<span style="margin-right:25px"> </span>  
				<input  class="wcfm-checkbox uwa_wcfm_checkbox" type="checkbox" id="uwa_auction_selling_type_buyitnow" name="uwa_auction_selling_type_buyitnow"	
									<?php echo $buyitnow_checked; ?> />  <?php _e('Buy it now', 'woo_ua'); ?>
					
			
			<?php
				
				$WCFM->wcfm_fields->wcfm_generate_form_field( 
					apply_filters( 'wcfm_product_manage_fields_auction_options', 
						array(  	
														
							/*  no 1 */
							"woo_ua_auction_type" => array(
								'label' => __('Auction type', 'woo_ua'),
								'type' => 'select',
								'options' => $uwa_auction_types,
								'value' => $uwa_auction_type_value,
								'class' => 'wcfm-select', 
								'label_class' => 'wcfm_title', 
							),

							/*  no 2 */
							"woo_ua_product_condition" => array(
								'label' => __('Product Condition', 'woo_ua'),
								'type' => 'select',
								'options' => apply_filters(
									'ultimate_woocommerce_auction_product_condition',
										$uwa_auction_item_condition),
								'class' => 'wcfm-select',
								'value' => $uwa_auction_item_condition_value,									
								'label_class' => 'wcfm_title', 
							),

							/*  no 3 */
							"uwa_auction_proxy" => array(
								'label' => __('Enable proxy bidding', 'woo_ua'),
								'type' => 'checkbox',
								'value' => 'yes', 
								'dfvalue' => $proxy_value,
								'label_class' => 'wcfm_title', 								
								'class' => 'wcfm-checkbox',
								'hints' => __("Proxy Bidding (also known as Automatic Bidding) - Our automatic bidding system makes bidding convenient so you don't have to keep coming back to re-bid every time someone places another bid. When you place a bid, you enter the maximum amount you're willing to pay for the item. The seller and other bidders don't know your maximum bid. We'll place bids on your behalf using the automatic bid increment amount, which is based on the current high bid. We'll bid only as much as necessary to make sure that you remain the high bidder, or to meet the reserve price, up to your maximum amount.",'woo_ua'), 
							),

							/*  no 4 */
							"uwa_auction_silent" => array(
								'label' => __('Enable Silent-Bid', 'woo_ua'),
								'type' => 'checkbox',								
								'value' => 'yes', 
								'dfvalue' => $silent_value,								
								'label_class' => 'wcfm_title', 								
								'class' => 'wcfm-checkbox',
								'hints' => __("A Silent-Bid auction is a type of auction process in which all bidders simultaneously submit Silent bids to the auctioneer, so that no bidder knows how much the other auction participants have bid. The highest bidder is usually declared the winner of the bidding process.",'woo_ua'), 
							),

							/*  no 5 */
							"woo_ua_opening_price" => array(
								'label' => __( 'Opening Price', 'woo_ua' ). 
									' (' . get_woocommerce_currency_symbol() . ')',
								'type' => 'numeric',							
								'class' => 'wcfm-text wcfm_non_negative_input',
								'label_class' => 'wcfm_title', 
								'value' => $uwa_opening_price,
								'attributes' => array( 'step' => 'any', 'min' => '0',
										'data-required'=> 1, 
										'data-required_message' => 
											"Opening Price: This field is required."), 

								'hints' => __( 'Set the opening price for the auction', 'woo_ua' ),
							),
							
							/*  no 6 */
							"woo_ua_lowest_price" => array(
								'label' => __('Lowest Price to Accept', 'woo_ua') . 
									' (' . get_woocommerce_currency_symbol() . ')',
								'type' => 'numeric',
								'value' => $uwa_reserved_price,
								'label_class' => 'wcfm_title',
								'class' => 'wcfm-text wcfm_non_negative_input', 
								'attributes' => array( 'step' => 'any', 'min' => '0' ), 
								'hints' => __( 'Set Reserve price for your auction.', 'woo_ua' ),
							),

							

							/*  no 8 */
							"_regular_price" => array(
								'label' => __( 'Buy now price', 'woo_ua' ). 
									' (' . get_woocommerce_currency_symbol() . ')',
								'type' => 'numeric',
								'value' => $uwa_buynow_price,
								'label_class' => 'wcfm_title',
								'class' => 'wcfm-text wcfm_non_negative_input', 
								'attributes' => array( 'step' => 'any', 'min' => '0' ), 
								'hints' => __( 'Visitors can buy your auction by making payments via Available payment method.', 'woo_ua' ),
							),	

							/*  no 9 */	
							"woo_ua_auction_start_date" => array(
								'label' => __( 'Start Date', 'woo_ua' ),
								'type' => 'text',  // date, datepicker
								//'class' => 'datetimepicker',
								'class' => 'wcfm-text',
								'value' => $nowdate_for_start,
								'label_class' => 'wcfm_title', 								
								'hints' => __( 'Set the Start date of Auction Product.', 'woo_ua' ),
							),

							/*  no 10 */
							"woo_ua_auction_end_date" => array(
								'label' => __( 'Ending Date', 'woo_ua' ),
								'type' => 'text', // date, datepicker
								//'class' => 'datetimepicker',
								'class' => 'wcfm-text',
								'value' => $end_date,
								'label_class' => 'wcfm_title', 								
								'hints' => __( 'Set the end date for the auction', 'woo_ua' ),
							),
							/*  no 7 */
							"woo_ua_bid_increment" => array(
								'label' => __( 'Bid Increment', 'woo_ua' ) . 
									' (' . get_woocommerce_currency_symbol() . ')',
								'type' => 'numeric',
								'value' => $bid_inc_val,  // $uwa_bid_increment,
								'label_class' => 'wcfm_title', 
								'class' => 'wcfm-text wcfm_non_negative_input',
								'attributes' => array( 'step' => 'any', 'min' => '0' ), 
								'hints' => __( 'Set an amount from which next bid should start.', 
									'woo_ua' ),
							),
																													
						), $product_id,$auction_product) 
				);


			?>
		
		
			<p class="uwa_auction_variable_bid_increment wcfm_title">
				<strong><?php _e('Variable Bid Increment', 'woo_ua'); ?></strong>
				</p>
				<label class="screen-reader-text" for="uwa_auction_variable_bid_increment"><?php _e('Variable Bid Increment', 'woo_ua'); ?></label>
				<input type="checkbox" id="uwa_auction_variable_bid_increment" name="uwa_auction_variable_bid_increment" class="wcfm-checkbox"
			     <?php echo $var_bid_inc_value_checked; ?>>
				 
				<p class="uwa_variable_bid_increment_main">		
								
						
						<span id="uwa_custom_field_add_remove"> 
							<!-- Don't 	remove -->


							<input type="button" id="plus_field" class="button button-secondary" value="Add New" />

						<?php 
						
						    $uwa_var_inc_data = get_post_meta( $product_id, 'uwa_var_inc_price_val', true );
							//$uwa_var_inc_data_count = count($uwa_var_inc_data); 
							$i=1;
							if ( !empty($uwa_var_inc_data)){
								foreach($uwa_var_inc_data as $key => $variable_val){
									
									if($key !== 'onwards' ){ ?>											
									<span id="uwa_custom_field_<?php echo $i; ?>" class="uwa_custom_field_main">
										<input type="number" min="1" class="uwa_auction_price_fields start_valid" id="start_val_<?php echo $i; ?>" data-startid="<?php echo $i; ?>" name="uwa_var_inc_val[<?php echo $i; ?>][start]" value="<?php echo $variable_val['start']; ?>" placeholder="<?php _e('Start Price', 'woo_ua'); ?>"/>
										<input type="number" min="1" class="uwa_auction_price_fields end_valid" id="end_val_<?php echo $i; ?>" data-endid="<?php echo $i; ?>"  name="uwa_var_inc_val[<?php echo $i; ?>][end]" value="<?php echo $variable_val['end']; ?>" placeholder="<?php _e('End Price', 'woo_ua'); ?>"/>
										<input type="number" min="1" class="uwa_auction_price_fields" id="inc_val_<?php echo $i; ?>" name="uwa_var_inc_val[<?php echo $i; ?>][inc_val]" value="<?php echo $variable_val['inc_val']; ?>" placeholder="<?php _e('Increment', 'woo_ua'); ?>"/>
										<?php
							              if($i!=1){ ?>
										<input type="button" class="button button-secondary minus_field" value="-" data-custom="<?php echo $i; ?>" />
										<?php } ?>
										
									</span>	
									<?php }	
									$i++;
								}
							} else { ?>
							<span id="uwa_custom_field_0" class="uwa_custom_field_main">
								<input type="number" min="1" class="uwa_auction_price_fields start_valid" id="start_val_0" data-startid="0" name="uwa_var_inc_val[0][start]" value="" placeholder="<?php _e('Start Price', 'woo_ua'); ?>"/>
								<input type="number" min="1" class="uwa_auction_price_fields end_valid" id="end_val_0" data-endid="0"  name="uwa_var_inc_val[0][end]" value="" placeholder="<?php _e('End Price', 'woo_ua'); ?>"/>
								<input type="number" min="1" class="uwa_auction_price_fields" id="inc_val_0" name="uwa_var_inc_val[0][inc_val]" value="" placeholder="<?php _e('Increment', 'woo_ua'); ?>"/>
							</span>
							<?php } ?>


					<?php if(!empty($uwa_var_inc_data) && $uwa_var_inc_data['onwards']['end'] == 'onwards' ){ ?>
							    <div id="uwa_custom_field_onwards" class="uwa_custom_field_onwards_main">
								<input type="number" min="1" class="uwa_auction_price_fields start_valid" id="start_val_onwards" name="uwa_var_inc_val[onwards][start]" value="<?php echo $uwa_var_inc_data['onwards']['start']; ?>" placeholder="<?php _e('Start', 'woo_ua'); ?>"/>
								<input type="text" class="uwa_auction_price_fields end_valid" id="end_val_onwards" name="uwa_var_inc_val[onwards][end]"
								value="onwards" placeholder="<?php _e('onwards', 'woo_ua'); ?>" readonly />
								<input type="number" min="1" class="uwa_auction_price_fields" id="inc_val_onwards" name="uwa_var_inc_val[onwards][inc_val]" value="<?php echo $uwa_var_inc_data['onwards']['inc_val']; ?>" placeholder="<?php _e('Increment', 'woo_ua'); ?>"/></div>
					<?php }  else { ?>					
						        <div id="uwa_custom_field_onwards" class="uwa_custom_field_onwards_main">
								<input type="number" min="1" class="uwa_auction_price_fields start_valid" id="start_val_onwards" name="uwa_var_inc_val[onwards][start]" value="" placeholder="<?php _e('Start Price', 'woo_ua'); ?>"/>
								<input type="text" class="uwa_auction_price_fields end_valid" id="end_val_onwards" name="uwa_var_inc_val[onwards][end]" value="onwards" placeholder="<?php _e('onwards', 'woo_ua'); ?>" readonly />
								<input type="number" min="1" class="uwa_auction_price_fields" id="inc_val_onwards" name="uwa_var_inc_val[onwards][inc_val]" value="" placeholder="<?php _e('Increment', 'woo_ua'); ?>"/></div>
						<?php } ?>					


						</span>
						<script type="text/javascript">
							<?php if($var_bid_inc_value=="yes"){ ?>								
								jQuery('p.uwa_variable_bid_increment_main').css("display", "block"); 
								jQuery('.uwa_custom_field_onwards_main').css("display", "block");			
								jQuery('.woo_ua_bid_increment').css("display", "none");
								jQuery('#woo_ua_bid_increment').css("display", "none");
								jQuery('#woo_ua_bid_increment').val("");
								
							<?php } ?>
					var flag=<?php echo $i;?>;

					var arr=[];

					jQuery('#plus_field').click(function(){

						jQuery('#uwa_custom_field_add_remove').append('<span id="uwa_custom_field_'+flag+'" class="uwa_custom_field_main"><input type="number" min="1"  class="uwa_auction_price_fields start_valid" id="start_val_'+flag+'" data-startid="'+flag+'" name="uwa_var_inc_val['+flag+'][start]" value="" placeholder="Start Price" /><input type="number" min="1" class=" uwa_auction_price_fields end_valid" id="end_val_'+flag+'" data-endid="'+flag+'" name="uwa_var_inc_val['+flag+'][end]" value="" placeholder="End Price" /><input type="number" min="1" class=" uwa_auction_price_fields" id="inc_val_'+flag+'" name="uwa_var_inc_val['+flag+'][inc_val]" value="" placeholder="Increment" /><input type="button" class="button button-secondary minus_field" value="-" data-custom="'+flag+'"></span>');
						var end_range_valid = (parseInt(flag) - 1);
						var end_range_val = jQuery("#end_val_"+end_range_valid).val();
						jQuery('#start_val_'+flag).val(end_range_val);
						flag++;

						/* check height is in px or not */
						var height_val = jQuery(".wcfm-tabWrap").height();
						var added_height = height_val + 50;
						var final_height = added_height + "px";
						jQuery(".wcfm-tabWrap").css("height", final_height);

					});

					jQuery(document).on('click', '.minus_field', function(){
						var id=jQuery(this).attr('data-custom');
						var id_name="uwa_custom_field_"+id+"";
						jQuery('#'+id_name+'').remove();
						flag--;

						/* check height is in px or not */
						var height_val = jQuery(".wcfm-tabWrap").height();
						var minus_height = height_val - 50;
						var final_height = minus_height + "px";
						jQuery(".wcfm-tabWrap").css("height", final_height);

					});

					jQuery(document).on('keyup', '.end_valid', function(){
						var end_range = jQuery(this).attr('data-endid');
						var end_range_val = jQuery(this).val();
						var end_range_valid = (parseInt(end_range) + 1);
						jQuery('#start_val_'+end_range_valid).val(end_range_val);
					});
					
				</script></p>
			<p class="uwa_admin_current_time">
				<br><br>
				<?php
					printf(__('Current Blog Time is %s', 'woo_ua'), '<strong>'.get_uwa_now_date().'</strong> ');
					echo __('Timezone:', 'woo_ua').' <strong>'.wp_timezone_string().'</strong>';
					//echo __('<a href="'.admin_url('options-general.php?#timezone_string').'" target="_blank">'.' '.__('Change', 'woo_ua').'</a>');?>								
			</p>
		
		</div>
		
	</div>
  <!-- end collapsible -->
  <div class="wcfm_clearfix"></div>
  

		<?php	
}


add_filter('wcfm_product_manage_fields_auction_options', 'uwa_vendor_support_manage_fields', 20, 3);


function uwa_vendor_support_manage_fields($fields, $product_id){

	if(get_option('uwa_proxy_bid_enable', 'no') != 'yes'){
		/* if not enabled then unset it  or display none */
		unset($fields['uwa_auction_proxy']);
	}
	if(get_option('uwa_silent_bid_enable', 'no') != 'yes'){
		/* if not enabled then unset it */				
		unset($fields['uwa_auction_silent']);		
	}	
	
	if(get_option('uwa_hide_product_condition_field') == 'yes'){
		/* if not enabled then unset it */				
		unset($fields['woo_ua_product_condition']);		
	}
	return $fields;
}

add_action( 'after_wcfm_products_manage_meta_save', 
	'uwa_vendor_support_products_manage_meta_save', 200, 2);

function uwa_vendor_support_products_manage_meta_save($new_product_id, 
	$wcfm_products_manage_form_data ){

	global $wpdb, $WCFM, $_POST;
	
	if( $wcfm_products_manage_form_data['product_type'] == 'auction' ) {
		
		$_regular_price = $wcfm_products_manage_form_data['_regular_price'];
		$woo_ua_auction_type = $wcfm_products_manage_form_data['woo_ua_auction_type'];
		$woo_ua_product_condition = $wcfm_products_manage_form_data[
			'woo_ua_product_condition'];
		$woo_ua_opening_price = $wcfm_products_manage_form_data['woo_ua_opening_price'];
		$woo_ua_lowest_price = $wcfm_products_manage_form_data['woo_ua_lowest_price'];
		$woo_ua_bid_increment = $wcfm_products_manage_form_data['woo_ua_bid_increment'];		
		$woo_ua_auction_start_date = $wcfm_products_manage_form_data[
			'woo_ua_auction_start_date'];
		$woo_ua_auction_end_date = $wcfm_products_manage_form_data['woo_ua_auction_end_date'];
		$uwa_auction_proxy = $wcfm_products_manage_form_data['uwa_auction_proxy'];
		$uwa_auction_silent = $wcfm_products_manage_form_data['uwa_auction_silent'];


		/* Save data to database */
		$post_id = $new_product_id;
		update_post_meta($post_id, '_manage_stock', 'yes');
		update_post_meta($post_id, '_stock', '1');
		update_post_meta($post_id, '_backorders', 'no');
		update_post_meta($post_id, '_sold_individually', 'yes');

		if (isset($_regular_price)) {
				update_post_meta($post_id, '_regular_price', wc_format_decimal(wc_clean($_regular_price)));
				update_post_meta($post_id, '_price', wc_format_decimal(wc_clean($_regular_price)));
		}

		if( isset( $woo_ua_product_condition)) {
				update_post_meta( $post_id, 'woo_ua_product_condition', esc_attr( $woo_ua_product_condition ) );
		}		
		if (isset($woo_ua_opening_price)) {			
			update_post_meta( $post_id, 'woo_ua_opening_price', wc_format_decimal(wc_clean($woo_ua_opening_price)) );
		}
		if( isset( $woo_ua_lowest_price ) )  {				
			update_post_meta( $post_id, 'woo_ua_lowest_price', wc_format_decimal(wc_clean($woo_ua_lowest_price)) );
		}
		if(isset( $uwa_auction_proxy )){
			update_post_meta( $post_id, 'uwa_auction_proxy', stripslashes( $uwa_auction_proxy ) );
		} else {
			update_post_meta( $post_id, 'uwa_auction_proxy', '0' );
		}
		
		if(isset($uwa_auction_silent)){
			update_post_meta( $post_id, 'uwa_auction_silent', stripslashes( $uwa_auction_silent ) );
		} else {
			update_post_meta( $post_id, 'uwa_auction_silent', '0' );
		}
		if( isset( $woo_ua_bid_increment)  ) {			
			update_post_meta( $post_id, 'woo_ua_bid_increment', wc_format_decimal(wc_clean( $woo_ua_bid_increment )) );
			delete_post_meta( $post_id, 'uwa_auction_variable_bid_increment' );		
			delete_post_meta( $post_id, 'uwa_var_inc_price_val' );
		}
		if( isset( $woo_ua_auction_type ) ) {						
				update_post_meta( $post_id, 'woo_ua_auction_type', esc_attr( $woo_ua_auction_type ) );
		}		
		if( isset( $woo_ua_auction_start_date ) )  {				
			update_post_meta( $post_id, 'woo_ua_auction_start_date', $woo_ua_auction_start_date);
		}
		
		if( isset( $woo_ua_auction_end_date ) )  {				
			update_post_meta( $post_id, 'woo_ua_auction_end_date', $woo_ua_auction_end_date);
		}
		
		    /* Selling type */
			/* Note : html static so checkbox checked == on or (blank) */ 
			if(isset($wcfm_products_manage_form_data['uwa_auction_selling_type_auction']) && isset($wcfm_products_manage_form_data['uwa_auction_selling_type_buyitnow'])) {
				if($wcfm_products_manage_form_data['uwa_auction_selling_type_auction'] == "on" && $wcfm_products_manage_form_data['uwa_auction_selling_type_buyitnow'] == "on" ){
					update_post_meta( $post_id, 'woo_ua_auction_selling_type', "both"  );
				}
			}
			else if(isset($wcfm_products_manage_form_data['uwa_auction_selling_type_auction'])) {
				if($wcfm_products_manage_form_data['uwa_auction_selling_type_auction'] == "on"){
					update_post_meta( $post_id, 'woo_ua_auction_selling_type', "auction"  );					
				}				
			}
			else if(isset($wcfm_products_manage_form_data['uwa_auction_selling_type_buyitnow'])) {
				if($wcfm_products_manage_form_data['uwa_auction_selling_type_buyitnow'] == "on"){
					update_post_meta( $post_id, 'woo_ua_auction_selling_type', "buyitnow"  );					
				}				
			}
	
	
	
	
		 /* Variable Increment */
		if (isset($wcfm_products_manage_form_data['uwa_auction_variable_bid_increment']) && isset($wcfm_products_manage_form_data['uwa_var_inc_val'])){
				if($wcfm_products_manage_form_data['uwa_auction_variable_bid_increment']=="on" && !empty($wcfm_products_manage_form_data['uwa_var_inc_val']) && empty($wcfm_products_manage_form_data['woo_ua_bid_increment'])){
				
					update_post_meta($post_id, 'uwa_auction_variable_bid_increment', "yes");
					update_post_meta($post_id, 'uwa_var_inc_price_val', $wcfm_products_manage_form_data['uwa_var_inc_val']);
					delete_post_meta( $post_id, 'woo_ua_bid_increment' );
				}
			}
			
		
	} /* end of if */

	
} /* end of function  */

add_action( 'end_wcfm_products_manage', 'uwa_wcb_wcfm_products_manage_form_load_views', 20 );

  function uwa_wcb_wcfm_products_manage_form_load_views (){
	  global $WCFM;
  }
  
 

 function uwa_wcb_wcfm_menus (  $menus ){
		global $WCFM;
		if( WCFM_Dependencies::wcfmu_plugin_active_check() ) {
			$menus = array_slice($menus, 0, 3, true) +
			array( 'wcfm-uwa-auctionslist' => array(   'label'  => __( 'Auctions', 'woo_ua'),
			'url'       => get_wcfm_uwa_auction_url(),
			'icon'      => 'fa-gavel',
			'priority'  => 15
			) )	 +
			array_slice($menus, 3, count($menus) - 3, true) ;
		} else {
			$menus = array_slice($menus, 0, 3, true) +
			array( 'wcfm-uwa-auctionslist' => array(   'label'  => __( 'Auctions', 'woo_ua'),
			'url'       => get_wcfm_uwa_auction_url(),
			'icon'      => 'fa-gavel',
			'priority'  => 15
			) )	 +
			array_slice($menus, 3, count($menus) - 3, true) ;
		}
	
  	return $menus;
  }

if(!function_exists('get_wcfm_auction_dashboard_url')) {
	function get_wcfm_auction_dashboard_url( $auction_status = '' ) {
		global $WCFM;
		$wcfm_page = get_wcfm_page();
		$wcfm_auction_dashboard_url = wcfm_get_endpoint_url( 'wcfm_auction_dashboard', '', $wcfm_page );
		return apply_filters( 'wcfm_auction_dashboard_url', $wcfm_auction_dashboard_url );
	}
}

if(!function_exists('get_wcfm_uwa_auction_url')) {
	function get_wcfm_uwa_auction_url( $auction_status = '') {
		global $WCFM;
		$wcfm_page = get_wcfm_page();		
		$wcfm_auctions_url = wcfm_get_endpoint_url( 'uwa-auctionslist', '', $wcfm_page );
		if( $auction_status ) $wcfm_auctions_url = add_query_arg( 'auction_status', $auction_status, $wcfm_auctions_url );
		return $wcfm_auctions_url;
	}
}

if(!function_exists('get_wcfm_view_auction_url')) {
	function get_wcfm_view_auction_url( $auction_id = '' ) {
		global $WCFM;
		$wcfm_page = get_wcfm_page();
		$wcfm_view_auction_url = wcfm_get_endpoint_url( 'uwa-auction-detail', $auction_id, $wcfm_page );
		return $wcfm_view_auction_url;
	}
}


function uwa_wcb_wcfm_query_vars( $query_vars ) {
	
	
	$query_vars['uwa-auctionslist'] = ! empty( $wcfm_modified_endpoints['uwa-auctionslist'] ) ? $wcfm_modified_endpoints['uwa-auctionslist'] : 'uwa-auctionslist';
	
	$query_vars['uwa-auction-detail'] = ! empty( $wcfm_modified_endpoints['uwa-auction-detail'] ) ? $wcfm_modified_endpoints['uwa-auction-detail'] : 'uwa-auction-detail';
		
		return $query_vars;
  }


  /**
   * WC Booking Endpoint Intialize
   */
  function uwa_wcb_wcfm_init() {
  	global $WCFM_Query;
	
		// Intialize WCFM End points
		$WCFM_Query->init_query_vars();
		$WCFM_Query->add_endpoints();
		
		if( !get_option( 'wcfm_updated_end_point_uwa_auctions' ) ) {
			// Flush rules after endpoint update
			flush_rewrite_rules();
			update_option( 'wcfm_updated_end_point_uwa_auctions', 1 );
		}
		
  }
  
 
   function uwa_wcb_load_views( $end_point ) {
	 global $WCFM, $WCFMu;
    
	  switch( $end_point ) {	  	    
	  	case 'uwa-auctionslist':
         include("wcfm-uwa-auctions-lists.php");		
      break;
      case 'uwa-auction-detail':
         include("wcfm-uwa-auctions-detail.php");		
      break;
	  }
	}
	
function wcfm_ajax_uwa_auction_callback(){
    global $wpdb,$woocommerce, $product, $post;    
	$datetimeformat = get_option('date_format').' '.get_option('time_format');	
	$curr_user_id = get_current_user_id();	
	
	$length = $_POST['length'];
	$offset = $_POST['start'];
	
	/* woo_ua_auction_bid_count	*/
		$meta_query = array(
						'relation' => 'AND',
							array(			     
								'key'  => 'woo_ua_auction_closed',
								'compare' => 'NOT EXISTS',
							),
							array(
								'key'     => 'woo_ua_auction_has_started',
								'value' => '1',
							)							
						);
		
		if (isset($_POST["auctions_status"]) && $_POST["auctions_status"]=='expired') {						
			$meta_query= array(
						'relation' => 'AND',
							array(			     
								'key' => 'woo_ua_auction_closed',
								'value' => array('1','2','3','4'),
								'compare' => 'IN',
							),							
						);
		}

		if (isset($_POST["auctions_status"]) && $_POST["auctions_status"]=='scheduled') {						
					
			$meta_query= array(						
							array(			     
								'key'     => 'woo_ua_auction_closed',
								'compare' => 'NOT EXISTS',
								),	
							array(
								'key'     => 'woo_ua_auction_started',
								'value' => '0',
							)	
						);						
		}
	
	
	
	
	$args = array(
			'post_type'	=> 'product',
			'post_status' => 'publish',
			'ignore_sticky_posts'	=> 1,
			'posts_per_page'   => $length,
			'offset'           => $offset,
			'orderby'          => 'date',
			'order'            => 'DESC',
			'meta_query' => array($meta_query),
			'tax_query' => array(array('taxonomy' => 'product_type' , 'field' => 'slug', 'terms' => 'auction')),
			'auction_arhive' => TRUE
		);
		
	if (current_user_can('administrator') or current_user_can('shop_manager')) {		 
		
	} else {
		$curr_user_id = get_current_user_id();
	    $args['author__in'] = $curr_user_id;
	}
		$auction_products_array = get_posts( $args );
		// Get Product Count
		$auction_count = 0;
		$filtered_auction_count = 0;		
		$auction_count = count($auction_products_array);
		// Get Filtered Post Count
		$args['posts_per_page'] = -1;
		$args['offset'] = 0;
		$wcfm_filterd_auction_array = get_posts( $args );
		$filtered_auction_count = count($wcfm_filterd_auction_array);
		
		
		
		$wcfm_uwa_auction_json = '';
		$wcfm_uwa_auction_json = '{
															"draw": ' . $_POST['draw'] . ',
															"recordsTotal": ' . $auction_count . ',
															"recordsFiltered": ' . $filtered_auction_count . ',
															"data": ';
		if(!empty($auction_products_array)) {
			
			$index = 0;
			$wcfm_uwa_auction_json_arr = array();
			foreach($auction_products_array as $auction_product) {			
				
				$product_data = new WC_Product_Auction( $auction_product->ID );				
				
				// Thumb				
				$wcfm_uwa_auction_json_arr[$index][] =  $product_data->get_image( 'thumbnail' );
				
				// Title 
				$auction_title = '<a href="'.get_permalink( $auction_product->ID ).'">'.get_the_title(  $auction_product->ID ).'</a>'; 	
				$wcfm_uwa_auction_json_arr[$index][] = $auction_title;
				
				//Start date 
				$starting_on_date = $product_data->get_uwa_auction_start_dates();
			    $wcfm_uwa_auction_json_arr[$index][] = mysql2date($datetimeformat,$starting_on_date);
				
				//End date 
				$ending_date = $product_data->get_uwa_auction_end_dates();
			    $wcfm_uwa_auction_json_arr[$index][] = mysql2date($datetimeformat,$ending_date);
				
				//Price date 
				$current_price = $product_data->get_price_html();
			    $wcfm_uwa_auction_json_arr[$index][] = $current_price;
				
				// View Link				
				$action_view_link = '<a class="wcfm-action-icon" target="_blank" href="'.get_permalink( $auction_product->ID ).'""><span class="wcfmfa fa-eye text_tip" data-tip="View" data-hasqtip="'.$auction_product->ID.'" aria-describedby="qtip-'.$auction_product->ID.'"></span></a><br>';
				
				$action_view_link .= '<a class="wcfm-action-icon" target="_blank" href="'.get_wcfm_edit_product_url( $auction_product->ID,$product_data ).'""><span class="wcfmfa fa-edit text_tip" data-tip="View" data-hasqtip="'.$auction_product->ID.'" aria-describedby="qtip-'.$auction_product->ID.'"></span></a><br>';
				
				
				$action_view_link .= '<a class="wcfm-action-icon" target="_blank" href="'.get_wcfm_view_auction_url( $auction_product->ID,$product_data ).'""><span class="wcfmfa fa-eye text_tip" data-tip="View Detail" data-hasqtip="'.$auction_product->ID.'" aria-describedby="qtip-'.$auction_product->ID.'"></span></a><br>';
				
				$wcfm_uwa_auction_json_arr[$index][] = $action_view_link;
				
				$index++;
			}//end for each
			
			
		} //end 
		if( !empty($wcfm_uwa_auction_json_arr) ) $wcfm_uwa_auction_json .= json_encode($wcfm_uwa_auction_json_arr);
		else $wcfm_uwa_auction_json .= '[]';
		$wcfm_uwa_auction_json .= '
													}';
													
		echo $wcfm_uwa_auction_json;
		die();
}

add_action('wp_ajax_wcfm_ajax_uwa_auction', 'wcfm_ajax_uwa_auction_callback');
add_action('wp_ajax_nopriv_wcfm_ajax_uwa_auction', 'wcfm_ajax_uwa_auction_callback');	



add_action('wp_ajax_wcfm_uwa_admin_force_uwa_relist_now', 'wcfm_uwa_admin_force_uwa_relist_now_callback');
add_action('wp_ajax_nopriv_wcfm_uwa_admin_force_uwa_relist_now', 'wcfm_uwa_admin_force_uwa_relist_now_callback');	
function wcfm_uwa_admin_force_uwa_relist_now_callback () {
		global $wpdb;
		if (!empty($_POST["auction_id"])) {	
			$auction_id = absint($_POST["auction_id"]);
			$uwa_relist_start_date = $_POST["start_date"];
			$uwa_relist_end_date = $_POST["end_date"];			
			$relisted = wcfm_uwa_manually_do_relist($auction_id, $uwa_relist_start_date, $uwa_relist_end_date);			
		 	$response['status'] = 1;
			$response['success_message'] = __('Auction Relisted successfully.','woo_ua');
			
		} else {
				$response['status'] = 0;
				$response['error_message'] = __('Try Again.','woo_ua');
		}	
		echo json_encode( $response );
		exit;		
	}		
	
function wcfm_uwa_manually_do_relist($auction_id, $uwa_relist_start_date, $uwa_relist_end_date) {
		
		global $wpdb;
		$uwa_relist_options = get_option('uwa_relist_options','uwa_relist_start_from_beg');
		
		$log_table = $wpdb->prefix . "woo_ua_auction_log";
		update_post_meta($auction_id, '_manage_stock', 'yes');
		update_post_meta($auction_id, '_stock', '1');
		update_post_meta($auction_id, '_stock_status', 'instock');
		update_post_meta($auction_id, '_backorders', 'no');
		update_post_meta($auction_id, '_sold_individually', 'yes');
		update_post_meta($auction_id, 'woo_ua_auction_start_date', 
			stripslashes($uwa_relist_start_date));
		update_post_meta($auction_id, 'woo_ua_auction_end_date', 
			stripslashes($uwa_relist_end_date));
		update_post_meta($auction_id, 'uwa_auction_relisted', current_time('mysql'));
		delete_post_meta($auction_id, 'woo_ua_auction_has_started');


		// do when addon is activated..
		$addons = uwa_enabled_addons();
		if(is_array($addons) && in_array('uwa_stripe_auto_debit_addon', $addons)){

			// backup autodebit values before deleting...

			$arr_debit['debit_amt'] = get_post_meta($auction_id, '_uwa_stripe_auto_debit_amt', true);
			$arr_debit['debit_bpm_amt'] = get_post_meta($auction_id, '_uwa_stripe_auto_debit_bpm_amt', true);
			$arr_debit['debit_total_amt'] = get_post_meta($auction_id, '_uwa_stripe_auto_debit_total_amt', true);
			$arr_debit['debit_status'] = get_post_meta($auction_id, '_uwa_stripe_auto_debit_status', true);
			$arr_debit['debit_date'] = get_post_meta($auction_id, '_uwa_stripe_auto_debit_date', true);
			$arr_debit['debit_userid'] = get_post_meta($auction_id, 'woo_ua_auction_current_bider', true);

			$arr_all_details = get_post_meta($auction_id, 'uwa_auto_debit_details', true);

			if(is_array($arr_all_details) && count($arr_all_details) > 0){
				
				array_push($arr_all_details, $arr_debit);
				update_post_meta($auction_id, 'uwa_auto_debit_details', $arr_all_details);
			}
			else{
				$arr_debit = array($arr_debit);
				update_post_meta($auction_id, 'uwa_auto_debit_details', $arr_debit);
			}

	 
			// auto debit fields
			delete_post_meta($auction_id, '_uwa_stripe_auto_debit_total_amt');
			delete_post_meta($auction_id, '_uwa_stripe_auto_debit_amt');
			delete_post_meta($auction_id, '_uwa_stripe_auto_debit_bpm_amt');
			delete_post_meta($auction_id, '_uwa_stripe_auto_debit_status');
			delete_post_meta($auction_id, '_uwa_stripe_auto_debit_date');
			delete_post_meta($auction_id, '_done_one_time_payment');
			delete_post_meta($auction_id, 'woo_ua_winner_request_sent_for_autodabit_payment');
			

		} /* end of addon */


		if($uwa_relist_options ==="uwa_relist_start_from_beg"){ 
			/* delete_post_meta($auction_id, 'woo_ua_auction_closed');*/
			/* delete_post_meta($auction_id, 'woo_ua_auction_fail_reason');*/
			delete_post_meta($auction_id, 'woo_ua_auction_bid_count');
			delete_post_meta($auction_id, 'woo_ua_auction_current_bider');
			delete_post_meta($auction_id, 'woo_ua_auction_current_bid');				
			delete_post_meta($auction_id, 'woo_ua_auction_max_bid');
			delete_post_meta($auction_id, 'woo_ua_auction_max_current_bider');
		
			$order_id = get_post_meta($auction_id, 'woo_ua_order_id', true);				
			if (!empty($order_id)) {
				$order = wc_get_order($order_id);
				$order->update_status('failed', __('Failed Relist', 'woo_ua'));
				delete_post_meta($auction_id, 'woo_ua_order_id');
			}
		}elseif($uwa_relist_options ==="uwa_relist_start_from_end"){			
			/* delete_post_meta($auction_id, 'woo_ua_auction_closed'); */
			/* delete_post_meta($auction_id, 'woo_ua_auction_fail_reason'); */
		}
		
		
		/* if auction is relisted then send mail to bidders and admin */
		if(metadata_exists('post', $auction_id, 'uwa_auction_relisted' )){			
			do_action( 'uwa_pro_auction_relist_email', $auction_id );
			/* now delete fail reason and auction closed meta keys..if it delete earlier then
			relist reasons could not get*/
			delete_post_meta($auction_id, 'woo_ua_auction_closed');
			delete_post_meta($auction_id, 'woo_ua_auction_fail_reason');
		}

	}	
	