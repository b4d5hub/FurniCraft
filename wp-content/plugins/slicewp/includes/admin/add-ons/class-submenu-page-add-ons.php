<?php

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;


Class SliceWP_Submenu_Page_Add_Ons extends SliceWP_Submenu_Page {

	/**
	 * Callback for the HTML output for the Add-ons page.
	 *
	 */
	public function output() {

		// Get cached add-ons.
		$remote_add_ons = get_option( 'slicewp_remote_add_ons', array() );

		$add_ons = $remote_add_ons;

		// Check if there are any values set. If there aren't pull from the server.
		if ( empty( $remote_add_ons['add_ons'] ) || $remote_add_ons['time_updated'] < time() - 2 * HOUR_IN_SECONDS ) {

			$add_ons = $this->remote_get_add_ons();

			if ( ! empty( $add_ons ) ) {
				update_option( 'slicewp_remote_add_ons', array( 'add_ons' => $add_ons, 'time_updated' => time() ) );
			}

		} else {

			$add_ons = $remote_add_ons['add_ons'];

		}

		if ( empty( $add_ons ) ) {
			$add_ons = $this->get_add_on_fallback_data();
		}

		// Sort add-ons.
		$add_ons_order = array(
			'affiliate-coupons',
			'custom-affiliate-fields',
			'paypal-payouts',
			'performance-bonuses',
			'multi-level-affiliates',
			'mailchimp-integration',
			'store-credit',
			'custom-affiliate-slug',
			'mailerlite-integration',
			'recurring-commissions',
			'lifetime-commissions',
			'convertkit-integration',
			'lead-commissions',
			'payout-requests',
			'affiliate-landing-pages',
			'product-commission-rates',
			'multi-currency',
			'reports',
			'affiliate-leaderboard',
			'affiliate-start-id',
			'cross-site-tracking',
			'import-export',
			'affiliate-social-share',
			'custom-conversion',
			'affiliate-commission-rates',
			'rest-api'
		);

		$_add_ons = array();

		foreach ( $add_ons_order as $add_on_slug ) {

			foreach ( $add_ons as $add_on ) {

				if ( empty( $add_on['slug'] ) ) {
					continue;
				}

				if ( $add_on['slug'] == $add_on_slug ) {
					$_add_ons[] = $add_on;
				}

			}

		}

		$add_ons = $_add_ons;

		// Display add-ons page.
		if ( empty( $this->current_subpage ) ) {
			include 'views/view-add-ons.php';
		}

	}


	/**
	 * Returns the fallback data that contains add-ons information.
	 * 
	 */
	protected function get_add_on_fallback_data() {

		$data = array(
			array(
				'name' => 'Payout Requests',
				'slug' => 'payout-requests',
				'description' => 'Allow your affiliates to request payouts for payable commissions from their account.',
				'url' => 'https://slicewp.com/products/payout-requests/',
				'date_added' => '2024-03-14 12:14:33',
			),
			array(
				'name' => 'Performance Bonuses',
				'slug' => 'performance-bonuses',
				'description' => 'Boost your sales by rewarding affiliates with special bonuses for reaching defined performance targets.',
				'url' => 'https://slicewp.com/products/performance-bonuses/',
				'date_added' => '2024-01-29 11:17:23',
			),
			array(
				'name' => 'Affiliate Landing Pages',
				'slug' => 'affiliate-landing-pages',
				'description' => 'Create dedicated landing pages for your affiliates, by linking posts and pages to affiliates.',
				'url' => 'https://slicewp.com/products/affiliate-landing-pages/',
				'date_added' => '2022-11-09 12:58:29',
			),
			array(
				'name' => 'Multi-level Affiliates',
				'slug' => 'multi-level-affiliates',
				'description' => 'Allow your affiliates to invite other people to join your affiliate program and be rewarded extra commissions for sales referred to you by the newly recruited affiliates.',
				'url' => 'https://slicewp.com/products/multi-level-affiliates/',
				'date_added' => '2022-10-11 14:33:46',
			),
			array(
				'name' => 'Affiliate Leaderboard',
				'slug' => 'affiliate-leaderboard',
				'description' => 'Show an affiliate leaderboard on your website to encourage your affiliates to make you more sales.',
				'url' => 'https://slicewp.com/products/affiliate-leaderboard/',
				'date_added' => '2022-06-08 09:18:21',
			),
			array(
				'name' => 'Store Credit for WooCommerce',
				'slug' => 'store-credit',
				'description' => 'Pay your affiliates with store credit, that they can then use to make discounted purchases from your WooCommerce store.',
				'url' => 'https://slicewp.com/products/store-credit/',
				'date_added' => '2022-03-23 14:40:24',
			),
			array(
				'name' => 'ConvertKit Integration',
				'slug' => 'convertkit-integration',
				'description' => 'Allow your affiliates to subscribe to your ConvertKit mailing list when they sign up for your affiliate program.',
				'url' => 'https://slicewp.com/products/convertkit-integration/',
				'date_added' => '2022-02-15 12:59:49',
			),
			array(
				'name' => 'MailerLite Integration',
				'slug' => 'mailerlite-integration',
				'description' => 'Allow your affiliates to subscribe to your MailerLite mailing list when they sign up for your affiliate program.',
				'url' => 'https://slicewp.com/products/mailerlite-integration/',
				'date_added' => '2022-02-09 11:31:49',
			),
			array(
				'name' => 'Mailchimp Integration',
				'slug' => 'mailchimp-integration',
				'description' => 'Allow your affiliates to subscribe to your Mailchimp mailing list when they sign up for your affiliate program.',
				'url' => 'https://slicewp.com/products/mailchimp-integration/',
				'date_added' => '2022-01-24 18:11:15',
			),
			array(
				'name' => 'Custom Affiliate Slug',
				'slug' => 'custom-affiliate-slug',
				'description' => 'Set custom slugs or generate random ones for your affiliates, which help customize the affiliate referral links.',
				'url' => 'https://slicewp.com/products/custom-affiliate-slug/',
				'date_added' => '2021-10-05 09:50:13',
			),
			array(
				'name' => 'Cross-site Tracking',
				'slug' => 'cross-site-tracking',
				'description' => 'Enables tracking of affiliate referrals from a different WordPress website to your main eCommerce WordPress website.',
				'url' => 'https://slicewp.com/products/cross-site-tracking/',
				'date_added' => '2021-08-31 09:23:34',
			),
			array(
				'name' => 'Affiliate Start ID',
				'slug' => 'affiliate-start-id',
				'description' => 'Enables you to set a custom starting ID for your affiliates.',
				'url' => 'https://slicewp.com/products/affiliate-start-id/',
				'date_added' => '2021-08-23 16:11:20',
			),
			array(
				'name' => 'Lead Commissions',
				'slug' => 'lead-commissions',
				'description' => 'Set up a pay-per-lead affiliate program with the Lead Commissions add-on for SliceWP. Reward affiliates for leads sent your way.',
				'url' => 'https://slicewp.com/products/lead-commissions/',
				'date_added' => '2021-07-15 12:28:20',
			),
			array(
				'name' => 'Data Export',
				'slug' => 'import-export',
				'description' => 'Export affiliates, commissions, affiliate payments and visits to a CSV file. Filter the exported data by affiliate, date and status.',
				'url' => 'https://slicewp.com/products/import-export/',
				'date_added' => '2021-06-29 12:59:42',
			),
			array(
				'name' => 'Lifetime Commissions',
				'slug' => 'lifetime-commissions',
				'description' => 'Enables you to reward commissions to your affiliates for all future purchases made by a customer.',
				'url' => 'https://slicewp.com/products/lifetime-commissions/',
				'date_added' => '2021-03-31 10:29:21',
			),
			array(
				'name' => 'Custom Affiliate Fields',
				'slug' => 'custom-affiliate-fields',
				'description' => 'Add custom fields to your affiliate registration and affiliate account settings forms.',
				'url' => 'https://slicewp.com/products/custom-affiliate-fields/',
				'date_added' => '2021-01-18 10:51:08',
			),
			array(
				'name' => 'Affiliate Social Share',
				'slug' => 'affiliate-social-share',
				'description' => 'Make it easier for your affiliates to share your products to their social media profiles by adding share button in their affiliate account.',
				'url' => 'https://slicewp.com/products/affiliate-social-share/',
				'date_added' => '2020-11-30 15:38:25',
			),
			array(
				'name' => 'Recurring Commissions',
				'slug' => 'recurring-commissions',
				'description' => 'Enables you to reward affiliates for recurring payments made by an active subscription.',
				'url' => 'https://slicewp.com/products/recurring-commissions/',
				'date_added' => '2020-06-03 11:52:48',
			),
			array(
				'name' => 'Product Commission Rates',
				'slug' => 'product-commission-rates',
				'description' => 'Overwrite global commission rates with custom rates for each individual product or subscription.',
				'url' => 'https://slicewp.com/products/product-commission-rates/',
				'date_added' => '2020-05-13 07:35:47',
			),
			array(
				'name' => 'Multi-currency',
				'slug' => 'multi-currency',
				'description' => 'Calculate affiliate commissions in the currency of your affiliate program for sales made in different currencies.',
				'url' => 'https://slicewp.com/products/multi-currency/',
				'date_added' => '2023-06-01 10:30:00',
			),
			array(
				'name' => 'Affiliate Coupons',
				'slug' => 'affiliate-coupons',
				'description' => 'Associate affiliates to coupon codes and track them to generate commissions for your affiliate partners.',
				'url' => 'https://slicewp.com/products/affiliate-coupons/',
				'date_added' => '2020-02-22 14:40:32',
			),
			array(
				'name' => 'Custom Conversion',
				'slug' => 'custom-conversion',
				'description' => 'Set up a custom conversion page to register commissions when using a checkout solution that doesn\'t integrate directly with SliceWP.',
				'url' => 'https://slicewp.com/products/custom-conversion/',
				'date_added' => '2020-01-23 21:07:14',
			),
			array(
				'name' => 'PayPal Payouts',
				'slug' => 'paypal-payouts',
				'description' => 'Pay affiliates in bulk directly from your WordPress administrator interface through PayPal\'s Payouts feature.',
				'url' => 'https://slicewp.com/products/paypal-payouts/',
				'date_added' => '2019-12-14 09:16:43',
			),
			array(
				'name' => 'Affiliate Commission Rates',
				'slug' => 'affiliate-commission-rates',
				'description' => 'Set custom commission rates, both percentage and fixed amounts, individually for each of your affiliates, to overwrite the global defaults.',
				'url' => 'https://slicewp.com/products/affiliate-commission-rates/',
				'date_added' => '2019-10-09 19:24:21',
			),
			array(
				'name' => 'Reports',
				'slug' => 'reports',
				'description' => 'Track the performance of your affiliate partners and monitor key metrics to help you improve your affiliate marketing program.',
				'url' => 'https://slicewp.com/products/reports/',
				'date_added' => '2019-10-09 19:24:02',
			),
			array(
				'name' => 'REST API',
				'slug' => 'rest-api',
				'description' => 'The REST API adds Get, Create, Update and Delete operations, offering developers and external applications control over the affiliate datasets.',
				'url' => 'https://slicewp.com/products/rest-api/',
				'date_added' => '2023-03-05 19:12:21',
			)

		);

		return $data;

	}


	/**
	 * Connects to the server to pull new information regarding add-ons.
	 * 
	 * @todo Maybe remove this method in the future, to no longer pull add-on information from website.
	 *
	 * @return array
	 *
	 */
	protected function remote_get_add_ons() {

		$add_ons  = array();
		$response = wp_remote_get( 'https://slicewp.com/wp-content/uploads/add-ons.json', array( 'sslverify' => false, 'timeout' => 15 ) );

		if ( ! is_wp_error( $response ) ) {

			$add_ons = json_decode( wp_remote_retrieve_body( $response ), true );

		}

		return $add_ons;

	}

}