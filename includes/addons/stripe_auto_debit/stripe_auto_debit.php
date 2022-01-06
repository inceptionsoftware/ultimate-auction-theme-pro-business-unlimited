<?php

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}
	
class Ultimate_WooCommerce_Auction_Pro_Addons_Stripe { 

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
		require_once ( UW_AUCTION_PRO_ADDONS. 'stripe_auto_debit/class-uwa-stripe-front.php');
		require_once ( UW_AUCTION_PRO_ADDONS .'stripe_auto_debit/uwa-core-functions.php');
	}
}
Ultimate_WooCommerce_Auction_Pro_Addons_Stripe::get_instance();