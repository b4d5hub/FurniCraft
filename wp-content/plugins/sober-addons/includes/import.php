<?php
/**
 * Register one click import demo data
 */

add_filter( 'soo_demo_packages', 'sober_addons_import_register' );

function sober_addons_import_register() {
	$active_tab = 'Elementor';

	if ( defined( 'WPB_VC_VERSION' ) && ! defined( 'ELEMENTOR_VERSION' ) ) {
		$active_tab = 'WPBakery Page Builder';
	}

	$options = array(
		'woocommerce_single_image_width' => '937',
		'woocommerce_thumbnail_image_width' => '433',
		'woocommerce_thumbnail_cropping' => 'custom',
		'woocommerce_thumbnail_cropping_custom_width' => '73',
		'woocommerce_thumbnail_cropping_custom_height' => '87',
		'elementor_disable_typography_schemes' => 'yes',
		'elementor_disable_color_schemes' => 'yes',
	);

	$menus = array(
		'primary'   => 'primary-menu',
		'secondary' => 'secondary-menu',
		'topbar'    => 'topbar-menu',
		'footer'    => 'footer-menu',
		'socials'   => 'socials',
	);

	return array(
		'active_tab' => $active_tab,
		array(
			'name'       => 'Minimal',
			'content'    => 'https://uix.store/data/sober/wpbakery-page-builder/minimal/demo-content.xml',
			'widgets'    => 'https://uix.store/data/sober/wpbakery-page-builder/minimal/widgets.wie',
			'preview'    => 'https://uix.store/data/sober/wpbakery-page-builder/minimal/preview.jpg',
			'customizer' => 'https://uix.store/data/sober/wpbakery-page-builder/minimal/customizer.dat',
			'sliders'    => 'https://uix.store/data/sober/wpbakery-page-builder/minimal/sliders.zip',
			'tab'        => 'WPBakery Page Builder',
			'pages'      => array(
				'front_page' => 'Home v1',
				'blog'       => 'Blog',
				'shop'       => 'Shop',
				'cart'       => 'Cart',
				'checkout'   => 'Checkout',
				'my_account' => 'My Account',
				'order_tracking' => 'Order Tracking',
				'portfolio'  => 'Portfolio',
			),
			'menus'      => $menus,
			'options'    => $options,
		),
		array(
			'name'       => 'Modern',
			'content'    => 'https://uix.store/data/sober/wpbakery-page-builder/modern/demo-content.xml',
			'widgets'    => 'https://uix.store/data/sober/wpbakery-page-builder/modern/widgets.wie',
			'preview'    => 'https://uix.store/data/sober/wpbakery-page-builder/modern/preview.jpg',
			'customizer' => 'https://uix.store/data/sober/wpbakery-page-builder/modern/customizer.dat',
			'sliders'    => 'https://uix.store/data/sober/wpbakery-page-builder/modern/sliders.zip',
			'tab'        => 'WPBakery Page Builder',
			'pages'      => array(
				'front_page' => 'Home v2',
				'blog'       => 'Blog',
				'shop'       => 'Shop',
				'cart'       => 'Cart',
				'checkout'   => 'Checkout',
				'my_account' => 'My Account',
				'order_tracking' => 'Order Tracking',
				'portfolio'  => 'Portfolio',
			),
			'menus'      => $menus,
			'options'    => $options,
		),
		array(
			'name'       => 'Classic',
			'content'    => 'https://uix.store/data/sober/wpbakery-page-builder/classic/demo-content.xml',
			'widgets'    => 'https://uix.store/data/sober/wpbakery-page-builder/classic/widgets.wie',
			'preview'    => 'https://uix.store/data/sober/wpbakery-page-builder/classic/preview.jpg',
			'customizer' => 'https://uix.store/data/sober/wpbakery-page-builder/classic/customizer.dat',
			'sliders'    => 'https://uix.store/data/sober/wpbakery-page-builder/classic/sliders.zip',
			'tab'        => 'WPBakery Page Builder',
			'pages'      => array(
				'front_page' => 'Home v3',
				'blog'       => 'Blog',
				'shop'       => 'Shop',
				'cart'       => 'Cart',
				'checkout'   => 'Checkout',
				'my_account' => 'My Account',
				'order_tracking' => 'Order Tracking',
				'portfolio'  => 'Portfolio',
			),
			'menus'      => $menus,
			'options'    => $options,
		),
		array(
			'name'       => 'Clean',
			'content'    => 'https://uix.store/data/sober/wpbakery-page-builder/clean/demo-content.xml',
			'widgets'    => 'https://uix.store/data/sober/wpbakery-page-builder/clean/widgets.wie',
			'preview'    => 'https://uix.store/data/sober/wpbakery-page-builder/clean/preview.jpg',
			'customizer' => 'https://uix.store/data/sober/wpbakery-page-builder/clean/customizer.dat',
			'sliders'    => 'https://uix.store/data/sober/wpbakery-page-builder/clean/sliders.zip',
			'tab'        => 'WPBakery Page Builder',
			'pages'      => array(
				'front_page' => 'Home v4',
				'blog'       => 'Blog',
				'shop'       => 'Shop',
				'cart'       => 'Cart',
				'checkout'   => 'Checkout',
				'my_account' => 'My Account',
				'order_tracking' => 'Order Tracking',
				'portfolio'  => 'Portfolio',
			),
			'menus'      => $menus,
			'options'    => $options,
		),
		array(
			'name'       => 'Categories V1',
			'content'    => 'https://uix.store/data/sober/wpbakery-page-builder/categories/demo-content.xml',
			'widgets'    => 'https://uix.store/data/sober/wpbakery-page-builder/categories/widgets.wie',
			'preview'    => 'https://uix.store/data/sober/wpbakery-page-builder/categories/preview.jpg',
			'customizer' => 'https://uix.store/data/sober/wpbakery-page-builder/categories/customizer.dat',
			'sliders'    => 'https://uix.store/data/sober/wpbakery-page-builder/categories/sliders.zip',
			'tab'        => 'WPBakery Page Builder',
			'pages'      => array(
				'front_page' => 'Home v5',
				'blog'       => 'Blog',
				'shop'       => 'Shop',
				'cart'       => 'Cart',
				'checkout'   => 'Checkout',
				'my_account' => 'My Account',
				'order_tracking' => 'Order Tracking',
				'portfolio'  => 'Portfolio',
			),
			'menus'      => $menus,
			'options'    => $options,
		),
		array(
			'name'       => 'Categories V2',
			'content'    => 'https://uix.store/data/sober/wpbakery-page-builder/categories-2/demo-content.xml',
			'widgets'    => 'https://uix.store/data/sober/wpbakery-page-builder/categories-2/widgets.wie',
			'preview'    => 'https://uix.store/data/sober/wpbakery-page-builder/categories-2/preview.jpg',
			'customizer' => 'https://uix.store/data/sober/wpbakery-page-builder/categories-2/customizer.dat',
			'sliders'    => 'https://uix.store/data/sober/wpbakery-page-builder/categories-2/sliders.zip',
			'tab'        => 'WPBakery Page Builder',
			'pages'      => array(
				'front_page' => 'Home v6',
				'blog'       => 'Blog',
				'shop'       => 'Shop',
				'cart'       => 'Cart',
				'checkout'   => 'Checkout',
				'my_account' => 'My Account',
				'order_tracking' => 'Order Tracking',
				'portfolio'  => 'Portfolio',
			),
			'menus'      => $menus,
			'options'    => $options,
		),
		array(
			'name'       => 'Best Sellings',
			'content'    => 'https://uix.store/data/sober/wpbakery-page-builder/bestselling/demo-content.xml',
			'widgets'    => 'https://uix.store/data/sober/wpbakery-page-builder/bestselling/widgets.wie',
			'preview'    => 'https://uix.store/data/sober/wpbakery-page-builder/bestselling/preview.jpg',
			'customizer' => 'https://uix.store/data/sober/wpbakery-page-builder/bestselling/customizer.dat',
			'tab'        => 'WPBakery Page Builder',
			'pages'      => array(
				'front_page' => 'Home v7',
				'blog'       => 'Blog',
				'shop'       => 'Shop',
				'cart'       => 'Cart',
				'checkout'   => 'Checkout',
				'my_account' => 'My Account',
				'order_tracking' => 'Order Tracking',
				'portfolio'  => 'Portfolio',
			),
			'menus'      => $menus,
			'options'    => $options,
		),
		array(
			'name'       => 'Parallax',
			'content'    => 'https://uix.store/data/sober/wpbakery-page-builder/parallax/demo-content.xml',
			'widgets'    => 'https://uix.store/data/sober/wpbakery-page-builder/parallax/widgets.wie',
			'preview'    => 'https://uix.store/data/sober/wpbakery-page-builder/parallax/preview.jpg',
			'customizer' => 'https://uix.store/data/sober/wpbakery-page-builder/parallax/customizer.dat',
			'tab'        => 'WPBakery Page Builder',
			'pages'      => array(
				'front_page' => 'Home v8',
				'blog'       => 'Blog',
				'shop'       => 'Shop',
				'cart'       => 'Cart',
				'checkout'   => 'Checkout',
				'my_account' => 'My Account',
				'order_tracking' => 'Order Tracking',
				'portfolio'  => 'Portfolio',
			),
			'menus'      => $menus,
			'options'    => $options,
		),
		array(
			'name'       => 'Full Screen',
			'content'    => 'https://uix.store/data/sober/wpbakery-page-builder/fullscreen/demo-content.xml',
			'widgets'    => 'https://uix.store/data/sober/wpbakery-page-builder/fullscreen/widgets.wie',
			'preview'    => 'https://uix.store/data/sober/wpbakery-page-builder/fullscreen/preview.jpg',
			'customizer' => 'https://uix.store/data/sober/wpbakery-page-builder/fullscreen/customizer.dat',
			'sliders'    => 'https://uix.store/data/sober/wpbakery-page-builder/fullscreen/sliders.zip',
			'tab'        => 'WPBakery Page Builder',
			'pages'      => array(
				'front_page' => 'Home v9',
				'blog'       => 'Blog',
				'shop'       => 'Shop',
				'cart'       => 'Cart',
				'checkout'   => 'Checkout',
				'my_account' => 'My Account',
				'order_tracking' => 'Order Tracking',
				'portfolio'  => 'Portfolio',
			),
			'menus'      => $menus,
			'options'    => $options,
		),
		array(
			'name'       => 'Full Slider',
			'content'    => 'https://uix.store/data/sober/wpbakery-page-builder/fullslider/demo-content.xml',
			'widgets'    => 'https://uix.store/data/sober/wpbakery-page-builder/fullslider/widgets.wie',
			'preview'    => 'https://uix.store/data/sober/wpbakery-page-builder/fullslider/preview.jpg',
			'customizer' => 'https://uix.store/data/sober/wpbakery-page-builder/fullslider/customizer.dat',
			'sliders'    => 'https://uix.store/data/sober/wpbakery-page-builder/fullslider/sliders.zip',
			'tab'        => 'WPBakery Page Builder',
			'pages'      => array(
				'front_page' => 'Home v10',
				'blog'       => 'Blog',
				'shop'       => 'Shop',
				'cart'       => 'Cart',
				'checkout'   => 'Checkout',
				'my_account' => 'My Account',
				'order_tracking' => 'Order Tracking',
				'portfolio'  => 'Portfolio',
			),
			'menus'      => $menus,
			'options'    => $options,
		),
		array(
			'name'       => 'Furniture',
			'content'    => 'https://uix.store/data/sober/wpbakery-page-builder/furniture/demo-content.xml',
			'widgets'    => 'https://uix.store/data/sober/wpbakery-page-builder/furniture/widgets.wie',
			'preview'    => 'https://uix.store/data/sober/wpbakery-page-builder/furniture/preview.jpg',
			'customizer' => 'https://uix.store/data/sober/wpbakery-page-builder/furniture/customizer.dat',
			'sliders'    => 'https://uix.store/data/sober/wpbakery-page-builder/furniture/sliders.zip',
			'tab'        => 'WPBakery Page Builder',
			'pages'      => array(
				'front_page' => 'Home v11',
				'blog'       => 'Blog',
				'shop'       => 'Shop',
				'cart'       => 'Cart',
				'checkout'   => 'Checkout',
				'my_account' => 'My Account',
				'order_tracking' => 'Order Tracking',
			),
			'menus'      => array(
				'primary'   => 'main-menu',
				'footer'    => 'footer-menu',
				'socials'   => 'socials',
			),
			'options'    => $options,
		),
		array(
			'name'       => 'Furniture 2',
			'content'    => 'https://uix.store/data/sober/wpbakery-page-builder/furniture-2/demo-content.xml',
			'widgets'    => 'https://uix.store/data/sober/wpbakery-page-builder/furniture-2/widgets.wie',
			'preview'    => 'https://uix.store/data/sober/wpbakery-page-builder/furniture-2/preview.jpg',
			'customizer' => 'https://uix.store/data/sober/wpbakery-page-builder/furniture-2/customizer.dat',
			'sliders'    => 'https://uix.store/data/sober/wpbakery-page-builder/furniture-2/sliders.zip',
			'tab'        => 'WPBakery Page Builder',
			'pages'      => array(
				'front_page' => 'Home v12',
				'blog'       => 'Blog',
				'shop'       => 'Shop',
				'cart'       => 'Cart',
				'checkout'   => 'Checkout',
				'my_account' => 'My Account',
				'order_tracking' => 'Order Tracking',
			),
			'menus'      => array(
				'primary'   => 'main-menu',
				'footer'    => 'footer-menu',
				'socials'   => 'socials',
			),
			'options'    => $options,
		),
		array(
			'name'       => 'Furniture 3',
			'content'    => 'https://uix.store/data/sober/wpbakery-page-builder/furniture-3/demo-content.xml',
			'widgets'    => 'https://uix.store/data/sober/wpbakery-page-builder/furniture-3/widgets.wie',
			'preview'    => 'https://uix.store/data/sober/wpbakery-page-builder/furniture-3/preview.jpg',
			'customizer' => 'https://uix.store/data/sober/wpbakery-page-builder/furniture-3/customizer.dat',
			'sliders'    => 'https://uix.store/data/sober/wpbakery-page-builder/furniture-3/sliders.zip',
			'tab'        => 'WPBakery Page Builder',
			'pages'      => array(
				'front_page' => 'Home v13',
				'blog'       => 'Blog',
				'shop'       => 'Shop',
				'cart'       => 'Cart',
				'checkout'   => 'Checkout',
				'my_account' => 'My Account',
				'order_tracking' => 'Order Tracking',
			),
			'menus'      => array(
				'primary'   => 'main-menu',
				'footer'    => 'footer-menu',
				'socials'   => 'socials',
			),
			'options'    => $options,
		),
		array(
			'name'       => 'Furniture 4',
			'content'    => 'https://uix.store/data/sober/wpbakery-page-builder/furniture-4/demo-content.xml',
			'widgets'    => 'https://uix.store/data/sober/wpbakery-page-builder/furniture-4/widgets.wie',
			'preview'    => 'https://uix.store/data/sober/wpbakery-page-builder/furniture-4/preview.jpg',
			'customizer' => 'https://uix.store/data/sober/wpbakery-page-builder/furniture-4/customizer.dat',
			'sliders'    => 'https://uix.store/data/sober/wpbakery-page-builder/furniture-4/sliders.zip',
			'tab'        => 'WPBakery Page Builder',
			'pages'      => array(
				'front_page' => 'Home v14',
				'blog'       => 'Blog',
				'shop'       => 'Shop',
				'cart'       => 'Cart',
				'checkout'   => 'Checkout',
				'my_account' => 'My Account',
				'order_tracking' => 'Order Tracking',
			),
			'menus'      => array(
				'primary'   => 'main-menu',
				'footer'    => 'footer-menu',
				'socials'   => 'socials',
			),
			'options'    => $options,
		),
		array(
			'name'       => 'Furniture 5',
			'content'    => 'https://uix.store/data/sober/wpbakery-page-builder/furniture-5/demo-content.xml',
			'widgets'    => 'https://uix.store/data/sober/wpbakery-page-builder/furniture-5/widgets.wie',
			'preview'    => 'https://uix.store/data/sober/wpbakery-page-builder/furniture-5/preview.jpg',
			'customizer' => 'https://uix.store/data/sober/wpbakery-page-builder/furniture-5/customizer.dat',
			'sliders'    => 'https://uix.store/data/sober/wpbakery-page-builder/furniture-5/sliders.zip',
			'tab'        => 'WPBakery Page Builder',
			'pages'      => array(
				'front_page' => 'Home v15',
				'blog'       => 'Blog',
				'shop'       => 'Shop',
				'cart'       => 'Cart',
				'checkout'   => 'Checkout',
				'my_account' => 'My Account',
				'order_tracking' => 'Order Tracking',
			),
			'menus'      => array(
				'primary'   => 'main-menu',
				'footer'    => 'footer-menu',
				'socials'   => 'socials',
			),
			'options'    => $options,
		),
		array(
			'name'       => 'Furniture 6',
			'content'    => 'https://uix.store/data/sober/wpbakery-page-builder/furniture-6/demo-content.xml',
			'widgets'    => 'https://uix.store/data/sober/wpbakery-page-builder/furniture-6/widgets.wie',
			'preview'    => 'https://uix.store/data/sober/wpbakery-page-builder/furniture-6/preview.jpg',
			'customizer' => 'https://uix.store/data/sober/wpbakery-page-builder/furniture-6/customizer.dat',
			'sliders'    => 'https://uix.store/data/sober/wpbakery-page-builder/furniture-6/sliders.zip',
			'tab'        => 'WPBakery Page Builder',
			'pages'      => array(
				'front_page' => 'Home v16',
				'blog'       => 'Blog',
				'shop'       => 'Shop',
				'cart'       => 'Cart',
				'checkout'   => 'Checkout',
				'my_account' => 'My Account',
				'order_tracking' => 'Order Tracking',
			),
			'menus'      => array(
				'primary'   => 'main-menu',
				'footer'    => 'footer-menu',
				'socials'   => 'socials',
			),
			'options'    => $options,
		),
		// Elementor
		array(
			'name'       => 'Minimal',
			'content'    => 'https://uix.store/data/sober/elementor/minimal/demo-content.xml',
			'widgets'    => 'https://uix.store/data/sober/elementor/minimal/widgets.wie',
			'preview'    => 'https://uix.store/data/sober/elementor/minimal/preview.jpg',
			'customizer' => 'https://uix.store/data/sober/elementor/minimal/customizer.dat',
			'sliders'    => 'https://uix.store/data/sober/elementor/minimal/sliders.zip',
			'tab'        => 'Elementor',
			'pages'      => array(
				'front_page' => 'Home v1',
				'blog'       => 'Blog',
				'shop'       => 'Shop',
				'cart'       => 'Cart',
				'checkout'   => 'Checkout',
				'my_account' => 'My Account',
				'order_tracking' => 'Order Tracking',
				'portfolio'  => 'Portfolio',
			),
			'menus'      => $menus,
			'options'    => $options,
		),
		array(
			'name'       => 'Modern',
			'content'    => 'https://uix.store/data/sober/elementor/modern/demo-content.xml',
			'widgets'    => 'https://uix.store/data/sober/elementor/modern/widgets.wie',
			'preview'    => 'https://uix.store/data/sober/elementor/modern/preview.jpg',
			'customizer' => 'https://uix.store/data/sober/elementor/modern/customizer.dat',
			'sliders'    => 'https://uix.store/data/sober/elementor/modern/sliders.zip',
			'tab'        => 'Elementor',
			'pages'      => array(
				'front_page' => 'Home v2',
				'blog'       => 'Blog',
				'shop'       => 'Shop',
				'cart'       => 'Cart',
				'checkout'   => 'Checkout',
				'my_account' => 'My Account',
				'order_tracking' => 'Order Tracking',
				'portfolio'  => 'Portfolio',
			),
			'menus'      => $menus,
			'options'    => $options,
		),
		array(
			'name'       => 'Classic',
			'content'    => 'https://uix.store/data/sober/elementor/classic/demo-content.xml',
			'widgets'    => 'https://uix.store/data/sober/elementor/classic/widgets.wie',
			'preview'    => 'https://uix.store/data/sober/elementor/classic/preview.jpg',
			'customizer' => 'https://uix.store/data/sober/elementor/classic/customizer.dat',
			'sliders'    => 'https://uix.store/data/sober/elementor/classic/sliders.zip',
			'tab'        => 'Elementor',
			'pages'      => array(
				'front_page' => 'Home v3',
				'blog'       => 'Blog',
				'shop'       => 'Shop',
				'cart'       => 'Cart',
				'checkout'   => 'Checkout',
				'my_account' => 'My Account',
				'order_tracking' => 'Order Tracking',
				'portfolio'  => 'Portfolio',
			),
			'menus'      => $menus,
			'options'    => $options,
		),
		array(
			'name'       => 'Clean',
			'content'    => 'https://uix.store/data/sober/elementor/clean/demo-content.xml',
			'widgets'    => 'https://uix.store/data/sober/elementor/clean/widgets.wie',
			'preview'    => 'https://uix.store/data/sober/elementor/clean/preview.jpg',
			'customizer' => 'https://uix.store/data/sober/elementor/clean/customizer.dat',
			'sliders'    => 'https://uix.store/data/sober/elementor/clean/sliders.zip',
			'tab'        => 'Elementor',
			'pages'      => array(
				'front_page' => 'Home v4',
				'blog'       => 'Blog',
				'shop'       => 'Shop',
				'cart'       => 'Cart',
				'checkout'   => 'Checkout',
				'my_account' => 'My Account',
				'order_tracking' => 'Order Tracking',
				'portfolio'  => 'Portfolio',
			),
			'menus'      => $menus,
			'options'    => $options,
		),
		array(
			'name'       => 'Categories V1',
			'content'    => 'https://uix.store/data/sober/elementor/categories/demo-content.xml',
			'widgets'    => 'https://uix.store/data/sober/elementor/categories/widgets.wie',
			'preview'    => 'https://uix.store/data/sober/elementor/categories/preview.jpg',
			'customizer' => 'https://uix.store/data/sober/elementor/categories/customizer.dat',
			'sliders'    => 'https://uix.store/data/sober/elementor/categories/sliders.zip',
			'tab'        => 'Elementor',
			'pages'      => array(
				'front_page' => 'Home v5',
				'blog'       => 'Blog',
				'shop'       => 'Shop',
				'cart'       => 'Cart',
				'checkout'   => 'Checkout',
				'my_account' => 'My Account',
				'order_tracking' => 'Order Tracking',
				'portfolio'  => 'Portfolio',
			),
			'menus'      => $menus,
			'options'    => $options,
		),
		array(
			'name'       => 'Categories V2',
			'content'    => 'https://uix.store/data/sober/elementor/categories-2/demo-content.xml',
			'widgets'    => 'https://uix.store/data/sober/elementor/categories-2/widgets.wie',
			'preview'    => 'https://uix.store/data/sober/elementor/categories-2/preview.jpg',
			'customizer' => 'https://uix.store/data/sober/elementor/categories-2/customizer.dat',
			'sliders'    => 'https://uix.store/data/sober/elementor/categories-2/sliders.zip',
			'tab'        => 'Elementor',
			'pages'      => array(
				'front_page' => 'Home v6',
				'blog'       => 'Blog',
				'shop'       => 'Shop',
				'cart'       => 'Cart',
				'checkout'   => 'Checkout',
				'my_account' => 'My Account',
				'order_tracking' => 'Order Tracking',
				'portfolio'  => 'Portfolio',
			),
			'menus'      => $menus,
			'options'    => $options,
		),
		array(
			'name'       => 'Best Sellings',
			'content'    => 'https://uix.store/data/sober/elementor/bestselling/demo-content.xml',
			'widgets'    => 'https://uix.store/data/sober/elementor/bestselling/widgets.wie',
			'preview'    => 'https://uix.store/data/sober/elementor/bestselling/preview.jpg',
			'customizer' => 'https://uix.store/data/sober/elementor/bestselling/customizer.dat',
			'tab'        => 'Elementor',
			'pages'      => array(
				'front_page' => 'Home v7',
				'blog'       => 'Blog',
				'shop'       => 'Shop',
				'cart'       => 'Cart',
				'checkout'   => 'Checkout',
				'my_account' => 'My Account',
				'order_tracking' => 'Order Tracking',
				'portfolio'  => 'Portfolio',
			),
			'menus'      => $menus,
			'options'    => $options,
		),
		array(
			'name'       => 'Parallax',
			'content'    => 'https://uix.store/data/sober/elementor/parallax/demo-content.xml',
			'widgets'    => 'https://uix.store/data/sober/elementor/parallax/widgets.wie',
			'preview'    => 'https://uix.store/data/sober/elementor/parallax/preview.jpg',
			'customizer' => 'https://uix.store/data/sober/elementor/parallax/customizer.dat',
			'tab'        => 'Elementor',
			'pages'      => array(
				'front_page' => 'Home v8',
				'blog'       => 'Blog',
				'shop'       => 'Shop',
				'cart'       => 'Cart',
				'checkout'   => 'Checkout',
				'my_account' => 'My Account',
				'order_tracking' => 'Order Tracking',
				'portfolio'  => 'Portfolio',
			),
			'menus'      => $menus,
			'options'    => $options,
		),
		array(
			'name'       => 'Full Screen',
			'content'    => 'https://uix.store/data/sober/elementor/fullscreen/demo-content.xml',
			'widgets'    => 'https://uix.store/data/sober/elementor/fullscreen/widgets.wie',
			'preview'    => 'https://uix.store/data/sober/elementor/fullscreen/preview.jpg',
			'customizer' => 'https://uix.store/data/sober/elementor/fullscreen/customizer.dat',
			'sliders'    => 'https://uix.store/data/sober/elementor/fullscreen/sliders.zip',
			'tab'        => 'Elementor',
			'pages'      => array(
				'front_page' => 'Home v9',
				'blog'       => 'Blog',
				'shop'       => 'Shop',
				'cart'       => 'Cart',
				'checkout'   => 'Checkout',
				'my_account' => 'My Account',
				'order_tracking' => 'Order Tracking',
				'portfolio'  => 'Portfolio',
			),
			'menus'      => $menus,
			'options'    => $options,
		),
		array(
			'name'       => 'Full Slider',
			'content'    => 'https://uix.store/data/sober/elementor/fullslider/demo-content.xml',
			'widgets'    => 'https://uix.store/data/sober/elementor/fullslider/widgets.wie',
			'preview'    => 'https://uix.store/data/sober/elementor/fullslider/preview.jpg',
			'customizer' => 'https://uix.store/data/sober/elementor/fullslider/customizer.dat',
			'sliders'    => 'https://uix.store/data/sober/elementor/fullslider/sliders.zip',
			'tab'        => 'Elementor',
			'pages'      => array(
				'front_page' => 'Home v10',
				'blog'       => 'Blog',
				'shop'       => 'Shop',
				'cart'       => 'Cart',
				'checkout'   => 'Checkout',
				'my_account' => 'My Account',
				'order_tracking' => 'Order Tracking',
				'portfolio'  => 'Portfolio',
			),
			'menus'      => $menus,
			'options'    => $options,
		),
		array(
			'name'       => 'Furniture',
			'content'    => 'https://uix.store/data/sober/elementor/furniture/demo-content.xml',
			'widgets'    => 'https://uix.store/data/sober/elementor/furniture/widgets.wie',
			'preview'    => 'https://uix.store/data/sober/elementor/furniture/preview.jpg',
			'customizer' => 'https://uix.store/data/sober/elementor/furniture/customizer.dat',
			'sliders'    => 'https://uix.store/data/sober/elementor/furniture/sliders.zip',
			'tab'        => 'Elementor',
			'pages'      => array(
				'front_page' => 'Home v11',
				'blog'       => 'Blog',
				'shop'       => 'Shop',
				'cart'       => 'Cart',
				'checkout'   => 'Checkout',
				'my_account' => 'My Account',
				'order_tracking' => 'Order Tracking',
			),
			'menus'      => array(
				'primary'   => 'main-menu',
				'footer'    => 'footer-menu',
				'socials'   => 'socials',
			),
			'options'    => $options,
		),
		array(
			'name'       => 'Furniture 2',
			'content'    => 'https://uix.store/data/sober/elementor/furniture-2/demo-content.xml',
			'widgets'    => 'https://uix.store/data/sober/elementor/furniture-2/widgets.wie',
			'preview'    => 'https://uix.store/data/sober/elementor/furniture-2/preview.jpg',
			'customizer' => 'https://uix.store/data/sober/elementor/furniture-2/customizer.dat',
			'sliders'    => 'https://uix.store/data/sober/elementor/furniture-2/sliders.zip',
			'tab'        => 'Elementor',
			'pages'      => array(
				'front_page' => 'Home v12',
				'blog'       => 'Blog',
				'shop'       => 'Shop',
				'cart'       => 'Cart',
				'checkout'   => 'Checkout',
				'my_account' => 'My Account',
				'order_tracking' => 'Order Tracking',
			),
			'menus'      => array(
				'primary'   => 'main-menu',
				'footer'    => 'footer-menu',
				'socials'   => 'socials',
			),
			'options'    => $options,
		),
		array(
			'name'       => 'Furniture 3',
			'content'    => 'https://uix.store/data/sober/elementor/furniture-3/demo-content.xml',
			'widgets'    => 'https://uix.store/data/sober/elementor/furniture-3/widgets.wie',
			'preview'    => 'https://uix.store/data/sober/elementor/furniture-3/preview.jpg',
			'customizer' => 'https://uix.store/data/sober/elementor/furniture-3/customizer.dat',
			'sliders'    => 'https://uix.store/data/sober/elementor/furniture-3/sliders.zip',
			'tab'        => 'Elementor',
			'pages'      => array(
				'front_page' => 'Home v13',
				'blog'       => 'Blog',
				'shop'       => 'Shop',
				'cart'       => 'Cart',
				'checkout'   => 'Checkout',
				'my_account' => 'My Account',
				'order_tracking' => 'Order Tracking',
			),
			'menus'      => array(
				'primary'   => 'main-menu',
				'footer'    => 'footer-menu',
				'socials'   => 'socials',
			),
			'options'    => $options,
		),
		array(
			'name'       => 'Furniture 4',
			'content'    => 'https://uix.store/data/sober/elementor/furniture-4/demo-content.xml',
			'widgets'    => 'https://uix.store/data/sober/elementor/furniture-4/widgets.wie',
			'preview'    => 'https://uix.store/data/sober/elementor/furniture-4/preview.jpg',
			'customizer' => 'https://uix.store/data/sober/elementor/furniture-4/customizer.dat',
			'sliders'    => 'https://uix.store/data/sober/elementor/furniture-4/sliders.zip',
			'tab'        => 'Elementor',
			'pages'      => array(
				'front_page' => 'Home v14',
				'blog'       => 'Blog',
				'shop'       => 'Shop',
				'cart'       => 'Cart',
				'checkout'   => 'Checkout',
				'my_account' => 'My Account',
				'order_tracking' => 'Order Tracking',
			),
			'menus'      => array(
				'primary'   => 'main-menu',
				'footer'    => 'footer-menu',
				'socials'   => 'socials',
			),
			'options'    => $options,
		),
		array(
			'name'       => 'Furniture 5',
			'content'    => 'https://uix.store/data/sober/elementor/furniture-5/demo-content.xml',
			'widgets'    => 'https://uix.store/data/sober/elementor/furniture-5/widgets.wie',
			'preview'    => 'https://uix.store/data/sober/elementor/furniture-5/preview.jpg',
			'customizer' => 'https://uix.store/data/sober/elementor/furniture-5/customizer.dat',
			'sliders'    => 'https://uix.store/data/sober/elementor/furniture-5/sliders.zip',
			'tab'        => 'Elementor',
			'pages'      => array(
				'front_page' => 'Home v15',
				'blog'       => 'Blog',
				'shop'       => 'Shop',
				'cart'       => 'Cart',
				'checkout'   => 'Checkout',
				'my_account' => 'My Account',
				'order_tracking' => 'Order Tracking',
			),
			'menus'      => array(
				'primary'   => 'main-menu',
				'footer'    => 'footer-menu',
				'socials'   => 'socials',
			),
			'options'    => $options,
		),
		array(
			'name'       => 'Furniture 6',
			'content'    => 'https://uix.store/data/sober/elementor/furniture-6/demo-content.xml',
			'widgets'    => 'https://uix.store/data/sober/elementor/furniture-6/widgets.wie',
			'preview'    => 'https://uix.store/data/sober/elementor/furniture-6/preview.jpg',
			'customizer' => 'https://uix.store/data/sober/elementor/furniture-6/customizer.dat',
			'sliders'    => 'https://uix.store/data/sober/elementor/furniture-6/sliders.zip',
			'tab'        => 'Elementor',
			'pages'      => array(
				'front_page' => 'Home v16',
				'blog'       => 'Blog',
				'shop'       => 'Shop',
				'cart'       => 'Cart',
				'checkout'   => 'Checkout',
				'my_account' => 'My Account',
				'order_tracking' => 'Order Tracking',
			),
			'menus'      => array(
				'primary'   => 'main-menu',
				'footer'    => 'footer-menu',
				'socials'   => 'socials',
			),
			'options'    => $options,
		),
	);
}

