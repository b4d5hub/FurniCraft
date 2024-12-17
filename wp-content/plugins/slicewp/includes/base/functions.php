<?php

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) exit;


/**
 * Includes the Base files.
 *
 */
function slicewp_include_files_base() {

	// Get legend dir path
	$dir_path = plugin_dir_path( __FILE__ );

	// Include deprecated functions
	if ( file_exists( $dir_path . 'functions-deprecated.php' ) ) {
		include $dir_path . 'functions-deprecated.php';
	}

	// Include utils functions
	if ( file_exists( $dir_path . 'functions-utils.php' ) ) {
		include $dir_path . 'functions-utils.php';
	}

	// Include datetime functions.
	if ( file_exists( $dir_path . 'functions-datetime.php' ) ) {
		include $dir_path . 'functions-datetime.php';
	}

	// Include country functions
	if ( file_exists( $dir_path . 'functions-country.php' ) ) {
		include $dir_path . 'functions-country.php';
	}

	// Include currency functions
	if ( file_exists( $dir_path . 'functions-currency.php' ) ) {
		include $dir_path . 'functions-currency.php';
	}

	// Include captcha functions.
	if ( file_exists( $dir_path . 'functions-captcha.php' ) ) {
		include $dir_path . 'functions-captcha.php';
	}

	// Include ajax actions
	if ( file_exists( $dir_path . 'functions-actions-ajax.php' ) ) {
		include $dir_path . 'functions-actions-ajax.php';
	}

	// Include globals class.
	if ( file_exists( $dir_path . 'class-globals.php' ) ) {
		include $dir_path . 'class-globals.php';
	}

	// Include tracking class
	if ( file_exists( $dir_path . 'class-tracking.php' ) ) {
		include $dir_path . 'class-tracking.php';
	}

	// Include debug logger class
	if ( file_exists( $dir_path . 'class-debug-logger.php' ) ) {
		include $dir_path . 'class-debug-logger.php';
	}

	// Include update checker
	if ( file_exists( $dir_path . 'class-update-checker.php' ) ) {
		include_once $dir_path . 'class-update-checker.php';
	}

	// Include merge tags class.
	if ( file_exists( $dir_path . 'class-merge-tags.php' ) ) {
		include $dir_path . 'class-merge-tags.php';
	}

	// Include plugin usage tracker class
	if ( file_exists( $dir_path . 'class-plugin-usage-tracker.php' ) ) {
		include $dir_path . 'class-plugin-usage-tracker.php';
	}

	// Include compatibility functions.
	if ( file_exists( $dir_path . 'functions-compatibility.php' ) ) {
		include $dir_path . 'functions-compatibility.php';
	}

	// Include rewrite rules class.
	if ( file_exists( $dir_path . 'class-rewrite-rules.php' ) ) {
		include $dir_path . 'class-rewrite-rules.php';
	}

}
add_action( 'slicewp_include_files', 'slicewp_include_files_base' );


/**
 * Returns a plugin option
 *
 * @param string $option
 * @param mixed  $default
 *
 * @return mixed
 *
 */
function slicewp_get_option( $option, $default = '' ) {

	return get_option( 'slicewp_' . $option, $default );

}


/**
 * Updates a plugin option by the given value
 *
 * @param string $option
 * @param mixed  $value
 *
 */
function slicewp_update_option( $option, $value ) {

	/**
	 * Filters a specific option before its value is updated.
	 * 
	 * @param mixed $value
	 * 
	 */
	$value = apply_filters( 'slicewp_pre_update_option_' . $option, $value );

	return update_option( 'slicewp_' . $option, $value );

}


/**
 * Returns a plugin setting from the settings plugin option
 *
 * @param string $option
 * @param mixed  $default
 *
 * @return mixed
 *
 */
function slicewp_get_setting( $setting, $default = '' ) {

	$settings = slicewp_get_option( 'settings', array() );

	return ( isset( $settings[$setting] ) ? $settings[$setting] : $default );

}


/**
 * Returns the affiliate_id of the referrer saved in the cookie
 *
 * @return null|int
 *
 */
function slicewp_get_referrer_affiliate_id() {

	return slicewp()->services['tracking']->get_referrer_affiliate_id();

}


/**
 * Returns the visit_id of the referrer saved in the cookie
 *
 * @return null|int
 *
 */
function slicewp_get_referrer_visit_id() {

	return slicewp()->services['tracking']->get_referrer_visit_id();

}


