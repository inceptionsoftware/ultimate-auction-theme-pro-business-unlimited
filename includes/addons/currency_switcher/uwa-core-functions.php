<?php

/**
 * 
 * @package Ultimate WooCommerce Auction PRO ADDON cur switch
 * @author Nitesh Singh
 * @since 1.0
 * 
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

add_action( 'plugins_loaded', 'uwa_cs_plugins_loaded' );

function uwa_cs_plugins_loaded() {
}

if ( ! class_exists( "UWA_CS_Addon_Main" ) && class_exists( 'WooCommerce' ) ) {
		require_once( 'classes/class-uwa-cs-main.php' );
}