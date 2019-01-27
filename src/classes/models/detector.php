<?php
/**
 * @version 0.0.2
 * @author technote-space
 * @since 0.0.1
 * @copyright technote-space All Rights Reserved
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
	 * check admin validity
	 */
	/** @noinspection PhpUnusedPrivateMethodInspection */
	private function check_admin_validity() {
		if ( empty( $this->app->utility->definedv( 'CSRF_DETECTOR_FUNCTION_DEFINED' ) ) ) {
			$this->app->add_message( '<h3>CSRF Detector</h3>', 'error', true, false );
			$this->app->add_message( '[wp_verify_nonce] function has already been defined by other plugin or theme', 'error', true );
			$this->app->add_message( 'so [CSRF Detector] is not available', 'error', true );

			return;
		}
		if ( ! $this->check( true ) ) {
			return;
		}
		global $plugin_page;
		if ( ! isset( $plugin_page ) ) {
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
	/** @noinspection PhpUnusedPrivateMethodInspection */
	private function check_not_admin_validity() {
		if ( ! $this->check( false ) ) {
			return;
		}
		if ( ! $this->app->utility->definedv( 'WP_USE_THEMES' ) ) {
			if ( $this->app->utility->definedv( 'DOING_CRON' ) || $this->app->utility->definedv( 'WP_ADMIN' ) ) {
				return;
			}
		}

		$this->_is_valid_detector = ! empty( $this->app->utility->definedv( 'CSRF_DETECTOR_FUNCTION_DEFINED' ) );
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
		if ( ! $is_admin && $this->apply_filters( 'check_only_admin' ) ) {
			return false;
		}
		if ( $this->apply_filters( 'check_only_post' ) && ! $this->app->input->is_post() ) {
			return false;
		}

		return true;
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
		if ( ! $this->_is_valid_detector || $ignore ) {
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
	 * @param string $option
	 *
	 * @return bool
	 */
	private function check_ignore_option( $option ) {
		$pattern = $this->get_ignore_option_pattern();
		if ( empty( $pattern ) ) {
			return false;
		}

		return @preg_match( $pattern, $option ) > 0;
	}

	/**
	 * @return string|false
	 */
	private function get_check_pattern() {
		if ( ! isset( $this->_check_pattern ) ) {
			$this->_check_pattern = false;
			$target               = $this->apply_filters( 'target_commands' );
			if ( preg_match( '#\A[a-zA-Z\s,]+\z#', $target ) ) {
				$targets = $this->app->utility->explode( $target );
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
		if ( $this->_is_valid_detector && ! $this->_has_verified_nonce ) {
			$backtrace = $this->get_debug_backtrace();
			$target    = $this->get_target_plugin_or_theme( $backtrace );
			if ( false === $target ) {
				return;
			}
			$this->_is_valid_detector = false;

			$this->app->log( 'csrf detected', [
				'target'    => $target,
				'query'     => $query,
				'backtrace' => $backtrace,
			], 'csrf' );
			$this->do_action( 'csrf_detected', $query, $backtrace, $target, $this->app, $this );
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
				$target = substr( $matches[1], 0, - 1 );

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