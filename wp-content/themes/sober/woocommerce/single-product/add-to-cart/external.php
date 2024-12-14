<?php
/**
 * External product add to cart
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/single-product/add-to-cart/external.php.
 *
 * HOWEVER, on occasion WooCommerce will need to update template files and you
 * (the theme developer) will need to copy the new files to your theme to
 * maintain compatibility. We try to do this as little as possible, but it does
 * happen. When this occurs the version of the template file will be bumped and
 * the readme will list any important changes.
 *
 * @see https://docs.woocommerce.com/document/template-structure/
 * @package WooCommerce\Templates
 * @version 7.0.1
 */

defined( 'ABSPATH' ) || exit;

do_action( 'woocommerce_before_add_to_cart_form' ); ?>

<form class="cart" action="<?php echo esc_url( $product_url ); ?>" method="get">
	<?php do_action( 'woocommerce_before_add_to_cart_button' ); ?>

	<button type="submit" class="single_add_to_cart_button button alt">
		<?php sober_shopping_cart_icon(); ?>
		<?php echo esc_html( $button_text ); ?>
	</button>

	<?php wc_query_string_form_fields( $product_url ); ?>

	<?php do_action( 'woocommerce_after_add_to_cart_button' ); ?>
	<?php
	if ( class_exists( '\WCBoost\Wishlist\Plugin' ) ) {
		\WCBoost\Wishlist\Frontend::instance()->single_add_to_wishlist_button();
	} elseif ( shortcode_exists( 'add_to_wishlist' ) ) {
		echo do_shortcode( '[add_to_wishlist]' );
	}
	?>
</form>

<?php do_action( 'woocommerce_after_add_to_cart_form' ); ?>
