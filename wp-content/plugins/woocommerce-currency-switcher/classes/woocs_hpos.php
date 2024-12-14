<?php

use Automattic\WooCommerce\Internal\DataStores\Orders\CustomOrdersTableController as HposController;
use Automattic\WooCommerce\Utilities\OrderUtil;

/**
 * WoocsHpos class 
 * 
 * compatibility functionality with new order tables High-Performance Order Storage (HPOS)
 */
class WoocsHpos {

    private $inabled_hpos = null;

    public function __construct() {
        
    }

    /**
     * Checking if the HPOS option is enabled
     * @return bool
     */
    public function isEnabledHpos(): bool {
		if (!class_exists('Automattic\WooCommerce\Utilities\OrderUtil')) {
			$this->inabled_hpos = false;
		}

        if (null === $this->inabled_hpos) {
            if (OrderUtil::custom_orders_table_usage_is_enabled()) {
                $this->inabled_hpos = true;
            } else {
                $this->inabled_hpos = false;
            }
        }

        return $this->inabled_hpos;
    }

    /**
     * Getting the page ID to place the meta box
     * @return string
     */
    public function getOrderScreenId(): string {
        if (!function_exists('wc_get_container') || !class_exists('Automattic\WooCommerce\Utilities\OrderUtil') ) {
            return '';
        }
        $screen = wc_get_container()->get(HposController::class)->custom_orders_table_usage_is_enabled() ? wc_get_page_screen_id('shop-order') : 'shop_order';
        return $screen;
    }

