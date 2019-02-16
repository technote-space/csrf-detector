<?php
/**
 * @version 0.0.12
 * @author technote-space
 * @since 0.0.12
 * @copyright technote-space All Rights Reserved
 * @license http://www.opensource.org/licenses/gpl-2.0.php GNU General Public License, version 2
 * @link https://technote.space/
 */

if ( ! defined( 'CSRF_DETECTOR' ) ) {
	return;
}
/** @var \WP_Framework_Presenter\Interfaces\Presenter $instance */
?>
<script>
    (function ($) {
        $(function () {
            $('input[name="reset"]').on('click', function () {
                return window.confirm('<?php $instance->h( 'Are you sure to reset settings?', true );?>');
            });
        });
    })(jQuery);
</script>