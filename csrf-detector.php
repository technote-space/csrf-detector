<?php
/*
Plugin Name: CSRF Detector
Plugin URI: https://github.com/technote-space/csrf-detector
Description: This plugin will detect csrf
Author: technote-space
Version: 0.0.1
Author URI: https://technote.space
Text Domain: csrf-detector
Domain Path: /languages/
*/

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

@require_once dirname( __FILE__ ) . DIRECTORY_SEPARATOR . 'vendor' . DIRECTORY_SEPARATOR . 'autoload.php';

define( 'CSRF_DETECTOR', 'CSRF_Detector' );

WP_Framework::get_instance( CSRF_DETECTOR, __FILE__ );

require_once dirname( __FILE__ ) . DIRECTORY_SEPARATOR . 'pluggable.php';
