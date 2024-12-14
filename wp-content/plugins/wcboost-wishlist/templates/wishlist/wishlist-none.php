<?php
/**
 * Template for displaying the notice of invalid wishlist.
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/wishlist/wishlist-none.php.
 *
 * @author  WCBoost
 * @package WCBoost\Wishlist\Templates
 * @version 1.0
 */

defined( 'ABSPATH' ) || exit;

do_action( 'wcboost_wishlist_before_wishlist' ); ?>

<div class="wishlist-none">
	<?php
	wc_print_notice( __( 'This wishlist is not available.', 'wcboost-wishlist' ), 'notice' );
	?>
</div>

<?php do_action( 'wcboost_wishlist_after_wishlist' ); ?>
