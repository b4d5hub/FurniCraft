<?php
/**
 * Template for displaying the add-to-wishlist button on the single product page.
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/single-product/add-to-wishlist.php.
 *
 * @author  WCBoost
 * @package WCBoost\Wishlist\Templates
 * @version 1.1.5
 */

defined( 'ABSPATH' ) || exit;

global $product;

echo apply_filters( // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
	'wcboost_wishlist_single_add_to_wishlist_link',
	sprintf(
		'<a href="%s" class="%s" %s>' .
			( empty( $icon ) ? '' : '<span class="wcboost-wishlist-button__icon">' . $icon . '</span>' ) .
			'<span class="wcboost-wishlist-button__text">%s</span>' .
		'</a>',
		esc_url( isset( $args['url'] ) ? $args['url'] : add_query_arg( [ 'add-to-wishlist' => $product->get_id() ] ) ),
		esc_attr( isset( $args['class'] ) ? $args['class'] : 'wcboost-wishlist-single-button wcboost-wishlist-button button' ),
		isset( $args['attributes'] ) ? wc_implode_html_attributes( $args['attributes'] ) : '',
		esc_html( isset( $args['label'] ) ? $args['label'] : __( 'Add to wishlist', 'wcboost-wishlist' ) )
	),
	$args
);
