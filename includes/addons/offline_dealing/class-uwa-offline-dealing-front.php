<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 *
 * Front Side Class
 *
 * Handles generic Front functionality and AJAX requests.
 *
 * @class UWA_OFFLINE_DEALING_Front
 * @package Ultimate WooCommerce Auction PRO offline dealing addon
 * @author Nitesh Singh
 * @since 1.0
 *
 */
class UWA_OFFLINE_DEALING_Front {
	
	private static $instance;
		
	/**
     * Returns the *Singleton* instance of this class.
     *
     * @return Singleton The *Singleton* instance.
     *
     */
    public static function get_instance(){
        if ( null === self::$instance ) {
            self::$instance = new self();
        }
        return self::$instance;
    }
	
	/**
	 * Plugin actions
	 *
	 */
	public function __construct() {
				
		if( get_option( 'offline_dealing_enabled_my_account' ) == 'yes' ) {

			/* check for other addons for contact fields */
			$addons = uwa_enabled_addons();
			$display = 1;

			if(in_array('uwa_stripe_auto_debit_addon', $addons)){
				$woo_billing_enabled = get_option('uwa_wc_billing_myaccount_page');
				if($woo_billing_enabled == 'yes'){
					$display = 0;
				}
			}

			if($display == 1){
			   add_action( 'woocommerce_register_form', array($this, 
			   		'offline_dealing_register_contact_form_fields'), 10, 0);
			   add_action( 'woocommerce_created_customer', array($this, 
			   		'offline_dealing_register_contact_form_fields_save'));
			   add_action( 'woocommerce_registration_errors', array($this, 
			   		'offline_dealing_register_contact_form_validate'), 10, 3);
			}
		}	

		$addons = uwa_enabled_addons();
		if(is_array($addons) && in_array('uwa_offline_dealing_addon', $addons)){
			if(in_array('uwa_buyers_premium_addon', $addons) && 
					!in_array('uwa_stripe_auto_debit_addon', $addons)){

				/* -- when offline_dealing_addon and buyer's premium is active -- */
				/* hook to change product price from cart */

				add_filter('woocommerce_product_get_price', array($this, 
					"product_custom_price"), 10, 2);
				/* add_filter( 'woocommerce_product_variation_get_price', array($this, "product_custom_price"), 10, 2 );

				add_filter( 'woocommerce_product_get_sale_price', array($this, 
					"product_custom_price"), 10, 2 ); */
			}

		} /* end of if - addons */
	}

	function offline_dealing_register_contact_form_fields() {
		global $woocommerce;
    	$checkout = $woocommerce->checkout();

    		$firstlast_name_enabled = get_option(
				'offline_dealing_field_firstlast_name', "no");

			$address_enabled = get_option(
				'offline_dealing_field_address', "no");

			$country_enabled = get_option(
				'offline_dealing_field_country', "no");

			$phone_enabled = get_option(
				'offline_dealing_field_phone', "no");
			$state_enabled = get_option(
				'offline_dealing_field_state', "no");
			/* add heading */
			if($firstlast_name_enabled == "yes" ||
				$address_enabled == "yes" ||
				$country_enabled == "yes"||
				$phone_enabled == "yes" ) {
				?>
					<p class="form-row form-row-wide">
						<strong><?php _e( 'Contact details', 'woo_ua' ); ?></strong>
					</p> 
				<?php
			}
		
			/* add fields */
		    foreach ( $checkout->get_checkout_fields( 'billing' ) as $key => $field ) {
		  	    
		  	    /*  total fields : 
		  	    	billing_first_name, billing_last_name, billing_company, 
		  	    	billing_country, billing_address_1, billing_address_2, billing_city, 
		  	    	billing_state, 	billing_postcode, billing_phone, billing_email
		  	    */	
	   					
		    	if($key == "billing_first_name" || $key == "billing_last_name"){
		    		if($firstlast_name_enabled == "yes"){
		    			woocommerce_form_field( $key, $field, $checkout->get_value( $key ) );
		    		}
		    	}
		
		    	/* state is not added */
		    	if($key == "billing_address_1" || $key == "billing_address_2" ||
		    		$key == "billing_city" || $key == "billing_postcode"){
		    		if($address_enabled == "yes"){
		    			woocommerce_form_field( $key, $field, $checkout->get_value( $key ) );
		    		}
		    	}
	 		    	
		    	if($key == "billing_country"){
		    		if($country_enabled == "yes"){						
		    			woocommerce_form_field( $key, $field, $checkout->get_value( $key ) );
						
		    		}
		    	}
		    	
				if($key == "billing_state"){
		    		if($state_enabled == "yes"){
						add_action( 'wp_footer', array($this, 'add_uwa_woo_scripts_offline'), 10, 0);
		    			woocommerce_form_field( $key, $field, $checkout->get_value( $key ) );
						
		    		}
		    	}
				
		    	if($key == "billing_phone"){
		    		if($phone_enabled == "yes"){
		    			woocommerce_form_field( $key, $field, $checkout->get_value( $key ) );
		    		}
		    	}	
		    	

		    } /* end of foreach */

		    /* fix for designing  */
		    if($firstlast_name_enabled == "yes"){
		    	?>
		    		<p class="form-row form-row-wide"></p>
		    	<?php
		    }	   
	    
	} /* end of function */
	function add_uwa_woo_scripts_offline() {
		 wp_enqueue_script('wc-country-select', get_site_url().'/wp-content/plugins/woocommerce/assets/js/frontend/country-select.min.js', array('jquery'), true);
			wp_localize_script(
				'wc-enhanced-select',
				'wc_enhanced_select_params',
				array(
					'i18n_no_matches'           => _x( 'No matches found', 'enhanced select', 'woocommerce' ),
					'i18n_ajax_error'           => _x( 'Loading failed', 'enhanced select', 'woocommerce' ),
					'i18n_input_too_short_1'    => _x( 'Please enter 1 or more characters', 'enhanced select', 'woocommerce' ),
					'i18n_input_too_short_n'    => _x( 'Please enter %qty% or more characters', 'enhanced select', 'woocommerce' ),
					'i18n_input_too_long_1'     => _x( 'Please delete 1 character', 'enhanced select', 'woocommerce' ),
					'i18n_input_too_long_n'     => _x( 'Please delete %qty% characters', 'enhanced select', 'woocommerce' ),
					'i18n_selection_too_long_1' => _x( 'You can only select 1 item', 'enhanced select', 'woocommerce' ),
					'i18n_selection_too_long_n' => _x( 'You can only select %qty% items', 'enhanced select', 'woocommerce' ),
					'i18n_load_more'            => _x( 'Loading more results&hellip;', 'enhanced select', 'woocommerce' ),
					'i18n_searching'            => _x( 'Searching&hellip;', 'enhanced select', 'woocommerce' ),
					'ajax_url'                  => admin_url( 'admin-ajax.php' ),
					'search_products_nonce'     => wp_create_nonce( 'search-products' ),
					'search_customers_nonce'    => wp_create_nonce( 'search-customers' ),
					'search_categories_nonce'   => wp_create_nonce( 'search-categories' ),
				)
			);
		?>
 
		<?php
		
    } /* end of function */

