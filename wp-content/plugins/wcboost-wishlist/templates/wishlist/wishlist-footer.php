<?php
/**
 * Template for displaying the wishlist footer.
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/wishlist/wishlist-footer.php.
 *
 * @author  WCBoost
 * @package WCBoost\Wishlist\Templates
 * @version 1.0
 */

defined( 'ABSPATH' ) || exit;

if ( empty( $wishlist ) || ! has_action( 'wcboost_wishlist_footer' ) ) {
	return;
}
?>

<div class="wcboost-wishlist-footer">

	<?php
	/**
	 * The hook 'wcboost_wishlist_footer' is used to display the wishlist footer.
	 *
	 * @hooked \WCBoost\Wishlist\Frontend::share_buttons - 10
	 * @hooked \WCBoost\Wishlist\Frontend::link_edit_wishlist - 50
	 */
	do_action( 'wcboost_wishlist_footer', $wishlist );
	?>

</div>
