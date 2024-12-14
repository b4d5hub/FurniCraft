<?php
/**
 * Template for displaying the content of wishlist widget.
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/wishlist/widget.php.
 *
 * @author  WCBoost
 * @package WCBoost\Wishlist\Templates
 * @version 1.1
 */

defined( 'ABSPATH' ) || exit;

if ( ! isset( $wishlist ) ) {
	return;
}

do_action( 'wcboost_wishlist_widget_before_content', $wishlist );

if ( ! $wishlist->is_empty() ) {
	?>

	<ul class="wcboost-wishlist-widget__products <?php echo esc_attr( $args['list_class'] ); ?>">
		<?php
		foreach ( $wishlist->get_items() as $item_key => $item ) :
			/** @var WC_Product */
			$_product = $item->get_product();

			if ( ! $_product || ! $_product->exists() ) {
				continue;
			}
			?>
			<li class="wcboost-wishlist-widget__item wcboost-wishlist-widget-item">
				<?php do_action( 'wcboost_wishlist_widget_item_start', $_product, $item, $wishlist ); ?>

				<a href="<?php echo esc_url( $item->get_remove_url() ) ?>" class="wcboost-wishlist-widget-item__remove remove" rel="nofollow">&times;</a>

				<?php if ( $_product->is_visible() ) : ?>
					<a href="<?php echo esc_url( $_product->get_permalink() ); ?>" class="wcboost-wishlist-widget-item__link">
						<?php echo wp_kses_post( $_product->get_image() ); ?>
						<span class="wcboost-wishlist-widget-item__title"><?php echo wp_kses_post( $_product->get_name() ); ?></span>
					</a>
				<?php else : ?>
					<span class="wcboost-wishlist-widget-item__title-wrapper">
						<?php echo wp_kses_post( $_product->get_image() ); ?>
						<span class="wcboost-wishlist-widget-item__title"><?php echo wp_kses_post( $_product->get_name() ); ?></span>
					</span>
				<?php endif; ?>

				<?php if ( ! empty( $args['show_price'] ) ) : ?>
					<span class="wcboost-wishlist-widget-item__price price">
						<?php echo $_product->get_price_html(); // PHPCS:Ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
					</span>
				<?php endif; ?>

				<?php if ( ! empty( $args['show_quantity'] ) ) : ?>
					<span class="wcboost-wishlist-widget-item__quantity">
						<?php echo esc_html( $item->get_quantity() ); ?>
					</span>
				<?php endif; ?>

				<?php if ( ! empty( $args['show_stock'] ) ) : ?>
					<?php
					$availability = $_product->get_availability();
					printf( '<span class="wcboost-wishlist-widget-item__stock-status %s">%s</span>', esc_attr( $availability['class'] ), $availability['availability'] ? esc_html( $availability['availability'] ) : esc_html__( 'In Stock', 'wcboost-wishlist' ) );
					?>
				<?php endif; ?>

				<?php if ( ! empty( $args['show_date'] ) ) : ?>
					<span class="wcboost-wishlist-widget-item__date">
						<?php echo esc_html( $item->get_date_added()->format( get_option( 'date_format' ) ) ); ?>
					</span>
				<?php endif; ?>

				<?php if ( ! empty( $args['show_purchase'] ) ) : ?>
					<span class="wcboost-wishlist-widget-item__add-to-cart">
						<?php
						if ( $_product->is_purchasable() && $_product->is_in_stock() ) {
							$GLOBALS['product'] = $_product;

							woocommerce_template_loop_add_to_cart( [ 'quantity' => max( 1, $item->get_quantity() ) ] );

							wc_setup_product_data( $GLOBALS['post'] );
						}
						?>
					</span>
				<?php endif; ?>

				<?php do_action( 'wcboost_wishlist_widget_item_end', $_product, $item, $wishlist ); ?>
			</li>
			<?php
		endforeach;
		?>
	</ul>

	<div class="wcboost-wishlist-widget__buttons">
		<?php do_action( 'wcboost_wishlist_widget_buttons', $wishlist, $args ); ?>
	</div>

	<?php
} else {
	?>
	<p class="wcboost-wishlist-widget__empty-message"><?php esc_html_e( 'No products in the wishlist', 'wcboost-wishlist' ); ?></p>
	<?php
}

do_action( 'wcboost_wishlist_widget_after_content', $wishlist );
