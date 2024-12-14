<?php

if (!defined('ABSPATH')) {
	exit;
}

add_filter('sumosubscriptions_get_subscription_price', function($subscription_price) {
	if (class_exists('WOOCS')) {
		global $WOOCS;
		if ($WOOCS->is_multiple_allowed) {
			$currrent = $WOOCS->current_currency;
			if ($currrent != $WOOCS->default_currency) {
				$currencies = $WOOCS->get_currencies();
				$rate = $currencies[$currrent]['rate'];
				$subscription_price = $subscription_price / $rate;
			}
		}
	}

	return $subscription_price;
});
add_filter('sumosubscriptions_alter_subscription_plan', function($subscription_plan) {
	if (class_exists('WOOCS')) {
		global $WOOCS;
		$subscription_plan['signup_fee'] = $WOOCS->woocs_exchange_value(floatval($subscription_plan['signup_fee']));
		$subscription_plan['trial_fee'] = $WOOCS->woocs_exchange_value(floatval($subscription_plan['trial_fee']));
	}

	return $subscription_plan;
});