/**
 * Calculates the commission amount for a given base amount taking into account
 * the passed arguments
 *
 * @param float $amount
 * @param array $args
 *
 * @return float
 *
 */
function slicewp_calculate_commission_amount( $amount, $args = array() ) {

	if ( empty( $args['origin'] ) ) {
		return 0;
	}

	if ( empty( $args['type'] ) ) {
		return 0;
	}

	$rate 	   = slicewp_get_setting( 'commission_rate_' . $args['type'] );
	$rate_type = slicewp_get_setting( 'commission_rate_type_' . $args['type'] );

	$commission_amount = ( $rate_type == 'percentage' ? round( ( $amount * $rate / 100 ), 2 ) : $rate );

	/**
	 * Filter the commission amount before returning it.
	 *
	 * @param float $commission_amount
	 * @param float $amount
	 * @param array $args
	 *
	 */
	$commission_amount = apply_filters( 'slicewp_calculate_commission_amount', $commission_amount, $amount, $args );

	return $commission_amount;
	
}


/**
 * Get the URL of the current page
 *
 * @return string
 *
 */
function slicewp_get_current_page_url() {

	global $wp;

	if ( get_option( 'permalink_structure' ) ) {
		$base = trailingslashit( home_url( $wp->request ) );
	} else {
		$base = add_query_arg( $wp->query_string, '', trailingslashit( home_url( $wp->request ) ) );
		$base = remove_query_arg( array( 'post_type', 'name' ), $base );
	}

	$scheme      = is_ssl() ? 'https' : 'http';
	$current_url = set_url_scheme( $base, $scheme );

	if ( is_front_page() ) {
		$current_url = home_url( '/' );
	}

	/**
	 * Filter the current page URL
	 *
	 * @param string $current_url
	 *
	 */
	return apply_filters( 'slicewp_get_current_page_url', $current_url );

}


/**
 * Locates a template and returns the path of the template file.
 *
 * @param string $template_name
 *
 * @return string|null
 *
 */
function slicewp_get_template_file_path( $template_name ) {

	/**
	 * Filter the default theme templates directory name
	 *
	 * @param string
	 *
	 */
	$templates_dir_name = apply_filters( 'slicewp_theme_templates_dir_name', 'slicewp' );

	$template_paths = array(
		1   => trailingslashit( get_stylesheet_directory() ) . $templates_dir_name,
		10  => trailingslashit( get_template_directory() ) . $templates_dir_name,
		100 => untrailingslashit( SLICEWP_PLUGIN_DIR ) . '/templates/'
	);

	/**
	 * Filter the template paths
	 *
	 * @param array $template_paths
	 *
	 */
	$template_paths = apply_filters( 'slicewp_template_paths', $template_paths );

	ksort( $template_paths, SORT_NUMERIC );
	$template_paths = array_map( 'trailingslashit', $template_paths );

	// Try to find the given template file.
	$file_path 	   = null;
	$template_name = ltrim( $template_name, '/' );

	foreach( $template_paths as $template_path ) {

		if ( file_exists( $template_path . $template_name ) ) {
			$file_path = $template_path . $template_name;
			break;
		}

	}

	/**
	 * Filter the template file path
	 *
	 * @param string $file_path
	 * @param string $template_name
	 *
	 */
	$file_path = apply_filters( 'slicewp_template_file_path', $file_path, $template_name );

	return $file_path;

}


/**
 * Loads a template part into a template.
 *
 * @param string $slug
 * @param string $name
 * @param array  $args
 *
 */
function slicewp_get_template_part( $slug, $name = null, $args = array() ) {
    
    $templates = array();
    $name      = (string) $name;

    if ( '' !== $name ) {
        $templates[] = "{$slug}-{$name}.php";
    }
 
    $templates[] = "{$slug}.php";
 
    /**
     * Fires before a template part is loaded.
     *
     * @param string $slug
     * @param string $name
     * @param array  $templates
     * @param array  $args
     *
     */
    do_action( 'slicewp_get_template_part', $slug, $name, $templates, $args );

    // Get the template file path
    $file_path = null;
 
 	foreach( $templates as $template_name ) {

 		$file_path = slicewp_get_template_file_path( $template_name );

 		if( ! is_null( $file_path ) )
 			break;

 	}

 	// If we find a file path, load the file
 	if ( ! is_null( $file_path ) ) {

 		load_template( $file_path, false, $args );

 	}

}


