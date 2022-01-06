<?php

/**
 * Ultimate WooCommerce Auction Pro Admin Help page
 *
 * @package Ultimate WooCommerce Auction Pro
 * @author Nitesh Singh 
 * @since 1.1.1 
 *
 */ 

?>
	<div class="uwa_main_setting wrap woocommerce">	
		<h1 class="uwa_admin_page_title"> <?php _e( 'Help & Support', 'woo_ua' ); ?></h1>
		
		<h2 class="uwa_main_h2"><?php _e( 'Ultimate WooCommerce Auction PRO', 'woo_ua' ); ?><span class="uwa_version_text"><?php _e( 'Version :', 'woo_ua' ); ?>  <?php echo UW_AUCTION_PRO_VERSION; ?></span>
		</h2>
		
		

		
		<div class="uwa_help_section_main">	
				<div class="uwa_help_row row1">
					<div class="uwa_help_block">
						<img src="<?php echo UW_AUCTION_PRO_ASSETS_URL; ?>images/uwa_need_assistance.svg" alt="<?php esc_attr_e( 'Need Any Assistance?', 'woo_ua' ); ?>">
						<h3><?php _e( 'Need Any Assistance?', 'woo_ua' ); ?></h3>
						<p><?php _e( 'Our EXPERT Support Team is always ready to Help you out.', 'woo_ua' ); ?></p>
					</div>
					<div class="uwa_help_block">
						<img src="<?php echo UW_AUCTION_PRO_ASSETS_URL; ?>images/uwa_any_bugs.svg" alt="<?php esc_attr_e( 'Found Any Bugs?', 'woo_ua' ); ?>">
						<h3><?php _e( 'Found Any Bugs?', 'woo_ua' ); ?></h3>
						<p><?php _e( 'Report any Bug that you Discovered, Get Instant Solutions.', 'woo_ua' ); ?></p> 
					</div>
					<div class="uwa_help_block">	
						<img src="<?php echo UW_AUCTION_PRO_ASSETS_URL; ?>images/uwa_customization.svg" alt="<?php esc_attr_e( 'Require Customization?', 'woo_ua' ); ?>">
						<h3><?php _e( 'Require Customization?', 'woo_ua' ); ?></h3>
						<p><?php _e( 'We would Love to hear your Integration and Customization Ideas.', 'woo_ua' ); ?></p> 

						
					</div>	
				<div class="uwa_woo_report_text">
				<?php $uwa_woo_status = admin_url('admin.php?page=wc-status')?>
					<?php
					printf(__( 'Please download system report from <a href="%s" >WooCommerce </a> and send it to us while reporting any issue.', 'woo_ua' ),$uwa_woo_status); ?>
				</div>
					
                <div class="box-btn">
				<a target="_blank" class="button button-primary btn-custum" href="https://docs.auctionplugin.net/#contactModal"><?php _e( 'Contact Support', 'woo_ua' ); ?></a></div>
				</div>

				<div class="uwa_help_row row2">
					<div class="uwa_help_block">
						<img src="<?php echo UW_AUCTION_PRO_ASSETS_URL; ?>images/uwa_documentation.svg" alt="<?php esc_attr_e( 'Looking for Something?', 'woo_ua' ); ?>">

						<h3><?php _e( 'Looking for Something?', 'woo_ua' ); ?></h3>

						<p><?php _e( 'We have detailed documentation on every aspects of Ultimate WooCommerce Auction - Pro.', 'woo_ua' ); ?></p>

						<a target="_blank" class="button button-primary" href="https://docs.auctionplugin.net/"><?php _e( 'Visit the Plugin Documentation', 'woo_ua' ); ?></a>
					</div>

					<div class="uwa_help_block">

						<img src="<?php echo UW_AUCTION_PRO_ASSETS_URL; ?>images/uwa_free_like.svg" alt="<?php esc_attr_e( 'Like The Plugin?', 'woo_ua' ); ?>">

						<h3><?php _e( 'Like The Plugin?', 'woo_ua' ); ?></h3>

						<p><?php _e( 'Your Review is very important to us as it helps us to grow more.', 'woo_ua' ); ?></p>

						<a target="_blank" class="button button-primary" href="https://wordpress.org/support/plugin/ultimate-woocommerce-auction/reviews?rate=5#new-post"><?php _e( 'Review Us on WP.org', 'woo_ua' ); ?></a>
					</div>
				</div>	
			
		</div>
		
	</div>