<?php
/**
 * @author Technote
 * @copyright Technote All Rights Reserved
 * @license http://www.opensource.org/licenses/gpl-2.0.php GNU General Public License, version 2
 * @link https://technote.space/
 */

namespace Csrf_Detector\Classes\Models;

use WP_Framework;
use WP_Framework_Common\Traits\Package;
use WP_Framework_Core\Traits\Hook;
use WP_Framework_Core\Traits\Singleton;
use WP_Framework_Presenter\Traits\Presenter;

if ( ! defined( 'CSRF_DETECTOR' ) ) {
	exit;
}

/**
 * Class Detector
 * @package Marker_Animation\Classes\Models
 */
class Detector implements \WP_Framework_Core\Interfaces\Singleton, \WP_Framework_Core\Interfaces\Hook, \WP_Framework_Presenter\Interfaces\Presenter {

	use Singleton, Hook, Presenter, Package;

	/**
	 * @var string|false $check_pattern
	 */
	private $check_pattern;

	/**
	 * @var string|false $ignore_option_pattern
	 */
	private $ignore_option_pattern;

	/**
	 * @var bool $is_valid_detector
	 */
	private $is_valid_detector = false;

	/**
	 * @var bool $has_verified_nonce
	 */
	private $has_verified_nonce = false;

	/**
	 * @var bool $ignore_check
	 */
	private $ignore_check = false;

	/**
	 * @var bool $db_update
	 */
	private $db_update = false;

	/**
	 * check validity
	 * @noinspection PhpUnusedPrivateMethodInspection
	 * @SuppressWarnings(PHPMD.UnusedPrivateMethod)
	 */
	private function check_validity() {
		if ( is_admin() ) {
			$this->check_admin_validity();
		} else {
			$this->check_not_admin_validity();
		}
	}

	/**
	 * verified nonce
	 * @noinspection PhpUnusedPrivateMethodInspection
	 * @SuppressWarnings(PHPMD.UnusedPrivateMethod)
	 */
	private function verified_nonce() {
		$this->detected_verify_nonce();
	}

	/**
	 * start db update
	 * @noinspection PhpUnusedPrivateMethodInspection
	 * @SuppressWarnings(PHPMD.UnusedPrivateMethod)
	 */
	private function start_db_update() {
		$this->db_update = true;
	}

	/**
	 * finished db update
	 * @noinspection PhpUnusedPrivateMethodInspection
	 * @SuppressWarnings(PHPMD.UnusedPrivateMethod)
	 */
	private function finished_db_update() {
		$this->db_update = false;
	}

	/**
	 * @noinspection PhpUnusedPrivateMethodInspection
	 * @SuppressWarnings(PHPMD.UnusedPrivateMethod)
	 *
	 * @param string $query
	 *
	 * @return string
	 */
	private function check_query( $query ) {
		if ( preg_match( '/^SHOW FULL COLUMNS FROM\s/', $query ) ) {
			return $query;
		}

		$ignore             = $this->ignore_check;
		$this->ignore_check = false;
		if ( ! $this->is_valid_detector || $ignore || $this->db_update ) {
			return $query;
		}

		if ( preg_match( $this->get_check_pattern(), $query ) ) {
			$this->detect_db_update( $query );
		}

		return $query;
	}

	/**
	 * @noinspection PhpUnusedPrivateMethodInspection
	 * @SuppressWarnings(PHPMD.UnusedPrivateMethod)
	 *
	 * @param string $option
	 */
	private function update_option( $option ) {
		$this->ignore_check = $this->check_ignore_option( $option );
	}

	/**
	 * @noinspection PhpUnusedPrivateMethodInspection
	 * @SuppressWarnings(PHPMD.UnusedPrivateMethod)
	 *
	 * @param string $option
	 */
	private function add_option( $option ) {
		$this->ignore_check = $this->check_ignore_option( $option );
	}

	/**
	 * @noinspection PhpUnusedPrivateMethodInspection
	 * @SuppressWarnings(PHPMD.UnusedPrivateMethod)
	 *
	 * @param string $option
	 */
	private function delete_option( $option ) {
		$this->ignore_check = $this->check_ignore_option( $option );
	}

