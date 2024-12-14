<?php
/**
 * Template for displaying the add-to-wishlist button on the catalog page.
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/loop/add-to-wishlist.php.
 *
 * @author  WCBoost
 * @package WCBoost\Wishlist\Templates
 * @version 1.1.5
 */

defined( 'ABSPATH' ) || exit;

global $product;

echo apply_filters( // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
	'wcboost_wishlist_loop_add_to_wishlist_link',
	sprintf(
		'<a href="%s" class="%s" %s>' .
			( ! empty( $args['icon'] ) ? '<span class="wcboost-wishlist-button__icon">' . $args['icon'] . '</span>' : '' ) .
			'<span class="wcboost-wishlist-button__text">%s</span>' .
		'</a>',
		esc_url( isset( $args['url'] ) ? $args['url'] : add_query_arg( [ 'add-to-wishlist' => $product->get_id() ] ) ),
		esc_attr( isset( $args['class'] ) ? $args['class'] : 'wcboost-wishlist-button button' ),
		isset( $args['attributes'] ) ? wc_implode_html_attributes( $args['attributes'] ) : '',
		isset( $args['label'] ) ? esc_html( $args['label'] ) : esc_html__( 'Add to wishlist', 'wcboost-wishlist' )
	),
	$args
);
