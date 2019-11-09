<?php
/**
 * @package cms2 l1
 * @version 3.2
 */
/*
Plugin Name: form shrortcode plugin
Plugin URI: http://kryle.se
Description: form shrortcode plugin
Author: Marcus Kryle
Version: 3.2
Author URI: http://kryle.se
*/


// plugin start
defined ( 'ABSPATH' ) || exit;


// add style
wp_register_style( 'contactform', plugin_dir_url(__FILE__) . 'form_shrortcode_plugin.css' );
wp_enqueue_style('contactform_style');

// add wp mail w ajax
add_action('wp_ajax_fire_mail', 'fire_mail');
add_action('wp_ajax_nopriv_fire_mail', 'fire_mail');


// contactform
function contactform($content) {
  echo '<p><strong>Description: </strong><br>'.$content.'<br><br><form class="contactform" action="'. admin_url('admin-ajax.php') .'" method="post">
        <labbel for="subject">Ämne:</label><br>
        <input type="text" name="subject"><br>
        <labbel for="message">Meddelande:</label>
        <textarea rows="10" cols="35" name="message"></textarea>
        <input type="hidden" name="action" value="fire_mail">
        <p><input type="submit" name="sendit" value="Skicka in!"></p>
        </form>';
}


// see if mail exist
var_dump(get_option( 'admin_email' ));
 var_dump( $_SERVER['HTTP_REFERER'] );

// sendmail funktion
function fire_mail() {
  // send when clikc button1
  echo "Admin isset <br>";
	if ( isset( $_POST['sendit'] ) ) {

echo "Admin ajax";

    // get ämne + meddelande
		$subject = strip_tags( $_POST["subject"] );
		$message = strip_tags( $_POST["message"] );

    // create d mail
		$to = get_option( 'admin_email' );
		$headers = "From: contactform <cms2labb>" . "\r\n";

		// mail-svar
		if ( wp_mail( $to, $subject, $message, $headers ) ) {
			echo '<p>Tack för ditt mail</p>';
		} else {
			echo 'error!?';
    }

    die();

  }
     
}


// no redirect, w8 action
function contactform_shortcode($atts, $content = null) {
	ob_start();
	fire_mail();
	contactform($content);

	return ob_get_clean();
}


// smash in shortcode
add_shortcode( 'contact', 'contactform_shortcode' );


?>