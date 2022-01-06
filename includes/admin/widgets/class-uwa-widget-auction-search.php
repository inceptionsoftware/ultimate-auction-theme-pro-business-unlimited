<?php

if (!defined('ABSPATH')) {
	exit;
}

/**
 * Auction Product Search Widget.
 *
 * @class UWA_Widget_Auction_Search
 * @package Ultimate WooCommerce Auction PRO
 * @author Nitesh Singh 
 * @since 1.0 
 *
 */
class UWA_Widget_Auction_Search extends WC_Widget {

	/**
	 * Constructor
	 *
	 */	
	public function __construct() {
		$this->widget_cssclass    = 'woocommerce widget_uwa_product_search';
		$this->widget_description = __( 'A search auctions form for your store.', 'woo_ua' );
		$this->widget_id          = 'woocommerce_auction_product_search';
		$this->widget_name        = __( 'Auction Product Search', 'woo_ua' );
		$this->settings           = array(
			'title' => array(
				'type'  => 'text',
				'std'   => '',
				'label' => __( 'Title', 'woocommerce' ),
			),
		);

		parent::__construct();
	}

	/**
	 * Output widget.
	 *
	 * @see WP_Widget
	 *
	 * @param array $args     Arguments.
	 * @param array $instance Widget instance.
	 *
	 */
	 public function widget( $args, $instance ) {
		$this->widget_start( $args, $instance );

		uwa_get_auction_product_search_form();

		$this->widget_end( $args );
	}	
}