	/**
	 * setup settings
	 * @noinspection PhpUnusedPrivateMethodInspection
	 * @SuppressWarnings(PHPMD.UnusedPrivateMethod)
	 */
	private function setup_settings() {
		$this->app->setting->edit_setting( 'is_valid_log', 'default', true );
		$this->app->setting->remove_setting( 'capture_shutdown_error' );
		$this->app->setting->remove_setting( 'minify_js' );
		$this->app->setting->remove_setting( 'minify_css' );
		$this->app->setting->remove_setting( 'assets_version' );
	}

	/**
	 * @noinspection PhpUnusedPrivateMethodInspection
	 * @SuppressWarnings(PHPMD.UnusedPrivateMethod)
	 *
	 * @return int
	 */
	private function logs_page_priority() {
		return 50;
	}

	/**
	 * check admin validity
	 */
	private function check_admin_validity() {
		if ( empty( $this->app->utility->defined( 'CSRF_DETECTOR_FUNCTION_DEFINED' ) ) ) {
			$this->app->add_message( '<h3>CSRF Detector</h3>', 'error', true, false );
			$this->app->add_message( '[wp_verify_nonce] function has already been defined by other plugin or theme', 'error', true );
			$this->app->add_message( 'so [CSRF Detector] is not available', 'error', true );

			return;
		}

		if ( ! $this->apply_filters( 'admin_validity', $this->check( true ) ) ) {
			return;
		}

		$this->is_valid_detector = true;
		if ( empty( $this->get_check_pattern() ) ) {
			$this->is_valid_detector = false;
			$this->app->add_message( '<h3>CSRF Detector</h3>', 'error', true, false );
			$this->app->add_message( sprintf( $this->translate( '[%s] is invalid: [%s]' ), $this->translate( 'Target commands' ), $this->apply_filters( 'target_commands' ) ), 'error', true );
			$this->app->add_message( 'so [CSRF Detector] is not available', 'error', true );
		}
	}

	/**
	 * check not admin validity
	 */
	private function check_not_admin_validity() {
		if ( ! $this->apply_filters( 'not_admin_validity', $this->check( false ) ) ) {
			return;
		}

		$this->is_valid_detector = ! empty( $this->app->utility->defined( 'CSRF_DETECTOR_FUNCTION_DEFINED' ) );
		if ( $this->is_valid_detector && empty( $this->get_check_pattern() ) ) {
			$this->is_valid_detector = false;
		}
	}

	/**
	 * @param bool $is_admin
	 *
	 * @return bool
	 */
	private function check( $is_admin ) {
		if ( ! $this->apply_filters( 'is_valid_detect' ) ) {
			return false;
		}
		if ( $this->app->utility->doing_cron() ) {
			// cronは除外
			return false;
		}

		$params = $this->app->input->get();
		if ( ! $this->check_params( $params, $is_admin ) ) {
			return false;
		}

		if ( $is_admin ) {
			if ( ! $this->check_admin( $params ) ) {
				return false;
			}
		} else {
			if ( ! $this->check_front() ) {
				return false;
			}
		}

		if ( ! $this->check_misc() ) {
			return false;
		}

		return true;
	}

	/**
	 * @param array $params
	 * @param bool $is_admin
	 *
	 * @return bool
	 */
	private function check_params( $params, $is_admin ) {
		if ( ! $this->app->input->is_post() ) {
			if ( $is_admin ) {
				unset( $params['page'] );
			}
			if ( empty( $params ) ) {
				// ページの表示のみ
				// GETパラメータによる操作等がないため除外
				return false;
			}
		}

		return true;
	}

	/**
	 * @param array $params
	 *
	 * @return bool
	 */
	private function check_admin( $params ) {
		if ( ! isset( $params['page'] ) ) {
			// 管理画面の対象はプラグイン等で追加されたページだけ (ajaxも除外)
			return false;
		}

		return true;
	}

	/**
	 * @return bool
	 */
	private function check_front() {
		if ( $this->apply_filters( 'exclude_front' ) ) {
			// フロント（管理画面以外）を除外
			return false;
		}
		if ( $this->apply_filters( 'exclude_get_front' ) && ! $this->app->input->is_post() ) {
			// フロント（管理画面以外） かつ GETメソッド(GET, HEAD, TRACE, OPTIONS) を除外
			return false;
		}

		return true;
	}

