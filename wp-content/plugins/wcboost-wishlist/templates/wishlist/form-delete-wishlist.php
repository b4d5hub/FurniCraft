<?php
/**
 * Template for displaying the form to delete a wishlist.
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/wishlist/wishlist-delete.php.
 *
 * @author  WCBoost
 * @package WCBoost\Wishlist\Templates
 * @version 1.0
 */

defined( 'ABSPATH' ) || exit;

if ( ! isset( $wishlist ) ) {
	return;
}

?>
<form class="wcboost-wishlist-form-delete" method="post" action="<?php echo esc_url( wc_get_page_permalink( 'wishlist' ) ); ?>">
	<?php if ( ! empty( $title ) ) : ?>
		<h4 class="wcboost-wishlist-form-delete__title"><?php echo esc_html( $title ); ?></h4>
	<?php endif; ?>

	<?php if ( ! empty( $message ) ) : ?>
		<div class="wcboost-wishlist-form-delete__message"><p><?php echo wp_kses_post( $message ); ?></p></div>
	<?php endif; ?>

	<?php do_action( 'wcboost_wishlist_after_delete_form', $wishlist ); ?>

	<?php if ( ! $wishlist->is_default() ) : ?>
		<button type="submit" class="button" name="delete_wishlist" value="<?php esc_attr_e( 'Delete wishlist', 'wcboost-wishlist' ); ?>"><?php esc_html_e( 'Delete wishlist', 'wcboost-wishlist' ); ?></button>

		<?php wp_nonce_field( 'wcboost-wishlist-delete' ); ?>
		<input type="hidden" name="wishlist_id" value="<?php echo esc_attr( $wishlist->get_id() ); ?>" />
		<input type="hidden" name="action" value="delete_wishlist" />
	<?php endif; ?>
</form>
