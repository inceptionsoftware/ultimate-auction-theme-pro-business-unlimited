<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 *
 * Auction Shortcodes
 *
 * @class  UWA_Shortcode
 * @package Ultimate WooCommerce Auction PRO
 * @author Nitesh Singh
 * @since 1.0
 *
 */
 
class UWA_Shortcode extends WC_Shortcodes {

	private static $instance;

	/**
	 * Returns the *Singleton* instance of this class.
	 *
	 * @return Singleton The *Singleton* instance.
	 *
	 */
    public static function get_instance() {
        if ( null === self::$instance ) {
            self::$instance = new self();
        }
        return self::$instance;
    }
	
	public function __construct() {
			
		add_shortcode( 'uwa_new_auctions', array( $this, 'uwa_new_auctions_fun' ) );	
		add_shortcode( 'uwa_all_auctions', array( $this, 'uwa_all_auctions_fun' ) );
		add_shortcode( 'uwa_live_auctions', array( $this, 'uwa_live_auctions_fun' ) );		
		add_shortcode( 'uwa_ending_soon_auctions', array( $this, 'uwa_ending_soon_auctions_fun' ) );
		add_shortcode( 'uwa_expired_auctions', array( $this, 'uwa_expired_auctions_fun' ) );		
		add_shortcode( 'uwa_pending_auctions', array( $this, 'uwa_pending_auctions_fun' ) );
		add_shortcode( 'uwa_featured_auctions', array( $this, 'uwa_featured_auctions_fun' ) );		
	  
	}

	/**
	 * New Auction shortcode  
	 * [uwa_new_auctions days_when_added="10" columns="4" orderby="date" order="desc/asc" 
	 	show_expired="yes/no"]	 
	 *
	 * @param array $atts	 
	 *
	 */
	public function uwa_new_auctions_fun( $atts ) {

		global $woocommerce_loop, $woocommerce;		
		extract(shortcode_atts(array(
			//'per_page' 	=> '12',
			'category'  => '',
			'columns' 	=> '4',
			'orderby' => 'date',
			'order' => 'desc',
			'days_when_added' =>'12',
			'show_expired' =>'yes',
			'paginate' => 'false',
		  	'limit' => -1
		), $atts));

		$limit = (int)$limit;  // don't remove

		$meta_query = $woocommerce->query->get_meta_query();
		if($show_expired == 'no'){
        	$meta_query [] = array(
								'key'     => 'woo_ua_auction_closed',
								'compare' => 'NOT EXISTS',
							);
        }
				
		$days_when_added_pera = "-".$days_when_added." days";
		$after_day = wp_date('Y-m-d', strtotime($days_when_added_pera),get_uwa_wp_timezone());		
		$args = array(
			'post_type'	=> 'product',
			'post_status' => 'publish',
			'product_cat' => $atts['category'],
			'ignore_sticky_posts'	=> 1,
			//'posts_per_page' => -1,   // -1 is default for all results to display
			'posts_per_page' => $limit,
			'orderby' => $orderby,
			'order' => $order,
			'meta_query' => $meta_query,
			'date_query' => array(
                    array(
						'after' => $after_day 
                    ),
                ),
			'tax_query' => array(array('taxonomy' => 'product_type' , 'field' => 'slug', 'terms' => 'auction')),
			'auction_arhive' => TRUE
		);

		/* Set Pagination Variable */
		if($paginate === "true"){
			$paged = get_query_var('paged') ? get_query_var('paged') : 1;
			$args['paged'] = $paged;
			//$woocommerce_loop['paged'] = $paged;
		}

		ob_start();
		$products = new WP_Query( $args );
		$woocommerce_loop['columns'] = $columns;

		if ( $products->have_posts() ) : ?>

			<?php
			
				/* Pagination Top Text */				
				if($paginate === "true" && ($limit >= 1 || $limit === -1 ))  {				
					$args_toptext = array(
						'total'    => $products->found_posts,
						//'per_page' => $products->get( 'posts_per_page' ),
						'per_page' => $limit,
						'current'  => max(1, get_query_var('paged')),
					);
					wc_get_template( 'loop/result-count.php', $args_toptext );
				}
			?>

			<?php woocommerce_product_loop_start(); ?>

				<?php while ( $products->have_posts() ) : $products->the_post(); ?>

					<?php wc_get_template_part( 'content', 'product' ); ?>

				<?php endwhile; // end of the loop. ?>

			<?php woocommerce_product_loop_end(); ?>

	   <?php else : ?>

            <?php wc_get_template( 'loop/no-products-found.php' ); ?>

		<?php endif;

		wp_reset_postdata();

		/* ---  Display Pagination ---  */

		if ( $paginate === "true" && $limit >= 1 && $limit < $products->found_posts ) { // don't change condition else design conflicts
			$big = 999999999;
			$current = max(1, get_query_var('paged'));
			$total   = $products->max_num_pages;
			$base    = esc_url_raw( str_replace( $big, '%#%', remove_query_arg( 'add-to-cart', get_pagenum_link( $big, false ))));
			$format  = '?paged=%#%';			

			if ( $total <= 1 ) {
				return;
			}
			
			$display_data = '<nav class="woocommerce-pagination">';
			$display_data .= paginate_links( 
				apply_filters( 'woocommerce_pagination_args', 
					array( 
						'base'         => $base,
						'format'       => $format,						
						'add_args'     => false,						
						'current'      => $current,
						'total'        => $total,
						'prev_text'    => '&larr;',
						'next_text'    => '&rarr;',
						//'type'         => 'list',
						'end_size'     => 3,
						'mid_size'     => 3,
					) 
				));
			$display_data .= '</nav>';
			echo $display_data;
			
		} /* end of if - paginate */

		return '<div class="woocommerce">' . ob_get_clean() . '</div>';
	}	

