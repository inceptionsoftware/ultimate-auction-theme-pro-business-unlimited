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

class Uwa_Users_Bids_List_Table extends WP_List_Table {
	
    var $allData;
    var $uwa_bid_type; 

    public function uwa_auction_get_data($per_page, $page_number){ 

    	global $wpdb, $woocommerce;	
		
		$uwa_bid_type = isset($_GET["uwa_bid_type"]) ? $_GET["uwa_bid_type"] : "active";

		$auct_users = get_option('uwa_users_bids_list');
		$curr_user_id = get_current_user_id();		
		$table = $wpdb->prefix."woo_ua_auction_log";
		$table2 = $wpdb->prefix."posts";
		$table_users = $wpdb->prefix."users";
		
        if(!empty($auct_users)){
        	/* display userwise data */
        	/* get auctions for particular user only */
		$query = $wpdb->prepare("SELECT DISTINCT(auction_id) FROM {$table} as alg JOIN {$table2} as wposts ON wposts.ID = alg.auction_id 
				WHERE alg.userid = %d 				
				AND wposts.post_type = 'product'  
				AND wposts.post_status = 'publish'  
				ORDER by alg.auction_id DESC", $auct_users);				
			$selected = "one_user"; 
		}
		else{
			/* display all data */
			/* get auctions except current logged in user only */
			$query = $wpdb->prepare("SELECT DISTINCT(auction_id) FROM {$table} as alg JOIN {$table2} as wposts ON wposts.ID = alg.auction_id 
				WHERE alg.userid != %d 				
				AND wposts.post_type = 'product' 
				AND wposts.post_status = 'publish'  
				GROUP by alg.auction_id ORDER by alg.auction_id DESC", $curr_user_id);
			$selected = "all";
		}

		$bids_data = $wpdb->get_results( $query );
		
        $all_auctions = array();
        $won_bids_array = array();
        $not_won_bids_array = array();
        $active_bids_array = array();

		$data_array = array();

		if(count($bids_data) > 0){
			foreach($bids_data as $bdata){
				global $product;	
				$product_data = wc_get_product( $bdata->auction_id );
				$auc_id = $bdata->auction_id;
				if( $product_data->get_type() == 'auction' ) {					
					
					if($uwa_bid_type == "won" && 
						$product_data->get_uwa_auction_expired() == '2' ){

						/* get only one last win bid per auction */
						/* auction user must be present in wp_users table */

						$subquery = "SELECT MAX(bid) FROM $table WHERE 
							auction_id = $auc_id";
						$query_maxbid = "SELECT * FROM $table 
							WHERE auction_id = $auc_id 	
							AND userid IN (SELECT ID FROM $table_users) 			
							AND bid = ($subquery)";
						
						$newdata = $wpdb->get_results( $query_maxbid );

						if(count($newdata) > 0){

								// check userid is not login user
								if($curr_user_id != $newdata[0]->userid){
									if($selected == "one_user"){
										if($auct_users == $newdata[0]->userid){
											$won_bids_array[] = $newdata[0];
										}
									}
									else{
										$won_bids_array[] = $newdata[0];
									}							
								}
						} /* end of if count */

					}
					elseif ($uwa_bid_type == "not-won" && 
						$product_data->get_uwa_auction_expired() == '2' ){

						/* get multiple bids per auction */
						/* auction user must be present in wp_users table */
					
						$subquery = "SELECT MAX(bid) FROM $table WHERE 
							auction_id = $auc_id";
						$query_otherbids = "SELECT * FROM $table 
							WHERE auction_id = $auc_id
							AND userid IN (SELECT ID FROM $table_users)	
							AND bid != ($subquery) 
							ORDER by id DESC";
						
						$newdata = $wpdb->get_results( $query_otherbids );

						if(count($newdata) > 0){

								foreach($newdata as $single_auction){
										
									// check userid is not login user
									if($curr_user_id != $single_auction->userid){
										if($selected == "one_user"){
											if($auct_users == $single_auction->userid){
												$not_won_bids_array[] = $single_auction;
											}
										}
										else{
											$not_won_bids_array[] = $single_auction;
										}
									}

								} /* end of foreach */
						} /* end of if count */

					}
					elseif ($uwa_bid_type == "active" && 
							$product_data->get_uwa_auction_expired() == false ){

						/* get only one last bid per auction */
						/* auction user must be present in wp_users table */

						$subquery = "SELECT MAX(bid) FROM $table WHERE 
							auction_id = $auc_id";

						$query_maxbid = "SELECT * FROM $table 
							WHERE auction_id = $auc_id 
							AND userid IN (SELECT ID FROM $table_users)	
							AND bid = ($subquery)";
						
						$newdata = $wpdb->get_results( $query_maxbid );

						if(count($newdata) > 0){

								// check userid is not login user
								if($curr_user_id != $newdata[0]->userid){							
									if($selected == "one_user"){
										if($auct_users == $newdata[0]->userid){
											$active_bids_array[] = $newdata[0];
										}
									}
									else{
										$active_bids_array[] = $newdata[0];
									}
								}
						} /* end of if count */

					} /* end of elseif */
				

				} /* end of if - get type */

			} /* end of foreach */

		} /* end of if - count */

		
			if($uwa_bid_type=="won"){
				$all_auctions = $won_bids_array;
			}			
			elseif($uwa_bid_type=="not-won"){
				$all_auctions = $not_won_bids_array;
			}
			else{
				$all_auctions = $active_bids_array;
			}	


		if(count($all_auctions) > 0){
			foreach ( $all_auctions as $my_auction ) {
				
		    	$row = array();
				$product = wc_get_product( $my_auction->auction_id );
				$auc_id = $my_auction->auction_id;			

				$auction_proxy = get_post_meta($auc_id, 'uwa_auction_proxy', true);
				$auction_silent = get_post_meta($auc_id, 'uwa_auction_silent', true);   					
					if($auction_proxy == "yes"){
						$auction_type = __('Proxy', 'woo_ua');
					}elseif($auction_silent == "yes"){
						$auction_type = __('Silent', 'woo_ua');
					}else{
						$auction_type = __('Simple', 'woo_ua');
					}

				/* Auction Type column */
				$row['auction_type'] = $auction_type; 

				/* Product Title column */				
				$row['title'] = '<a href="'.get_permalink( $auc_id ).'">'.get_the_title(  
					$auc_id ).'</a>';
				
				/* Bid column */					
				$row['bid_value'] = wc_price($my_auction->bid);

				/* MaxBid column */
				if($auction_proxy == "yes"){
					$maxbid_metakey = "woo_ua_auction_user_max_bid_".$auc_id;
		        	$max_bid =  get_user_meta($my_auction->userid, $maxbid_metakey, true);	
					$row['max_bid_value'] = wc_price($max_bid);
				}
				else {
					$row['max_bid_value'] = wc_price(0);	
				}

				/* Winning Bid & User column */
				// old.. $bidder_name = uwa_user_display_name($my_auction->userid);
				$userobj = get_userdata( $my_auction->userid );
				$bidder_name = $userobj->display_name;

				$row['winning_bid_bidders'] = wc_price($my_auction->bid)." & ".$bidder_name;
				// OR .. $row['winning_bid_bidders'] = $row['bid_value']." & ".$bidder_name;

				/* Lost bid Username column */
				$row['lostusername'] = $bidder_name;

					/* enddate */
				$row['enddate'] = '';
				$row['enddate'] = $product->get_uwa_auction_end_dates();	
				

				/* Action column */
				$row_action = "";
				if ($uwa_bid_type == "active"){						
					$row_action = "<a href='#' data-id=".$my_auction->id." data-postid=".$auc_id." class='uwa_force_delete_bid button' >". __('Delete', 'woo_ua')."</a>";
				}

				if ($uwa_bid_type == 'won') {	
                    $product->get_uwa_auction_current_bider();				
					if (($my_auction->userid == $product->get_uwa_auction_current_bider() && 
						$product->get_uwa_auction_expired() == '2' && 
						!$product->get_uwa_auction_payed() ) ) {
						$row_action = "<a href='#' data-postid=".$my_auction->auction_id."  class='uwa_force_remind_to_pay button' >". __('Remind to Pay', 'woo_ua')."</a>";
					}								
				}

				if ($uwa_bid_type == 'not-won') {					
					$row_action = "";
				}

				$row['uwa_action'] = $row_action;
				
			   	$data_array[] = $row;

		    } /* end of foreach */

		} /* end of if - count -  all auctions */

		$this->allData = $data_array;
		return $data_array;

	} /* end of fuction - uwa_auction_get_data */


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
				'lostusername' => __('UserName','woo_ua'),
								       
			);
		}		
        return $columns;

    } /* end of fuction - get_columns */


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
		$uwa_bid_type = isset($_GET["uwa_bid_type"]) ? $_GET["uwa_bid_type"] : "active";
		$columns = $this->get_columns();
		$hidden = array();		
		$current_page = $this->get_pagenum();
		$sortable = $this->get_sortable_columns();
		$this->_column_headers = array($columns, $hidden, $sortable);
		$per_page = 20;

		/*$orderby = (!empty($_GET['orderby'])) ? $_GET['orderby'] : 'title';
		if ($orderby === 'title') {
			$this->items = $this->uwa_auction_sort_array($this->uwa_auction_get_data($per_page, 
				$current_page));
		} else {
			$this->items = $this->uwa_auction_get_data($per_page, $current_page);
		}*/

		
		$this->items = $this->uwa_auction_get_data($per_page, $current_page);

		/*$total_items = count($this->items); 

	    if($total_items > 0){
	    	echo "<div><b>Total items: $total_items</b></div>";
	    }*/
    	
	} /* end of fuction - prepare_items */


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

	} /* end of fuction - uwa_auction_sort_array */

	public function column_default($item, $column_name){
	    switch ($column_name) {
	        case 'auction_type':
	        case 'title':
	        case 'bid_value':
	        case 'max_bid_value':
	        case 'winning_bid_bidders':
    	    case 'enddate':
	        case 'uwa_action':
	        case 'lostusername':	        	  
	        	return $item[ $column_name ];
	        default:
	            return print_r($item, true); //Show the whole array for troubleshooting purposes
	    }

	} /* end of fuction - column_default */


} /* end of class */

