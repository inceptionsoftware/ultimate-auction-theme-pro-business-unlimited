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
 * @class UWA_STRIPE_Front
 * @package Ultimate WooCommerce Auction PRO stripe addon
 * @author Nitesh Singh
 * @since 1.0
 *
 */
class UWA_STRIPE_Front {
	
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
		
		/* add credit card fields in site register form */
		if( get_option( 'uwa_stripe_card_register_page' ) == 'yes' ) {
			//add_action( 'register_form', array($this, 'uwa_stripe_wp_register_form'));
			//add_filter( 'registration_errors', array($this, 'uwa_stripe_validate_wp_register_form'), 10, 3 );
		}
		
		/* add credit card fields in site register form */
		if( get_option( 'uwa_stripe_card_myaccount_page',"yes" ) == 'yes' ) {
			
			add_action( 'woocommerce_register_form_start', array($this, 'uwa_stripe_wc_register_form_payment_error'));
			add_action( 'woocommerce_register_form', array($this, 'uwa_stripe_wc_register_form_fields'),10,0);
			add_action( 'woocommerce_registration_errors', array($this, 'uwa_stripe_wc_register_form_validate'), 10, 3);
			add_action( 'woocommerce_created_customer', array($this, 'uwa_stripe_wc_register_form_fields_save'));
			
		}
		if( get_option( 'uwa_wc_billing_myaccount_page' ) == 'yes' ) {
			/* newly added */
			add_action( 'wp_footer', array($this, 'add_uwa_woo_scripts'), 10, 0);
			/* ==================================== */
			
		   add_action( 'woocommerce_register_form', array($this, 'uwa_stripe_wc_register_billing_form_fields'),10,0);
		   add_action( 'woocommerce_created_customer', array($this, 'uwa_stripe_wc_register_billing_form_fields_save'));
		   add_action( 'woocommerce_registration_errors', array($this, 'uwa_stripe_wc_register_billing_form_validate'), 10, 3);
		}		
	}
	function add_uwa_woo_scripts() {
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
	function uwa_stripe_wc_register_billing_form_fields() {
		?>
			<p class="form-row form-row-wide">
				<strong><?php _e( 'Billing details', 'woo_ua' ); ?></strong>
			</p> 
		<?php 

		global $woocommerce;
    	$checkout = $woocommerce->checkout();
    
	    foreach ( $checkout->get_checkout_fields( 'billing' ) as $key => $field ) {
	        if($key!='billing_email'){ 
	            woocommerce_form_field( $key, $field, $checkout->get_value( $key ) );
	        }
	    } 
	}

	function uwa_stripe_wc_register_billing_form_fields_save( $customer_id ) {
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


	function uwa_stripe_wc_register_billing_form_validate( $errors, $username, $email ){
		global $woocommerce;
    	$address = $_POST;
    	foreach ($address as $key => $field) :
        	// Validation: Required fields
       
            if($key == 'billing_country' && $field == ''){
            
			   $errors->add( 'billing_country_error', __( '<strong>ERROR</strong> Please select a country', 'woo_ua' ) );
            }
            if($key == 'billing_first_name' && $field == ''){
               
				  $errors->add( 'billing_first_name_error', __( '<strong>ERROR</strong> Please enter first name', 'woo_ua' ) );
            }
            if($key == 'billing_last_name' && $field == ''){
             
				 $errors->add( 'billing_last_name_error', __( '<strong>ERROR</strong> Please enter last name', 'woo_ua' ) );
            }
            if($key == 'billing_address_1' && $field == ''){
               
				 $errors->add( 'billing_address_1_error', __( '<strong>ERROR</strong> Please enter address', 'woo_ua' ) );
            }
            if($key == 'billing_city' && $field == ''){
             
				 $errors->add( 'billing_city_error', __( '<strong>ERROR</strong> Please enter city', 'woo_ua' ) );
            }
            
            if($key == 'billing_postcode' && $field == ''){
              
				 $errors->add( 'billing_postcode_error', __( '<strong>ERROR</strong> Please enter a postcode', 'woo_ua' ) );
            }
            
            if($key == 'billing_phone' && $field == ''){
               
				  $errors->add( 'billing_phone_error', __( '<strong>ERROR</strong> Please enter phone number', 'woo_ua' ) );
            }

    	endforeach;
		return $errors;
	}


	public function uwa_stripe_wc_register_form_payment_error() { 
		?>
			<div class="alert alert-info" style="display:none;">
				<span class="payment-errors"></span>
			</div>		
		<?php
	}	
	
	
	function uwa_stripe_wc_register_form_fields() {			
		?>
			<style>
				.StripeElement {
		  			box-sizing: border-box;
		  			height: 40px;
		  			padding: 10px 12px;
					border: 1px solid transparent;
					border-radius: 4px;
					background-color: white;
		  			box-shadow: 0 1px 3px 0 #e6ebf1;
		  			-webkit-transition: box-shadow 150ms ease;
		  			transition: box-shadow 150ms ease;
				}
				.StripeElement--focus {
		  			box-shadow: 0 1px 3px 0 #cfd7df;
				}
				.StripeElement--invalid {
		  			border-color: #fa755a;
				}
				.StripeElement--webkit-autofill {
		  			background-color: #fefde5 !important;
				}
			</style>
			<p class="form-row form-row-wide">			
				<strong><?php _e( 'Enter your Credit Card Details:', 'woo_ua' ); ?></strong>			
			</p>

			<p class="form-row form-row-wide">
				<label for="card-element"><?php _e( 'Card Number', 'woo_ua' ); ?><span class="required">*</span></label>
				<div id="uwa-card-number" class="field empty"></div>	
				
			</p>
			
			<p class="form-row form-row-wide">
				<label for="card-element"><?php _e( 'Expiration', 'woo_ua' ); ?><span class="required">*</span></label>
				<div id="uwa-card-expiry" class="field empty third-width"></div>	
				
			</p>
			
			<p class="form-row form-row-wide">
				<label for="card-element"><?php _e( 'CVV', 'woo_ua' ); ?><span class="required">*</span></label>
				<div id="uwa-card-cvc" class="field empty third-width"></div>	
				
			</p>
			
			<p class="form-row form-row-wide">				
				<span id="uwa-card-errors" style="color:red;">
			   		<!-- a Stripe Element will be inserted here. -->
				</span>
			</p>
			
			<input type="hidden" name="uwa_stripe_k_id" value="" id="uwa_stripe_k_id"/> 
				<p class="form-row validate-required">
					<label class="woocommerce-form__label woocommerce-form__label-for-checkbox checkbox">
					<input type="checkbox" class="woocommerce-form__input woocommerce-form__input-checkbox input-checkbox" name="terms" <?php checked( apply_filters( 'woocommerce_terms_is_checked_default', isset( $_POST['terms'] ) ), true ); // WPCS: input var ok, csrf ok. ?> id="terms" />
						<span class="woocommerce-terms-and-conditions-checkbox-text"><?php wc_terms_and_conditions_checkbox_text(); ?></span>&nbsp;<span class="required">*</span>
					</label>
					<input type="hidden" name="terms-field" value="1" />
				</p>

	    	<div class="clear"></div>
			<div class="clear"></div>

       	<?php	
			$pubkey = "";
			require_once ( UW_AUCTION_PRO_ADDONS .'stripe_auto_debit/lib/uwa-stripe/stripe-main.php' );
	
			wp_enqueue_script('jquery');	
			wp_enqueue_script('uwa-stripe', 'https://js.stripe.com/v3/');
			wp_enqueue_script('stripe-processing', UW_AUCTION_PRO_ADDONS_URL . 'stripe_auto_debit/lib/uwa-stripe/uwa-stripe-processing.js',array(), null );
	   		// wp_enqueue_script('stripe-processing', plugins_url( '/includes/lib/uwa-stripe/stripe-processing.js',array(), null  ));	
			wp_localize_script('stripe-processing', 'stripe_vars', 
				array('publishable_key' => $pubkey));
	     		
	}
	
	function uwa_stripe_wc_register_form_validate( $errors, $username, $email ) {

		if (empty( $_POST["uwa_stripe_k_id"]) ) {
			$errors->add( 'uwa_stripe_card_error', __( '<strong>Your Payment Information not valid. Please check and fill correct payment information.</strong>', 
				'woo_ua' ));		
		}
		return $errors;			
	}
	
	function uwa_stripe_wc_register_form_fields_save( $customer_id ) {	
		
		if ( isset( $_POST['uwa_stripe_k_id'] ) ) { 
				$token = $_POST['uwa_stripe_k_id'];							
				require_once ( UW_AUCTION_PRO_ADDONS .'stripe_auto_debit/lib/uwa-stripe/stripe-main.php' );				
		}
	}
	
	public function uwa_stripe_wp_register_form(){			

			$cardholder = ""; 
			$credit_card = "";
			$card_code = "";
			$expmonth = "-1";
			$expyear = "-1";

			if(isset($_POST['uwa_stripe_cardholder'])){
				$cardholder = sanitize_text_field($_POST['uwa_stripe_cardholder']);
			}
			if(isset($_POST['uwa_stripe_creditcard'])){
				$credit_card = absint($_POST['uwa_stripe_creditcard']);
			}
			if(isset($_POST['uwa_stripe_cardcode'])){
				$card_code = absint($_POST['uwa_stripe_cardcode']);
			}
			if(isset($_POST['uwa_stripe_card_expmonth'])){
				$expmonth = absint($_POST['uwa_stripe_card_expmonth']);
			}
			if(isset($_POST['uwa_stripe_card_expyear'])){
				$expyear = absint($_POST['uwa_stripe_card_expyear']);
			}

			?>

				<style>
					#login{
						width:777px!important;
					}
					#login select.input{
						font-size:17px!important;
					}
					.uwa_inline{
					 	display:inline;
					}
				</style>

				<h3><?php _e( 'Credit Card Details', 'woo_ua' ); ?></h3><br><br>

			    <label for="uwa_stripe_cardholder"><?php _e( 'Card holdername*', 'woo_ua' ); ?>
			   		<input type="text" name="uwa_stripe_cardholder" 
			   			id="uwa_stripe_cardholder" 
			        	class="input form-row-wide"
			        	value="<?php echo $cardholder; ?>"  
			        	required  />
			    </label>
			    <!-- autocomplete="",  -->
		
				<label for="uwa_stripe_creditcard"><?php _e( 'Credit card*', 'woo_ua' ); ?>
			   		<input type="number" name="uwa_stripe_creditcard" 
			   			id="uwa_stripe_creditcard" 
			        	class="input form-row-wide" 
			        	value=""  
			        	required  />
			    </label>
			    <!-- autocomplete="",  -->

				<label for="uwa_stripe_cardcode"><?php _e( 'CVV (3 or 4 digit code)*', 
					'woo_ua' ); ?>
			   		<input type="number" name="uwa_stripe_cardcode" id="uwa_stripe_cardcode" 
			        	class="input form-row-wide"  
			        	value=""
			        	required />
			    </label>
			    <!-- autocomplete="",  -->


		    	<label for="uwa_stripe_card_expmonth"><?php _e( 'Expiry month*', 'woo_ua' ); ?>
		   		<br>
		   			<select name="uwa_stripe_card_expmonth" id="uwa_stripe_card_expmonth" 
		   				class="uwa_inline" data-stripe="exp_month">
							<option value="-1">
								<?php _e( '---Select---', 'woo_ua' ); ?>							
							</option>

									<?php 
										$start=1; $end=12;
										for($i=$start; $i<=$end; $i++){ 
										?>

										<option  value="<?php echo $i; ?>" ><?php _e($i, "woo_ua"); ?>	</option>

									<?php } ?>		
					</select>
				</label>			
				<br><Br>

		   		<label for="uwa_stripe_card_expyear"><?php _e( 'Expiry year*', 'woo_ua' ); ?>
		   		<br>
			  	 	<select name="uwa_stripe_card_expyear" id="uwa_stripe_card_expyear" 
			  	 		data-stripe="exp_year">
							<option value="-1">
								<?php _e( '---Select---', 'woo_ua' ); ?>
							</option>
								<?php 
									$start=2019; $end=2030;
									for($i=$start; $i<=$end; $i++){ ?>
									<option  value="<?php echo $i; ?>" > 
										<?php _e($i, "woo_ua"); ?> </option>
								<?php } ?>
					</select>
	      
	     		</label>
	     		</br></br></br>

		    <?php

	} /* end of function */
	

	function uwa_stripe_validate_wp_register_form( $errors, $sanitized_user_login, 
		$user_email ) {
		
		if(trim($_POST['uwa_stripe_cardholder']) == ""){
			$errors->add( 'uwa_stripe_error', 
	        	__( '<strong>ERROR</strong>: Please enter card holdername.', 
	        	'woo_ua' ) );
		}
		/* validates for numbers too...........recheck - pending */
		elseif ( ! preg_match('/[a-zA-Z]/', $_POST['uwa_stripe_cardholder'] ) ) {
	        $errors->add( 'uwa_stripe_error', 
	        	__( '<strong>ERROR</strong>: Invalid credit card holdername.', 
	        	'woo_ua' ) );
	    }


	    if(trim($_POST['uwa_stripe_creditcard']) == ""){
			$errors->add( 'uwa_stripe_error', 
	        	__( '<strong>ERROR</strong>: Please enter credit card.', 
	        	'woo_ua' ) );
		}
	    elseif ( ! preg_match('/[0-9]/', $_POST['uwa_stripe_creditcard'] ) ) {
	        $errors->add( 'uwa_stripe_error', 
	        	__( '<strong>ERROR</strong>: Invalid credit card.', 
	        	'woo_ua' ) );
	    }


	    if(trim($_POST['uwa_stripe_cardcode']) == ""){
			$errors->add( 'uwa_stripe_error', 
	        	__( '<strong>ERROR</strong>: Please enter CVV code.', 
	        	'woo_ua' ) );
		}
	    elseif ( ! preg_match('/[0-9]/', $_POST['uwa_stripe_cardcode'] ) ) {
	        $errors->add( 'uwa_stripe_error', 
	        	__( '<strong>ERROR</strong>: Invalid CVV code.', 
	        	'woo_ua' ) );
	    }

	    if ( $_POST['uwa_stripe_card_expmonth'] == "-1") {
	        $errors->add( 'uwa_stripe_error', 
	        	__( '<strong>ERROR</strong>: Please select expiry month', 
	        	'woo_ua' ) );
	    }
	   	if ( $_POST['uwa_stripe_card_expyear'] == "-1") {
	        $errors->add( 'uwa_stripe_error', 
	        	__( '<strong>ERROR</strong>: Please select expiry year', 
	        	'woo_ua' ) );
	    }	    

    	return $errors;
	}


} /* end of class */

UWA_STRIPE_Front::get_instance();