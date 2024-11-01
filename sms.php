<?php
/*
Plugin Name: Vantage SMS Payment Gateway - grinMedia.ro
Plugin URI: http://grinMedia.ro
Description: Extends Vantage Payments using grinMedia.ro platform.
Version: 1.0
Author: Bogdan Bocioaca
Author URI: http://grinMedia.ro
License: GPL2
*/
load_plugin_textdomain( sms, null, dirname( plugin_basename( __FILE__ ) ) . '/lang/' );
require("includes/smsfunctions.php");
require("includes/functions.php");
register_activation_hook(__FILE__,'db_sms_credit');
add_action( 'wp_head', 'sms_add_sms_style' );
add_action( 'init', 'sms_setup' );
function sms_setup(){
    include 'includes/sms-gateway.php';
}

?>