<?php

if (!defined('ABSPATH')) {
    exit;
}

use \Automattic\WooCommerce\Admin\API\Reports\Coupons\DataStore as CouponsDataStore;
use \Automattic\WooCommerce\Admin\API\Reports\Products\DataStore as ProductsDataStore;
use \Automattic\WooCommerce\Admin\API\Reports\Taxes\DataStore as TaxesDataStore;
use \Automattic\WooCommerce\Admin\API\Reports\TimeInterval;

class WOOCS_analytics {

    public function __construct() {
        //coupons
        add_action('woocommerce_analytics_update_coupon', array($this, 'convert_coupons'), 12, 2);
		
        //products 
		//wp-content\plugins\woocommerce\src\Admin\API\Reports\Products\DataStore.php::sync_order_products
        add_action('woocommerce_analytics_update_product', array($this, 'convert_products'), 12, 2);
        //tax
        add_action('woocommerce_analytics_update_tax', array($this, 'convert_tax'), 12, 2);
    }
	public function update_order_analytics_data($order_id){
		ProductsDataStore::sync_order_products($order_id);
	}
	public function convert_tax($tax_rate_id, $order_id) {
        global $wpdb;
        global $WOOCS;
        $decimal = 2;
        $currencies = $WOOCS->get_currencies();
		//hpos
        //$_order_currency = get_post_meta($order_id, '_order_currency', true);
		$order = wc_get_order( $order_id ); 		
		$_order_currency  = $order->get_currency();
		
        if (!$_order_currency || $_order_currency == $WOOCS->default_currency) {
            return;
        }
		//hpos
        //$order_rate = get_post_meta($order_id, '_woocs_order_rate', true);
		$order_rate = $order->get_meta( '_woocs_order_rate', true);
        $decimal = $currencies[$WOOCS->default_currency]['decimals'];
        if (!$order_rate && isset($currencies[$_order_currency])) {
            $order_rate = $currencies[$_order_currency]['rate'];
        }

        if (!$order_rate) {
            return;
        }

        $table_name = TaxesDataStore::get_db_table_name();
        $tax_item = $wpdb->get_row(
                $wpdb->prepare(
                        // phpcs:ignore WordPress.DB.PreparedSQL.NotPrepared, WordPress.DB.PreparedSQL.InterpolatedNotPrepared
                        "SELECT shipping_tax,order_tax,total_tax FROM {$table_name} WHERE order_id = %d AND tax_rate_id = %d",
                        $order_id,
                        $tax_rate_id
                ),
                ARRAY_A
        );

        foreach ($tax_item as $key => $amount) {

            if (floatval($amount)) {
                $pr = floatval($amount) / $order_rate;
                $tax_item[$key] = round($pr, $decimal);
            }
        }

        $wpdb->update(
                $table_name,
                $tax_item,
                array(
                    'order_id' => $order_id,
                    'tax_rate_id' => $tax_rate_id
                ),
                array('%f', '%f', '%f'),
                array('%d', '%d')
        );
    }

    public function convert_products($order_item_id, $order_id) {
        global $wpdb;
        global $WOOCS;

        $decimal = 2;
        $currencies = $WOOCS->get_currencies();
        
		//hpos
        //$_order_currency = get_post_meta($order_id, '_order_currency', true);
		$order = wc_get_order( $order_id ); 		
		$_order_currency  = $order->get_currency();
		
		
        if (!$_order_currency || $_order_currency == $WOOCS->default_currency) {
            return;
        }
		
		//hpos
        //$order_rate = get_post_meta($order_id, '_woocs_order_rate', true);
		$order_rate = $order->get_meta( '_woocs_order_rate', true);
        
        $decimal = $currencies[$WOOCS->default_currency]['decimals'];
        if (!$order_rate && isset($currencies[$_order_currency])) {
            $order_rate = $currencies[$_order_currency]['rate'];
        }

        if (!$order_rate) {
            return;
        }

        $table_name = ProductsDataStore::get_db_table_name();
        $product_item = $wpdb->get_row(
                $wpdb->prepare(
                        // phpcs:ignore WordPress.DB.PreparedSQL.NotPrepared, WordPress.DB.PreparedSQL.InterpolatedNotPrepared
                        "SELECT product_net_revenue,product_gross_revenue,tax_amount,shipping_amount,shipping_tax_amount,coupon_amount FROM {$table_name} WHERE order_id = %d AND order_item_id = %d",
                        $order_id,
                        $order_item_id
                ),
                ARRAY_A
        );

        foreach ($product_item as $key => $amount) {

            if (floatval($amount)) {
                $pr = floatval($amount) / $order_rate;
                $product_item[$key] = round($pr, $decimal);
            }
        }
        $wpdb->update(
                $table_name,
                $product_item,
                array(
                    'order_id' => $order_id,
                    'order_item_id' => $order_item_id
                ),
                array('%f', '%f', '%f', '%f', '%f', '%f'),
                array('%d', '%d')
        );
    }

