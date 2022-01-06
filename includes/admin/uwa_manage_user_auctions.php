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
 

/* $this->auction_status = (isset($_GET['auction_status']) && !empty($_GET['auction_status'])) ? $_GET['auction_status'] : 'live'; */
 
class Uwa_Manage_Users_Auctions_List_Table extends WP_List_Table {
	
	public $allData;
    public $auction_status;
	
	/**
     * Get auction data
     *
     * @param int $per_page, $page_number
     * @return array
     *
     */
	public function uwa_auction_get_data($per_page, $page_number){  

		global $wpdb; 
		$datetimeformat = get_option('date_format').' '.get_option('time_format');	
		$curr_user_id = get_current_user_id();	
		
		$pagination = ((int)$page_number - 1) * (int)$per_page;
		
		/* woo_ua_auction_bid_count	*/
		$meta_query = array(
						'relation' => 'AND',
							array(			     
								'key'  => 'woo_ua_auction_closed',
								'compare' => 'NOT EXISTS',
							),
							array(
								'key'     => 'woo_ua_auction_has_started',
								'value' => '1',
							)							
						);
		
		if (isset($_GET["auction_status"]) && $_GET["auction_status"]=='expired') {						
			$meta_query= array(
						'relation' => 'AND',
							array(			     
								'key' => 'woo_ua_auction_closed',
								'value' => array('1','2','3','4'),
								'compare' => 'IN',
							),							
						);
		}

		if (isset($_GET["auction_status"]) && $_GET["auction_status"]=='scheduled') {						
		
			$meta_query= array(						
							array(			     
								'key'  => 'woo_ua_auction_closed',
								'compare' => 'NOT EXISTS',
								),	
							array(
							'key'     => 'woo_ua_auction_started',
							'value' => '0',
							)	
						);						
		}
		
		$args = array(
			'post_type'	=> 'product',
			'post_status' => 'publish',
			'ignore_sticky_posts'	=> 1,
			'posts_per_page' => $per_page,
			'offset' => $pagination,			
			//'s'=> $search,			
			'meta_query' => array($meta_query),
			'tax_query' => array(array('taxonomy' => 'product_type' , 'field' => 'slug', 'terms' => 'auction')),
			'auction_arhive' => TRUE
		);
		
		$curr_user_id = get_current_user_id();
		$selected_user = isset($_POST['uwa_select_auction_users']) && !empty($_POST['uwa_select_auction_users']) ? $_POST['uwa_select_auction_users'] : '';
        if (!empty($selected_user)) {
            $post_user = get_user_by('login', $selected_user);
            $args['author__in'] = $post_user->ID;
        } else {
            //$args[ 'author__not_in'] = $curr_user_id;
            /* note : when author is 0 then auctions are not displayed in list */
            $args[ 'author__not_in'] = array($curr_user_id, 0); 
        }

        /*$search = (isset($_POST['uwa_auction_search'])) ? $_POST['uwa_auction_search'] : '';*/
		if(isset($_POST['uwa_auction_search'])){			
			$new_search = sanitize_text_field($_POST['uwa_auction_search']);
			$args['s'] = $new_search;
		}
		
		$auction_item_array = get_posts($args);
		$data_array = array();
		foreach ($auction_item_array as $single_auction) { 	
				
			$row = array();
			$auction_ID = $single_auction->ID;	
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
	        $row['title'] = '<a href="'.get_permalink( $auction_ID ).'">'.get_the_title(  $auction_ID ).'</a>';
			
			/* Start Date column */
			$row['create_date'] = '';
			$starting_on_date = get_post_meta($auction_ID, 'woo_ua_auction_start_date', true);
			$uwa_start_date = mysql2date($datetimeformat, $starting_on_date);
			$row['create_date'] = $uwa_start_date;

			/* End Date column */
			$row['end_date'] = '';
			$ending_date = get_post_meta($auction_ID, 'woo_ua_auction_end_date', true);
			$uwa_end_date = mysql2date($datetimeformat,$ending_date);			
			$row['end_date'] = $uwa_end_date;
		    
			/* Opening Price column */
			$row['opening_price'] = '';
			$opening_price = get_post_meta($auction_ID, 'woo_ua_opening_price', true);
			$current_bid_price = get_post_meta($auction_ID, 'woo_ua_auction_current_bid', true);
			$row['opening_price'] = wc_price($opening_price);
			if(!empty($current_bid_price)){
				$row['opening_price'] = wc_price($opening_price).' / '.wc_price($current_bid_price);
			}
			/* Reserve Price column */
			$row['reserve_price'] = wc_price(get_post_meta($auction_ID, 'woo_ua_lowest_price', true)); 
			
			/* Bidder/ Bid / Max Bid / Time column */
			$row['bidders'] = '';
			$results = array();
			$row_bidders = '';
			$query_bidders = 'SELECT * FROM '.$wpdb->prefix.'woo_ua_auction_log WHERE auction_id ='.$single_auction->ID.' ORDER BY id DESC LIMIT 2';
            $results = $wpdb->get_results($query_bidders);			
			if (!empty($results)) {
               				
                foreach ($results as $result) {

					//$bidder_name = get_userdata($result->userid)->display_name;	

					$obj_user = get_userdata($result->userid);	
					$bidder_name = "";
					if($obj_user){					
						$bidder_name = $obj_user->display_name;	
					}

	                if ($bidder_name){						

						$bidder_name = "<a href='".get_edit_user_link( $result->userid )."' target='_blank'>".$bidder_name.'</a>';						

					} else {						
						$bidder_name = 'User id:'.$result->userid;
	                } 

	                $userid = $result->userid;
	                $maxbid_metakey = "woo_ua_auction_user_max_bid_".$auction_ID;
	                $max_bid =  wc_price(get_user_meta($userid, $maxbid_metakey, true));

					$bid_amt = wc_price($result->bid);
					$bid_time = mysql2date($datetimeformat, $result->date);
					$row_bidders .= "<tr>";
					$row_bidders .= "<td>".$bidder_name." </td>";
					$row_bidders .= "<td>".$bid_amt."</td>";
					$row_bidders .= "<td>".$max_bid."</td>";
					$row_bidders .= "<td>".$bid_time."</td>";										
					if ($this->auction_status == 'live') {
						$bid_ID = $result->id;
						$bid_user_ID = $result->userid;
						$bid_amount = $result->bid;
						$row_bidders .= "<td><a href='#' class='button uwa_force_choose_winner' 
							data-bid_id=".$bid_ID." 
							data-bid_user_id=".$bid_user_ID." 
							data-bid_amount=".$bid_amount." 
							data-auction_id=".$auction_ID." >".__('Choose Winner', 'woo_ua')."</a></td>";
					}

					$row_bidders .= "</tr>";
					
                }

					/* $row['bidders'] = "<div class='uwa-bidder-list-".$single_auction->ID.">"; */
					$row['bidders'] = "<table class='uwa-bidslist uwa-bidder-list-".$auction_ID."'>";
					$row['bidders'] .= $row_bidders;
					$row['bidders'] .= "</table>";
					
					$query_bidders_count = 'SELECT * FROM '.$wpdb->prefix.'woo_ua_auction_log WHERE auction_id ='.$single_auction->ID.' ORDER BY id DESC';
	            	$results_count = $wpdb->get_results($query_bidders_count);
					if (count($results_count) > 2) {
	                        $row['bidders'] .= "
	                            <a href='#' class='uwa-see-more show-all'  rel='".$auction_ID."' >".__('See more', 'woo_ua').'</a>';
	                }				

			} else {				
				$row['bidders'] = __('No bids placed', 'woo_ua');
			}
			if ($this->auction_status == 'expired') {
				 
				$row['expiry_reason'] = "";
				$user_name="";
				$fail_reason = get_post_meta($auction_ID, 'woo_ua_auction_fail_reason', true); 
				$reason_closed = get_post_meta($auction_ID, 'woo_ua_auction_closed', true); 
				$current_bidder = get_post_meta($auction_ID, 'woo_ua_auction_current_bider', true);
				$order_id = get_post_meta($auction_ID, 'woo_ua_order_id', true);				 
				if($current_bidder){		
					/* $user_name = get_userdata($current_bidder)->display_name; */

					$obj_user = get_userdata($current_bidder);	
					$user_name = "";
					if($obj_user){					
						$user_name = $obj_user->display_name;	
					}	
				}
				if($fail_reason == 1){	
				
					$row['expiry_reason'] ='<span style="color:red;font-size:13px">'.__('No Bid', 'woo_ua').'</span>';
					
				} elseif($fail_reason == 2) {
					
					$row['expiry_reason'] = '<span style="color:red;font-size:13px">'.__('Reserve Not Met', 'woo_ua').'</span>';
				}elseif($reason_closed == 3){

					$row['expiry_reason'] = '<span style="color:#7ad03a;font-size:13px">'.__('Sold', 'woo_ua').'</span>';									
					if ( $order_id ){						
						$row['expiry_reason'] .='<br>';
						$row['expiry_reason'] .='<span style="font-size:13px">
						<a href="post.php?&action=edit&post='.$order_id.'">'.__('Order ID: ', 'woo_ua').$order_id.'</a></span>';
					}
				}else {

					$row['expiry_reason'] ='<span style="color:#7ad03a;font-size:13px">'.__('Won', 'woo_ua').'</span><br>';
					$row['expiry_reason'] .='<span style="font-size:13px">'.__('Highest bidder was', 'woo_ua').'</span><br>';
					$row['expiry_reason'] .='<span style="font-size:13px"><a href='.get_edit_user_link($current_bidder).'>'.$user_name.'</a></span><br>';					
					if ( $order_id ){
						$row['expiry_reason'] .='<span style="font-size:13px">
						<a href="post.php?&action=edit&post='.$order_id.'">'.__('Order ID: ', 'woo_ua').$order_id.'</a></span><br>';
					}
				}
			    
			}

			$row_action = "";

			if ($this->auction_status == 'live') {
				$auction_edit_url = get_edit_post_link($auction_ID);
				$row_action = "<a href=".$auction_edit_url." class='button'>".__('Edit', 'woo_ua')."</a> <br /><br />"	;		
				$row_action .= "<a href='#' class='button uwa_force_end_now' data-auction_id=".$auction_ID." >".__('End Now', 'woo_ua')."</a>";	
			}			

		    if ($this->auction_status == 'expired') {				
				$woo_ua_auction_payed = get_post_meta($auction_ID, 'woo_ua_auction_payed', true);
				$reason_closed = get_post_meta($auction_ID, 'woo_ua_auction_closed', true); 
			if(empty($woo_ua_auction_payed) && $woo_ua_auction_payed !='1') {
				
				$auction_edit_url = get_edit_post_link($auction_ID);
				$auction_relist_url = $auction_edit_url."&relist=true";
			    $row_action = "<a href=".$auction_relist_url." class='button'>".__('Relist', 'woo_ua')."</a>";	
				if($reason_closed == 2){
						$row_action .= "<a href='#' data-postid=".$auction_ID."  class='button uwa_force_remind_to_pay button' >". __('Remind to Pay', 'woo_ua')."</a>";
					}
				
			  }				
			}
			if ($this->auction_status == 'scheduled') {
				
				$row_action = "<a href='#' class='button uwa_force_make_live' data-auction_id=".$auction_ID." >".__('Make It Live', 'woo_ua')."</a>";
			}
			$row['uwa_action'] = $row_action;			
			$data_array[] = $row;
		}
	   
	   	$this->allData = $data_array;
		return $data_array;

	} /* end of function */
	
