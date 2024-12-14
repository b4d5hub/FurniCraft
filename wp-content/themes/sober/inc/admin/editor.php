<?php
/**
 * Editor customizer
 */

/**
 * Add dynmic style to editor.
 *
 * @param  string $mce
 * @return string
 */
function sober_editor_dynamic_styles( $mce ) {
    $styles = sober_block_editor_typography_css();
    $styles = preg_replace( '/\s+/', ' ', $styles );
    $styles = str_replace( '"', '', $styles );

    if ( isset( $mce['content_style'] ) ) {
        $mce['content_style'] .= ' ' . $styles . ' ';
    } else {
        $mce['content_style'] = $styles . ' ';
    }

    return $mce;
}

add_filter( 'tiny_mce_before_init', 'sober_editor_dynamic_styles' );


/**
 * Enqueue editor styles for Gutenberg
 */
function sober_block_editor_styles() {
	// Add custom fonts.
	wp_enqueue_style( 'sober-fonts', sober_fonts_url() );

	// Block styles.
	wp_enqueue_style( 'sober-block-editor-style', get_theme_file_uri( '/css/editor-blocks.css' ) );

	wp_add_inline_style( 'sober-block-editor-style', sober_block_editor_typography_css() );

    // Additional CSS for widget ediotr.
	wp_add_inline_style( 'sober-block-editor-style', 'button.components-button { line-height: 1.2}' );
}

add_action( 'enqueue_block_editor_assets', 'sober_block_editor_styles' );

/**
 * Get typography CSS for block editor.
 *
 * @return string
 */
function sober_block_editor_typography_css() {
	$settings = array(
		'typo_body'       => '.wp-block, .editor-styles-wrapper .wp-block, .block-editor .editor-styles-wrapper, .mce-content-body',
		'typo_link'       => '.wp-block a, .mce-content-body a',
		'typo_link_hover' => '.wp-block a:hover, .mce-content-body a',
		'typo_h1'         => '.wp-block h1, .wp-block .h1, .mce-content-body h1, .mce-content-body .h1',
		'typo_h2'         => '.wp-block h2, .wp-block .h2, .mce-content-body h2, .mce-content-body .h2',
		'typo_h3'         => '.wp-block h3, .wp-block .h3, .mce-content-body h3, .mce-content-body .h3',
		'typo_h4'         => '.wp-block h4, .wp-block .h4, .mce-content-body h4, .mce-content-body .h4',
		'typo_h5'         => '.wp-block h5, .wp-block .h5, .mce-content-body h5, .mce-content-body .h5',
		'typo_h6'         => '.wp-block h6, .wp-block .h6, .mce-content-body h6, .mce-content-body .h6',
	);

	$css        = '';
	$properties = array(
		'font-family'    => 'font-family',
		'font-size'      => 'font-size',
		'variant'        => 'font-weight',
		'line-height'    => 'line-height',
		'letter-spacing' => 'letter-spacing',
		'color'          => 'color',
		'text-transform' => 'text-transform',
		'text-align'     => 'text-align',
	);

	foreach ( $settings as $setting => $selector ) {
		$typography = sober_get_option( $setting );
		$style      = '';

		foreach ( $properties as $key => $property ) {
			if ( ! empty( $typography[ $key ] ) ) {
				$value = 'font-family' == $key ? rtrim( trim( $typography[ $key ] ), ',' ) : $typography[ $key ];
				$value = 'variant' == $key ? str_replace( 'regular', '400', $value ) : $value;

				if ( $value ) {
					$style .= $property . ': ' . $value . ';';
				}
			}
		}

		if ( ! empty( $style ) ) {
			$css .= $selector . '{' . $style . '}';
		}
	}

	return $css;
}
