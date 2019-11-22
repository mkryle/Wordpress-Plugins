<?php 
/**
 * @package cms2 l2
 * @version 3.6
 */
/*
Plugin Name: woo list bestsellers
Plugin URI: http://kryle.se
Description: woo list bestsellers
Author: Marcus Kryle
Version: 3.6
Author URI: http://kryle.se
*/


// pluging start
defined( 'ABSPATH' ) || exit;




function top_sell($atts, $content = null) {

    $args = array(
        'post_type' => 'product',
        'meta_key' => 'total_sales',
        'orderby' => 'meta_value_num',
        'posts_per_page' => 10,
    );


    $loop = new WP_Query( $args );


    while ( $loop->have_posts() ) : $loop->the_post(); 
        global $product; 

        $p0 = '<h1>';
        $p1 = the_title();
        $p2 = '</h1><h2>';
        $p3 =  get_post_meta( get_the_ID(), '_regular_price', true);
        $p4 = '</h2><p>';
        $p5 = the_content(); 
        $p6 = '</p>';


    endwhile; 

    $output = $p1 . $p2 . $p3 . $p4 . $p5 . $p6;

    wp_reset_query(); 


    return $output;

}

add_shortcode('topsell', 'top_sell');




?>