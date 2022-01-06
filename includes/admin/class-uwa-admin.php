<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Admin Class
 *
 * Handles generic Admin functionality and AJAX requests.
 *
 * @package Ultimate WooCommerce Auction PRO
 * @author Nitesh Singh 
 * @since 1.0
 *
 */

class UWA_Admin {
	
	private static $instance;
	public $uwa_auction_item_condition;
	public $uwa_auction_types;

	/**
     * Returns the *Singleton* instance of this class.
     *
     * @return Singleton The *Singleton* instance.
     *
     */
    public static function get_instance() {
        if ( null === self::$instance ) {
            self::$instance = new self();
        }
        return self::$instance;
    }
	

	public function __construct() {
		global $woocommerce;

		/* Admin Menu Page init */
		add_action('admin_menu', array($this, 'uwa_admin_main_menu'));		

		/* Create Auction Product Tab */
		add_filter( 'woocommerce_product_data_tabs', array( $this, 'uwa_custom_product_tabs' ) );
		add_action( 'woocommerce_product_data_panels', array( $this, 'uwa_options_product_tab_content' ) );		
		/* Create new Product Type - Auction */
		add_filter( 'product_type_selector', array( $this, 'uwa_add_auction_product' ) );		

		/* Save Auction Product Data */
		add_action( 'woocommerce_process_product_meta_auction', array( $this, 'uwa_save_auction_option_field' )  );	
				
		/* Auction Product Metabox - Bid History */
		add_action( 'add_meta_boxes_product', array( $this, 'uwa_add_auction_metabox') );	
				
		/* Auction Product Condition */
		$this->uwa_auction_item_condition =  array('new' => __('New', 'woo_ua'), 'used' => __('Used', 'woo_ua'));
		
		/* Added In Pro */
		$this->uwa_auction_types =  array('normal' => __('Normal', 'woo_ua'), 'reverse' => __('Reverse', 'woo_ua'));
		
		/* Emails For Admin */
     	 add_filter('woocommerce_email_classes', array($this, 'uwa_register_email_classes'));
		 
		/* Emails for html/plain */
        add_filter( 'woocommerce_locate_core_template', array( $this, 'uwa_locate_core_template' ), 10, 3 );	
		
		/* Filter On Admin product List page For Auction Product Type */
		add_action('restrict_manage_posts', array($this, 'admin_uwa_filter_restrict_manage_posts'));		
		add_filter('parse_query', array($this, 'admin_uwa_filter'));
		
		/* processing auction  product  item with woocoomrce order. */
		add_action('woocommerce_order_status_processing', array($this, 'uwa_auction_payed'), 10, 1);
		add_action('woocommerce_order_status_completed', array($this, 'uwa_auction_payed'), 10, 1);
		add_action('woocommerce_order_status_cancelled', array($this, 'uwa_auction_order_canceled'), 
			10, 1);
		add_action('woocommerce_order_status_refunded', array($this, 'uwa_auction_order_canceled'), 
			10, 1);
		add_action('woocommerce_checkout_update_order_meta', array($this, 'uwa_auction_order'), 
			10, 2);
		
		/* Bid Cancel By Admin */
		add_action("wp_ajax_admin_cancel_bid", array($this, "wp_ajax_admin_cancel_bid"));
		
		/* End Auction by Admin */
		add_action("wp_ajax_uwa_admin_force_end_now", array($this, "uwa_admin_force_end_now_callback"));
		add_action("wp_ajax_uwa_admin_force_make_live_now", array($this, "uwa_admin_force_make_live_now_callback"));
		add_action("wp_ajax_uwa_admin_force_remind_to_pay", array($this, "uwa_admin_force_remind_to_pay_callback"));
		
		add_action("wp_ajax_uwa_admin_force_choose_winner", array($this, "uwa_admin_force_choose_winner_callback"));
		
		/* Delete Auction product Meta While duplicating Products */
		add_action("woocommerce_duplicate_product", array($this, "uwa_woocommerce_duplicate_product"));
		
		/* custom js */
		add_action( 'admin_footer', array( $this, 'uwa_auction_custom_js' ) );
		
		/* new auction status in admin side in product list page */		 
		add_filter( 'manage_edit-product_columns',array( $this, 'uwa_auctions_status_columns'), 20 );
		
		add_action( 'manage_product_posts_custom_column', array( $this, 'uwa_auctions_status_columns_status' ),10, 2  );
		
		add_action( 'admin_notices', array( $this,'uwa_manage_auction_page_admin_notice') );
		
		add_action('init', array( $this,'uwa_email_remind_to_pay_notification_fun'));	
		//add_action('init', array( $this,'uwa_email_auction_ending_soon_notification_fun'));
		
		add_action('show_user_profile', array( $this,'uwa_block_unblock_user_to_bid_profile_fields'));
		add_action('edit_user_profile', array( $this,'uwa_block_unblock_user_to_bid_profile_fields'));
		
		add_action('personal_options_update', array( $this,'uwa_block_unblock_user_to_bid_save_profile_fields'));	
		add_action('edit_user_profile_update', array( $this,'uwa_block_unblock_user_to_bid_save_profile_fields'));
		
		add_filter( 'manage_users_custom_column', array( $this,'uwa_block_unblock_user_modify_user_table_row'), 10, 3 );
		add_filter( 'manage_users_columns', array( $this,'uwa_block_unblock_modify_user_table'));
		
		add_filter( 'bulk_actions-users',         array( $this, 'uwa_bulk_action_block_unblock_users'));
		add_filter( 'handle_bulk_actions-users',  array( $this, 'uwa_handle_bulk_block_unblock_users'   ), 10, 3 );
		add_action( 'admin_notices',              array( $this, 'uwa_handle_bulk_block_unblock_users_notices'   )        );
			
		
		add_action( 'init', array( $this, 'uwa_automatic_renew_auction_fun' ) );

		/* Save Product Data */
		add_action( 'woocommerce_process_product_meta', array( $this, 'uwa_woocommerce_process_product_meta'), 1000);
		
	}
	
	public function uwa_bulk_action_block_unblock_users($bulk_actions) {
		$bulk_actions['uwa_block_users']  = __( 'Block For Bid',  'woo_ua' );
		$bulk_actions['uwa_unblock_users'] = __( 'Unblock For Bid', 'ja-disable_users' );
		return $bulk_actions;
	}
	
	public function uwa_handle_bulk_block_unblock_users($redirect_to, $doaction, $user_ids) {
		
		if ($doaction !== 'uwa_block_users' && $doaction !== 'uwa_unblock_users'){
			return $redirect_to;
		}
		
		if($doaction == 'uwa_block_users'){
			foreach ( $user_ids as $user_id ){
				update_user_meta( $user_id, 'uwa_block_user_status', "uwa_block_user_to_bid" );
			}
			
			$redirect_to = add_query_arg( 'uwa_block_users', count($user_ids), $redirect_to );
			$redirect_to = remove_query_arg( 'uwa_unblock_users', $redirect_to );
			
			
		}elseif($doaction == 'uwa_unblock_users') {
			
			foreach ( $user_ids as $user_id ){
				update_user_meta( $user_id, 'uwa_block_user_status', "uwa_unblock_user_to_bid" );
			}
			
			$redirect_to = add_query_arg( 'uwa_unblock_users',  count($user_ids), $redirect_to );
			$redirect_to = remove_query_arg( 'uwa_block_users', $redirect_to );
			
		}
		
		return $redirect_to;
	}
	
	
	public function uwa_handle_bulk_block_unblock_users_notices() {
		if (! empty( $_REQUEST['uwa_block_users'] ) ){
			$updated = intval( $_REQUEST['uwa_block_users'] );
			printf( '<div id="message" class="updated">' .
				_n( 'Blocked %s user.',
					'Blocked %s users.',
					$updated,
					'woo_ua'
				) . '</div>', $updated );
		}
		if (! empty( $_REQUEST['uwa_unblock_users'] ) ){
			$updated = intval( $_REQUEST['uwa_unblock_users'] );
			printf( '<div id="message" class="updated">' .
				_n( 'Unblocked %s user.',
					'Unblocked %s users.',
					$updated,
					'woo_ua'
				) . '</div>', $updated );
		}
	}

	
	/**
	 * Add Page In Admin Menu.
	 *
	 */
	public function uwa_admin_main_menu(){

		global $wp_version;
		if($wp_version >= '3.8')
			$menu_icon = UW_AUCTION_PRO_ASSETS_URL.'images/uwa_admin_menu_icon.png';
		else
			$menu_icon = UW_AUCTION_PRO_ASSETS_URL.'images/uwa_admin_menu_icon_black.png';	
				
		add_menu_page(__('Auctions', 'woo_ua'), __('Auctions', 'woo_ua'), 'manage_options', 'uwa_auctions_dashboard',  array($this, 'uwa_auctions_dashboard_page_handler'),$menu_icon, 57.77);			
		
		add_submenu_page('uwa_auctions_dashboard', __('License & Addon', 'woo_ua'), __('License & Addon', 'woo_ua'), 'manage_options', 'uwa_auctions_dashboard', array($this, 'uwa_auctions_dashboard_page_handler'));
			
		add_submenu_page('uwa_auctions_dashboard', __('Auctions', 'woo_ua'), __('Auctions', 'woo_ua'), 'manage_woocommerce', 'uwa_manage_auctions', array($this, 'uwa_manage_auctions_page_handler'));	
		
		add_submenu_page('uwa_auctions_dashboard', __('Add Auction', 'woo_ua'), __('Add Auction', 'woo_ua'), 'manage_woocommerce', 'uwa_add_auctions_products', array($this, 'uwa_add_auctions_products_page_handler'));	
		
		add_submenu_page('uwa_auctions_dashboard', __('Bids', 'woo_ua'), __('Bids', 'woo_ua'), 'manage_woocommerce', 'uwa_auctions_bids_list', array($this, 'uwa_auctions_bids_page_handler'));	

		add_submenu_page('uwa_auctions_dashboard', __('Import', 'woo_ua'), __('Import', 'woo_ua'), 
			'manage_options', 'uwa_auctions_import', array($this, 
			'uwa_auction_import_page_handler'));	
			
		add_submenu_page('uwa_auctions_dashboard', __('Settings', 'woo_ua'), __('Settings', 'woo_ua'), 'manage_options', 'uwa_general_setting', array($this, 'uwa_auction_setting_page_handler'));
		
		add_submenu_page('uwa_auctions_dashboard', __('Help', 'woo_ua'), __('Help', 'woo_ua'), 'manage_options', 'uwa_auction_help', array($this, 'uwa_auction_help_page_handler'));
		
	}
	
	/**
	 * Auction Setting Callback Function.
	 *
	 */		
	public function uwa_auction_setting_page_handler() {		
		include_once( UW_AUCTION_PRO_ADMIN . '/uwa_general_setting.php');				
	}
		
	public function uwa_manage_auctions_page_handler() {		
		include_once( UW_AUCTION_PRO_ADMIN . '/uwa_manage_auctions.php');
		uwa_manage_auctions_list_page_handler_display();	
	}
	public function uwa_add_auctions_products_page_handler() {		
		include_once( UW_AUCTION_PRO_ADMIN . '/uwa_auctions_add_products.php');				
	}
	
	public function uwa_auctions_dashboard_page_handler() {		
		include_once( UW_AUCTION_PRO_ADMIN . '/uwa_auctions_dashboard.php');				
	}
	public function uwa_auctions_bids_page_handler() {		
		include_once( UW_AUCTION_PRO_ADMIN . '/uwa_bids_lists.php');	
		 uwa_bids_list_page_handler_display();
	}
		
	public function uwa_auction_import_page_handler() {		
	    include_once( UW_AUCTION_PRO_ADMIN . '/uwa_importer_page.php');	
	   
	}
	
	public function uwa_auction_help_page_handler() {		
		include_once( UW_AUCTION_PRO_ADMIN . '/uwa_auction_help.php');				
	}
	/**
	 * Add a custom product tab.
	 *
	 */
	public  function uwa_custom_product_tabs( $product_data_tabs ) {	  
	    $auction_tab = array(
							'auction_tab' => array(
									'label'  => __('Auction', 'woo_ua'),
									'target' => 'auction_options',
									'class'  => array('show_if_auction' , 'hide_if_grouped', 'hide_if_external','hide_if_variable','hide_if_simple' ),
							),
						);

		return $auction_tab + $product_data_tabs;
    }