    /**
     * Recalculation of the order to another currency
     * 
     * @param class WOOCS $woocs
     * @param int $order_id
     * @param string $selected_currency
     * @return void
     */
    public function recalculateOrder($woocs, $order_id, $selected_currency = ''): void {
        if (!$selected_currency) {
            $selected_currency = $woocs->default_currency;
        }

        //HPOS
        $order = wc_get_order($order_id);
        $order_currency = $order->get_currency();
        $_woocs_order_rate = $order->get_meta('_woocs_order_rate', true);

        //lets avoid recalculation for order which is already in
        if (strtolower($order_currency) === strtolower($selected_currency) OR empty($order_currency)) {
            return;
        }

        $decimals = $woocs->get_currency_price_num_decimals($selected_currency, $woocs->price_num_decimals);
        $currencies = $woocs->get_currencies();

        //***
        //hpos
        $order->set_currency($selected_currency);
        $order->update_meta_data('_woocs_order_currency', $selected_currency);
        $order->update_meta_data('_woocs_order_base_currency', $woocs->default_currency);
        $order->update_meta_data('_woocs_order_rate', floatval($currencies[$selected_currency]['rate']));
        $order->update_meta_data('_woocs_order_currency_changed_mannualy', time());

        //***
        //hpos
        $_order_shipping = $order->get_shipping_total();
        $val = $woocs->back_convert($_order_shipping, $_woocs_order_rate, $decimals);
        if ($selected_currency !== $woocs->default_currency) {
            $val = floatval($val) * floatval($currencies[$selected_currency]['rate']);
        }

        //hpos
        $order->set_shipping_total($val);

        //hpos
        $_order_total = $order->get_total();
        $val = $woocs->back_convert($_order_total, $_woocs_order_rate, $decimals);
        if ($selected_currency !== $woocs->default_currency) {
            $val = floatval($val) * floatval($currencies[$selected_currency]['rate']);
        }
        //hpos;
        $order->set_total($val);

        //hpos
        //$_refund_amount = get_post_meta($order_id, '_refund_amount', true);
//		$_refund_amount = $order->get_total_refunded();
//
//        $val = $this->back_convert($_refund_amount, $_woocs_order_rate, $decimals);
//        if ($selected_currency !== $this->default_currency) {
//            $val = floatval($val) * floatval($currencies[$selected_currency]['rate']);
//        }
//        update_post_meta($order_id, '_refund_amount', $val);
        //fing for hpos
        //hpos
        $_cart_discount_tax = $order->get_discount_tax();
        $val = $woocs->back_convert($_cart_discount_tax, $_woocs_order_rate, $decimals);
        if ($selected_currency !== $woocs->default_currency) {
            $val = floatval($val) * floatval($currencies[$selected_currency]['rate']);
        }
        //hpos
        $order->set_discount_tax($val);

        //hpos
        //$_order_tax = get_post_meta($order_id, '_order_tax', true);
//		$_order_tax = $order->get_total_tax();
//        $val = $this->back_convert($_order_tax, $_woocs_order_rate, $decimals);
//        if ($selected_currency !== $this->default_currency) {
//            $val = floatval($val) * floatval($currencies[$selected_currency]['rate']);
//        }
        //hpos
        //update_post_meta($order_id, '_order_tax', $val);
        //$order->set_total_tax($val);
        //hpos
        $_order_shipping_tax = $order->get_shipping_tax();
        $val = $woocs->back_convert($_order_shipping_tax, $_woocs_order_rate, $decimals);
        if ($selected_currency !== $woocs->default_currency) {
            $val = floatval($val) * floatval($currencies[$selected_currency]['rate']);
        }
        //hpos
        $order->set_shipping_tax($val);

        //hpos
        $_cart_discount = $order->get_discount_total();
        $val = $woocs->back_convert($_cart_discount, $_woocs_order_rate, $decimals);
        if ($selected_currency !== $woocs->default_currency) {
            $val = floatval($val) * floatval($currencies[$selected_currency]['rate']);
        }
        //hpos
        $order->set_discount_tax($val);

//***
        //hpos
        $line_items = $order->get_items(['line_item', 'shipping', 'tax']);
        if (!empty($line_items) AND is_array($line_items)) {
            foreach ($line_items as $v) {
                //hpos
                $order_item_id = $v->get_id();
                $order_item_type = $v->get_type();

                switch ($order_item_type) {
                    case 'line_item':
                        //hpos
                        $amount = $v->get_subtotal();
                        $val = $woocs->back_convert($amount, $_woocs_order_rate, $decimals);
                        if ($selected_currency !== $woocs->default_currency) {
                            $val = floatval($val) * floatval($currencies[$selected_currency]['rate']);
                        }
                        //hpos
                        $v->set_subtotal($val);

                        //hpos
                        $amount = $v->get_total();
                        $val = $woocs->back_convert($amount, $_woocs_order_rate, $decimals);
                        if ($selected_currency !== $woocs->default_currency) {
                            $val = floatval($val) * floatval($currencies[$selected_currency]['rate']);
                        }
                        //hpos
                        $v->set_total($val);

                        //hpos
                        $amount = $v->get_subtotal_tax();
                        $val = $woocs->back_convert($amount, $_woocs_order_rate, $decimals);
                        if ($selected_currency !== $woocs->default_currency) {
                            $val = floatval($val) * floatval($currencies[$selected_currency]['rate']);
                        }
                        //hpos
                        $v->set_subtotal_tax($val);

                        //hpos
                        $amount = $v->get_total_tax();
                        $val = $woocs->back_convert($amount, $_woocs_order_rate, $decimals);
                        if ($selected_currency !== $woocs->default_currency) {
                            $val = floatval($val) * floatval($currencies[$selected_currency]['rate']);
                        }
                        //hpos
                        $v->set_total_tax($val);

                        //hpos
                        $_line_tax_data = $v->get_taxes();
                        if (!empty($_line_tax_data) AND is_array($_line_tax_data)) {
                            foreach ($_line_tax_data as $key => $values) {
                                if (!empty($values)) {
                                    if (is_array($values)) {
                                        foreach ($values as $k => $value) {
                                            if (is_numeric($value)) {
                                                $_line_tax_data[$key][$k] = $woocs->back_convert($value, $_woocs_order_rate, $decimals);
                                                if ($selected_currency !== $woocs->default_currency) {
                                                    $_line_tax_data[$key][$k] = floatval($_line_tax_data[$key][$k]) * floatval($currencies[$selected_currency]['rate']);
                                                }
                                            }
                                        }
                                    } else {
                                        if (is_numeric($values)) {
                                            $_line_tax_data[$key] = $woocs->back_convert($values, $_woocs_order_rate, $decimals);
                                            if ($selected_currency !== $woocs->default_currency) {
                                                $_line_tax_data[$key] = floatval($_line_tax_data[$key]) * floatval($currencies[$selected_currency]['rate']);
                                            }
                                        }
                                    }
                                }
                            }
                        }
                        //hpos
                        $v->set_taxes($_line_tax_data);

                        break;

                    case 'shipping':

                        //hpos
                        $amount = $v->get_total();

                        $val = $woocs->back_convert($amount, $_woocs_order_rate, $decimals);
                        if ($selected_currency !== $woocs->default_currency) {
                            $val = floatval($val) * floatval($currencies[$selected_currency]['rate']);
                        }
                        //hpos
                        $v->set_total($val);
                        //hpos
                        $taxes = $v->get_taxes();
                        if (!empty($taxes) AND is_array($taxes)) {
                            foreach ($taxes as $key => $values) {
                                if (!empty($values)) {
                                    if (is_array($values)) {
                                        foreach ($values as $k => $value) {
                                            if (is_numeric($value)) {
                                                $taxes[$key][$k] = $woocs->back_convert($value, $_woocs_order_rate, $decimals);
                                                if ($selected_currency !== $woocs->default_currency) {
                                                    $taxes[$key][$k] = floatval($taxes[$key][$k]) * floatval($currencies[$selected_currency]['rate']);
                                                }
                                            }
                                        }
                                    } else {
                                        if (is_numeric($values)) {
                                            $taxes[$key] = $woocs->back_convert($values, $_woocs_order_rate, $decimals);
                                            if ($selected_currency !== $woocs->default_currency) {
                                                $taxes[$key] = floatval($taxes[$key]) * floatval($currencies[$selected_currency]['rate']);
                                            }
                                        }
                                    }
                                }
                            }
                        }
                        //hpos
                        $v->set_taxes($taxes);
                        break;

                    case 'tax':
                        //hpos
                        $amount = $v->get_tax_total();
                        $val = $woocs->back_convert($amount, $_woocs_order_rate, 3);
                        if ($selected_currency !== $woocs->default_currency) {
                            $val = floatval($val) * floatval($currencies[$selected_currency]['rate']);
                        }
                        //hpos
                        $v->set_tax_total($val);

                        //hpos
                        $amount = $v->get_shipping_tax_total();
                        $val = $woocs->back_convert($amount, $_woocs_order_rate, $decimals);
                        if ($selected_currency !== $woocs->default_currency) {
                            $val = floatval($val) * floatval($currencies[$selected_currency]['rate']);
                        }
                        //hpos
                        $v->set_shipping_tax_total($val);

                        break;

                    default:
                        break;
                }
                $v->save();
            }
        }

//***

        $refunds = $order->get_refunds();
        $order->calculate_taxes();
        if (!empty($refunds)) {
            foreach ($refunds as $refund) {
                $post_id = 0;

                if (method_exists($refund, 'get_id')) {
                    $post_id = $refund->get_id();
                } else {
                    $post_id = $refund->id;
                }

                //hpos
                $amount = $refund->get_amount();
                $val = $woocs->back_convert($amount, $_woocs_order_rate, $decimals);
                if ($selected_currency !== $woocs->default_currency) {
                    $val = floatval($val) * floatval($currencies[$selected_currency]['rate']);
                }
                //hpos
                $refund->set_amount($val);

                //hpos
                $amount = $refund->get_total();
                $val = $woocs->back_convert($amount, $_woocs_order_rate, $decimals);
                if ($selected_currency !== $woocs->default_currency) {
                    $val = floatval($val) * floatval($currencies[$selected_currency]['rate']);
                }
                //hpos
                $refund->set_total($val);
                $refund->set_currency($selected_currency);
                $refund->save();
            }
        }
        $order->save();
    }

}
