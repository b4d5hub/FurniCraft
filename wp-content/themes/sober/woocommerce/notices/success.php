<?php
/**
 * Show messages
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/notices/success.php.
 *
 * HOWEVER, on occasion WooCommerce will need to update template files and you
 * (the theme developer) will need to copy the new files to your theme to
 * maintain compatibility. We try to do this as little as possible, but it does
 * happen. When this occurs the version of the template file will be bumped and
 * the readme will list any important changes.
 *
 * @see     https://woo.com/document/template-structure/
 * @package WooCommerce\Templates
 * @version 8.6.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
if ( ! $notices ) {
	return;
}
?>

<?php foreach ( $notices as $notice ) : ?>
	<div class="woocommerce-message sober-message-box success closeable"  role="alert">
		<span class="message-icon"><?php sober_svg_icon( 'icon=success' ) ?></span>
		<div class="box-content">
			<?php echo wc_kses_notice( $notice['notice'] ); ?>
		</div>
		<a class="close" href="#"><?php sober_svg_icon( 'icon=close-delete-small&size=14' ) ?></a>
	</div>
<?php endforeach; ?>
