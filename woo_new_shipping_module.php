<?php
 
/**
 * @package cms2 grp
 * @version 6.4
 */
/*
Plugin Name: woo frakt plugin
Plugin URI: http://kryle.se
Description: plugin för frakt CMS2
Author: Marcus Kryle
Version: 6.4
Author URI: http://kryle.se
*/
 
if ( ! defined( 'WPINC' ) ) {
    die;
}
 
// check woo
if ( in_array( 'woocommerce/woocommerce.php', apply_filters( 'active_plugins', get_option( 'active_plugins' ) ) ) ) {
 
    function threadinc_shipping_method() {
        if ( ! class_exists( 'ThreadInc_Shipping_Method' ) ) {
            class ThreadInc_Shipping_Method extends WC_Shipping_Method {
                /**
                 * Constructor for your shipping class
                 *
                 * @access public
                 * @return void
                 */
                public function __construct() {
                    $this->id                 = 'threadinc'; 
                    $this->method_title       = __( 'ThreadInc Shipping', 'threadinc' );  
                    $this->method_description = __( 'Custom Shipping Method for ThreadInc', 'threadinc' ); 
 
                    // only in sweden...
                    $this->availability = 'including';
                    $this->countries = array(
                        'SE'
                        );
 
                    $this->init();
 
                    $this->enabled = isset( $this->settings['enabled'] ) ? $this->settings['enabled'] : 'yes';
                    $this->title = isset( $this->settings['title'] ) ? $this->settings['title'] : __( 'ThreadInc Shipping', 'threadinc' );
                }
 
                /**
                 * Init your settings
                 *
                 * @access public
                 * @return void
                 */
                function init() {
                    // Load the settings API
                    $this->init_form_fields(); 
                    $this->init_settings(); 
 
                    // Save settings in admin if you have any defined
                    add_action( 'woocommerce_update_options_shipping_' . $this->id, array( $this, 'process_admin_options' ) );
                }
 
                /**
                 * Define settings field for this shipping
                 * @return void 
                 */
                function init_form_fields() { 
 
                    $this->form_fields = array(
 
                     'enabled' => array(
                          'title' => __( 'Enable', 'threadinc' ),
                          'type' => 'checkbox',
                          'description' => __( 'Enable this shipping.', 'threadinc' ),
                          'default' => 'yes'
                          ),
 
                     'title' => array(
                        'title' => __( 'Title', 'threadinc' ),
                          'type' => 'text',
                          'description' => __( 'Title to be display on site', 'threadinc' ),
                          'default' => __( 'ThreadInc Shipping', 'threadinc' )
                          ),
 

                    'maxweight' => array(
					'title'       => __( 'weight', 'threadinc' ),
					'type'        => 'text',
					'description' => __( 'maxweight (kg)', 'threadinc' ),
					'default'     => __( '100', 'threadinc' ),	
                            ),
                
					'prisklass1' => array(
					'title'       => __( 'prisklass2', 'threadinc' ),
					'type'        => 'text',
					'description' => __( 'prisklass2', 'threadinc' ),
					'default'     => __( '100', 'threadinc' ),	
                            ),
                            
					'prisklass2' => array(
					'title'       => __( 'prisklass2', 'threadinc' ),
					'type'        => 'text',
					'description' => __( 'prisklass2', 'threadinc' ),
					'default'     => __( '200', 'threadinc' ),	
							),
							
					'prisklass3' => array(
					'title'       => __( 'prisklass3', 'threadinc' ),
					'type'        => 'text',
					'description' => __( 'prisklass3', 'threadinc' ),
					'default'     => __( '300', 'threadinc' ),	
  					      ),

 
                     );
 
                }
				
                /**
                 * This function is used to calculate the shipping cost. Within this function we can check for weights, dimensions and other parameters.
                 *
                 * @access public
                 * @param mixed $package
                 * @return void
                 */
                public function calculate_shipping( $package ) {
					$weight = 0;
					
                    $cost = 0;
                    $country = $package["destination"]["country"];
 
                    foreach ( $package['contents'] as $item_id => $values ) 
                    { 
                        $_product = $values['data']; 
                        $weight = $weight + $_product->get_weight() * $values['quantity']; 
                    }
 
                    $weight = wc_get_weight( $weight, 'kg' );
 
                    if( $weight <= 10 ) {
 
                        $cost = 0;
 
                    } elseif( $weight <= 30 ) {
 
                        $cost = 5;
 
                    } elseif( $weight <= 50 ) {
 
                        $cost = 10;
 
                    } else {
 
                        $cost = 20;
 
                    }
 
                    $countryZones = array(
                        'SE' => 0,
                        'US' => 3,
                        'GB' => 2,
                        'CA' => 3,
                        'ES' => 2,
                        'DE' => 1,
                        'IT' => 1
                        );
 
                    $zonePrices = array(
                        0 => 10,
                        1 => 30,
                        2 => 50,
                        3 => 70
                        );
 
                    $zoneFromCountry = $countryZones[ $country ];
                    $priceFromZone = $zonePrices[ $zoneFromCountry ];
 
                    $cost += $priceFromZone;
 
                    $rate = array(
                        'id' => $this->id,
						'label' => $this->title,
                        'cost' => $cost
                    );
 
                    $this->add_rate( $rate );
                    
                }
            }
        }
    }
 
    add_action( 'woocommerce_shipping_init', 'threadinc_shipping_method' );
 
    function add_threadinc_shipping_method( $methods ) {
        $methods[] = 'ThreadInc_Shipping_Method';
        return $methods;
    }
 
    add_filter( 'woocommerce_shipping_methods', 'add_threadinc_shipping_method' );

    function threadinc_validate_order( $posted )   {
 
        $packages = WC()->shipping->get_packages();
 
        $chosen_methods = WC()->session->get( 'chosen_shipping_methods' );
         
        if( is_array( $chosen_methods ) && in_array( 'threadinc', $chosen_methods ) ) {
             
            foreach ( $packages as $i => $package ) {
 
                if ( $chosen_methods[ $i ] != "threadinc" ) {
                             
                    continue;
                             
                }
                
                $ThreadInc_Shipping_Method = new ThreadInc_Shipping_Method();
                $weightLimit = (int) $ThreadInc_Shipping_Method->settings['weight'];
                $weight = 0;
 
                foreach ( $package['contents'] as $item_id => $values ) 
                { 
                    $_product = $values['data']; 
                    $weight = $weight + $_product->get_weight() * $values['quantity']; 
                }
 
                $weight = wc_get_weight( $weight, 'kg' );
                
                if( $weight > $weightLimit ) {
 
                        $message = sprintf( __( 'Sorry, %d kg exceeds the maximum weight of %d kg for %s', 'threadinc' ), $weight, $weightLimit, $ThreadInc_Shipping_Method->title );
                             
                        $messageType = "error";
 
                        if( ! wc_has_notice( $message, $messageType ) ) {
                         
                            wc_add_notice( $message, $messageType );
                      
                        }
                }
            }       
        } 
    }
 
    add_action( 'woocommerce_review_order_before_cart_contents', 'threadinc_validate_order' , 10 );
    add_action( 'woocommerce_after_checkout_validation', 'threadinc_validate_order' , 10 );
}