/**
 * Adds the "async" and "defer" attributes to scripts that have the tag
 * explicitly added to the handle
 *
 * @param string $tag
 * @param string $handle
 *
 */
function slicewp_script_async_defer_attribute( $tag, $handle ) {

	if ( is_admin() ) {
		return $tag;
	}

	if ( false === strpos( $handle, 'slicewp' ) ) {
		return $tag;
	}

	// Return tag with both async and defer
	if( false !== strpos( $handle, 'async-defer' ) ) {
		return str_replace( '<script ', '<script async defer ', $tag );
	}

    // Return the tag with the async attribute
    if ( false !== strpos( $handle, 'async' ) ) {
		return str_replace( '<script ', '<script async ', $tag );
	}

    // Return the tag with the defer attribute
    if ( false !== strpos( $handle, 'defer' ) ) {
		return str_replace( '<script ', '<script defer ', $tag );
	}
    
    return $tag;

}
add_filter( 'script_loader_tag', 'slicewp_script_async_defer_attribute', 10, 2 );


/**
 * Get the email of the current user
 *
 * @return string|null
 *
 */
function slicewp_get_current_user_email() {

	if ( 0 == get_current_user_id() ) {
		return null;
	}
	
	$user = wp_get_current_user();
	
	return $user->get( 'user_email' );

}


/**
 * Returns the user's preferences array.
 * 
 * @param int $user_id
 * 
 * @return array
 * 
 */
function slicewp_get_user_preferences( $user_id = 0 ) {

	$user_id = ( ! empty( $user_id ) ? absint( $user_id ) : get_current_user_id() );

	if ( empty( $user_id ) ) {
		return null;
	}

	$user_preferences = get_user_meta( $user_id, 'slicewp_user_preferences', true );
    $user_preferences = ( ! empty( $user_preferences ) && is_array( $user_preferences ) ? $user_preferences : array() );

	return $user_preferences;

}


/**
 * Updates the user's preferences array.
 * 
 * @param int   $user_id
 * @param array $preferences
 * 
 * @return bool
 * 
 */
function slicewp_update_user_preferences( $user_id, $preferences ) {

	if ( empty( $user_id ) ) {
		return false;
	}

	$user_preferences = _slicewp_array_wp_kses_post( ! empty( $user_preferences ) && is_array( $user_preferences ) ? $user_preferences : array() );

	return (bool)update_user_meta( absint( $user_id ), 'slicewp_user_preferences', $preferences );

}


/**
 * Adds title search in where clause of WP Query.
 * 
 * @param string   $where
 * @param WP_Query $wp_query
 * 
 * @return string
 * 
 */
function slicewp_add_posts_title_search_wp_query( $where, &$wp_query ) {

	global $wpdb;

	if ( $title = $wp_query->get( 'search_title' ) ) {

		$where .= " AND " . $wpdb->posts . ".post_title LIKE '%" . esc_sql( $wpdb->esc_like( $title ) ) . "%'";
	
	}

	return $where;

}


/**
 * Returns an array of data that will be used as the global "slicewp" JavaScript object on the front-end.
 * 
 * @return array
 * 
 */
function slicewp_get_global_script_vars() {

	$vars = array(
		'ajaxurl' 				 => admin_url( 'admin-ajax.php' ),
		'cookie_duration'   	 => absint( slicewp_get_setting( 'cookie_duration', '30' ) ),
		'affiliate_credit'  	 => sanitize_text_field( slicewp_get_setting( 'affiliate_credit', 'first' ) ),
		'affiliate_keyword' 	 => sanitize_text_field( slicewp_get_setting( 'affiliate_keyword', 'aff' ) ),
		'predefined_date_ranges' => _slicewp_array_wp_kses_post( slicewp_get_predefined_date_ranges() ),
		'settings' 				 => array(
			'active_currency' 		   	   => sanitize_text_field( slicewp_get_setting( 'active_currency', 'USD' ) ),
			'currency_symbol_position' 	   => sanitize_text_field( slicewp_get_setting( 'currency_symbol_position', 'before' ) ),
			'currency_thousands_separator' => sanitize_text_field( slicewp_get_setting( 'currency_thousands_separator', ',' ) ),
			'currency_decimal_separator'   => sanitize_text_field( slicewp_get_setting( 'currency_decimal_separator', '.' ) ),
		)
	);

	/**
	 * Filter the vars before returning them.
	 * 
	 * @param array
	 * 
	 */
	$vars = apply_filters( 'slicewp_global_script_vars', $vars );

	return $vars;

}