	function offline_dealing_register_contact_form_validate( $errors, $username, 
		$email ){
		
		global $woocommerce;
    	$address = $_POST;

    		$firstlast_name_enabled = get_option(
				'offline_dealing_field_firstlast_name', "no");

			$address_enabled = get_option(
				'offline_dealing_field_address', "no");

			$country_enabled = get_option(
				'offline_dealing_field_country', "no");

			$phone_enabled = get_option(
				'offline_dealing_field_phone', "no");


    	foreach ($address as $key => $field) {
        	// Validation: Required fields

            if($firstlast_name_enabled  == "yes"){
            	if($key == 'billing_first_name' && $field == ''){  

				 	$errors->add( 'billing_first_name_error', __( '<strong>ERROR</strong> Please enter first name', 'woo_ua' ) );
            	}
            	if($key == 'billing_last_name' && $field == ''){ 

					$errors->add( 'billing_last_name_error', __( '<strong>ERROR</strong> Please enter last name', 'woo_ua' ) );
            	}
            }

            if($address_enabled == "yes"){

		            if($key == 'billing_address_1' && $field == ''){
		               
						 $errors->add( 'billing_address_1_error', __( '<strong>ERROR</strong> Please enter address', 'woo_ua' ) );
		            }
		            if($key == 'billing_city' && $field == ''){
		             
						 $errors->add( 'billing_city_error', __( '<strong>ERROR</strong> Please enter city', 'woo_ua' ) );
		            }		            
		            if($key == 'billing_postcode' && $field == ''){
		              
						 $errors->add( 'billing_postcode_error', __( '<strong>ERROR</strong> Please enter a postcode', 'woo_ua' ) );
		            }
            }
            
            if($country_enabled == "yes"){
            	if($key == 'billing_country' && $field == ''){            
			   		$errors->add( 'billing_country_error', __( '<strong>ERROR</strong> Please select a country', 'woo_ua' ) );
            	}
            }
            
            if($phone_enabled == "yes"){
            	if($key == 'billing_phone' && $field == ''){               
				  $errors->add( 'billing_phone_error', __( '<strong>ERROR</strong> Please enter phone number', 'woo_ua' ) );
            	}
            }

    	}

		return $errors;

	} /* end of function */


	function offline_dealing_register_contact_form_fields_save( $customer_id ) {

		global $woocommerce;
		$address = $_POST;
		foreach ($address as $key => $field){
		
			// Condition to add firstname and last name to user meta table
			if($key == 'billing_first_name' || $key == 'billing_last_name'){
				$new_key = explode('billing_', $key);
				update_user_meta( $customer_id, $new_key[1], $_POST[$key] );
			}
			update_user_meta( $customer_id, $key, $_POST[$key] );
		}
	}


	function product_custom_price($price, $product) {
			if (is_page('cart') ||  is_cart() ||  is_checkout() ||  
						is_page('checkout')) {

				/* --------  when offline_dealing_addon is active ------- */
				$addons = uwa_enabled_addons();

				if(is_array($addons) && in_array('uwa_offline_dealing_addon', $addons)){
					if(in_array('uwa_buyers_premium_addon', $addons) && 
							!in_array('uwa_stripe_auto_debit_addon', $addons)){

						 /* set price = 0 when offline dealing and buyer's premium addons 
							are active */

							/* check product is auction or not */
							$type = $product->get_type();				
							if($type == "auction"){
								$custom_price = 0;
								return $custom_price;
							}
					}
				} /* end of if - is_array addons */

			} /* end of if - cart */

			return $price;
	}

	
} /* end of class */

UWA_OFFLINE_DEALING_Front::get_instance();