    public function convert_coupons($coupon_id, $order_id) {
        global $wpdb;
        global $WOOCS;

        $decimal = 2;
        $currencies = $WOOCS->get_currencies();
		
		//hpos
        //$_order_currency = get_post_meta($order_id, '_order_currency', true);
		$order = wc_get_order( $order_id );
		$_order_currency  = $order->get_currency();
		
        if (!$_order_currency || $_order_currency == $WOOCS->default_currency) {
            return;
        }
        
		//hpos
        //$order_rate = get_post_meta($order_id, '_woocs_order_rate', true);
		$order_rate = $order->get_meta( '_woocs_order_rate', true);		
		
		
        $decimal = $currencies[$WOOCS->default_currency]['decimals'];
        if (!$order_rate && isset($currencies[$_order_currency])) {
            $order_rate = $currencies[$_order_currency]['rate'];
        }

        if (!$order_rate) {
            return;
        }
        $table_name = CouponsDataStore::get_db_table_name();
        $coupon_item = $wpdb->get_row(
                $wpdb->prepare(
                        // phpcs:ignore WordPress.DB.PreparedSQL.NotPrepared, WordPress.DB.PreparedSQL.InterpolatedNotPrepared
                        "SELECT discount_amount FROM {$table_name} WHERE order_id = %d AND coupon_id = %d",
                        $order_id,
                        $coupon_id
                ),
                ARRAY_A
        );

        foreach ($coupon_item as $key => $amount) {

            if (floatval($amount)) {
                $pr = floatval($amount) / $order_rate;
                $coupon_item[$key] = round($pr, $decimal);
            }
        }
        $wpdb->update(
                $table_name,
                $coupon_item,
                array(
                    'order_id' => $order_id,
                    'order_item_id' => $coupon_item
                ),
                array('%f'),
                array('%d', '%d')
        );

//		global $wpdb;
//		global $WOOCS;
//		$order = wc_get_order($order_id);
//
//		if (!$order) {
//			return;
//		}
//
//		$coupon_items = $order->get_items('coupon');
//		$currencies = $WOOCS->get_currencies();
//
//		$_order_currency = get_post_meta($order_id, '_order_currency', true);
//		if (!$_order_currency || $_order_currency == $WOOCS->default_currency) {
//			return;
//		}
//		$order_rate = get_post_meta($order_id, '_woocs_order_rate', true);
//
//		if (!$order_rate && isset($currencies[$_order_currency])) {
//			$order_rate = $currencies[$_order_currency]['rate'];
//		}
//
//		if (!$order_rate) {
//			return;
//		}
//
//		foreach ($coupon_items as $coupon_item) {
//			$current_coupon_id = CouponsDataStore::get_coupon_id($coupon_item);
//			if ($current_coupon_id != $coupon_id) {
//				continue;
//			}
//
//			$discount = $coupon_item->get_discount();
//
//			$discount = $discount / $order_rate;
//
//			$result = $wpdb->replace(
//					CouponsDataStore::get_db_table_name(),
//					array(
//						'order_id' => $order_id,
//						'coupon_id' => $coupon_id,
//						'discount_amount' => $discount,
//						'date_created' => $order->get_date_created('edit')->date(TimeInterval::$sql_datetime_format),
//					),
//					array(
//						'%d',
//						'%d',
//						'%f',
//						'%s',
//					)
//			);
//		}
    }

}
