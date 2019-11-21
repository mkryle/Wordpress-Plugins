<?php 
/**
 * @package cms2 l2
 * @version 571
 */
/*
Plugin Name: woo SHIPPING cost
Plugin URI: http://kryle.se
Description: woo SHIPPING cost
Author: Marcus Kryle
Version: 5.7
Author URI: http://kryle.se
*/


// pluging start
defined( 'ABSPATH' ) || exit;

if ( in_array( 'woocommerce/woocommerce.php', apply_filters( 'active_plugins', get_option( 'active_plugins' ) ) ) ) {
	function your_shipping_method_init() {
		if ( ! class_exists( 'WC_Your_Shipping_Method' ) ) {
			class WC_Your_Shipping_Method extends WC_Shipping_Method {
				/**
				 * Constructor for shipping class
				 *
				 * @access public
				 * @return void
				 */
				public function __construct() {
					$this->id                 = 'your_shipping_method'; 
					$this->method_title       = __( 'Your Shipping Method' );  
					$this->method_description = __( 'Description of your shipping method' ); 
					$this->enabled            = "yes"; 
					$this->title              = "shipping by weight"; 
					$this->init();
				}
				/**
				 * Init your settings
				 *
				 * @access public
				 * @return void
				 */
				function init() {
					$this->init_form_fields(); 
					$this->init_settings(); 
					add_action( 'woocommerce_update_options_shipping_' . $this->id, array( $this, 'process_admin_options' ) );
				}
				/**
				 * calculate_shipping function.
				 *
				 * @access public
				 * @param mixed $package
				 * @return void
				 */
				public function calculate_shipping( $package ) {


						// get items
						global $woocommerce;
						$items = $woocommerce->cart->get_cart();
						   

						global $product;
						$attributes = $product->get_attributes();
					
						// add a total weight
						$totalweight = 0;
					
					
							// get weight for each item
       						foreach($items as $item => $values) { 
         					$_product =  wc_get_product( $values['data']->get_id()); 
							 
								if ( $product->has_weight() ) {
								
								$itemweight = $product->get_weight();
								$totalweight = $totalweight + $itemweight;
								}
							}
				

						  // get price for current weight
      					  if ($totalweight < 1) {
						  $total = '30';
						  } elseif ($totalweight > 1 && $totalweight < 5) {
						  $total = '60';
						  } elseif ($totalweight > 5 && $totalweight < 10) {
						  $total = '100';
						  } elseif ($totalweight > 10 && $totalweight < 20) {
						  $total = '200';
						  } elseif ($totalweight > 20 ) {
						  $total = "'" . $totalweight * 10 . "'";
						  }
					   
						  else {
						  $total = 'error';
						  }
										
							
			
						  //send back shippping price
					$rate = array(
						'id' => $this->id,
						'label' => $this->title,
						'cost' => $total,
						'calc_tax' => 'per_item'
					);
					$this->add_rate( $rate );

				}
			}
		}
	}
	add_action( 'woocommerce_shipping_init', 'your_shipping_method_init' );
	function add_your_shipping_method( $methods ) {
		$methods['your_shipping_method'] = 'WC_Your_Shipping_Method';
		return $methods;
	}
	add_filter( 'woocommerce_shipping_methods', 'add_your_shipping_method' );
}




?>