	/**
     * [REQUIRED] This method return columns to display in table
     * you can skip columns that you do not want to show
     * like content, or description
     *
     * @return array
     *
     */
   function get_columns() {
        $columns = array(           
            'auction_type' => __('Type', 'woo_ua'),
            'title' => __('Product Title', 'woo_ua'),
            'create_date' => __('Start Date', 'woo_ua'),
            'end_date' => __('End Date', 'woo_ua'),            
            'opening_price' => __('Opening / Current Price', 'woo_ua'),
            'reserve_price' => __('Reserve Price', 'woo_ua'),
            'bidders' => __('Bidder &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Bid&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Max Bid &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Time&nbsp;&nbsp;&nbsp;&nbsp;Choose Winner','woo_ua'),                    
			'uwa_action' => __('Actions', 'woo_ua'),
        );
		
		if ($this->auction_status == 'expired') {
			$columns = array(
				'auction_type' => __('Type', 'woo_ua'),
				'title' => __('Product Title', 'woo_ua'),
            	'create_date' => __('Start Date', 'woo_ua'),
            	'end_date' => __('End Date', 'woo_ua'),
				'opening_price' => __('Opening / Final Price', 'woo_ua'),
				'reserve_price' => __('Reserve Price', 'woo_ua'),
				 'bidders' => __('Bidder &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Bid&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Max Bid &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Time','woo_ua'),                    
				'expiry_reason' => __('Expiry Reason', 'woo_ua'),                    
				'uwa_action' => __('Actions', 'woo_ua'),                    			   
			);
		}
		
		if ($this->auction_status == 'scheduled') {
			 $columns = array(       
				'auction_type' => __('Type', 'woo_ua'),
				'title' => __('Product Title', 'woo_ua'),				
				'create_date' => __('Starting Date', 'woo_ua'),					
            	'end_date' => __('End Date', 'woo_ua'),							
				'opening_price' => __('Opening / Final Price', 'woo_ua'),
				'reserve_price' => __('Reserve Price', 'woo_ua'),
				'uwa_action' => __('Actions', 'woo_ua'),				       
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
            'create_date' => array('create_date', true),
            'end_date' => array('end_date', true),            
            'opening_price' => array('opening_price', true),
			'reserve_price' => __('Reserve Price', 'woo_ua'),
            'bidders' => array('bidders', true),
        );
        return $sortable_columns;
    }
	
	
	/**
     * [REQUIRED] This is the most important method
     *
     * It will get rows from database and prepare them to be showed in table
     *
     */
    function prepare_items(){

		
		$this->auction_status = (isset($_GET['auction_status']) && !empty($_GET['auction_status'])) ? $_GET['auction_status'] : 'live';
		$columns = $this->get_columns();
		$hidden = array();
		$per_page = '';
		$current_page = '';
		$sortable = $this->get_sortable_columns();
		$this->_column_headers = array($columns, $hidden, $sortable);
		$orderby = (!empty($_GET['orderby'])) ? $_GET['orderby'] : 'title';
		if ($orderby === 'title') {
			$this->items = $this->uwa_auction_sort_array($this->uwa_auction_get_data($per_page, $current_page));
		} else {
			$this->items = $this->uwa_auction_get_data($per_page, $current_page);
		}
		
		$per_page = 20;
		$current_page = $this->get_pagenum();		
	 	$meta_query = array(
						'relation' => 'AND',
							array(			     
								'key'  => 'woo_ua_auction_closed',
								'compare' => 'NOT EXISTS',
							),							
						);
		
		if ($this->auction_status == 'expired') {
			$meta_query= array(
						'relation' => 'AND',
							array(			     
								'key' => 'woo_ua_auction_closed',
								'value' => array('1','2','3','4'),
								'compare' => 'IN',
							),							
						);
		}
		
		if ($this->auction_status == 'scheduled') {						
			$meta_query= array(
						'relation' => 'AND',
							array(			     
								'key'  => 'woo_ua_auction_closed',
								'compare' => 'NOT EXISTS',
							),
							
							array(
							'key'     => 'woo_ua_auction_started',
							'value' => '0',
							)	
						);
		}
		
		$args = array(
			'post_type'	=> 'product',
			'post_status' => 'publish',
			'posts_per_page' => -1,			
			//'s'=> $search,
			'meta_key' => 'woo_ua_auction_last_activity',
			'orderby' => 'meta_value_num',
			'order'  => 'DESC',			
			'meta_query' => array($meta_query),
			'tax_query' => array(array('taxonomy' => 'product_type' , 'field' => 'slug', 'terms' => 'auction')),
			'auction_arhive' => TRUE
		);

		if ($this->auction_status == 'expired' || $this->auction_status == 'live') {	

			$args['meta_key'] = 'woo_ua_auction_last_activity';
			$args['orderby']= 'meta_value_num';
			$args['order']= 'DESC';
			
		} else {

			$args['meta_key'] = 'woo_ua_auction_start_date';
			$args['orderby']= 'meta_value';
			$args['order']= 'ASC';

		}
		
		
		$curr_user_id = get_current_user_id();
        $selected_user = isset($_POST['uwa_select_auction_users']) && 
        	!empty($_POST['uwa_select_auction_users']) ? $_POST['uwa_select_auction_users'] : '';
        if (!empty($selected_user)) {
        	$post_user = get_user_by('login', $selected_user);
            $args['author__in'] = $post_user->ID;
        } else {
            //$args[ 'author__not_in'] = $curr_user_id;
            /* note : when author is 0 then auctions are not displayed in list */
            $args[ 'author__not_in'] = array($curr_user_id, 0);
        }

		/*$search = (isset($_POST['s'])) ? $_POST['s'] : '';
        $search = (isset($_POST['uwa_auction_search'])) ? $_POST['uwa_auction_search'] : '';*/
        if(isset($_POST['uwa_auction_search'])){			
			$new_search = sanitize_text_field($_POST['uwa_auction_search']);
			$args['s'] = $new_search;
		}

		$auctions = get_posts($args);
    	$total_items = count($auctions);    
    	/* $this->found_data = array_slice($this->allData, (($current_page - 1) * $per_page), $per_page); */
    	$this->set_pagination_args(array(
        	'total_items' => $total_items,
        	'per_page' => $per_page,
        ));
    	$this->items = $this->uwa_auction_sort_array($this->uwa_auction_get_data($per_page, 
    		$current_page));  
	} /* end of fuction */

	public function get_result_e(){
    	return $this->allData;
	}

	/**
     * Auction sorting
     *
     * @param array $args
     * @return array
     *
     */
	public function uwa_auction_sort_array($args){
	    if (!empty($args)) {

	        $orderby = (!empty($_GET['orderby'])) ? $_GET['orderby'] : 'title';

			if($orderby === 'create_date') {			
	            $order = (!empty($_GET['order'])) ? $_GET['order'] : 'asc';
	        }
			else if($orderby === 'end_date') {			
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

	/**
     * Default column
     *
     * @param array $item, string $column_name 
     * @return array
     *
     */
	public function column_default($item, $column_name){
	    switch ($column_name) {
	        case 'auction_type':
	        case 'title':
	        case 'create_date':
	        case 'end_date':	        
	        case 'opening_price':
	        case 'reserve_price':
	        case 'bidders':	  
	        case 'expiry_reason':	  
	        case 'uwa_action':	  
	        	return $item[ $column_name ];
	        default:
	            return print_r($item, true); //Show the whole array for troubleshooting purposes
	    }
	}


} /* end of class */


function uwa_manage_users_auctions_list_page_handler_display() {	
 ?> 	

  	<?php if (current_user_can('administrator') ||  current_user_can('shop_manager') && 
  		$_GET['users_auctions'] =="true" ) { ?> 

		<div class="uwa-action-container" style="float:right;margin-right: 10px;">	
        	<div style="float: right;">
				<form action="" method="POST">
					<select id="uwa_select_auction_users" name="uwa_select_auction_users">
						<option id='all-au' value=''><?php _e('Select Auction Author', 'woo_ua');?>
						</option>
						<?php	
						$curr_user_id = get_current_user_id();
						$args = array(					
							'exclude'  => array( $curr_user_id ),
							'orderby' => 'post_count',
							'order'   => 'DESC'
						);	
						$allUsers = get_users($args);
						foreach ($allUsers as $au) {
							if ( count_user_posts_by_type( $au->ID, 'product') ) {
								$uwa_selected = "";
								if (isset($_POST['uwa_select_auction_users']) && 
									$_POST['uwa_select_auction_users'] == $au->user_login) {
									$uwa_selected = "selected='selected'";
								} 
								?>
								<option <?php echo $uwa_selected;?>  value='<?php echo $au->user_login; ?>'><?php echo $au->user_login;?></option>
								<?php 
					 		}
						}
							?>
					</select>

        			<input type="submit" value="<?php _e('Show auctions', 'woo_ua');?>" class="button-secondary" />
				</form>
			</div>		
	<?php  } ?> 

		<?php
		$selected_user = isset($_POST['uwa_select_auction_users']) && !empty($_POST['uwa_select_auction_users']) ? $_POST['uwa_select_auction_users'] : '';
		$post_user = get_user_by('login', $selected_user);	
		$selected_user_id = 0;
		if(!empty($post_user)){
			$selected_user_id = $post_user->ID;
		}
		
		if (isset($_REQUEST[ 'auction_status' ])) {
			$manage_auction_tab = sanitize_text_field($_REQUEST[ 'auction_status' ]);
		} else {
			$manage_auction_tab = 'live';
		}		
	?>	
	<div class="uwa-action-container" style="float:right;margin-right: 10px;">
		<form action="" method="POST">
			<input type="text" name="uwa_auction_search" value="<?php echo (isset($_POST['uwa_auction_search'])) ? $_POST['uwa_auction_search'] : ''; ?>" />
			<input type="submit" class="button-secondary" name="uwa_auction_search_submit" value="Search" />
			<input type="hidden" id="statusofauction" value="<?php echo $manage_auction_tab; ?>">
		</form>
    </div>

	
	    
	<ul class="subsubsub">
		<li><a href="?page=uwa_manage_auctions&&users_auctions=true&auction_status=live" class="<?php echo $manage_auction_tab == 'live' ? 'current' : '';
                    ?>"><?php _e('Live auctions', 'woo_ua');?></a> (<?php echo uwa_get_users_auctions_count('live',$selected_user_id);?>) |</li>
		<li><a href="?page=uwa_manage_auctions&users_auctions=true&auction_status=expired" class="<?php echo $manage_auction_tab == 'expired' ? 'current' : '';
                            ?>"><?php _e('Expired auctions', 'woo_ua');?></a> (<?php echo uwa_get_users_auctions_count('expired',$selected_user_id);?>) |</li>
		<li><a href="?page=uwa_manage_auctions&users_auctions=true&auction_status=scheduled" class="<?php echo $manage_auction_tab == 'scheduled' ? 'current' : ''; ?>"><?php _e('Future auctions', 'woo_ua');?></a> (<?php echo uwa_get_users_auctions_count('scheduled',$selected_user_id);?>)
		</li>
		
	</ul><br class="clear">
		
	<?php 
			global $wpdb;
			$table = new Uwa_Manage_Users_Auctions_List_Table();
			$table->prepare_items();			
			$table->display();
	?>
			
	</div>	
	<?php
		
}	