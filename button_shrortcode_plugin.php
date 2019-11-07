<?php
/**
 * @package cms2 l1
 * @version 3.2
 */
/*
Plugin Name: button shrortcode plugin
Plugin URI: http://kryle.se
Description: button shrortcode plugin
Author: Marcus Kryle
Version: 3.2
Author URI: http://kryle.se
*/


// plugin start
defined ( 'ABSPATH' ) || exit;


// add style
wp_register_style( 'my_button', plugin_dir_url(__FILE__) . 'button_shrortcode_plugin.css' );
wp_enqueue_style('button_style');


// make button
function my_button($atts, $content = null) {
  
  
  extract(shortcode_atts(array(

    // extract for add inputs

                    'url' => '#',
                    'text' => 'Knapp',
                    'background' => 'grey',
                    'width' => '',
                    'style' => ''
    
    ), $atts));

    //resturn all em stuff together
    return '<a class="button" href="'.$url.'" style="'.$style.'width:'.$width.'px;background-color:'.$background.';" width="'.$width.'">' . do_shortcode($content) . '<span>'.$text.'</span></a>';
}


// smash in the button
add_shortcode('button', 'my_button');



?>