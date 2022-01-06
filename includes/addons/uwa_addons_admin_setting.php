<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Ultimate WooCommerce Auction Pro - business addons
 *
 * @package Ultimate WooCommerce Auction Pro
 * @author Nitesh Singh 
 * @since 1.0 
 *
 */

?>
	<div class="uwa_main_setting_content">

	<?php 

		$uwa_addons_default_setting_tabs = uwa_all_addons_list();			
		$uwa_addons_setting_tabs = apply_filters('uwa_admin_addons_default_setting_tabs', 
			$uwa_addons_default_setting_tabs);
		$uwa_enabled_addons_list = uwa_enabled_addons();		
		$active_addon_tab = isset($_GET['uwa_addons_tab']) ? $_GET['uwa_addons_tab'] : 
			$uwa_enabled_addons_list[0]; 
		$i = 0;
		$addons_len = count($uwa_enabled_addons_list);
		if($addons_len > 0 ){
			?>
			<ul class="subsubsub">	
				<?php 
					foreach( $uwa_addons_setting_tabs as $key => $addontab ){
						if(in_array($addontab['slug'], $uwa_enabled_addons_list)) { ?>
							<li>
								<a href="?page=uwa_general_setting&setting_section=uwa_addons_setting&uwa_addons_tab=<?php echo $addontab['slug'];?>" 
								class="<?php echo $active_addon_tab == $addontab['slug'] ? 
								'current' : ''; ?>"><?php echo $addontab['name'];?></a>
							</li>
							<?php  			
								if ($i != $addons_len - 1) { echo "|"; }
					 			$i++;		
			 			} 
			 		} 
			 	?>
			</ul>
			<?php 
		}		
		
		if(in_array($active_addon_tab, $uwa_enabled_addons_list)) {
			$addons_folder_name = str_replace(["uwa_", "_addon"], '', $active_addon_tab);
			$addons_file  = $addons_folder_name.'/admin/'.$addons_folder_name.'_setting.php';
			include_once( UW_AUCTION_PRO_ADDONS.$addons_file);
		}  
	?>
		
	</div>