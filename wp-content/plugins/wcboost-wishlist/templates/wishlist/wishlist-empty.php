<?php
/**
 * Template for displaying the empty wishlist.
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/wishlist/wishlist-empty.php.
 *
 * @author  WCBoost
 * @package WCBoost\Wishlist\Templates
 * @version 1.0.10
 */

defined( 'ABSPATH' ) || exit;

do_action( 'wcboost_wishlist_before_wishlist' ); ?>

<div class="wishlist-empty">
	<?php
	wc_print_notice( apply_filters( 'wcboost_wishlist_empty_message', __( 'The wishlist is empty.', 'wcboost-wishlist' ) ), 'notice' );
	?>
</div>

<?php if ( ! empty( $args['return_url'] ) ) : ?>
	<p class="return-to-shop">
		<?php
		echo wp_kses_post( apply_filters( 'wcboost_wishlist_return_to_shop_link', sprintf(
			'<a href="%s" class="button wc-backward">%s</a>',
			esc_url( $args['return_url'] ),
			esc_html__( 'Return to shop', 'wcboost-wishlist' )
		), $args ) );
		?>
	</p>
<?php endif; ?>

<?php do_action( 'wcboost_wishlist_after_wishlist' ); ?>