	/**
	 * ALl Auction shortcode  
	 * [uwa_all_auctions columns=3 orderby="date" order="desc/asc"]	 
	 *	 
	 * @param array $atts
	 *	 
	 */
	public function uwa_all_auctions_fun( $atts ) {
		global $woocommerce_loop;		
		extract(shortcode_atts(array(
			'category'  => '',
			'columns' 	=> '4',
		  	'orderby'   => 'title',
		  	'order'     => 'asc',
		  	'paginate' => 'false',
		  	'limit' => -1,		  	
			), $atts));

		$limit = (int)$limit;  // don't remove

	  	$args = array(
			'post_type'	=> 'product',
			'post_status' => 'publish',
			'product_cat' => $atts['category'],
			'ignore_sticky_posts'	=> 1,
			'orderby' => $orderby,
			'order' => $order,
			//'posts_per_page' => -1,   // -1 is default for all results to display
			'posts_per_page' => $limit,
			'tax_query' => array(array('taxonomy' => 'product_type' , 'field' => 'slug', 'terms' => 'auction')),
			'auction_arhive' => TRUE,			
		);
	  	
		$product_visibility_terms  = wc_get_product_visibility_term_ids();
		$product_visibility_not_in = $product_visibility_terms['exclude-from-catalog'];
		if ( ! empty( $product_visibility_not_in ) ) {
					$tax_query[] = array(
						'taxonomy' => 'product_visibility',
						'field'    => 'term_taxonomy_id',
						'terms'    => $product_visibility_not_in,
						'operator' => 'NOT IN',
					);
		}

		/* skus */ 
		if(isset($atts['skus'])){
			$skus = explode(',', $atts['skus']);
		  	$skus = array_map('trim', $skus);
	    	$args['meta_query'][] = array(
	      		'key' 		=> '_sku',
	      		'value' 	=> $skus,
	      		'compare' 	=> 'IN'
	    	);
	  	}

		/* ids */
		if(isset($atts['ids'])){
			$ids = explode(',', $atts['ids']);
		  	$ids = array_map('trim', $ids);
	    	$args['post__in'] = $ids;
		}

		/* Set Pagination Variable */
		if($paginate === "true"){
			$paged = get_query_var('paged') ? get_query_var('paged') : 1;
			$args['paged'] = $paged;
			//$woocommerce_loop['paged'] = $paged;
		}
		
	  	ob_start();
		$products = new WP_Query( $args );
		
		$woocommerce_loop['columns'] = $columns;

		if ( $products->have_posts() ) : ?>

			<?php
			
				/* Pagination Top Text */				
				if($paginate === "true" && ($limit >= 1 || $limit === -1 ))  {				
					$args_toptext = array(
						'total'    => $products->found_posts,
						//'per_page' => $products->get( 'posts_per_page' ),
						'per_page' => $limit,
						'current'  => max(1, get_query_var('paged')),
					);
					wc_get_template( 'loop/result-count.php', $args_toptext );
				}
			?>

			<?php woocommerce_product_loop_start(); ?>

				<?php while ( $products->have_posts() ) : $products->the_post(); ?>

					<?php wc_get_template_part( 'content', 'product' ); ?>

				<?php endwhile; // end of the loop. ?>

				<?php woocommerce_product_loop_end(); ?>

	   	<?php else : ?>

        	<?php wc_get_template( 'loop/no-products-found.php' ); ?>

		<?php endif;

		wp_reset_postdata();

		/* ---  Display Pagination ---  */

		if ( $paginate === "true" && $limit >= 1 && $limit < $products->found_posts ) { // don't change condition else design conflicts
			$big = 999999999;
			$current = max(1, get_query_var('paged'));
			$total   = $products->max_num_pages;
			$base    = esc_url_raw( str_replace( $big, '%#%', remove_query_arg( 'add-to-cart', get_pagenum_link( $big, false ))));
			$format  = '?paged=%#%';			

			if ( $total <= 1 ) {
				return;
			}
			
			$display_data = '<nav class="woocommerce-pagination">';
			$display_data .= paginate_links( 
				apply_filters( 'woocommerce_pagination_args', 
					array( 
						'base'         => $base,
						'format'       => $format,						
						'add_args'     => false,						
						'current'      => $current,
						'total'        => $total,
						'prev_text'    => '&larr;',
						'next_text'    => '&rarr;',
						//'type'         => 'list',
						'end_size'     => 3,
						'mid_size'     => 3,
					) 
				));			
			$display_data .= '</nav>';
			echo $display_data;
			
		} /* end of if - paginate */

		return '<div class="woocommerce">' . ob_get_clean() . '</div>';
	}
	
