<?php

/**
 * 
 * @package Ultimate WooCommerce Auction PRO ADDON currency switcher
 * @author Nitesh Singh
 * @since 1.0
 * 
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class Ultimate_WooCommerce_Auction_Pro_Addons_Currency_Switcher { 

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
			
			/* enabled only when aelia plugin is activated */

			$blog_plugins = get_option( 'active_plugins', array() );
			$site_plugins = is_multisite() ? (array) maybe_unserialize( get_site_option(
				'active_sitewide_plugins' ) ) : array();

			if ( in_array( 'woocommerce-aelia-currencyswitcher/woocommerce-aelia-currencyswitcher.php', $blog_plugins ) || isset( $site_plugins['woocommerce-aelia-currencyswitcher/woocommerce-aelia-currencyswitcher.php'] ) ) {

				/* check for multiple currency */								

				add_action( 'init', array( &$this, 'init' ) );
			}
	}	

	/**
	 * Init the plugin after plugins_loaded so environment variables are set.
	 *			 
	 */
	public function init() {
		global $woocommerce;
		require_once ( UW_AUCTION_PRO_ADDONS .'currency_switcher/uwa-core-functions.php' );
	}

} /* end of class */

Ultimate_WooCommerce_Auction_Pro_Addons_Currency_Switcher::get_instance();