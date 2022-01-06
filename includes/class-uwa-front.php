<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 *
 * Front Side Class
 *
 * Handles generic Front functionality and AJAX requests.
 *
 * @class  UWA_Front
 * @package Ultimate WooCommerce Auction PRO
 * @author Nitesh Singh
 * @since 1.0
 *
 */
session_start();
class UWA_Front {
	
	private static $instance;
	
	public $uwa_types;	
	public $uwa_item_condition;
		
	/**
     * Returns the *Singleton* instance of this class.
     *
     * @return Singleton The *Singleton* instance.
     *
     */	 
    public static function get_instance(){
		
        if ( null === self::$instance ) {			
            self::$instance = new self();			
        }		
        return self::$instance;
    }
	
	/**
	 * Plugin actions
	 *
	 */
	public function __construct() {	
		if ( ! is_admin() || defined('UWA_DOING_AJAX') ) {

			
			/* ------  when elementor or elementor pro is active ------ */
			/* ------  when divi theme is active ------ */

			$blog_plugins = get_option( 'active_plugins', array() );

			$site_plugins = is_multisite() ? (array) maybe_unserialize( get_site_option('active_sitewide_plugins' ) ) : array();

			if ( (in_array( 'elementor/elementor.php', $blog_plugins ) || isset( $site_plugins[
				'elementor/elementor.php'] ))  || get_template() == "Divi" ){

				/* Bidding Area On single product page */
				add_action( 'woocommerce_auction_add_to_cart', array($this,
					'woocommerce_uwa_auction_bid'), 25 );

				if (is_user_logged_in()) {
					/* Pay Now Button for auction winner */
					add_action( 'woocommerce_auction_add_to_cart', array($this,'woocommerce_uwa_auction_pay'), 100 );
				}
			}
			else{						
				
				/* Bidding Area On single product page */
				add_action( 'woocommerce_single_product_summary', array($this,
					'woocommerce_uwa_auction_bid'), 25 );

				if (is_user_logged_in()) {
					/* Pay Now Button for auction winner */
					add_action( 'woocommerce_single_product_summary', array($this,'woocommerce_uwa_auction_pay'), 100 );
				}
			}


			/* Product Add to cart */
			add_action( 'woocommerce_auction_add_to_cart', array($this,'woocommerce_uwa_auction_add_to_cart'), 30 );
			
				
			if (is_user_logged_in()) {
				
				/* Pay Now Button for auction winner loop/shop page */
		        add_action('woocommerce_after_shop_loop_item', array($this,'uwa_pay_now_winner_fun'), 
		        	60);
				
			}
		}	
		
		//add_filter( 'post_class', array($this,'uwa_extra_div_class_start'));
		// added for php 8
		add_filter( 'woocommerce_post_class', array($this,'uwa_extra_div_class_start'),10,2);
		
		/* Add To cart item */
		add_action('wp_loaded', array($this,'uwa_add_product_to_cart'));			
		
		/* Auction Product Badge shop/loop */
		add_action('woocommerce_before_shop_loop_item_title',array($this,'uwa_auction_bage_fun'), 60);		
		
	
		/* Auction Product Badge single auction page
		//add_filter('woocommerce_single_product_image_thumbnail_html', array($this, 'uwa_auction_badge_single_product'), 60);			
		//Auction Type */ 
		$this->uwa_types =  array('normal' => __('Normal', 'woo_ua'), 'reverse' => __('Reverse', 
			'woo_ua'));
		
		/* Auction Condition */
		$this->uwa_item_condition =  array('new' => __('New', 'woo_ua'), 'used' => __('Used', 
			'woo_ua'));
		
	
		/* Total Bids Place Section On Auction Detail Page */
		if( get_option( 'woo_ua_auctions_bids_section_tab' ) == 'yes' ) {
			add_action('woocommerce_product_tabs', array($this, 'uwa_auction_bids_tab'), 10);	
		}	
		
		/* Private Message Section On Auction Detail Page */
		if( get_option( 'woo_ua_auctions_private_message' ) == 'yes' ) {		
			add_action('woocommerce_product_tabs', array($this, 'uwa_auction_private_msg'));
		
			/* Ajax For Private Message	*/			
			add_action("wp_ajax_send_private_message_process", array($this, "send_private_message_process_ajax"));
			
			add_action("wp_ajax_nopriv_send_private_message_process", array($this, "send_private_message_process_ajax"));		
		}
		
		/* Watchlist Section On Auction Detail Page */
		if( get_option( 'woo_ua_auctions_watchlists' ) == 'yes' ) {
		
			/* for Single page */ 
			add_action('ultimate_woocommerce_auction_before_bid_form', array($this, 
				'uwa_add_watchlist_button'), 10);
			
			add_action("uwa_ajax_watchlist", array($this, "uwa_ajax_watchlist_auction"));			
		}
		
		/* Ajax Action to check auction finish or not */
		add_action("wp_ajax_expired_auction", array($this, "uwa_ajax_auction_expired_callback"));
		add_action("uwa_ajax_expired_auction", array($this, "uwa_ajax_auction_expired_callback"));
		
		/* Last Activity Timestamps */
		add_action('ultimate_woocommerce_auction_place_bid', array($this, 
			'uwa_update_last_activity_timestamp'), 1);
		add_action('ultimate_woocommerce_auction_delete_bid', array($this, 
			'uwa_update_last_activity_timestamp'), 1);
		add_action('ultimate_woocommerce_auction_close', array($this, 
			'uwa_update_last_activity_timestamp'), 1);
		add_action('ultimate_woocommerce_auction_started', array($this, 
			'uwa_update_last_activity_timestamp'), 1);
		
		
		/* Ajax Check Auction Live Status  */
		add_action("wp_ajax_get_live_stutus_auction", array($this,"uwa_get_live_stutus_auction_callback"));
		add_action("wp_ajax_nopriv_get_live_stutus_auction", array($this,"uwa_get_live_stutus_auction_callback"));
		add_action("uwa_ajax_get_live_stutus_auction", array($this,"uwa_get_live_stutus_auction_callback"));
		
		/* Modify is_purchasable  */
		add_filter('woocommerce_is_purchasable', array($this, 'is_purchasable'), 10, 2);
	

		/* Redirect Auction page After login */
		add_action('woocommerce_login_form_end', array($this,
			'add_redirect_after_login') );

		/* Redirect Auction page After Registration */
		add_action('woocommerce_register_form_end', array($this,
			'add_redirect_after_register') );

			
		/* Product Query modification	*/
		add_action('woocommerce_product_query', array($this, 
			'uwa_delete_from_woocommerce_product_query'), 2);

		add_filter( 'woocommerce_product_related_posts_query', array( $this,
			'uwa_delete_from_woocommerce_related_products_query' ) );
		
		add_action('woocommerce_product_query', array($this, 'uwa_pre_get_posts'), 99, 2);

		/* add_filter('pre_get_posts', array($this, 'auction_arhive_pre_get_posts'));
		/* add_action('pre_get_posts', array($this, 'uwa_query_auction_archive'), 1); */
				
		add_action("query_vars", array($this, "uwa_search_auctions_query"));
		
		// search by SKU better in WooCommerce
		add_filter( 'pre_get_posts', array($this, "auction_sku_search_helper"));	
		
		add_action( 'template_redirect', array( $this, 'uwa_get_recently_view_auctions'));
		
		if( get_option( 'uwa_show_timer_on_shoppage' ) == 'yes' ) {
		  add_action( 'woocommerce_after_shop_loop_item', array( $this, 'woocommerce_template_loop_product_link_close'),5);
		}

		
		add_filter('woocommerce_catalog_orderby', array($this, 
			'uwa_auction_woocommerce_catalog_orderby'));
		
		add_filter('woocommerce_default_catalog_orderby_options', array($this,
			'uwa_auction_woocommerce_catalog_orderby'));


		/* it must runs at the last else it will not affect */
		/* changes count of product category for widget and shop page */
		add_filter( 'get_terms', array($this, 'uwa_change_count_product_category'), 500, 4);

		/* redirects to checkout page after woo login */		
		add_filter( 'woocommerce_login_redirect', array($this, 'uwa_woo_login_redirect'), 7000, 2);

		/* display winner name in expired auctions */
		add_action( 'woocommerce_product_meta_end', array( $this, 'uwa_woo_product_meta_end'));
		/* display winner name in shop page */
		add_action('woocommerce_after_shop_loop_item', array($this,'uwa_woo_display_winner'), 5000);


		if(get_option("uwa_display_wining_losing_text") == "yes") {

			/* Auction Product Badge for Winner shop/loop */
			add_action('woocommerce_before_shop_loop_item_title', array($this,'uwa_auction_bage_fun_winning'), 60);
		}
		
		
	}
	
