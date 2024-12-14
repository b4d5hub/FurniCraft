<?php

/*
  Plugin Name: FOX - Currency Switcher Professional for WooCommerce
  Plugin URI: https://currency-switcher.com/
  Description: Currency Switcher for WooCommerce that allows to the visitors and customers on your woocommerce store site switch currencies and optionally apply selected currency on checkout
  Author: realmag777
  Version: 1.4.2.3
  Requires at least: WP 4.9.0
  Tested up to: WP 6.7
  Requires PHP: 7.4
  Text Domain: woocommerce-currency-switcher
  Domain Path: /languages
  Forum URI: https://pluginus.net/support/forum/woocs-woocommerce-currency-switcher-multi-currency-and-multi-pay-for-woocommerce/
  Author URI: https://pluginus.net/
  WC requires at least: 6.0
  WC tested up to: 9.3
 */

if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}


if (isset($_GET['woocommerce_gpf'])) {
    return false;
}

//disable FOX influence for REST api requests
if (isset($_SERVER['SCRIPT_URI'])) {
    $uri = parse_url(trim($_SERVER['SCRIPT_URI']));
    $uri = explode('/', trim($uri['path'], ' /'));
    if ($uri[0] === 'wp-json') {
        $show_legacy = array('widget-types', 'sidebars', 'widgets', 'batch', 'collection-data', 'cart', 'store');
        $match = array_intersect($show_legacy, $uri);

        if (count($match) == 0) {
            $allow = ['woocs'];
            if (isset($uri[1]) AND !in_array($uri[1], $allow)) {
                return; //!!it is important for different reports to exclude FOX influence
            }
        }
    }
}


if (defined('DOING_AJAX')) {

    if (isset($_REQUEST['action'])) {
        //do not recalculate refund amounts when we are in order backend
        if ($_REQUEST['action'] == 'woocommerce_refund_line_items') {
            if (!class_exists('WooCommerce_PDF_IPS_Pro') && !class_exists('WC_Smart_Coupons') && !class_exists('ACFWF')) {
                return;
            }

            if (apply_filters('woocs_disable_backend_refund_calculation', false)) {
                return;
            }
        }

        if (isset($_REQUEST['order_id']) AND $_REQUEST['action'] == 'woocommerce_load_order_items') {
            return;
        }
    }
}

define('WOOCS_VERSION', '1.4.2.3');
//define('WOOCS_VERSION', uniqid('woocs-'));//for dev test purposes to reset browser cache
define('WOOCS_MIN_WOOCOMMERCE', '6.0');
define('WOOCS_PATH', plugin_dir_path(__FILE__));
define('WOOCS_LINK', plugin_dir_url(__FILE__));
define('WOOCS_PLUGIN_NAME', plugin_basename(__FILE__));

//classes
include_once WOOCS_PATH . 'classes/woocs_session.php';
include_once WOOCS_PATH . 'classes/storage.php';
include_once WOOCS_PATH . 'classes/cron.php';
include_once WOOCS_PATH . 'classes/alert.php';
include_once WOOCS_PATH . 'classes/smart-designer.php';
include_once WOOCS_PATH . 'classes/fixed/fixed_amount.php';
include_once WOOCS_PATH . 'classes/fixed/fixed_price.php';
include_once WOOCS_PATH . 'classes/statistic.php';
include_once WOOCS_PATH . 'classes/reports.php';
include_once WOOCS_PATH . 'classes/dashboard_stat.php';
include_once WOOCS_PATH . 'classes/profiles.php';
include_once WOOCS_PATH . 'classes/compatibility/compatibility.php';
include_once WOOCS_PATH . 'classes/woocs_hpos.php';

include_once WOOCS_PATH . 'classes/world_currencies.php';

//03-11-2024
class WOOCS_STARTER {

