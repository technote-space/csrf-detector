<?php
/**
 * @version 0.0.12
 * @author technote-space
 * @since 0.0.7
 * @since 0.0.12 Changed: trivial change
 * @copyright technote All Rights Reserved
 * @license http://www.opensource.org/licenses/gpl-2.0.php GNU General Public License, version 2
 * @link https://technote.space/
 */

if ( ! defined( 'CSRF_DETECTOR' ) ) {
	return;
}
/** @var \WP_Framework_Presenter\Interfaces\Presenter $instance */
/** @var array $args */
/** @var array $settings */
?>

<?php $instance->form( 'open', $args ); ?>
<div id="<?php $instance->id(); ?>-dashboard" class="wrap narrow">
    <div id="<?php $instance->id(); ?>-content-wrap">
        <table class="form-table">
			<?php foreach ( $settings as $k => $v ) : ?>
                <tr>
                    <th>
                        <label for="<?php $instance->h( $v['id'] ); ?>"><?php $instance->h( $v['title'] ); ?></label>
                    </th>
                    <td>
						<?php $instance->form( $v['form'], $args, $v ); ?>
                    </td>
                </tr>
			<?php endforeach; ?>
        </table>
        <div>
			<?php $instance->form( 'input/submit', $args, [
				'name'  => 'update',
				'value' => 'Update',
				'class' => 'button-primary large',
			] ); ?>
			<?php $instance->form( 'input/submit', $args, [
				'name'  => 'reset',
				'value' => 'Reset',
				'class' => 'button-primary',
			] ); ?>
        </div>
    </div>
</div>
<?php $instance->form( 'close', $args ); ?>