	//Helps search by SKU better in WooCommerce
	function auction_sku_search_helper($wp){
		global $wpdb;

		if( ! is_admin() ) {

			//Check to see if query is requested
			if( !isset( $wp->query['s'] ) || !isset( $wp->query['post_type'] ) || $wp->query['post_type'] != 'product') return;
			$sku = $wp->query['s'];
			$ids = $wpdb->get_col( $wpdb->prepare("SELECT post_id FROM $wpdb->postmeta WHERE meta_key='_sku' AND meta_value = %s;", $sku) );
			if ( ! $ids ) return;
			unset( $wp->query['s'] );
			unset( $wp->query_vars['s'] );
			$wp->query['post__in'] = array();
			foreach($ids as $id){
				$post = get_post($id);
				if($post->post_type == 'product_variation'){
					$wp->query['post__in'][] = $post->post_parent;
					$wp->query_vars['post__in'][] = $post->post_parent;
				} else {
					$wp->query_vars['post__in'][] = $post->ID;
				}
			}
		}
	}
	
	public function woocommerce_template_loop_product_link_close() {
		global $wpdb,$woocommerce, $product, $post;
		$uwa_countdown_format = get_option( 'woo_ua_auctions_countdown_format' );
		$product_id =  $product->get_id();
		if(method_exists( $product, 'get_type') && $product->get_type() == 'auction') {
			
		 if(($product->is_uwa_expired() === FALSE ) and ($product->is_uwa_live()  === TRUE )) { 
			$uwa_remaining_seconds = $product->get_uwa_remaining_seconds();
			$uwa_remaining_seconds  =  wp_date('Y-m-d H:i:s',$product->get_uwa_remaining_seconds(),get_uwa_wp_timezone());
			$uwa_time_zone =  (array)wp_timezone();
			$sinceday  =  wp_date('M j, Y H:i:s O',time(),get_uwa_wp_timezone());		
			?>
			<script>
			var servertime='<?php echo $sinceday;?>';
			</script>
			<div class="uwa-main-auction-product-loop uwa_auction_product_countdown" data-time="<?php echo $uwa_remaining_seconds;?>" data-auction-id="<?php echo esc_attr( $product_id ); ?>" 
			data-format="<?php echo $uwa_countdown_format; ?>" data-timezonetype="<?php echo $uwa_time_zone['timezone_type']; ?>" data-zone="<?php echo $uwa_time_zone['timezone']; ?>" ></div> 
			<?php  } elseif (($product->is_uwa_expired()  === FALSE ) and ($product->is_uwa_live()  === FALSE )) { 
			$starting_time  =  wp_date('Y-m-d H:i:s',$product->get_uwa_seconds_to_start_auction(),get_uwa_wp_timezone());
			$uwa_time_zone =  (array)wp_timezone();
			$sinceday  =  wp_date('M j, Y H:i:s O',time(),get_uwa_wp_timezone());	
			?>
			<script>
			var servertime='<?php echo $sinceday;?>';
			</script>
			<div class="uwa-main-auction-product-loop  uwa_auction_product_countdown scheduled" data-time="<?php echo $starting_time; ?>" data-auction-id="<?php echo esc_attr( $product_id ); ?>" 
			data-format="<?php echo $uwa_countdown_format; ?>" data-timezonetype="<?php echo $uwa_time_zone['timezone_type']; ?>" data-zone="<?php echo $uwa_time_zone['timezone']; ?>"  ></div>
		<?php }

		}
	}

	/**
	 *  Add Auction Page Template
	 *
	 */	
	public function woocommerce_uwa_auction_bid() {
		global $product;
		
		if(method_exists( $product, 'get_type') && $product->get_type() == 'auction')
			wc_get_template( 'single-product/uwa-bid.php' );
	}

	/**
	 *  Auction Product Add to Cart Area. 
	 *
	 */
	public function woocommerce_uwa_auction_add_to_cart() {
		global $product;
		
		if(method_exists( $product, 'get_type') && $product->get_type() == 'auction')			
			wc_get_template( 'single-product/add-to-cart/uwa-auction.php' );
	}

	/**
	 *  Auction Product Pay Now Button Single Page.	
	 *
	 */	
	public function woocommerce_uwa_auction_pay() {		
		global $product;
		
		if(method_exists( $product, 'get_type') && $product->get_type() == 'auction')			
			wc_get_template( 'single-product/uwa-pay.php' );
	}

	/**
	 *  Auction Product Pay Now Button Shop/loop.	 
	 *
	 */	
	public	function uwa_pay_now_winner_fun() {		
		wc_get_template('loop/uwa-pay-button.php');		
	}	

	function uwa_extra_div_class_start($classes, $product) {
	
		global $post, $product;
	
		if(method_exists( $product, 'get_type') && $product->get_type() == 'auction') {

			/* add class for Shop Isle theme if Shop Isle theme is active */
			if(get_template() == "shop-isle"){ 
				$class_name = "uwa_theme_".get_template();
				$classes[] .= $class_name;     /* classname = uwa_theme_shop-isle */
			}		
		
			if(($product->is_uwa_expired() === FALSE ) and ($product->is_uwa_live() === TRUE )) {
				$classes[] .= 'uwa_auction_status_live';
			}
			if($product->is_uwa_expired() === TRUE ) {
				$classes[] .= 'uwa_auction_status_expired';
			}
			if(($product->is_uwa_expired() === FALSE ) and ($product->is_uwa_live() === FALSE )) {
				$classes[] .= 'uwa_auction_status_pending';
			}
			return $classes; 
			
		} else {
			return $classes;
		}
	} 

	/**
	 *  Auction Product  Add to Cart After Pay Now Button Click.	 
	 *
	 */	
	public function uwa_add_product_to_cart() {

		if (!is_admin()) {

			if (!empty($_GET['pay-uwa-auction'])) {

				$current_user = wp_get_current_user();
				
				//$product_ids = explode( ',', intval($_GET['pay-uwa-auction']));
				$product_ids = explode( ',', $_GET['pay-uwa-auction']);
                $count       = count( $product_ids );
				
				
				if ($count < 0) {
					wp_redirect(home_url());
					exit;
				}

				if (!is_user_logged_in()) {

					/*header('Location: ' . wp_login_url(WC()->cart->get_checkout_url() . '?pay-uwa-auction=' . 
						$_GET['pay-uwa-auction'])); */

						$myaccount_page_id = get_option( 'woocommerce_myaccount_page_id' );
						if($myaccount_page_id > 0){
							$myaccount_page_url = get_permalink( $myaccount_page_id );
							
							$checkout_url = add_query_arg(array( 'pay-uwa-auction' => $_GET['pay-uwa-auction']  ), uwa_auction_get_checkout_url()); 
							
							$url_val = add_query_arg(
								array('uwa-new-redirect' => urlencode($checkout_url)),  $myaccount_page_url);
						}
						else{
							$url_val = wp_login_url(WC()->cart->get_checkout_url() . '?pay-uwa-auction=' . 
										$_GET['pay-uwa-auction']);
						}						
					
						header('Location: ' . $url_val);
						exit;
				}
				
				foreach ( $product_ids as $product_id ) {
					
				   $product_data = wc_get_product($product_id);
				   
					if ($current_user->ID == $product_data->get_uwa_auction_current_bider()) {
						WC()->cart->add_to_cart($product_id);
					
				  	} else {
					  wc_add_notice(sprintf(__('You can not buy this "%s" auction because you have not won it!', 'woo_ua'), $product_data->get_title()), 'error');
				  	}			   
				
				}
				
				wp_safe_redirect(remove_query_arg(array('pay-uwa-auction', 'quantity', 'product_id'), WC()->cart->get_checkout_url()));
				exit;
			}
		}
	}

