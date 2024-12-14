<?php
/**
 * Template for displaying the content of compare widget.
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/compare/compare-widget.php.
 *
 * @author  WCBoost
 * @package WCBoost\ProductsCompare\Templates
 * @version 1.0.5
 */

defined( 'ABSPATH' ) || exit;

if ( ! isset( $compare_items ) ) {
	return;
}

do_action( 'wcboost_products_compare_widget_before_contents' );
?>

<?php if ( ! empty( $compare_items ) ) : ?>

	<ul class="wcboost-products-compare-widget__products <?php echo esc_attr( $args['list_class'] ); ?>">
		<?php
		foreach ( $compare_items as $item_key => $product_id ) :
			$_product = wc_get_product( $product_id );

			if ( $_product && $_product->exists() ) {
				$product_permalink = $_product->is_visible() ? $_product->get_permalink() : '';
				?>
				<li class="wcboost-products-compare-widget__item wcboost-products-compare-widget-item">
					<a href="<?php echo esc_url( \WCBoost\ProductsCompare\Helper::get_remove_url( $_product ) ) ?>" class="wcboost-products-compare-widget-item__remove remove" rel="nofollow">&times;</a>
					<?php if ( $_product->is_visible() ) : ?>
						<a href="<?php echo esc_url( $_product->get_permalink() ); ?>">
							<?php echo wp_kses_post( $_product->get_image() ); ?>
							<span class="wcboost-products-compare-widget-item__title"><?php echo wp_kses_post( $_product->get_name() ); ?></span>
						</a>
					<?php else : ?>
						<?php echo wp_kses_post( $_product->get_image() ); ?>
						<span class="wcboost-products-compare-widget-item__title"><?php echo wp_kses_post( $_product->get_name() ); ?></span>
					<?php endif; ?>

					<?php if ( $args['show_rating'] ) : ?>
						<?php echo wc_get_rating_html( $_product->get_average_rating() ); // PHPCS:Ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
					<?php endif; ?>

					<span class="price">
						<?php echo $_product->get_price_html(); // PHPCS:Ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
					</span>
				</li>
				<?php
			}
		endforeach;
		?>
	</ul>

	<div class="wcboost-products-compare-widget__buttons">
		<?php do_action( 'wcboost_products_compare_widget_buttons' ); ?>
	</div>

<?php else : ?>
	<p class="wcboost-products-compare-widget__empty-message"><?php esc_html_e( 'No products in the compare list', 'wcboost-products-compare' ); ?></p>
<?php endif; ?>

<?php
do_action( 'wcboost_products_compare_widget_after_contents' );