	/**
	 * Live Auction shortcode  
	 * [uwa_live_auctions columns=3 orderby="date" order="desc/asc"]
	 *	
	 * @param array $atts	 
	 * 
	 */
	public function uwa_live_auctions_fun( $atts ) {
		global $woocommerce_loop, $woocommerce;	
		
		extract(shortcode_atts(array(
			'category'  => '',
			'columns' 	=> '4',
		  	'orderby'   => 'title',
		  	'order'     => 'asc',		  	
		  	'hidescheduled' => 'no',	
		  	'paginate' => 'false',
		  	'limit' => -1,
			), $atts));

		$limit = (int)$limit;  // don't remove

        $meta_query []= array(
							'key'     => 'woo_ua_auction_closed',
							'compare' => 'NOT EXISTS',
							);

        if($hidescheduled == 'yes'){
        	$meta_query []= array(
							'key'     => 'woo_ua_auction_started',
							'compare' => 'NOT EXISTS',
							);
        }
		
	  	$args = array(
			'post_type'	=> 'product',
			'post_status' => 'publish',
			'product_cat' => $atts['category'],
			'ignore_sticky_posts'	=> 1,
			'orderby' => $orderby,
			'order' => $order,
			'meta_query' => $meta_query,
			//'posts_per_page' => -1,   // -1 is default for all results to display
			'posts_per_page' => $limit,
			'tax_query' => array(array('taxonomy' => 'product_type' , 'field' => 'slug', 
				'terms' => 'auction')),
			'auction_arhive' => TRUE,			
		);
	  	
		$product_visibility_terms  = wc_get_product_visibility_term_ids();
		$product_visibility_not_in = $product_visibility_terms['exclude-from-catalog'];
		if ( ! empty( $product_visibility_not_in ) ) {
					$tax_query[] = array(
						'taxonomy' => 'product_visibility',
						'field'    => 'term_taxonomy_id',
						'terms'    => $product_visibility_not_in,
						'operator' => 'NOT IN',
					);
		}

		/* skus */
		if(isset($atts['skus'])){
			$skus = explode(',', $atts['skus']);
		  	$skus = array_map('trim', $skus);
	    	$args['meta_query'][] = array(
	      		'key' 		=> '_sku',
	      		'value' 	=> $skus,
	      		'compare' 	=> 'IN'
	    	);
	  	}

		/* ids */
		if(isset($atts['ids'])){
			$ids = explode(',', $atts['ids']);
		  	$ids = array_map('trim', $ids);
	    	$args['post__in'] = $ids;
		}
		
		/* Set Pagination Variable */
		if($paginate === "true"){
			$paged = get_query_var('paged') ? get_query_var('paged') : 1;
			$args['paged'] = $paged;
			//$woocommerce_loop['paged'] = $paged;
		}

	  	ob_start();
		$products = new WP_Query( $args );
		
		$woocommerce_loop['columns'] = $columns;

		if ( $products->have_posts() ) : ?>

			<?php
			
				/* Pagination Top Text */				
				if($paginate === "true" && ($limit >= 1 || $limit === -1 ))  {				
					$args_toptext = array(
						'total'    => $products->found_posts,
						//'per_page' => $products->get( 'posts_per_page' ),
						'per_page' => $limit,
						'current'  => max(1, get_query_var('paged')),
					);
					wc_get_template( 'loop/result-count.php', $args_toptext );
				}
			?>

			<?php woocommerce_product_loop_start(); ?>

				<?php while ( $products->have_posts() ) : $products->the_post(); ?>

					<?php wc_get_template_part( 'content', 'product' ); ?>

				<?php endwhile; // end of the loop. ?>

			<?php woocommerce_product_loop_end(); ?>
	   	<?php else : ?>

        	<?php wc_get_template( 'loop/no-products-found.php' ); ?>

		<?php endif;

		wp_reset_postdata();


		/* ---  Display Pagination ---  */

		if ( $paginate === "true" && $limit >= 1  && $limit < $products->found_posts ) { // don't change condition else design conflicts
		
			$big = 999999999;
			$current = max(1, get_query_var('paged'));
			$total   = $products->max_num_pages;
			$base    = esc_url_raw( str_replace( $big, '%#%', remove_query_arg( 'add-to-cart', get_pagenum_link( $big, false ))));
			$format  = '?paged=%#%';			

			if ( $total <= 1 ) {
				return;
			}
			
			$display_data = '<nav class="woocommerce-pagination">';
			$display_data .= paginate_links( 
				apply_filters( 'woocommerce_pagination_args', 
					array( 
						'base'         => $base,
						'format'       => $format,						
						'add_args'     => false,						
						'current'      => $current,
						'total'        => $total,
						'prev_text'    => '&larr;',
						'next_text'    => '&rarr;',
						//'type'         => 'list',
						'end_size'     => 3,
						'mid_size'     => 3,
					) 
				));			
			$display_data .= '</nav>';
			echo $display_data;
			
		} /* end of if - paginate */

		return '<div class="woocommerce">' . ob_get_clean() . '</div>';
	}

