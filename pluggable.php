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

if ( ! function_exists( 'wp_verify_nonce' ) ) :
	define( 'CSRF_DETECTOR_FUNCTION_DEFINED', true );

	function csrf_detector_verified_nonce() {
		if ( defined( 'WP_FRAMEWORK_IS_MOCK' ) && ! WP_FRAMEWORK_IS_MOCK ) {
			$app = WP_Framework::get_instance( CSRF_DETECTOR );
			/** @var Csrf_Detector\Classes\Models\Detector $detector */
			$detector = Csrf_Detector\Classes\Models\Detector::get_instance( $app );
			$detector->detected_verify_nonce();
		}
	}

	function wp_verify_nonce( $nonce, $action = - 1 ) {
		$nonce = (string) $nonce;
		$user  = wp_get_current_user();
		$uid   = (int) $user->ID;
		if ( ! $uid ) {
			/**
			 * Filters whether the user who generated the nonce is logged out.
			 *
			 * @since 3.5.0
			 *
			 * @param int $uid ID of the nonce-owning user.
			 * @param string $action The nonce action.
			 */
			$uid = apply_filters( 'nonce_user_logged_out', $uid, $action );
		}

		if ( empty( $nonce ) ) {
			return false;
		}

		$cookie = wp_parse_auth_cookie( '', 'logged_in' );
		$token  = ! empty( $cookie['token'] ) ? $cookie['token'] : '';
		$i      = wp_nonce_tick();

		// Nonce generated 0-12 hours ago
		$expected = substr( wp_hash( $i . '|' . $action . '|' . $uid . '|' . $token, 'nonce' ), - 12, 10 );
		if ( hash_equals( $expected, $nonce ) ) {
			csrf_detector_verified_nonce();

			return 1;
		}

		// Nonce generated 12-24 hours ago
		$expected = substr( wp_hash( ( $i - 1 ) . '|' . $action . '|' . $uid . '|' . $token, 'nonce' ), - 12, 10 );
		if ( hash_equals( $expected, $nonce ) ) {
			csrf_detector_verified_nonce();

			return 2;
		}

		/**
		 * Fires when nonce verification fails.
		 *
		 * @since 4.4.0
		 *
		 * @param string $nonce The invalid nonce.
		 * @param string|int $action The nonce action.
		 * @param WP_User $user The current user object.
		 * @param string $token The user's session token.
		 */
		do_action( 'wp_verify_nonce_failed', $nonce, $action, $user, $token );

		// Invalid nonce
		return false;
	}
endif;