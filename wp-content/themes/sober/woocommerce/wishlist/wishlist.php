<?php
/**
 * Template for displaying wishlist.
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/wishlist/wishlist.php.
 *
 * @author  WCBoost
 * @package WCBoost\Wishlist\Templates
 * @version 1.0
 */
?>

<?php if ( isset( $wishlist ) && class_exists( '\WCBoost\Wishlist\Plugin' ) ) : // WCBoost wishlist ?>

	<?php do_action( 'wcboost_wishlist_before_wishlist', $wishlist ); ?>

	<form class="wcboost-wishlist-form" action="<?php echo esc_url( wc_get_page_permalink( 'wishlist' ) ); ?>" method="post">

		<?php do_action( 'wcboost_wishlist_main_content', $wishlist ); ?>

	</form>

	<?php do_action( 'wcboost_wishlist_after_wishlist', $wishlist ); ?>

<?php elseif ( isset( $list ) && class_exists( 'Soo_Wishlist_Plugin' ) ) : ?>
	<?php include 'soo-wishlist.php'; ?>
<?php endif;
