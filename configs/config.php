<?php
/**
 * @version 0.0.1
 * @author technote-space
 * @since 0.0.1
 * @copyright technote-space All Rights Reserved
 * @license http://www.opensource.org/licenses/gpl-2.0.php GNU General Public License, version 2
 * @link https://technote.space
 */

if ( ! defined( 'CSRF_DETECTOR' ) ) {
	exit;
}

return [

	// update
	'update_info_file_url'           => 'https://raw.githubusercontent.com/technote-space/csrf-detector/develop/update.json',

	// suppress setting help contents
	'suppress_setting_help_contents' => true,

	// log level (for developer)
	'log_level'                      => [
		'error' => [
			'is_valid_log'  => false,
			'is_valid_mail' => false,
		],
		'info'  => [
			'is_valid_log'  => false,
			'is_valid_mail' => false,
		],
		'csrf'  => [
			'is_valid_log'  => true,
			'is_valid_mail' => true,
			'filters'       => [
				'csrf_mail_to',
			],
		],
		// set default level
		''      => 'info',
	],

];
