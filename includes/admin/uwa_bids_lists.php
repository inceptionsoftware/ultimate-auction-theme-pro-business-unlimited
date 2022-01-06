<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}


/* == NOTICE ===================================================================
 * Please do not alter this file. Instead: make a copy of the entire plugin, 
 * rename it, and work inside the copy. If you modify this plugin directly and 
 * an update is released, your changes will be lost!
 * ========================================================================== */



/*************************** LOAD THE BASE CLASS *******************************
 *******************************************************************************
 * The WP_List_Table class isn't automatically available to plugins, so we need
 * to check if it's available and load it if necessary. In this tutorial, we are
 * going to use the WP_List_Table class directly from WordPress core.
 *
 * IMPORTANT:
 * Please note that the WP_List_Table class technically isn't an official API,
 * and it could change at some point in the distant future. Should that happen,
 * I will update this plugin with the most current techniques for your reference
 * immediately.
 *
 * If you are really worried about future compatibility, you can make a copy of
 * the WP_List_Table class (file path is shown just below) to use and distribute
 * with your plugins. If you do that, just remember to change the name of the
 * class to avoid conflicts with core.
 *
 * Since I will be keeping this tutorial up-to-date for the foreseeable future,
 * I am going to work with the copy of the class provided in WordPress core.
 */

if(!class_exists('WP_List_Table')){
    require_once( ABSPATH . 'wp-admin/includes/class-wp-list-table.php' );
}