	/**
	 * Add Auction Badge for Auction Product Shop/loop.	 
	 *
	 */		
	public function uwa_auction_bage_fun() {
		global $product;
		
		if (method_exists( $product, 'get_type') && $product->get_type() == 'auction') {
			
			$badge_img_url = get_option('uwa_badge_image_url');
			if(!empty($badge_img_url)){
				$badge_img_url = get_option('uwa_badge_image_url');
			}else{
				$badge_img_url = UW_AUCTION_PRO_ASSETS_URL."images/woo_ua_auction_big.png";
			}
			echo '<span class="uwa_auction_bage_icon" style="background:url('.$badge_img_url.') center center no-repeat;background-size: 100%;" ></span>';
		}
	}

	/**
	 * Add Auction Badge for Winner Shop/loop.	 
	 *
	 */	
	public	function uwa_auction_bage_fun_winning() {
		global $wpdb, $product;
		  
		if(is_user_logged_in()) {
			if(method_exists($product, 'get_type') && $product->get_type() == 'auction') {

				$product_id = $product->get_id();
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
				
					
			} /* end of method */			
		}

	} /* end of function */

	/**
	 * Add Bids Tab Single Page.
	 *
	 * @param array $tabs
	 * @return array	
	 * 	 
	 */	
	public function uwa_auction_bids_tab($tabs) {
		global $product;

		if(method_exists( $product, 'get_type') && $product->get_type() == 'auction') {							
			$tabs['uwa_auction_bids_history'] = array(
				'title' => __('Bids', 'woo_ua'),
				'priority' => 25,
				'callback' => array($this, 'uwa_auction_bids_tab_callback'),				
			);
		}			
		return $tabs;
	}


	/**
	 * Auction call back from bids tab.
	 *
	 * @param array $tabs
	 *	 
	 */		
	public function uwa_auction_bids_tab_callback($tabs) {
		wc_get_template('single-product/tabs/uwa-bids-history.php');
	}

	/**
	 * Add Private message Tab Single Page.
	 *
	 * @param array $tabs
	 * @return array
	 *
	 */		
	public function uwa_auction_private_msg( $tabs ) {
		global $product;
		
		if(method_exists( $product, 'get_type') && $product->get_type() == 'auction') {
			
			$tabs['uwa_auction_private_msg_tab'] = array(
				'title' => __('Private message', 'woo_ua'),
				'priority' => 50,
				'callback' => array($this, 'uwa_auction_private_msg_tab_callback'),				
			);
		}
		
		return $tabs;
	}

	/**
	 * Auction call back from Private Message Tab.
	 *	 
	 */	 
	public function uwa_auction_private_msg_tab_callback($tabs) {	
		wc_get_template('single-product/tabs/uwa-private-msg.php');
	}

	/**
	 * Auction Private Message Send Mail To Admin.
	 *	 
	 * @return json
	 *
	 */	
	function send_private_message_process_ajax() {
			
		$firstname = sanitize_text_field($_POST['firstname']);
		$email_id = sanitize_email($_POST['email']);
		$message = sanitize_text_field($_POST['message']);
		$product_id = absint($_POST['product_id']);
		$sending = 1;
	
			if(empty($firstname)){
				$response['status'] = 0;				
				$response['error_name'] = __('Please enter your Name!','woo_ua');
				$sending = 0;
			} 
			if(!is_email($email_id) || empty($email_id)){
				$response['status'] = 0;
				$response['error_email'] = __('Please enter your Email address!','woo_ua');
				$sending = 0;
			}
			
			if(empty($message)){
				$response['status'] = 0;
				$response['error_message'] = __('Please enter a message!','woo_ua');
				$sending = 0;
			}
			
			if($sending == 1){
				   /* Sending private message to admin */
				
				  $user_args = array(
					'user_name' => $firstname,
					'user_email' => $email_id,
					'user_message' => $message,
					'product_id' => $product_id,
				  );
			
				 WC()->mailer();							   
				 do_action('uwa_private_msg_email_admin',$user_args);
				
				$response['status'] = 1;
				$response['success_message'] = __('Thank you for Contact.','woo_ua');				
			}
			
		echo json_encode( $response );
		exit;
	}	

	/**
	 * Add Watchlist Button.
	 *	 
	 */		
	function uwa_add_watchlist_button() {		
		wc_get_template('single-product/uwa-watch.php');
	}	

	/**
	 * Ajax watch list auction
	 *
	 * Function for adding or removing auctions to watchlist	 
	 *
	 */
	function uwa_ajax_watchlist_auction() {

		if (is_user_logged_in()) {

			global $product;
			$post_id = intval($_GET["post_id"]);
			$user_ID = get_current_user_id();
			$product = wc_get_product($post_id);

			if ($product) {

				if ($product->is_uwa_user_watching()) {
						delete_post_meta($post_id, 'woo_ua_auction_watch', $user_ID);
						delete_user_meta($user_ID, 'woo_ua_auction_watch', $post_id);
						do_action('ultimate_woocommerce_auction_delete_from_watchlist',$post_id, $user_ID);
				} else {

						add_post_meta($post_id, 'woo_ua_auction_watch', $user_ID);
						add_user_meta($user_ID, 'woo_ua_auction_watch', $post_id);
						do_action('ultimate_woocommerce_auction_after_add_to_watchlist',$post_id, $user_ID);
				}
				wc_get_template('single-product/uwa-watch.php');
			}

		} else {

			echo "<p>";
			
			printf(__('<span class="watchlist-error">Please sign in to add auction to watchlist. </span><a href="%s" class="button watchlist-error">Login &rarr;</a>', 'woo_ua'), get_permalink(wc_get_page_id('myaccount')));
			echo "</p>";
		}

		exit;
	}	

