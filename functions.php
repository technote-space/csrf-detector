<?php
/**
 * @version 0.0.7
 * @author technote-space
 * @since 0.0.1
 * @copyright technote-space All Rights Reserved
 * @license http://www.opensource.org/licenses/gpl-2.0.php GNU General Public License, version 2
 * @link https://technote.space
 */

if ( ! defined( 'CSRF_DETECTOR' ) ) {
	exit;
}

add_action( 'csrf_detector/app_initialize', function ( $app ) {
	/** @var \WP_Framework $app */
	$app->setting->edit_setting( 'is_valid_log', 'default', true );
	$app->setting->remove_setting( 'capture_shutdown_error' );
	$app->setting->remove_setting( 'minify_js' );
	$app->setting->remove_setting( 'minify_css' );
	$app->setting->remove_setting( 'assets_version' );
} );

add_action( 'csrf_detector/logs_page_priority', function () {
	return 50;
} );
