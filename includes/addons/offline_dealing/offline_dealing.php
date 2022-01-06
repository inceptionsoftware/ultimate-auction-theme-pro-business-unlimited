<?php

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}
	
class Ultimate_WooCommerce_Auction_Pro_Addons_Offline_Dealing { 

	private static $instance;

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
		add_action( 'init', array( &$this, 'init' ) );
	}

	/**
	 * Init the plugin after plugins_loaded so environment variables are set.
	 *			 
	 */
	public function init() {
		global $woocommerce;
		
		require_once ( UW_AUCTION_PRO_ADDONS. 'offline_dealing/class-uwa-offline-dealing-front.php');

		/*require_once ( UW_AUCTION_PRO_ADDONS .'offline_dealing/
			uwa-core-functions.php');*/
	}
}
Ultimate_WooCommerce_Auction_Pro_Addons_Offline_Dealing::get_instance();