	/**
	 * Ajax function for checking finishing auction		
	 *
	 */
	function uwa_ajax_auction_expired_callback() {
		
		if (isset($_POST["post_id"])) {			 
			
				$product_data = wc_get_product( wc_clean( $_POST["post_id"] ) );

					$product_base_currency = $product_data->uwa_aelia_get_base_currency();   
  					$args = array("currency" => $product_base_currency);


				if ($product_data->is_uwa_expired()) {

					if (isset($_POST["ret"]) && $_POST["ret"] != '0') {
                        
						if ($product_data->is_uwa_reserved()) {
							if (!$product_data->is_uwa_reserve_met()) {
								
								echo "<p class='woo_ua_auction_product_reserve_not_met'>";
								_e("Reserve price has not been met!", 'woo_ua');
								echo "</p>";							
								die();
							}
						}
						
						$current_bidder = $product_data->get_uwa_auction_current_bider();
						
						if ($current_bidder) {
							
							printf(__("Winning bid is %s by %s.", 'woo_ua'), wc_price($product_data->get_uwa_current_bid(),  $args), uwa_user_display_name($current_bidder));
							echo "</p>";
							if ( get_current_user_id() == $current_bidder ){
								
							//WC()->cart->add_to_cart( $_POST["post_id"], 1 ) ;	
								
							$checkout_url = esc_attr(add_query_arg("pay-uwa-auction",$product_data->get_id(), uwa_auction_get_checkout_url()));
							
							$get_charged_for_winner = get_option("_uwa_w_s_charge_".$product_data->get_id()."_".$current_bidder, false);
							$w_product_price = $product_data->get_uwa_auction_current_bid();
							
								if($get_charged_for_winner == $w_product_price){
									
									echo '<p><a href="'.$checkout_url.'" class="button">'.apply_filters('ultimate_woocommerce_auction_pay_now_button_text', __( 'Get Item', 'woo_ua' ), $product_data).'</a></p>';
									
								} else {
									
									echo '<p><a href="'.$checkout_url.'" class="button">'.apply_filters('ultimate_woocommerce_auction_pay_now_button_text', __( 'Pay Now', 'woo_ua' ), $product_data).'</a></p>';
								}
							}
							
						} else {
							echo "<p>";
							_e("There were no bids for this auction.", 'woo_ua');
							echo "</p>";
							die();
						}

					}

				} else {

					echo "<div>";
					
					printf(__("Please refresh page.", 'woo_ua'));

					echo "</div>";
				}
		}
		die();
	}	

	/**
	 * Update Last Activity.	
	 *
	 */	
	function uwa_update_last_activity_timestamp( $data ){

		$product_id = is_array($data) ? $data['product_id'] : $data;
		$current_time = current_time('timestamp');
		
		update_option('woo_ua_auction_last_activity', $current_time);
		update_post_meta($product_id, 'woo_ua_auction_last_activity', $current_time);
	}

	/**
	 * Ajax get Live Status For Auctions
	 *	
	 *
	 */
	function uwa_get_live_stutus_auction_callback() {
		$response = null;						 
		if (isset($_POST["last_timestamp"])) {
			
			$last_timestamp = get_option('woo_ua_auction_last_activity','0');

			if(intval($_POST['last_timestamp']) == $last_timestamp){
				wp_send_json(apply_filters('ultimate_woocommerce_auction_get_price_for_auctions',$response));
				die();
			} else{
				$response['last_timestamp'] = $last_timestamp;
			}	
		 
			$args = array(
				'post_type' => 'product',
				'posts_per_page' => '-1',
				'meta_query' => array(
					array(
						'key'     => 'woo_ua_auction_last_activity',
						'compare' => '>',
						'value'		=> 	intval($_POST['last_timestamp']),
						'type' => 'NUMERIC'
					),
				),						
				'fields' => 'ids',
			);
		
			$the_query = new WP_Query($args);
			
			$posts_ids = $the_query->posts;	
			if(is_array($posts_ids)){
				foreach ($posts_ids as $posts_id) {
					$product_data = wc_get_product($posts_id);
					
						/* --aelia-- */
						$product_base_currency = $product_data->uwa_aelia_get_base_currency();   
						$args = array("currency" => $product_base_currency);
						
						
					$response[$posts_id]['wua_curent_bid'] = $product_data->get_price_html();
					$response[$posts_id]['wua_current_bider'] = $product_data->get_uwa_auction_current_bider();					
					$response[$posts_id]['wua_timer'] = $product_data->get_uwa_remaining_seconds();
					
					/*$response[$posts_id]['wua_activity'] = $product_data->uwa_auction_log_history_last($posts_id);*/
					
					/* get whole bids table */
					$response[$posts_id]['wua_activity'] = $product_data->get_uwa_bids_history_data($posts_id);														
					
					$response[$posts_id]['wua_bid_value'] = $product_data->uwa_bid_value();
					$response[$posts_id]['wua_bid_value_inc'] = wc_price($product_data->uwa_bid_value(), $args);

					$response[$posts_id]['wua_loggedin_userid'] = get_current_user_id();
					$response[$posts_id]['wua_is_loggedin'] = is_user_logged_in();
									
					
					/* -------------- Wining Losing text ------------------ */

					if (is_user_logged_in()) {

						if(get_option('uwa_display_wining_losing_text') == 'yes'){

								$uwa_imgtext = "";
								$uwa_detailtext = "";

								$arr_getdata = $product_data->uwa_display_user_winlose_text();
								$set_text = $arr_getdata['set_text'];
									
								if($set_text != ""){
									$display_text = $arr_getdata['display_text'];

									if($set_text == "winner"){

										/* display above auction image */										
										$uwa_imgtext = '<span class="uwa_winning">'.$display_text.'</span>';

										/* display above timer */										
										$uwa_detailtext = '<span class="uwa_winning_detail">'.$display_text.'</span>';

									}
									elseif($set_text == "loser"){
								
										/* display above auction image */										
										$uwa_imgtext = '<span class="uwa_losing">'.$display_text.'</span>';

										/* display above timer */										
										$uwa_detailtext = '<span class="uwa_losing_detail">'.$display_text.'</span>';

									}

									$response[$posts_id]['wua_uwa_imgtext'] = $uwa_imgtext;
									$response[$posts_id]['wua_uwa_detailtext'] = $uwa_detailtext;
								}
									
						} /* end of if - wining losing */
						
					} /* end of if - user logged in */	
													
					
					/* -------------------- Next bids ---------------------- */

						$uwa_proxy  = $product_data->get_uwa_auction_proxy();
						$uwa_silent = $product_data->get_uwa_auction_silent();
						$uwa_bid_value = $response[$posts_id]['wua_bid_value'];						
						$response[$posts_id]['wua_auctiontype'] = $product_data->get_uwa_auction_type();

							
						if(get_option('uwa_show_direct_bid') == 'yes'){

							if($uwa_silent != 'yes'){
								
								$bid_inc = $product_data->get_uwa_auction_bid_increment();
								$uwa_variable_inc_enable = get_post_meta($product_data->get_uwa_wpml_default_product_id(), 
									'uwa_auction_variable_bid_increment', true);

											
								if($uwa_variable_inc_enable == "yes"){  /* variable increment */

									if($uwa_proxy == "yes"){
										$uwa_next_bids = $product_data->get_uwa_next_bid_options_proxy_variable(
											$uwa_bid_value, $bid_inc);
									}
									else{
										$uwa_next_bids = $product_data->get_uwa_next_bid_options_variable($uwa_bid_value);
									}									
									$response[$posts_id]['wua_next_bids'] = $uwa_next_bids;
								}
								else{   /* bid increment */

									if($uwa_proxy == "yes"){
										$uwa_next_bids = $product_data->get_uwa_next_bid_options_proxy($uwa_bid_value, 
											$bid_inc);
									}
									else{
										$uwa_next_bids = $product_data->get_uwa_next_bid_options($uwa_bid_value, $bid_inc);
									}									
									$response[$posts_id]['wua_next_bids'] = $uwa_next_bids;
								}

							} /* end of if - slient */


						} /* end of if - directbid */


						/* ----- winning username ----- */

						if(get_option('uwa_winner_live_product') == 'yes'){
						
								
						    /* live auctions */
							$winner_text = $product_data->get_uwa_winner_text(); 

							$winusername = '<span style="color:green;font-size:20px;" >'.$winner_text.'</span><br>';

							$response[$posts_id]['wua_winuser'] = $winusername;	
							
						}
						
															
					$response[$posts_id]['add_to_cart_text'] = $product_data->add_to_cart_text();
					if ($product_data->is_uwa_reserved() === TRUE) {
							if(get_option('uwa_show_reserve_price', 'no') == 'yes'){
							$reserve_price = (wc_price($product_data->get_uwa_auction_reserved_price(), $args));
								if ($product_data->is_uwa_reserve_met() === FALSE) {
									$not_met_txt = __("Reserve price ".$reserve_price." has not been met.", 'woo_ua');
									$response[$posts_id]['wua_reserve'] = $not_met_txt;
								} elseif ($product_data->is_uwa_reserve_met() === TRUE) {
									$met_txt = __("Reserve price ".$reserve_price." has been met.", 'woo_ua');
									$response[$posts_id]['wua_reserve'] =$met_txt;
								}
								
							}	
						
						if(get_option('uwa_hide_reserve_field', 'no') == 'no' && get_option('uwa_show_reserve_price', 'no') == 'no'){
						
							if ($product_data->is_uwa_reserve_met() === FALSE) {
								$response[$posts_id]['wua_reserve'] = __("Reserve price has not been met.", 'woo_ua');
							} elseif ($product_data->is_uwa_reserve_met() === TRUE) {
								$response[$posts_id]['wua_reserve'] =__("Reserve price has been met.", 'woo_ua');
							}
						}
					}
				} /* end of foreach */
			} /* end of if - is-array */
			
		} /* end of if - isset */
		
		wp_send_json(apply_filters('ultimate_woocommerce_auction_get_price_for_auctions',$response));
		die();		
	}

