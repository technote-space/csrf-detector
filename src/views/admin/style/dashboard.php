<?php
/**
 * @version 0.0.7
 * @author technote-space
 * @since 0.0.7
 * @copyright technote All Rights Reserved
 * @license http://www.opensource.org/licenses/gpl-2.0.php GNU General Public License, version 2
 * @link https://technote.space/
 */

if ( ! defined( 'CSRF_DETECTOR' ) ) {
	return;
}
/** @var \WP_Framework_Presenter\Interfaces\Presenter $instance */
?>

<style>
    #<?php $instance->id(); ?>-main-contents input[type="submit"].button-primary.left {
        margin-right: 10px;
    }

    #<?php $instance->id(); ?>-main-contents .form-table {
        width: auto;
    }

    #<?php $instance->id(); ?>-main-contents .form-table th {
        width: 30%;
    }

    #<?php $instance->id(); ?>-main-contents .form-table td {
        width: 70%;
    }

    #<?php $instance->id(); ?>-main-contents select {
        width: 120px;
    }

    #<?php $instance->id(); ?>-main-contents input[type="text"] {
        width: 100%;
    }
</style>