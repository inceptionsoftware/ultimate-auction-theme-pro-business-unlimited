<?php

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class Ultimate_WooCommerce_Auction_PRo_Addons_Twilio_Sms { 

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
		add_action( 'init', array( &$this, 'init' ) );
	}

	/**
	 * Init the plugin after plugins_loaded so environment variables are set.
	 *			 
	 */
	public function init() {
		global $woocommerce;
		
		require_once (UW_AUCTION_PRO_ADDONS .'twilio_sms/class-uwa-twilio-sms-scripts.php');
		require_once (UW_AUCTION_PRO_ADDONS .'twilio_sms/uwa-core-functions.php');
				
		/* phone/country fields on WooCommerce My account Page */
	
		if( get_option( 'uwa_twilio_sms_enabled_myaccount_page', "yes" ) == 'yes' ) {
				$addons = uwa_enabled_addons();
				$display = 1;

				if(in_array('uwa_stripe_auto_debit_addon', $addons)){
					$woo_billing_enabled = get_option('uwa_wc_billing_myaccount_page');
					if($woo_billing_enabled == 'yes'){
						$display = 0;
					}
				}

				if(in_array('uwa_offline_dealing_addon', $addons)){
					$offline_dealing_fields_enabled = get_option(
						'offline_dealing_enabled_my_account');
					if($offline_dealing_fields_enabled == 'yes'){

						/* check phone and country fields are enabled or not */
						$country_enabled =  get_option(
							'offline_dealing_field_country', "no");
						$phone_enabled  = get_option(
							'offline_dealing_field_phone', "no");

						/* here checked for both field */
						if($country_enabled == "yes" && $phone_enabled == "yes"){
							$display = 0;
						}
					}
				}

				if($display == 1){
					add_action( 'woocommerce_register_form', array($this, 
						'uwa_twilio_sms_wc_register_form'), 5, 0);
					add_filter( 'woocommerce_registration_errors', array($this, 
						'uwa_twilio_sms_wc_register_form_validate'), 10, 3);
					add_action( 'woocommerce_created_customer', array($this, 
						'uwa_twilio_sms_wc_register_form_save'), 10, 3);
				}
		}

		/* phone/country fields on Wordpress Register Page */

		if( get_option( 'uwa_twilio_sms_enabled_register_page' ) == 'yes' ) {
			
			add_action( 'register_form', array($this, 'uwa_twilio_sms_wp_register_form'));
			add_filter( 'registration_errors', array($this, 
				'uwa_twilio_sms_wp_register_form_validate'), 10, 3);
			add_action( 'user_register', array($this, 
				'uwa_twilio_sms_wp_register_form_save'));
		}
	}

	public function uwa_twilio_sms_wc_register_form(){
		global $woocommerce;

		/* here check for offline_dealing addon single fields */
		$display_phone = 1;
		$display_country = 1;

		$addons = uwa_enabled_addons();
		if(in_array('uwa_offline_dealing_addon', $addons)){

			$offline_dealing_fields_enabled = get_option(
				'offline_dealing_enabled_my_account');

			if($offline_dealing_fields_enabled == 'yes'){

				/* check phone and country fields are enabled or not */
				$phone_enabled  = get_option(
					'offline_dealing_field_phone', "no");
				$country_enabled =  get_option(
					'offline_dealing_field_country', "no");		

				if($phone_enabled == "yes")
					$display_phone = 0;
				
				if($country_enabled == "yes")
					$display_country = 0;
				
			}
		}

		?>
			<div class="clear"></div>

		<?php
			if($display_phone == 1){
		?>
				<p class="form-row form-row-wide">

				<label for="reg_billing_phone"><?php _e( 'Phone', 'woocommerce' ); ?>
				<span class="required">*</span></label>
		  		<input type="text" class="input-text" name="billing_phone" 
		  			id="reg_billing_phone" value="<?php if ( ! empty( $_POST['billing_phone'] ) ) esc_attr_e( $_POST['billing_phone'] ); ?>" />

		   		</p>
		<?php 				
			} /* end of if - display_phone  */
			if($display_country == 1){

				$countries_obj = new WC_Countries();
				$countries = $countries_obj->__get('countries');
				echo '<div id="uwa_countries_field">';

				woocommerce_form_field('billing_country', array(
					'type'       => 'select',
					'class'      => array( 'select2-selection__rendered' ),
					'label'      => __('Country', 'woocommerce'),
					'placeholder'=> __('Enter something', 'woo_ua'),
					'required'   => true,
					'options'    => $countries
				));

				echo '</div>';	

			} /* end of if - display_country  */
	}
			
	public function uwa_twilio_sms_wc_register_form_validate($errors, $username, $email){

		$display_phone = 1;
		$display_country = 1;

		$addons = uwa_enabled_addons();

		/* check for offline_dealing addon fields */
		if(in_array('uwa_offline_dealing_addon', $addons)){

			$offline_dealing_fields_enabled = get_option(
				'offline_dealing_enabled_my_account');

			if($offline_dealing_fields_enabled == 'yes'){

				/* check phone and country fields are enabled or not */
				$phone_enabled  = get_option(
					'offline_dealing_field_phone', "no");
				$country_enabled =  get_option(
					'offline_dealing_field_country', "no");

				if($phone_enabled == "yes")
					$display_phone = 0;
				
				if($country_enabled == "yes")
					$display_country = 0;				
			}
		}

		if($display_phone == 1){
			if ( isset( $_POST['billing_phone'] ) && empty( $_POST['billing_phone'] ) ) {
				$errors->add( 'billing_phone_error', __( '<strong>Billing Phone</strong>:  is a required field', 'woocommerce' ) );
			}
		}

		if($display_country == 1){
			if ( isset( $_POST['billing_country'] ) && empty( $_POST['billing_country'] ) ) {
				$errors->add( 'billing_country_error', __( '<strong>Billing Phone</strong>: Country!.', 'woocommerce' ) );
			}
		}

		return $errors;
	}
			
	public function uwa_twilio_sms_wc_register_form_save( $customer_id ){
		if ( isset( $_POST['billing_phone'] ) ) {
			update_user_meta( $customer_id, 'billing_phone', 
				sanitize_text_field( $_POST['billing_phone'] ) );
		}
		if ( isset( $_POST['billing_country'] ) ) {
			update_user_meta( $customer_id, 'billing_country', 
				sanitize_text_field( $_POST['billing_country'] ) );
			update_user_meta( $customer_id, 'shipping_country', 
				sanitize_text_field($_POST['billing_country']) );
		} 
	}
		
	public function uwa_twilio_sms_wp_register_form(){	 
		?>	
			<p>			
				<label for="reg_billing_phone"><?php _e( 'Phone', 'woocommerce' ); ?>
					<input type="text" class="input-text" name="billing_phone" 
						id="reg_billing_phone" value="<?php if ( ! empty( 
						$_POST['billing_phone'] ) ) esc_attr_e( $_POST['billing_phone'] ); 
						?>" />
				</label>	
			</p>			
		<?php
			
			$countries_obj = new WC_Countries();
			$countries = $countries_obj->__get('countries');

			woocommerce_form_field('billing_country', array(
				'type'       => 'select',
				'class'      => array( 'select2-selection__rendered' ),
				'label'      => __('Country', 'woocommerce'),
				'placeholder'=> __('Enter something', 'woo_ua'),
				'required'   => true,
				'options'    => $countries
			));
			echo '</br>';
	} 	

	public function uwa_twilio_sms_wp_register_form_validate( $errors, $sanitized_user_login, $user_email ){

		if ( isset( $_POST['billing_phone'] ) && empty( $_POST['billing_phone'] ) ) {
			$errors->add( 'billing_phone_error', __( '<strong>Billing Phone</strong>:  is a required field', 'woocommerce' ) );
		}
		if ( isset( $_POST['billing_country'] ) && empty( $_POST['billing_country'] ) ) {
			$errors->add( 'billing_country_error', __( '<strong>Country</strong>: is a required field', 'woocommerce' ) );
		}	   
    	return $errors;
	}

	public function uwa_twilio_sms_wp_register_form_save( $user_id ) {
		if ( isset( $_POST['billing_phone'] ) ) {
			update_user_meta( $user_id, 'billing_phone', sanitize_text_field( 
				$_POST['billing_phone'] ) );
		}
		if ( isset( $_POST['billing_country'] ) ) {
			update_user_meta( $user_id, 'billing_country', sanitize_text_field( 
				$_POST['billing_country'] ) );
			update_user_meta( $user_id, 'shipping_country', sanitize_text_field(
				$_POST['billing_country'] ) );
		}
	} 
	
	
} /* end of class */

Ultimate_WooCommerce_Auction_PRo_Addons_Twilio_Sms::get_instance();