	/**
	 * Modify is_purchasable For Auction Product
	 *
	 */	
	function is_purchasable( $is_purchasable, $object ) {

		$object_type = method_exists( $object, 'get_type' ) ? $object->get_type() : $object->product_type;
		if ($object_type == 'auction') {
			
			$uwa_disable_buy_it_now = get_option('uwa_disable_buy_it_now');
			if ($uwa_disable_buy_it_now != "yes") {
				return TRUE;
			}
			
			if (!$object->get_uwa_auction_expired() && $object->get_uwa_auction_type() == 'normal' && ($object->get_price() < $object->get_uwa_current_bid())) {
				return false;
			} 
			
			if (!$object->get_uwa_auction_expired() && !$object->get_uwa_auction_expired() && $object->get_price() !== '') {
				return TRUE;
			}			

			if (!is_user_logged_in()) {
				return false;
			}

			$current_user = wp_get_current_user();
			if ($current_user->ID != $object->get_uwa_auction_current_bider()) {
				return false;
			}

			if (!$object->get_uwa_auction_expired()) {
				return false;
			}
			if ($object->get_uwa_auction_expired() != '2') {
				return false;
			}			

			return TRUE;
		}

		return $is_purchasable;
	}

	/**
	 * Redirect Auction page After login
	 *
	 * Add Custom $_GET parameters in form for redirect to single product page
	 * 	
	 */
	public function add_redirect_after_login() {

		global $post;		

		$slug =  $post->post_name; /* default = my-account */
		
		if(isset($_SERVER["HTTP_REFERER"])){

			/* check which is referer page */
			$url = esc_url_raw($_SERVER["HTTP_REFERER"]);
			$url_parts = explode("/", $url);
			$total = count($url_parts);	
			$refer_slug  = $url_parts[$total - 2];


			if($refer_slug != $slug){
				$auction_url = esc_url_raw($_SERVER["HTTP_REFERER"]);				
			} 
			else{				
				if(isset($_REQUEST['redirect'])){
					$auction_url = esc_url_raw($_REQUEST['redirect']);		
				}
				else{
					$auction_url = esc_url_raw($_SERVER["HTTP_REFERER"]);
				}
			} 
						
			echo '<input type="hidden" name="redirect" 
					value="'.$auction_url.'" >';


		} /* end of if - http referer */

	}	

	/**
	 * Redirect Auction page After Registration
	 *
	 * Add Custom $_GET parameters in form for redirect to single product page
	 * 	
	 */
	public function add_redirect_after_register() {

		global $post;		

		$slug =  $post->post_name; /* default = my-account */
		
		if(isset($_SERVER["HTTP_REFERER"])){

			/* check which is referer page */
			$url = esc_url_raw($_SERVER["HTTP_REFERER"]);
			$url_parts = explode("/", $url);
			$total = count($url_parts);	
			$refer_slug  = $url_parts[$total - 2];


			if($refer_slug != $slug){
				$auction_url = esc_url_raw($_SERVER["HTTP_REFERER"]);				
			} 
			else{				
				if(isset($_REQUEST['redirect'])){
					$auction_url = esc_url_raw($_REQUEST['redirect']);		
				}
				else{
					$auction_url = esc_url_raw($_SERVER["HTTP_REFERER"]);
				}
			} 

			echo '<input type="hidden" name="redirect" 
					value="'.$auction_url.'" >';


		} /* end of if - http referer */

	} 	


	/**
	 * Based on Setting Modify Product Query.
	 *	 
	 */
	function uwa_delete_from_woocommerce_product_query( $q ) {

		/* do with main query */
		if (!$q->is_main_query()) {
			return;
		}

		if ($q === true ) {
			return;
		}

		if (!$q->is_post_type_archive('product') && !$q->is_tax(get_object_taxonomies('product'))) {
			return;
		}
		

		/* Hide/show Auction product on shop page */
		$uwa_shoppage_enabled = get_option('woo_ua_show_auction_pages_shop');
		
		if ($uwa_shoppage_enabled != 'yes' && (!isset($q->query_vars['is_auction_archive']) OR $q->query_vars['is_auction_archive'] !== 'true')) {
				$taxquery = $q->get('tax_query');
				if (!is_array($taxquery)) {
					$taxquery = array();
				}
				$taxquery[] =
				array(
					'taxonomy' => 'product_type',
					'field' => 'slug',
					'terms' => 'auction',
					'operator' => 'NOT IN',
				);
				$q->set('tax_query', $taxquery);
		}
		
		/* Hide/show Auction product on category page page */
		$uwa_catpage_enabled = get_option('woo_ua_show_auction_pages_cat');
		
		if ($uwa_catpage_enabled != 'yes' && is_product_category()) {
			
			$taxquery = $q->get('tax_query');
			if (!is_array($taxquery)) {
				$taxquery = array();
			}
			$taxquery[] =
			array(
				'taxonomy' => 'product_type',
				'field' => 'slug',
				'terms' => 'auction',
				'operator' => 'NOT IN',
			);
			$q->set('tax_query', $taxquery);
		}
		
		/* Hide/show Auction product on Tag page page */
		$uwa_tagpage_enabled = get_option('woo_ua_show_auction_pages_tag');
		
		if ($uwa_tagpage_enabled != 'yes' && is_product_tag()) {
			$taxquery = $q->get('tax_query');
			if (!is_array($taxquery)) {
				$taxquery = array();
			}
			$taxquery[] =
			array(
				'taxonomy' => 'product_type',
				'field' => 'slug',
				'terms' => 'auction',
				'operator' => 'NOT IN',
			);
			$q->set('tax_query', $taxquery);
		}
		
		/* Hide/show Auction product on Search page page */
		$woo_ua_show_auction_pages_search = get_option('woo_ua_show_auction_pages_search');

		if (!is_admin() && $q->is_main_query() && $q->is_search()) {

			if (isset($q->query['uwa_auctions_search']) && $q->query['uwa_auctions_search'] == TRUE) {
				$taxquery = $q->get('tax_query');
				if (!is_array($taxquery)) {
					$taxquery = array();
				}
				$taxquery[] =
				array(
					'taxonomy' => 'product_type',
					'field' => 'slug',
					'terms' => 'auction',
				);

				$q->set('tax_query', $taxquery);
				$q->query['auction_arhive'] = TRUE;

			} elseif ($woo_ua_show_auction_pages_search != 'yes') {

				$taxquery = $q->get('tax_query');
				if (!is_array($taxquery)) {
					$taxquery = array();
				}
				$taxquery[] =
				array(
					'taxonomy' => 'product_type',
					'field' => 'slug',
					'terms' => 'auction',
					'operator' => 'NOT IN',
				);

				$q->set('tax_query', $taxquery);
			}
			return;
		}

	}	