	/**
	 * Ending soon Auction shortcode
	 * [uwa_ending_soon_auctions]
	 * 
	 * @param array $atts	 
	 *
	 */
	public function uwa_ending_soon_auctions_fun( $atts ) {

		global $woocommerce_loop, $woocommerce;		
		extract(shortcode_atts(array(
			//'per_page' 	=> '12',
			'category'  => '',
			'columns' 	=> '4',
			'order' => 'asc',
			'orderby' => 'meta_value',
			'hidescheduled' => 'yes',
			'paginate' => 'false',
		  	'limit' => -1,
		), $atts));

		$limit = (int)$limit;  // don't remove
		
		$meta_query = $woocommerce->query->get_meta_query();
		
        $meta_query []= array(
							'key'     => 'woo_ua_auction_closed',
							'compare' => 'NOT EXISTS',
							);
        if($hidescheduled == 'yes'){
        	$meta_query []= array(
							'key'     => 'woo_ua_auction_started',
							'compare' => 'NOT EXISTS',
							);
        }
		$args = array(
			'post_type'	=> 'product',
			'post_status' => 'publish',
			'product_cat' => $atts['category'],
			'ignore_sticky_posts'	=> 1,
			//'posts_per_page' => $per_page,
			//'posts_per_page' => -1,   // -1 is default for all results to display
			'posts_per_page' => $limit,
			'orderby' => $orderby,
			'order' => $order,
			'meta_query' => $meta_query,
			'tax_query' => array(array('taxonomy' => 'product_type' , 'field' => 'slug', 
				'terms' => 'auction')),
            'meta_key' => 'woo_ua_auction_end_date',
			'auction_arhive' => TRUE
		);
		
		/* Set Pagination Variable */
		if($paginate === "true"){
			$paged = get_query_var('paged') ? get_query_var('paged') : 1;
			$args['paged'] = $paged;
			//$woocommerce_loop['paged'] = $paged;
		}

		ob_start();

		$products = new WP_Query( $args );

		$woocommerce_loop['columns'] = $columns;

		if ( $products->have_posts() ) : ?>

			<?php
			
				/* Pagination Top Text */				
				if($paginate === "true" && ($limit >= 1 || $limit === -1 ))  {				
					$args_toptext = array(
						'total'    => $products->found_posts,
						//'per_page' => $products->get( 'posts_per_page' ),
						'per_page' => $limit,
						'current'  => max(1, get_query_var('paged')),
					);
					wc_get_template( 'loop/result-count.php', $args_toptext );
				}
			?>

			<?php woocommerce_product_loop_start(); ?>

				<?php while ( $products->have_posts() ) : $products->the_post(); ?>

					<?php wc_get_template_part( 'content', 'product' ); ?>

				<?php endwhile; // end of the loop. ?>

			<?php woocommerce_product_loop_end(); ?>
	   	<?php else : ?>
            <?php wc_get_template( 'loop/no-products-found.php' ); ?>

		<?php endif;

		wp_reset_postdata();

		/* ---  Display Pagination ---  */

		if ( $paginate === "true" && $limit >= 1 && $limit < $products->found_posts) { // don't change condition else design conflicts
			$big = 999999999;
			$current = max(1, get_query_var('paged'));
			$total   = $products->max_num_pages;
			$base    = esc_url_raw( str_replace( $big, '%#%', remove_query_arg( 'add-to-cart', get_pagenum_link( $big, false ))));
			$format  = '?paged=%#%';			

			if ( $total <= 1 ) {
				return;
			}
			
			$display_data = '<nav class="woocommerce-pagination">';
			$display_data .= paginate_links( 
				apply_filters( 'woocommerce_pagination_args', 
					array( 
						'base'         => $base,
						'format'       => $format,						
						'add_args'     => false,						
						'current'      => $current,
						'total'        => $total,
						'prev_text'    => '&larr;',
						'next_text'    => '&rarr;',
						//'type'         => 'list',
						'end_size'     => 3,
						'mid_size'     => 3,
					) 
				));			
			$display_data .= '</nav>';
			echo $display_data;
			
		} /* end of if - paginate */

		return '<div class="woocommerce">' . ob_get_clean() . '</div>';
	}