  /**
	 * Contents of the Auction  Product options product tab.
	 *	
	 */
    public function uwa_options_product_tab_content() {
        global $post;
			$product = wc_get_product($post->ID);

			$woo_ua_form_type = "add_product";
			if(isset($_GET['action'])){
		        if($_GET['action'] == "edit"){
		        	$woo_ua_form_type = "edit_product";
		        }
		    }
		    
	        $is_auction_expired = "no";
	        $is_auction_live = "no";
	        $auction_status_type = "";
			
			$auction_checked = "checked";
			$buyitnow_checked = "checked";
		
			if (method_exists( $product, 'get_type') && $product->get_type() == 'auction'){
				if($woo_ua_form_type == "edit_product"){
					
					/* GET auction  live/expired */
					if($product->is_uwa_live() == TRUE){  // get_uwa_auction_has_started
						$is_auction_live = "yes";					
						$auction_status_type = "live";
					}
					if($product->is_uwa_expired() == TRUE){ // get_uwa_auction_expired					
						$is_auction_expired = "yes";
						$auction_status_type = "expired";
					}
					
					/* GET auction selling type */
					$post_id = $post->ID;
					$auction_checked = "";
					$buyitnow_checked = "";
					//$selling_type = get_post_meta( $post_id, 'woo_ua_auction_selling_type', true);					
					$selling_type = $product->get_uwa_auction_selling_type();
									
					
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
					else{ //elseif($selling_type == ""){
						/* for old auctions in which key is not set */
						$auction_checked = "checked";
						$buyitnow_checked = "checked";
					}										
						
				} /* end of if -- edit product */
				
			} /* end of if -- method exists */

			?>
			
		<div id='auction_options' class='panel woocommerce_options_panel'>
	
			<div class='options_group'>
					<?php

						/* product is added or updated */
						woocommerce_wp_hidden_input( array(
							'id'			=> 'woo_ua_auction_currency',
							'value'         => get_woocommerce_currency_symbol(),				
						));


						/* product is added or updated */
						woocommerce_wp_hidden_input( array(
							'id'			=> 'woo_ua_auction_form_type',
							'value'         => $woo_ua_form_type,					
													
						));

						/* product status type is live or expired */
						woocommerce_wp_hidden_input( array(
							'id'			=> 'woo_ua_auction_status_type',
							'value'         => $auction_status_type
						));


						if(isset($_GET['action']) && $_GET['action'] == "edit"){

							/* add field during edit product to store product type */
							woocommerce_wp_hidden_input( array(
								'id'			=> 'woo_ua_product_type',
								'value'         => $product->get_type()
							));
	        			}

						
		echo "<div  width='70%'> ";  // 1 start - main 
						
						
						$selling_type_desc = __('Set the selling type for the auction', 'woo_ua');
					?>
								
						<p class="form-field"> 									
								<label><?php _e('Selling Type', 'woo_ua'); ?></label>									
								<input type="checkbox" id="uwa_auction_selling_type_auction" name="uwa_auction_selling_type_auction"	
									<?php echo $auction_checked; ?> /> <?php _e('Auction', 'woo_ua'); ?> 										  
									
								<span style="margin-right:25px"> </span>  
								
								<input type="checkbox" id="uwa_auction_selling_type_buyitnow" name="uwa_auction_selling_type_buyitnow"	
									<?php echo $buyitnow_checked; ?> />  <?php _e('Buy it now', 'woo_ua'); ?>
										
									<?php echo wc_help_tip($selling_type_desc); ?>
						</p>
								
					<?php
						if(get_option('uwa_hide_product_condition_field', 'no') == 'no'){
							woocommerce_wp_select( array(
								'id' => 'woo_ua_product_condition', 
								'label' => __('Product Condition', 'woo_ua'),
								'options' => apply_filters('ultimate_woocommerce_auction_product_condition' ,$this->uwa_auction_item_condition))
							);
						}
 
				echo "<div class='selling_type_auction'>";     // 2 start -  selling type auction 

						woocommerce_wp_select( array(
							'id' => 'woo_ua_auction_type', 
							'label' => __('Auction type', 'woo_ua'),
							'options' => $this->uwa_auction_types
							)
						);
						
						if(get_option('uwa_proxy_bid_enable', 'no') == 'yes'){
						
							$proxy_value =  in_array(get_post_meta( $post->ID, 'uwa_auction_proxy', true ) , array( '0', 'yes')) ? get_post_meta( $post->ID, 'uwa_auction_proxy', true ) :
								get_option('uwa_proxy_bid_enable', 'no');
							
							woocommerce_wp_checkbox(
									array(	
									'value' => $proxy_value,
									'id' => 'uwa_auction_proxy',
									'wrapper_class' => '',
									'label' => __('Enable proxy bidding', 'woo_ua'),
									'description' => __("Proxy Bidding (also known as Automatic Bidding) - Our automatic bidding system makes bidding convenient so you don't have to keep coming back to re-bid every time someone places another bid. When you place a bid, you enter the maximum amount you're willing to pay for the item. The seller and other bidders don't know your maximum bid. We'll place bids on your behalf using the automatic bid increment amount, which is based on the current high bid. We'll bid only as much as necessary to make sure that you remain the high bidder, or to meet the reserve price, up to your maximum amount.",'woo_ua'), 
									'desc_tip' => 'true')
							);
						}
						
						if(get_option('uwa_silent_bid_enable', 'no') == 'yes'){		
							woocommerce_wp_checkbox(
										array(								
										'id' => 'uwa_auction_silent',
										'wrapper_class' => '',
										'label' => __('Enable Silent-Bid', 'woo_ua'),
										'description' => __("A Silent-Bid auction is a type of auction process in which all bidders simultaneously submit Silent bids to the auctioneer, so that no bidder knows how much the other auction participants have bid. The highest bidder is usually declared the winner of the bidding process.",'woo_ua'), 
										'desc_tip' => 'true')
							);
						}
							
						woocommerce_wp_text_input( array(
							'id'			=> 'woo_ua_opening_price',
							'label'			=> __( 'Opening Price', 'woo_ua' ). ' (' . get_woocommerce_currency_symbol() . ')',
							'desc_tip'		=> 'true',
							'description'	=> __( 'Set the opening price for the auction', 'woo_ua' ),
							'data_type' 			=> 'price',
							'custom_attributes' => array(
									'step' => 'any',
									'min' => '0',
									)
						));	
							  
						woocommerce_wp_text_input( array(
							'id'			=> 'woo_ua_lowest_price',            
							'label'			=>  __('Lowest Price to Accept', 'woo_ua') . ' (' . get_woocommerce_currency_symbol() . ')',
							'desc_tip'		=> 'true',							
							'description'	=> __( 'Set Reserve price for your auction.', 'woo_ua' ),
							'data_type' => 'price',
							'custom_attributes' => array(
									'step' => 'any',
									'min' => '0',
							)
						));
										
						$default_bid_inc = 1;
						$get_inc_val = get_post_meta($post->ID, 'woo_ua_bid_increment', 
							true);
						if($get_inc_val >= 0.1){       // if($get_inc_val >= 1){
							$bid_inc_val = $get_inc_val;
						}
						else{

							$uwa_global_bid_inc = get_option("uwa_global_bid_inc");							
							//$uwa_global_bid_inc = (float)$uwa_global_bid_inc;
							
							if($uwa_global_bid_inc > 0){								
								$bid_inc_val = $uwa_global_bid_inc;
							}
							else{
								$bid_inc_val = $default_bid_inc;
							}
							
						}

						woocommerce_wp_text_input( array(
							'id'			=> 'woo_ua_bid_increment',
							'label'			=> __( 'Bid Increment', 'woo_ua' ) . ' (' . get_woocommerce_currency_symbol() . ')',
							'desc_tip'		=> 'true',							
							'description'	=> __( 'Set an amount from which next bid should start.', 'woo_ua' ),
							'data_type' => 'price',
							'value' => $bid_inc_val,							
							'custom_attributes' => array(
									'step' => 'any',
									'min' => '0',
							)
						));
						$var_bid_inc_value =  get_post_meta( $post->ID, 'uwa_auction_variable_bid_increment', true);
						woocommerce_wp_checkbox(
									array(										
									'id' => 'uwa_auction_variable_bid_increment',
									'value' => $var_bid_inc_value,
									'wrapper_class' => '',
									'label'			=> __('Variable Bid Increment', 'woo_ua' ). ' (' . get_woocommerce_currency_symbol() . ')',
									'description' => __("Enable Variable Incremental Price.",'woo_ua'), 
									'desc_tip' => 'true')
								);
						?>
						
					<p class="form-field uwa_variable_bid_increment_main">		
								
						
						<span id="uwa_custom_field_add_remove"> 
							<!-- Don't 	remove -->

							<label><?php _e('Variable Bid Increment', 'woo_ua'); ?><?php echo '(' . get_woocommerce_currency_symbol() . ')';?></label>

							<input type="button" id="plus_field" class="button button-secondary" value="Add New" />

						<?php 
						
						    $uwa_var_inc_data = get_post_meta( $post->ID, 'uwa_var_inc_price_val', true );
							//$uwa_var_inc_data_count = count($uwa_var_inc_data); 
							$i=1;
							if ( !empty($uwa_var_inc_data)){
								foreach($uwa_var_inc_data as $key => $variable_val){
									
									if($key !== 'onwards' ){ ?>											
									<span id="uwa_custom_field_<?php echo $i; ?>" class="uwa_custom_field_main">
										<input type="number" class="uwa_auction_price_fields start_valid" id="start_val_<?php echo $i; ?>" data-startid="<?php echo $i; ?>" name="uwa_var_inc_val[<?php echo $i; ?>][start]" value="<?php echo $variable_val['start']; ?>" placeholder="<?php _e('Start Price', 'woo_ua'); ?>"/>
										<input type="number" class="uwa_auction_price_fields end_valid" id="end_val_<?php echo $i; ?>" data-endid="<?php echo $i; ?>"  name="uwa_var_inc_val[<?php echo $i; ?>][end]" value="<?php echo $variable_val['end']; ?>" placeholder="<?php _e('End Price', 'woo_ua'); ?>"/>
										<input type="number" class="uwa_auction_price_fields" id="inc_val_<?php echo $i; ?>" name="uwa_var_inc_val[<?php echo $i; ?>][inc_val]" value="<?php echo $variable_val['inc_val']; ?>" placeholder="<?php _e('Increment Price', 'woo_ua'); ?>"/>
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
								<input type="number" class="uwa_auction_price_fields start_valid" id="start_val_0" data-startid="0" name="uwa_var_inc_val[0][start]" value="" placeholder="<?php _e('Start Price', 'woo_ua'); ?>"/>
								<input type="number" class="uwa_auction_price_fields end_valid" id="end_val_0" data-endid="0"  name="uwa_var_inc_val[0][end]" value="" placeholder="<?php _e('End Price', 'woo_ua'); ?>"/>
								<input type="number" class="uwa_auction_price_fields" id="inc_val_0" name="uwa_var_inc_val[0][inc_val]" value="" placeholder="<?php _e('Increment Price', 'woo_ua'); ?>"/>
							</span>
							<?php } ?>


					<?php if(!empty($uwa_var_inc_data) && $uwa_var_inc_data['onwards']['end'] == 'onwards' ){ ?>
							    <div id="uwa_custom_field_onwards" class="uwa_custom_field_onwards_main">
								<input type="number" class="uwa_auction_price_fields start_valid" id="start_val_onwards" name="uwa_var_inc_val[onwards][start]" value="<?php echo $uwa_var_inc_data['onwards']['start']; ?>" placeholder="<?php _e('Start', 'woo_ua'); ?>"/>
								<input type="text" class="uwa_auction_price_fields end_valid" id="end_val_onwards" name="uwa_var_inc_val[onwards][end]"
								value="onwards" placeholder="<?php _e('onwards', 'woo_ua'); ?>" readonly />
								<input type="number" class="uwa_auction_price_fields" id="inc_val_onwards" name="uwa_var_inc_val[onwards][inc_val]" value="<?php echo $uwa_var_inc_data['onwards']['inc_val']; ?>" placeholder="<?php _e('Increment Price', 'woo_ua'); ?>"/></div>
					<?php }  else { ?>					
						        <div id="uwa_custom_field_onwards" class="uwa_custom_field_onwards_main">
								<input type="number" class="uwa_auction_price_fields start_valid" id="start_val_onwards" name="uwa_var_inc_val[onwards][start]" value="" placeholder="<?php _e('Start Price', 'woo_ua'); ?>"/>
								<input type="text" class="uwa_auction_price_fields end_valid" id="end_val_onwards" name="uwa_var_inc_val[onwards][end]" value="onwards" placeholder="<?php _e('onwards', 'woo_ua'); ?>" readonly />
								<input type="number" class="uwa_auction_price_fields" id="inc_val_onwards" name="uwa_var_inc_val[onwards][inc_val]" value="" placeholder="<?php _e('Increment Price', 'woo_ua'); ?>"/></div>
						<?php } ?>					


						</span>
						<script type="text/javascript">
							<?php if($var_bid_inc_value=="yes"){ ?>								
								jQuery('p.uwa_variable_bid_increment_main').css("display", "block"); 
								jQuery('.uwa_custom_field_onwards_main').css("display", "block");			
								jQuery('.form-field.woo_ua_bid_increment_field').css("display", "none");
								jQuery('#woo_ua_bid_increment').val("");
								
							<?php } ?>
					var flag=<?php echo $i;?>;

					var arr=[];

					jQuery('#plus_field').click(function(){

						jQuery('#uwa_custom_field_add_remove').append('<span id="uwa_custom_field_'+flag+'" class="uwa_custom_field_main"><input type="number" class="uwa_auction_price_fields start_valid" id="start_val_'+flag+'" data-startid="'+flag+'" name="uwa_var_inc_val['+flag+'][start]" value="" placeholder="Start Price" /><input type="number" class=" uwa_auction_price_fields end_valid" id="end_val_'+flag+'" data-endid="'+flag+'" name="uwa_var_inc_val['+flag+'][end]" value="" placeholder="End Price" /><input type="number" class=" uwa_auction_price_fields" id="inc_val_'+flag+'" name="uwa_var_inc_val['+flag+'][inc_val]" value="" placeholder="Increment Price" /><input type="button" class="button button-secondary minus_field" value="-" data-custom="'+flag+'"></span>');
						var end_range_valid = (parseInt(flag) - 1);
						var end_range_val = jQuery("#end_val_"+end_range_valid).val();
						jQuery('#start_val_'+flag).val(end_range_val);
						flag++;

					});

					jQuery(document).on('click', '.minus_field' ,function(){
						var id=jQuery(this).attr('data-custom');
						var id_name="uwa_custom_field_"+id+"";
						jQuery('#'+id_name+'').remove();
						flag--;
					});

					jQuery(document).on('keyup', '.end_valid', function(){
						var end_range = jQuery(this).attr('data-endid');
						var end_range_val = jQuery(this).val();
						var end_range_valid = (parseInt(end_range) + 1);
						jQuery('#start_val_'+end_range_valid).val(end_range_val);
					});
					
				</script></p>
								  <?php

						if($woo_ua_form_type == "edit_product"){							
							//$nextbids_val = $product->get_uwa_total_next_bids();
							$nextbids_val =  get_post_meta( $post->ID, 'woo_ua_next_bids', true );				
						}
						else{
							$nextbids_val = 10;
						}
						
						woocommerce_wp_text_input( array(
							'id'			=> 'woo_ua_next_bids',
							'label'			=> __( 'Number of next bids', 'woo_ua' ), 
							'desc_tip'		=> 'true',							
							'description'	=> __( 'Set an amount that how many next bids in dropdown when direct bid is enabled and maximum value is 100', 'woo_ua' ),
							'data_type' => 'number',
							'value' => $nextbids_val,							
							'custom_attributes' => array(
									//'step' => 'any',
									//'min' => '1'									
							)
						));
						
						
				echo "</div>";  // 2 end -  selling type auction 

				echo "<div class='selling_type_buyitnow'>"; // 6 start - buyit now auction 
						woocommerce_wp_text_input( array(
							'id'			=> '_regular_price',
							'label'			=> __( 'Buy now price', 'woo_ua' ). ' (' . get_woocommerce_currency_symbol() . ')',
							'desc_tip'		=> 'true',
							'data_type' => 'price',
							'description'	=> __( 'Visitors can buy your auction by making payments via Available payment method.', 'woo_ua' ),							
						));
				echo "</div>"; // 6 end - buyit now auction 
			

		echo "</div>";  // 1 end - main 
							
					
						$nowdate_for_start = get_post_meta($post->ID, 'woo_ua_auction_start_date', true) ?  : get_uwa_now_date();	
						woocommerce_wp_text_input( array(
							'id'			=> 'woo_ua_auction_start_date',
							'label'			=> __( 'Start Date', 'woo_ua' ),
							'desc_tip'		=> 'true',
							'description'	=> __( 'Set the Start date of Auction Product.', 'woo_ua' ),
							'type' 			=> 'text',			
							'class'         => 'datetimepicker',
							'value'         => $nowdate_for_start
						)); 						
						$nowdate =  wp_date('Y-m-d H:i:s',strtotime('+1 day', time()),get_uwa_wp_timezone());
						$end_date = get_post_meta($post->ID, 'woo_ua_auction_end_date', true) ?  : $nowdate;	 
						woocommerce_wp_text_input( array(
							'id'			=> 'woo_ua_auction_end_date',
							'label'			=> __( 'Ending Date', 'woo_ua' ),
							'desc_tip'		=> 'true',
							'description'	=> __( 'Set the end date for the auction', 'woo_ua' ),
							'type' 			=> 'text',			
							'class'         => 'datetimepicker',
							'value'         => $end_date
						));
						
					$uwa_auto_renew_enable = get_post_meta( $post->ID, 'uwa_auto_renew_enable', true );

					woocommerce_wp_checkbox(
					array('value' => $uwa_auto_renew_enable,
					'id' => 'uwa_auto_renew_enable', 
					'wrapper_class' => '', 
					'label' => __('Enable Automatic Relist', 'woo_ua'), 
					'desc_tip'		=> 'true',					
					'description' => __('If there is no bid placed, Reserve price not met,if winner user not paid on this auction then the auction will get automatically Relist(Renew) for the duration specified in Relist duration interval.', 'woo_ua'))
					);
					
					echo '<div class="uwa_auto_renew_auction_main">';
					/* Recurring Auto Relist */	
					$uwa_auto_renew_recurring_enable = get_post_meta( $post->ID, 'uwa_auto_renew_recurring_enable', true );
					woocommerce_wp_checkbox(
					array('value' => $uwa_auto_renew_recurring_enable,
					'id' => 'uwa_auto_renew_recurring_enable', 
					'wrapper_class' => '', 
					'label' => __('Keep repeating Relist if below conditions are met', 'woo_ua'), 
					'desc_tip'		=> 'true',					
					'description' => __('Auction will get automatically Relist(Renew) repeat on the duration specified in Relist duration interval. Skip For Once Relist', 'woo_ua'))
					);

					/* Enable For Not Paid User Start */	
					$uwa_auto_renew_not_paid_enable = get_post_meta( $post->ID, 'uwa_auto_renew_not_paid_enable', true );
					woocommerce_wp_checkbox(
					array('value' => $uwa_auto_renew_not_paid_enable,
					'id' => 'uwa_auto_renew_not_paid_enable', 
					'wrapper_class' => '', 
					'label' => __('Relist if winner has not paid', 'woo_ua'), 
					'desc_tip'		=> 'true',					
					'description' => __('Enable if winner won the auction and not paid.', 'woo_ua'))
					);
					
					echo '<div class="uwa_auto_renew_auction_not_paid">';
						woocommerce_wp_text_input(array('id' => 'uwa_auto_renew_not_paid_hours',			
						'class' => 'wc_input_price short', 
						'label' => __('Specify no. of hours after which Relist will happen', 'woo_ua'),
						'description' => __('Hours.', 'woo_ua'),
						'type' => 'number',					
						'custom_attributes' => array(
							'step' => 'any',
							'min' => '1',
						)));
					echo "</div>";
					/* Enable For Not Paid User  End */
					
					/* Enable For Bo Bids Start */	
					$uwa_auto_renew_no_bids_enable = get_post_meta( $post->ID, 'uwa_auto_renew_no_bids_enable', true );
					woocommerce_wp_checkbox(
					array('value' => $uwa_auto_renew_no_bids_enable,
					'id' => 'uwa_auto_renew_no_bids_enable', 
					'wrapper_class' => '', 
					'label' => __('Relist if there are no bids in auction', 'woo_ua'), 
					'desc_tip'		=> 'true',					
					'description' => __('Enable if auction expired with no bids.', 'woo_ua'))
					);
					echo '<div class="uwa_auto_renew_auction_no_bids">';
					woocommerce_wp_text_input(array('id' => 'uwa_auto_renew_fail_hours',
					'class' => 'wc_input_price short',
					'label' => __('Specify no. of hours after which Relist will happen', 'woo_ua'),
					'description' => __('Hours.', 'woo_ua'),	
					'type' => 'number', 'custom_attributes' => array(
						'step' => 'any',
						'min' => '0',
					)));
					echo "</div>";
					/* Enable For Bo Bids End */
					
					
					/* Enable For Reserve Price not met Start */	
					$uwa_auto_renew_no_reserve_enable = get_post_meta( $post->ID, 'uwa_auto_renew_no_reserve_enable', true );
					woocommerce_wp_checkbox(
					array('value' => $uwa_auto_renew_no_reserve_enable,
					'id' => 'uwa_auto_renew_no_reserve_enable', 
					'wrapper_class' => '', 
					'label' => __('Relist if reserve price has not met', 'woo_ua'), 
					'desc_tip'		=> 'true',					
					'description' => __('Enable Relist if Reserve Price not met..', 'woo_ua'))
					);
					echo '<div class="uwa_auto_renew_auction_no_reserve">';
					woocommerce_wp_text_input(array('id' => 'uwa_auto_renew_reserve_fail_hours',
					'class' => 'wc_input_price short',
					'label' => __('Specify no. of hours after which Relist will happen', 'woo_ua'),
					'description' => __('Hours.', 'woo_ua'),	
					'type' => 'number', 'custom_attributes' => array(
						'step' => 'any',
						'min' => '0',
					)));
					echo "</div>";
					/* Enable For Reserve Price not met End */
					
					
					woocommerce_wp_text_input(array('id' => 'uwa_auto_renew_duration_hours',
					'class' => 'wc_input_price short',
					'label' => __('Specify the duration in hours for which auction will be live after Relist', 'woo_ua'),
					'description' => __('Hours.', 'woo_ua'),	
					'type' => 'number', 'custom_attributes' => array(
						'step' => 'any',
						'min' => '0',
					)));

					
					?>
				
				</div>				
					<?php if($uwa_auto_renew_enable=="yes"){ ?>	
					       <script type="text/javascript">							
							jQuery('.uwa_auto_renew_auction_main').css("display", "block");
						  </script>
					<?php } ?>
				
					<?php if($uwa_auto_renew_not_paid_enable=="yes"){ ?>	
					       <script type="text/javascript">							
							jQuery('.uwa_auto_renew_auction_not_paid').css("display", "block");
						  </script>
					<?php } ?>
					
					
					<?php if($uwa_auto_renew_no_bids_enable=="yes"){ ?>	
					       <script type="text/javascript">							
							jQuery('.uwa_auto_renew_auction_no_bids').css("display", "block");
						  </script>
					<?php } ?>
					
					<?php if($uwa_auto_renew_no_reserve_enable=="yes"){ ?>	
					       <script type="text/javascript">							
							jQuery('.uwa_auto_renew_auction_no_reserve').css("display", "block");
						  </script>
					<?php } ?>
					

<?php

 /*  ----- Buyer premium form - start ----  */

$addons = uwa_enabled_addons();
if(is_array($addons) && in_array('uwa_buyers_premium_addon', $addons)){

?>
	<div class="uwa_auction_buyers_main">

			<div class="uwa_auction_buyers_heading"
				style="margin-top:15px;margin-bottom:10px;padding-left:12px;font-size:15px;">
				<strong><u><i>
				<?php echo __( "Set Buyer's Premium (B.P)", 'woo_ua' ); ?>
				</i></u></strong>						
			</div>

			<?php
				$woo_ua_buyer_level = get_post_meta( $post->ID, 'woo_ua_buyer_level', true );
				if(!$woo_ua_buyer_level){
					$woo_ua_buyer_level = "globally";
				}
				woocommerce_wp_radio(array(
					'id'			=> 'woo_ua_buyer_level',
				   	'label'			=> __( 'Which B.P settings do you want to apply', 'woo_ua' ),					   	
				   	'value' 		=> $woo_ua_buyer_level, 
				    //'style' 		=> '', 
				    //'wrapper_class' => '', 					    
				    //'name' 			=> 'my_radio_buttons', 
				    'options'		=> array( 
				      'globally' 		=> __( 'Defined at System Level', 'woo_ua' ),
				      'product_level' 	=> __( 'Set for this product', 'woo_ua' )
				    ), 						    
				));

			?>
				
			<div class="uwa_auction_buyers_globally" style="margin-top:20px;margin-bottom:60px;padding-left:12px;">
					<strong>
					<?php echo __( "Check settings for Buyer's Premium at  ", 'woo_ua' ); ?>				
					<a href="<?php echo admin_url('admin.php?page=uwa_general_setting&setting_section=uwa_addons_setting');
						?>"  target="_blank"><?php _e('Globally', 'woo_ua'); ?></a>
					</strong>						
			</div>				

			<div class="uwa_auction_buyers_productlevel" style="margin-top:20px;margin-bottom:30px;">					

						<?php

							$woo_ua_buyer_given_to = get_post_meta( $post->ID, 'woo_ua_buyer_given_to', true );			
							if(!$woo_ua_buyer_given_to){
								$woo_ua_buyer_given_to = "uwa_admin";
							}

							$woo_ua_buyer_type = get_post_meta( $post->ID, 'woo_ua_buyer_type', true );
							if(!$woo_ua_buyer_type){
								$woo_ua_buyer_type = "flat";
							}


							woocommerce_wp_radio(array(
								'id'			=> 'woo_ua_buyer_given_to',
							   	'label'			=> __("Give Buyer's Premium to", "woo_ua"),
							   	'value' 		=> $woo_ua_buyer_given_to, 
							    //'style' => '', 
							    //'wrapper_class' => '', 
							    //'name' => 'my_radio_buttons', 
							    'options' 		=> array( 
							      'uwa_admin' 	=> __("Admin", "woo_ua"),
							      'uwa_owners' 	=> __("Auction Owners", "woo_ua")
							    ), 						    
							));

							woocommerce_wp_radio(array(
								'id'			=> 'woo_ua_buyer_type',
							   	'label'			=> __("Buyer's Premium Type", "woo_ua"),
							   	'value' 		=> $woo_ua_buyer_type, 
							    //'style' => '', 
							    //'wrapper_class' => '',							    
							    //'name' => 'my_radio_buttons', 
							    'options' 		=> array( 
							      'flat' 		=> __("Flat", "woo_ua"),
							      'percentage' 	=> __("Percentage", "woo_ua")
							    ), 						    
							));

							
							if($woo_ua_buyer_type == "flat"){
								$display = get_woocommerce_currency_symbol();
							}elseif($woo_ua_buyer_type == "percentage"){
								$display = "in %";
							}

							woocommerce_wp_text_input(array(
								'id'			=> 'woo_ua_buyer_fee_amt',
								'label'			=> __("Fee Amount", "woo_ua") ." (<span class='ua_b_fee_amt'>".$display."</span>)",
								'style' 		=> 'width:30%;', 
								'desc_tip'		=> 'true',
								'description'	=> __("Based on your selection above this field is Amount for Flat Rate or Percentage", "woo_ua"),
								'data_type' 	=> 'price',								
								'custom_attributes' => array(
										'step' 	=> 'any',
										'min' 	=> '1',
								),
							));

							woocommerce_wp_text_input(array(
								'id'			=> 'woo_ua_buyer_min_amt',
								'label'			=> __("Minimum Premium Amount", "woo_ua"). ' (' . get_woocommerce_currency_symbol() . ')',
								'style'			=> 'width:30%;', 
								'desc_tip'		=> 'true',
								'description'	=> __("This amount is minimum buyer's premium amount in unit of currency that will be applicable. If the amount calculated in percentage is below this minimum amount then this amount will be charged", "woo_ua"),
								'data_type' 	=> 'price',								
								'custom_attributes' => array(
										'step'	=> 'any',
										'min'	=> '1',
								),
							));	

							woocommerce_wp_text_input(array(
								'id'			=> 'woo_ua_buyer_max_amt',
								'label'			=> __("Maximum Premium Amount", "woo_ua" ). ' (' . get_woocommerce_currency_symbol() . ')',
								'style' 		=> 'width:30%;', 
								'desc_tip'		=> 'true',
								'description'	=> __("This amount is maximum buyer's premium amount in unit of currency that will be applicable. If the amount calculated in percentage is above this maximum amount then this amount will be charged.", "woo_ua"),
								'data_type'  	=> 'price',								
								'custom_attributes' => array(
										'step'	=> 'any',
										'min'	=> '1',
								),
							));	
		

						?>

			</div> <!-- end of  uwa_auction_buyers_productlevel  -->


	</div> <!-- end of  uwa_auction_buyers_main  -->

	<style>
		/*.uwa_auction_buyers_main .wc-radios li{
			display:inline!important;			
		}
		.uwa_auction_buyers_main .wc-radios li:nth-child(2){		
			margin-left:20px!important;	
		}
		.uwa_auction_buyers_main input{
			disabled:disabled!important;
		}*/
			
	</style>

<?php 
} /* end of if - addon-buyers premium */


 /*  ----- Buyer premium form - end ----  */

?>







						<div class="uwa_admin_current_time">
								<?php											
								printf(__('Current Blog Time is %s', 'woo_ua'), '<strong>'.get_uwa_now_date().'</strong> ');
								echo __('Timezone:', 'woo_ua').' <strong>'.wp_timezone_string().'</strong>';
								echo __('<a href="'.admin_url('options-general.php?#timezone_string').'" target="_blank">'.' '.__('Change', 'woo_ua').'</a>');?>								
						</div>
						<?php
						if ((method_exists( $product, 'get_type') && $product->get_type() == 'auction') && $product->get_uwa_auction_expired() && !$product->get_uwa_auction_payed()) { ?>
						
							<p class="form-field uwa_relist_dates_fields" id="uwa_relist_dates_fields">
								<a href="#" class="button uwa_force_relist" data-auction_id="<?php echo $post->ID;?>"><?php _e('Manually Relist Now', 'woo_ua'); ?></a>
							</p>
						
							<div  class="uwa_auction_relist_date_field">
								<?php 	
								woocommerce_wp_text_input( array(
									'id'			=> 'uwa_relist_start_date',
									'label'			=> __( 'Start Date', 'woo_ua' ),
									'desc_tip'		=> 'true',
									'description'	=> __( 'Set the Start date of Auction Product.', 'woo_ua' ),
									'type' 			=> 'text',			
									'class'         => 'datetimepicker',							 
								) ); 
								 
								 
								woocommerce_wp_text_input( array(
									'id'			=> 'uwa_relist_end_date',
									'label'			=> __( 'Ending Date', 'woo_ua' ),
									'desc_tip'		=> 'true',
									'description'	=> __( 'Set the end date for the auction', 'woo_ua' ),
									'type' 			=> 'text',			
									'class'         => 'datetimepicker',							
								) ); ?>
				       
							</div>

						  <?php  
						} 
						if ((method_exists( $product, 'get_type') && $product->get_type() == 'auction') and ($product->is_uwa_live()  === FALSE )) { ?>
							
							<p class="form-field uwa_admin_uwa_make_live">
							<a href="#" class="button uwa_force_make_live" data-auction_id="<?php echo $post->ID;?>"><?php _e('Make It Live', 'woo_ua'); ?></a>
							</p> <?php  
						}
						
						if ((method_exists( $product, 'get_type') && $product->get_type() == 'auction') && $product->is_uwa_expired() === FALSE && ($product->is_uwa_live()  === TRUE )) {   ?>
							
							<p class="form-field uwa_admin_uwa_force_end_now">
							<a href="#" class="button uwa_force_end_now" data-auction_id="<?php echo $post->ID;?>"><?php _e('End Now', 'woo_ua'); ?></a>
							</p>  <?php  											
							
						} ?>
					

			</div>
		</div> 
	  <?php	 
    }
	
    /**
	 * Add to product type drop down
	 *	
	 */
	public function uwa_add_auction_product( $types ){
		/* Key should be exactly the same as in the class */
		$types[ 'auction' ] = __( 'Auction Product', 'woo_ua' );
		return $types;
	}

	/**
	 * Save Auction Product Data.
	 *	
	 */
    function uwa_save_auction_option_field( $post_id ) {
		global $wpdb, $woocommerce, $woocommerce_errors;
		$product_type = empty($_POST['product-type']) ? 'simple' : sanitize_title(stripslashes($_POST['product-type']));	
		if ( $product_type == 'auction' ) {

			update_post_meta($post_id, '_manage_stock', 'yes');
			update_post_meta($post_id, '_stock', '1');
			update_post_meta($post_id, '_backorders', 'no');
			update_post_meta($post_id, '_sold_individually', 'yes');
			
			if (isset($_POST['_regular_price'])) {
				update_post_meta($post_id, '_regular_price', wc_format_decimal(wc_clean($_POST['_regular_price'])));
				update_post_meta($post_id, '_price', wc_format_decimal(wc_clean($_POST['_regular_price'])));
			}
			
			if( isset( $_POST['woo_ua_product_condition'])) {
				update_post_meta( $post_id, 'woo_ua_product_condition', esc_attr( $_POST['woo_ua_product_condition'] ) );
			}

			if(isset($_POST['uwa_auction_proxy'])){
				update_post_meta( $post_id, 'uwa_auction_proxy', stripslashes( $_POST['uwa_auction_proxy'] ) );
			} else {
				update_post_meta( $post_id, 'uwa_auction_proxy', '0' );
			}
			
			if(isset($_POST['uwa_auction_silent'])){
				update_post_meta( $post_id, 'uwa_auction_silent', stripslashes( $_POST['uwa_auction_silent'] ) );
			} else {
				update_post_meta( $post_id, 'uwa_auction_silent', '0' );
			}
			
			
			if (isset($_POST['woo_ua_opening_price'])) {			
				update_post_meta( $post_id, 'woo_ua_opening_price', wc_format_decimal(wc_clean($_POST['woo_ua_opening_price'])) );
			}
			
			if( isset( $_POST['woo_ua_lowest_price'] ) )  {				
				update_post_meta( $post_id, 'woo_ua_lowest_price', wc_format_decimal(wc_clean($_POST['woo_ua_lowest_price'])) );
			}
			

			if( isset( $_POST['woo_ua_next_bids'] ) )  {
				if($_POST['woo_ua_next_bids'] > 0 && $_POST['woo_ua_next_bids'] <= 100 ){
					update_post_meta( $post_id, 'woo_ua_next_bids', wc_format_decimal(wc_clean($_POST['woo_ua_next_bids']))
					 );
				}
				else{
					update_post_meta( $post_id, 'woo_ua_next_bids', 10);	
				}
			}
			
			
			if( isset( $_POST['woo_ua_bid_increment']) && $_POST['woo_ua_bid_increment'] !='' ) {
				update_post_meta( $post_id, 'woo_ua_bid_increment', wc_format_decimal(wc_clean($_POST['woo_ua_bid_increment'])) );	
				
				delete_post_meta( $post_id, 'uwa_auction_variable_bid_increment' );		
				delete_post_meta( $post_id, 'uwa_var_inc_price_val' );		
			} 

			/* Pro Plugin */			
			if(isset( $_POST['woo_ua_auction_start_date']) && $_POST['woo_ua_auction_start_date'] !="" ) {
				
				update_post_meta( $post_id, 'woo_ua_auction_start_date', stripslashes( $_POST['woo_ua_auction_start_date'] ) );			   
			} else {				
				 //update_post_meta( $post_id, 'woo_ua_auction_start_date', stripslashes( $start_date ) );
			}
			
			if( isset( $_POST['woo_ua_auction_end_date']) ) {
				update_post_meta( $post_id, 'woo_ua_auction_end_date', stripslashes( $_POST['woo_ua_auction_end_date'] ) );			   
			}
			
			/* Add In Pro */
			if( isset( $_POST['woo_ua_auction_type']) ) {						
				update_post_meta( $post_id, 'woo_ua_auction_type', esc_attr( $_POST['woo_ua_auction_type'] ) );
			}
			
			if (isset($_POST['uwa_relist_start_date']) && isset($_POST['uwa_relist_end_date'])){
				$uwa_relist_start_date = $_POST['uwa_relist_start_date'];
				$uwa_relist_end_date = $_POST['uwa_relist_end_date'];
				if(!empty($uwa_relist_start_date) && !empty($uwa_relist_end_date)) {
					$this->uwa_manually_do_relist($post_id, $uwa_relist_start_date,$uwa_relist_end_date);
				}
			}
			
			/* Auto Renew */		
			if(isset($_POST['uwa_auto_renew_enable'])){				
					update_post_meta( $post_id, 'uwa_auto_renew_enable', stripslashes( $_POST['uwa_auto_renew_enable'] ) );
				
				if( isset( $_POST['uwa_auto_renew_recurring_enable']) ) {	
					update_post_meta( $post_id, 'uwa_auto_renew_recurring_enable', stripslashes( $_POST['uwa_auto_renew_recurring_enable'] ) );
				} else {
					delete_post_meta( $post_id, 'uwa_auto_renew_recurring_enable' );
				}
				/* not Paid */
				if( isset( $_POST['uwa_auto_renew_not_paid_enable']) ) {	
					update_post_meta( $post_id, 'uwa_auto_renew_not_paid_enable', esc_attr( $_POST['uwa_auto_renew_not_paid_enable'] ) );					
					
					if( isset( $_POST['uwa_auto_renew_not_paid_hours']) ) {
						update_post_meta( $post_id, 'uwa_auto_renew_not_paid_hours', esc_attr( $_POST['uwa_auto_renew_not_paid_hours'] ) );
					}					
				} else{
					  delete_post_meta( $post_id, 'uwa_auto_renew_not_paid_enable' );
				}
				
				/* No Bid Placed */
				if( isset( $_POST['uwa_auto_renew_no_bids_enable']) ) {	
					update_post_meta( $post_id, 'uwa_auto_renew_no_bids_enable', esc_attr( $_POST['uwa_auto_renew_no_bids_enable'] ) );				
					
					if( isset( $_POST['uwa_auto_renew_fail_hours']) ) {
						update_post_meta( $post_id, 'uwa_auto_renew_fail_hours', esc_attr( $_POST['uwa_auto_renew_fail_hours'] ) );
					}					
				} else{
					  delete_post_meta( $post_id, 'uwa_auto_renew_no_bids_enable' );
				}
				/* Reserve Not met */
				if( isset( $_POST['uwa_auto_renew_no_reserve_enable']) ) {	
					update_post_meta( $post_id, 'uwa_auto_renew_no_reserve_enable', esc_attr( $_POST['uwa_auto_renew_no_reserve_enable'] ) );				
					
					if( isset( $_POST['uwa_auto_renew_reserve_fail_hours']) ) {
						update_post_meta( $post_id, 'uwa_auto_renew_reserve_fail_hours', esc_attr( $_POST['uwa_auto_renew_reserve_fail_hours'] ) );
					}					
				} else{
					  delete_post_meta( $post_id, 'uwa_auto_renew_no_reserve_enable' );
				}	

				if( isset( $_POST['uwa_auto_renew_duration_hours']) ) {	
					update_post_meta( $post_id, 'uwa_auto_renew_duration_hours', esc_attr( $_POST['uwa_auto_renew_duration_hours'] ) );				
									
				}
				
					
			} else {
				delete_post_meta( $post_id, 'uwa_auto_renew_enable' );
				
			}
			
			if (isset($_POST['uwa_auction_variable_bid_increment']) && isset($_POST['uwa_var_inc_val'])){
				if($_POST['uwa_auction_variable_bid_increment']=="yes" && !empty($_POST['uwa_var_inc_val']) && empty($_POST['woo_ua_bid_increment'])){
				
							update_post_meta($post_id, 'uwa_auction_variable_bid_increment', $_POST['uwa_auction_variable_bid_increment']);
							update_post_meta($post_id, 'uwa_var_inc_price_val', $_POST['uwa_var_inc_val']);
							delete_post_meta( $post_id, 'woo_ua_bid_increment' );
				}
			} else {
				delete_post_meta( $post_id, 'uwa_auction_variable_bid_increment' );
			}
						
			/* Selling type */
			/* Note : html static so checkbox checked == on or (blank) */ 
			if(isset($_POST['uwa_auction_selling_type_auction']) && isset($_POST['uwa_auction_selling_type_buyitnow'])) {
				if($_POST['uwa_auction_selling_type_auction'] == "on" && $_POST['uwa_auction_selling_type_buyitnow'] == "on" ){
					update_post_meta( $post_id, 'woo_ua_auction_selling_type', "both"  );
				}
			}
			else if(isset($_POST['uwa_auction_selling_type_auction'])) {
				if($_POST['uwa_auction_selling_type_auction'] == "on"){
					update_post_meta( $post_id, 'woo_ua_auction_selling_type', "auction"  );					
				}				
			}
			else if(isset($_POST['uwa_auction_selling_type_buyitnow'])) {
				if($_POST['uwa_auction_selling_type_buyitnow'] == "on"){
					update_post_meta( $post_id, 'woo_ua_auction_selling_type', "buyitnow"  );					
				}				
			}


			/* ---------- buyers premium  ---------- */

			if(isset($_POST['woo_ua_buyer_level'])){				

				$b_level = $_POST['woo_ua_buyer_level'];
				if($b_level == "product_level"){
						$b_givento = sanitize_text_field($_POST['woo_ua_buyer_given_to']);
						$b_type = sanitize_text_field($_POST['woo_ua_buyer_type']);
						$b_feeamt = $_POST['woo_ua_buyer_fee_amt'];

						update_post_meta($post_id, 'woo_ua_buyer_level', $b_level);
						update_post_meta($post_id, 'woo_ua_buyer_given_to', $b_givento);
						update_post_meta($post_id, 'woo_ua_buyer_type', $b_type);
						delete_post_meta( $post_id, 'woo_ua_buyer_min_amt' );
						delete_post_meta( $post_id, 'woo_ua_buyer_max_amt' );

						if($b_type == "percentage"){
														
							// fee amount
							if($b_feeamt >= 1 && $b_feeamt <= 100) {
								update_post_meta($post_id, 'woo_ua_buyer_fee_amt', $b_feeamt);
							}
							else{
								update_post_meta($post_id, 'woo_ua_buyer_fee_amt', "1");
							}

							// min amount
							if(isset($_POST['woo_ua_buyer_min_amt'])){
								$b_minamt = $_POST['woo_ua_buyer_min_amt'];
								if(!empty($b_minamt)){
									update_post_meta($post_id, 'woo_ua_buyer_min_amt', $b_minamt);
								}
							}

							// max amount
							if(isset($_POST['woo_ua_buyer_max_amt'])){
								$b_maxamt = $_POST['woo_ua_buyer_max_amt'];
								if(!empty($b_maxamt)){
									update_post_meta($post_id, 'woo_ua_buyer_max_amt', $b_maxamt);
								}
							}


						}elseif($b_type == "flat"){							
							update_post_meta($post_id, 'woo_ua_buyer_fee_amt', $b_feeamt);						
						}

				}
				elseif($b_level == "globally"){
					update_post_meta($post_id, 'woo_ua_buyer_level', $b_level);
					delete_post_meta( $post_id, 'woo_ua_buyer_given_to' );
					delete_post_meta( $post_id, 'woo_ua_buyer_type' );
					delete_post_meta( $post_id, 'woo_ua_buyer_fee_amt' );
					delete_post_meta( $post_id, 'woo_ua_buyer_min_amt' );
					delete_post_meta( $post_id, 'woo_ua_buyer_max_amt' );
				}

			}  /* end of buyers premium level */




			/* delete some metadata only when simple, grouped, variable or any product 
				 become auction product during edit product */
			if(isset($_POST['woo_ua_product_type'])) {
				if($_POST['woo_ua_product_type'] != "auction"){
					//delete_post_meta( $post_id, "_sale_price");
				}
			}
	
			
		} /*  end of if - auction */
		else {
					delete_post_meta( $post_id, 'woo_ua_product_condition' );
					delete_post_meta( $post_id, 'woo_ua_opening_price' );
					delete_post_meta( $post_id, 'woo_ua_lowest_price' );
					delete_post_meta( $post_id, 'uwa_auction_proxy' );
					delete_post_meta( $post_id, 'uwa_auction_silent' );
					delete_post_meta( $post_id, 'woo_ua_bid_increment' );
					delete_post_meta( $post_id, 'woo_ua_auction_type' );
					delete_post_meta( $post_id, 'woo_ua_auction_start_date' );
					delete_post_meta( $post_id, 'woo_ua_auction_end_date' );
					delete_post_meta( $post_id, 'woo_ua_auction_has_started' );
					delete_post_meta( $post_id, 'woo_ua_auction_last_activity' );
					delete_post_meta( $post_id, 'woo_ua_auction_closed' );
					delete_post_meta( $post_id, 'woo_ua_auction_fail_reason' );
					delete_post_meta( $post_id, 'woo_ua_order_id' );
					delete_post_meta( $post_id, 'woo_ua_auction_payed' );
					delete_post_meta( $post_id, 'woo_ua_auction_max_bid' );
					delete_post_meta( $post_id, 'woo_ua_auction_max_current_bider' );
					delete_post_meta( $post_id, 'woo_ua_auction_current_bid' );
					delete_post_meta( $post_id, 'woo_ua_auction_current_bider' );
					delete_post_meta( $post_id, 'woo_ua_auction_bid_count' );
					delete_post_meta( $post_id, 'woo_ua_auction_current_bid_proxy' );
					delete_post_meta( $post_id, 'woo_ua_auction_last_bid' );
					delete_post_meta( $post_id, 'uwa_auction_relisted' );
					delete_post_meta( $post_id, 'woo_ua_buy_now' );					
					
					delete_post_meta( $post_id, 'uwa_auto_renew_enable' );
					delete_post_meta( $post_id, 'uwa_auto_renew_recurring_enable' );
					delete_post_meta( $post_id, 'uwa_auto_renew_not_paid_enable' );
					delete_post_meta( $post_id, 'uwa_auto_renew_not_paid_hours' );
					delete_post_meta( $post_id, 'uwa_auto_renew_no_bids_enable' );
					delete_post_meta( $post_id, 'uwa_auto_renew_fail_hours' );
					delete_post_meta( $post_id, 'uwa_auto_renew_no_reserve_enable' );
					delete_post_meta( $post_id, 'uwa_auto_renew_reserve_fail_hours' );
					delete_post_meta( $post_id, 'uwa_auto_renew_duration_hours' );					
					
					delete_post_meta( $post_id, 'woo_ua_auction_extend_time_antisnipping' );
					delete_post_meta( $post_id, 'woo_ua_auction_extend_time_antisnipping_recursive' );
					
					delete_post_meta( $post_id, 'uwa_auction_variable_bid_increment' );
					delete_post_meta( $post_id, 'uwa_var_inc_price_val' );
					delete_post_meta( $post_id, 'woo_ua_auction_selling_type' );
					
				}
	

    }

    /**
	 * Add Metabox for Auction Log/History Section
	 *
	 */
	public function uwa_add_auction_metabox( $product ) {

		$woo_pf = new WC_Product_Factory();
		$woo_prd = $woo_pf->get_product($product->ID);
		if( $woo_prd->get_type() !== 'auction' ) return;

		add_meta_box('uwa-auction-log',
					__( 'Bid History', 'woo_ua' ),
					 array( $this, 'uwa_render_auction_log' ),
					'product',
					'normal',
					'default'
		);
	}

	/**
	 * Add New  Email Setting On WooCommerce Email Setting page
	 *	
	 */
	public function uwa_register_email_classes( $email_classes ) {
           
		   /* User Emails */
            $email_classes['UWA_Email_Place_Bid'] = include(UW_AUCTION_PRO_ADMIN . '/email/class-uwa-email-auction-place-bid.php');	

            $email_classes['UWA_Email_Place_Bid_Admin'] = include(UW_AUCTION_PRO_ADMIN . '/email/class-uwa-email-auction-place-bid-admin.php');

			$email_classes['UWA_Email_Auction_Bid_Overbid'] = include(UW_AUCTION_PRO_ADMIN . '/email/class-uwa-email-auction-bid-overbid.php');
			
			$email_classes['UWA_Email_Auction_Winner'] = include(UW_AUCTION_PRO_ADMIN . '/email/class-uwa-email-auction-winner.php');

			$email_classes['UWA_Email_Auction_Winner_Admin'] = include(UW_AUCTION_PRO_ADMIN . '/email/class-uwa-email-auction-winner-admin.php');
		
			/* Admin	Private Message */
			$email_classes['UWA_Email_Private_Msg'] = include(UW_AUCTION_PRO_ADMIN . '/email/class-uwa-email-private-msg.php');
					
			$email_classes['UWA_Email_Auction_Ending_Soon_Bidder'] = include(UW_AUCTION_PRO_ADMIN . '/email/class-uwa-email-auction-ending-soon-bidder.php');
			
			$email_classes['UWA_Email_Auction_Remind_to_Pay'] = include(UW_AUCTION_PRO_ADMIN . '/email/class-uwa-email-auction-remind-to-pay.php');
			
			$email_classes['UWA_Email_Auction_Relist'] = include(UW_AUCTION_PRO_ADMIN . '/email/class-uwa-email-auction-relist.php');
			
			$email_classes['UWA_Email_Auction_Bid_Delete'] = include(UW_AUCTION_PRO_ADMIN . '/email/class-uwa-email-auction-bid-delete.php');
			
			$email_classes['UWA_Email_Auction_Watchlist'] = include(UW_AUCTION_PRO_ADMIN . '/email/class-uwa-email-auction-watchlist.php');

			$uwa_proxy = get_option('uwa_proxy_bid_enable', 'no');
			$uwa_silent = get_option('uwa_silent_bid_enable', 'no');
			if($uwa_proxy =="yes"  || $uwa_silent == "yes"){
				$email_classes['UWA_Email_Auction_Loser_Bidders'] = include(UW_AUCTION_PRO_ADMIN . '/email/class-uwa-email-auction-loser-bidders.php');
			}
			
             return $email_classes;
    }

    /**
	 * Create local Email Template for email setting
	 *
	 */		
	public function uwa_locate_core_template( $core_file, $template, $template_base ) {

            $custom_template = array(
                
				/* HTML Email  Bidder(User) */
                'emails/placed-bid.php',
                'emails/placed-bid-admin.php',               
                'emails/bid-outbided.php', 
				'emails/auction-winner.php',
				'emails/auction-winner-admin.php',
				'emails/ending-soon-bidder.php',
				'emails/auction-remind-to-pay.php',				
				/* HTML Email  Admin(Administrator) */
				'emails/auction-private-msg.php',				
				'emails/auction-relist.php',
				'emails/bid-deleted.php', 
				'emails/loser-bidders.php', 
				'emails/watchlist.php', 
				

                /* Plain Email Bidder(User) */
                'emails/plain/placed-bid.php',
                'emails/plain/placed-bid-admin.php',
                'emails/plain/bid-outbided.php',
                'emails/plain/auction-winner.php',
                'emails/plain/auction-winner-admin.php',
                'emails/plain/ending-soon-bidder.php',
                'emails/plain/auction-remind-to-pay.php',
                'emails/plain/auction-relist.php',
                'emails/plain/bid-deleted.php',
                'emails/plain/loser-bidders.php',
                'emails/plain/watchlist.php', 


				/* Plain Email Admin(Administrator) */	                
            );

            if ( in_array( $template, $custom_template ) ) {
                $core_file = UW_AUCTION_PRO_WC_TEMPLATE . $template;
            }
            return $core_file;
    }

    /**
	 * Auction Filter On Product list Page
	 *	 
	 */
	function admin_uwa_filter_restrict_manage_posts() {
		/* Drop down list for auction  */
		if (isset($_GET['post_type']) && $_GET['post_type'] == 'product') {
			$filter_values = array(
				__('Live Auction', 'woo_ua') => 'live',
				__('Future Auction', 'woo_ua') => 'scheduled',
				__('Expired Auction', 'woo_ua') => 'expired',
				__('Fail Auction', 'woo_ua') => 'fail',
				__('Sold Auction', 'woo_ua') => 'sold',
				__('Paid Auction', 'woo_ua') => 'payed',
			);
			?>
	        <select name="uwa_filter">
	        	<option value=""><?php _e('Auction filter By ', 'woo_ua');?></option>
	        	<?php
                $current_filter = isset($_GET['uwa_filter']) ? $_GET['uwa_filter'] : '';
                foreach ($filter_values as $label => $value) {
                    printf ( '<option value="%s"%s>%s</option>',$value, $value == $current_filter ? ' selected="selected"' : '', $label );
                }
            	?>
	        </select>
	        <?php
        }
	}

	/**
	 * If submitted filter by post meta
	 *
	 * make sure to change META_KEY to the actual meta key
	 * and POST_TYPE to the name of your custom post type
	 *
	 */
	function admin_uwa_filter($query) {				
		global $pagenow;	
		
		if (isset($_GET['post_type']) && $_GET['post_type'] == 'product' && is_admin() && $pagenow == 'edit.php' && isset($_GET['uwa_filter']) && $_GET['uwa_filter'] != '') {

			$taxquery = $query->get('tax_query');
			if (!is_array($taxquery)) {
				$taxquery = array();
			}

			$taxquery[] =
			array(
				'taxonomy' => 'product_type',
				'field' => 'slug',
				'terms' => 'auction',
			);

			$query->set('tax_query', $taxquery);
			
			switch ($_GET['uwa_filter']) {
				case 'live':
					$query->query_vars['meta_query'] = array(
						array(
								'key'     => 'woo_ua_auction_closed',
								'compare' => 'NOT EXISTS',
						),

						array(
								'key'     => 'woo_ua_auction_has_started',
								'value' => '1',
						)
					);
					break;

				case 'expired':
					$query->query_vars['meta_query'] = array(
						array(
							'key' => 'woo_ua_auction_closed',
							'value' => array('1','2','3','4'),
							'compare' => 'IN',
						),
					);
					break;

				case 'scheduled':
					$query->query_vars['meta_query'] = array(
						array(
								'key'     => 'woo_ua_auction_closed',
								'compare' => 'NOT EXISTS',
						),
						array(
								'key'     => 'woo_ua_auction_started',
								'value' => '0',
						)
					);
					break;

				case 'fail':
					$query->query_vars['meta_key'] = 'woo_ua_auction_closed';
					$query->query_vars['meta_value'] = '1';
					break;

				case 'sold':
					$query->query_vars['meta_query'] = array(
						array(
							'key' => 'woo_ua_auction_closed',
							'value' => '3',
						),

						array(
							'key'     => 'woo_ua_auction_payed',
							'compare' => 'NOT EXISTS',
						)
					);
					break;

				case 'payed':
					$query->query_vars['meta_key'] = 'woo_ua_auction_payed';
					$query->query_vars['meta_value'] = '1';
					break;
			}
			
		}
	}

	/**
	 * Auction Product  paid for	
	 *
	 */
	function uwa_auction_payed($order_id) {

		$order = wc_get_order($order_id);
		if ($order) {
			$order_items = $order->get_items();

			if ($order_items) {
				foreach ($order_items as $item_id => $item) {
					$item_meta 	= method_exists( $order, 'wc_get_order_item_meta' ) ? $order->wc_get_order_item_meta( $item_id ) : $order->get_item_meta( $item_id );
					$product_data = wc_get_product($item_meta["_product_id"][0]);
					if (method_exists( $product_data, 'get_type') && $product_data->get_type() == 'auction') {
							update_post_meta($item_meta["_product_id"][0], 'woo_ua_auction_payed', 1, true);
							update_post_meta($item_meta["_product_id"][0], 'woo_ua_order_id', $order_id, true);                                       
					}
				}
			}
		}
	}

	/**
	 * Function When Order Cancel by user
	 *		 
	 */
	function uwa_auction_order_canceled($order_id) {
		$order = wc_get_order($order_id);

		if ($order) {
			$order_items = $order->get_items();

			if ($order_items) {

				foreach ($order_items as $item_id => $item) {

					$item_meta 	= method_exists( $order, 'wc_get_order_item_meta' ) ? $order->wc_get_order_item_meta( $item_id ) : $order->get_item_meta( $item_id );
					$product_data = wc_get_product($item_meta["_product_id"][0]);
					if (method_exists( $product_data, 'get_type') && $product_data->get_type() == 'auction') {
							delete_post_meta($item_meta["_product_id"][0], 'woo_ua_auction_payed');
					}
				}	
			}
		} 
	}

	/**
	 * Auction Product Order
	 *			 
	 */
	function uwa_auction_order($order_id, $posteddata) {

		$order = wc_get_order($order_id);

		if ($order) {

			$order_items = $order->get_items();

			if ($order_items) {

				foreach ($order_items as $item_id => $item) {
					$item_meta 	= method_exists( $order, 'wc_get_order_item_meta' ) ? $order->wc_get_order_item_meta( $item_id ) : $order->get_item_meta( $item_id );
					$product_data = wc_get_product($item_meta["_product_id"][0]);
					if (method_exists( $product_data, 'get_type') && $product_data->get_type() == 'auction') {
							update_post_meta($order_id, '_auction', '1');
							update_post_meta($item_meta["_product_id"][0], 'woo_ua_order_id', $order_id, 
							true);

						if (!$product_data->is_uwa_completed()) {
							update_post_meta($item_meta["_product_id"][0], 'woo_ua_auction_closed', '3');
							update_post_meta($item_meta["_product_id"][0], 'woo_ua_buy_now', '1');
							update_post_meta($item_meta["_product_id"][0], 'woo_ua_auction_end_date', date('Y-m-d h:s'));
							
						}
					}
				}
			}
		}
	}

	/**
	 * Ajax delete bid
	 *
	 * Function for deleting bid in wp admin	
	 * @param  array
	 * @return string
	 *
	 */
	function wp_ajax_admin_cancel_bid() {
		global $wpdb;

		if (!current_user_can('edit_product', $_POST["postid"])) {
			die();
		}
		if ($_POST["postid"] && $_POST["logid"]) {
			$product_data = wc_get_product($_POST["postid"]);
			$log_table = $wpdb->prefix . "woo_ua_auction_log";
			$history = $wpdb->get_row($wpdb->prepare("SELECT * FROM {$log_table} WHERE id = %d", $_POST["logid"]));

			if (!is_null($history)) {

				/* Get data for delete bid mail before it's deleted */
				//$postid = $_POST["postid"];
				$auctionid = absint($_POST["postid"]);
				$logid = absint($_POST["logid"]);
				$userid = $history->userid;
				$deletedbid = $history->bid;
				
				if ($product_data->get_uwa_auction_type() == 'normal') {

					if (($history->bid == $product_data->get_uwa_auction_current_bid()) && ($history->userid == 
						$product_data->get_uwa_auction_current_bider())) {
						
						if ($product_data->get_uwa_auction_silent() == 'yes') {
							
							/* query for slient auction */	
							$newbid = $wpdb->get_row($wpdb->prepare("SELECT * FROM {$log_table} WHERE auction_id = %d and id != %d and 
								bid = (SELECT MAX(bid) FROM {$log_table} WHERE auction_id = %d and id != %d ) ORDER BY date ASC LIMIT 1", 
								$auctionid, $logid, $auctionid, $logid ));
						}
						else{

							/* query for simple and proxy auction */
							$newbid = $wpdb->get_row($wpdb->prepare("SELECT * FROM {$log_table} WHERE auction_id = %d  
								ORDER BY date DESC, bid DESC LIMIT 1, 1", $auctionid));
						}						

						if (!is_null($newbid)) {
							update_post_meta($auctionid, 'woo_ua_auction_current_bid', $newbid->bid);
							update_post_meta($auctionid, 'woo_ua_auction_current_bider', $newbid->userid);
							delete_post_meta($auctionid, 'woo_ua_auction_max_bid');
							delete_post_meta($auctionid, 'woo_ua_auction_max_current_bider');
							$new_max_bider_id =  $newbid->userid;
							
							
							/* send mail to winner only when auction is expired and there */

							if($product_data->get_uwa_auction_expired() == '2'){
								update_user_meta($newbid->userid, 'woo_ua_auction_win', $auctionid);
								delete_post_meta($auctionid, 'woo_ua_winner_mail_sent');
								WC()->mailer();								
								$mail_sent = get_post_meta($auctionid, "woo_ua_winner_mail_sent", true);
								if ( $mail_sent !='1' ) { 

									/* re-calculate buyer's premium because winner is changed and 
									   winner mail includes buyers premium info */ 

									$addons = uwa_enabled_addons();
									if(is_array($addons) && in_array('uwa_buyers_premium_addon', $addons)){							
										$buyer_premium_amt = uwa_get_buyer_premium_value($auctionid, $newbid->bid);
										if($buyer_premium_amt > 0){
											update_post_meta($auctionid, '_uwa_buyer_premium_amt', $buyer_premium_amt);
										}
									}

									do_action('woo_ua_auctions_won_email_bidder', $auctionid, $newbid->userid);
									do_action('woo_ua_auctions_won_email_bidder_admin', $auctionid, $newbid->userid);
								}								
								if( $product_data->get_uwa_auction_proxy()=="yes" || $product_data->get_uwa_auction_silent() == "yes" ) {
					
									do_action('woo_ua_auctions_loser_email_bidder', $auctionid ,$newbid->userid);	
							
								}
								update_post_meta( $auctionid, 'woo_ua_winner_mail_sent', '1');
							}
							
						} else {
							delete_post_meta($auctionid, 'woo_ua_auction_current_bid');
							delete_post_meta($auctionid, 'woo_ua_auction_current_bider');
							delete_post_meta($auctionid, 'woo_ua_auction_max_bid');
							delete_post_meta($auctionid, 'woo_ua_auction_max_current_bider');
							$new_max_bider_id = false;
						}
						$wpdb->query($wpdb->prepare("DELETE FROM {$log_table} WHERE id = %d", $_POST["logid"]));
						update_post_meta($auctionid, 'woo_ua_auction_bid_count', intval($product_data->get_uwa_auction_bid_count() - 1));
						do_action('ultimate_woocommerce_auction_delete_bid', array('product_id' => $auctionid, 
							'delete_user_id' => $history->userid, 'new_max_bider_id ' => $new_max_bider_id ));
							
						$response['status'] = 1;
						$response['success_message'] = __('Bid Deleted Successfully', 'woo_ua');

					} 
					else {

						$wpdb->query($wpdb->prepare("DELETE FROM {$log_table} WHERE id = %d", $_POST["logid"]));
						update_post_meta($auctionid, 'woo_ua_auction_bid_count', intval($product_data->get_uwa_auction_bid_count() - 1));
						
						$response['status'] = 1;
						$response['success_message'] = __('Bid Deleted Successfully', 'woo_ua');
					}

				} 
				elseif($product_data->get_uwa_auction_type() == 'reverse'){
					
					if (( $history->bid == $product_data->get_uwa_auction_current_bid() ) && ( $history->userid == 
						$product_data->get_uwa_auction_current_bider() )) {
						
						
						if ($product_data->get_uwa_auction_silent() == 'yes') {

							/* query for slient auction */
							$newbid = $wpdb->get_row($wpdb->prepare("SELECT * FROM {$log_table} WHERE auction_id = %d and id != %d 
								and bid = (SELECT MIN(bid) FROM {$log_table} WHERE auction_id = %d and id != %d) ORDER BY date ASC 
								LIMIT 1", $auctionid, $logid, $auctionid, $logid ));
														
						}
						else {

							/* query for simple and proxy auction */
							$newbid = $wpdb->get_row($wpdb->prepare("SELECT * FROM {$log_table} WHERE auction_id = %d
								ORDER BY date DESC, bid ASC LIMIT 1, 1", $auctionid));
						}
						
				
						if ( ! is_null( $newbid ) ) {
							update_post_meta( $auctionid, 'woo_ua_auction_current_bid', $newbid->bid );
							update_post_meta( $auctionid, 'woo_ua_auction_current_bider', $newbid->userid );
							delete_post_meta( $auctionid, 'woo_ua_auction_max_bid' );
							delete_post_meta( $auctionid, 'woo_ua_auction_max_current_bider' );
							$new_max_bider_id = $newbid->userid;
							
							/* send mail to winner only when auction is expired and there */

							if($product_data->get_uwa_auction_expired() == '2'){
								update_user_meta($newbid->userid, 'woo_ua_auction_win', $auctionid);
								delete_post_meta($auctionid, 'woo_ua_winner_mail_sent');
								WC()->mailer();
								$mail_sent = get_post_meta($auctionid, "woo_ua_winner_mail_sent", true);
								if ( $mail_sent !='1' ) {  
									  do_action('woo_ua_auctions_won_email_bidder', $auctionid ,$newbid->userid);
									  do_action('woo_ua_auctions_won_email_bidder_admin', $auctionid ,$newbid->userid);
								}								
								if( $product_data->get_uwa_auction_proxy()=="yes" || $product_data->get_uwa_auction_silent() == "yes" ) {
					
									do_action('woo_ua_auctions_loser_email_bidder', $auctionid ,$newbid->userid);	
							
								}
								update_post_meta( $auctionid, 'woo_ua_winner_mail_sent', '1');
							}
						} 
						else {
							delete_post_meta( $auctionid, 'woo_ua_auction_current_bid' );
							delete_post_meta( $auctionid, 'woo_ua_auction_current_bider' );
							$new_max_bider_id = false;										
						}
							
						$wpdb->query($wpdb->prepare("DELETE FROM {$log_table} WHERE id = %d", $_POST['logid'] ));
						update_post_meta( $auctionid, 'woo_ua_auction_bid_count', intval( $product_data->get_uwa_auction_bid_count() - 1 ));
						do_action('ultimate_woocommerce_auction_delete_bid', 
							array(	'product_id' => $auctionid,
									'delete_user_id' => $history->userid, 
									'new_max_bider_id ' => $new_max_bider_id 
							));
									
						$response['status'] = 1;
					    $response['success_message'] = __('Bid Deleted Successfully', 'woo_ua');
						
					}
					else {
							$wpdb->query( $wpdb->prepare("DELETE FROM {$log_table} WHERE id = %d", $_POST['logid'] ));
							
							update_post_meta( $auctionid, 'woo_ua_auction_bid_count', 
								intval( $product_data->get_uwa_auction_bid_count() - 1 ) );
								
							do_action('ultimate_woocommerce_auction_delete_bid',
									array(
										'product_id' => $auctionid,
										'delete_user_id' => $history->userid,
									));
							
							$response['status'] = 1;
							$response['success_message'] = __('Bid Deleted Successfully','woo_ua');
					}				
					
					
				} /* end of elseif - reverse */

				/* Send delete bid mail */
				WC()->mailer();
				do_action('uwa_pro_delete_bid_email', $auctionid, $userid, $deletedbid);

				
			} /* end of if - is null history */

		} 
		else {
			$response['status'] = 0;
			$response['error_message'] = __('Bid Not Deleted', 'woo_ua');
		}

		echo json_encode( $response );
		exit;
	}

	/**
	 * Ajax End Auction
	 *
	 * Function for deleting bid in wp admin	
	 * @param  array
	 * @return string
	 *
	 */	
	function uwa_admin_force_end_now_callback() {
		global $wpdb;
		$end_time = get_uwa_now_date();		
		if (!current_user_can('edit_product', $_POST["postid"])) {
				die();
		}	

		if (!empty($_POST["postid"])) {	
			$product_id = absint($_POST["postid"]);
			$product_data = wc_get_product( wc_clean( $product_id ) );
			$closed_auction = $product_data->get_uwa_auction_expired();
			if (!empty($closed_auction)){

				die(); /* Auction Already Ended */

			}else {	
			
				$started_auction = $product_data->is_uwa_live();
			    $finished_auction = $product_data->is_uwa_completed();				
				$current_bid = $product_data->get_uwa_auction_current_bid();
				$current_bider = $product_data->get_uwa_auction_current_bider();			
				if ($started_auction === TRUE){					
					update_post_meta($product_id,'woo_ua_auction_end_date',$end_time);
						
					if ( !$current_bider && !$current_bid){					
						update_post_meta( $product_id, 'woo_ua_auction_closed', '1');
						update_post_meta( $product_id, 'woo_ua_auction_fail_reason', '1');	
					}				
					elseif ( $product_data->is_uwa_reserve_met() === FALSE){
						if($product_data->get_uwa_auction_proxy() != "yes" ){	
							update_post_meta( $product_id, 'woo_ua_auction_closed', '1');
							update_post_meta( $product_id, 'woo_ua_auction_fail_reason', '2');
						}
					}
					else {

						/* maxbid >= reserve price then set maxbidder is winner */
						/*if ($product_data->is_uwa_reserve_met() === FALSE){
							if($product_data->get_uwa_auction_proxy() == "yes" ){
								$maxbid = $product_data->get_uwa_auction_max_bid();
									if($maxbid > 0){
										$maxbid_user = $product_data->get_uwa_auction_max_current_bider();
										$reserved_price = $product_data->get_uwa_auction_reserved_price();

										if($maxbid >= $reserved_price){
											update_post_meta( $product_id, 'woo_ua_auction_current_bid', $maxbid);
											update_post_meta( $product_id, 'woo_ua_auction_current_bider', $maxbid_user);
										}
									}
							}	
						}

							$current_bid = $product_data->get_uwa_auction_current_bid();
							$current_bider = $product_data->get_uwa_auction_current_bider();*/

							/* insert maxbid as bid to log table */
							/*$bid_obj = new UWA_Bid;
							$bid_inserted = $bid_obj->history_bid($product_id, $maxbid, get_userdata($maxbid_user), 1);
							if($bid_inserted){
								$bid_count = (int)$product_data->get_uwa_auction_bid_count() + 1;
								update_post_meta($product_id, 'woo_ua_auction_bid_count', $bid_count);
							}*/


						update_post_meta($product_id, 'woo_ua_auction_closed', '2');
						add_user_meta($current_bider, 'woo_ua_auction_win', $product_id);
						do_action('ultimate_woocommerce_auction_close',  $product_id);
					
						if($current_bider){
							WC()->mailer();

							/* winner sms */
							do_action('ultimate_woocommerce_auction_winner_sms',  $product_id);
							
							$call_autodabit = get_post_meta($product_id, "woo_ua_winner_request_sent_for_autodabit_payment", true);
							if ( $call_autodabit !='1' ){
								add_post_meta($product_id, 'woo_ua_winner_request_sent_for_autodabit_payment','1');
								do_action('ultimate_woocommerce_auction_autodabit_payment',  $product_id);
							}
							
							$mail_sent = get_post_meta($product_id, "woo_ua_winner_mail_sent", true);
							if ( $mail_sent !='1' ) {  
							  do_action('woo_ua_auctions_won_email_bidder', $product_id ,$current_bider);
							  do_action('woo_ua_auctions_won_email_bidder_admin', $product_id ,$current_bider);
							}
							if( $product_data->get_uwa_auction_proxy()=="yes" || $product_data->get_uwa_auction_silent() == "yes" ) {
					
								do_action('woo_ua_auctions_loser_email_bidder', $product_id ,$current_bider);	
							
							}
							update_post_meta( $product_id, 'woo_ua_winner_mail_sent', '1');
						}					
					}	
				
					$response['status'] = 1;
					$response['success_message'] = __('Auction ended successfully.','woo_ua');
				}				
				else {

					$response['status'] = 0;
					$response['error_message'] = __('Sorry, this auction cannot be ended.','woo_ua');
				}							
				
			}
		 
		}

		echo json_encode( $response );
		exit;		
	}
	
	/**
	 * Ajax End Auction
	 *
	 * Function for deleting bid in wp admin	
	 * @param  array
	 * @return string
	 *
	 */	
	function uwa_admin_force_make_live_now_callback () {
		global $wpdb;	
		$nowdate_for_start = get_uwa_now_date();			
		if (!current_user_can('edit_product', $_POST["auction_id"])) {
				die();
		}

		if (!empty($_POST["auction_id"])) {	
			$product_id = absint($_POST["auction_id"]);
			$product_data = wc_get_product( wc_clean( $product_id ) );
			$started_auction = $product_data->is_uwa_live();
		 	if (!empty($started_auction)){
				$response['status'] = 0;
				$response['error_message'] = __('Auction Already Live.','woo_ua');
			}else {
				update_post_meta($product_id, 'woo_ua_auction_start_date', $nowdate_for_start);
				update_post_meta($product_id, 'woo_ua_auction_has_started', "1");
				delete_post_meta($product_id, "woo_ua_auction_started");
				$response['status'] = 1;
				$response['success_message'] = __('Auction Live successfully.','woo_ua');
			}
		}	
		echo json_encode( $response );
		exit;		
	}	

	/**
	 * Duplicate product
	 *
	 * Clear metadata when copy auction
	 * @param  array
	 * @return string
	 *
	 */
	function uwa_woocommerce_duplicate_product($postid) {

		$product = wc_get_product($postid);

			if (!$product) {
				return FALSE;
			}

			if (!(method_exists( $product, 'get_type') && $product->get_type() == 'auction') ) {
				return FALSE;
			}

			delete_post_meta($postid, 'woo_ua_auction_end_date');
			delete_post_meta($postid, 'woo_ua_auction_start_date');
			delete_post_meta($postid, 'woo_ua_auction_current_bid');
			delete_post_meta($postid, 'woo_ua_auction_current_bider');
			delete_post_meta($postid, 'woo_ua_auction_bid_count');			
			delete_post_meta($postid, 'woo_ua_winner_mail_sent');
			delete_post_meta($postid, 'woo_ua_auction_has_started');
			delete_post_meta($postid, 'woo_ua_auction_closed');
			delete_post_meta($postid, 'woo_ua_auction_started');			
			delete_post_meta($postid, 'woo_ua_auction_max_bid');			
			delete_post_meta($postid, 'woo_ua_auction_max_current_bider');
			delete_post_meta($postid, 'woo_ua_auction_current_bid_proxy' );
			delete_post_meta($postid, 'woo_ua_auction_last_bid' );
			delete_post_meta($postid, 'woo_ua_auction_fail_reason');
			delete_post_meta($postid, 'woo_ua_auction_payed');
			delete_post_meta($postid, 'woo_ua_order_id');	
			delete_post_meta($postid, '_stock_status');				
			delete_post_meta($postid, 'woo_ua_auction_extend_time_antisnipping');
			delete_post_meta($postid, 'uwa_auction_relisted');
			delete_post_meta($postid, 'uwa_number_of_sent_mails');
			delete_post_meta($postid, 'uwa_dates_of_sent_mails');
			delete_post_meta($postid, 'uwa_auction_stop_mails');
			delete_post_meta($postid, 'woo_ua_auction_watch');
			delete_post_meta($postid, 'woo_ua_auction_last_activity');			
			update_post_meta($postid, '_stock_status', 'instock');
			
			//buyers premium fields
			delete_post_meta($postid, '_uwa_buyer_premium_amt'); 			
			//update_post_meta($postid, '_uwa_buyer_premium_amt');


			//auto debit fields
			delete_post_meta($postid, '_uwa_stripe_auto_debit_total_amt');
			delete_post_meta($postid, '_uwa_stripe_auto_debit_amt');
			delete_post_meta($postid, '_uwa_stripe_auto_debit_bpm_amt');
			delete_post_meta($postid, '_uwa_stripe_auto_debit_status');
			delete_post_meta($postid, '_uwa_stripe_auto_debit_date');
			delete_post_meta($postid, '_done_one_time_payment');
			delete_post_meta($postid, 'woo_ua_winner_request_sent_for_autodabit_payment');


			//auction staus			
			delete_post_meta($postid, '_done_one_time_sms');			
			delete_post_meta($postid, '_uwa_won_sms_sent_status');

			return TRUE;
	}

	/**
	 * Show pricing fields for Action product.
	 *
	 */
	function uwa_auction_custom_js() {

		if ( 'product' != get_post_type() ) :
			return;
		endif;

		?>
		<script type='text/javascript'>
			jQuery( document ).ready( function() {
				jQuery( '.inventory_tab' ).addClass( 'show_if_auction' ).show();
				
			});

		</script>
		<?php

	}

	/**
	 * Add New Column In Product list in admins side.
	 *		
	 * @param  array
	 * @return string
	 *
	 */
	function uwa_auctions_status_columns( $columns_array ) {
	 
		/* I want to display Brand column just after the product name column */
		$auction_status_columns = __('Auction Status','woo_ua'); 
		return array_slice( $columns_array, 0, 5, true )
			+ array( 'admin_auction_status' => $auction_status_columns )
			+ array_slice( $columns_array, 5, NULL, true );	 
	} 

	/**
	 * Add New Column Data In Product list in admins side.
	 *		 
	 * @param  array
	 * @return string
	 *
	 */
	function uwa_auctions_status_columns_status( $column, $postid ) {
		global $woocommerce, $post;

		if( $column  == 'admin_auction_status' ) {
			$product_data = wc_get_product($postid);

			if( $product_data->get_type() == 'auction') {
				$closed = $product_data->is_uwa_expired();				
				$started = $product_data->is_uwa_live();
				$failed = $product_data->get_uwa_auction_fail_reason();
				if($closed === FALSE && $started === TRUE){ ?>				
						<span style="color:#7ad03a;font-size:18px"><?php _e('Live', 'woo_ua')?></span>
					<?php 
				} elseif($closed === FALSE && $started === FALSE){ ?>					
						<span style="color:orange;font-size:18px"><?php _e('Future', 'woo_ua')?></span>
						</br><span style="color:#0073aa;font-size:10px"><?php _e('Not Started', 'woo_ua')?></span>
					<?php 
				} else { ?>				
					   <span style="color:red;font-size:18px"><?php _e('Expired', 'woo_ua')?></span>
					   
						<?php if ($product_data->get_uwa_auction_expired() == '3') { ?>
						
							</br><span style="color:#0073aa;font-size:10px"><?php _e('Sold', 'woo_ua')?></span>
							<?php 
						} elseif ($product_data->get_uwa_auction_fail_reason() == '1') { ?>
						
							</br><span style="color:#0073aa;font-size:10px"><?php _e('No Bid', 'woo_ua')?></span>
					
				    		<?php 
						} elseif ($product_data->get_uwa_auction_fail_reason() == '2') { ?>
				
							</br><span style="color:#0073aa;font-size:10px"><?php _e('Reserve Not Met', 'woo_ua')?></span>
					
							<?php 
						} else { ?>				
							</br><span style="color:#0073aa;font-size:10px"><?php _e('Won', 'woo_ua')?></span>			
							<?php
						
						} /* end of else */

				} /* end of else */ /* main Expired */
				
			} /* end of if - auction */

		} /* end of if - admin-auction-status */
	}

	public function uwa_manage_auction_page_admin_notice() { 
	
		if( isset( $_GET[ 'page' ] )  AND  $_GET[ 'page' ] == "uwa_general_setting"  AND  isset($_GET['setting_section' ])) {

			if($_GET[ 'setting_section' ] == "uwa_manage_auctions"){
		
				$products_page_url = admin_url('edit.php?post_type=product'); ?>	
		    
				<div class="notice notice-warning is-dismissible">
		      		<p><?php _e( 'You can Manage All Auctions via Products List <a href="'.$products_page_url.'" target="blank" >  Click Here.</a>', 'woo' ); ?></p>  
			  	</div>
		    	<?php  	
		    }
		}	
		
	} /* end of fuction */  
	
	/**
	 * Hide Attributes data panel.
	 *	
	 */
	public  function uwa_hide_attributes_data_panel($tabs) {        
        return $tabs;
    }
	
	function uwa_manually_do_relist($auction_id, $uwa_relist_start_date, $uwa_relist_end_date) {
		
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

		//delete_post_meta($auction_id, '_done_one_time_payment');

		delete_post_meta($auction_id, '_done_one_time_sms');
		

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
			delete_post_meta($auction_id, 'woo_ua_auction_payed' );
			delete_post_meta($auction_id, 'woo_ua_winner_mail_sent' );
			delete_post_meta($auction_id, 'woo_ua_auction_current_bid_proxy' );
			delete_post_meta($auction_id, 'woo_ua_auction_last_bid' );
			
			$order_id = get_post_meta($auction_id, 'woo_ua_order_id', true);				
			if (!empty($order_id)) {
				$order = wc_get_order($order_id);
				$order->update_status('failed', __('Failed Relist', 'woo_ua'));
				delete_post_meta($auction_id, 'woo_ua_order_id');
			}
			
			/*user meta delete*/
		    $wpdb->delete(
					$wpdb->usermeta,
					array(
						'meta_key'   => 'woo_ua_auction_win',
						'meta_value' => $auction_id,
					),
					array( '%s', '%s' )
			);
			
		    $uwa_auto_renew_recurring_enable = get_post_meta( $auction_id, 'uwa_auto_renew_recurring_enable', true );
			if($uwa_auto_renew_recurring_enable !="yes"){
				
			   delete_post_meta($auction_id, 'uwa_auto_renew_enable' );
			   delete_post_meta($auction_id, 'uwa_auto_renew_recurring_enable' );
			   
			   delete_post_meta($auction_id, 'uwa_auto_renew_not_paid_enable' );
			   delete_post_meta($auction_id, 'uwa_auto_renew_not_paid_hours' );
			   
			   delete_post_meta($auction_id, 'uwa_auto_renew_no_bids_enable' );			   
			   delete_post_meta($auction_id, 'uwa_auto_renew_fail_hours' );
			   
			   delete_post_meta($auction_id, 'uwa_auto_renew_no_reserve_enable' );
			   delete_post_meta($auction_id, 'uwa_auto_renew_reserve_fail_hours' );		   
			   delete_post_meta($auction_id, 'uwa_auto_renew_duration_hours' );			   
			   
			}				
			
		}elseif($uwa_relist_options ==="uwa_relist_start_from_end"){			
			/* delete_post_meta($auction_id, 'woo_ua_auction_closed'); */
			/* delete_post_meta($auction_id, 'woo_ua_auction_fail_reason'); */
		}
		
		
		/* if auction is relisted then send mail to bidders and admin */
		if(metadata_exists('post', $auction_id, 'uwa_auction_relisted' )){			
			do_action( 'uwa_pro_auction_relist_email', $auction_id );
			/* now delete fail reason and auction closed meta keys..if it delete earlier then
			relist reasons could not get */
			delete_post_meta($auction_id, 'woo_ua_auction_closed');
			delete_post_meta($auction_id, 'woo_ua_auction_fail_reason');			
		}

		
		/* delete bids data only if auction is relisted from beginning */
		if($uwa_relist_options == "uwa_relist_start_from_beg"){
			/* delete from auction log table */
			$table = $wpdb->prefix."woo_ua_auction_log";
			$bids_deleted =  $wpdb->query($wpdb->prepare("DELETE FROM {$table} WHERE auction_id = %d", $auction_id));
		}

	}

	/**
	 * Callback for adding a meta box to the product editing screen used in uwa_render_auction_log
	 *
	 */
	function uwa_render_auction_log() {
		global $woocommerce, $post;
		$datetimeformat = get_option('date_format').' '.get_option('time_format');
		$product_data = wc_get_product($post->ID); ?>
			<?php
			$uwa_auction_relisted = $product_data->get_uwa_auction_relisted();
			if ( ! empty( $uwa_auction_relisted ) ) {
			?>
			<p><?php _e( 'Auction has been relisted on:', 'woo_ua' ); ?> <?php echo mysql2date($datetimeformat ,$uwa_auction_relisted)?> </p>
			<?php } ?>
		<?php if (($product_data->is_uwa_expired() === TRUE) and ($product_data->is_uwa_live() === TRUE)): ?>				
				<p><?php _e('Auction has expired', 'woo_ua')?></p>
				
				<?php if ($product_data->get_uwa_auction_fail_reason() == '1') { ?>
				
						<p><?php _e('Auction Expired without any bids.', 'woo_ua')?></p>
					
				<?php } elseif ($product_data->get_uwa_auction_fail_reason() == '2') { ?>
				
						<p><?php _e('Auction Expired without reserve price met', 'woo_ua')?></p>
							
						<!--<a class="removereserve" href="#" data-postid="<?php echo $post->ID;?>">
						<?php _e('Remove Reserve Price', 'woo_ua'); ?> </a>	-->
					
				<?php }
				
				if ($product_data->get_uwa_auction_expired() == '3') {?>
				
					<p><?php _e('This Auction Product has been sold for buy now price', 'woo_ua')?>: <span><?php echo wc_price($product_data->get_regular_price()) ?></span></p>
					<?php 
					$order = wc_get_order( $product_data->get_uwa_order_id() );
								if ( $order ){
									$order_status = $order->get_status() ? $order->get_status() : __('unknown', 'woo_ua');?>
									<p><?php _e('Order has been made, order status is', 'woo_ua')?>: <a href='post.php?&action=edit&post=<?php echo $product_data->get_uwa_order_id() ?>'><?php echo $order_status ?></a><span>
								<?php }
				  } elseif ($product_data->get_uwa_auction_current_bider()) {?>
				
						<?php
							$current_bidder = $product_data->get_uwa_auction_current_bider();
						?>

						<p><?php _e('Highest bidder was', 'woo_ua')?>: <span class="maxbider"><a href='<?php echo get_edit_user_link($current_bidder)?>'><?php   echo uwa_user_display_name($current_bidder); ?></a></span></p>
						
						<p><?php _e('Highest bid was', 'woo_ua')?>: <span class="maxbid" ><?php echo wc_price($product_data->get_uwa_current_bid()) ?></span></p>

						<?php if ($product_data->get_uwa_auction_payed()) {?>
					
							<p><?php _e('Order has been paid, order ID is', 'woo_ua')?>: <span><a href='post.php?&action=edit&post=<?php echo $product_data->get_uwa_order_id() ?>'><?php echo $product_data->get_uwa_order_id() ?></a></span></p>
							
						<?php } elseif ($product_data->get_uwa_order_id()) {
						
								$order = wc_get_order( $product_data->get_uwa_order_id() );
								if ( $order ){
									$order_status = $order->get_status() ? $order->get_status() : __('unknown', 'woo_ua');?>
									<p><?php _e('Order has been made, order status is', 'woo_ua')?>: <a href='post.php?&action=edit&post=<?php echo $product_data->get_uwa_order_id() ?>'><?php echo $order_status ?></a><span>
								<?php }
						}?>
						
							
							<?php if ($product_data->get_uwa_stripe_auto_debit_bid_amt()) {?>					
							
								<p><?php _e('Bid won auto debit amount', 'woo_ua')?> : <?php echo wc_price($product_data->get_uwa_stripe_auto_debit_bid_amt()) ?></p>	
								
							<?php }?>
							
							<?php if ($product_data->get_uwa_stripe_auto_debit_bpm_amt()) {?>					
							
								<p><?php _e("Buyer's Premium Auto Debit", 'woo_ua')?> : <?php echo wc_price($product_data->get_uwa_stripe_auto_debit_bpm_amt()) ?></p>									
							<?php }?>
							
							<?php if ($product_data->get_uwa_stripe_auto_debit_total_amt()) {?>					
							
								<p><?php _e('Total Auto Debit', 'woo_ua')?> : <?php echo wc_price($product_data->get_uwa_stripe_auto_debit_total_amt()) ?></p>	
								
							<?php }?>
							
						
				<?php }?>

		<?php endif;?>

		
		<?php if (($product_data->is_uwa_expired() === FALSE) and ($product_data->is_uwa_live() === TRUE)): ?>
		
		<?php endif;?>

		<?php  $heading = apply_filters('ultimate_woocommerce_auction_total_bids_heading', __( 'Total Bids Placed:', 'woo_ua' ) ); ?>
		<h2><?php echo $heading; ?></h2>

			<div class="woo_ua" id="uwa_auction_log_history" v-cloak>
				<div class="uwa-table-responsive">
						<table class="uwa-admin-table uwa-admin-table-bordered">
							<?php							
							$uwa_auction_log_history = $product_data->uwa_auction_log_history();

							if ( !empty($uwa_auction_log_history)  ): ?>
							
								<tr>
									<th><?php _e('Bidder Name', 'woo_ua')?></th>
									<th><?php _e('Bidding Time', 'woo_ua')?></th>
									<th><?php _e('Bid', 'woo_ua')?></th>								
									<th><?php _e('Auto', 'woo_ua')?></th>								
									<th class="actions"><?php _e('Actions', 'woo_ua')?></th>
								</tr>
								<?php foreach ($uwa_auction_log_history as $history_value) { 
								$start_date = $product_data->get_uwa_auction_start_time();								
								if ( $history_value->date < $product_data->get_uwa_auction_relisted() && ! isset( $uwa_relisted )) {
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
											<?php 
											/*if ($product_data->get_uwa_auction_expired() != '2') { */ ?>
											<?php if(!$product_data->get_uwa_auction_payed()){ ?>
												<a href='#' data-id=<?php echo $history_value->id;?> 
												data-postid=<?php echo $post->ID;?>  ><?php echo __('Delete', 'woo_ua');?></a>
											<?php } ?>
										</td>
									</tr>
								<?php } ?>	

							<?php endif;?>

							<tr class="start">									
									<?php 
									$start_date = $product_data->get_uwa_auction_start_time();
									 if ($product_data->is_uwa_live() === TRUE) { ?>
									<td class="started"><?php echo __('Auction started', 'woo_ua');?>
										<?php }   else { ?>									
									<td  class="started"><?php echo __('Auction starting', 'woo_ua');?>		
										<?php } ?></td>	
										
									<td colspan="4"  class="bid_date"><?php echo mysql2date($datetimeformat,$start_date)?></td>
							</tr>
						</table>
				</div>
			</div>		
		<?php 
	}		
	 /**
	 * Remind to pay 
	 *
	 */	
	public function uwa_email_remind_to_pay_notification_fun() {
		global $woocommerce;		
		$remind_to_payment = get_option( 'woocommerce_woo_ua_email_auction_remind_to_pay_settings' );
		if( is_array($remind_to_payment) ) {
		if ( $remind_to_payment['enabled'] == 'yes' ) {
				
		$uwa_interval    = ( ! empty( $remind_to_payment['uwa_interval'] ) ) ? (int) $remind_to_payment['uwa_interval'] : 5;
		$uwa_stopsending = ( ! empty( $remind_to_payment['uwa_stopsending'] ) ) ? (int) $remind_to_payment['uwa_stopsending'] : 4;
		$args        = array(
						'post_type'          => 'product',
						'posts_per_page'     => '-1',
						'show_past_auctions' => true,
						'tax_query'          => array(
							array(
								'taxonomy' => 'product_type',
								'field'    => 'slug',
								'terms'    => 'auction',
							),
						),
						'meta_query'         => array(
							'relation' => 'AND',
							array(
								'key'   => 'woo_ua_auction_closed',
								'value' => '2',
							),
							array(
								'key'     => 'woo_ua_auction_payed',
								'compare' => 'NOT EXISTS',
							),
							array(
								'key'     => 'uwa_auction_stop_mails',
								'compare' => 'NOT EXISTS',
							),
						),
						'auction_arhive'     => true,
						'show_past_auctions' => true,
					);


					$the_query = new WP_Query( $args );

					if ( $the_query->have_posts() ) {

						while ( $the_query->have_posts() ) :
							$the_query->the_post();
							$no_of_sent_mail = get_post_meta( $the_query->post->ID, 'uwa_number_of_sent_mails', true );
							$sent_mail_dates  = get_post_meta( $the_query->post->ID, 'uwa_dates_of_sent_mails', false );
							$no_days              = (int) $remind_to_payment['uwa_interval'];

							$product_data = wc_get_product( $the_query->post->ID );

							if ( (int) $no_of_sent_mail >= $uwa_stopsending ) {
								update_post_meta( $the_query->post->ID, 'uwa_auction_stop_mails', '1' );

							} elseif ( ( ! $sent_mail_dates or ( (int) end( $sent_mail_dates ) < strtotime( '-' . $uwa_interval . ' days' ) ) ) and ( strtotime( $product_data->get_uwa_auction_end_dates() ) < strtotime( '-' . $uwa_interval . ' days' ) ) ) {

								update_post_meta( $the_query->post->ID, 'uwa_number_of_sent_mails', $no_of_sent_mail + 1 );
								add_post_meta( $the_query->post->ID, 'uwa_dates_of_sent_mails', time(), false );
								do_action( 'uwa_email_remind_to_pay_notification', $the_query->post->ID );
							}

						endwhile;
						wp_reset_postdata();
					}		
			} else {
				
			}
		}
	}

	/**
     * Ending soon auctions
     *
     */    
    public function uwa_email_auction_ending_soon_notification_fun() {    	

    	/* note : $the_query->the_post(); -- Don't use anywhere, it changes timezone */

        global $woocommerce, $wpdb;
        $uwa_ending_soon = get_option( 'woocommerce_woo_ua_email_auction_ending_bidders_settings' );    
        
        if ( $uwa_ending_soon['enabled'] === 'yes' ) {

            //echo 
            $uwa_interval = $uwa_ending_soon['uwa_interval'];

			$uwa_interval_time = date( 'Y-m-d H:i', current_time( 'timestamp' ) + ( $uwa_interval * HOUR_IN_SECONDS ) );

            // get auction which are live, and then matched interval with end date
            $args = array(
                        'post_type'          => 'product',
                        'posts_per_page'     => '100',                        
                        //'posts_per_page'     => '-1',                        
                        'tax_query'          => array(
                            array(
                                'taxonomy' => 'product_type',
                                'field'    => 'slug',
                                'terms'    => 'auction',
                            ),
                        ),
                        'meta_query'         => array(
                            'relation' => 'AND',        
                            array(
                                'key'     => 'woo_ua_auction_has_started',
                                'value' => '1',
                            ),                            
                            array(
                                'key'     => 'woo_ua_auction_closed',
                                'compare' => 'NOT EXISTS',
                            ),
							array(
									'key'     => 'uwa_auction_sent_ending_soon',									
									'compare' => 'NOT EXISTS',
							),
							array(
								'key'     => 'woo_ua_auction_end_date',
								'compare' => '<',
								'value'   => $uwa_interval_time,
								'type '   => 'DATETIME',
							),
                            
                        ),                        
                    );

            $the_query = new WP_Query( $args );           
			if ( $the_query->have_posts() ) {
				while ( $the_query->have_posts() ) :
					$the_query->the_post();	
					$product_data = wc_get_product( $the_query->post->ID );
					$now_timestamp = current_time( "timestamp");
					WC()->mailer();
					add_post_meta( $the_query->post->ID, 'uwa_auction_sent_ending_soon', $now_timestamp, true );
					do_action( 'woo_ua_auctions_ending_soon_email_bidders', $the_query->post->ID);	
					
					
				endwhile;
				wp_reset_postdata();
			}
						
		           
  			
        } /* end of if - uwa_enabled_bidders */
            
       
    } /* end of function - ending soon */     

	
	public function uwa_block_unblock_modify_user_table( $column ) {
		$column['uwa_block_user_status'] = __('Bidding Status', 'woo_ua');		
		return $column;
	}


public function uwa_block_unblock_user_modify_user_table_row( $val, $column_name, $user_id ) {
    switch ($column_name) {
        case 'uwa_block_user_status' :
			$user_status = get_the_author_meta( 'uwa_block_user_status', $user_id );
			$user_bid_status = __('Unblock', 'woo_ua');
		    if( $user_status =="uwa_block_user_to_bid"){
				$user_bid_status = __('Block', 'woo_ua');
			}		
            return $user_bid_status;        
        default:
    }
    return $val;
}

	/**
	 * Add new Field to user Profile for Block/Unblock for Bidding.
	 *
	 */
	public function uwa_block_unblock_user_to_bid_profile_fields ( $user ) {	
		$user_status = get_the_author_meta( 'uwa_block_user_status', $user->ID,true );
		?>
		<h3><?php _e('UWA Pro Block/Unblock User', 'woo_ua'); ?></h3>
		<table class="form-table">
   	 <tr>
   		 <th><label for="uwa_block_user_status"><?php _e('Block/Unblock User to Bid', 'woo_ua'); ?></label></th>
   		 <td>
   			 <select id="uwa_block_user_status" name="uwa_block_user_status">
			  <option value=""><?php _e('Select Status', 'woo_ua'); ?> </option>
			 <option value="uwa_block_user_to_bid" <?php selected( $user_status , 'uwa_block_user_to_bid'); ?>><?php _e('Block', 'woo_ua'); ?> </option>
             <option value="uwa_unblock_user_to_bid" <?php selected( $user_status, 'uwa_unblock_user_to_bid'); ?>> <?php _e('Unblock', 'woo_ua'); ?></option>			 
			 </select>
   			 
   		 </td>
   	 </tr>
    </table>
	   <?php 	
	}
	/**
	 * Saved new Field to user Profile for Block/Unblock for Bidding.
	 *
	 */
	public function uwa_block_unblock_user_to_bid_save_profile_fields ( $user_id ) {
		
			 if ( !current_user_can( 'edit_user', $user_id ) ) { return false; } else{
				if(isset($_POST['uwa_block_user_status']) && $_POST['uwa_block_user_status'] !=""){
					update_usermeta( $user_id, 'uwa_block_user_status', $_POST['uwa_block_user_status'] );
				}else{
					delete_usermeta($user_id, 'uwa_block_user_status');
				}
			}
		
	}
	/**
	 * Auto Renew (Relisting)
	 *
	 */
	public function uwa_automatic_renew_auction_fun() {	

		global $woocommerce, $product, $post;

		$args = array(
						'post_type'          => 'product',
						'posts_per_page'     => '200',
						'auction_arhive'     => true,						
						'tax_query'          => array(
							array(
								'taxonomy' => 'product_type',
								'field'    => 'slug',
								'terms'    => 'auction',
							),
						),
						'meta_query'         => array(
							'relation' => 'AND',

							array(
								'key'     => 'woo_ua_auction_closed',
								'compare' => 'EXISTS',
							),
							array(
								'key'     => 'woo_ua_auction_payed',
								'compare' => 'NOT EXISTS',
							),
							array(
								'key'   => 'uwa_auto_renew_enable',
								'value' => 'yes',
							),
						),
						
					);

					$the_query = new WP_Query( $args );					

					if ( $the_query->have_posts() ) {

						while ( $the_query->have_posts() ) {
	
							$the_query->the_post();
							$postid = $the_query->post->ID;
							$posttype = get_post_type($postid);							

							if($posttype == "product"){	
								$this->uwa_auto_renew_auction( $the_query->post->ID );
							}
						}

						wp_reset_postdata();
					}
		
	}
	
	public function uwa_auto_renew_auction( $product_id ) {

				$product_data = wc_get_product( wc_clean( $product_id ) );
			    $auto_renew_enable = $product_data->get_uwa_auto_renew_enable();
			    $is_uwa_completed = $product_data->is_uwa_completed();
			    $auto_renew_duration_hours = $product_data->get_uwa_auto_renew_duration_hours();
				
				if ( $auto_renew_enable == 'yes' && $is_uwa_completed && $auto_renew_duration_hours ) {
					 $expired_auction = $product_data->get_uwa_auction_expired();
					 $uwa_auction_payed = $product_data->get_uwa_auction_payed();
				   	 $auction_fail_reason = $product_data->get_uwa_auction_fail_reason();					
					 $uwa_relist_start_date = get_uwa_now_date();					 
					 $dt = strtotime($uwa_relist_start_date)+( $auto_renew_duration_hours * 3600);

					 /*$uwa_relist_end_date = wp_date('Y-m-d H:i:s', $dt,get_uwa_wp_timezone());*/

					 $uwa_relist_end_date = date('Y-m-d H:i:s', $dt);
								 
					 $uwa_old_end_date   = $product_data->get_uwa_auction_end_dates();					
					/*  if winner not paid fail*/
					$renew_not_paid_enable = $product_data->get_uwa_auto_renew_not_paid_enable();
					$renew_not_paid_hours = $product_data->get_uwa_auto_renew_not_paid_hours();					
					
					if ( $renew_not_paid_enable == 'yes' && $expired_auction == '2' && 
						$auto_renew_duration_hours && !$uwa_auction_payed ) {
						if ( current_time( 'timestamp' ) > ( strtotime( $uwa_old_end_date ) + ( $renew_not_paid_hours * 3600 ) ) ) {
							if(!empty($uwa_relist_start_date) && !empty($uwa_relist_end_date)) {
								$this->uwa_manually_do_relist($product_id, $uwa_relist_start_date,$uwa_relist_end_date);
							}
							return;
						}
					}
					
					/*  For No Bid Placed */
					$renew_no_bids_enable = $product_data->get_uwa_auto_renew_no_bids_enable();
					$renew_no_bids_hours = $product_data->get_uwa_auto_renew_fail_hours();					
					
					if ( $renew_no_bids_enable == 'yes' && $auction_fail_reason == '1' && 
						$auto_renew_duration_hours ) {
						if ( current_time( 'timestamp' ) > ( strtotime( $uwa_old_end_date ) + ( $renew_no_bids_hours * 3600 ) ) ) {
							if(!empty($uwa_relist_start_date) && !empty($uwa_relist_end_date)) {
								$this->uwa_manually_do_relist($product_id, $uwa_relist_start_date,$uwa_relist_end_date);
							}
							return;
						}
					}
					
					/*  For Reserve Not Met */
					$renew_no_reserve_enable = $product_data->get_uwa_auto_renew_no_reserve_enable();
					$renew_no_reserve_hours = $product_data->get_uwa_auto_renew_reserve_fail_hours();					
					
					if ( $renew_no_reserve_enable == 'yes' && $auction_fail_reason == '2' && 
						$auto_renew_duration_hours ) {
						if ( current_time( 'timestamp' ) > ( strtotime( $uwa_old_end_date ) + ( $renew_no_reserve_hours * 3600 ) ) ) {
							if(!empty($uwa_relist_start_date) && !empty($uwa_relist_end_date)) {
								$this->uwa_manually_do_relist($product_id, $uwa_relist_start_date,$uwa_relist_end_date);
							}
							return;
						}
					}
					
					
				}

				return;
			}
	
	/**
	 * Ajax End Auction
	 *
	 * Function for deleting bid in wp admin	
	 * @param  array
	 * @return string
	 *
	 */	
	function uwa_admin_force_remind_to_pay_callback () {
		 global $woocommerce, $wpdb;			
			if (!current_user_can('edit_product', $_POST["postid"])) {
					die();
			}	
			if (!empty($_POST["postid"])) {	
					WC()->mailer(); 
					$emails = do_action('uwa_email_remind_to_pay_notification', $_POST["postid"]);						
					$response['status'] = 1;
					$response['success_message'] = __('Reminder Send Successfully.','woo_ua');
					
				}  else {
					$response['status'] = 0;
					$response['error_message'] = __('Please Try Again','woo_ua');
					}							
			
			echo json_encode( $response );
			exit;		
	}
	
	/**
	 * Ajax Callback
	 *
	 * Function Choose winner in live auctions
	 * @param  array
	 * @return string
	 *
	 */	
	function uwa_admin_force_choose_winner_callback () {
		global $wpdb;
		$response = array();
		$end_time = get_uwa_now_date();
				
		/*if (!current_user_can('manage_options')) {
				die();
		}*/	

		if (!empty($_POST["postid"])) {	
			$product_id = absint($_POST["postid"]);
			$bid_id = absint($_POST["bid_id"]);
			$bid_user_id = absint($_POST["bid_user_id"]);
			$bid_amount = $_POST["bid_amount"];			
			
			if (!empty($bid_id) && !empty($bid_user_id) && !empty($bid_amount)) {	
			
					$product_data = wc_get_product( wc_clean( $product_id ) );
					$closed_auction = $product_data->get_uwa_auction_expired();
					if (!empty($closed_auction)){
						
						die(); /* Auction Already Ended */

					}else {				
						
						$started_auction = $product_data->is_uwa_live();
						$finished_auction = $product_data->is_uwa_completed();	
						$uwa_proxy  = $product_data->get_uwa_auction_proxy();				
						
						//$current_bid = $product_data->get_uwa_auction_current_bid();
						//$current_bider = $product_data->get_uwa_auction_current_bider();	
						
						if ($started_auction === TRUE){
							update_post_meta($product_id, 'woo_ua_auction_end_date', $end_time);
							update_post_meta($product_id, 'woo_ua_auction_current_bid', $bid_amount);
							update_post_meta($product_id, 'woo_ua_auction_current_bider', $bid_user_id);											
							if($uwa_proxy == "yes"){
								update_post_meta($product_id, 'woo_ua_auction_max_current_bider', $bid_user_id);
							}
														
								// Expired info
								update_post_meta($product_id, 'woo_ua_auction_closed', '2');						
								add_user_meta($bid_user_id, 'woo_ua_auction_win', $product_id);
								do_action('ultimate_woocommerce_auction_close',  $product_id);
							
								if($bid_user_id){

									/* winner sms */
									do_action('ultimate_woocommerce_auction_winner_sms',  $product_id);
									
									$call_autodabit = get_post_meta($product_id, "woo_ua_winner_request_sent_for_autodabit_payment", true);
									if ( $call_autodabit !='1' ){
										add_post_meta($product_id, 'woo_ua_winner_request_sent_for_autodabit_payment','1');
										do_action('ultimate_woocommerce_auction_autodabit_payment',  $product_id);
									}
									
									WC()->mailer();
									$mail_sent = get_post_meta($product_id, "woo_ua_winner_mail_sent", true);
									if ( $mail_sent !='1' ) {  
									  do_action('woo_ua_auctions_won_email_bidder', $product_id ,$bid_user_id);
									  do_action('woo_ua_auctions_won_email_bidder_admin', $product_id ,$bid_user_id);
									}
									if( $product_data->get_uwa_auction_proxy()=="yes" || $product_data->get_uwa_auction_silent() == "yes" ) {
					
										do_action('woo_ua_auctions_loser_email_bidder', $product_id ,$bid_user_id);	
							
									}
									
									update_post_meta( $product_id, 'woo_ua_winner_mail_sent', '1');
								}
							//}	
						
							$response['status'] = 1;
							$response['success_message'] = __('Auction has expired with a winner chosen.', 'woo_ua');
						}				
						else {
							$response['status'] = 0;
							$response['error_message'] = __('Sorry, there is a problem!', 'woo_ua');
						} /* end of else */	
						
					} /* end of else */
		 
			} /* end of if - $_POST["bid id"] */
		} /* end of if - $_POST["postid"] */

		echo json_encode( $response );
		exit;
	}
	

	function uwa_woocommerce_process_product_meta( $post_id ) {
		global $wpdb;		

		/* converting auction product to any product */
		if(isset($_POST['woo_ua_product_type']) && isset($_POST['product-type'])) {
			if($_POST['woo_ua_product_type'] == "auction" && $_POST['product-type'] != "auction") {

				if ( $post_id > 0 ){

					/* 1. removing watchlist users */
					$metakey = "woo_ua_auction_watch";
					$table = $wpdb->prefix."usermeta";
					/*$qry = $wpdb->prepare("DELETE FROM $table 
						WHERE meta_key='%s' && meta_value='%s'", $metakey, $post_id);*/

					$rows_affected = $wpdb->query($wpdb->prepare("DELETE FROM $table 
						WHERE meta_key='%s' && meta_value='%s'", $metakey, $post_id));

					if($rows_affected){						
						delete_post_meta($post_id, 'woo_ua_auction_watch');						
					}


				} /* end of if - postid */				
			} 
		} 

	} /* end of function */
	
} /* end of class */

UWA_Admin::get_instance();