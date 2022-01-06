<?php
/**
 * WCFM plugin views
 *
 * Plugin WC UWA Auctions List Views 
 */
 global $WCFM, $WCFMu;
$wcfmu_auctions_menus = array( 'live' => __('Live Auctions', 'wc-frontend-manager' ), 
	'expired' => __('Expired Auctions', 'wc-frontend-manager' ),
	'scheduled' => __('Future Auctions', 'wc-frontend-manager' ),
			);

$auctions_status = ! empty( $_GET['auctions_status'] ) ? sanitize_text_field( $_GET['auctions_status'] ) : 'live';
		
		
           ?>
			<div class="collapse wcfm-collapse" id="wcfm_uwa_auctions_listing">
				<div class="wcfm-page-headig">
					<span class="wcfmfa fa-calendar"></span>
					<span class="wcfm-page-heading-text"><?php _e( 'Manage Auctions', 'wc-frontend-manager' ); ?></span>
					<?php do_action( 'wcfm_page_heading' ); ?>
				</div>

				<div class="wcfm-collapse-content">
					<div id="wcfm_page_load"></div>
					<div class="wcfm-container wcfm-top-element-container">
						<ul class="wcfm_uwa_auctions_menus">
						<?php
						$is_first = true;
						foreach( $wcfmu_auctions_menus as $wcfmu_auctions_menu_key => $wcfmu_auctions_menu) {
						?>
						<li class="wcfm_uwa_auctions_menu_item">
						<?php
						if($is_first) $is_first = false;
						else echo " | ";
						?>
						<a class="<?php echo ( $wcfmu_bookings_menu_key == $auctions_status ) ? 'active' : ''; ?>" href="<?php echo get_wcfm_uwa_auction_url( $wcfmu_auctions_menu_key ); ?>"><?php echo $wcfmu_auctions_menu; ?></a>
						</li>
						<?php
						}
						?>
						</ul>
						
					<?php 
					echo '<a id="add_new_product_dashboard" class="add_new_wcfm_ele_dashboard text_tip" href="'.get_wcfm_edit_product_url().'" data-tip="' . __('Add New Product', 'wc-frontend-manager') . '"><span class="wcfmfa fa-cube"></span><span class="text">' . __( 'Add New', 'wc-frontend-manager') . '</span></a>';
					?>
					
					<div class="wcfm-clearfix"></div>
					</div>
				
				<div class="wcfm-clearfix"></div><br />
				
				<div class="wcfm-container">
			<div id="wwcfm_uwa_auctions_listing_expander" class="wcfm-content">
				<table id="wcfm-uwa-auctions" class="display" cellspacing="0" width="100%">
					<thead>
						<tr>
<th class="sorting_disabled" rowspan="1" colspan="1" style="width:41px;" >
<span class="wcfmfa fa-image text_tip" data-tip="Image" data-hasqtip="125" aria-describedby="qtip-125"></span></th>						
							<th><?php _e( 'Title', 'woo_ua' ); ?></th>
							<th><?php _e( 'Start On', 'woo_ua' ); ?></th>
							<th><?php _e( 'Ending On', 'woo_ua' ); ?></th>
							<th><?php _e( 'Current Price', 'woo_ua' ); ?></th>							
							<th><?php _e( 'Actions', 'woo_ua' ); ?></th>
							
						</tr>
					</thead>
					<tfoot>
						<tr>
					<th class="sorting_disabled" rowspan="1" colspan="1" style="width:41px;" >
<span class="wcfmfa fa-image text_tip" data-tip="Image" data-hasqtip="125" aria-describedby="qtip-125"></span></th>
							<th><?php _e( 'Title', 'woo_ua' ); ?></th>
							<th><?php _e( 'Start On', 'woo_ua' ); ?></th>
							<th><?php _e( 'Ending On', 'woo_ua' ); ?></th>
							<th><?php _e( 'Current Price', 'woo_ua' ); ?></th>						
							<th><?php _e( 'Actions', 'woo_ua' ); ?></th>
						</tr>
					</tfoot>
				</table>
				<div class="wcfm-clearfix"></div>
			</div>
		</div>
				
				
				
			</div>  <!--wcfm-collapse-content END -->
			</div>  <!--Main Div END -->