/************************** CREATE A PACKAGE CLASS *****************************
 *******************************************************************************
 * Create a new list table package that extends the core WP_List_Table class.
 * WP_List_Table contains most of the framework for generating the table, but we
 * need to define and override some methods so that our data can be displayed
 * exactly the way we need it to be.
 * 
 * To display this example on a page, you will first need to instantiate the class,
 * then call $yourInstance->prepare_items() to handle any data manipulation, then
 * finally call $yourInstance->display() to render the table to the page.
 * 
 * Our theme for this list table is going to be movies.
 */
 //$this->uwa_bid_type = isset($_GET["uwa_bid_type"]) ? $_GET["uwa_bid_type"] : "active";
 class Uwa_Bids_List_Table extends WP_List_Table {
	
    var $allData;
    var $uwa_bid_type;

   	public function uwa_auction_get_data($per_page, $current_page){ 

		global $wpdb, $woocommerce;		
		$curr_user_id = get_current_user_id();	
		
		$table = $wpdb->prefix."woo_ua_auction_log";
		$table2 = $wpdb->prefix."posts";
		
		$uwa_bid_type = isset($_GET["uwa_bid_type"]) ? $_GET["uwa_bid_type"] : "active";

		/* check that posts also available in wp_posts table too */
		/*$query   = $wpdb->prepare("SELECT * FROM $table as alg JOIN $table2 as wposts ON  wposts.ID = alg.auction_id WHERE alg.userid = %d  AND wposts.post_type = 'product' 
			GROUP by alg.auction_id ORDER by alg.date DESC", 
			$curr_user_id);*/

		$query = $wpdb->prepare("SELECT alg.auction_id, max(alg.bid) as bid, 
			alg.date, alg.userid FROM {$table} as alg JOIN {$table2} as wposts ON wposts.ID = alg.auction_id 
			WHERE alg.userid = %d 
			AND wposts.post_type = 'product' 
			AND wposts.post_status = 'publish'  
			GROUP by alg.auction_id ORDER by alg.auction_id DESC", $curr_user_id);
				
		$bids_data = $wpdb->get_results( $query );
		
		$all_auctions = array();
        $won_bids_array = array();
        $not_won_bids_array = array();
        $active_bids_array = array();
       
        $active_bids_count = 0;
		$lost_bids_count = 0;
		$won_bids_count = 0;
		$won_bids_products_ids = array();
		$data_array = array();
		
		foreach ( $bids_data as $bdata ) {
			
			global $product;
			$product_data = wc_get_product( $bdata->auction_id );
			if($product_data->get_type() == 'auction' ) {
				$auction_ID = $bdata->auction_id;
				$user_id = get_current_user_id();
				 
				if ($uwa_bid_type == "won" && $user_id == $product_data->get_uwa_auction_current_bider() && $product_data->get_uwa_auction_expired() == '2' ){
				
					$won_bids_array[] = $bdata;
					
				}elseif ($uwa_bid_type == "not-won" && $user_id != $product_data->get_uwa_auction_current_bider() && $product_data->get_uwa_auction_expired() == '2' ){
					
					$not_won_bids_array[] = $bdata;
					
				}elseif($uwa_bid_type == "active" && $product_data->get_uwa_auction_expired() == false){
						$active_bids_array[] = $bdata;
				}
			}
			
		} /* end of foreach */
		
			if(isset($_GET["uwa_bid_type"]) && $_GET["uwa_bid_type"]=="won"){
				$all_auctions = $won_bids_array;
			}
			elseif(isset($_GET["uwa_bid_type"]) && $_GET["uwa_bid_type"]=="not-won"){
				$all_auctions = $not_won_bids_array;
			}
			else{
				$all_auctions = $active_bids_array;
			}
				
        foreach ( $all_auctions as $my_auction ) {
		    global $product;
			$row = array();   
			$product = wc_get_product( $my_auction->auction_id );
			$auction_ID = $my_auction->auction_id;
			$user_id = get_current_user_id();
						
			$auction_types = get_post_meta($auction_ID, 'woo_ua_auction_type', true);
			$auction_proxy = get_post_meta($auction_ID, 'uwa_auction_proxy', true);
			$auction_silent = get_post_meta($auction_ID, 'uwa_auction_silent', true);   
				$row['auction_type'] = '';	
				if($auction_proxy =="yes"){
					$auction_type = __('Proxy', 'woo_ua');
				}elseif($auction_silent =="yes"){
					$auction_type = __('Silent', 'woo_ua');
				}else{
					$auction_type = __('Simple', 'woo_ua');
				}
							
				/* Auction Type column */
				$row['auction_type'] = $auction_type; 

				/* Product Title column */
				$row['title'] = '';	
				$row['title'] = '<a href="'.get_permalink( $auction_ID ).'">'.get_the_title(
					$auction_ID ).'</a>';				
				
				/* bid_value */
				$row['bid_value '] = '';	
				$row['bid_value'] = wc_price($my_auction->bid);
				
				/* max_bid_value */
				$row['max_bid_value '] = '';
				$maxbid_metakey = "woo_ua_auction_user_max_bid_".$auction_ID;
	            $max_bid =  wc_price(get_user_meta($my_auction->userid, $maxbid_metakey, true));	
				$row['max_bid_value'] = wc_price($max_bid);
				
				$bidder_name = uwa_user_display_name($my_auction->userid);
				$row['winning_bid_bidders '] = '';	
				$row['winning_bid_bidders'] = wc_price($my_auction->bid)." & ".$bidder_name;
				
				/* enddate */
				$row['enddate'] = '';
				$row['enddate'] = $product->get_uwa_auction_end_dates();	
				
				$row_action = "";
				if ($uwa_bid_type == 'active'){

					//$row_action = "<a href='#' data-id=".$my_auction->id." data-postid=".$auction_ID." class='uwa_force_delete_bid button' >". __('Delete', 'woo_ua')."</a>";							
				}

				if ($uwa_bid_type == 'won') {				
				
					if ( ($user_id == $product->get_uwa_auction_current_bider() && $product->get_uwa_auction_expired() == '2' && !$product->get_uwa_auction_payed() ) ) {
				     $checkout_url = esc_attr(add_query_arg("pay-uwa-auction",$product->get_id(), uwa_auction_get_checkout_url()));
					 $row_action = "<a href='".$checkout_url."'  class='uwa_force_pay_now button' >". __('Pay Now', 'woo_ua')."</a>"	;				
					}
				}				
				

				if ($uwa_bid_type == 'not-won') {
					$row_action = '';
				}

				$row['uwa_action'] = $row_action;				
				$data_array[] = $row;
			
        }  /* end of foreach */
			   
		$this->allData = $data_array;
		return $data_array;
	}    

 function get_columns() {
	  $uwa_bid_type = isset($_GET["uwa_bid_type"]) ? $_GET["uwa_bid_type"] : "active";
        $columns = array(           
            'auction_type' => __('Auction Type', 'woo_ua'),
            'title' => __('Product Title', 'woo_ua'),
            'bid_value' => __('Bid', 'woo_ua'),           
            'max_bid_value' => __('Max Bid', 'woo_ua'),          
            'winning_bid_bidders' => __('Winning Bid & User','woo_ua'),                    
			'uwa_action' => __('Actions', 'woo_ua'),
        );
		
		if ($uwa_bid_type == 'won') {
			$columns = array(
				'auction_type' => __('Auction Type', 'woo_ua'),
				'title' => __('Product Title', 'woo_ua'),
				'bid_value' => __('Bid', 'woo_ua'),				
				'max_bid_value' => __('Max Bid', 'woo_ua'),				
				'winning_bid_bidders' => __('Won Bid & User','woo_ua'),
				'enddate' => __('End date', 'woo_ua'),                    			   				                  
				'uwa_action' => __('Actions', 'woo_ua'),                    			   
			);
		}
		
		if ($uwa_bid_type == 'not-won') {
			 $columns = array(       
				'auction_type' => __('Auction Type', 'woo_ua'),
				'title' => __('Product Title', 'woo_ua'),				
				'bid_value' => __('Bid', 'woo_ua'),								
				'max_bid_value' => __('Max Bid', 'woo_ua'),
				/* 'winning_bid_bidders' => __('Winning Bid & User','woo_ua'),*/
								       
			);
		}		
        return $columns;
    }

	/**
     * [OPTIONAL] This method return columns that may be used to sort table
     * all strings in array - is column names
     * notice that true on name column means that its default sort
     *
     * @return array
     *
     */
    function get_sortable_columns(){
        $sortable_columns = array(			
            'auction_type' => array('auction_type', true),
            'title' => array('title', true),
            'bid_value' => array('bid_value', true),
            'max_bid_value' => array('max_bid_value', true),            
            'enddate' => array('enddate', true),
            'winning_bid_bidders' => array('winning_bid_bidders', true),
        );
        return $sortable_columns;
    }
	

	/**
     * [REQUIRED] This is the most important method
     *
     * It will get rows from database and prepare them to be showed in table
     */
    function prepare_items() {
		global $wpdb; 		

		$search = (isset($_POST['s'])) ? $_POST['s'] : '';
		$this->auction_status = (isset($_GET['auction_status']) && !empty($_GET['auction_status'])) ? 
			$_GET['auction_status'] : 'live';
		$columns = $this->get_columns();
		$hidden = array();
		$per_page = '';
		$current_page = '';
		$sortable = $this->get_sortable_columns();
		$this->_column_headers = array($columns, $hidden, $sortable);
		$orderby = (!empty($_GET['orderby'])) ? $_GET['orderby'] : 'title';
		if ($orderby === 'title') {
			$this->items = $this->uwa_auction_sort_array($this->uwa_auction_get_data($per_page, 
				$current_page));
		} else {
			$this->items = $this->uwa_auction_get_data($per_page, $current_page);
		}

		$per_page = 20;
		$current_page = $this->get_pagenum();
		$curr_user_id = get_current_user_id();			 
		
	    /* new data -- start */
	    /*$total_items = count($this->uwa_auction_get_data($per_page, 
	    	$current_page));

	    if($total_items > 0){
	    	echo "<div><b>Total items: $total_items</b></div>";
	    }*/
	    /* new data -- end */	

	    /*$this->items = $this->uwa_auction_sort_array($this->uwa_auction_get_data($per_page, 
	    	$current_page));*/
	    $this->items = $this->uwa_auction_get_data($per_page, 
	    	$current_page);
    	
	}

	public function get_result_e(){
    	return $this->allData;
	}

	public function uwa_auction_sort_array($args){

    	if (!empty($args)) {		
        	$orderby = (!empty($_GET['orderby'])) ? $_GET['orderby'] : 'title';

			if($orderby === 'auction_type') {				
	            $order = (!empty($_GET['order'])) ? $_GET['order'] : 'asc';
	        }
			else if($orderby === 'bid_value') {				
	            $order = (!empty($_GET['order'])) ? $_GET['order'] : 'asc';
	        }
			else if($orderby === 'max_bid_value') {				
	            $order = (!empty($_GET['order'])) ? $_GET['order'] : 'asc';
	        }
			else if($orderby === 'winning_bid_bidders') {				
	            $order = (!empty($_GET['order'])) ? $_GET['order'] : 'asc';
	        }			
			else {
	            $order = 'desc';
	        }		        
		
	        foreach ($args as $array) {
	            $sort_key[] = $array[$orderby];
	        }

	        if ($order == 'asc') {
	            array_multisort($sort_key, SORT_ASC, $args);
	        } else {
	            array_multisort($sort_key, SORT_DESC, $args);
	        }
    	}
    	return $args;
	}

public function column_default($item, $column_name){
	    switch ($column_name) {
	        case 'auction_type':
	        case 'title':
	        case 'bid_value':
	        case 'max_bid_value':
	        case 'winning_bid_bidders':	
	        case 'enddate':
	        case 'uwa_action':	  
	        	return $item[ $column_name ];
	        default:
	            return print_r($item, true); //Show the whole array for troubleshooting purposes
	    }
	}

}
/* end of class */

	
function uwa_bids_list_page_handler_display() {
		?>
		<div class="uwa_main_setting wrap">	
		
			<h1 class="uwa_admin_page_title">
					<?php _e( 'Manage Bids', 'woo_ua' ); ?>
			</h1>
			<h2 class="uwa_main_h2"><?php _e( 'Ultimate WooCommerce Auction PRO', 'woo_ua' ); ?>
				<span class="uwa_version_text"><?php _e( 'Version :', 'woo_ua' ); ?> <?php echo UW_AUCTION_PRO_VERSION; ?></span>
			</h2>
		
		<div id="uwa-auction-banner-text">	
			<?php _e('If you like <a href="https://wordpress.org/support/plugin/ultimate-woocommerce-auction/reviews?rate=5#new-post" target="_blank"> our plugin working </a> with WooCommerce, please leave us a <a href="https://wordpress.org/support/plugin/ultimate-woocommerce-auction/reviews?rate=5#new-post" target="_blank">★★★★★</a> rating. A huge thanks in advance!', 'woo_ua' ); ?>	 
    	</div>

	 	<br class="clear">
		<?php if (current_user_can('administrator') ||   current_user_can('shop_manager') ) { ?>   

			<div style="float:left;">
				<ul class="subsubsub">
					<li>
						<a class="uwa-highlight-btn <?php echo isset($_GET['users_bids'])!= 'true' ? 'highlight-btn-active' : 'highlight-btn-disabled';?>" href="?page=uwa_auctions_bids_list" ><?php _e('Your Bids', 'woo_ua');?></a>
					</li>
					<li>
						<a class="uwa-highlight-btn <?php echo isset($_GET['users_bids']) && $_GET['users_bids'] == 'true' ? 'highlight-btn-active' : 'highlight-btn-disabled';?>"	 	href="?page=uwa_auctions_bids_list&users_bids=true"><?php _e('User Bids', 'woo_ua'); ?></a>
					</li>
				</ul>
			</div>

			<br class="clear">	
		<?php  } ?> 

		<?php
		if (isset($_GET['page']) && $_GET['page'] = 'uwa_auctions_bids_list' && isset($_GET['users_bids']) 
			&& $_GET['users_bids'] == 'true') {
				include_once( UW_AUCTION_PRO_ADMIN . '/uwa_users_bids_lists.php');
				uwa_users__bids_list_page_handler_display();
        } 
		else {
	
	 
			if (isset($_REQUEST[ 'uwa_bid_type' ])) {
				$manage_bid_tab = sanitize_text_field($_REQUEST[ 'uwa_bid_type' ]);
			} else {
				$manage_bid_tab = 'active';
			}		

			?>	
			<div class="uwa-action-container" style="float:right;margin-right: 10px;">
				<form action="" method="POST">
					<input type="text" name="uwa_auction_search" value="<?php echo (isset($_POST['uwa_auction_search'])) ? $_POST['uwa_auction_search'] : ''; ?>" />
					<input type="submit" class="button-secondary" name="uwa_auction_search_submit" value="Search" />
				</form>
        	</div>		

	    	<ul class="subsubsub">
				<li><a href="?page=uwa_auctions_bids_list&uwa_bid_type=active" class="<?php echo $manage_bid_tab == 'active' ? 'current' : ''; ?>"><?php _e('Active Bids', 'woo_ua');?></a>|</li>
				<li><a href="?page=uwa_auctions_bids_list&uwa_bid_type=won" class="<?php echo $manage_bid_tab == 'won' ? 'current' : ''; ?>"><?php _e('Bids Won', 'woo_ua');?></a>|</li>
				<li><a href="?page=uwa_auctions_bids_list&uwa_bid_type=not-won" class="<?php echo $manage_bid_tab == 'not-won' ? 'current' : ''; ?>"><?php _e('Bids Lost', 'woo_ua');?></a></li>
	    	</ul>
	    	<br class="clear">
		
				<?php 
				global $wpdb;
				$myListTable = new Uwa_Bids_List_Table();
				$myListTable->prepare_items();
				$myListTable->display();?>			
			</div>

		<?php }
}
?>