	/**
	 * @return bool
	 */
	private function check_misc() {
		if ( $this->apply_filters( 'exclude_get_method' ) && ! $this->app->input->is_post() ) {
			// GETメソッド(GET, HEAD, TRACE, OPTIONS) を除外
			return false;
		}
		if ( $this->apply_filters( 'exclude_same_host' ) ) {
			if ( ! $this->app->utility->is_changed_host() ) {
				// hostに変化がない場合を除外
				return false;
			}
		}
		if ( $this->apply_filters( 'exclude_admin_referer' ) ) {
			if ( $this->app->utility->was_admin() ) {
				// 管理画面からの送信を除外
				return false;
			}
		}

		return true;
	}

	/**
	 * @param string $option
	 *
	 * @return bool
	 */
	private function check_ignore_option( $option ) {
		if ( $this->check_wp_framework_option( $option ) ) {
			return true;
		}

		$pattern = $this->get_ignore_option_pattern();
		if ( empty( $pattern ) ) {
			return false;
		}

		return preg_match( $pattern, $option ) > 0;
	}

	/**
	 * @param string $option
	 *
	 * @return bool
	 */
	private function check_wp_framework_option( $option ) {
		foreach ( $this->app->get_instances() as $instance ) {
			if ( $instance->option->is_managed_option_name( $option ) ) {
				return true;
			}
		}

		return false;
	}

	/**
	 * @return string|false
	 */
	private function get_check_pattern() {
		if ( ! isset( $this->check_pattern ) ) {
			$this->check_pattern = false;
			$target              = $this->apply_filters( 'target_commands' );
			if ( preg_match( '#\A[a-zA-Z\s,]+\z#', $target ) ) {
				$targets = $this->app->string->explode( $target );
				if ( ! empty( $targets ) ) {
					$this->check_pattern = '/\A\s*(' . implode( '|', $targets ) . ')\s/i';
				}
			}
		}

		return $this->check_pattern;
	}

	/**
	 * @return string|false
	 */
	private function get_ignore_option_pattern() {
		if ( ! isset( $this->ignore_option_pattern ) ) {
			$this->ignore_option_pattern = $this->apply_filters( 'ignore_option_pattern' );
		}

		return $this->ignore_option_pattern;
	}

	/**
	 * @param string $query
	 */
	private function detect_db_update( $query ) {
		if ( ! $this->has_verified_nonce ) {
			$backtrace = $this->get_debug_backtrace();
			$target    = $this->get_target_plugin_or_theme( $backtrace );
			if ( false === $target ) {
				return;
			}
			$this->is_valid_detector = false;

			$this->app->log( 'csrf detected', [
				'target'    => $target,
				'query'     => $query,
				'backtrace' => $backtrace,
			], 'csrf' );
			$this->do_action( 'csrf_detected', $query, $backtrace, $target, $this->app, $this );

			if ( $this->apply_filters( 'shutdown_if_detected' ) ) {
				WP_Framework::wp_die( array_map( 'esc_html', [ $this->translate( 'CSRF detected' ), $target, $query ] ), __FILE__, __LINE__, '', false );
			}
		}
	}

	/**
	 * @return array
	 */
	private function get_debug_backtrace() {
		$backtrace = $this->app->utility->get_debug_backtrace();
		$found     = false;
		foreach ( $backtrace as $key => $value ) {
			if ( isset( $value['class'] ) && 'wpdb' === $value['class'] ) {
				$found = true;
			} elseif ( $found ) {
				break;
			}
			unset( $backtrace[ $key ] );
		}

		return $backtrace;
	}

	/**
	 * @param array $backtrace
	 *
	 * @return string|false
	 */
	private function get_target_plugin_or_theme( $backtrace ) {
		foreach ( $backtrace as $value ) {
			if ( isset( $value['file'] ) && preg_match( '#/(themes|plugins)/([^/]+)/#', $value['file'], $matches ) ) {
				$target = substr( $matches[1], 0, -1 );

				return "$target: {$matches[2]}";
			}
		}

		return false;
	}

	/**
	 * detected verify nonce
	 */
	public function detected_verify_nonce() {
		$this->has_verified_nonce = true;
	}
}
