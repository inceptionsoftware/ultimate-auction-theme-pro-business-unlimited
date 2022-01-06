<?php
/*
 * Plugin Name: Ultimate WooCommerce Auction Pro - Unlimited
 * Plugin URI: http://auctionplugin.net
 * Description: Awesome plugin to host auctions with WooCommerce on your wordpress site and sell anything you want.
 * Author: Nitesh Singh
 * Author URI: http://auctionplugin.net
 * Version: 2.2.1
 * Text Domain: woo_ua
 * Domain Path: languages
 * License: GPLv2
 * Copyright 2021 Nitesh Singh
 * WC requires at least: 3.0.0
 * WC tested up to: 6.0.0
*/

if ( ! defined( 'ABSPATH' ) ) {
    exit;
} 

require_once ABSPATH . 'wp-admin/includes/plugin.php';

$blog_plugins = get_option( 'active_plugins', array() );
$site_plugins = is_multisite() ? (array) maybe_unserialize( get_site_option('active_sitewide_plugins' ) ) : array();

if ( in_array( 'woocommerce/woocommerce.php', $blog_plugins ) || isset( $site_plugins['woocommerce/woocommerce.php'] ) ) {

//EDD Licensing start
// this is the URL our updater / license checker pings. This should be the URL of the site with EDD installed
define('EDD_UWA_AUCTION_PRO_STORE_URL', 'https://auctionplugin.net/'); // you should use your own CONSTANT name, and be sure to replace it throughout this file

// the name of your product. This should match the download name in EDD exactly
define('EDD_UWA_AUCTION_PRO_ITEM_NAME', 'Ultimate Woo Auction Pro - Unlimited - Annual'); // you should use your own CONSTANT name, and be sure to replace it throughout this file

if (!class_exists('EDD_SL_Plugin_Updater')) {
    // load our custom updater
    include dirname(__FILE__).'/includes/EDD_SL_Plugin_Updater.php';
}

// retrieve our license key from the DB
$license_key = trim(get_option('edd_uwa_auction_pro_license_key'));

// setup the updater
$edd_updater = new EDD_SL_Plugin_Updater(EDD_UWA_AUCTION_PRO_STORE_URL, __FILE__, array(
    'version' => '2.2.1', // current version number
    'license' => $license_key, // license key (used get_option above to retrieve from DB)
    'item_name' => EDD_UWA_AUCTION_PRO_ITEM_NAME, // name of this plugin
    'author' => 'Nitesh Singh', // author of this plugin
    ));

/************************************
 * the code below is just a standard
 * options page. Substitute with
 * your own.
 *************************************/

    function edd_uwa_auction_pro_register_option()
    {
        register_setting('edd_uwa_auction_pro_license', 'edd_uwa_auction_pro_license_key', 'edd_uwa_auction_pro_sanitize_license');
    }
    add_action('admin_init', 'edd_uwa_auction_pro_register_option');

    function edd_uwa_auction_pro_sanitize_license($new)
    {
        $old = get_option('edd_uwa_auction_pro_license_key');
        if ($old && $old != $new) {
            delete_option('edd_uwa_auction_pro_license_status');
        }

        return $new;
    }

    function edd_uwa_auction_pro_activate_license()
    {
        if (isset($_POST['edd_uwa_auction_pro_license_activate'])) {
            if (!check_admin_referer('edd_uwa_auction_pro_nonce', 'edd_uwa_auction_pro_nonce')) {
                return;
            }
            $license = trim(get_option('edd_uwa_auction_pro_license_key'));
            if(empty($license)){
				  $license = sanitize_key($_POST['edd_uwa_auction_pro_license_key']);
			   }

            $api_params = array(
                'edd_action' => 'activate_license',
                'license' => $license,
                'item_name' => urlencode(EDD_UWA_AUCTION_PRO_ITEM_NAME),
                );

            $response = wp_remote_get(add_query_arg($api_params, EDD_UWA_AUCTION_PRO_STORE_URL), array('timeout' => 15, 'sslverify' => false));

            if (is_wp_error($response)) {
                return false;
            }

            $license_data = json_decode(wp_remote_retrieve_body($response));

            update_option('edd_uwa_auction_pro_license_status', $license_data->license);
        }
    }
    add_action('admin_init', 'edd_uwa_auction_pro_activate_license');

    function edd_uwa_auction_pro_deactivate_license()
    {
        if (isset($_POST['edd_uwa_auction_pro_license_deactivate'])) {
            if (!check_admin_referer('edd_uwa_auction_pro_nonce', 'edd_uwa_auction_pro_nonce')) {
                return;
            }

            $license = trim(get_option('edd_uwa_auction_pro_license_key'));

            $api_params = array(
                'edd_action' => 'deactivate_license',
                'license' => $license,
                'item_name' => urlencode(EDD_UWA_AUCTION_PRO_ITEM_NAME),
                );

            $response = wp_remote_get(add_query_arg($api_params, EDD_UWA_AUCTION_PRO_STORE_URL), array('timeout' => 15, 'sslverify' => false));

            if (is_wp_error($response)) {
                return false;
            }

            $license_data = json_decode(wp_remote_retrieve_body($response));

            if ($license_data->license == 'deactivated') {
                delete_option('edd_uwa_auction_pro_license_status');
            }
        }
    }
    add_action('admin_init', 'edd_uwa_auction_pro_deactivate_license');

	if ( ! class_exists( 'Ultimate_WooCommerce_Auction_Pro' ) ) {

		/* Required minimums and constants */
		if( !defined( 'UW_AUCTION_PRO_VERSION' ) ) {
			define( 'UW_AUCTION_PRO_VERSION', '2.2.1' ); /* plugin version */
		}
		if( !defined( 'UW_AUCTION_PRO_DIR' ) ) {
			define( 'UW_AUCTION_PRO_DIR', dirname( __FILE__ ) ); /* plugin dir */
		}
		if( !defined( 'UW_AUCTION_PRO_MAIN_FILE' ) ) {
			define( 'UW_AUCTION_PRO_MAIN_FILE', UW_AUCTION_PRO_DIR.'/ultimate-woocommerce-auction-pro.php' ); /* plugin dir */
		}
		if( !defined( 'UW_AUCTION_PRO_URL' ) ) {
			define( 'UW_AUCTION_PRO_URL', plugin_dir_url( __FILE__ ) ); /* plugin url */
		}
		if( !defined( 'UW_AUCTION_PRO_ASSETS_URL' ) ) {
			define( 'UW_AUCTION_PRO_ASSETS_URL', UW_AUCTION_PRO_URL . 'assets/' ); /* plugin url */
		}
		if( !defined( 'UW_AUCTION_PRO_ADMIN' ) ) {
			define( 'UW_AUCTION_PRO_ADMIN', UW_AUCTION_PRO_DIR . '/includes/admin' ); 
			/* plugin admin dir */			
		}		
		if( !defined( 'UW_AUCTION_PRO_ADDONS' ) ) {
			define( 'UW_AUCTION_PRO_ADDONS', UW_AUCTION_PRO_DIR . '/includes/addons/' ); 
			/* plugin admin dir */			
		}	

		if( !defined( 'UW_AUCTION_PRO_ADDONS_URL' ) ) {
			define( 'UW_AUCTION_PRO_ADDONS_URL', UW_AUCTION_PRO_URL . '/includes/addons/' ); 
			/* plugin admin dir */			
		}
		
		if( !defined( 'UW_AUCTION_PRO_PLUGIN_BASENAME' ) ) {
			define( 'UW_AUCTION_PRO_PLUGIN_BASENAME', basename( UW_AUCTION_PRO_DIR ) ); 
			/* plugin base name	*/
		}
		if( !defined( 'UW_AUCTION_PRO_TEMPLATE' ) ) {
			define( 'UW_AUCTION_PRO_TEMPLATE', UW_AUCTION_PRO_DIR . '/templates/' ); 
			/* plugin admin dir */
		}
		if( !defined( 'UW_AUCTION_PRO_WC_TEMPLATE' ) ) {
			define( 'UW_AUCTION_PRO_WC_TEMPLATE', UW_AUCTION_PRO_DIR . '/templates/woocommerce/' ); 
			/* plugin admin dir */
		}
		if( !defined( 'UW_AUCTION_PRO_POST_TYPE' ) ) {
			define( 'UW_AUCTION_PRO_POST_TYPE', 'product' ); /* plugin base name */
		}
		if( !defined( 'UW_AUCTION_PRO_PRODUCT_TYPE' ) ) {
			define( 'UW_AUCTION_PRO_PRODUCT_TYPE', 'auction' ); /* plugin base name */
		}
		
		class Ultimate_WooCommerce_Auction_Pro { 	

			public function __construct() {
			  add_action( 'woocommerce_init', array( &$this, 'init' ) );
			}	
			
			/**
			 * Init the plugin after plugins_loaded so environment variables are set.
			 *			 
			 */
			public function init() {
			
				global $woocommerce;
				global $sitepress;
				
				add_action('admin_notices', array($this, 'uwa_pro_vendor_plugins_notice'));
				add_action( 'init', array( $this, 'uwa_pro_plugins_textdomain' ) );
				add_action( 'wpmu_new_blog', array( $this, 'uwa_pro_plugin_new_blog' ), 10, 6 );

				add_action( 'delete_user', array( $this, 'uwa_pro_delete_user' ) );
				add_action( 'delete_post', array( $this, 'uwa_pro_delete_post' ) );
				
				   /* Create Auction Product Type */
				require_once ( UW_AUCTION_PRO_DIR . '/includes/class-uwa-product.php' );		  
				  /* Scripts class to handle scripts functionality */
				require_once( UW_AUCTION_PRO_DIR . '/includes/class-uwa-scripts.php' );
					/* loads the Misc Functions file */
				require_once ( UW_AUCTION_PRO_DIR . '/includes/uwa-misc-functions.php' );
				require_once ( UW_AUCTION_PRO_DIR . '/includes/class-users-bid.php' );	
				 
				add_action( 'widgets_init', array( $this, 'uwa_register_widgets' ) );

				 /***To override templates within a plugin, two filters are provided by WooCommerce, ' woocommerce_locate_template' and  	'wc_get_template_part'. 
				*Create a subfolder named 'woocommerce' inside the plugin folder, and place there custom templates. 
				*Templates will be loaded in the following hierarchy: 
				*plugin/template_path/template_name 
				*default/template_name				
				*/		
				add_filter('woocommerce_locate_template', array( $this,'uwa_pro_woocommerce_locate_template'), 10, 3);

				/* Admin class to handle admin side functionality */
				require_once( UW_AUCTION_PRO_ADMIN . '/class-uwa-admin.php' );

				/* front side template */
				require_once( UW_AUCTION_PRO_DIR . '/includes/class-uwa-front.php' );
				/* Bidding Class File */
				require_once ( UW_AUCTION_PRO_DIR . '/includes/class-uwa-bid.php' );
				/* Ajax handle */
				require_once ( UW_AUCTION_PRO_DIR . '/includes/class-uwa-ajax.php' );
				/* Shortcode class for handels plugin shortcodes  */
				/* Shortcode class for handels plugin shortcodes  */
				require_once ( UW_AUCTION_PRO_DIR . '/includes/class-uwa-shortcodes.php' );				
				
				require_once ( UW_AUCTION_PRO_ADMIN .'/uwa_importer_support.php' );		

				include_once( UW_AUCTION_PRO_ADDONS . 'uwa_addons_main.php');	

				/* countdown shortcode */
				require_once( UW_AUCTION_PRO_DIR . '/includes/countdown-short-code.php' );
				/* Cron funcation for non-cpanel URL */
				require_once ( UW_AUCTION_PRO_DIR . '/includes/class-auto-cron.php' );	

				/* GENERATE BID CSV -  New Featur*/
				
				// Add action hook only if action=download_csv
				if ( isset($_GET['action'] ) && $_GET['action'] == 'uwa_download_csv' )  {
					// Handle CSV Export	
					add_action( 'admin_init', 'uwa_auctions_download_csv');	
				}
				require_once ( UW_AUCTION_PRO_DIR . '/includes/uwa_bid_won_csv.php' );
				
				
				/* place bid using page load */
				add_action('init', array( $this,'ultimate_woocommerce_auction_place_bid'));	
				
				/* place bid using ajax */
				add_action( 'wp_ajax_uwa_ajax_placed_bid',array($this, 'placebid_uwa_ajax_placed_bid_callback' ));
				add_action( 'wp_ajax_nopriv_uwa_ajax_placed_bid',array($this, 'placebid_uwa_ajax_placed_bid_callback' ));
					
				
				/* For WPML Support*/
				if ( function_exists( 'icl_object_id' ) && method_exists( $sitepress, 'get_default_language' ) ) {

					add_action( 'ultimate_woocommerce_auction_place_bid', array( $this, 'uwa_syncronise_metadata_wpml' ), 1 );
					add_action( 'ultimate_woocommerce_auction_delete_bid', array( $this, 'uwa_syncronise_metadata_wpml' ), 1 );
					add_action( 'ultimate_woocommerce_auction_close', array( $this, 'uwa_syncronise_metadata_wpml' ), 1 );
					add_action( 'ultimate_woocommerce_auction_started', array( $this, 'uwa_syncronise_metadata_wpml' ), 1 );
					add_action( 'woocommerce_process_product_meta', array( $this, 'uwa_syncronise_metadata_wpml' ), 85 );
					
				}
				
				
				add_action( 'admin_notices', array( $this, 'uwa_pro_server_cron_admin_notice' ) );	
				add_action( 'init', array( $this, 'uwa_pro_server_cron_setup' ) );
				add_action( 'admin_init', array( $this, 'uwa_pro_server_cron_admin_notice_save' ) );
				
			}
			
			public function uwa_pro_server_cron_admin_notice_save( ) {
				if ( current_user_can( 'manage_options' ) ) {
					global $current_user;
					$user_id = $current_user->ID;
					/* If user clicks to ignore the notice, add that to their user meta */
					if ( isset( $_GET['uwa_process_auction_cron_ignore_notice'] ) && '0' == $_GET['uwa_process_auction_cron_ignore_notice'] ) {
						add_user_meta( $user_id, 'uwa_process_auction_cron_ignore_notice', 'true', true );
					}
				}
			}
			
			
				
			public function uwa_pro_server_cron_admin_notice( ) {
				global $current_user;
				if ( current_user_can( 'manage_options' ) ) {
					$user_id = $current_user->ID;
					$uwa_cron_type = "uwa_cron_server";
					if ( get_option( 'uwa_process_auction_cron' ) != 'yes' && ! get_user_meta( $user_id, 'uwa_process_auction_cron_ignore_notice' ) && $uwa_cron_type =="uwa_cron_server") {
						echo '<div class="notice notice-info is-dismissible">
					   	<p>' . sprintf( __( '<b>Ultimate Woo Auction Pro:</b> Important Message - We recommend you to set up cron jobs for your auction products so that their status and associated emails are triggered properly. You can go through <a href="https://docs.auctionplugin.net/article/123-set-your-auction-cron-job" target="_blank">this article</a> to know how to set these cron jobs. <a href="%2$s">Hide Notice</a>', 'woo_ua' ), get_bloginfo( 'url' ), esc_attr( add_query_arg( 'uwa_process_auction_cron_ignore_notice', '0' ) ) ) . '</p>
						</div>';
					}
					
					/* if ( get_option( 'uwa_ending_soon_email_cron' ) != 'yes' && ! get_user_meta( $user_id, 'uwa_ending_soon_email_cron_ignore_notice' ) && $uwa_cron_type =="uwa_cron_server") {
						echo '<div class="notice notice-info is-dismissible">
					   	<p>' . sprintf( __( 'Ultimate WooCommerce Auction PRO recommends that you set up a cron job to send ending soon emails: <b>%1$s/?ua-auction-cron=ending-soon-email</b>. Set it every 1 hour | <a href="%2$s">Hide Notice</a>', 'woo_ua' ), get_bloginfo( 'url' ), esc_attr( add_query_arg( 'uwa_ending_soon_email_cron_ignore_notice', '0' ) ) ) . '</p>
						</div>';
					}*/
					
					/*if ( get_option( 'uwa_payment_reminder_email_cron' ) != 'yes' && ! get_user_meta( $user_id, 'uwa_payment_reminder_email_cron_ignore_notice' ) && $uwa_cron_type =="uwa_cron_server") {
						echo '<div class="notice notice-info is-dismissible">
					   	<p>' . sprintf( __( 'Ultimate WooCommerce Auction PRO recommends that you set up a cron job to send Payment Reminder: <b>%1$s/?ua-auction-cron=payment-reminder-email</b>. Set it every 1 hour | <a href="%2$s">Hide Notice</a>', 'woo_ua' ), get_bloginfo( 'url' ), esc_attr( add_query_arg( 'uwa_payment_reminder_email_cron_ignore_notice', '0' ) ) ) . '</p>
						</div>';
					} */
					
					/*if ( get_option( 'uwa_auto_relist_cron' ) != 'yes' && ! get_user_meta( $user_id, 'uwa_auto_relist_cron_ignore_notice' ) && $uwa_cron_type =="uwa_cron_server") {
						echo '<div class="notice notice-info is-dismissible">
					   	<p>' . sprintf( __( 'Ultimate WooCommerce Auction PRO recommends that you set up a cron job For automatic relisting: <b>%1$s/?ua-auction-cron=auto-relist</b>. Set it every 1 hour| <a href="%2$s">Hide Notice</a>', 'woo_ua' ), get_bloginfo( 'url' ), esc_attr( add_query_arg( 'uwa_auto_relist_cron_ignore_notice', '0' ) ) ) . '</p>
						</div>';
					}*/
					
					
				}
				
				
			}
			// http://example.com/?ua-auction-cron=process-auction	

			public function uwa_pro_server_cron_setup( $url = false ) {

				if(isset($_REQUEST['ua-auction-cron'])){
				
				if ( @$_REQUEST['ua-auction-cron'] == 'process-auction' ) {
					update_option( 'uwa_process_auction_cron', 'yes' );
					$meta_query= array(	array('key'  => 'woo_ua_auction_closed',	'compare' => 'NOT EXISTS'),
						array('key' => 'woo_ua_auction_has_started','compare' =>'==', 'value'=>'1'),);
						
						$args = array(
							'post_type' => 'product',
							'posts_per_page' => -1,
							'meta_query'=> $meta_query,
							'meta_key' => 'woo_ua_auction_end_date',
							'orderby' => 'meta_value',
							'order' => 'ASC',
							'tax_query' => array(array('taxonomy' => 'product_type', 'field' => 'slug', 'terms' => 'auction')),
							'auction_arhive' => TRUE,
							'show_past_auctions' => TRUE,
							'show_future_auctions' => TRUE,
						);

						$the_query = new WP_Query($args);						
						if ($the_query->have_posts()) {
							while ($the_query->have_posts()): $the_query->the_post();
								$product_data = wc_get_product($the_query->post->ID);
								if (method_exists( $product_data, 'get_type') && $product_data->get_type() == 'auction' ) {
									$product_data->is_uwa_expired(); // this goes to is_uwa_expired function make change as per this function.
								}
							endwhile;
						}	
					
				}
				// http://example.com/?ua-auction-cron=ending-soon-email
				
				if ( @$_REQUEST['ua-auction-cron'] == 'ending-soon-email' ) {
					update_option( 'uwa_ending_soon_email_cron', 'yes' );
					$uwa_ending_soon = get_option( 'woocommerce_woo_ua_email_auction_ending_bidders_settings' );    
        
					if ( $uwa_ending_soon['enabled'] === 'yes' ) {
						$uwa_interval = $uwa_ending_soon['uwa_interval'];
						$uwa_interval_time = date( 'Y-m-d H:i', current_time( 'timestamp' ) + ( $uwa_interval * HOUR_IN_SECONDS ) );						
						$args = array(
									'post_type'          => 'product',
									'posts_per_page'     => '100', 
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
								
				} /* End Ending soon mail  */
				
				/* http://example.com/?ua-auction-cron=payment-reminder-email 
				*/
				if ( @$_REQUEST['ua-auction-cron'] == 'payment-reminder-email' ) {
					update_option( 'uwa_payment_reminder_email_cron', 'yes' );
					$remind_to_payment = get_option( 'woocommerce_woo_ua_email_auction_remind_to_pay_settings' );
	
					if ( $remind_to_payment['enabled'] === 'yes' ) {
							
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

										} elseif ( ( ! $sent_mail_dates or ( (int) end( $sent_mail_dates ) > strtotime( '-' . $uwa_interval . ' days' ) ) ) or ( strtotime( $product_data->get_uwa_auction_end_dates() ) > strtotime( '-' . $uwa_interval . ' days' ) ) ) {

											update_post_meta( $the_query->post->ID, 'uwa_number_of_sent_mails', (int)$no_of_sent_mail + 1 );
											add_post_meta( $the_query->post->ID, 'uwa_dates_of_sent_mails', time(), false );											

											WC()->mailer();
											do_action( 'uwa_email_remind_to_pay_notification', $the_query->post->ID );
										}

									endwhile;
									wp_reset_postdata();
								}		
						} 
					
				}/* End Payment Reminder cron*/
				
				/* http://example.com/?ua-auction-cron=auto-relist
				*/
				
				if ( @$_REQUEST['ua-auction-cron'] == 'auto-relist' ) {
					update_option( 'uwa_auto_relist_cron', 'yes' );
					$args = array(
						'post_type'          => 'product',
						'posts_per_page'     => '200',												
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
							/* $this->uwa_auto_renew_auction( $the_query->post->ID ); */
							$UWA_relist = new UWA_Admin;
							$UWA_relist->uwa_auto_renew_auction( $the_query->post->ID );
						}

						wp_reset_postdata();
					}
					
				}/* End Auto relist cron*/
				
				}
				
			}


			public function uwa_pro_install($network_wide) {	
				global $wpdb;
				$free_plugin = 'ultimate-woocommerce-auction/ultimate-woocommerce-auction.php';
				if ( is_plugin_active( $free_plugin ) ) {
					deactivate_plugins( $free_plugin );
				}
			
				/* Check if the plugin is being network-activated or not. */
				if ( $network_wide ) {

					/* Retrieve all site IDs from this network.*/
					$site_ids = $wpdb->get_col( "SELECT blog_id FROM $wpdb->blogs WHERE site_id = $wpdb->siteid;" );
					
					/* Install the plugin for all these sites. */
					foreach ( $site_ids as $site_id ) {
						switch_to_blog( $site_id );
						$this->uwa_pro_create_tables();
						$this-> uwa_pro_create_shortcode_pages();
						restore_current_blog();
					}
				} else {
					$this->uwa_pro_create_tables();
					$this->uwa_pro_create_shortcode_pages();			
				}				
			}
		public function uwa_pro_plugin_new_blog($blog_id, $user_id, $domain, $path, $site_id, $meta) {

			$plugin_file      = basename( dirname( __FILE__ ) ) . '/ultimate-woocommerce-auction-pro.php';
			if ( is_plugin_active_for_network(  $plugin_file ) ) {
					switch_to_blog($blog_id);
					$this->uwa_pro_create_tables();
					$this->uwa_pro_create_shortcode_pages();
					restore_current_blog();
				} 

		}
			public static function uwa_pro_deactivation() {				
			}

			/**
			 * Create Database	
			 *	 
			 */ 	
			public function uwa_pro_create_tables() {
					
				require_once ABSPATH . 'wp-admin/includes/upgrade.php';
				global $wpdb;
				
				$log_table = $wpdb->prefix . "woo_ua_auction_log";
				$sql = "CREATE TABLE IF NOT EXISTS $log_table (
				`id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
				  `userid` bigint(20) unsigned NOT NULL,
				  `auction_id` bigint(20) unsigned DEFAULT NULL,
				  `bid` decimal(32,4) DEFAULT NULL,
				  `date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
				  `proxy` tinyint(1) DEFAULT NULL,
				  PRIMARY KEY (`id`)
				);";
				
				dbDelta($sql);
				
				
				$table_name_1 = $wpdb->prefix . "auction_direct_payment";  //get the database table prefix to create my new table

				$sql_1 = "CREATE TABLE $table_name_1 (				
				  `id` int(11) NOT NULL AUTO_INCREMENT,
				  `pid` int(11) NOT NULL,
				  `uid` int(11) NOT NULL,
				  `debit_type` varchar(255) NOT NULL,
				  `debit_amount_type` varchar(255) NOT NULL,
				  `amount_or_percentage` varchar(255) NOT NULL,
				  `transaction_amount` varchar(255) NOT NULL,
				  `main_amount` varchar(255) NOT NULL,
				  `status` varchar(255) NOT NULL,
				  `response_json` text NOT NULL,
				  `created_at` date NOT NULL,
				  `updated_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
				  PRIMARY KEY (`id`)
				) ENGINE=InnoDB DEFAULT CHARSET=latin1; ";

				dbDelta($sql_1);
				
				wp_insert_term('auction', 'product_type');
			       if (get_option('woo_ua_show_auction_pages_shop') == FALSE) {
						add_option('woo_ua_show_auction_pages_shop', 'yes');
					}
					if (get_option('woo_ua_show_auction_pages_search') == FALSE) {
						add_option('woo_ua_show_auction_pages_search', 'yes');
					}				
					if (get_option('woo_ua_show_auction_pages_cat') == FALSE) {
						add_option('woo_ua_show_auction_pages_cat', 'yes');
					}
					
					if (get_option('woo_ua_show_auction_pages_tag') == FALSE) {
						add_option('woo_ua_show_auction_pages_tag', 'yes');
					}				
					
					if (get_option('woo_ua_auctions_countdown_format') == FALSE) {
						add_option('woo_ua_auctions_countdown_format', 'yowdHMS');
					}
					if (get_option('woo_ua_auctions_bid_ajax_enable') == FALSE) {
						add_option('woo_ua_auctions_bid_ajax_enable', 'no');
					}
					if (get_option('woo_ua_auctions_bid_ajax_interval') == FALSE) {
						add_option('woo_ua_auctions_bid_ajax_interval', '1');
					}
					
					if (get_option('woo_ua_auctions_bids_reviews_tab') == FALSE) {
						add_option('woo_ua_auctions_bids_reviews_tab', 'yes');
					}
					if (get_option('woo_ua_auctions_private_message') == FALSE) {
						add_option('woo_ua_auctions_private_message', 'yes');
					}
					
					if (get_option('woo_ua_auctions_bids_section_tab') == FALSE) {
						add_option('woo_ua_auctions_bids_section_tab', 'yes');
					}
					
					if (get_option('woo_ua_auctions_watchlists') == FALSE) {
						add_option('woo_ua_auctions_watchlists', 'yes');
					}
					
					/* cron setting	*/			
					if (get_option('woo_ua_cron_auction_status') == FALSE) {
						add_option('woo_ua_cron_auction_status', '2');
					}
					if (get_option('woo_ua_cron_auction_status_number') == FALSE) {
						add_option('woo_ua_cron_auction_status_number', '25');
					}
									
					update_option('uwa_auction_pro_db_ver', UW_AUCTION_PRO_VERSION);
					update_option('uwa_auction_pro_ver', UW_AUCTION_PRO_VERSION);
					flush_rewrite_rules();						
			}

			/**
			 * Create Shortcode Default Pages	
			 *			 
			 */ 	
			public function uwa_pro_create_shortcode_pages () {
				global $wpdb;
				$option = 'uwa_default_page_exists';
		        $default = array();
		        $default = get_option($option);

				/*
				* All Auctions Page
				*/		
		        if (!isset($default['all-auctions'])) {
		            $all_auctions_page = array(
		                'post_type' => 'page',
		                'post_title' => __('All Auctions', 'woo_ua'),
		                'post_status' => 'publish',
		                'post_content' => '[uwa_all_auctions]',
		                );

		            $id = wp_insert_post($all_auctions_page);

		            if (!empty($id)) {
		                $default['all-auctions'] = $id;
		                update_option($option, $default);
		            }
		        }
				/*
				* Live Auctions Page
				*/		
				if (!isset($default['live-auctions'])) {
		            $live_auctions_page = array(
		                'post_type' => 'page',
		                'post_title' => __('Live Auctions', 'woo_ua'),
		                'post_status' => 'publish',
		                'post_content' => '[uwa_live_auctions]',
		                );

		        	$id = wp_insert_post($live_auctions_page);

		            if (!empty($id)) {
		                $default['live-auctions'] = $id;
		                update_option($option, $default);
		            }
		        }
				/*
				* Expired Auctions Page
				*/		
				if (!isset($default['expired-auctions'])) {
		            $expired_auctions_page = array(
		                'post_type' => 'page',
		                'post_title' => __('Expired Auctions', 'woo_ua'),
		                'post_status' => 'publish',
		                'post_content' => '[uwa_expired_auctions]',
		                );

		          	$id = wp_insert_post($expired_auctions_page);

		            if (!empty($id)) {
		                $default['expired-auctions'] = $id;
		                update_option($option, $default);
		            }
		        }	
				/*
				* Schedule Auctions Page
				*/		
				if (!isset($default['scheduled-auctions'])) {
		            $expired_auctions_page = array(
		                'post_type' => 'page',
		                'post_title' => __('Future Auctions', 'woo_ua'),
		                'post_status' => 'publish',
		                'post_content' => '[uwa_pending_auctions]',
		                );

		        	$id = wp_insert_post($expired_auctions_page);

		            if (!empty($id)) {
		                $default['scheduled-auctions'] = $id;
		                update_option($option, $default);
		            }
		        }
			}	
		
			/**
			 * Function For Place Bid Button Click.
			 *			
			 */		
			public function ultimate_woocommerce_auction_place_bid( $url = false ) {
				
				$uwa_placebid_ajax_enable = get_option('woo_ua_auctions_placebid_ajax_enable', "no");

				/* place bid using page load only */
				if($uwa_placebid_ajax_enable  == "no" || $uwa_placebid_ajax_enable == ""){
					
				
						if (empty($_REQUEST['uwa-place-bid']) || !is_numeric($_REQUEST['uwa-place-bid'])) {
							return;
						}
						
						global $woocommerce;
					
						$product_id = absint($_REQUEST['uwa-place-bid']);
						$bid = abs(round(str_replace(',', '.', $_REQUEST['uwa_bid_value']), wc_get_price_decimals()));				
						$was_place_bid = false;
						$placed_bid = array();
						$placing_bid = wc_get_product($product_id);
						$product_type = method_exists( $placing_bid, 'get_type') ? $placing_bid->get_type() : $placing_bid->product_type;		
						$quantity = 1;
						
						if ('auction' === $product_type) {
							
							$product_data = wc_get_product($product_id);
							$current_user = wp_get_current_user();
							$outbiddeduser = $placing_bid->get_uwa_auction_current_bider();	
							$auction_high_current_bider = $product_data->get_uwa_auction_max_current_bider();
							$uwa_silent = $product_data->get_uwa_auction_silent();	
							if($auction_high_current_bider == $outbiddeduser){
								$outbiddeduser ="";
							}
							$uwa_silent_outbid_email = get_option('uwa_silent_outbid_email',"no");
							if($uwa_silent == 'yes'  && $uwa_silent_outbid_email =="no"){								
								$outbiddeduser ="";
							}							
							if($outbiddeduser  == $current_user->ID){
								$outbiddeduser ="";								
							}
							
							$UWA_Bid = new UWA_Bid; 			

							
							/* Placing Bid */
							if ($UWA_Bid->uwa_bidplace($product_id, $bid)) {
								
								/* here use options to check */
								
								$type_of_antisnipping  = get_option('uwa_aviod_snipping_type');
								/*if($type_of_antisnipping != ""){*/
									
										if($type_of_antisnipping == "snipping_recursive"){
											
											do_action('uwa_extend_auction_time', $product_id);
											
										} /* end of if - snipping recursive */										
										else if($type_of_antisnipping == "snipping_only_once" || $type_of_antisnipping == "" ){ 
								
											/* Anti snipping hook and it must be called only once  -- start */
											$is_antisnipped = get_post_meta($product_id, 
												'woo_ua_auction_extend_time_antisnipping', true);
											/* Extend auction end time only if it not extended before */
											if($is_antisnipped != 'yes'){
												do_action('uwa_extend_auction_time', $product_id);
											}
											/* Anti snipping hook and it must be called only once  -- end */
											
										} /* end of if - snipping  only once */
								/*}*/

								$placebid_userid = $current_user->ID;

								/*  --- place bid message not to display when proxy setting enabled -- */
								$get_proxy_same_maxbid = get_option('uwa_proxy_same_maxbid');

								if($get_proxy_same_maxbid == "yes") {
									$bidmsg = get_user_meta($current_user->ID, "uwa_samemaxbid_bidmsg_display", true);
									$pid = get_user_meta($current_user->ID, "uwa_samemaxbid_bidmsg_auction", true);

									if($bidmsg == "no" && $pid == $product_id){
										$placebid_userid = get_post_meta($product_id, 
											'woo_ua_auction_current_bider', true);
										update_user_meta($current_user->ID, "uwa_samemaxbid_sent_mail", "yes");

										delete_user_meta($current_user->ID, "uwa_samemaxbid_bidmsg_display");
										delete_user_meta($current_user->ID, "uwa_samemaxbid_bidmsg_auction");
									}
									else{								
										uwa_bid_place_message($product_id);
									}									
								}
								else{																	
									uwa_bid_place_message($product_id);
								}							
								
								///uwa_bid_place_message($product_id);
								$was_place_bid = true;
								$placed_bid[] = $product_id;						
									
									
								/* Send Notification to Bidder/Admin	*/
								if($was_place_bid){
									WC()->mailer();
									
									/* bid placed notification to bidder */
									do_action('uwa_pro_bid_place_email', $placebid_userid, $placing_bid);
									do_action('uwa_pro_bid_place_email_admin', $placebid_userid, $placing_bid);


									/* send watchlist mail */
									$watchlistusers = get_uwa_auction_watchlist_by_auctionid($product_id);

									if(count($watchlistusers) > 0){
										do_action('uwa_pro_watchlist_email', $product_data, 
											$watchlistusers);
									}

													
									if(!empty($outbiddeduser)){
										
										/* send mail to outbiddeduser user */
										do_action( 'uwa_pro_outbid_bid_email', $outbiddeduser,$placing_bid);
										do_action('ultimate_woocommerce_auction_outbid_bid', $product_id ,$outbiddeduser);		
									}									
								}						
							}

							if (version_compare($woocommerce->version, '2.1', ">=")) {

								if (wc_notice_count('error') == 0) {
								  wp_safe_redirect(esc_url(remove_query_arg(array('uwa-place-bid', 'quantity', 'product_id'), wp_get_referer())));
								   exit;
								}
								return;

							} else {
							  wp_safe_redirect(esc_url(remove_query_arg(array('uwa-place-bid', 'quantity', 'product_id'), wp_get_referer())));
							  exit;
							}
								
						} else {
							wc_add_notice(__('This product is not Auction', 'woo_ua'), 'error');
							return;
						}
						
				} /* end of if - uwa_placebid_ajax_enable  */
			}

			/**
			 * Templating with plugin folder
			 *
			 * @param int $post_id the post (product) identifier
			 * @param stdClass $post the post (product)			
			 *
			 */
			public function uwa_pro_woocommerce_locate_template($template, $template_name, 
				$template_path){
				global $woocommerce;

				if (!$template_path) {
				  $template_path = $woocommerce->template_url;
				}

			    $plugin_path = UW_AUCTION_PRO_WC_TEMPLATE;
				$template_locate = locate_template( array( $template_path . $template_name, 
					$template_name ) );

				/* Modification: Get the template from this plugin, if it exists  */
				if (!$template_locate && file_exists($plugin_path . $template_name)) {
					return $plugin_path . $template_name;
				} else { 				
					return $template;
				}
			}
		
			/**	
			 * Widgets Register
			 *
			 */
		    public function uwa_register_widgets() {						
							
				include_once( UW_AUCTION_PRO_ADMIN . '/widgets/class-uwa-widget-auction-search.php');
				/* Register widgets	*/		
				register_widget('UWA_Widget_Auction_Search');	

				include_once( UW_AUCTION_PRO_ADMIN . '/widgets/class-uwa-widget-live-auctions.php');
				/* Register widgets	 */			
				register_widget('UWA_Widget_Live_Auctions');
					
				include_once( UW_AUCTION_PRO_ADMIN . '/widgets/class-uwa-widget-latest-auctions.php');
				register_widget('UWA_Widget_Latest_Auctions');	
					
				include_once( UW_AUCTION_PRO_ADMIN . '/widgets/class-uwa-widget-scheduled-auctions.php');
				/* Register widgets */				
				register_widget('UWA_Widget_Scheduled_Auctions');		

				include_once( UW_AUCTION_PRO_ADMIN . '/widgets/class-uwa-widget-expired-auctions.php');
				/* Register widgets	 */			
				register_widget('UWA_Widget_Expired_Auctions');

				include_once( UW_AUCTION_PRO_ADMIN . '/widgets/class-uwa-widget-ending-soon-auctions.php');
				/* Register widgets */				
				register_widget('UWA_Widget_Ending_Soon_Auctions');
				
				include_once( UW_AUCTION_PRO_ADMIN . '/widgets/class-uwa-widget-recently-view-auctions.php');
				register_widget('UWA_Widget_Recently_View_Auctions');
				
				include_once( UW_AUCTION_PRO_ADMIN . '/widgets/class-uwa-widget-my-aucution.php');
				register_widget('UWA_Widget_My_Auctions');	
			}
			

			public function uwa_pro_plugins_textdomain() {
			
				/* Set filter for plugin's languages directory */
				$lang_dir	= dirname( plugin_basename( __FILE__ ) ) . '/languages/';
				$lang_dir	= apply_filters( 'ultimate_woocommerce_auction_languages_directory', 
					$lang_dir );
				
				/* Traditional WordPress plugin locale filter */
				$locale	= apply_filters( 'plugin_locale',  get_locale(), 'woo_ua' );
				$mofile	= sprintf( '%1$s-%2$s.mo', 'woo_ua', $locale );
				
				/* Setup paths to current locale file */
				$mofile_local	= $lang_dir . $mofile;
				$mofile_global	= WP_LANG_DIR . '/' . UW_AUCTION_PRO_PLUGIN_BASENAME . '/' . $mofile;
				
				if ( file_exists( $mofile_global ) ) {

					/* Look in global /wp-content/languages/ultimate-woocommerce-auction-pro folder */
					load_textdomain( 'woo_ua', $mofile_global );
					
				} elseif ( file_exists( $mofile_local ) ) { 

					/* Look in local plugins/ultimate-woocommerce-auction-pro/languages/ folder  */
					load_textdomain( 'woo_ua', $mofile_local );
					
				} else { 

					/* Load the default language files */				
					load_plugin_textdomain( 'woo_ua', false, $lang_dir );
				}
			}	
						
			public function uwa_pro_vendor_plugins_notice() {
				
				global $current_user;
					$user_id = $current_user->ID;

					/* If user clicks to ignore the notice, add that to their user meta */
					if (isset($_GET['uwa_vendor_plugin_notice_ignore']) && '0' == $_GET['uwa_vendor_plugin_notice_ignore']) {
					
						update_user_meta($user_id, 'uwa_vendor_plugin_notice_disable', 'true', true);
					}
				
				
					if (current_user_can('manage_options')) {
						$user_id = $current_user->ID;
						$user_hide_notice = get_user_meta( $user_id, 'uwa_vendor_plugin_notice_disable', true );				
						if ($user_hide_notice != "true") {
							echo '<div class="notice notice-info is-dismissible">  
							<p>' . sprintf(__('Ultimate WooCommerce Auction Pro integrates with popular Vendor plugins. If you want your users to be vendor then simply install and activate any one of below plugins | <a href="%s">Hide Notice</a> 
							<ol>
							<li><a href="https://wordpress.org/plugins/wc-multivendor-marketplace/" target="_blank">WCFM Multi-Vendor Marketplace by WC Lovers (FREE)</a> </li>
							<li><a href="https://woocommerce.com/products/product-vendors/?aff=10264&cid=1129132" target="_blank">WooCommerce Product Vendors</a> </li>
							<li><a href="https://wordpress.org/plugins/dokan-lite/" target="_blank">Dokan</a></li>																				
							</ol>
							', 'woo_ua'), esc_attr(add_query_arg('uwa_vendor_plugin_notice_ignore', '0'))) . '</p>
							</div>';
						}
					}		
			} /* end of function */
			
			
			
			/**
			 * Function For Place Bid using AJAX.  
			 *			
			 */				 
			public function placebid_uwa_ajax_placed_bid_callback () {
				$message = "";

				$uwa_place_bid = absint($_REQUEST['uwa_place_bid']);
				$uwa_bid_value = $_REQUEST['uwa_bid_value'];

				if (empty($uwa_place_bid) || !is_numeric($uwa_place_bid)) {
					return;
				}
				
				global $woocommerce;
			
				$product_id = absint($uwa_place_bid);
				$bid = abs(round(str_replace(',', '.', $uwa_bid_value), wc_get_price_decimals()));				
				$was_place_bid = false;
				$placed_bid = array();
				$placing_bid = wc_get_product($product_id);
				$product_type = method_exists( $placing_bid, 'get_type') ? $placing_bid->get_type() : $placing_bid->product_type;		
				$quantity = 1;
				
				//$message .=  "<Br>before if auction product";

				if ('auction' === $product_type) {

					//$message .= "<br>in if auction product";
					
					$product_data = wc_get_product($product_id);

					/* --aelia-- */
					$product_base_currency = $product_data->uwa_aelia_get_base_currency();   
  					$args = array("currency" => $product_base_currency);

					$current_user = wp_get_current_user();
					$outbiddeduser = $placing_bid->get_uwa_auction_current_bider();	
					$auction_high_current_bider = $product_data->get_uwa_auction_max_current_bider();
					$uwa_silent = $product_data->get_uwa_auction_silent();	
					if($auction_high_current_bider == $outbiddeduser){
						$outbiddeduser ="";
					}
					$uwa_silent_outbid_email = get_option('uwa_silent_outbid_email',"no");
					if($uwa_silent == 'yes'  && $uwa_silent_outbid_email =="no"){								
						$outbiddeduser ="";
					}	
					if($outbiddeduser  == $current_user->ID){
								$outbiddeduser ="";								
					}
					
					$UWA_Bid = new UWA_Bid; 			

					/* Placing Bid */  
					/* here we change function name */	

					$ret = $UWA_Bid->ajax_uwa_bidplace($product_id, $bid);

					if(isset($ret['success'])){
						if($ret['success']){
							$successmsg = '<div class="woocommerce-message" role="alert">'.
												$ret['success'].'</div>';

							$message .= $successmsg;
						}
					}	

					if(isset($ret['error'])){
						if($ret['error']){
							$errormsg = '<ul class="woocommerce-error" role="alert"><li>'.
											$ret['error'].'</li></ul>';

							$message .= $errormsg;
						}
					}	


					if ($ret['status']) {

						//$message .= "<Br>in if part of place bid";
						
							$type_of_antisnipping  = get_option('uwa_aviod_snipping_type');
								/*if($type_of_antisnipping != ""){*/
									
										if($type_of_antisnipping == "snipping_recursive"){
											
											do_action('uwa_extend_auction_time', $product_id);
											
										} /* end of if - snipping recursive */										
										else if($type_of_antisnipping == "snipping_only_once" || $type_of_antisnipping == "" ){ 
								
											/* Anti snipping hook and it must be called only once  -- start */
											$is_antisnipped = get_post_meta($product_id, 
												'woo_ua_auction_extend_time_antisnipping', true);
											/* Extend auction end time only if it not extended before */
											if($is_antisnipped != 'yes'){
												do_action('uwa_extend_auction_time', $product_id);
											}
											/* Anti snipping hook and it must be called only once  -- end */
											
										} /* end of if - snipping  only once */
								/*}*/
						
							
							$placebid_userid = $current_user->ID;

							/*  --- place bid message not to display when proxy setting enabled -- */
							$get_proxy_same_maxbid = get_option('uwa_proxy_same_maxbid');

							if($get_proxy_same_maxbid == "yes"){
								$bidmsg = get_user_meta($current_user->ID, "uwa_samemaxbid_bidmsg_display", true);
								$pid = get_user_meta($current_user->ID, "uwa_samemaxbid_bidmsg_auction", true);

								if($bidmsg == "no" && $pid == $product_id){									
									$placebid_userid = 	get_post_meta($product_id, 'woo_ua_auction_current_bider', true);
									update_user_meta($current_user->ID, "uwa_samemaxbid_sent_mail", "yes"); 

									delete_user_meta($current_user->ID, "uwa_samemaxbid_bidmsg_display");
									delete_user_meta($current_user->ID, "uwa_samemaxbid_bidmsg_auction");
								}
								else{									
									/* here we change function name */	
									$newmessage = ajax_uwa_bid_place_message($product_id);
									$message .= $newmessage;
								}									
							}
							else{																	
								/* here we change function name */	
								$newmessage = ajax_uwa_bid_place_message($product_id);
								$message .= $newmessage;
							}


						/* here we change function name */
						///$newmessage = ajax_uwa_bid_place_message($product_id);
						///$message .= $newmessage;

						$was_place_bid = true;
						$placed_bid[] = $product_id;						
							
						/* Send Notification to Bidder/Admin	*/			 
						if($was_place_bid){
							WC()->mailer();
							
							/* bid placed notification to bidder */
							do_action('uwa_pro_bid_place_email', $placebid_userid, 
								$placing_bid);
							do_action('uwa_pro_bid_place_email_admin', $placebid_userid, 
								$placing_bid);

							/* send watchlist mail */
							$watchlistusers = get_uwa_auction_watchlist_by_auctionid($product_id);

							if(count($watchlistusers) > 0){
								do_action('uwa_pro_watchlist_email', $product_data, 
									$watchlistusers);
							}
							
											
							if(!empty($outbiddeduser)){
								
								/* send mail to outbiddeduser user */
								do_action( 'uwa_pro_outbid_bid_email', $outbiddeduser,
									$placing_bid);
								do_action('ultimate_woocommerce_auction_outbid_bid', $product_id ,$outbiddeduser);		
							}									
						}

						/* get data for auction detail page and bids tab */

						/* note - if auction type is slient then no need to change any data *** */

						//$product_data = wc_get_product($posts_id);

						$uwa_auction_type = $product_data->get_uwa_auction_type();
						$uwa_proxy  = $product_data->get_uwa_auction_proxy();
						$uwa_silent = $product_data->get_uwa_auction_silent();
						$user_max_bid = $product_data->get_uwa_user_max_bid($product_id, 
							$current_user->ID );
						$uwa_reserved = $product_data->is_uwa_reserved();
						$uwa_reserve_met = $product_data->is_uwa_reserve_met();
						$uwa_bid_value = $product_data->uwa_bid_value();


						/* -------------- Next bids ------------------ */

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
									$display_alldata['next_bids'] = $uwa_next_bids;
								}
								else{   /* bid increment */

									if($uwa_proxy == "yes"){
										$uwa_next_bids = $product_data->get_uwa_next_bid_options_proxy($uwa_bid_value, 
											$bid_inc);
									}
									else{
										$uwa_next_bids = $product_data->get_uwa_next_bid_options($uwa_bid_value, $bid_inc);
									}
									$display_alldata['next_bids'] = $uwa_next_bids;
								}

							} /* end of if - slient */


						} /* end of if - directbid */

						
						/* -------------- Reserve Price ------------------ */

						if ($uwa_silent != 'yes'){	/* for normal and proxy */	
							if(($uwa_reserved === TRUE) && ($uwa_reserve_met === FALSE)){ 
								$betwtext = __( "Reserve price has not been met.", 
									'woo_ua' );								
								$reserve_text = "<p class='uwa_auction_reserve_not_met'>
									<strong>".$betwtext."</strong></p>";

								$display_alldata['reservetext'] = $reserve_text;
							} 

							if(($uwa_reserved === TRUE) && ($uwa_reserve_met === TRUE)) { 
								$betwtext = __( "Reserve price has been met.", 
									'woo_ua' ); 
								$reserve_text = "<p class='uwa_auction_reserve_met'>
									<strong>".$betwtext."</strong></p>";

								$display_alldata['reservetext'] = $reserve_text;
							} 
						}

						/* -------------- Maximum / Minimum Bid ------------------ */

						if ($uwa_proxy == 'yes' &&  
							$product_data->get_uwa_auction_max_current_bider() && get_current_user_id() == $product_data->get_uwa_auction_max_current_bider()) {
		
							$maxminbid = wc_price($product_data->get_uwa_auction_max_bid(), $args);
							$reverse_bid_text = ($uwa_auction_type == 'reverse' ? 
									__( 'Your Minimum Bid is', 
								'woo_ua' ) : __( 'Your Maximum Bid is', 'woo_ua' ));

							$maxmintext = __( $reverse_bid_text , 'woo_ua' )." ". 
								$maxminbid;
							
							$display_alldata['maxmintext'] = $maxmintext; 
						}

						/* --------------------- Bids table --------------------- */
						

						$display_alldata['uwa_bids_alldata'] = $product_data->get_uwa_bids_history_data($product_id);



						/* -------------- Place bid input min max value -------------- */

						if($uwa_auction_type == 'reverse' ){
							$min = 1;
							$max = $uwa_bid_value;
							$display_alldata['uwa_bid_maxval'] = $max;
						}else {
							if ($uwa_silent != 'yes'){
								$min = $uwa_bid_value;
							}
							if ($uwa_silent == 'yes'){  
								$min = "1";
							}
						}
						
						/* ----------------- change timer  -------------------- */

						/*$display_alldata['remaining_secs'] = $product_data->	get_uwa_remaining_seconds();*/

						$is_timerchanged = get_post_meta($product_id, 
					    	'woo_ua_auction_antisnipped_changed_timer', true);
						if($is_timerchanged == 'no'){
							$display_alldata['remaining_secs'] = $product_data->get_uwa_remaining_seconds();

								update_post_meta($product_id, 
					    	'woo_ua_auction_antisnipped_changed_timer', "yes");

							/*$message .= "<br>after antisnipped";
					    	$message .= "<br>Value is ". $display_alldata['remaining_secs'];*/						
						}
						

						/* -------------- Wining Losing text ------------------ */

							if(get_option('uwa_display_wining_losing_text') == 'yes'){

								$uwa_imgtext = "";
								$uwa_detailtext = "";

								$current_userid = get_current_user_id();
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

									$display_alldata['uwa_imgtext'] = $uwa_imgtext;
									$display_alldata['uwa_detailtext'] = $uwa_detailtext;
								}
									
						} /* end of if - wining losing */
						


						$display_alldata['uwa_bid_minval'] = $min;

						$display_alldata['uwa_curent_bid'] = 
							$product_data->get_price_html();
						$display_alldata['entervalue'] = 
							wc_price($product_data->uwa_bid_value(), $args);
						$display_alldata['auction_type'] = $uwa_auction_type;

						
						echo json_encode(array('allstatus' => 1, 'allmsg' => $message,
							'alldata_display' => $display_alldata));
						exit;
					}
					elseif(!$ret['status']){					

						//$message .= "<Br>in else part of place bid";
						$display_alldata = array();
						
						$current_user = wp_get_current_user();
						$display_alldata = array();

						$uwa_auction_type = $product_data->get_uwa_auction_type();
						$uwa_proxy  = $product_data->get_uwa_auction_proxy();
						$uwa_silent = $product_data->get_uwa_auction_silent();
						$user_max_bid = $product_data->get_uwa_user_max_bid($product_id, $current_user->ID );
						$uwa_reserved = $product_data->is_uwa_reserved();
						$uwa_reserve_met = $product_data->is_uwa_reserve_met();


						/* -------------- Next bids ------------------ */

						$uwa_bid_value = $product_data->uwa_bid_value();
						$display_alldata['auction_type'] = $uwa_auction_type;

							
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
									$display_alldata['next_bids'] = $uwa_next_bids;
								}
								else{   /* bid increment */

									if($uwa_proxy == "yes"){
										$uwa_next_bids = $product_data->get_uwa_next_bid_options_proxy($uwa_bid_value, 
											$bid_inc);
									}
									else{
										$uwa_next_bids = $product_data->get_uwa_next_bid_options($uwa_bid_value, $bid_inc);
									}
									$display_alldata['next_bids'] = $uwa_next_bids;
								}

							} /* end of if - slient */


						} /* end of if - directbid */



						/* -------------- Maximum / Minimum Bid ------------------ */

						if ($uwa_proxy == 'yes' &&  
							$product_data->get_uwa_auction_max_current_bider() && get_current_user_id() == $product_data->get_uwa_auction_max_current_bider()) {
		
							$maxminbid = wc_price($product_data->get_uwa_auction_max_bid(), $args);
							$reverse_bid_text = ($uwa_auction_type == 'reverse' ? 
									__( 'Your Minimum Bid is', 
								'woo_ua' ) : __( 'Your Maximum Bid is', 'woo_ua' ));	

							$maxmintext = __( $reverse_bid_text , 'woo_ua' )." ". 
								$maxminbid;
							$display_alldata['maxmintext'] = $maxmintext; 
						}

						$display_alldata['auction_type'] = $uwa_auction_type;

						echo json_encode(array('allstatus' => 0, 'allmsg' => $message,
							'alldata_display' => $display_alldata));


						exit;
					}

						
				} else {
					wc_add_notice(__('This product is not Auction', 'woo_ua'), 'error');
					return;
				}

				die();
				
			} /* end of function */
			
			
			
			/**
			 * Syncronise auction meta data with wpml
			 *
			 * Sync meta via translated products			 
			 *
			 */
			public function uwa_syncronise_metadata_wpml( $data ) {

				global $sitepress;

				$deflanguage = $sitepress->get_default_language();
				if ( is_array( $data ) ) {
					$product_id = $data['product_id'];
				} else {
					$product_id = $data;
				}

				$meta_values = get_post_meta( $product_id );
				$orginalid   = $sitepress->get_original_element_id( $product_id, 'post_product' );
				$trid        = $sitepress->get_element_trid( $product_id, 'post_product' );
				$all_posts   = $sitepress->get_element_translations( $trid, 'post_product' );

				unset( $all_posts[ $deflanguage ] );

				if ( ! empty( $all_posts ) ) {
					foreach ( $all_posts as $key => $translatedpost ) {
						if ( isset( $meta_values['woo_ua_product_condition'][0] ) ) {
							update_post_meta( $translatedpost->element_id, 'woo_ua_product_condition', $meta_values['woo_ua_product_condition'][0] );
						}

						if ( isset( $meta_values['woo_ua_opening_price'][0] ) ) {
							update_post_meta( $translatedpost->element_id, 'woo_ua_opening_price', $meta_values['woo_ua_opening_price'][0] );
						}

						if ( isset( $meta_values['woo_ua_lowest_price'][0] ) ) {
							update_post_meta( $translatedpost->element_id, 'woo_ua_lowest_price', $meta_values['woo_ua_lowest_price'][0] );
						}

						if ( isset( $meta_values['uwa_auction_proxy'][0] ) ) {
							update_post_meta( $translatedpost->element_id, 'uwa_auction_proxy', $meta_values['uwa_auction_proxy'][0] );
						}

						if ( isset( $meta_values['uwa_auction_silent'][0] ) ) {
							update_post_meta( $translatedpost->element_id, 'uwa_auction_silent', $meta_values['uwa_auction_silent'][0] );
						}

						if ( isset( $meta_values['woo_ua_bid_increment'][0] ) ) {
							update_post_meta( $translatedpost->element_id, 'woo_ua_bid_increment', $meta_values['woo_ua_bid_increment'][0] );
						}

						if ( isset( $meta_values['woo_ua_auction_type'][0] ) ) {
							update_post_meta( $translatedpost->element_id, 'woo_ua_auction_type', $meta_values['woo_ua_auction_type'][0] );
						}

						if ( isset( $meta_values['woo_ua_auction_start_date'][0] ) ) {
							update_post_meta( $translatedpost->element_id, 'woo_ua_auction_start_date', $meta_values['woo_ua_auction_start_date'][0] );
						}

						if ( isset( $meta_values['woo_ua_auction_end_date'][0] ) ) {
							update_post_meta( $translatedpost->element_id, 'woo_ua_auction_end_date', $meta_values['woo_ua_auction_end_date'][0] );
						}

						if ( isset( $meta_values['woo_ua_auction_has_started'][0] ) ) {
							update_post_meta( $translatedpost->element_id, 'woo_ua_auction_has_started', $meta_values['woo_ua_auction_has_started'][0] );
						}

						if ( isset( $meta_values['woo_ua_auction_closed'][0] ) ) {
							update_post_meta( $translatedpost->element_id, 'woo_ua_auction_closed', $meta_values['woo_ua_auction_closed'][0] );
						}

						if ( isset( $meta_values['woo_ua_auction_fail_reason'][0] ) ) {
							update_post_meta( $translatedpost->element_id, 'woo_ua_auction_fail_reason', $meta_values['woo_ua_auction_fail_reason'][0] );
						}

						if ( isset( $meta_values['woo_ua_order_id'][0] ) ) {
							update_post_meta( $translatedpost->element_id, 'woo_ua_order_id', $meta_values['woo_ua_order_id'][0] );
						}

						if ( isset( $meta_values['woo_ua_auction_payed'][0] ) ) {
							update_post_meta( $translatedpost->element_id, 'woo_ua_auction_payed', $meta_values['woo_ua_auction_payed'][0] );
						}

						if ( isset( $meta_values['woo_ua_auction_max_bid'][0] ) ) {
							update_post_meta( $translatedpost->element_id, 'woo_ua_auction_max_bid', $meta_values['woo_ua_auction_max_bid'][0] );
						}

						if ( isset( $meta_values['woo_ua_auction_max_current_bider'][0] ) ) {
							update_post_meta( $translatedpost->element_id, 'woo_ua_auction_max_current_bider', $meta_values['woo_ua_auction_max_current_bider'][0] );
						}

						if ( isset( $meta_values['woo_ua_auction_current_bid'][0] ) ) {
							update_post_meta( $translatedpost->element_id, 'woo_ua_auction_current_bid', $meta_values['woo_ua_auction_current_bid'][0] );
						}

						if ( isset( $meta_values['woo_ua_auction_current_bider'][0] ) ) {
							update_post_meta( $translatedpost->element_id, 'woo_ua_auction_current_bider', $meta_values['woo_ua_auction_current_bider'][0] );
						}

						if ( isset( $meta_values['woo_ua_auction_bid_count'][0] ) ) {
							update_post_meta( $translatedpost->element_id, 'woo_ua_auction_bid_count', $meta_values['woo_ua_auction_bid_count'][0] );
						}

						if ( isset( $meta_values['woo_ua_auction_current_bid_proxy'][0] ) ) {
							update_post_meta( $translatedpost->element_id, 'woo_ua_auction_current_bid_proxy', $meta_values['woo_ua_auction_current_bid_proxy'][0] );
						}

						if ( isset( $meta_values['woo_ua_auction_last_bid'][0] ) ) {
							update_post_meta( $translatedpost->element_id, 'woo_ua_auction_last_bid', $meta_values['woo_ua_auction_last_bid'][0] );
						}

						if ( isset( $meta_values['uwa_auction_relisted'][0] ) ) {
							update_post_meta( $translatedpost->element_id, 'uwa_auction_relisted', $meta_values['uwa_auction_relisted'][0] );
						}
						
						if ( isset( $meta_values['uwa_auto_renew_enable'][0] ) ) {
							update_post_meta( $translatedpost->element_id, 'uwa_auto_renew_enable', $meta_values['uwa_auto_renew_enable'][0] );
						}
						
						
						if ( isset( $meta_values['uwa_auto_renew_recurring_enable'][0] ) ) {
							update_post_meta( $translatedpost->element_id, 'uwa_auto_renew_recurring_enable', $meta_values['uwa_auto_renew_recurring_enable'][0] );
						}
						
						if ( isset( $meta_values['uwa_auto_renew_not_paid_enable'][0] ) ) {
							update_post_meta( $translatedpost->element_id, 'uwa_auto_renew_not_paid_enable', $meta_values['uwa_auto_renew_not_paid_enable'][0] );
						}	
						if ( isset( $meta_values['uwa_auto_renew_not_paid_hours'][0] ) ) {
							update_post_meta( $translatedpost->element_id, 'uwa_auto_renew_not_paid_hours', $meta_values['uwa_auto_renew_not_paid_hours'][0] );
						}
						
						if ( isset( $meta_values['uwa_auto_renew_no_bids_enable'][0] ) ) {
							update_post_meta( $translatedpost->element_id, 'uwa_auto_renew_no_bids_enable', $meta_values['uwa_auto_renew_no_bids_enable'][0] );
						}
						
						if ( isset( $meta_values['uwa_auto_renew_fail_hours'][0] ) ) {
							update_post_meta( $translatedpost->element_id, 'uwa_auto_renew_fail_hours', $meta_values['uwa_auto_renew_fail_hours'][0] );
						}
						
						if ( isset( $meta_values['uwa_auto_renew_no_reserve_enable'][0] ) ) {
							update_post_meta( $translatedpost->element_id, 'uwa_auto_renew_no_reserve_enable', $meta_values['uwa_auto_renew_no_reserve_enable'][0] );
						}
						
						if ( isset( $meta_values['uwa_auto_renew_reserve_fail_hours'][0] ) ) {
							update_post_meta( $translatedpost->element_id, 'uwa_auto_renew_reserve_fail_hours', $meta_values['uwa_auto_renew_reserve_fail_hours'][0] );
						}
						if ( isset( $meta_values['uwa_auto_renew_duration_hours'][0] ) ) {
							update_post_meta( $translatedpost->element_id, 'uwa_auto_renew_duration_hours', $meta_values['uwa_auto_renew_duration_hours'][0] );
						}
						if ( isset( $meta_values['woo_ua_auction_extend_time_antisnipping'][0] ) ) {
							update_post_meta( $translatedpost->element_id, 'woo_ua_auction_extend_time_antisnipping', $meta_values['woo_ua_auction_extend_time_antisnipping'][0] );
						}
						if ( isset( $meta_values['woo_ua_auction_extend_time_antisnipping_recursive'][0] ) ) {
							update_post_meta( $translatedpost->element_id, 'woo_ua_auction_extend_time_antisnipping_recursive', $meta_values['woo_ua_auction_extend_time_antisnipping_recursive'][0] );
						}
						if ( isset( $meta_values['woo_ua_buy_now'][0] ) ) {
							update_post_meta( $translatedpost->element_id, 'woo_ua_buy_now', $meta_values['woo_ua_buy_now'][0] );
						}						
						
						if ( isset( $meta_values['_regular_price'][0] ) ) {
							update_post_meta( $translatedpost->element_id, '_regular_price', $meta_values['_regular_price'][0] );
						}						
						
						if ( isset( $meta_values['_auction_wpml_language'][0] ) ) {
							update_post_meta( $translatedpost->element_id, '_lottery_wpml_language', $meta_values['_auction_wpml_language'][0] );
						}
						
						if ( isset( $meta_values['_done_one_time_payment'][0] ) ) {
							update_post_meta( $translatedpost->element_id, '_done_one_time_payment', $meta_values['_done_one_time_payment'][0] );
						}
						
						if ( isset( $meta_values['_done_one_time_sms'][0] ) ) {
							update_post_meta( $translatedpost->element_id, '_done_one_time_sms', $meta_values['_done_one_time_sms'][0] );
						}
						if ( isset( $meta_values['_uwa_stripe_auto_debit_status'][0] ) ) {
							update_post_meta( $translatedpost->element_id, '_uwa_stripe_auto_debit_status', $meta_values['_uwa_stripe_auto_debit_status'][0] );
						}
						 /* Auto dabit */
						if ( isset( $meta_values['woo_ua_winner_request_sent_for_autodabit_payment'][0] ) ) {
							update_post_meta( $translatedpost->element_id, 'woo_ua_winner_request_sent_for_autodabit_payment', $meta_values['woo_ua_winner_request_sent_for_autodabit_payment'][0] );
						}
						
						if ( isset( $meta_values['_uwa_won_sms_sent_status'][0] ) ) {
							update_post_meta( $translatedpost->element_id, '_uwa_won_sms_sent_status', $meta_values['_uwa_won_sms_sent_status'][0] );
						}
						
					}
				}
			}	
			

			public function uwa_pro_delete_user( $id ){
				global $wpdb;

				/* delete auction log details when user is deleted */
				if ( $id > 0 ){					
					$table = $wpdb->prefix."woo_ua_auction_log";			
					$wpdb->query($wpdb->prepare("DELETE FROM $table 
						WHERE userid = %d", $id));
				}
			}

			public function uwa_pro_delete_post( $postid ){
				global $wpdb;

				/* delete auction log details when product is deleted */
				if ( $postid > 0 ){					
					$table = $wpdb->prefix."woo_ua_auction_log";			
					$wpdb->query($wpdb->prepare("DELETE FROM $table 
						WHERE auction_id = %d", $postid));
				}
			}
			
						
		} /* end of class */
		
	} /* end of if - class*/

	$uwa_auctions = new Ultimate_WooCommerce_Auction_Pro();
	register_activation_hook( __FILE__, array( $uwa_auctions, 'uwa_pro_install' ) );
	register_deactivation_hook( __FILE__, array( $uwa_auctions, 'uwa_pro_deactivation' ) );
							
} 
else {

	add_action( 'admin_notices', 'uwa_install_woocommerce_admin_notice' );

	/**
	 * Print an admin notice if WooCommerce is deactivated
	 *	 
	 */	
	if( ! function_exists( 'uwa_install_woocommerce_admin_notice' ) ) {
		
		function uwa_install_woocommerce_admin_notice() { ?>
			<!-- <div class="error">
				<p>Ultimate WooCommerce Auction Pro <?php _e('is not enabled and effective without <a href="' . admin_url('plugin-install.php?tab=search&type=term&s=WooCommerce') . '" target="_blank">WooCommerce</a>.', 'woo_ua'); ?></p>	
			</div> -->

			<div class="updated" id="uwa-pro-installer-notice" style="padding: 1em; position: relative;">
            	<h2><?php _e( 'Your Ultimate WooCommerce Auction Pro is almost ready!', 'woo_ua' ); ?></h2>

	            <?php
	            $plugin_file      = basename( dirname( __FILE__ ) ) . '/ultimate-woocommerce-auction-pro.php';
	            $core_plugin_file = 'woocommerce/woocommerce.php';
	            ?>
	            <a href="<?php echo wp_nonce_url( 'plugins.php?action=deactivate&amp;plugin=' . $plugin_file . '&amp;plugin_status=all&amp;paged=1&amp;s=', 'deactivate-plugin_' . $plugin_file ); ?>" class="notice-dismiss" style="text-decoration: none;" title="<?php _e( 'Dismiss this notice', 'woo_ua' ); ?>"></a>

	            <?php if ( file_exists( WP_PLUGIN_DIR . '/' . $core_plugin_file ) && 
	            	is_plugin_inactive('woocommerce' ) ): ?>
	                <p><?php echo sprintf( __( 'You just need to activate the <strong>%s</strong> to make it functional.', 'woo_ua' ), 'WooCommerce' ); ?></p>
	                <p>
	                    <a class="button button-primary" 

	                    href="<?php echo wp_nonce_url( 
	                    	'plugins.php?action=activate&amp;plugin=' . $core_plugin_file . '&amp;plugin_status=all&amp;paged=1&amp;s&amp;_wpnonce=214569a558', 'activate-plugin_' . $core_plugin_file ); ?>"  title="<?php 
	                    	_e( 'Activate this plugin', 'woo_ua' ); ?>"><?php _e( 'Activate', 'woo_ua' ); ?></a>
	                </p>
	            <?php else: ?>
	                <p><?php echo sprintf( __( "You just need to install the %sCore Plugin%s to make it functional.", "woo_ua" ), '<a target="_blank" 
	                	href="https://wordpress.org/plugins/woocommerce/">', '</a>' ); ?></p>

	                <p>
	                  
	                   <a class="install-now button" data-slug="woocommerce" 
	                   		href="<?php echo admin_url('plugin-install.php?tab=search&type=term&s=WooCommerce') ;?>" aria-label="Install WooCommerce 3.9.3 now" data-name="WooCommerce 3.9.9">Install Now</a>
	                </p>
	            <?php endif ?>
	        </div>

			<?php			
		}
	}
	
	$plugin = plugin_basename( __FILE__ );

	if ( is_plugin_active( $plugin ) ) {
		deactivate_plugins( $plugin );
	}
	
} 