	/**
	 * Based on Setting Modify Product Query.	 
	 *
	 */
	public function uwa_delete_from_woocommerce_related_products_query( $query ) {

			global $wpdb;
			$uwa_expired_enabled = get_option('woo_ua_expired_auction_enabled', 'no');
			$uwa_schedule_enabled = get_option('uwa_schedule_enabled','no');
		
			if ( $uwa_expired_enabled != 'yes' ) {
			
				$expired_auctions = uwa_get_expired_auctions_id();
			
			}
			if ( $uwa_schedule_enabled != 'yes' ) {
				
				$scheduled_auctions = uwa_get_scheduled_auctions_id();					
			}
			if ( $uwa_expired_enabled  != 'yes' && count( $expired_auctions ) ) {
					
					$query['where'] .= ' AND p.ID IN ( ' . implode( ',', array_map( 'absint', $expired_auctions ) ) . ' )';
			}
			if ( $uwa_schedule_enabled != 'yes'  && count( $scheduled_auctions ) ) {
				
					$query['where'] .= ' AND p.ID NOT IN ( ' . implode( ',', array_map( 'absint', $scheduled_auctions ) ) . ' )';
			}

			return $query;
	}


	/**
	 * Modify query based on settings
	 *	 
	 * @param object
	 * @return object
	 *
	 */
	function uwa_pre_get_posts($q) {

		$auction = array();
		$uwa_expired_enabled = get_option('woo_ua_expired_auction_enabled');
		$uwa_schedule_enabled = get_option('uwa_schedule_enabled');
		$uwa_shoppage_enabled = get_option('woo_ua_show_auction_pages_shop');
		$uwa_catpage_enabled = get_option('woo_ua_show_auction_pages_cat');
		$uwa_tagpage_enabled = get_option('woo_ua_show_auction_pages_tag');

		if (isset($q->query_vars['is_auction_archive']) && $q->query_vars['is_auction_archive'] == 'true') {

            $taxquery = $q->get('tax_query');
            if (!is_array($taxquery)) {
            	$taxquery = array();
            }

            $taxquery[] =
            array(
                    'taxonomy' => 'product_type',
                    'field' => 'slug',
                    'terms' => 'auction',
            );

            $q->set('tax_query', $taxquery);			
            add_filter( 'woocommerce_is_filtered' , array($this, 'add_is_filtered'), 99); 
		}

		if (isset($q->query_vars['is_auction_archive']) && $q->query_vars['is_auction_archive'] == 'true') {
						//$orderby_value = isset($_GET['orderby']) ? wc_clean($_GET['orderby']) : get_option('uwa_default_orderby'); 
			$orderby_value = isset($_GET['orderby']) ? wc_clean($_GET['orderby']) :get_option('woocommerce_default_catalog_orderby'); 
			
		} else {						
		      //$orderby_value = isset($_GET['orderby']) ? wc_clean($_GET['orderby']) : false;
			$orderby_value = isset($_GET['orderby']) ? wc_clean($_GET['orderby']) : get_option('woocommerce_default_catalog_orderby');
		}

		switch ($orderby_value) {

			/*case 'uwa_bid_asc':

				$q->set('post_type', 'product');
				$q->set('ignore_sticky_posts', 1);
				$q->set('tax_query', array(array('taxonomy' => 'product_type', 'field' => 'slug', 'terms' => 'auction')));

				$meta_query = array(
					array('relation' => 'OR',
						'woo_ua_auction_current_bid' => array(
							'key' => 'woo_ua_auction_current_bid',
							'type' => 'DECIMAL(32,4)',
						),
						'woo_ua_opening_price' => array(
							'key' => 'woo_ua_opening_price',
							'type' => 'DECIMAL(32,4)',
						),
					),
					
				);
				$q->set('meta_query', $meta_query);
				$q->set('orderby', array('woo_ua_opening_price' => 'Asc', 
					'woo_ua_auction_current_bid' => 'Asc'));

				break;

			case 'uwa_bid_desc':

				$q->set('post_type', 'product');
				$q->set('ignore_sticky_posts', 1);
				$q->set('tax_query', array(array('taxonomy' => 'product_type', 'field' => 'slug', 'terms' => 'auction')));
				$meta_query = array(
					array('relation' => 'OR',
						'woo_ua_auction_current_bid' => array(
							'key' => 'woo_ua_auction_current_bid',
							'type' => 'DECIMAL(32,4)',
						),
						'woo_ua_opening_price' => array(
							'key' => 'woo_ua_opening_price',
							'type' => 'DECIMAL(32,4)',
						),
					),
					
				);
				$q->set('meta_query', $meta_query);
				$q->set('orderby', array('woo_ua_opening_price' => 'desc', 
					'woo_ua_auction_current_bid' => 'desc'));

				break;
*/
			case 'uwa_ending':

				$q->set('post_type', 'product');
				$q->set('ignore_sticky_posts', 1);
				$q->set('tax_query', array(array('taxonomy' => 'product_type', 'field' => 'slug', 
					'terms' => 'auction')));
				$time = current_time('Y-m-d h:i');
				$meta_query = array(
					'woo_ua_auction_end_date' => array(
						'key' => 'woo_ua_auction_end_date',
						'value' => $time,
						'type' => 'DATETIME',
						'compare' => '>=',
					),
					array(
						'key'     => 'woo_ua_auction_closed',
						'compare' => 'NOT EXISTS',
					),
					array(
						'key'   => 'woo_ua_auction_started',
						'compare' => 'NOT EXISTS',
					));
				$q->set('meta_query', $meta_query);
				$q->set('orderby', array('woo_ua_auction_end_date' => 'Asc'));

				break;

			case 'uwa_started':

				$q->set('post_type', 'product');
				$q->set('ignore_sticky_posts', 1);
				$q->set('tax_query', array(array('taxonomy' => 'product_type', 'field' => 'slug', 
					'terms' => 'auction')));
				$time = current_time('Y-m-d h:i');
				$meta_query = array(
					'woo_ua_auction_start_date' => array(
						'key' => 'woo_ua_auction_start_date',
						'value' => $time,
						'type' => 'DATETIME',
						'compare' => '<=',
					),

					array(
						'key'     => 'woo_ua_auction_closed',
						'compare' => 'NOT EXISTS',
					),
					array(
						'key'   => 'woo_ua_auction_started',
						'compare' => 'NOT EXISTS',
					));
				$q->set('meta_query', $meta_query);
				$q->set('orderby', array('woo_ua_auction_start_date' => 'desc'));

				break;

			case 'uwa_active':

				$q->set('post_type', 'product');
				$q->set('ignore_sticky_posts', 1);
				$q->set('tax_query', array(array('taxonomy' => 'product_type', 'field' => 'slug', 
					'terms' => 'auction')));
				$meta_query = array(
				array(
					'relation' => 'OR',
					'uwa_most_active' =>
							array(
								'key'     => 'woo_ua_auction_bid_count',
								'type' => 'numeric',
							)
				));

				$q->set('meta_query', $meta_query);
				$q->set('orderby', array('uwa_most_active' => 'desc'));

				break;

		} /* end of switch */



		if ( $uwa_schedule_enabled != 'yes' ) {			
			$metaquery = $q->get('meta_query');

			if (!is_array($metaquery)) {
				$metaquery = array();
			}

			$metaquery[] =array(
					'key'     => 'woo_ua_auction_started',
					'compare' => 'NOT EXISTS',
			);

			$q->set('meta_query', $metaquery);			
		}

		if ( $uwa_expired_enabled != 'yes' ) {
			$metaquery = $q->get('meta_query');

			if (!is_array($metaquery)) {
				$metaquery = array();
			}

			$metaquery[] =array(
					'key'     => 'woo_ua_auction_closed',
					'compare' => 'NOT EXISTS',
				);

			$q->set('meta_query', $metaquery);
		}

		if ($uwa_catpage_enabled != 'yes' && is_product_category()) {
			return;
		}

		if ($uwa_tagpage_enabled != 'yes' && is_product_tag()) {
			return;
		}			
			
		if (!isset($q->query_vars['auction_arhive'])  && !$q->is_main_query()) {

				if ($uwa_shoppage_enabled != 'yes') {

					$taxquery = $q->get('tax_query');
					if (!is_array($taxquery)) {
						$taxquery = array();
					}

					$taxquery[] =
					array(
						'taxonomy' => 'product_type',
						'field' => 'slug',
						'terms' => 'auction',
						'operator' => 'NOT IN',
					);

					$q->set('tax_query', $taxquery);
					return;
				}
				return;

		} /* end of if */

	}

