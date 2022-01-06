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
				<?php _e( 'Add Auction Products', 'woo_ua' ); ?>
	</h1>
	<h2 class="uwa_main_h2"><?php _e( 'Ultimate WooCommerce Auction PRO', 'woo_ua' ); ?>
		<span class="uwa_version_text"><?php _e( 'Version :', 'woo_ua' ); ?> <?php echo UW_AUCTION_PRO_VERSION; ?></span>
	</h2>

	
  
	<div id="uwa-auction-banner-text">	
		<?php _e('If you like <a href="https://wordpress.org/support/plugin/ultimate-woocommerce-auction/reviews?rate=5#new-post" target="_blank"> our plugin working </a> with WooCommerce, please leave us a <a href="https://wordpress.org/support/plugin/ultimate-woocommerce-auction/reviews?rate=5#new-post" target="_blank">★★★★★</a> rating. A huge thanks in advance!', 'woo_ua' ); ?>	 
	</div>	
   <div class="post-box-container">
			
	
		<div class="uwa_add_auction_produt_main">
			<img src="<?php echo UW_AUCTION_PRO_ASSETS_URL?>/images/add_auction.png">
			  <h2><?php _e('Add Auction Products:', 'woo_ua'); ?></h2> 
				<?php $auction_pro_add_url = admin_url('post-new.php?post_type=product'); ?>			 
				<?php $auction_product_importer = admin_url('edit.php?post_type=product&page=product_importer'); ?>			 
			 <a href="<?php echo $auction_pro_add_url ;?>" class="button button-primary"><?php _e('Add Auction Product', 'woo_ua'); ?></a>.
			 <a href="<?php echo $auction_product_importer ;?>" class="button"><?php _e('Import Auction Product', 'woo_ua'); ?></a>
		</div>
		<div class="uwa_add_auction_produt_hint">
		    		
		</div>
	
	</div>
</div>
