<?php
/**
 * @author Technote
 * @copyright Technote All Rights Reserved
 * @license http://www.opensource.org/licenses/gpl-2.0.php GNU General Public License, version 2
 * @link https://technote.space
 */

if ( ! defined( 'CSRF_DETECTOR' ) ) {
	exit;
}

return [
	'10' => [
		'CSRF' => [
			'10' => [
				'is_valid_detect'       => [
					'label'   => 'Validity',
					'type'    => 'bool',
					'default' => true,
				],
				'shutdown_if_detected'  => [
					'label'   => 'Shutdown validity',
					'type'    => 'bool',
					'default' => true,
				],
				'csrf_mail_to'          => [
					'label'   => 'Mail to',
					'default' => '',
				],
				'target_commands'       => [
					'label'   => 'Target commands',
					'default' => 'create,alter,truncate,drop,insert,delete,update,replace',
				],
				'ignore_option_pattern' => [
					'label'   => 'Ignore option pattern',
					'default' => '/^(_transient_|_site_transient_)/',
				],
				'exclude_get_method'    => [
					'label'   => 'Whether to exclude get method',
					'type'    => 'bool',
					'default' => false,
				],
				'exclude_front'         => [
					'label'   => 'Whether to exclude front',
					'type'    => 'bool',
					'default' => true,
				],
				'exclude_same_host'     => [
					'label'   => 'Whether to exclude same host',
					'type'    => 'bool',
					'default' => false,
				],
				'exclude_admin_referer' => [
					'label'   => 'Whether to exclude admin referer',
					'type'    => 'bool',
					'default' => true,
				],
				'exclude_get_front'     => [
					'label'   => 'Whether to exclude get front',
					'type'    => 'bool',
					'default' => true,
				],
			],
		],
	],
];
