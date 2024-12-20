<?php
/**
 * Template for displaying wishlist.
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/wishlist/wishlist.php.
 *
 * @author        SooPlugins
 * @package       Soo Wishlist/Templates
 * @version       1.0.4
 */

global $product, $post;
?>

<?php do_action( 'soo_wishlist_before_list', $list ); ?>

<table class="shop_table shop_table_responsive wishlist_table" cellspacing="0">
	<thead>
	<tr>
		<?php if ( Soo_Wishlist()->can_edit( $list ) ) : ?>
			<th class="product-remove">&nbsp;</th>
		<?php endif; ?>
		<th class="product-thumbnail" colspan="2"><?php esc_html_e( 'Product', 'sober' ); ?></th>
		<?php if ( $options['show_price'] == 'yes' ) : ?>
			<th class="product-price"><?php esc_html_e( 'Price', 'sober' ); ?></th>
		<?php endif; ?>
		<?php if ( $options['show_stock_status'] == 'yes' ) : ?>
			<th class="product-stock-status"><?php esc_html_e( 'Stock status', 'sober' ); ?></th>
		<?php endif; ?>
		<?php if ( $options['show_button'] == 'yes' ) : ?>
			<th class="product-add-to-cart">&nbsp;</th>
		<?php endif; ?>
	</tr>
	</thead>

	<?php foreach ( $list->items as $key => $item ) : ?>
		<?php
		$item_id = soow_translated_object_id( $item['id'], 'product' );
		$product = wc_get_product( $item_id );

		if ( ! $product || is_wp_error( $product ) ) {
			continue;
		}

		if ( ! $product->is_visible() ) {
			continue;
		}
		?>
		<tr class="product-item">
			<?php if ( Soo_Wishlist()->can_edit( $list ) ) : ?>
				<td class="item-remove">
					<?php
					echo apply_filters(
						'soo_wishlist_item_remove_link',
						sprintf(
							'<a href="%s" class="remove" title="%s" data-product_id="%s">' . sober_svg_icon( 'icon=close-delete-small&size=12&echo=0' ) . '</a>',
							esc_url( Soo_Wishlist()->get_remove_url( $item['id'] ) ),
							esc_html__( 'Remove this item', 'sober' ),
							esc_attr( $product->get_id() )
						),
						$product
					);
					?>
				</td>
			<?php endif; ?>
			<td class="product-thumbnail">
				<a href="<?php echo esc_url( $product->get_permalink() ) ?>"><?php echo wp_kses_post( $product->get_image() ) ?></a>
			</td>
			<td class="product-name">
				<a href="<?php echo esc_url( $product->get_permalink() ) ?>"><?php echo wp_kses_post( $product->get_title() ) ?></a>

				<?php if ( $options['show_price'] == 'yes' ) : ?>
					<span class="product-inline-price">
						<?php esc_html_e( 'Price', 'sober' ); ?>:
						<?php echo wp_kses_post( $product->get_price_html() ) ?>
					</span>
				<?php endif; ?>

				<?php if ( $options['show_stock_status'] == 'yes' ) : ?>
					<span class="product-inline-stock-status">
						<?php
						if ( $product->is_in_stock() ) {
							esc_html_e( 'In stock', 'sober' );
						} else {
							esc_html_e( 'Out of stock', 'sober' );
						}
						?>
					</span>
				<?php endif; ?>

				<?php if ( $options['show_button'] == 'yes' ) : ?>
					<span class="product-inline-button"><?php woocommerce_template_loop_add_to_cart(); ?></span>
				<?php endif; ?>
			</td>

			<?php if ( $options['show_price'] == 'yes' ) : ?>
				<td class="product-price">
					<?php echo wp_kses_post( $product->get_price_html() ) ?>
				</td>
			<?php endif; ?>

			<?php if ( $options['show_stock_status'] == 'yes' ) : ?>
				<td class="product-stock-status">
					<?php
					if ( $product->is_in_stock() ) {
						esc_html_e( 'In stock', 'sober' );
					} else {
						esc_html_e( 'Out of stock', 'sober' );
					}
					?>
				</td>
			<?php endif; ?>

			<?php if ( $options['show_button'] == 'yes' ) : ?>
				<td class="product-add-to-cart">
					<?php
					if ( $product->is_in_stock() && ( $product->is_purchasable() || $product->is_type( 'external' ) ) ) {
						woocommerce_template_loop_add_to_cart();
					}
					?>
				</td>
			<?php endif; ?>
		</tr>
	<?php endforeach; ?>
</table>

<?php do_action( 'soo_wishlist_after_list', $list ); ?>

<?php
wc_setup_product_data( $post );