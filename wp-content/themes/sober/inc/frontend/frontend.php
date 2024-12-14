<?php
/**
 * Custom functions that act independently of the theme templates.
 *
 * Eventually, some of the functionality here could be replaced by core features.
 *
 * @package Sober
 */

/**
 * Adds custom classes to the array of body classes.
 *
 * @param array $classes Classes for the body element.
 *
 * @return array
 */
function sober_body_classes( $classes ) {
	// Adds a class of group-blog to blogs with more than 1 published author.
	if ( is_multi_author() ) {
		$classes[] = 'group-blog';
	}

	// Adds a class of layout style
	$classes[] = sober_get_option( 'layout_style' );

	// Adds a class of layout
	$classes[] = 'sidebar-' . sober_get_layout();

	// Adds a class of hfeed to non-singular pages.
	if ( ! is_singular() ) {
		$classes[] = 'hfeed';
	}

	// Adds classes of topbar
	if ( sober_get_option( 'topbar_enable' ) ) {
		$classes[] = 'topbar-enabled';
		$classes[] = 'topbar-' . sober_get_option( 'topbar_color' );

		if ( sober_get_option( 'mobile_topbar' ) ) {
			$classes[] = 'topbar-mobile-enabled mobile-topbar';
		} else {
			$classes[] = 'topbar-mobile-disabled';
		}
	} else {
		$classes[] = 'topbar-disabled';
	}

	// Adds a class of header layout
	$classes[] = 'header-' . sober_get_option( 'header_layout' );

	// Adds a class for header sticky
	$sticky = sober_get_option( 'header_sticky' );
	if ( $sticky && 'none' != $sticky ) {
		$classes[] = 'header-sticky';
		$classes[] = 'header-sticky-' . $sticky;
	}

	// Adds classes of header background.
	$page_header_style    = sober_get_option( 'page_header_style' );
	$default_header_bg    = sober_get_option( 'header_bg' );
	$default_header_color = sober_get_option( 'header_text_color' );
	$page_id              = sober_get_current_page_id();

	if ( sober_has_page_header() ) {
		$header_bg    = $default_header_bg;
		$header_color = $default_header_color;

		if ( $page_id ) {
			$header_bg    = get_post_meta( $page_id, 'site_header_bg', true );
			$header_color = get_post_meta( $page_id, 'site_header_text_color', true );
			$header_color = in_array( $header_bg, array( 'transparent', 'custom' ) ) ? $header_color : $default_header_color;
			$header_bg    = $header_bg ? $header_bg : $default_header_bg;
			$header_bg    = 'transparent' == $header_bg && function_exists( 'is_checkout' ) && is_checkout() ? 'white' : $header_bg;
		}

		$header_bg    = 'minimal' == $page_header_style && 'transparent' == $header_bg ? 'white' : $header_bg;
		$header_color = 'dark' == $header_bg ? 'light' : ( 'white' == $header_bg ? 'dark' : $header_color );

		$classes[] = 'header-' . $header_bg;
		$classes[] = 'header-text-' . $header_color;
	} else {
		$header_bg    = 'transparent' == $default_header_bg ? 'white' : $default_header_bg;
		$header_color = $default_header_color;

		if ( is_page_template( 'templates/full-screen.php' ) ) {
			$header_bg    = 'transparent';
			$header_color = get_post_meta( get_the_ID(), 'fullscreen_header_text_color', true );
			$header_color = $header_color ? $header_color: $default_header_color;
		} elseif ( function_exists( 'is_product' ) && is_product() && 'style-5' == sober_get_option( 'single_product_style' ) ) {
			$header_bg    = 'transparent';
			$header_color = 'dark';
		} elseif ( $page_id ) {
			$page_header_bg    = get_post_meta( $page_id, 'site_header_bg', true );
			$header_bg         = $page_header_bg ? $page_header_bg : $header_bg;
			$header_bg         = 'transparent' != $header_bg ? $header_bg : ( is_page() ? $header_bg : 'white' );
			$page_header_color = get_post_meta( $page_id, 'site_header_text_color', true );
			$header_color      = in_array( $header_bg, array( 'transparent', 'custom' ) ) ? $page_header_color : $default_header_color;
		}

		$header_color = 'dark' == $header_bg ? 'light' : ( 'white' == $header_bg ? 'dark' : $header_color );

		$classes[] = 'header-' . $header_bg;
		$classes[] = 'header-text-' . $header_color;
	}

	// Adds a class for header hover effect.
	if ( sober_get_option( 'header_hover' ) ) {
		$classes[] = 'header-hoverable';
	}

	// Adds classes of page header.
	$classes[] = sober_has_page_header() ? 'has-page-header' : 'no-page-header';
	$classes[] = 'page-header-style-' . $page_header_style;

	// Adds a class of page header text color.
	if ( 'minimal' != $page_header_style && sober_has_page_header() ) {
		$classes[] = sober_get_page_header_image() ? 'page-header-image' : 'page-header-color';

		if ( function_exists( 'is_checkout' ) && is_checkout() ) {
			$color = 'dark';
		} elseif ( is_singular() ) {
			$color = get_post_meta( get_the_ID(), 'page_header_text_color', true );
			$color = $color ? $color : sober_get_option( 'page_header_text_color' );
		} elseif ( function_exists( 'is_woocommerce' ) && is_woocommerce() ) {
			$color = sober_get_option( 'shop_page_header_text_color' );

			if ( is_shop() ) {
				$shop_page_id = wc_get_page_id( 'shop' );
				$shop_color   = get_post_meta( $shop_page_id, 'page_header_text_color', true );
				$color        = $shop_color ? $shop_color : $color;
			}
		} elseif ( is_post_type_archive( 'portfolio' ) || is_tax( 'portfolio_type' ) ) {
			// This is masonry style because we checked has_page_header before
			$color = sober_get_option( 'portfolio_page_header_text_color' );
		} elseif ( is_home() && ! is_front_page() ) {
			$posts_page_id = get_option( 'page_for_posts' );

			if ( $posts_page_id ) {
				$color = get_post_meta( $posts_page_id, 'page_header_text_color', true );
				$color = $color ? $color : sober_get_option( 'page_header_text_color' );
			} else {
				$color = sober_get_option( 'page_header_text_color' );
			}
		} else {
			$color = sober_get_option( 'page_header_text_color' );
		}

		// Double check for taxonomy page.
		if ( is_tax() || is_category() || is_tag() ) {
			$term_id    = get_queried_object_id();
			$term_color = get_term_meta( $term_id, 'page_header_text_color', true );
			$color      = $term_color ? $term_color : $color;
		}

		$classes[] = 'page-header-text-' . $color;
	}

	// Adds a class for hidden page title
	if ( is_page() && get_post_meta( get_the_ID(), 'hide_page_title', true ) ) {
		$classes[] = 'page-title-hidden';
	}

	// Adds a class of product hover image
	if ( ! in_array( sober_get_option( 'products_item_style' ), array( 'slider', 'zoom' ) ) && sober_get_option( 'product_hover_thumbnail' ) ) {
		$classes[] = 'shop-hover-thumbnail';
	}

	// Adds a class of product quick view
	if ( sober_get_option( 'product_quickview' ) ) {
		$classes[] = 'product-quickview-enable';
	}

	// Adds a class of order tracking page
	if ( sober_is_order_tracking_page() ) {
		$classes[] = 'woocommerce-page woocommerce-order-tracking';
	}

	// Add a class of blog layout
	if ( is_search() ) {
		$classes[] = 'blog-classic';
	} else {
		$classes[] = 'blog-' . sober_get_option( 'blog_layout' );
	}

	// Adds a class of single product layout
	if ( is_singular( array( 'product' ) ) ) {
		$classes[] = 'product-' . sober_get_option( 'single_product_style' );
	}

	// Adds a class of shop navigation type
	$classes[] = 'shop-navigation-' . sober_get_option( 'shop_nav_type' );

	if ( is_post_type_archive( 'portfolio' ) || is_tax( 'portfolio_type' ) ) {
		$classes[] = 'portfolio-' . sober_get_option( 'portfolio_style' );
	}

	// Adds a class for showing product buttons of shop page on mobile
	if ( sober_get_option( 'mobile_shop_add_to_cart' ) ) {
		$classes[] = 'mobile-shop-buttons';
	}

	// Add classes of no-header and no-footer.
	if ( is_page_template( 'templates/full-screen.php' ) ) {
		if ( get_post_meta( get_the_ID(), 'fullscreen_no_header', true ) ) {
			$classes[] = 'no-site-header';
		}

		if ( get_post_meta( get_the_ID(), 'fullscreen_no_footer', true ) ) {
			$classes[] = 'no-site-footer';
		}

		$footer_color = get_post_meta( get_the_ID(), 'fullscreen_footer_text_color', true );

		if ( $footer_color ) {
			$classes[] = 'footer-text-' . $footer_color;
		}
	} elseif ( is_page() ) {
		if ( get_post_meta( get_the_ID(), 'no_header', true ) ) {
			$classes[] = 'no-site-header';
		}

		if ( get_post_meta( get_the_ID(), 'no_footer', true ) ) {
			$classes[] = 'no-site-footer';
		}
	}

	// Add a class of maintenance mode.
	if ( sober_get_option( 'maintenance_enable' ) ) {
		$classes[] = 'maintenance-mode';
	}

	if ( function_exists( 'is_checkout' ) && is_checkout() ) {
		$classes[] = 'woocommerce-checkout--' . sober_get_option( 'checkout_layout' );
	}

	return $classes;
}

