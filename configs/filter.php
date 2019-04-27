<?php
/**
 * @version 0.0.17
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

	'\Csrf_Detector\Classes\Models\Detector' => [
		'init'                           => [
			'check_validity' => 9,
		],
		'query'                          => [
			'check_query',
		],
		'update_option'                  => [
			'update_option',
		],
		'add_option'                     => [
			'add_option',
		],
		'delete_option'                  => [
			'delete_option',
		],
		'${framework}verified_nonce'     => [
			'verified_nonce',
		],
		'${framework}start_db_update'    => [
			'start_db_update',
		],
		'${framework}finished_db_update' => [
			'finished_db_update',
		],
		'${prefix}app_initialize'        => [
			'setup_settings',
		],
		'${prefix}logs_page_priority'    => [
			'logs_page_priority',
		],
	],
];