	/**
	 *
	 * Add ordering for auctions
	 *
	 * @param array
	 * @return array
	 *
	 */
	function uwa_auction_woocommerce_catalog_orderby($data) {
				
			$uwa_shoppage_enabled = get_option('woo_ua_show_auction_pages_shop');
			$uwa_catpage_enabled = get_option('woo_ua_show_auction_pages_cat');
			$uwa_tagpage_enabled = get_option('woo_ua_show_auction_pages_tag');

			$is_auction_archive  =  get_query_var('is_auction_archive', false);

			if ((is_shop() && $uwa_shoppage_enabled != 'yes')  && $is_auction_archive !== 'true') {
					return $data;
			}
			if ((is_product_category() && ( $uwa_shoppage_enabled != 'yes' or $uwa_catpage_enabled != 'yes' ))  && $is_auction_archive !== 'true') {
					return $data;
			}
			if ((is_product_tag() && ( $uwa_shoppage_enabled != 'yes' or $uwa_tagpage_enabled == 'yes' ))  && $is_auction_archive !== 'true') {
					return $data;
			}

			/*$data['uwa_bid_asc'] = __( 'Sort by current bid: Low to high', 'woo_ua' );
			$data['uwa_bid_desc'] = __( 'Sort by current bid: High to low', 'woo_ua' );*/
			$data['uwa_ending'] = __( 'Sort auction by Ending Soon', 'woo_ua' );
			$data['uwa_started'] = __( 'Sort auction by Just started', 'woo_ua' );
			$data['uwa_active'] = __( 'Sort auction by Most Active', 'woo_ua' );
					
					
			return $data;
	}

	function uwa_search_auctions_query($qvars) {
		$qvars[] = 'uwa_auctions_search';
		return $qvars;
	}


	public function uwa_get_recently_view_auctions() {
		if ( ! is_singular( 'product' ) || ! is_active_widget( false, false, 
			'uwa_recently_view_auctions', true ) ) {
			return;
		}

		global $post;

		if ( empty( $_COOKIE['uwa_recently_viewed_auctions'] ) ) {
			$viewed_auction = array();
		} else {
			$viewed_auction = (array) explode( '|', $_COOKIE['uwa_recently_viewed_auctions'] );
		}

		if ( ! in_array( $post->ID, $viewed_auction ) ) {
			$viewed_auction[] = $post->ID;
		}

		if ( sizeof( $viewed_auction ) > 15 ) {
			array_shift( $viewed_auction );
		}

		/* Store for session only */
		wc_setcookie( 'uwa_recently_viewed_auctions', implode( '|', $viewed_auction ) );
	}
	

	/* other function */
	
	
	/**
	 * Add Auction Badge for Auction Product Page.	 
	 *
	 */			
	public function uwa_auction_badge_single_product( $output ){
	   	global $product;
	 
		if ( method_exists( $product, 'get_type') && $product->get_type() == 'auction' ) {
			
			$badge_img_url = get_option('uwa_badge_image_url');
			if(!empty($badge_img_url)){
				$badge_img_url = get_option('uwa_badge_image_url');
			}else{
				$badge_img_url = UW_AUCTION_PRO_ASSETS_URL."images/woo_ua_auction_big.png";
			}
			echo '<span class="uwa_auction_bage_icon" style="background:url('.$badge_img_url.') center center no-repeat;background-size: 100%;" ></span>';
		}
		
		return $output;
	}	
				
    /**
	 * Pre_get_post for auction product archive
	 *    		 
	 * @param object $q
	 *    		 
	 */
	function uwa_auction_arhive_pre_get_posts( $q ) {

		if (isset($q->query['auction_arhive']) OR (!isset($q->query['auction_arhive']) && (isset($q->query['post_type']) && $q->query['post_type'] == 'product' && !$q->is_main_query()))) {
			$this->pre_get_posts($q);
		}
	}

	/**
	 * Query for auction product archive
	 *    		 
	 * @param object $q
	 *
	 */
	function uwa_query_auction_archive( $q ) {

		if (!$q->is_main_query()) {
			return;
		}

		if (isset($q->queried_object->ID) && $q->queried_object->ID === wc_get_page_id('auction')) {

			$q->set('post_type', 'product');
			$q->set('page', '');
			$q->set('pagename', '');
			$q->set('auction_arhive', 'true');
			$q->set('is_auction_archive', 'true');

			/* Fix conditional Functions */
			$q->is_archive = true;
			$q->is_post_type_archive = true;
			$q->is_singular = false;
			$q->is_page = false;
		}

		/* When orderby is set, WordPress shows posts. Get around that here. */
		if ( ($q->is_home() && 'page' === get_option( 'show_on_front' )) && (absint( get_option( 'page_on_front' ) ) === absint( wc_get_page_id( 'auction' )) )) {
			$_query = wp_parse_args( $q->query );
			if ( empty( $_query ) || ! array_diff( array_keys( $_query ), array( 'preview', 'page', 'paged', 'cpage', 'orderby' ) ) ) {
				$q->is_page = true;
				$q->is_home = false;
				$q->set( 'page_id', (int) get_option( 'page_on_front' ) );
				$q->set( 'post_type', 'product' );
			}
		}

		if ($q->is_page() && 'page' === get_option('show_on_front') && absint($q->get('page_id')) === wc_get_page_id('auction')) {

			$q->set('post_type', 'product');

			/* This is a front-page shop  */
			$q->set('post_type', 'product');
			$q->set('page_id', '');
			$q->set('auction_arhive', 'true');
			$q->set('is_auction_archive', 'true');

			if (isset($q->query['paged'])) {
				$q->set('paged', $q->query['paged']);
			}

			/* Define a variable so we know this is the front page shop later on */
			define('AUCTIONS_IS_ON_FRONT', true);

			/* Get the actual WP page to avoid errors and let us use is_front_page()
			// This is hacky but works. Awaiting https://core.trac.wordpress.org/ticket/21096 */
			global $wp_post_types;

			$auction_page = get_post(wc_get_page_id('auction'));

			$wp_post_types['product']->ID = $auction_page->ID;
			$wp_post_types['product']->post_title = $auction_page->post_title;
			$wp_post_types['product']->post_name = $auction_page->post_name;
			$wp_post_types['product']->post_type = $auction_page->post_type;
			$wp_post_types['product']->ancestors = get_ancestors($auction_page->ID, 
				$auction_page->post_type);

			/* Fix conditional Functions like is_front_page */
			$q->is_singular = false;
			$q->is_post_type_archive = true;
			$q->is_archive = true;
			$q->is_page = true;

			/* Remove post type archive name from front page title tag */
			add_filter('post_type_archive_title', '__return_empty_string', 5);
		}

	}
				
