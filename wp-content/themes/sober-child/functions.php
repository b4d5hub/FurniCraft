<?php
/**
 * Sober functions and definitions.
 *
 * @link    https://developer.wordpress.org/themes/basics/theme-functions/
 *
 * @package Sober Child
 */

add_action( 'wp_enqueue_scripts', 'sober_child_enqueue_scripts', 20 );

/**
 * Enqueues stylesheets and scripts of the child theme
 * 
 * @return void
 */
function sober_child_enqueue_scripts() {
    if ( is_rtl() ) {
        wp_enqueue_style( 'sober-rtl', get_template_directory_uri() . '/rtl.css' );
    }

    wp_enqueue_style( 'sober-child', get_stylesheet_uri() );
}
