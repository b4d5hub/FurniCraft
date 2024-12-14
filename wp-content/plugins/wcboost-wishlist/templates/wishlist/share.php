<?php
/**
 * Template for displaying wishlist sharing buttons.
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/wishlist/share.php.
 *
 * @author  WCBoost
 * @package WCBoost\Wishlist\Templates
 * @version 1.1.5
 */

defined( 'ABSPATH' ) || exit;

do_action( 'wcboost_wishlist_before_share_buttons' );
?>

<div class="wcboost-wishlist-share wishlist-share">
	<?php if ( ! empty( $args['title'] ) ) : ?>
		<h4 class="wishlist-share__title" aria-label="<?php esc_attr_e( 'Share the wishlist', 'wcboost-wishlist' ) ?>"><?php echo esc_html( $args['title'] ); ?></h4>
	<?php endif; ?>

	<ul class="wishlist-share__buttons">
		<?php foreach ( $args['socials'] as $social ) : ?>
			<li class="wishlist-share__<?php echo esc_attr( $social ); ?>">
				<?php
				// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
				echo \WCBoost\Wishlist\Helper::get_share_link( $social, $args['wishlist'] );
				?>
			</li>
		<?php endforeach; ?>
	</ul>
</div>

<?php
do_action( 'wcboost_wishlist_after_share_buttons' );
