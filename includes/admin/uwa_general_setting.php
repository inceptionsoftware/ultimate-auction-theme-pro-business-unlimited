<?php

/**
 * Ultimate WooCommerce Auction Pro Admin General Settings
 *
 * @package Ultimate WooCommerce Auction Pro
 * @author Nitesh Singh 
 * @since 1.0 
 *
 */ 
?>

<div class="uwa_main_setting wrap woocommerce">	
	<h1 class="uwa_admin_page_title">
				<?php _e( 'Settings', 'woo_ua' ); ?>
	</h1>
	<h2 class="uwa_main_h2"><?php _e( 'Ultimate WooCommerce Auction PRO', 'woo_ua' ); ?>
		<span class="uwa_version_text"><?php _e( 'Version :', 'woo_ua' ); ?> <?php echo UW_AUCTION_PRO_VERSION; ?></span>
	</h2>
	<div id="uwa-auction-banner-text">	
		<?php _e('If you like <a href="https://wordpress.org/support/plugin/ultimate-woocommerce-auction/reviews?rate=5#new-post" target="_blank"> our plugin working </a> with WooCommerce, please leave us a <a href="https://wordpress.org/support/plugin/ultimate-woocommerce-auction/reviews?rate=5#new-post" target="_blank">★★★★★</a> rating. A huge thanks in advance!', 'woo_ua' ); ?>	 
	</div>

	<div class="uwa_main_setting_page_nav">
		<h2 class="nav-tab-wrapper">
		<?php
		
		/*$uwa_default_setting_tabs = array(array( 'slug' => 'uwa_auction_setting', 'label' => __('Auction', 'woo_ua')),array( 'slug' => 'uwa_display_setting', 'label' => __('Display', 'woo_ua')),array( 'slug' => 'uwa_cron_setting', 'label' => __('Cron Setting', 'woo_ua')));*/ 
		$uwa_default_setting_tabs = array(array( 'slug' => 'uwa_auction_setting', 'label' => __('Auction', 'woo_ua')),array( 'slug' => 'uwa_display_setting', 'label' => __('Display', 'woo_ua')));
			
		$uwa_setting_tabs = apply_filters('uwa_admin_default_setting_tabs', $uwa_default_setting_tabs);	
		$active_tab = isset($_GET['setting_section']) ? $_GET['setting_section'] : 'uwa_auction_setting'; 
		
		foreach( $uwa_setting_tabs as $tab){ ?>
		
		<a href="?page=uwa_general_setting&setting_section=<?php echo $tab['slug'];?>" class="nav-tab <?php echo $active_tab == $tab['slug'] ? 'nav-tab-active' : ''; ?>"><?php echo $tab['label'];?></a>
		
	    <?php } ?>	
			
		</h2>
	</div>
	
	<?php 		
	if( $active_tab == 'uwa_auction_setting' ) {
		include_once( UW_AUCTION_PRO_ADMIN . '/uwa_general_setting_tab.php');
	} 
	if( $active_tab == 'uwa_display_setting' ) {
		include_once( UW_AUCTION_PRO_ADMIN . '/uwa_display_setting_tab.php');
	}
	
	if( $active_tab == 'uwa_cron_setting' ) {
		include_once( UW_AUCTION_PRO_ADMIN . '/uwa_cron_setting_tab.php');
	}
	
	do_action( 'uwa_admin_after_default_setting_tabs',$active_tab );
	
	
	?>
</div>