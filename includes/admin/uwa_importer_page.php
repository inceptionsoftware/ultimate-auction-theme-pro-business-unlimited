<?php


/**
 * Ultimate WooCommerce Auction Pro Importer
 *
 * @package Ultimate WooCommerce Auction Pro
 * @author Nitesh Singh 
 * @since 1.1.1 
 *
 */ 

	$auction_import_url = admin_url(). "admin.php?page=uwa_auctions_import&tab=auction";
	$csv_url = UW_AUCTION_PRO_ASSETS_URL.'uwa_auction_import_sample.csv';
	$help_url = "https://docs.auctionplugin.net/article/91-5-how-to-bulk-import-auction-products";
	$wooimport_page_url = admin_url(). "edit.php?post_type=product&page=product_importer";

	?>

	<div class="uwa_main_setting wrap woocommerce">
		<h1 class="uwa_admin_page_title">
				<?php _e( 'Import Auction Products', 'woo_ua' ); ?>
		</h1>

		
		<div class="uwa_import">
			<h2> <?php _e( 'Follow below steps to import auction products inside your WooCommerce site.', 'woo_ua' ); ?>
			</h2>
			<ul>
				<li> 
					<a href = "<?php echo $csv_url; ?>" class="button button-primary"><?php _e('Download Sample CSV', 'woo_ua');?> </a>
					<?php _e( 'Download Sample CSV file.', 'woo_ua' ); ?>
				</li>

				<li> 
					<a target="_blank" href = "<?php echo $help_url; ?>" class="button button-primary"><?php _e('See Valid Values', 'woo_ua');?> </a>
					<?php _e( 'Open the article to see what valid values you need to enter in each field.', 'woo_ua' ); ?>
				</li>

				<li> 		
				<a href = "<?php echo $wooimport_page_url; ?>" class="button button-primary"><?php _e('WooCommerce Import Page', 'woo_ua');?> </a> 
					<?php _e( 'Once your CSV file is ready, you can import here.', 'woo_ua' ); ?> 
				</li>				
			</ul>	
		</div>
		
	</div>
