<?php

const CCFM_ADDITIONAL_CACHE_NAMES = [
    'premium-addons-for-elementor' => 'Premium Addons for Elementor'
];

/**
 * Return the first caching system found.
 */
function ccfm_get_caching_system_used() {
    $cache_system_key = '';

    switch (true) {
        case class_exists( 'autoptimizeCache' ):
            $cache_system_key = 'autooptimize';
            break;
        case class_exists( 'Cache_Enabler' ):
            $cache_system_key = 'cacheenabler';
            break;
        case class_exists( '\Kinsta\Cache' ):
            $cache_system_key = 'kinsta';
            break;
        case class_exists( '\WPaaS\Cache' ):
            $cache_system_key = 'godaddy';
            break;
        case class_exists( 'Breeze_Admin' ):
            $cache_system_key = 'breeze';
            break;
        case class_exists( 'WP_Optimize' ) && defined( 'WPO_PLUGIN_MAIN_PATH' ):
            $cache_system_key = 'wp_optimize';
            break;
        case function_exists( 'sg_cachepress_purge_cache' ):
            $cache_system_key = 'siteground';
            break;
        case function_exists( 'w3tc_pgcache_flush' ):
            $cache_system_key = 'w3tc';
            break;
        case function_exists( 'wp_cache_clean_cache' ):
            $cache_system_key = 'wp_cache';
            break;
        case method_exists( 'WpFastestCache', 'deleteCache' ):
            $cache_system_key = 'wp_fastest_cache';
            break;
        case function_exists( 'rocket_clean_domain' ):
            $cache_system_key = 'wp_rocket';
            break;
        case class_exists( 'WpeCommon' ):
            $cache_system_key = 'wpengine';
            break;
        case defined( 'LSCWP_V' ):
            $cache_system_key = 'litespeed';
            break;
        default:
            break;
    }

    return $cache_system_key;
}

/**
 * Return the caching system name for the key.
 */
function ccfm_get_cache_system_name( $cache_system_key = '' ) {
    if ( empty( $cache_system_key ) ) {
        $cache_system_key = ccfm_get_caching_system_used();
    }
    $cache_name = '';
    switch( $cache_system_key ) {
        case 'autooptimize':
            $cache_name = 'Autoptimize';
            break;
        case 'breeze':
            $cache_name = 'Breeze';
            break;
        case 'cacheenabler':
            $cache_name = 'Cache Enabler';
            break;
        case 'godaddy':
            $cache_name = 'GoDaddy Cache';
            break;
        case 'kinsta':
            $cache_name = 'Kinsta Cache';
            break;
        case 'litespeed':
            $cache_name = 'LiteSpeed Cache';
            break;
        case 'siteground':
            $cache_name = 'SiteGround SuperCacher';
            break;
        case 'w3tc':
            $cache_name = 'W3 Total Cache';
            break;
        case 'wp_cache':
            $cache_name = 'WP Super Cache';
            break;
        case 'wp_fastest_cache':
            $cache_name = 'WP Fastest Cache';
            break;
        case 'wp_optimize':
            $cache_name = 'WP Optimize';
            break;
        case 'wp_rocket':
            $cache_name = 'WP Rocket';
            break;
        case 'wpengine':
            $cache_name = 'WPEngine Cache';
            break;
        default:
            break;
    }
    
    return $cache_name;
}

/**
 * Return the name a supported plugin.
 */
function ccfm_additional_caching_name( $plugin_slug ) {
    return CCFM_ADDITIONAL_CACHE_NAMES[$plugin_slug];
}

/**
 * Return an array of plugin slugs that exist on this install.
 * If $clear_cache is true or not set, run functions.
 */
function ccfm_clear_addtional_cache( $clear_cache = true ) {
    $plugin_slugs = [];

    if ( class_exists( 'PremiumAddons\Admin\Includes\Admin_Helper' ) &&
         method_exists(PremiumAddons\Admin\Includes\Admin_Helper::class, 'get_instance') && 
         method_exists(PremiumAddons\Admin\Includes\Admin_Helper::class, 'delete_assets_options') ) {
            $plugin_slugs[] = 'premium-addons-for-elementor';
            if ( $clear_cache ) {
                $admin_helper = PremiumAddons\Admin\Includes\Admin_Helper::get_instance();
                $admin_helper->delete_assets_options();
            }
    }

    return $plugin_slugs;
}
