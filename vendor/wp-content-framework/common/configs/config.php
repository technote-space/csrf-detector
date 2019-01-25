<?php
/**
 * WP_Framework_Common Configs Config
 *
 * @version 0.0.1
 * @author technote-space
 * @since 0.0.1
 * @copyright technote-space All Rights Reserved
 * @license http://www.opensource.org/licenses/gpl-2.0.php GNU General Public License, version 2
 * @link https://technote.space
 */

if ( ! defined( 'WP_CONTENT_FRAMEWORK' ) ) {
	exit;
}

return [

	// update
	'update_info_file_url'      => '',

	// readme
	'readme_file_check_url'     => '',

	// cache filter result
	'cache_filter_result'       => true,

	// cache filter exclude list
	'cache_filter_exclude_list' => [],

];