<?php
/**
 * @version 0.0.17
 * @author Technote
 * @since 0.0.7
 * @copyright Technote All Rights Reserved
 * @license http://www.opensource.org/licenses/gpl-2.0.php GNU General Public License, version 2
 * @link https://technote.space/
 */

namespace Csrf_Detector\Classes\Controllers\Admin;

use WP_Framework_Admin\Classes\Controllers\Admin\Base;

if ( ! defined( 'CSRF_DETECTOR' ) ) {
	exit;
}

/**
 * Class Dashboard
 * @package Csrf_Detector\Classes\Controllers\Admin
 */
class Dashboard extends Base {

	use \WP_Framework_Admin\Traits\Dashboard;

	/**
	 * @return array
	 */
	protected function get_setting_list() {
		return [
			'shutdown_if_detected',
			'csrf_mail_to',
			'target_commands' => [
				'form'    => 'multi_select',
				'options' => $this->app->array->combine( $this->app->string->explode( $this->app->setting->get_setting( 'target_commands', true )['default'] ), null ),
			],
			'exclude_get_method',
			'exclude_front',
			'exclude_same_host',
			'exclude_admin_referer',
		];
	}
}
