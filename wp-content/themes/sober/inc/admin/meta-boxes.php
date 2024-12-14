<?php
/**
 * Registering meta boxes
 *
 * All the definitions of meta boxes are listed below with comments.
 *
 * For more information, please visit:
 * @link http://www.deluxeblogtips.com/meta-box/
 */


/**
 * Registering meta boxes
 *
 * Using Meta Box plugin: http://www.deluxeblogtips.com/meta-box/
 *
 * @see http://www.deluxeblogtips.com/meta-box/docs/define-meta-boxes
 *
 * @param array $meta_boxes Default meta boxes. By default, there are no meta boxes.
 *
 * @return array All registered meta boxes
 */
function sober_register_meta_boxes( $meta_boxes ) {
	// Post format's meta box
	$meta_boxes[] = array(
		'id'       => 'post-format-settings',
		'title'    => esc_html__( 'Format Details', 'sober' ),
		'pages'    => array( 'post' ),
		'context'  => 'normal',
		'priority' => 'high',
		'autosave' => true,
		'fields'   => array(
			array(
				'name'             => esc_html__( 'Image', 'sober' ),
				'id'               => 'image',
				'type'             => 'image_advanced',
				'class'            => 'image',
				'max_file_uploads' => 1,
			),
			array(
				'name'  => esc_html__( 'Gallery', 'sober' ),
				'id'    => 'images',
				'type'  => 'image_advanced',
				'class' => 'gallery',
			),
			array(
				'name'  => esc_html__( 'Audio', 'sober' ),
				'id'    => 'audio',
				'type'  => 'textarea',
				'cols'  => 20,
				'rows'  => 2,
				'class' => 'audio',
			),
			array(
				'name'  => esc_html__( 'Video', 'sober' ),
				'id'    => 'video',
				'type'  => 'textarea',
				'cols'  => 20,
				'rows'  => 2,
				'class' => 'video',
			),
		),
	);

	// Display Settings
	$meta_boxes[] = array(
		'id'       => 'display-settings',
		'title'    => esc_html__( 'Display Settings', 'sober' ),
		'pages'    => array( 'page' ),
		'context'  => 'normal',
		'priority' => 'high',
		'fields'   => array(
			array(
				'name'  => esc_html__( 'Site Header', 'sober' ),
				'id'    => 'heading_site_header',
				'class' => 'site_header_heading',
				'type'  => 'heading',
			),
			array(
				'name'    => esc_html__( 'Header Background', 'sober' ),
				'id'      => 'site_header_bg',
				'type'    => 'select',
				'options' => array(
					''            => esc_html__( 'Default', 'sober' ),
					'white'       => esc_html__( 'White', 'sober' ),
					'dark'        => esc_html__( 'Dark', 'sober' ),
					'transparent' => esc_html__( 'Transparent', 'sober' ),
					'custom'      => esc_html__( 'Custom', 'sober' ),
				),
			),
			array(
				'name'  => '&nbsp;',
				'id'    => 'header_background_color',
				'class' => 'header-background-color',
				'type'  => 'color',
				'class' => 'header-background-color',
			),
			array(
				'name'    => esc_html__( 'Header Text Color', 'sober' ),
				'id'      => 'site_header_text_color',
				'class'   => 'site_header_text_color',
				'type'    => 'select',
				'options' => array(
					''      => esc_html__( 'Default', 'sober' ),
					'light' => esc_html__( 'Light', 'sober' ),
					'dark'  => esc_html__( 'Dark', 'sober' ),
				),
			),
			array(
				'name'    => esc_html__( 'Remove Header', 'sober' ),
				'id'      => 'no_header',
				'type'    => 'checkbox',
				'desc'     => esc_html__( 'Remove the header from this page', 'sober' ),
			),
			array(
				'name'  => esc_html__( 'Page Header', 'sober' ),
				'id'    => 'heading_page_header',
				'class' => 'page_header_heading',
				'type'  => 'heading',
			),
			array(
				'name' => esc_html__( 'Hide Page Header', 'sober' ),
				'id'   => 'hide_page_header',
				'type' => 'checkbox',
				'std'  => false,
			),
			array(
				'name'             => esc_html__( 'Page Header Image', 'sober' ),
				'id'               => 'page_header_bg',
				'class'            => 'page-header-field',
				'type'             => 'image_advanced',
				'max_file_uploads' => 1,
			),
			array(
				'name'    => esc_html__( 'Page Header Text Color', 'sober' ),
				'id'      => 'page_header_text_color',
				'class'   => 'page-header-field',
				'type'    => 'select',
				'options' => array(
					''      => esc_html__( 'Default', 'sober' ),
					'light' => esc_html__( 'Light', 'sober' ),
					'dark'  => esc_html__( 'Dark', 'sober' ),
				),
			),
			array(
				'name'  => esc_html__( 'Hide Breadcrumb', 'sober' ),
				'id'    => 'hide_breadcrumb',
				'class' => 'page-header-field',
				'type'  => 'checkbox',
				'std'   => false,
			),
			array(
				'name'  => esc_html__( 'Hide Page Title', 'sober' ),
				'id'    => 'hide_page_title',
				'class' => 'hide-page-title',
				'type'  => 'checkbox',
				'std'   => false,
			),
			array(
				'name'  => esc_html__( 'Footer', 'sober' ),
				'id'    => 'heading_footer',
				'class' => 'footer-option layout_footer',
				'type'  => 'heading',
			),
			array(
				'name'  => esc_html__( 'Remove Footer', 'sober' ),
				'id'    => 'no_footer',
				'class' => 'footer-option no-footer',
				'type'  => 'checkbox',
				'desc'  => esc_html__( 'Remove the footer from this page', 'sober' ),
			),
			array(
				'name'    => esc_html__( 'Footer Background', 'sober' ),
				'id'      => 'footer_background',
				'class' => 'footer-option footer-background',
				'type'    => 'select',
				'options' => array(
					''            => esc_html__( 'Default', 'sober' ),
					'light'       => esc_html__( 'Light', 'sober' ),
					'dark'        => esc_html__( 'Dark', 'sober' ),
					'transparent' => esc_html__( 'Transparent', 'sober' ),
					'custom'      => esc_html__( 'Custom', 'sober' ),
				),
			),
			array(
				'name'  => '&nbsp;',
				'id'    => 'footer_background_color',
				'class' => 'footer-background-color',
				'type'  => 'color',
				'class' => 'footer-option footer-background-color',
			),
			array(
				'name'    => esc_html__( 'Footer Text Color', 'sober' ),
				'id'      => 'footer_textcolor',
				'class'   => 'footer-option footer-text-color',
				'type'    => 'select',
				'options' => array(
					''      => esc_html__( 'Default', 'sober' ),
					'light' => esc_html__( 'Light', 'sober' ),
					'dark'  => esc_html__( 'Dark', 'sober' ),
				),
			),
			array(
				'name'  => esc_html__( 'Layout', 'sober' ),
				'id'    => 'heading_layout',
				'class' => 'layout_heading',
				'type'  => 'heading',
			),
			array(
				'name' => esc_html__( 'Custom Layout', 'sober' ),
				'id'   => 'custom_layout',
				'type' => 'checkbox',
				'std'  => false,
			),
			array(
				'name'    => esc_html__( 'Layout', 'sober' ),
				'id'      => 'layout',
				'type'    => 'image_select',
				'class'   => 'custom-layout',
				'options' => array(
					'no-sidebar'   => get_template_directory_uri() . '/images/options/sidebars/empty.png',
					'single-left'  => get_template_directory_uri() . '/images/options/sidebars/single-left.png',
					'single-right' => get_template_directory_uri() . '/images/options/sidebars/single-right.png',
				),
			),
			array(
				'name'    => esc_html__( 'Content Top Spacing', 'sober' ),
				'id'      => 'top_spacing',
				'type'    => 'select',
				'options' => array(
					''       => esc_html__( 'Default', 'sober' ),
					'none'   => esc_html__( 'No spacing', 'sober' ),
					'custom' => esc_html__( 'Custom', 'sober' ),
				),
			),
			array(
				'name'  => '&nbsp;',
				'id'    => 'top_padding',
				'class' => 'custom-spacing hidden',
				'type'  => 'text',
				'std'   => '50px',
			),
			array(
				'name'    => esc_html__( 'Content Bottom Spacing', 'sober' ),
				'id'      => 'bottom_spacing',
				'type'    => 'select',
				'options' => array(
					''       => esc_html__( 'Default', 'sober' ),
					'none'   => esc_html__( 'No spacing', 'sober' ),
					'custom' => esc_html__( 'Custom', 'sober' ),
				),
			),
			array(
				'name'  => '&nbsp;',
				'id'    => 'bottom_padding',
				'class' => 'custom-spacing hidden',
				'type'  => 'text',
				'std'   => '100px',
			),
		),
	);

	// Display Settings (Full Screen)
	$meta_boxes[] = array(
		'id'       => 'full-screen-display-settings',
		'title'    => esc_html__( 'Display Settings', 'sober' ),
		'pages'    => array( 'page' ),
		'context'  => 'normal',
		'priority' => 'high',
		'fields'   => array(
			array(
				'name'    => esc_html__( 'Page Background', 'sober' ),
				'id'      => 'fullscreen_background',
				'type'    => 'background',
				'std'     => esc_html__( 'Transparent', 'sober' ),
				'max_file_uploads' => 1,
			),
			array(
				'type'    => 'divider',
			),
			array(
				'name'    => esc_html__( 'Header Background', 'sober' ),
				'type'    => 'custom_html',
				'std'     => esc_html__( 'Transparent', 'sober' ),
			),
			array(
				'name'    => esc_html__( 'Header Text Color', 'sober' ),
				'id'      => 'fullscreen_header_text_color',
				'type'    => 'select',
				'options' => array(
					''      => esc_html__( 'Default', 'sober' ),
					'light' => esc_html__( 'Light', 'sober' ),
					'dark'  => esc_html__( 'Dark', 'sober' ),
				),
			),
			array(
				'name'    => esc_html__( 'Remove Header', 'sober' ),
				'id'      => 'fullscreen_no_header',
				'type'    => 'checkbox',
				'desc'     => esc_html__( 'Remove the header from your website', 'sober' ),
			),
			array(
				'type'    => 'divider',
			),
			array(
				'name'    => esc_html__( 'Content Container', 'sober' ),
				'id'      => 'fullscreen_content_container',
				'type'    => 'radio',
				'std'     => 'wrapped',
				'options' => array(
					'wrapped'   => esc_html__( 'Wrapped', 'sober' ),
					'fullwidth' => esc_html__( 'Full-width', 'sober' ),
				),
			),
			array(
				'type'    => 'divider',
			),
			array(
				'name'    => esc_html__( 'Footer Background', 'sober' ),
				'type'    => 'custom_html',
				'std'     => esc_html__( 'Transparent', 'sober' ),
			),
			array(
				'name'    => esc_html__( 'Footer Text Color', 'sober' ),
				'id'      => 'fullscreen_footer_text_color',
				'type'    => 'select',
				'options' => array(
					''      => esc_html__( 'Default', 'sober' ),
					'light' => esc_html__( 'Light', 'sober' ),
					'dark'  => esc_html__( 'Dark', 'sober' ),
				),
			),
			array(
				'name'    => esc_html__( 'Remove Footer', 'sober' ),
				'id'      => 'fullscreen_no_footer',
				'type'    => 'checkbox',
				'desc'     => esc_html__( 'Remove the footer from your website', 'sober' ),
			),
		),
	);

	if ( 'style-5' == sober_get_option( 'single_product_style' ) ) {
		$meta_boxes[] = array(
			'id'       => 'display-settings',
			'title'    => esc_html__( 'Display Settings', 'sober' ),
			'pages'    => array( 'product' ),
			'context'  => 'normal',
			'priority' => 'low',
			'fields'   => array(
				array(
					'name'  => esc_html__( 'Background Color', 'sober' ),
					'desc' => esc_html__( 'Pick a background color for product page. Or leave it empty to automatically detect the background from product main image.', 'sober' ),
					'id'    => 'background_color',
					'type'  => 'color',
				),
				array(
					'name' => esc_html__( 'Keep this color', 'sober' ),
					'desc' => esc_html__( 'Keep the picked color for all gallery images.', 'sober' ),
					'id'   => 'background_color_for_all_images',
					'type' => 'checkbox',
					'std'  => false,
				),
			),
		);
	}

	$meta_boxes[] = array(
		'id'       => 'product-video',
		'title'    => esc_html__( 'Product Video', 'sober' ),
		'pages'    => array( 'product' ),
		'context'  => 'side',
		'priority' => 'low',
		'fields'   => array(
			array(
				'name' => esc_html__( 'Video URL', 'sober' ),
				'id'   => 'video_url',
				'type' => 'oembed',
			),
			array(
				'name'             => esc_html__( 'Video Thumbnail', 'sober' ),
				'id'               => 'video_thumbnail',
				'type'             => 'image_advanced',
				'max_file_uploads' => 1,
				'desc'             => esc_html__( 'The video thumbnail', 'sober' ),
			),
			array(
				'name'    => esc_html__( 'Video Position', 'sober' ),
				'id'      => 'video_position',
				'type'    => 'select',
				'std'     => 'last',
				'options' => array(
					'first' => esc_html__( 'At the begining of product gallery', 'sober' ),
					'last'  => esc_html__( 'At the end of product gallery', 'sober' ),
				),
			),
			array(
				'name'    => esc_html__( 'Mute Video', 'sober' ),
				'id'      => 'mute_video',
				'type'    => 'select',
				'std'     => '',
				'options' => array(
					''       => esc_html__( 'Default', 'sober' ),
					'mute'   => esc_html__( 'Mute', 'sober' ),
					'unmute' => esc_html__( 'Unmute', 'sober' ),
				),
			),
		),
	);

	return $meta_boxes;
}

add_filter( 'rwmb_meta_boxes', 'sober_register_meta_boxes' );

/**
 * Enqueue scripts for admin
 *
 * @since  1.0
 */
function sober_meta_boxes_scripts( $hook ) {

	if ( in_array( $hook, array( 'post.php', 'post-new.php' ) ) ) {
		wp_enqueue_script( 'sober-meta-boxes', get_template_directory_uri() . '/js/admin/meta-boxes.js', array( 'jquery' ), '20160523', true );
	}
}

add_action( 'admin_enqueue_scripts', 'sober_meta_boxes_scripts' );