add_filter( 'body_class', 'sober_body_classes' );

/**
 * Enqueues front-end CSS for theme customization
 */
function sober_customize_css() {
	$css = '';

	// Typogarphy
	$css .= sober_typography_css();

	// Page header image
	if ( $image = sober_get_page_header_image() ) {
		$css .= '.page-header { background-image: url(' . esc_url( $image ) . '); }';
	}

	// 404 Background image
	if ( is_404() ) {
		$image = sober_get_option( '404_bg' );

		if ( $image ) {
			$css .= 'body.error404 { background-image: url( ' . esc_url( $image ) . '); }';
		}
	}

	// Logo width
	$logo_width  = sober_get_option( 'logo_width' );
	$logo_height = sober_get_option( 'logo_height' );

	if ( $logo_width > 0 || $logo_height > 0 ) {
		$logo_css = '';

		if ( $logo_width ) {
			$logo_css .= 'width: ' . esc_attr( $logo_width ) . 'px;';
		}

		if ( $logo_height ) {
			$logo_css .= 'height: ' . esc_attr( $logo_height ) . 'px;';
		}

		$css .= '.site-branding .logo img {' . $logo_css . '}';
	}

	// Logo margin
	$logo_margin = sober_get_option( 'logo_position' );
	$logo_margin = array_filter( $logo_margin );

	if ( $logo_margin ) {
		$logo_css = '';

		foreach( $logo_margin as $pos => $value ) {
			$logo_css .= 'margin-' . $pos . ': ' . esc_attr( $value ) . ';';
		}

		$css .= '.site-branding .logo {' . $logo_css . '}';
	}

	// Preloader
	if ( sober_get_option( 'preloader' ) ) {
		$color = sober_get_option( 'preloader_background_color' );

		$css .= $color ? '.preloader { background-color: ' . $color . '; }' : '';
	}

	// Popup
	$css .= '.sober-popup.popup-layout-fullscreen, .sober-popup-backdrop {background-color: ' . esc_attr( sober_get_option( 'popup_overlay_color' ) ) . '; }';

	// Page content spacings
	if ( is_page() && ! is_page_template( 'templates/homepage.php' ) ) {
		$top_spacing    = get_post_meta( get_the_ID(), 'top_spacing', true );
		$bottom_spacing = get_post_meta( get_the_ID(), 'bottom_spacing', true );

		if ( 'none' == $top_spacing ) {
			$css .= '.site-content { padding-top: 0 !important; }';
		} elseif ( 'custom' == $top_spacing && ( $top = get_post_meta( get_the_ID(), 'top_padding', true ) ) ) {
			$css .= '.site-content { padding-top: ' . $top . ' !important; }';
		}

		if ( 'none' == $bottom_spacing ) {
			$css .= '.site-content { padding-bottom: 0 !important; }';
		} elseif ( 'custom' == $bottom_spacing && ( $bottom = get_post_meta( get_the_ID(), 'bottom_padding', true ) ) ) {
			$css .= '.site-content { padding-bottom: ' . $bottom . ' !important; }';
		}
	}

	// Product badges
	if ( ( $color = sober_get_option( 'product_badge_sale_bg' ) ) ) {
		$css .= '.woocommerce .ribbons .onsale {background-color: ' . $color . '}';
	}

	if ( ( $color = sober_get_option( 'product_badge_new_bg' ) ) ) {
		$css .= '.woocommerce .ribbons .newness {background-color: ' . $color . '}';
	}

	if ( ( $color = sober_get_option( 'product_badge_featured_bg' ) ) ) {
		$css .= '.woocommerce .ribbons .featured {background-color: ' . $color . '}';
	}

	if ( ( $color = sober_get_option( 'product_badge_soldout_bg' ) ) ) {
		$css .= '.woocommerce .ribbons .sold-out {background-color: ' . $color . '}';
	}

	// Product background.
	if ( 'style-5' == sober_get_option( 'single_product_style' ) && is_singular( array( 'product' ) ) ) {
		$background = get_post_meta( get_the_ID(), 'background_color', true );

		if ( $background ) {
			$css .= '.woocommerce div.product .product-summary { background-color: ' . esc_attr( $background ) . ' }';
		}
	}

	if ( is_page_template( 'templates/full-screen.php' ) ) {
		$background = get_post_meta( get_the_ID(), 'fullscreen_background', true );

		$background_css = '';

		if ( ! empty( $background['color'] ) ) {
			$background_css .= 'background-color: ' . esc_attr( $background['color'] ) . ';';
		}

		if ( ! empty( $background['image'] ) ) {
			$background_css .= 'background-image: url(' . esc_url( $background['image'] ) . ');';
		}

		if ( ! empty( $background['repeat'] ) ) {
			$background_css .= 'background-repeat: ' . esc_attr( $background['repeat'] ) . ';';
		}

		if ( ! empty( $background['attachment'] ) ) {
			$background_css .= 'background-attachment: ' . esc_attr( $background['attachment'] ) . ';';
		}

		if ( ! empty( $background['position'] ) ) {
			$background_css .= 'background-position: ' . esc_attr( $background['position'] ) . ';';
		}

		if ( ! empty( $background['size'] ) ) {
			$background_css .= 'background-size: ' . esc_attr( $background['size'] ) . ';';
		}

		if ( ! empty( $background_css ) ) {
			$css .= '.page-template-full-screen {' . $background_css . '}';
		}
	}

	$page_id = sober_get_current_page_id();

	// Footer Background
	if ( $page_id && 'custom' == get_post_meta( $page_id, 'footer_background', true ) ) {
		$footer_background = get_post_meta( $page_id, 'footer_background_color', true );
	} elseif ( 'custom' == sober_get_option( 'footer_background' ) ) {
		$footer_background = sober_get_option( 'footer_background_color' );
	}

	if ( ! empty( $footer_background ) ) {
		$css .= '.site-footer.custom { background-color: ' . esc_attr( $footer_background ) . '; }';
		unset( $footer_background );
	}

	// Header Background
	if ( $page_id && 'custom' == get_post_meta( $page_id, 'site_header_bg', true ) ) {
		$header_bg_color = get_post_meta( $page_id, 'header_background_color', true );
	} elseif ( 'custom' == sober_get_option( 'header_bg' ) ) {
		$header_bg_color = sober_get_option( 'header_background_color' );
	}

	if ( ! empty( $header_bg_color ) ) {
		$css .= '
			.header-custom .site-header,
			.header-custom.header-sticky-smart .site-header.headroom--not-top
			{ background-color: ' . esc_attr( $header_bg_color ) . '; }
			';
		unset( $header_bg_color );
	}

	wp_add_inline_style( 'sober', $css );
}

