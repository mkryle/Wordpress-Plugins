<?php 
/**
 * @package cms2 grp
 * @version 8.2
 */
/*
Plugin Name: woo faktura plugin
Plugin URI: http://kryle.se
Description: plugin för faktura CMS2
Author: Marcus Kryle
Version: 8.2
Author URI: http://kryle.se
*/
 

//start plugin
defined( 'ABSPATH' ) or exit;

// check woocommerce 
if ( ! in_array( 'woocommerce/woocommerce.php', apply_filters( 'active_plugins', get_option( 'active_plugins' ) ) ) ) {
	return;
}

// add personnummer field
add_action( 'woocommerce_after_checkout_billing_form', 'persid_field' );
add_action( 'woocommerce_checkout_update_order_meta', 'persid_save' );
add_action('woocommerce_checkout_process', 'check_id');

// funct add field
function persid_field( $checkout ){

	woocommerce_form_field( 'personnummer', array(
		'type'          => 'text', 
		'required'	=> true, 
		'class'         => array('persid-field', 'form-row-wide'), 
		'label'         => 'Personnummer (10 siffor)',
		'label_class'   => 'persid-label', 
	
		), $checkout->get_value( 'personnummer' ) );
 
}
 

// validera personnummer
function check_id() {
 
function luhn_validate($number, $mod5 = false) {
	$parity = strlen($number) % 2;
	$total = 0;
	// dela siffrir i array
  	$digits = str_split($number);
  	foreach($digits as $key => $digit) {
		// alla siffror * 2
	  	if (($key % 2) == $parity) 
		  	$digit = ($digit * 2);
		// 11 = 1+1
	  	if ($digit >= 10) {
			// dela upp
		  	$digit_parts = str_split($digit);
			// sätt ihop
		  	$digit = $digit_parts[0]+$digit_parts[1];
	  	}
		// place total
		$total += $digit;
  	}
	return ($total % ($mod5 ? 5 : 10) == 0 ? true : false); // returnera tru eller false
}


// validera personnummer
 $checkID = luhn_validate($_POST['personnummer']);


 // om validering gick fel eller personnumme inte är 10 siffror = error
 if ( strlen($_POST['personnummer'])  != 10 || $checkID == false)

		wc_add_notice( 'Var god ange korrekt personnumer.', 'error' );
 
}
 
// spara peronnummer till order
function persid_save( $order_id ){
 
	if( !empty( $_POST['personnummer'] ) )
		update_post_meta( $order_id, 'personnummer', sanitize_text_field( $_POST['personnummer'] ) );
 
 
}




//invoice payment (faktura)

function wc_offline_add_to_gateways( $gateways ) {
	$gateways[] = 'WC_Gateway_Offline';
	return $gateways;
}


add_filter( 'woocommerce_payment_gateways', 'wc_offline_add_to_gateways' );

function wc_offline_gateway_plugin_links( $links ) {
	$plugin_links = array(
		'<a href="' . admin_url( 'admin.php?page=wc-settings&tab=checkout&section=offline_gateway' ) . '">' . __( 'Configure', 'wc-gateway-offline' ) . '</a>'
	);
	return array_merge( $plugin_links, $links );
}

add_filter( 'plugin_action_links_' . plugin_basename( __FILE__ ), 'wc_offline_gateway_plugin_links' );
add_action( 'plugins_loaded', 'wc_offline_gateway_init', 11 );


function wc_offline_gateway_init() {
	class WC_Gateway_Offline extends WC_Payment_Gateway {

    // construkt gateway
		public function __construct() {
	  
			$this->id                 = 'offline_gateway';
			$this->icon               = apply_filters('woocommerce_offline_icon', '');
			$this->has_fields         = false;
			$this->method_title       = __( 'Invoice (faktura)', 'wc-gateway-offline' );
			$this->method_description = __( 'Pay with 30 days invoice', 'wc-gateway-offline' );
		  
			// get inställningar
			$this->init_form_fields();
			$this->init_settings();
		  
			// title, des, inst
			$this->title        = $this->get_option( 'title' );
			$this->description  = $this->get_option( 'description' );
			$this->instructions = $this->get_option( 'instructions', $this->description );
		  
			// add actions
			add_action( 'woocommerce_update_options_payment_gateways_' . $this->id, array( $this, 'process_admin_options' ) );
			add_action( 'woocommerce_thankyou_' . $this->id, array( $this, 'thankyou_page' ) );
		  
			// kundemail
			add_action( 'woocommerce_email_before_order_table', array( $this, 'email_instructions' ), 10, 3 );
		}
	

    // add settings
		public function init_form_fields() {
	  
			$this->form_fields = apply_filters( 'wc_offline_form_fields', array(
		  
				'enabled' => array(
					'title'   => __( 'Enable/Disable', 'wc-gateway-offline' ),
					'type'    => 'checkbox',
					'label'   => __( 'Enable Invoice Payment', 'wc-gateway-offline' ),
					'default' => 'yes'
				),
				
				'title' => array(
					'title'       => __( 'Title', 'wc-gateway-offline' ),
					'type'        => 'text',
					'description' => __( 'This controls the title for the payment method the customer sees during checkout.', 'wc-gateway-offline' ),
					'default'     => __( 'Pay with 30 days invoice', 'wc-gateway-offline' ),
					'desc_tip'    => true,
				),
				
				'description' => array(
					'title'       => __( 'Description', 'wc-gateway-offline' ),
					'type'        => 'textarea',
					'description' => __( 'Payment method description that the customer will see on your checkout.', 'wc-gateway-offline' ),
					'default'     => __( 'Choose to pay with 30 days invoice bill.', 'wc-gateway-offline' ),
					'desc_tip'    => true,
        ),

				'instructions' => array(
					'title'       => __( 'Instructions', 'wc-gateway-offline' ),
					'type'        => 'textarea',
					'description' => __( 'Instructions that will be added to the thank you page and emails.', 'wc-gateway-offline' ),
					'default'     => 'We send you your producs with an invoice bill that has to be payed within 30 days, Thread Inc',
					'desc_tip'    => true,
				),
			) );
		}
	
	
// order recived
		public function thankyou_page() {
			if ( $this->instructions ) {
				echo wpautop( wptexturize( $this->instructions ) );
			}
		}
	
	
// add instruction tto emails
		public function email_instructions( $order, $sent_to_admin, $plain_text = false ) {
		
			if ( $this->instructions && ! $sent_to_admin && $this->id === $order->payment_method && $order->has_status( 'on-hold' ) ) {
				echo wpautop( wptexturize( $this->instructions ) ) . PHP_EOL;
			}
		}
	
	

    // process payment
		public function process_payment( $order_id ) {
	
			$order = wc_get_order( $order_id );
			
			// Mark as on-hold (we're awaiting the payment)
			$order->update_status( 'on-hold', __( 'Awaiting offline payment', 'wc-gateway-offline' ) );
			
			// Reduce stock levels
			$order->reduce_order_stock();
			
			// Remove cart
			WC()->cart->empty_cart();
			
			// Return thankyou redirect
			return array(
				'result' 	=> 'success',
				'redirect'	=> $this->get_return_url( $order )
			);
		}
	
  } // end \WC_Gateway_Offline class
}