	/**
	 * Expired Auction shortcode
	 * [uwa_expired_auctions]
	 *
	 * @param array $atts	 
	 * 
	 */
	public function uwa_expired_auctions_fun( $atts ) {
		global $woocommerce_loop, $woocommerce;	
		extract(shortcode_atts(array(
			//'per_page' 	=> '12',
			'category'  => '',
			'columns' 	=> '4',
			'orderby' => 'meta_value',
			'order' => 'desc',
			'paginate' => 'false',
		  	'limit' => -1,
		), $atts));

		$limit = (int)$limit;  // don't remove

		$meta_query = $woocommerce->query->get_meta_query();

		$meta_query []= array(
			'key'     => 'woo_ua_auction_closed',
			'compare' => 'EXISTS',
		);

		$args = array(
			'post_type'	=> 'product',
			'post_status' => 'publish',
			'product_cat' => $atts['category'],
			'ignore_sticky_posts'	=> 1,
			//'posts_per_page' => $per_page,
			//'posts_per_page' => -1,   // -1 is default for all results to display
			'posts_per_page' => $limit,
			'orderby' => $orderby,
			'order' => $order,
			'meta_query' => $meta_query,
			'tax_query' => array(array('taxonomy' => 'product_type' , 'field' => 'slug', 
				'terms' => 'auction')),
			'meta_key' => 'woo_ua_auction_start_date',
			'auction_arhive' => TRUE,
			'show_expired_auctions' => TRUE
		);

		/* Set Pagination Variable */
		if($paginate === "true"){
			$paged = get_query_var('paged') ? get_query_var('paged') : 1;
			$args['paged'] = $paged;
			//$woocommerce_loop['paged'] = $paged;
		}

		ob_start();
		$products = new WP_Query( $args );
		$woocommerce_loop['columns'] = $columns;

		if ( $products->have_posts() ) : ?>

			<?php
			
				/* Pagination Top Text */				
				if($paginate === "true" && ($limit >= 1 || $limit === -1 ))  {				
					$args_toptext = array(
						'total'    => $products->found_posts,
						//'per_page' => $products->get( 'posts_per_page' ),
						'per_page' => $limit,
						'current'  => max(1, get_query_var('paged')),
					);
					wc_get_template( 'loop/result-count.php', $args_toptext );
				}
			?>

		 	<?php woocommerce_product_loop_start(); ?>

			<?php while ( $products->have_posts() ) : $products->the_post(); ?>

				 	<?php wc_get_template_part( 'content', 'product' ); ?>

			<?php endwhile; // end of the loop. ?>

		 	<?php woocommerce_product_loop_end(); ?>

		<?php else : ?>

				<?php wc_get_template( 'loop/no-products-found.php' ); ?>

		<?php endif;

		wp_reset_postdata();

		/* ---  Display Pagination ---  */

		if ( $paginate === "true" && $limit >= 1 && $limit < $products->found_posts) { // don't change condition else design conflicts
			$big = 999999999;
			$current = max(1, get_query_var('paged'));
			$total   = $products->max_num_pages;
			$base    = esc_url_raw( str_replace( $big, '%#%', remove_query_arg( 'add-to-cart', get_pagenum_link( $big, false ))));
			$format  = '?paged=%#%';			

			if ( $total <= 1 ) {
				return;
			}
			
			$display_data = '<nav class="woocommerce-pagination">';
			$display_data .= paginate_links( 
				apply_filters( 'woocommerce_pagination_args', 
					array( 
						'base'         => $base,
						'format'       => $format,						
						'add_args'     => false,						
						'current'      => $current,
						'total'        => $total,
						'prev_text'    => '&larr;',
						'next_text'    => '&rarr;',
						//'type'         => 'list',
						'end_size'     => 3,
						'mid_size'     => 3,
					) 
				));			
			$display_data .= '</nav>';
			echo $display_data;
			
		} /* end of if - paginate */

		return '<div class="woocommerce">' . ob_get_clean() . '</div>';
	}

