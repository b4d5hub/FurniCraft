<?php
/**
 * Template for displaying wishlist table.
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/wishlist/wishlist-table.php.
 *
 * @author  WCBoost
 * @package WCBoost\Wishlist\Templates
 * @version 1.1.5
 */

defined( 'ABSPATH' ) || exit;

if ( ! isset( $wishlist ) ) {
	return;
}
?>

<?php do_action( 'wcboost_wishlist_before_wishlist_table', $wishlist ); ?>

<table class="shop_table shop_table_responsive wishlist_table wishlist" cellspacing="0">
	<thead>
		<tr>
			<?php if ( $wishlist->can_edit() ) : ?>
				<th class="product-remove">&nbsp;</th>
			<?php endif; ?>
			<th class="product-thumbnail">&nbsp;</th>
			<th class="product-name"><?php esc_html_e( 'Product', 'wcboost-wishlist' ); ?></th>
			<?php if ( $args['columns']['price'] ) : ?>
				<th class="product-price"><?php esc_html_e( 'Price', 'wcboost-wishlist' ); ?></th>
			<?php endif; ?>
			<?php if ( $args['columns']['quantity'] && $wishlist->can_edit() ) : ?>
				<th class="product-quantity"><?php esc_html_e( 'Quantity', 'wcboost-wishlist' ); ?></th>
			<?php endif; ?>
			<?php if ( $args['columns']['stock'] ) : ?>
				<th class="product-stock-status"><?php esc_html_e( 'Stock status', 'wcboost-wishlist' ); ?></th>
			<?php endif; ?>
			<?php if ( $args['columns']['date'] ) : ?>
				<th class="product-date"><?php esc_html_e( 'Added Date', 'wcboost-wishlist' ); ?></th>
			<?php endif; ?>
			<?php if ( $args['columns']['purchase'] ) : ?>
				<th class="product-add-to-cart">&nbsp;</th>
			<?php endif; ?>
		</tr>
	</thead>
	<tbody>
		<?php
		foreach ( $wishlist->get_items() as $item_key => $item ) :
			/** @var WC_Product */
			$_product = $item->get_product();

			if ( ! $_product || ! $_product->exists() ) {
				continue;
			}

			$product_permalink = $_product->is_visible() ? $_product->get_permalink() : '';
			?>
			<tr class="wcboost-wishlist__item <?php echo esc_attr( apply_filters( 'wcboost_wishlist_item_class', 'wcboost-wishlist-item', $item, $item_key ) ); ?>">

				<?php if ( $wishlist->can_edit() ) : ?>
					<td class="product-remove">
						<?php
						echo apply_filters( // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
							'wcboost_wishlist_item_remove_link',
							sprintf(
								'<a href="%s" class="remove" aria-label="%s" data-product_id="%s" data-product_sku="%s" rel="nofollow">&times;</a>',
								esc_url( $item->get_remove_url() ),
								esc_html__( 'Remove this item', 'wcboost-wishlist' ),
								esc_attr( $_product->get_id() ),
								esc_attr( $_product->get_sku() )
							),
							$item_key
						);
						?>
					</td>
				<?php endif; ?>

				<td class="product-thumbnail">
					<?php
					if ( ! $product_permalink ) {
						echo wp_kses_post( $_product->get_image() );
					} else {
						echo wp_kses_post( sprintf( '<a href="%s">%s</a>', esc_url( $product_permalink ), $_product->get_image() ) );
					}
					?>
				</td>

				<td class="product-name" data-title="<?php esc_attr_e( 'Product', 'wcboost-wishlist' ); ?>">
					<?php
					if ( ! $product_permalink ) {
						echo wp_kses_post( $_product->get_name() );
					} else {
						echo wp_kses_post( sprintf( '<a href="%s">%s</a>', esc_url( $product_permalink ), $_product->get_name() ) );
					}

					do_action( 'wcboost_wishlist_after_item_name', $item, $item_key, $wishlist );

					if ( $args['show_variation_data'] && $_product->is_type( 'variation' ) ) {
						echo wp_kses_post( wc_get_formatted_variation( $_product ) );
					}
					?>
				</td>

				<?php if ( $args['columns']['price'] ) : ?>
					<td class="product-price" data-title="<?php esc_attr_e( 'Price', 'wcboost-wishlist' ); ?>">
						<span class="price"><?php echo wp_kses_post( $_product->get_price_html() ); ?></span>
					</td>
				<?php endif; ?>

				<?php if ( $args['columns']['quantity'] && $wishlist->can_edit() ) : ?>
					<td class="product-quantity" data-title="<?php esc_attr_e( 'Quantity', 'wcboost-wishlist' ); ?>">
						<?php
						if ( $_product->is_sold_individually() ) {
							printf( '1 <input type="hidden" name="wishlist_item[%s][qty]" value="1" />', esc_attr( $item_key ) );
						} else {
							woocommerce_quantity_input(
								[
									'input_name'   => "wishlist_item[{$item_key}][qty]",
									'input_value'  => $item->get_quantity(),
									'max_value'    => $_product->get_max_purchase_quantity(),
									'min_value'    => '0',
									'product_name' => $_product->get_name(),
								],
								$_product
							);
						}
						?>
					</td>
				<?php endif; ?>

				<?php if ( $args['columns']['stock'] ) : ?>
					<td class="product-stock-status" data-title="<?php esc_attr_e( 'Stock status', 'wcboost-wishlist' ); ?>">
						<?php
						$availability = $_product->get_availability();
						printf( '<span class="%s">%s</span>', esc_attr( $availability['class'] ), $availability['availability'] ? esc_html( $availability['availability'] ) : esc_html__( 'In Stock', 'wcboost-wishlist' ) );
						?>
					</td>
				<?php endif; ?>

				<?php if ( $args['columns']['date'] ) : ?>
					<td class="product-date"><?php echo esc_html( $item->get_date_added()->format( get_option( 'date_format' ) ) ); ?></td>
				<?php endif; ?>

				<?php if ( $args['columns']['purchase'] ) : ?>
					<td class="product-add-to-cart">
						<?php
						if ( $_product->is_purchasable() && $_product->is_in_stock() ) {
							$GLOBALS['product'] = $_product;

							woocommerce_template_loop_add_to_cart( [ 'quantity' => max( 1, $item->get_quantity() ) ] );

							wc_setup_product_data( $GLOBALS['post'] );
						}
						?>
					</td>
				<?php endif; ?>

			</tr>
			<?php
		endforeach;
		?>
	</tbody>
</table>

<?php do_action( 'wcboost_wishlist_after_wishlist_table', $wishlist ); ?>

<?php if ( $wishlist->can_edit() && $args['columns']['quantity'] ) : ?>
	<div class="wcboost-wishlist-actions">
		<button type="submit" class="button alt" name="update_wishlist" value="<?php esc_attr_e( 'Update wishlist', 'wcboost-wishlist' ); ?>"><?php esc_html_e( 'Update wishlist', 'wcboost-wishlist' ); ?></button>
		<input type="hidden" name="wishlist_id" value="<?php echo esc_attr( $wishlist->get_id() ); ?>" />
		<?php wp_nonce_field( 'wcboost-wishlist-update' ); ?>
	</div>
<?php endif; ?>
