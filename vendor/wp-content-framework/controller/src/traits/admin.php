<?php
/**
 * WP_Framework_Controller Traits Admin
 *
 * @version 0.0.1
 * @author technote-space
 * @since 0.0.1
 * @copyright technote-space All Rights Reserved
 * @license http://www.opensource.org/licenses/gpl-2.0.php GNU General Public License, version 2
 * @link https://technote.space
 */

namespace WP_Framework_Controller\Traits;

if ( ! defined( 'WP_CONTENT_FRAMEWORK' ) ) {
	exit;
}

/**
 * Trait Admin
 * @package WP_Framework_Controller\Traits\Controller
 * @property \WP_Framework $app
 */
trait Admin {

	/**
	 * @return null|string|false
	 */
	public function get_capability() {
		return $this->app->get_config( 'capability', 'admin_capability', 'manage_options' );
	}
}