    private $default_woo_version = 6.0;
    private $actualized = 0.0;
    private $version_key = "woocs_woo_version";
    private $_woocs = null;
    public $disable_plugin = array(); // add a slug of the  page  to  disble  the plugin. Example: 'account','cart'
    public $reverse_disable_plugin = 0; // set: true - to activate the plugin  on exact  pages

    public function __construct() {
        $this->actualized = floatval(get_option($this->version_key, $this->default_woo_version));
        $apl = get_option('woocs_activate_page_list', '');
        if ($apl) {
            $this->disable_plugin = array_map('trim', explode(',', $apl));
        } else {
            $this->disable_plugin = [];
        }
        $this->reverse_disable_plugin = get_option('woocs_activate_page_list_reverse', 1);
    }

    public function update_version() {
        if (defined('WOOCOMMERCE_VERSION') AND ( $this->actualized !== floatval(WOOCOMMERCE_VERSION))) {
            update_option('woocs_woo_version', WOOCOMMERCE_VERSION);
        }
    }

    public function get_actual_obj() {

        if (count($this->disable_plugin) AND !is_admin() AND (isset($_SERVER['SCRIPT_URI']) || isset($_SERVER['REQUEST_URI']))) {
            $exclude = false;
            $url = false;
            if (isset($_SERVER['SCRIPT_URI'])) {
                $url = $_SERVER['SCRIPT_URI'];
            }

            if (!$url) {
                $url = explode('?', $_SERVER['REQUEST_URI']);
                $url = $url[0];
                $url = explode('/page/', $_SERVER['REQUEST_URI']);
                $url = $url[0];
            }
            if (preg_match("/\/([-a-zA-Z0-9_]+)[\/]$/", $url, $matches)) {

                $exclude = in_array($matches[1], $this->disable_plugin);
            } elseif (in_array("", $this->disable_plugin)) {
                $exclude = true;
            }

            if ($this->reverse_disable_plugin) {
                $exclude = !$exclude;
            }

            //do not exclude in widget page
            if (isset($_SERVER['SCRIPT_URI'])) {
                $uri = parse_url(trim($_SERVER['SCRIPT_URI']));

                $uri = explode('/', trim($uri['path'], ' /'));
                if ($uri[0] === 'wp-json') {
                    $show_legacy = array('widget-types', 'sidebars', 'widgets', 'batch');
                    $match = array_intersect($show_legacy, $uri);
                    if (count($match) != 0) {
                        $exclude = false;
                    }
                }
            }

            if ($exclude) {
                return false;
            }
        }

        if ($this->_woocs != null) {
            return $this->_woocs;
        }

        include_once WOOCS_PATH . 'classes/woocs.php'; //woocs_after_33.php
        include_once WOOCS_PATH . 'classes/fixed/fixed_coupon.php';
        include_once WOOCS_PATH . 'classes/fixed/fixed_shipping.php';
        include_once WOOCS_PATH . 'classes/fixed/fixed_shipping_free.php';
        include_once WOOCS_PATH . 'classes/fixed/fixed_user_role.php';
        include_once WOOCS_PATH . 'classes/auto_switcher.php';
        include_once WOOCS_PATH . 'classes/analytics.php';

        $this->_woocs = new WOOCS();
        return $this->_woocs;
    }
}

//+++
if (isset($_GET['P3_NOCACHE'])) {
    //stupid trick for that who believes in P3
    return;
}

//+++
//fix: because of long id which prevent js script working
function woocs_short_id($smth) {
    return substr(md5($smth), 1, 7);
}

//+++
$WOOCS_STARTER = new WOOCS_STARTER();

$WOOCS = $WOOCS_STARTER->get_actual_obj();
if ($WOOCS) {
    $GLOBALS['WOOCS'] = $WOOCS;
    add_action('init', array($WOOCS, 'init'), 11);
}

