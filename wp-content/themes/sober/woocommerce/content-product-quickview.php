<?php
/**
 * Display product quickview.
 *
 * @author  UIX Themes
 * @package Sober
 * @version 1.0.0
 */
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

$GLOBALS['post'] = $post_object;
wc_setup_product_data( $post_object );
?>

<div class="product-quickview product-summary clearfix">
	<?php
	/**
	 * Hook: sober_woocommerce_before_product_quickview_summary
	 *
	 * @hooked Sober_WooCommerce::product_ribbons - 5
	 * @hooked woocommerce_show_product_images - 10
	 */
	do_action( 'sober_woocommerce_before_product_quickview_summary' );
	?>

	<div class="summary entry-summary">
		<?php
		/**
		 * Hook: sober_woocommerce_product_quickview_summary
		 *
		 * @hooked woocommerce_template_single_title - 10
		 * @hooked woocommerce_template_single_excerpt - 20
		 * @hooked woocommerce_template_single_rating - 30
		 * @hooked woocommerce_template_single_price - 40
		 * @hooked woocommerce_template_single_add_to_cart - 50
		 * @hooked woocommerce_template_single_meta - 60
		 */
		do_action( 'sober_woocommerce_product_quickview_summary' );
		?>
	</div>

	<?php
	/**
	 * Hook: sober_woocommerce_after_product_quickview_summary
	 */
	do_action( 'sober_woocommerce_after_product_quickview_summary' );
	?>
</div>

<?php
wp_reset_postdata();
wc_setup_product_data( $GLOBALS['post'] );
