<?php

/**
 * 
 * @package Ultimate WooCommerce Auction PRO ADDON buyer preminum
 * @author Nitesh Singh 
 * @since 1.0
 * 
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class Ultimate_WooCommerce_Auction_Pro_Addons_Buyers_Premium { 

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
		require_once ( UW_AUCTION_PRO_ADDONS .'buyers_premium/uwa-core-functions.php' );
	}

} /* end of class */

Ultimate_WooCommerce_Auction_Pro_Addons_Buyers_Premium::get_instance();