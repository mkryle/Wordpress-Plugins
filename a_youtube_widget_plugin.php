<?php
/**
 * @package cms2 l1
 * @version 3.2
 */
/*
Plugin Name: a youtube widget plugin
Plugin URI: http://kryle.se
Description: a youtube widget plugin
Author: Marcus Kryle
Version: 3.2
Author URI: http://kryle.se
*/


// start plugin
defined ( 'ABSPATH' ) || exit;


// register widget
function youtube_register_widget() {
register_widget( 'youtube_widget' );
}


// throw in widget at widgets
add_action( 'widgets_init', 'youtube_register_widget' );


// construct widget
class youtube_widget extends WP_Widget {
function __construct() {
parent::__construct(

// widget ID
'youtube_widget',

// widget name
__('youtube widget CMS2 labb', ' youtube_widget_domain'),

// widget description
array( 'description' => __( 'youtube Widget cms 2 labb', 'youtube_widget_domain' ), )
);
}


// add widget into widgets
public function widget( $args, $instance ) {
$title = apply_filters( 'youtube widget labb cms2', $instance['title'], $instance['id'], $instance['controls'], $instance['autoplay'] );
echo $args['before_widget'];


// ---inputs
//title
if ( ! empty( $title ) )
echo $args['before_title'] . $title . $args['after_title'];

//tube id
if ( ! empty( $id ) )
echo $args['before_id'] . $id . $args['after_id'];

//controls
if ( ! empty( $controls ) )
echo $args['before_controls'] . $controls . $args['after_controls'];

// autoplay
if ( ! empty( $autoplay ) )
echo $args['before_autoplay'] . $autoplay . $args['after_autoplay'];




//---output (outprint) from widget to page
echo __( '<iframe width="560" height="315" src="https://www.youtube.com/embed/'.$instance['id'].'?'.$instance['autoplay'].'&'.$instance['controls'].'" frameborder="0" allow="accelerometer; autoplay; encrypted-media; gyroscope; picture-in-picture" allowfullscreen></iframe>', 'youtube_widget_domain' );
echo $args['after_widget'];
}


//set title
public function form( $instance ) {
if ( isset( $instance[ 'title' ] ) )
$title = $instance[ 'title' ];
else
$title = __( 'My youtube video', 'youtube_widget_domain' );

// set tube id
if ( isset( $instance[ 'id' ] ) )
$id = $instance[ 'id' ];
else
$id = __( 'w61lIAyqxB0', 'youtube_widget_domain' );

// set controls
if ( isset( $instance[ 'controls' ] ) )
$controls = $instance[ 'controls' ];
else
$controls = __( 'controls=0', 'youtube_widget_domain' );

// set autoplay
if ( isset( $instance[ 'autoplay' ] ) )
$autoplay = $instance[ 'autoplay' ];
else
$autoplay = __( 'autoplay=0', 'youtube_widget_domain' );


?>

<!--widget form title -->
<p>
<label for="<?php echo $this->get_field_id( 'title' ); ?>"><?php _e( 'Title:' ); ?></label>
<input class="widefat" id="<?php echo $this->get_field_id( 'title' ); ?>" name="<?php echo $this->get_field_name( 'title' ); ?>" type="text" value="<?php echo esc_attr( $title ); ?>" />
</p>

<!--widget form tube id -->
<p>
<label for="<?php echo $this->get_field_id( 'id' ); ?>"><?php _e( '<br>youtube id: ex. youtube.com/watch?v=<strong>w61lIAyqxB0</strong>' ); ?></label>
<input class="widefat" id="<?php echo $this->get_field_id( 'id' ); ?>" name="<?php echo $this->get_field_name( 'id' ); ?>" type="text" value="<?php echo esc_attr( $id ); ?>" />
</p>

<!--widget form controls -->
<p>
<label for="<?php echo $this->get_field_id( 'controls' ); ?>"><?php _e( '<br><strong>controls=0</strong>  -no controls<br><strong>controls=1</strong>  -with controls' ); ?></label>
<input class="widefat" id="<?php echo $this->get_field_id( 'controls' ); ?>" name="<?php echo $this->get_field_name( 'controls' ); ?>" type="text" value="<?php echo esc_attr( $controls ); ?>" />
</p>


<!--widget form autoplay -->
<p>
<label for="<?php echo $this->get_field_id( 'autoplay' ); ?>"><?php _e( '<br><strong>autoplay=0</strong>  -no autoplay<br><strong>autoplay=1</strong>  -with autoplay' ); ?></label>
<input class="widefat" id="<?php echo $this->get_field_id( 'autoplay' ); ?>" name="<?php echo $this->get_field_name( 'autoplay' ); ?>" type="text"  value="<?php echo esc_attr( $autoplay ); ?>" />
</p>



<?php
// update/save inputs
}
public function update( $new_instance, $old_instance ) {
$instance = array();
$instance['title'] = ( ! empty( $new_instance['title'] ) ) ? strip_tags( $new_instance['title'] ) : '';
$instance['id'] = ( ! empty( $new_instance['id'] ) ) ? strip_tags( $new_instance['id'] ) : '';
$instance['controls'] = ( ! empty( $new_instance['controls'] ) ) ? strip_tags( $new_instance['controls'] ) : '';
$instance['autoplay'] = ( ! empty( $new_instance['autoplay'] ) ) ? strip_tags( $new_instance['autoplay'] ) : '';


// return updated instance's
return $instance;
}
}

// end of widgetplugin



/*


// old youtube shortcode (widget bettre) :O

function tube($atts, $content = null) {
  
  
  extract(shortcode_atts(array(


                    'id' => '',
                    'controls' => '',
                    'autoplay' => ''
    
    ), $atts));


    return '<iframe width="560" height="315" src="https://www.youtube.com/embed/'.$id.'?controls='.$sontrols.'&autoplay='.$autoplay.'" frameborder="0" allow="accelerometer; autoplay; encrypted-media; gyroscope; picture-in-picture" allowfullscreen></iframe>';
}



add_shortcode('itube', 'tube');


*/
?>