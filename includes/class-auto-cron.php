<?php
/**
 * main functions for plugin
 *
 * @package Ultimate WooCommerce Auction PRO
 * @author Nitesh Singh 
 * @since 1.0
 *	
 */
 
	/* Callback to change the auction status */
	add_action('scheduled_process_auction', 'woo_ua_process_auction', 10);
	add_action('scheduled_ending_soon_email', 'woo_ua_ending_soon_email', 10);
	add_action('scheduled_auto_relist', 'woo_ua_auto_relist', 10);
	add_action('scheduled_payment_reminder_email', 'woo_ua_payment_reminder_email', 10);

		function woo_ua_process_auction(){
			update_option( 'uwa_process_auction_cron', 'yes' );
			$meta_query= array(	array('key'  => 'woo_ua_auction_closed',	'compare' => 'NOT EXISTS'),
				array('key' => 'woo_ua_auction_has_started','compare' =>'==', 'value'=>'1'),);
				
				$args = array(
					'post_type' => 'product',
					'posts_per_page' => -1,
					'meta_query'=> $meta_query,
					'meta_key' => 'woo_ua_auction_end_date',
					'orderby' => 'meta_value',
					'order' => 'ASC',
					'tax_query' => array(array('taxonomy' => 'product_type', 'field' => 'slug', 'terms' => 'auction')),
					'auction_arhive' => TRUE,
					'show_past_auctions' => TRUE,
					'show_future_auctions' => TRUE,
				);

				$the_query = new WP_Query($args);						
				if ($the_query->have_posts()) {
					while ($the_query->have_posts()): $the_query->the_post();
						$product_data = wc_get_product($the_query->post->ID);
						if (method_exists( $product_data, 'get_type') && $product_data->get_type() == 'auction' ) {
							$product_data->is_uwa_expired(); // this goes to is_uwa_expired function make change as per this function.
						}
					endwhile;
				}	
			
		}
		
		/*
			End Ending soon mail Hook 
		*/
			
		function woo_ua_ending_soon_email(){
			
			update_option( 'uwa_ending_soon_email_cron', 'yes' );
			$uwa_ending_soon = get_option( 'woocommerce_woo_ua_email_auction_ending_bidders_settings' );    

			if ( $uwa_ending_soon['enabled'] === 'yes' ) {
				$uwa_interval = $uwa_ending_soon['uwa_interval'];
				$uwa_interval_time = date( 'Y-m-d H:i', current_time( 'timestamp' ) + ( $uwa_interval * HOUR_IN_SECONDS ) );						
				$args = array(
							'post_type'          => 'product',
							'posts_per_page'     => '100', 
							'tax_query'          => array(
								array(
									'taxonomy' => 'product_type',
									'field'    => 'slug',
									'terms'    => 'auction',
								),
							),
							'meta_query'         => array(
								'relation' => 'AND',        
								array(
									'key'     => 'woo_ua_auction_has_started',
									'value' => '1',
								),                            
								array(
									'key'     => 'woo_ua_auction_closed',
									'compare' => 'NOT EXISTS',
								),
								array(
										'key'     => 'uwa_auction_sent_ending_soon',									
										'compare' => 'NOT EXISTS',
								),
								array(
									'key'     => 'woo_ua_auction_end_date',
									'compare' => '<',
									'value'   => $uwa_interval_time,
									'type '   => 'DATETIME',
								),
								
							),                        
						);

				$the_query = new WP_Query( $args );           
				if ( $the_query->have_posts() ) {
					while ( $the_query->have_posts() ) :
						$the_query->the_post();	
						$product_data = wc_get_product( $the_query->post->ID );
						$now_timestamp = current_time( "timestamp");
						WC()->mailer();
						add_post_meta( $the_query->post->ID, 'uwa_auction_sent_ending_soon', $now_timestamp, true );
						do_action( 'woo_ua_auctions_ending_soon_email_bidders', $the_query->post->ID);	
						
					endwhile;
					wp_reset_postdata();
				}
							
					   
				
			} /* end of if - uwa_enabled_bidders */
						
		} /* End Ending soon mail  */
				
		/* 
		Payment Reminder Hook
		*/
		function woo_ua_payment_reminder_email(){
			update_option( 'uwa_payment_reminder_email_cron', 'yes' );
			$remind_to_payment = get_option( 'woocommerce_woo_ua_email_auction_remind_to_pay_settings' );

			if ( $remind_to_payment['enabled'] === 'yes' ) {
					
			$uwa_interval    = ( ! empty( $remind_to_payment['uwa_interval'] ) ) ? (int) $remind_to_payment['uwa_interval'] : 5;
			$uwa_stopsending = ( ! empty( $remind_to_payment['uwa_stopsending'] ) ) ? (int) $remind_to_payment['uwa_stopsending'] : 4;
			$args        = array(
							'post_type'          => 'product',
							'posts_per_page'     => '-1',
							'show_past_auctions' => true,
							'tax_query'          => array(
								array(
									'taxonomy' => 'product_type',
									'field'    => 'slug',
									'terms'    => 'auction',
								),
							),
							'meta_query'         => array(
								'relation' => 'AND',
								array(
									'key'   => 'woo_ua_auction_closed',
									'value' => '2',
								),
								array(
									'key'     => 'woo_ua_auction_payed',
									'compare' => 'NOT EXISTS',
								),
								array(
									'key'     => 'uwa_auction_stop_mails',
									'compare' => 'NOT EXISTS',
								),
							),
							'auction_arhive'     => true,
							'show_past_auctions' => true,
						);


						$the_query = new WP_Query( $args );

						if ( $the_query->have_posts() ) {

							while ( $the_query->have_posts() ) :
								$the_query->the_post();
								$no_of_sent_mail = get_post_meta( $the_query->post->ID, 'uwa_number_of_sent_mails', true );
								$sent_mail_dates  = get_post_meta( $the_query->post->ID, 'uwa_dates_of_sent_mails', false );
								$no_days              = (int) $remind_to_payment['uwa_interval'];

								$product_data = wc_get_product( $the_query->post->ID );

								if ( (int) $no_of_sent_mail >= $uwa_stopsending ) {
									update_post_meta( $the_query->post->ID, 'uwa_auction_stop_mails', '1' );

								} elseif ( ( ! $sent_mail_dates or ( (int) end( $sent_mail_dates ) > strtotime( '-' . $uwa_interval . ' days' ) ) ) or ( strtotime( $product_data->get_uwa_auction_end_dates() ) > strtotime( '-' . $uwa_interval . ' days' ) ) ) {

									update_post_meta( $the_query->post->ID, 'uwa_number_of_sent_mails', (int)$no_of_sent_mail + 1 );
									add_post_meta( $the_query->post->ID, 'uwa_dates_of_sent_mails', time(), false );											

									WC()->mailer();
									do_action( 'uwa_email_remind_to_pay_notification', $the_query->post->ID );
								}

							endwhile;
							wp_reset_postdata();
						}		
				} 
			
		}/* End Payment Reminder cron*/
		
		/* 
		 Auto Relist Hook
		*/
		function woo_ua_auto_relist(){		
			update_option( 'uwa_auto_relist_cron', 'yes' );
			$args = array(
				'post_type'          => 'product',
				'posts_per_page'     => '200',												
				'tax_query'          => array(
					array(
						'taxonomy' => 'product_type',
						'field'    => 'slug',
						'terms'    => 'auction',
					),
				),
				'meta_query'         => array(
					'relation' => 'AND',

					array(
						'key'     => 'woo_ua_auction_closed',
						'compare' => 'EXISTS',
					),
					array(
						'key'     => 'woo_ua_auction_payed',
						'compare' => 'NOT EXISTS',
					),
					array(
						'key'   => 'uwa_auto_renew_enable',
						'value' => 'yes',
					),
				),
				
			);

			$the_query = new WP_Query( $args );

			if ( $the_query->have_posts() ) {

				while ( $the_query->have_posts() ) {

					$the_query->the_post();
					/*$this->uwa_auto_renew_auction( $the_query->post->ID );*/
					$UWA_relist = new UWA_Admin;
					$UWA_relist->uwa_auto_renew_auction( $the_query->post->ID );

				}

				wp_reset_postdata();
			}
			
		}/* End Auto relist cron*/
				