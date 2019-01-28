<?php
/**
 * @version 0.0.7
 * @author technote-space
 * @since 0.0.7
 * @copyright technote All Rights Reserved
 * @license http://www.opensource.org/licenses/gpl-2.0.php GNU General Public License, version 2
 * @link https://technote.space/
 */

namespace Csrf_Detector\Classes\Controllers\Admin;

if ( ! defined( 'CSRF_DETECTOR' ) ) {
	exit;
}

/**
 * Class Dashboard
 * @package Csrf_Detector\Classes\Controllers\Admin
 */
class Dashboard extends \WP_Framework_Admin\Classes\Controllers\Admin\Base {

	/**
	 * @return int
	 */
	public function get_load_priority() {
		return 0;
	}

	/**
	 * @return string
	 */
	public function get_page_title() {
		return 'Dashboard';
	}

	/**
	 * post
	 */
	protected function post_action() {
		if ( $this->app->input->post( 'reset' ) ) {
			foreach ( $this->get_settings() as $name => $form ) {
				$this->app->option->delete( $this->get_filter_prefix() . $name );
				$this->delete_hook_cache( $name );
			}
			$this->app->add_message( 'Settings have been reset.', 'setting' );
		} elseif ( $this->app->input->post( 'update' ) ) {
			foreach ( $this->get_settings() as $name => $form ) {
				$this->update_setting( $name, $form );
			}
			$this->app->add_message( 'Settings have been updated.', 'setting' );
		}
	}

	/**
	 * @return array
	 */
	protected function get_view_args() {
		$settings = [];
		foreach ( $this->get_settings() as $name => $form ) {
			$settings[ $name ] = $this->get_setting( $name, $form );
		}

		return [
			'settings' => $settings,
		];
	}

	/**
	 * @return array
	 */
	private function get_settings() {
		return [
			'shutdown_if_detected' => 'input/checkbox',
			'csrf_mail_to'         => 'input/text',
			'target_commands'      => [
				'form' => 'multi_select',
				'args' => [
					'options' => $this->app->utility->array_combine( $this->app->utility->explode( $this->app->setting->get_setting( 'target_commands', true )['default'] ), null ),
				],
			],
			'check_only_post'      => 'input/checkbox',
			'check_only_admin'     => 'input/checkbox',
		];
	}

	/**
	 * @param string $name
	 * @param string|array $form
	 *
	 * @return array
	 */
	private function process_setting_detail( $name, $form ) {
		$detail       = $this->app->utility->array_get( is_array( $form ) ? $form : [], 'detail', $this->app->setting->get_setting( $name, true ) );
		$value        = $this->app->utility->array_get( $detail, 'value' );
		$ret          = [
			'id'         => $this->get_id_prefix() . $name,
			'class'      => 'csrf-detector-option',
			'name'       => $this->get_name_prefix() . $name,
			'value'      => $value,
			'title'      => $this->translate( $this->app->utility->array_get( $detail, 'label', $name ) ),
			'attributes' => [
				'data-value'   => $value,
				'data-default' => $this->app->utility->array_get( $detail, 'default' ),
			],
			'detail'     => $detail,
		];
		$ret['label'] = $ret['title'];
		if ( is_array( $form ) ) {
			$ret['form'] = $form['form'];
			$ret         = array_replace_recursive( $ret, isset( $form['args'] ) && is_array( $form['args'] ) ? $form['args'] : [] );
		} else {
			$ret['form'] = $form;
		}

		return [ $ret, $detail, $value ];
	}

	/**
	 * @param string $name
	 * @param string|array $form
	 *
	 * @return array
	 */
	private function get_setting( $name, $form ) {
		list( $ret, $detail, $value ) = $this->process_setting_detail( $name, $form );

		if ( $this->app->utility->array_get( $detail, 'type' ) === 'bool' ) {
			$ret['value'] = 1;
			$ret['label'] = 'Yes';
			! empty( $value ) and $ret['checked'] = true;
		}
		if ( $ret['form'] === 'select' ) {
			$ret['selected'] = $value;
			if ( ! empty( $ret['options'] ) && ! isset( $ret['options'][ $value ] ) ) {
				$ret['options'][ $value ] = $value;
			}
		}
		if ( $ret['form'] === 'multi_select' ) {
			$ret['form']     = 'select';
			$ret['multiple'] = true;
			$ret['selected'] = $this->app->utility->explode( $value, $this->app->utility->array_get( $ret, 'delimiter', ',' ) );
			! empty( $ret['options'] ) and $ret['size'] = count( $ret['options'] );
			$ret['name'] .= '[]';
		}

		return $ret;
	}

	/**
	 * @param string $name
	 * @param string|array $form
	 *
	 * @return bool
	 */
	private function update_setting( $name, $form ) {
		list( $ret, $detail ) = $this->process_setting_detail( $name, $form );
		$default = null;
		if ( $this->app->utility->array_get( $detail, 'type' ) === 'bool' ) {
			$default = 0;
		}
		if ( $ret['form'] === 'multi_select' ) {
			$key = $this->get_filter_prefix() . $name;
			$this->app->input->set_post( $key, implode( $this->app->utility->array_get( $ret, 'delimiter', ',' ), $this->app->input->post( $key ) ) );
		}

		return $this->app->option->set_post_value( $this->get_filter_prefix() . $name, $default );
	}

	/**
	 * @return string
	 */
	private function get_id_prefix() {
		return $this->app->slug_name . '-';
	}

	/**
	 * @return string
	 */
	private function get_name_prefix() {
		return $this->get_filter_prefix();
	}
}