add_action( 'soodi_after_setup_pages', 'sober_addons_import_order_tracking' );

/**
 * Update more page options
 *
 * @param $pages
 */
function sober_addons_import_order_tracking( $pages ) {
	if ( isset( $pages['order_tracking'] ) ) {
		$order = get_page_by_title( $pages['order_tracking'] );

		if ( $order ) {
			update_option( 'sober_order_tracking_page_id', $order->ID );
		}
	}

	if ( isset( $pages['portfolio'] ) ) {
		$portfolio = get_page_by_title( $pages['portfolio'] );

		if ( $portfolio ) {
			update_option( 'sober_portfolio_page_id', $portfolio->ID );
		}
	}
}

// add_action( 'soodi_before_import_content', 'sober_addons_import_product_attributes' );

/**
 * Prepare product attributes before import demo content
 *
 * @param $file
 */
function sober_addons_import_product_attributes( $file ) {
	global $wpdb;

	if ( ! class_exists( 'WXR_Parser' ) ) {
		require_once WP_PLUGIN_DIR . '/soo-demo-importer/includes/parsers.php';
	}

	$parser      = new WXR_Parser();
	$import_data = $parser->parse( $file );

	if ( isset( $import_data['posts'] ) ) {
		$posts = $import_data['posts'];

		if ( $posts && sizeof( $posts ) > 0 ) {
			foreach ( $posts as $post ) {
				if ( 'product' === $post['post_type'] ) {
					if ( ! empty( $post['terms'] ) ) {
						foreach ( $post['terms'] as $term ) {
							if ( strstr( $term['domain'], 'pa_' ) ) {
								if ( ! taxonomy_exists( $term['domain'] ) ) {
									$attribute_name = wc_sanitize_taxonomy_name( str_replace( 'pa_', '', $term['domain'] ) );

									// Create the taxonomy
									if ( ! in_array( $attribute_name, wc_get_attribute_taxonomies() ) ) {
										$attribute = array(
											'attribute_label'   => $attribute_name,
											'attribute_name'    => $attribute_name,
											'attribute_type'    => 'select',
											'attribute_orderby' => 'menu_order',
											'attribute_public'  => 0
										);
										$wpdb->insert( $wpdb->prefix . 'woocommerce_attribute_taxonomies', $attribute );
										delete_transient( 'wc_attribute_taxonomies' );
									}

									// Register the taxonomy now so that the import works!
									register_taxonomy(
										$term['domain'],
										apply_filters( 'woocommerce_taxonomy_objects_' . $term['domain'], array( 'product' ) ),
										apply_filters( 'woocommerce_taxonomy_args_' . $term['domain'], array(
											'hierarchical' => true,
											'show_ui'      => false,
											'query_var'    => true,
											'rewrite'      => false,
										) )
									);
								}
							}
						}
					}
				}
			}
		}
	}
}