add_action( 'wp_enqueue_scripts', 'sober_customize_css', 20 );

/**
 * Open content container
 */
function sober_open_content_container() {
	$class = 'container';

	if (
		( function_exists( 'WC' ) && ( is_shop() || is_product_taxonomy() || is_product() ) ) ||
		is_page_template( 'templates/homepage.php' ) ||
		is_page_template( 'templates/full-width.php' ) ||
		( ( is_post_type_archive( 'portfolio' ) || is_tax( 'portfolio_type' ) ) && 'fullwidth' == sober_get_option( 'portfolio_style' ) )
	) {
		$class = 'sober-container';
	} elseif ( is_page_template( 'templates/full-screen.php' ) ) {
		$container = get_post_meta( get_the_ID(), 'fullscreen_content_container', true );

		if ( 'fullwidth' == $container ) {
			$class = 'sober-container';
		}
	}

	$class = apply_filters( 'sober_site_content_container_class', $class );

	echo '<div class="' . $class . '">';

	if ( 'no-sidebar' != sober_get_layout() ) {
		echo '<div class="row">';
	}
}

add_action( 'sober_before_content', 'sober_open_content_container', 5 );

/**
 * Close content container
 */
function sober_close_content_container() {
	echo '</div>';

	if ( 'no-sidebar' != sober_get_layout() ) {
		echo '</div>';
	}
}