	/**
	 * Pending Auction shortcode
	 * [uwa_pending_auctions]
	 * 
	 * @param array $atts	 
	 *
	 */
	public function uwa_pending_auctions_fun( $atts ) {
		global $woocommerce_loop, $woocommerce;		
		extract(shortcode_atts(array(
			//'per_page' 	=> '12',
			'category'  => '',
			'columns' 	=> '4',
			'orderby' => 'meta_value',
			'order' => 'asc',
			'paginate' => 'false',
		  	'limit' => -1,
		), $atts));

		$limit = (int)$limit;  // don't remove

		$meta_query = $woocommerce->query->get_meta_query();
        $meta_query []= array(
							'key'     => 'woo_ua_auction_closed',
							'compare' => 'NOT EXISTS',
					);

        $meta_query []=  array( 'key' => 'woo_ua_auction_started',
						            'value'=> '0',);
		$args = array(
			'post_type'	=> 'product',
			'post_status' => 'publish',
			'product_cat' => $atts['category'],
			'ignore_sticky_posts'	=> 1,
			//'posts_per_page' => $per_page,
			//'posts_per_page' => -1,   // -1 is default for all results to display
			'posts_per_page' => $limit,
			'orderby' => $orderby,
			'order' => $order,
			'meta_query' => $meta_query,
			'tax_query' => array(array('taxonomy' => 'product_type' , 'field' => 'slug', 
			'terms' => 'auction')),
            'meta_key' => 'woo_ua_auction_start_date',
			'auction_arhive' => TRUE,
			'show_schedule_auctions' => TRUE
		);

		/* Set Pagination Variable */
		if($paginate === "true"){
			$paged = get_query_var('paged') ? get_query_var('paged') : 1;
			$args['paged'] = $paged;
			//$woocommerce_loop['paged'] = $paged;
		}

		ob_start();
		$products = new WP_Query( $args );

		$woocommerce_loop['columns'] = $columns;
		if ( $products->have_posts() ) : ?>

			<?php
			
				/* Pagination Top Text */				
				if($paginate === "true" && ($limit >= 1 || $limit === -1 ))  {				
					$args_toptext = array(
						'total'    => $products->found_posts,
						//'per_page' => $products->get( 'posts_per_page' ),
						'per_page' => $limit,
						'current'  => max(1, get_query_var('paged')),
					);
					wc_get_template( 'loop/result-count.php', $args_toptext );
				}
			?>

			<?php woocommerce_product_loop_start(); ?>

				<?php while ( $products->have_posts() ) : $products->the_post(); ?>

					<?php wc_get_template_part( 'content', 'product' ); ?>

				<?php endwhile; // end of the loop. ?>

			<?php woocommerce_product_loop_end(); ?>

		<?php else : ?>

        	<?php wc_get_template( 'loop/no-products-found.php' ); ?>

		<?php endif;

		wp_reset_postdata();

		/* ---  Display Pagination ---  */

		if ( $paginate === "true" && $limit >= 1 && $limit < $products->found_posts) { // don't change condition else design conflicts
			$big = 999999999;
			$current = max(1, get_query_var('paged'));
			$total   = $products->max_num_pages;
			$base    = esc_url_raw( str_replace( $big, '%#%', remove_query_arg( 'add-to-cart', get_pagenum_link( $big, false ))));
			$format  = '?paged=%#%';			

			if ( $total <= 1 ) {
				return;
			}
			
			$display_data = '<nav class="woocommerce-pagination">';
			$display_data .= paginate_links( 
				apply_filters( 'woocommerce_pagination_args', 
					array( 
						'base'         => $base,
						'format'       => $format,						
						'add_args'     => false,						
						'current'      => $current,
						'total'        => $total,
						'prev_text'    => '&larr;',
						'next_text'    => '&rarr;',
						//'type'         => 'list',
						'end_size'     => 3,
						'mid_size'     => 3,
					) 
				));			
			$display_data .= '</nav>';
			echo $display_data;
			
		} /* end of if - paginate */

		return '<div class="woocommerce">' . ob_get_clean() . '</div>';
	}