//****
//rate + interes
add_filter('woocs_currency_data_manipulation', function ($currencies) {
    foreach ($currencies as $key => $value) {
        if (isset($value['rate_plus'])) {
            $interes = 0;
            if (!strpos($value['rate_plus'], '%')) {
                $interes = floatval($value['rate_plus']);
            } else {
                //example: 20%
                $interes = floatval(floatval($value['rate']) / 100) * intval($value['rate_plus']);
            }
            $currencies[$key]['rate'] = floatval($value['rate']) + $interes;
        }
    }

    return $currencies;
}, 1, 1);

//hide WOOCS meta in the order
add_filter('woocommerce_order_item_get_formatted_meta_data', function ($formatted_meta) {
    foreach ($formatted_meta as $key => $meta) {
        if (in_array($meta->key, ['_woocs_order_rate', '_woocs_order_base_currency', '_woocs_order_currency_changed_mannualy'])) {
            unset($formatted_meta[$key]);
        }
    }
    return $formatted_meta;
}, 10, 1);

//for bots
function woocs_is_bot(&$botname = '') {
    $bots = array(
        'rambler', 'googlebot', 'aport', 'yahoo', 'msnbot', 'turtle', 'mail.ru', 'omsktele',
        'yetibot', 'picsearch', 'sape.bot', 'sape_context', 'gigabot', 'snapbot', 'alexa.com',
        'megadownload.net', 'askpeter.info', 'igde.ru', 'ask.com', 'qwartabot', 'yanga.co.uk',
        'scoutjet', 'similarpages', 'oozbot', 'shrinktheweb.com', 'aboutusbot', 'followsite.com',
        'dataparksearch', 'google-sitemaps', 'appEngine-google', 'feedfetcher-google',
        'liveinternet.ru', 'xml-sitemaps.com', 'agama', 'metadatalabs.com', 'h1.hrn.ru',
        'googlealert.com', 'seo-rus.com', 'yaDirectBot', 'yandeG', 'yandex',
        'yandexSomething', 'Copyscape.com', 'AdsBot-Google', 'domaintools.com',
        'Nigma.ru', 'bing.com', 'dotnetdotcom'
    );

    $HTTP_USER_AGENT = "";
    if (isset($_SERVER['HTTP_USER_AGENT'])) {
        $HTTP_USER_AGENT = $_SERVER['HTTP_USER_AGENT'];
    }
    foreach ($bots as $bot) {
        if (stripos($HTTP_USER_AGENT, $bot) !== false) {
            $botname = $bot;
            return true;
        }
    }

    return false;
}

add_action('wp_head', function () {
    if (woocs_is_bot() && !get_option('woocs_disable_reset_currency_bots', 0)) {
        if (class_exists('WOOCS')) {
            global $WOOCS;
            $WOOCS->reset_currency();
        }
    }
}, 1);

//for separators
add_filter('option_woocommerce_price_thousand_sep', function ($value) {
    global $WOOCS;

    if (is_object($WOOCS)) {
        $current_currency = $WOOCS->get_woocommerce_currency();
        $value = $WOOCS->get_thousand_sep($current_currency, $value);
    }

    return $value;
});

//for separators
add_filter('option_woocommerce_price_decimal_sep', function ($value) {
    global $WOOCS;

    if (is_object($WOOCS)) {
        $current_currency = $WOOCS->get_woocommerce_currency();
        $value = $WOOCS->get_decimal_sep($current_currency, $value);
    }

    return $value;
});

add_filter("woocommerce_product_export_product_query_args", function ($args) {
    global $WOOCS;

    if (is_object($WOOCS) && $WOOCS->current_currency != $WOOCS->default_currency) {
        $WOOCS->reset_currency();
    }
    return $args;
});

add_action('before_woocommerce_init', function () {
    if (class_exists(\Automattic\WooCommerce\Utilities\FeaturesUtil::class)) {
        \Automattic\WooCommerce\Utilities\FeaturesUtil::declare_compatibility('custom_order_tables', __FILE__, true);
    }
});

