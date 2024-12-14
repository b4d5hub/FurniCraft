<?php
/**
 * Single Product Thumbnails
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/single-product/product-thumbnails.php.
 *
 * HOWEVER, on occasion WooCommerce will need to update template files and you
 * (the theme developer) will need to copy the new files to your theme to
 * maintain compatibility. We try to do this as little as possible, but it does
 * happen. When this occurs the version of the template file will be bumped and
 * the readme will list any important changes.
 *
 * @see         https://docs.woocommerce.com/document/template-structure/
 * @package     WooCommerce/Templates
 * @version     3.5.1
 */

defined( 'ABSPATH' ) || exit;

global $product;

$attachment_ids = method_exists( $product, 'get_gallery_image_ids' ) ? $product->get_gallery_image_ids() : $product->get_gallery_attachment_ids();

if ( $attachment_ids && has_post_thumbnail() ) {
	array_unshift( $attachment_ids, get_post_thumbnail_id() );
	$video_position = get_post_meta( $product->get_id(), 'video_position', true );

	echo '<div class="thumbnails">';

	echo 'first' == $video_position ? sober_get_product_video_thumbnail() : '';

	foreach ( $attachment_ids as $index => $attachment_id ) {
		$full_size       = apply_filters( 'woocommerce_gallery_full_size', apply_filters( 'woocommerce_product_thumbnails_large_size', 'full' ) );
		$full_size_image = wp_get_attachment_image_src( $attachment_id, $full_size );
		$thumbnail       = wp_get_attachment_image_src( $attachment_id, 'woocommerce_gallery_thumbnail' );
		$thumbnail_2x    = wp_get_attachment_image_src( $attachment_id, 'sober_wc_gallery_thumbnail' );

		$active = false;

		if ( ( ! sober_product_has_video() || $video_position != 'first' ) && ! $index ) {
			$active = true;
		}

		$image_attr = array(
			'src'        => $thumbnail[0],
			'data-o_src' => $thumbnail[0],
			'srcset'     => $thumbnail_2x[0] . ' 2x',
		);

		$image_attr = array_map( 'esc_attr', $image_attr );

		$html  = '<div class="woocommerce-product-gallery__image '. ( $active ? 'active' : '' ) .'"><a href="' . esc_url( $full_size_image[0] ) . '" data-elementorOpenLightbox="no">';
		$html .= '<img';
		foreach ( $image_attr as $name => $value ) {
			$html .= " $name=" . '"' . $value . '"';
		}
		$html .= ' />';
		$html .= '</a></div>';

		echo apply_filters( 'woocommerce_single_product_image_thumbnail_html', $html, $attachment_id );
	}

	echo 'last' == $video_position ? sober_get_product_video_thumbnail() : '';

	echo '</div>';
}
