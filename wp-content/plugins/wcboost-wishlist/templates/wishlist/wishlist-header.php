<?php
/**
 * Template for displaying the wishlist header.
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/wishlist/wishlist-header.php.
 *
 * @author  WCBoost
 * @package WCBoost\Wishlist\Templates
 * @version 1.0
 */

defined( 'ABSPATH' ) || exit;

if ( empty( $wishlist ) || ! has_action( 'wcboost_wishlist_header' ) ) {
	return;
}
?>

<div class="wcboost-wishlist-header">

	<?php
	/**
	 * The hook 'wcboost_wishlist_header' is used to display the wishlist header.
	 *
	 * @hooked \WCBoost\Wishlist\Frontend::wishlist_title - 10
	 * @hooked \WCBoost\Wishlist\Frontend::wishlist_description - 20
	 */
	do_action( 'wcboost_wishlist_header', $wishlist );
	?>

</div>