function uwa_users__bids_list_page_handler_display() {
	global $wpdb; 
		
	$uwa_bid_type = isset($_GET["uwa_bid_type"]) ? $_GET["uwa_bid_type"] : "active";
		?>
		<div class="uwa_main_setting wrap">
	 		<br class="clear">		

			<?php
			 
			if (isset($uwa_bid_type)) {
				$manage_bid_tab = $uwa_bid_type;
			} else {
				$manage_bid_tab = 'active';
			}		

			/*$user_args = array(
				'blog_id'      => $GLOBALS['blog_id'],
				'orderby'      => 'login',
				'order'        => 'ASC'
			);

			$allUsers = get_users( $user_args );*/

			$table_auc_log = $wpdb->prefix."woo_ua_auction_log";
			$table_users = $wpdb->prefix."users";			

			/* get only one column */
			/*$users_id_list = $wpdb->get_col( "SELECT DISTINCT(userid) FROM 
				$table_auc_log");*/

			$users_id_list = $wpdb->get_col( "SELECT DISTINCT(userid) FROM 
				$table_auc_log where userid IN (SELECT ID FROM $table_users)" );
			
			if(isset($_POST['select-auction-users']))
				update_option('uwa_users_bids_list', $_POST['select-auction-users']);
				?>	
				<div class="uwa-action-container" style="float:right;margin-right: 10px;">
					 <form id="users-bids-form" name="users-bids-form" method="post" action="">
	        			<select id="select-auction-users" name="select-auction-users">
	            			<option id='all-au' value=''><?php _e('All', 'wdm-ultimate-auction');?>
	            			</option>
	            	<?php
	            

			            $curr_user_id = get_current_user_id();
			            foreach($users_id_list as $userid){

			            	if($userid != $curr_user_id){
			            		
				        		$au = get_userdata($userid);
				            	
				                ?>
				                <option id='au-<?php echo $au->user_login;?>' value='<?php echo $au->ID;?>' <?php if(isset($_POST['select-auction-users']) && $_POST['select-auction-users'] == $au->ID) { echo "selected='selected'"; } elseif(get_option('uwa_users_bids_list') == $au->ID) { echo "selected='selected'"; }?>><?php echo $au->user_login;?></option>
				                <?php 

			                } /* end of if */
			            }?>
        			</select>
        <input type="submit" value="<?php _e('Show Bids', 'wdm-ultimate-auction');?>" class="button-secondary" />
    </form>
        	</div>		

	    	<ul class="subsubsub">
					<li><a href="?page=uwa_auctions_bids_list&users_bids=true&uwa_bid_type=active" class="<?php echo $manage_bid_tab == 'active' ? 'current' : ''; ?>"><?php _e('Active Bids', 'woo_ua');?></a>|</li>
				<li><a href="?page=uwa_auctions_bids_list&users_bids=true&uwa_bid_type=won" class="<?php echo $manage_bid_tab == 'won' ? 'current' : ''; ?>"><?php _e('Bids Won', 'woo_ua');?></a>|</li>
				<li><a href="?page=uwa_auctions_bids_list&users_bids=true&uwa_bid_type=not-won" class="<?php echo $manage_bid_tab == 'not-won' ? 'current' : ''; ?>"><?php _e('Bids Lost', 'woo_ua');?></a></li>
	    	</ul>
	    	<br class="clear">
		
				<?php 
				global $wpdb;
				$myListTable = new Uwa_Users_Bids_List_Table();
				$myListTable->prepare_items();
				$myListTable->display();?>			
			</div>

		<?php 
}