	/**
     * Set is filtered is true to skip displaying categories only on page
     *		     
     * @return bolean
     *
     */
    function add_is_filtered($id){
        return true;
    }
	

	/**
     * Changes count value of each category in Woocommerce product category widget 
     * Changes count value of each category in Shop page
     *	
     * note : for right count - both shop page and category page must be selected in settings
     *
     * @return number
     *
     */

	function uwa_change_count_product_category($terms, $taxonomy, $args, $wp_term_query){

		global $wpdb;

		$other_count = 0;
		$live_count = 0;
		$expired_count = 0;
		$pending_count = 0;
		$new_text = "";

		/* changes count value when category is -- only product category */
		if(is_array($args)){
			if(isset($args['taxonomy'])){
				if(isset($args['taxonomy'][0])){
					if($args['taxonomy'][0] == "product_cat"){

						/* change count - in woo product category widget and  */
						/* change count - in shop page only */

						/*if((isset($args['show_count']) && $args['show_count'] == 1) || is_shop()) {*/
						if((isset($args['show_count']) && $args['show_count'] == 1)) {
							
							$terms_new = $terms;
							
							foreach($terms_new as $key => $value){
								/*if($value->term_id == 17){									
									$value->count = 11;
								}*/

				/* --------------- loop start ---------------- */	

				$category_name = $value->slug;


				/* count live auctions */

					$meta_query = array(
									array('key'   => 'woo_ua_auction_closed',
										'compare' => 'NOT EXISTS'));


					$tax_query = array('relation' => 'AND',
									array('taxonomy' => 'product_type', 'field' => 'slug', 
										'terms' => 'auction'),
									array('taxonomy' => 'product_cat', 'field' => 'slug', 
			            				'terms' => $category_name));

					$args = array(
						'post_type'	=> 'product',
						'post_status' => 'publish',			
						'posts_per_page' => -1,   			
						'meta_query' => $meta_query,
						'tax_query' => $tax_query,					
						'meta_key' => 'woo_ua_auction_has_started',
						'meta_value' => 1,						
					);

					$live_products = new WP_Query( $args );

					if(isset($live_products->post_count)){		
						$live_count  = $live_products->post_count;
					}

				/* count expire auctions */

					$meta_query1 = array(
									array('key'   => 'woo_ua_auction_closed',
										'compare' => 'EXISTS'));

					$tax_query1 = array('relation' => 'AND',
									array('taxonomy' => 'product_type', 'field' => 'slug', 
										'terms' => 'auction'),
									array('taxonomy' => 'product_cat', 'field' => 'slug', 
			            				'terms' => $category_name));

					$args1 = array(
						'post_type'	=> 'product',
						'post_status' => 'publish',			
						'posts_per_page' => -1,   			
						'meta_query' => $meta_query1,
						'tax_query' => $tax_query1,					
						//'meta_key' => 'woo_ua_auction_has_started',			
					);

					$expired_products = new WP_Query( $args1 );

					if(isset($expired_products->post_count)){		
						$expired_count  = $expired_products->post_count;
					}

				/* count pending auctions */

					$tax_query2 = array('relation' => 'AND',
									array('taxonomy' => 'product_type', 'field' => 'slug', 
										'terms' => 'auction'),
									array('taxonomy' => 'product_cat', 'field' => 'slug', 
			            				'terms' => $category_name));

					$args2 = array(
						'post_type'	=> 'product',
						'post_status' => 'publish',			
						'posts_per_page' => -1,   			
						//'meta_query' => $meta_query,
						'tax_query' => $tax_query2,
						'meta_key' => 'woo_ua_auction_started',
						'meta_value' => 0,			
					);

					$pending_products = new WP_Query( $args2 );

					if(isset($pending_products->post_count)){			
						$pending_count = $pending_products->post_count;
					}

				/* count other auctions */

					$total_count = $value->count; /* --- change ---- */
					$other_count = $total_count - ($live_count + $expired_count + $pending_count);		
						
				/* display count value */

					$shop_enable = get_option('woo_ua_show_auction_pages_shop');
					$catpage_enabled = get_option('woo_ua_show_auction_pages_cat');

					/*  In widget, categories link goes to category page so both 
					shop page and category page must be selected in auction settings */

					if($shop_enable == "yes" && $catpage_enabled == "yes"){

							$display_count = $other_count + $live_count;

							/* display as per options enabled */
							
							$expired_enable = get_option('woo_ua_expired_auction_enabled');	
							$schedule_enable = get_option('uwa_schedule_enabled');	
							if($expired_enable == "yes" && $schedule_enable == "yes"){
								//$new_text = "both";
								$display_count = $other_count + $live_count + $expired_count + 
									$pending_count;	
							}
							elseif($expired_enable == "yes"){
								//$new_text = "expired only";
								$display_count = $other_count + $live_count + $expired_count;	
							}
							elseif($schedule_enable == "yes"){
								//$new_text = "pending only";
								$display_count = $other_count + $live_count + $pending_count;
							}
											
							$value->count = $display_count; /* --- change ---- */
					 	
					}
					else{
						$value->count = $other_count; // only other product to show in counting	
					}

					/* --------------- loop end ---------------- */	

							}  /* end of foreach */

							$terms = $terms_new;
							return $terms_new;


						} /* end of if */
					}

				}
			
			}
		
		}
		return $terms;
				
	} /* end of function */


	function uwa_woo_login_redirect( $redirect, $user ) {

		if(isset($_GET['uwa-new-redirect'])){
			if($_GET['uwa-new-redirect']){			
				$redirect = esc_url_raw($_GET['uwa-new-redirect']);
			}
		}
		return $redirect;

	} /* end of function */


	/* display winner info in product detail page for expired auctions */
	function uwa_woo_product_meta_end() {

	   	global $product;
	   	$winner_text = "";

		if(method_exists($product, 'get_type') && $product->get_type() == 'auction'){

			if(get_option('uwa_winner_expired_product') == 'yes'){
				
				$uwa_expired = $product->is_uwa_expired();
				if($uwa_expired == TRUE){					
					$winner_text = $product->get_uwa_winner_text();
					if($winner_text){ ?>
						<br><div style="color:red;font-size:20px;"><?php echo $winner_text; ?></div>
						<?php
					}
				}
			}

		}
		return $winner_text;

	} /* end of function */


	/* display winner info in shop page for live and expired auctions */
	function uwa_woo_display_winner() {

	   	global $wpdb,$woocommerce, $product, $post;
	   	$product_id =  $product->get_id();
	   	$winner_text = "";

		if(method_exists($product, 'get_type') && $product->get_type() == 'auction'){

			$uwa_expired = $product->is_uwa_expired();

			/* live auctions */
			if(get_option('uwa_winner_live_shop') == 'yes'){
				if($uwa_expired == FALSE){		
				?>
				<div class="winner-name" data-auction_id="<?php echo esc_attr( $product_id ); ?>">
				<?php			
					$winner_text = $product->get_uwa_winner_text(); 
					//if($winner_text){ 
					?>
						<span style="color:green;font-size:20px;"><?php echo $winner_text; ?></span>
						<?php
					//}
					?>
				</div>
				<?php
				}
			}

			/* expired auctions */
			if(get_option('uwa_winner_expired_shop') == 'yes'){
				if($uwa_expired == TRUE){					
					$winner_text = $product->get_uwa_winner_text();
					if($winner_text){ ?>
						<div style="color:red;font-size:20px;"><?php echo $winner_text; ?></div>
						<?php
					}
				}
			}

		}
		return $winner_text;

	} /* end of function */


} /* end of class */

UWA_Front::get_instance();