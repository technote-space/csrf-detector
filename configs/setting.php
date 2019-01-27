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
				'check_only_post'       => [
					'label'   => 'Check only post',
					'type'    => 'bool',
					'default' => true,
				],
				'check_only_admin'      => [
					'label'   => 'Check only admin',
					'type'    => 'bool',
					'default' => true,
				],
			],
		],
	],

];