<?php
/**
 * WP_Framework_Common Configs Filter
 *
 * @version 0.0.1
 * @author technote-space
 * @since 0.0.1
 * @copyright technote-space All Rights Reserved
 * @license http://www.opensource.org/licenses/gpl-2.0.php GNU General Public License, version 2
 * @link https://technote.space
 */

if ( ! defined( 'WP_CONTENT_FRAMEWORK' ) ) {
	exit;
}

return [

	'uninstall' => [
		'${prefix}app_activated' => [
			'register_uninstall' => [],
		],
	],

	'upgrade' => [
		'${prefix}app_initialized' => [
			'setup_settings' => [],
		],
		'init'                     => [
			'upgrade' => [],
		],
		'admin_init'               => [
			'setup_update' => [],
		],
	],

];