add_action( 'sober_after_content', 'sober_close_content_container', 50 );

/**
 * Add icon list as svg at the footer
 * It is hidden
 */
function sober_include_shadow_icons() {
	echo '<div id="svg-defs" class="svg-defs hidden">';
	include get_template_directory() . '/images/sprite.svg';
	echo '</div>';
}

add_action( 'sober_before_site', 'sober_include_shadow_icons' );

/**
 * Allow SVG in kses function with context "post".
 *
 * @param array $tags
 * @param string $context
 * @return array
 */
function sober_kses_allow_svg( $tags, $context ) {
	if ( 'post' != $context ) {
		return $tags;
	}

	$tags['svg'] = array(
		'width' => true,
		'height' => true,
		'viewBox' => true,
		'class' => true,
		'style' => true,
		'aria-hidden' => true,
	);

	$tags['use'] = array(
		'xlink:href' => true,
		'href' => true,
	);

	return $tags;
}

add_filter( 'wp_kses_allowed_html', 'sober_kses_allow_svg', 10, 2 );

/**
 * Remove site header and footer
 */
function sober_remove_header_and_footer() {
	if ( ! is_page() ) {
		return;
	}

	if ( is_page_template( 'templates/full-screen.php' ) ) {
		if ( get_post_meta( get_the_ID(), 'fullscreen_no_header', true ) ) {
			remove_all_actions( 'sober_header' );
		}

		if ( get_post_meta( get_the_ID(), 'fullscreen_no_footer', true ) ) {
			remove_all_actions( 'sober_footer' );
		}
	} else {
		if ( get_post_meta( get_the_ID(), 'no_header', true ) ) {
			remove_all_actions( 'sober_header' );
		}

		if ( get_post_meta( get_the_ID(), 'no_footer', true ) ) {
			remove_all_actions( 'sober_footer' );
		}
	}
}