	/**
	 * Featured Auction shortcode
	 * [uwa_featured_auctions]
	 * 
	 * @param array $atts	 
	 *
	 */	
	public function uwa_featured_auctions_fun( $atts ) {
		global $woocommerce_loop, $woocommerce;
		extract(shortcode_atts(array(
			//'per_page' 	=> '12',
			'columns' 	=> '4',
			'orderby' => 'date',
			'order' => 'desc',
			'paginate' => 'false',
		  	'limit' => -1,		  	
		), $atts));

		$limit = (int)$limit;  // don't remove

		$args = array(
			'post_type'	=> 'product',
			'post_status' => 'publish',
			'ignore_sticky_posts'	=> 1,
			//'posts_per_page' => $per_page,
			//'posts_per_page' => -1,   // -1 is default for all results to display
			'posts_per_page' => $limit,
			'orderby' => $orderby,
			'order' => $order,
			'tax_query' => array(array('taxonomy' => 'product_type' , 'field' => 'slug', 'terms' => 'auction')),
			'auction_arhive' => TRUE,
		);

		$args['tax_query'][] = array(
 				'taxonomy' => 'product_visibility',
				'field'    => 'name',
				'terms'    => 'featured',
    	);

		/* Set Pagination Variable */
		if($paginate === "true"){
			$paged = get_query_var('paged') ? get_query_var('paged') : 1;
			$args['paged'] = $paged;
			//$woocommerce_loop['paged'] = $paged;
		}

		ob_start();
		$products = new WP_Query( $args );
		$woocommerce_loop['columns'] = $columns;
		if ( $products->have_posts() ) : ?>

			<?php
			
				/* Pagination Top Text */				
				if($paginate === "true" && ($limit >= 1 || $limit === -1 ))  {				
					$args_toptext = array(
						'total'    => $products->found_posts,
						//'per_page' => $products->get( 'posts_per_page' ),
						'per_page' => $limit,
						'current'  => max(1, get_query_var('paged')),
					);
					wc_get_template( 'loop/result-count.php', $args_toptext );
				}
			?>

			<?php woocommerce_product_loop_start(); ?>

			<?php while ( $products->have_posts() ) : $products->the_post(); ?>

				<?php wc_get_template_part( 'content', 'product' ); ?>

			<?php endwhile; // end of the loop. ?>

			<?php woocommerce_product_loop_end(); ?>
			
		<?php else : ?>
		
        	<?php wc_get_template( 'loop/no-products-found.php' ); ?>

		<?php endif;		

		wp_reset_postdata();

		/* ---  Display Pagination ---  */

		if ( $paginate === "true" && $limit >= 1 && $limit < $products->found_posts ) { // don't change condition else design conflicts
			$big = 999999999;
			$current = max(1, get_query_var('paged'));
			$total   = $products->max_num_pages;
			$base    = esc_url_raw( str_replace( $big, '%#%', remove_query_arg( 'add-to-cart', get_pagenum_link( $big, false ))));
			$format  = '?paged=%#%';			

			if ( $total <= 1 ) {
				return;
			}
			
			$display_data = '<nav class="woocommerce-pagination">';
			$display_data .= paginate_links( 
				apply_filters( 'woocommerce_pagination_args', 
					array( 
						'base'         => $base,
						'format'       => $format,						
						'add_args'     => false,						
						'current'      => $current,
						'total'        => $total,
						'prev_text'    => '&larr;',
						'next_text'    => '&rarr;',
						//'type'         => 'list',
						'end_size'     => 3,
						'mid_size'     => 3,
					) 
				));			
			$display_data .= '</nav>';
			echo $display_data;
			
		} /* end of if - paginate */

		return '<div class="woocommerce">' . ob_get_clean() . '</div>';
	}

} /* end of class */

UWA_Shortcode::get_instance();