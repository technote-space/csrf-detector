<?php
/**
 * @version 0.0.15
 * @author Technote
 * @since 0.0.1
 * @copyright Technote All Rights Reserved
 * @license http://www.opensource.org/licenses/gpl-2.0.php GNU General Public License, version 2
 * @link https://technote.space
 */

if ( ! defined( 'CSRF_DETECTOR' ) ) {
	exit;
}

return [

	// update
	'update_info_file_url'           => 'https://raw.githubusercontent.com/technote-space/csrf-detector/develop/update.json',

	// menu image url
	'menu_image'                     => 'icon-24x24.png',

	// suppress setting help contents
	'suppress_setting_help_contents' => true,

	// setting page title
	'setting_page_title'             => 'Detail Settings',

	// setting page priority
	'setting_page_priority'          => 100,

	// setting page slug
	'setting_page_slug'              => 'dashboard',

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

	// detail url
	'detail_url'                     => 'https://technote.space/csrf-detector',

	// twitter
	'twitter'                        => 'technote15',

	// github repo
	'github_repo'                    => 'technote-space/csrf-detector',

];