add_action( 'template_redirect', 'sober_remove_header_and_footer', 100 );

/**
 * Redirect to the target page if the maintenance mode is enabled.
 */
function sober_maintenance_redirect() {
	if ( ! sober_get_option( 'maintenance_enable' ) ) {
		return;
	}

	if ( current_user_can( 'super admin' ) || current_user_can( 'administrator' ) ) {
		return;
	}

	$mode     = sober_get_option( 'maintenance_mode' );
	$page_id  = sober_get_option( 'maintenance_page' );
	$code     = 'maintenance' == $mode ? 503 : 200;
	$page_url = $page_id ? get_page_link( $page_id ) : '';

	// Use default message.
	if ( ! $page_id || ! $page_url ) {
		if ( 'coming_soon' == $mode ) {
			$message = sprintf( '<h1>%s</h1><p>%s</p>', esc_html__( 'Coming Soon', 'sober' ), esc_html__( 'Our website is under construction. We will be here soon with our new awesome site.', 'sober' ) );
		} else {
			$message = sprintf( '<h1>%s</h1><p>%s</p>', esc_html__( 'Website Under Maintenance', 'sober' ), esc_html__( 'Our website is currently undergoing scheduled maintenance. Please check back soon.', 'sober' ) );
		}

		wp_die( $message, get_bloginfo( 'name' ), array( 'response' => $code ) );
	}

	// Additional check for special page
	$is_page = is_page( $page_id );

	if ( get_option( 'show_on_front' ) == 'page' && $page_id == get_option( 'page_for_posts' ) ) {
		$is_page = $is_page || ( is_home() && ! is_front_page() );
	}

	if ( get_option( 'sober_portfolio' ) && $page_id == get_option( 'sober_portfolio_page_id' ) ) {
		$is_page = $is_page || is_post_type_archive( 'portfolio' );
	}

	if ( class_exists( 'WooCommerce' ) && $page_id == wc_get_page_id( 'shop' ) ) {
		$is_page = $is_page || is_shop();
	}

	// Redirect to the correct page.
	if ( ! $is_page ) {
		wp_redirect( $page_url );
		exit;
	} else {
		if ( ! headers_sent() ) {
			status_header( $code );
		}

		remove_all_actions( 'sober_before_header' );
		remove_all_actions( 'sober_header' );
		remove_all_actions( 'sober_after_header' );

		remove_all_actions( 'sober_before_footer' );
		remove_all_actions( 'sober_footer' );
		remove_all_actions( 'sober_after_footer' );
	}
}

add_action( 'template_redirect', 'sober_maintenance_redirect', 1 );

/**
 * Change markup of archive and category widget to include .count for post count
 *
 * @param string $output
 *
 * @return string
 */
function sober_widget_archive_count( $output ) {
	$output = preg_replace( '|\((\d+)\)|', '<span class="count">\\1</span>', $output );

	return $output;
}

add_filter( 'wp_list_categories', 'sober_widget_archive_count' );
add_filter( 'get_archives_link', 'sober_widget_archive_count' );

/**
 * Support Polylang search URL.
 *
 * @param string $url
 * @return string
 */
function sober_search_form_action_url( $url ) {
	global $polylang;

	if ( $polylang && $polylang->curlang ) {
		$url = $polylang->curlang->search_url;
	}

	return $url;
}

add_filter( 'sober_search_form_action', 'sober_search_form_action_url' );
