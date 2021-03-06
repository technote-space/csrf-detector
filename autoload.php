<?php
/**
 * Plugin Name: CSRF Detector
 * Plugin URI: https://github.com/technote-space/csrf-detector
 * Description: This plugin will detect csrf
 * Author: Technote
 * Version: 0.2.19
 * Author URI: https://technote.space
 * Text Domain: csrf-detector
 * Domain Path: /languages/
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( defined( 'CSRF_DETECTOR' ) || defined( 'PHPUNIT_COMPOSER_INSTALL' ) ) {
	return;
}

define( 'CSRF_DETECTOR', 'CSRF_Detector' );

@require_once dirname( __FILE__ ) . DIRECTORY_SEPARATOR . 'vendor' . DIRECTORY_SEPARATOR . 'autoload.php';

WP_Framework::get_instance( CSRF_DETECTOR, __FILE__ );
