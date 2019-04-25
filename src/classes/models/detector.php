<?php
/**
 * @version 0.0.16
 * @author Technote
 * @since 0.0.1
 * @copyright Technote All Rights Reserved
 * @license http://www.opensource.org/licenses/gpl-2.0.php GNU General Public License, version 2
 * @link https://technote.space/
 */

namespace Csrf_Detector\Classes\Models;

if ( ! defined( 'CSRF_DETECTOR' ) ) {
	exit;
}

/**
 * Class Detector
 * @package Marker_Animation\Classes\Models
 */
class Detector implements \WP_Framework_Core\Interfaces\Singleton, \WP_Framework_Core\Interfaces\Hook, \WP_Framework_Presenter\Interfaces\Presenter {

	use \WP_Framework_Core\Traits\Singleton, \WP_Framework_Core\Traits\Hook, \WP_Framework_Presenter\Traits\Presenter, \WP_Framework_Common\Traits\Package;

	/**
	 * @var string|false $_check_pattern
	 */
	private $_check_pattern;

	/**
	 * @var string|false $_ignore_option_pattern
	 */
	private $_ignore_option_pattern;

	/**
	 * @var bool $_is_valid_detector
	 */
	private $_is_valid_detector = false;

	/**
	 * @var bool $_has_verified_nonce
	 */
	private $_has_verified_nonce = false;

	/**
	 * @var bool $_ignore_check
	 */
	private $_ignore_check = false;

	/**
	 * @var bool $_db_update
	 */
	private $_db_update = false;

	/**
	 * check validity
	 */
	/** @noinspection PhpUnusedPrivateMethodInspection */
	private function check_validity() {
		if ( is_admin() ) {
			$this->check_admin_validity();
		} else {
			$this->check_not_admin_validity();
		}
	}

	/**
	 * verified nonce
	 */
	/** @noinspection PhpUnusedPrivateMethodInspection */
	private function verified_nonce() {
		$this->detected_verify_nonce();
	}

	/**
	 * start db update
	 */
	/** @noinspection PhpUnusedPrivateMethodInspection */
	private function start_db_update() {
		$this->_db_update = true;
	}

	/**
	 * finished db update
	 */
	/** @noinspection PhpUnusedPrivateMethodInspection */
	private function finished_db_update() {
		$this->_db_update = false;
	}

	/**
	 * @param string $query
	 *
	 * @return string
	 */
	/** @noinspection PhpUnusedPrivateMethodInspection */
	private function check_query( $query ) {
		if ( preg_match( '/^SHOW FULL COLUMNS FROM\s/', $query ) ) {
			return $query;
		}

		$ignore              = $this->_ignore_check;
		$this->_ignore_check = false;
		if ( ! $this->_is_valid_detector || $ignore || $this->_db_update ) {
			return $query;
		}

		if ( @preg_match( $this->get_check_pattern(), $query ) ) {
			$this->detect_db_update( $query );
		}

		return $query;
	}

	/**
	 * @param string $option
	 */
	/** @noinspection PhpUnusedPrivateMethodInspection */
	private function update_option( $option ) {
		$this->_ignore_check = $this->check_ignore_option( $option );
	}

	/**
	 * @param string $option
	 */
	/** @noinspection PhpUnusedPrivateMethodInspection */
	private function add_option( $option ) {
		$this->_ignore_check = $this->check_ignore_option( $option );
	}

	/**
	 * @param string $option
	 */
	/** @noinspection PhpUnusedPrivateMethodInspection */
	private function delete_option( $option ) {
		$this->_ignore_check = $this->check_ignore_option( $option );
	}

	/**
	 * setup settings
	 */
	/** @noinspection PhpUnusedPrivateMethodInspection */
	private function setup_settings() {
		$this->app->setting->edit_setting( 'is_valid_log', 'default', true );
		$this->app->setting->remove_setting( 'capture_shutdown_error' );
		$this->app->setting->remove_setting( 'minify_js' );
		$this->app->setting->remove_setting( 'minify_css' );
		$this->app->setting->remove_setting( 'assets_version' );
	}

	/**
	 * @return int
	 */
	/** @noinspection PhpUnusedPrivateMethodInspection */
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

		$this->_is_valid_detector = true;
		if ( empty( $this->get_check_pattern() ) ) {
			$this->_is_valid_detector = false;
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

		$this->_is_valid_detector = ! empty( $this->app->utility->defined( 'CSRF_DETECTOR_FUNCTION_DEFINED' ) );
		if ( $this->_is_valid_detector && empty( $this->get_check_pattern() ) ) {
			$this->_is_valid_detector = false;
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
		if ( ! $is_admin && $this->apply_filters( 'exclude_front' ) ) {
			// フロント（管理画面以外）を除外
			return false;
		}
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
		if ( ! $this->app->input->is_post() ) {
			$params = $this->app->input->get();
			if ( $is_admin ) {
				unset( $params['page'] );
			}
			if ( empty( $params ) ) {
				// ページの表示のみ
				// GETパラメータによる操作等がないため除外
				return false;
			}
		}
		if ( $is_admin && ! isset( $_GET['page'] ) ) {
			// 管理画面の対象はプラグイン等で追加されたページだけ (ajaxも除外)
			return false;
		}
		if ( $this->app->utility->doing_cron() ) {
			// cronは除外
			return false;
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

		return @preg_match( $pattern, $option ) > 0;
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
		if ( ! isset( $this->_check_pattern ) ) {
			$this->_check_pattern = false;
			$target               = $this->apply_filters( 'target_commands' );
			if ( preg_match( '#\A[a-zA-Z\s,]+\z#', $target ) ) {
				$targets = $this->app->string->explode( $target );
				if ( ! empty( $targets ) ) {
					$this->_check_pattern = '/\A\s*(' . implode( '|', $targets ) . ')\s/i';
				}
			}
		}

		return $this->_check_pattern;
	}

	/**
	 * @return string|false
	 */
	private function get_ignore_option_pattern() {
		if ( ! isset( $this->_ignore_option_pattern ) ) {
			$this->_ignore_option_pattern = $this->apply_filters( 'ignore_option_pattern' );
		}

		return $this->_ignore_option_pattern;
	}

	/**
	 * @param string $query
	 */
	private function detect_db_update( $query ) {
		if ( ! $this->_has_verified_nonce ) {
			$backtrace = $this->get_debug_backtrace();
			$target    = $this->get_target_plugin_or_theme( $backtrace );
			if ( false === $target ) {
				return;
			}
			$this->_is_valid_detector = false;

			try {
				$this->app->log( 'csrf detected', [
					'target'    => $target,
					'query'     => $query,
					'backtrace' => $backtrace,
				], 'csrf' );
				$this->do_action( 'csrf_detected', $query, $backtrace, $target, $this->app, $this );
			} catch ( \Exception $e ) {
			}
			if ( $this->apply_filters( 'shutdown_if_detected' ) ) {
				\WP_Framework::wp_die( [ $this->translate( 'CSRF detected' ), $target, $query ], __FILE__, __LINE__, '', false );
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
		$this->_has_verified_nonce = true;
	}
}