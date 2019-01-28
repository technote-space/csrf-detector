<?php
/**
 * @version 0.0.4
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

	'\Csrf_Detector\Classes\Models\Detector' => [
		'admin_init'                  => [
			'check_admin_validity' => [ 1 ],
		],
		'init'                        => [
			'check_not_admin_validity' => [ 9 ],
		],
		'query'                       => [
			'check_query' => [],
		],
		'update_option'               => [
			'update_option' => [],
		],
		'add_option'                  => [
			'add_option' => [],
		],
		'delete_option'               => [
			'delete_option' => [],
		],
		'${prefix}start_db_update'    => [
			'on_upgrade' => [],
		],
		'${prefix}finished_db_update' => [
			'off_upgrade' => [],
		],
		'${prefix}start_upgrade'      => [
			'on_upgrade' => [],
		],
		'${prefix}finished_upgrade'   => [
			'off_upgrade' => [],
		],
	],
];