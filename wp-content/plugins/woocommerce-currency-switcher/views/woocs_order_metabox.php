<?php if (!defined('ABSPATH')) die('No direct access allowed'); ?>

<?php
$currencies = $this->get_currencies();
//hpos
//$rate = get_post_meta($post->ID, '_woocs_order_rate', TRUE);
//$currency = get_post_meta($post->ID, '_order_currency', TRUE);
//$base_currency = get_post_meta($post->ID, '_woocs_order_base_currency', TRUE);
//$changed_mannualy = get_post_meta($post->ID, '_woocs_order_currency_changed_mannualy', TRUE);

$rate = $order->get_meta( '_woocs_order_rate',true);
$currency = $order->get_currency();
$base_currency = $order->get_meta( '_woocs_order_base_currency',true);
$changed_mannualy = $order->get_meta( '_woocs_order_currency_changed_mannualy',true);

if (empty($base_currency)) {
    $base_currency = $this->default_currency;
}
?>

<div id="woocs_order_metabox" >
    <strong><?php esc_html_e('Order currency', 'woocommerce-currency-switcher') ?></strong>: 
    <span class="woocs_order_currency">
        <i><?php echo esc_html($currency) ?></i>
        <select name="woocs_order_currency2" class="woocs_settings_hide" >
            <?php foreach ($currencies as $key => $curr) : ?>
                <option value="<?php echo esc_attr($key) ?>"><?php echo esc_html($curr['name']) ?></option>
            <?php endforeach; ?>
        </select>
    </span>&nbsp;<span class="tips" data-tip="<?php esc_html_e('Currency in which the customer paid.', 'woocommerce-currency-switcher') ?><?php if ($changed_mannualy > 0): ?> <?php printf(esc_html__('THIS order currency is changed manually %s!', 'woocommerce-currency-switcher'), esc_html(date('d-m-Y', $changed_mannualy))) ?><?php endif; ?>">[?]</span><br />
    <strong><?php esc_html_e('Base currency', 'woocommerce-currency-switcher') ?></strong>: <?php echo esc_html($base_currency) ?><br />
    <strong><?php esc_html_e('Order currency rate', 'woocommerce-currency-switcher') ?></strong>: <?php echo esc_html($rate) ?>&nbsp;<span class="tips" data-tip="<?php esc_html_e('Currency rate when the customer paid', 'woocommerce-currency-switcher') ?>"> [?]</span><br />
    <strong><?php esc_html_e('Total amount', 'woocommerce-currency-switcher') ?></strong>: 
    <?php
    $_REQUEST['no_woocs_order_amount_total'] = 1;
    echo esc_html(trim(number_format($order->get_total(), $this->price_num_decimals) . ' ' . $currency));
    ?><br />
    <hr />

    <?php if ($this->default_currency === $base_currency): ?>
        <a href="javascript:woocs_change_order_data();void(0);" class="button woocs_change_order_curr_button"><?php esc_html_e('Change order currency', 'woocommerce-currency-switcher') ?>&nbsp;<img class="help_tip" data-tip="<?php esc_html_e('Change the order currency to any other', 'woocommerce-currency-switcher') ?>" src="<?php echo esc_attr(WOOCS_LINK) ?>/img/help.png" height="16" width="16" /></a>
    <?php else: ?>
        <?php echo esc_html(sprintf(esc_html__('Not possible to recalculate the order - because current base currency is %s, and order base currency is %s!', 'woocommerce-currency-switcher'), $this->default_currency, $base_currency)) ?>
    <?php endif; ?>

    <a href="javascript:woocs_cancel_order_data();void(0);" class="button woocs_cancel_order_curr_button" style="display: none;"><?php esc_html_e('cancel', 'woocommerce-currency-switcher') ?></a>&nbsp;
    <a data-order_id="<?php /*hpos*/echo esc_attr($order->get_id()) ?>" href="javascript:woocs_recalculate_order_data();void(0);" style="display: none;" class="button woocs_recalculate_order_curr_button"><?php esc_html_e("Recalculate order", 'woocommerce-currency-switcher') ?>&nbsp;<img class="help_tip" data-tip="<?php esc_html_e('Recalculate current order with the selected currency.', 'woocommerce-currency-switcher') ?>" src="<?php echo esc_attr(WOOCS_LINK) ?>/img/help.png" height="16" width="16" /></a><br />
	<hr>
	<div class="woocs_update_order_rate_container">
		<p><?php esc_html_e('Rate update field. Be careful with this setting', 'woocommerce-currency-switcher') ?></p>
		<label for="woocs_toogle_update_order"><?php esc_html_e('Open/Close', 'woocommerce-currency-switcher') ?></label>
		<input id="woocs_toogle_update_order" type="checkbox" style="display:none;">
		<p class="woocs_toogle_update_order_hidden" >
		<?php 
		$current_rate = 1;
		if (isset($currencies[$currency]) && $currency != $this->default_currency) {
			$current_rate = $currencies[$currency]['rate'];
		}
		?>
			<input type="text" value="<?php echo esc_attr($current_rate) ?>" name='woocs_current_rate'>
			<a href="javascript:woocs_update_order_rate();void(0);" class="button woocs_update_order_curr_button" ><?php esc_html_e('Update rate', 'woocommerce-currency-switcher') ?></a>
		</p>
	</div>

</div>


