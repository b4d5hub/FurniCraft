<?php
/**
 * Template for displaying the table of compared products.
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/compare/compare-table.php.
 *
 * @author  WCBoost
 * @package WCBoost\ProductsCompare\Templates
 * @version 1.0.4
 */

defined( 'ABSPATH' ) || exit;

if ( ! isset( $compare_items ) ) {
	return;
}
?>

<?php do_action( 'wcboost_products_compare_before_compare_table', $compare_items ); ?>

<div class="wcboost-products-compare__table">
	<table class="shop_table compare_table" cellspacing="0">
		<tbody>
			<?php foreach ( $compare_fields as $field => $field_name ) : ?>
				<tr class="product-<?php echo esc_attr( $field ); ?>" data-title="<?php echo esc_attr( $field_name ); ?>">
					<th>
						<?php echo wp_kses_post( apply_filters( 'wcboost_products_compare_field_label', $field_name, $field ) ); ?>
					</th>
					<?php foreach ( $compare_items as $item_key => $_product ) : ?>
						<td>
							<?php do_action( 'wcboost_products_compare_field_content', $field, $_product, $item_key ); ?>
						</td>
					<?php endforeach; ?>
				</tr>
			<?php endforeach; ?>
		</tbody>
	</table>
</div>

<?php do_action( 'wcboost_products_compare_after_compare_table', $compare_items ); ?>
