<?php

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) exit;

?>

<!-- Register website -->
<div class="slicewp-card">

	<div class="slicewp-card-header">
		<span class="slicewp-card-title"><?php echo __( 'Register Website', 'slicewp' ); ?></span>
	</div>

	<div class="slicewp-card-inner">

		<!-- License Key -->
		<div class="slicewp-field-wrapper slicewp-field-wrapper-inline slicewp-last">

			<div class="slicewp-field-label-wrapper">
				<label for="slicewp-license-key">
					<?php echo __( 'License Key', 'slicewp' ); ?>
				</label>
			</div>

			<div class="slicewp-flex">

				<?php $license_key = get_option( 'slicewp_license_key', '' ); ?>

				<input id="slicewp-license-key" name="license_key" type="<?php echo ( empty( $license_key ) ? 'text' : 'password' ); ?>" value="<?php echo esc_attr( $license_key ); ?>">
				
				<a id="slicewp-register-license-key" class="slicewp-button-secondary" href="#">
					<span class="slicewp-register" <?php echo ( slicewp_is_website_registered() ? 'style="display: none;"' : '' ); ?>><?php echo __( 'Register', 'slicewp' ); ?></span>
					<span class="slicewp-deregister" <?php echo ( ! slicewp_is_website_registered() ? 'style="display: none;"' : '' ); ?>><?php echo __( 'Deregister', 'slicewp' ); ?></span>
					<div class="spinner"></div>
				</a>
				
			</div>

			<input id="slicewp-is-website-registered" type="hidden" value="<?php echo ( slicewp_is_website_registered() ? 'true' : 'false' ); ?>" />

			<?php if( ! slicewp_is_website_registered() ): ?>
				<div class="slicewp-field-notice slicewp-field-notice-warning" style="display: block;">
					<p><strong><?php echo __( 'Enter your license key.', 'slicewp' ); ?></strong> <?php echo sprintf( __( 'Your license key can be found in your %sSliceWP account%s.', 'slicewp' ), '<a href="https://slicewp.com/account/?utm_source=plugin-free&amp;utm_medium=plugin-card-license-key&amp;utm_campaign=SliceWPFree" target="_blank">', '</a>' ); ?></p>
					<p><?php echo sprintf( __( 'You can use this core version of SliceWP for free. For priority support and advanced functionality, %sa license key is required%s.', 'slicewp' ), '<a href="https://slicewp.com/pricing/?utm_source=plugin-free&amp;utm_medium=plugin-card-license-key&amp;utm_campaign=SliceWPFree" target="_blank">', '</a>' ); ?></p>
				</div>
			<?php endif; ?>

		</div><!-- / License Key -->

	</div>

</div>
<!-- / Register website -->


<!-- General Settings -->
<div class="slicewp-card">

	<div class="slicewp-card-header">
		<span class="slicewp-card-title"><?php echo __( 'General Settings', 'slicewp' ); ?></span>
	</div>

	<div class="slicewp-card-inner">

		<!-- Allow Affiliate Registration -->
		<div class="slicewp-field-wrapper slicewp-field-wrapper-inline">

			<div class="slicewp-field-label-wrapper">
				<label for="slicewp-allow-affiliate-registration">
					<?php echo __( 'Allow Affiliate Registration', 'slicewp' ); ?>
				</label>
			</div>

			<div class="slicewp-switch">

				<input id="slicewp-allow-affiliate-registration" class="slicewp-toggle slicewp-toggle-round" name="settings[allow_affiliate_registration]" type="checkbox" value="1" <?php checked( ! empty( $_POST['settings']['allow_affiliate_registration'] ) ? '1' : ( empty( $_POST ) ? slicewp_get_setting( 'allow_affiliate_registration' ) : '' ), '1' ); ?> />
				<label for="slicewp-allow-affiliate-registration"></label>

			</div>

			<label for="slicewp-allow-affiliate-registration"><?php echo __( 'Allow visitors to register as affiliates.', 'slicewp' ); ?></label>

		</div><!-- / Allow Affiliates Registration -->
		
		<!-- Register new Affiliates with Active status -->
		<div class="slicewp-field-wrapper slicewp-field-wrapper-inline">

			<div class="slicewp-field-label-wrapper">
				<label for="slicewp-affiliate-register-status-active">
					<?php echo __( 'Register Affiliates as Active', 'slicewp' ); ?>
				</label>
			</div>

			<div class="slicewp-switch">

				<input id="slicewp-affiliate-register-status-active" class="slicewp-toggle slicewp-toggle-round" name="settings[affiliate_register_status_active]" type="checkbox" value="1" <?php checked( ! empty( $_POST['settings']['affiliate_register_status_active'] ) ? '1' : ( empty( $_POST ) ? slicewp_get_setting( 'affiliate_register_status_active' ) : '' ), '1' ); ?> />
				<label for="slicewp-affiliate-register-status-active"></label>

			</div>

			<label for="slicewp-affiliate-register-status-active"><?php echo __( 'New affiliate accounts will be created with Active status.', 'slicewp' ); ?></label>

		</div><!-- / Register new Affiliates with Active status -->

		<!-- Auto Register Affiliates -->
		<div class="slicewp-field-wrapper slicewp-field-wrapper-inline">

			<div class="slicewp-field-label-wrapper">
				<label for="slicewp-auto-register-affiliates">
					<?php echo __( 'Auto Register Affiliates', 'slicewp' ); ?>
				</label>
			</div>

			<div class="slicewp-switch">

				<input id="slicewp-auto-register-affiliates" class="slicewp-toggle slicewp-toggle-round" name="settings[affiliate_auto_register]" type="checkbox" value="1" <?php checked( ! empty( $_POST['settings']['affiliate_auto_register'] ) ? '1' : ( empty( $_POST ) ? slicewp_get_setting( 'affiliate_auto_register' ) : '' ), '1' ); ?> />
				<label for="slicewp-auto-register-affiliates"></label>

			</div>

			<label for="slicewp-auto-register-affiliates"><?php echo __( 'Automatically register new user accounts as affiliates.', 'slicewp' ); ?></label>

		</div>
		<!-- / Auto Register Affiliates -->

		<!-- Cookie Duration -->
		<div class="slicewp-field-wrapper slicewp-field-wrapper-inline slicewp-tooltip-wide">

			<div class="slicewp-field-label-wrapper">
				<label for="slicewp-cookie-duration">
					<?php echo __( 'Tracking Cookie Duration', 'slicewp' ); ?>
					<?php echo slicewp_output_tooltip( '<p>' . __( 'The number of days a referred visitor is being tracked.' , 'slicewp' ) . '</p><p>' . __( 'If the referred visitor makes a purchase in this timeframe, the referring affiliate will be rewarded a commission.', 'slicewp' ) . '</p>' . '<hr />' . '<a href="https://slicewp.com/docs/cookie-duration/" target="_blank">' . __( 'Click here to learn more', 'slicewp' ) . '</a>' ); ?>
				</label>
			</div>

			<input id="slicewp-cookie-duration" name="settings[cookie_duration]" type="number" value="<?php echo ( ! empty( $_POST['settings']['cookie_duration'] ) ? esc_attr( $_POST['settings']['cookie_duration'] ) : ( slicewp_get_setting( 'cookie_duration' ) ) ); ?>">

		</div><!-- / Cookie Duration -->

		<?php
		
			/**
			 * Hook to add extra fields if needed to the General Settings card.
			 *
			 */
			do_action( 'slicewp_view_settings_section_general_settings_bottom' );
		
		?>

	</div>

</div><!-- / General Settings -->


<!-- Commissions Settings -->
<div id="slicewp-card-settings-commissions-settings" class="slicewp-card">

	<div class="slicewp-card-header">
		<span class="slicewp-card-title"><?php echo __( 'Commissions Settings', 'slicewp' ); ?></span>

		<div class="slicewp-card-actions">
			<a href="https://slicewp.com/docs/commission-rates/" target="_blank" class="slicewp-button-info" title="<?php echo esc_attr( __( 'Click to learn more...', 'slicewp' ) ); ?>"><svg height="18" width="18" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24"><g><path d="M13 9h-2V7h2v2zm0 2h-2v6h2v-6zm-1-7c-4.41 0-8 3.59-8 8s3.59 8 8 8 8-3.59 8-8-3.59-8-8-8m0-2c5.523 0 10 4.477 10 10s-4.477 10-10 10S2 17.523 2 12 6.477 2 12 2z"></path></g></svg></a>
		</div>
	</div>

	<div class="slicewp-card-inner">

		<?php 
			$commission_types = slicewp_get_available_commission_types();
		?>

		<!-- Commission Rates -->
		<?php foreach( $commission_types as $type => $details ): ?>

			<?php if( $type == 'recurring' || $type == 'lifetime_sale' ) continue; ?>

			<?php 
				$rate 	   = slicewp_get_setting( 'commission_rate_' . $type );
				$rate_type = slicewp_get_setting( 'commission_rate_type_' . $type );
			?>

			<div class="slicewp-field-wrapper slicewp-field-wrapper-inline slicewp-field-wrapper-commission-rate" style="display: none;">

				<div class="slicewp-field-label-wrapper">
					<label for="slicewp-commission-rate-<?php echo str_replace( '_', '-', $type ); ?>">
						<?php echo sprintf( __( '%s Rate', 'slicewp' ), $details['label'] ); ?>
					</label>
				</div>
				
				<input id="slicewp-commission-rate-<?php echo str_replace( '_', '-', $type ); ?>" name="settings[commission_rate_<?php echo $type; ?>]" type="number" step="any" min="0" value="<?php echo ( ! empty( $_POST['settings']['commission_rate_' . $type] ) ? esc_attr( $_POST['settings']['commission_rate_' . $type] ) : $rate) ?>" />					

				<select name="settings[commission_rate_type_<?php echo $type; ?>]" class="slicewp-select2" <?php echo ( count( $details['rate_types'] ) == 1 ? 'disabled' : '' ); ?>>
					<?php $currency_symbol = slicewp_get_currency_symbol( slicewp_get_setting( 'active_currency', 'USD' ) ); ?>
					<?php foreach( $details['rate_types'] as $details_rate_type ): ?>
						<option value="<?php echo esc_attr( $details_rate_type ); ?>" <?php selected( $rate_type, $details_rate_type ); ?>><?php echo ( $details_rate_type == 'percentage' ? __( 'Percentage (%)', 'slicewp' ) : __( 'Fixed Amount', 'slicewp' ) . ' (' . esc_attr( $currency_symbol ) . ')' ); ?></option>
					<?php endforeach; ?>
				</select>

			</div>

		<?php endforeach; ?>
		<!-- / Commission Rates -->

		<!-- Sale Fixed Amount Commission Basis -->
		<div class="slicewp-field-wrapper slicewp-field-wrapper-inline" style="display: none;">

			<div class="slicewp-field-label-wrapper">
				<label>
					<?php echo __( 'Sale Commission Basis', 'slicewp' ); ?>
				</label>
			</div>

			<select id="slicewp-fixed-amount-commission-basis" name="settings[commission_fixed_amount_rate_basis]" class="slicewp-select2">
				<option value="product" <?php echo ( slicewp_get_setting( 'commission_fixed_amount_rate_basis' ) == 'product' ? 'selected="selected"' : '' ); ?>><?php echo __( 'Fixed amount per product', 'slicewp' ); ?></option>
				<option value="order" <?php echo ( slicewp_get_setting( 'commission_fixed_amount_rate_basis' ) == 'order' ? 'selected="selected"' : '' ); ?>><?php echo __( 'Fixed amount per order', 'slicewp' ); ?></option>
			</select>

		</div>
		<!-- / Sale Fixed Amount Commission Basis -->

		<!-- Exclude Shipping -->
		<div class="slicewp-field-wrapper slicewp-field-wrapper-inline">

			<div class="slicewp-field-label-wrapper">
				<label for="slicewp-exclude-shipping">
					<?php echo __( 'Exclude Shipping', 'slicewp' ); ?>
					<?php echo slicewp_output_tooltip( __( 'The option is applicable only for WooCommerce and Easy Digital Downloads.', 'slicewp' ) ); ?>
				</label>
			</div>

			<div class="slicewp-switch">

				<input id="slicewp-exclude-shipping" class="slicewp-toggle slicewp-toggle-round" name="settings[exclude_shipping]" type="checkbox" value="1" <?php checked( ! empty( $_POST['settings']['exclude_shipping'] ) ? '1' : ( empty( $_POST ) ? slicewp_get_setting( 'exclude_shipping' ) : '' ), '1' ); ?> />
				<label for="slicewp-exclude-shipping"></label>

			</div>

			<label for="slicewp-exclude-shipping"><?php echo __( 'Exclude shipping costs from commission calculations.', 'slicewp' ); ?></label>

		</div><!-- / Exclude Shipping -->

		<!-- Exclude Tax -->
		<div class="slicewp-field-wrapper slicewp-field-wrapper-inline">

			<div class="slicewp-field-label-wrapper">
				<label for="slicewp-exclude-tax">
					<?php echo __( 'Exclude Taxes', 'slicewp' ); ?>
				</label>
			</div>

			<div class="slicewp-switch">

				<input id="slicewp-exclude-tax" class="slicewp-toggle slicewp-toggle-round" name="settings[exclude_tax]" type="checkbox" value="1" <?php checked( ! empty( $_POST['settings']['exclude_tax'] ) ? '1' : ( empty( $_POST ) ? slicewp_get_setting( 'exclude_tax' ) : '' ), '1' ); ?> />
				<label for="slicewp-exclude-tax"></label>

			</div>

			<label for="slicewp-exclude-tax"><?php echo __( 'Exclude taxes from commission calculations.', 'slicewp' ); ?></label>

		</div><!-- / Exclude Tax -->

		<!-- Reject Unpaid Commissions on Refund -->
		<div class="slicewp-field-wrapper slicewp-field-wrapper-inline">

			<div class="slicewp-field-label-wrapper">
				<label for="slicewp-reject-commissions-on-refund">
					<?php echo __( 'Reject Commissions on Refund', 'slicewp' ); ?>
				</label>
			</div>

			<div class="slicewp-switch">

				<input id="slicewp-reject-commissions-on-refund" class="slicewp-toggle slicewp-toggle-round" name="settings[reject_commissions_on_refund]" type="checkbox" value="1" <?php checked( ! empty( $_POST['settings']['reject_commissions_on_refund'] ) ? '1' : ( empty( $_POST ) ? slicewp_get_setting( 'reject_commissions_on_refund' ) : '' ), '1' ); ?> />
				<label for="slicewp-reject-commissions-on-refund"></label>

			</div>

			<label for="slicewp-reject-commissions-on-refund"><?php echo __( 'Mark unpaid commissions as rejected if the originating purchase is refunded.', 'slicewp' ); ?></label>

		</div><!-- / Reject Unpaid Commissions on Refund -->

		<!-- Zero Amount Commissions -->
		<div class="slicewp-field-wrapper slicewp-field-wrapper-inline">

			<div class="slicewp-field-label-wrapper">
				<label for="slicewp-zero-amount-commissions">
					<?php echo __( 'Zero Amount Commissions', 'slicewp' ); ?>
					<?php echo slicewp_output_tooltip( __( 'Enable the registration of commisions that have the total amount equal to zero. This is useful if you want to track conversions for fully discounted products.', 'slicewp' ) ); ?>
				</label>
			</div>

			<div class="slicewp-switch">

				<input id="slicewp-zero-amount-commissions" class="slicewp-toggle slicewp-toggle-round" name="settings[zero_amount_commissions]" type="checkbox" value="1" <?php checked( ! empty( $_POST['settings']['zero_amount_commissions'] ) ? '1' : ( empty( $_POST ) ? slicewp_get_setting( 'zero_amount_commissions' ) : '' ), '1' ); ?> />
				<label for="slicewp-zero-amount-commissions"></label>

			</div>

			<label for="slicewp-zero-amount-commissions"><?php echo __( 'Enable the registration of zero sum commisions.', 'slicewp' ); ?></label>

		</div><!-- Zero Amount Commissions -->
        
        <!-- Affiliate Own Commissions -->
		<div class="slicewp-field-wrapper slicewp-field-wrapper-inline">

			<div class="slicewp-field-label-wrapper">
				<label for="slicewp-affiliates-own-commissions">
					<?php echo __( 'Affiliate Own Commissions', 'slicewp' ); ?>
				</label>
			</div>

			<div class="slicewp-switch">

				<input id="slicewp-affiliates-own-commissions" class="slicewp-toggle slicewp-toggle-round" name="settings[affiliate_own_commissions]" type="checkbox" value="1" <?php checked( ! empty( $_POST['settings']['affiliate_own_commissions'] ) ? '1' : ( empty( $_POST ) ? slicewp_get_setting( 'affiliate_own_commissions' ) : '' ), '1' ); ?> />
				<label for="slicewp-affiliates-own-commissions"></label>

			</div>

			<label for="slicewp-affiliates-own-commissions"><?php echo __( 'Allow affiliates to earn commissions for their own orders.', 'slicewp' ); ?></label>

		</div><!-- / Affiliate Own Commissions -->
		
	</div>

</div><!-- / Commisions Settings -->

<!-- Payouts Settings -->
<div id="slicewp-card-payouts-settings" class="slicewp-card">

	<div class="slicewp-card-header">
		<span class="slicewp-card-title"><?php echo __( 'Payouts Settings', 'slicewp' ); ?></span>

		<div class="slicewp-card-actions">
			<a href="https://slicewp.com/docs/paying-your-affiliates/" target="_blank" class="slicewp-button-info" title="<?php echo esc_attr( __( 'Click to learn more...', 'slicewp' ) ); ?>"><svg height="18" width="18" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24"><g><path d="M13 9h-2V7h2v2zm0 2h-2v6h2v-6zm-1-7c-4.41 0-8 3.59-8 8s3.59 8 8 8 8-3.59 8-8-3.59-8-8-8m0-2c5.523 0 10 4.477 10 10s-4.477 10-10 10S2 17.523 2 12 6.477 2 12 2z"></path></g></svg></a>
		</div>
	</div>

	<div class="slicewp-card-inner">

		<!-- Default Payout Method -->
		<?php $payout_methods = slicewp_get_payout_methods(); ?>

		<?php if ( count( $payout_methods ) > 1 ): ?>

			<div class="slicewp-field-wrapper slicewp-field-wrapper-inline slicewp-tooltip-wide">

				<div class="slicewp-field-label-wrapper">
					<label for="slicewp-default-payout-method">
						<?php echo __( 'Default Payout Method', 'slicewp' ); ?>
					</label>
				</div>

				<select class="slicewp-select2" name="settings[default_payout_method]">

					<?php foreach ( $payout_methods as $method_slug => $method_data ): ?>
						<option value="<?php echo esc_attr( $method_slug ); ?>" <?php echo selected( ( ! empty( $_POST['settings']['default_payout_method'] ) ? $_POST['settings']['default_payout_method'] : ( empty( $_POST ) ? slicewp_get_setting( 'default_payout_method' ) : '' ) ) , $method_slug ); ?>><?php echo $method_data['label']; ?></option>
					<?php endforeach; ?>

				</select>

			</div>

		<?php else: ?>

			<input type="hidden" name="settings[default_payout_method]" value="<?php echo esc_attr( count( $payout_methods ) == 1 && array_key_exists( ( ! empty( $_POST['settings']['default_payout_method'] ) ? $_POST['settings']['default_payout_method'] : ( empty( $_POST ) ? slicewp_get_setting( 'default_payout_method' ) : 'manual' ) ), $payout_methods ) ? : 'manual' ); ?>" />
			
		<?php endif; ?>
		<!-- / Default Payout Method -->

		<!-- Payments Minimum Amount -->
		<div class="slicewp-field-wrapper slicewp-field-wrapper-inline slicewp-tooltip-wide">

			<div class="slicewp-field-label-wrapper">
				<label for="slicewp-payments-minimum-amount">
					<?php echo __( 'Payments Minimum Amount', 'slicewp' ); ?>
					<?php echo slicewp_output_tooltip( '<p>' . sprintf( __( 'Set a minimum commissions total amount that your affiliates need to reach to be eligible for payment.', 'slicewp' ) ) . '</p>' . '<hr />' . '<a href="https://slicewp.com/docs/paying-your-affiliates/#payments-minimum-amount" target="_blank">' . __( 'Click here to learn more', 'slicewp' ) . '</a>' ); ?>
				</label>
			</div>

			<div class="slicewp-field-currency-amount">
				<div class="slicewp-field-currency-symbol"><?php echo slicewp_get_currency_symbol( slicewp_get_setting( 'active_currency', 'USD' ) ); ?></div>
				<input id="slicewp-payments-minimum-amount" name="settings[payments_minimum_amount]" type="number" value="<?php echo( ! empty( $_POST['settings']['payments_minimum_amount'] ) ? esc_attr( $_POST['settings']['payments_minimum_amount'] ) : ( ! empty( slicewp_get_setting( 'payments_minimum_amount' ) ) ? slicewp_get_setting( 'payments_minimum_amount' ) : 0 ) ); ?>">
			</div>

		</div><!-- / Payments Minimum Amount -->

		<!-- Refund Grace Period -->
		<div class="slicewp-field-wrapper slicewp-field-wrapper-inline slicewp-tooltip-wide slicewp-last">

			<div class="slicewp-field-label-wrapper">
				<label for="slicewp-refund-grace-period">
					<?php echo __( 'Refund Grace Period', 'slicewp' ); ?>
					<?php echo slicewp_output_tooltip( '<p>' . sprintf( __( 'The grace period (set in number of days) is used when generating payouts for your affiliates. It helps you filter out commissions that could still be rejected due to a refund of the underlying purchase. We recommend you to set this equal to your store refund policy.', 'slicewp' ) ) . '</p>' . '<hr />' . '<a href="https://slicewp.com/docs/paying-your-affiliates/#refund-grace-period" target="_blank">' . __( 'Click here to learn more', 'slicewp' ) . '</a>' ); ?>
				</label>
			</div>

			<input id="slicewp-refund-grace-period" name="settings[commissions_grace_period]" type="number" value="<?php echo( ! empty( $_POST['settings']['commissions_grace_period'] ) ? esc_attr( $_POST['settings']['commissions_grace_period'] ) : ( ! empty( slicewp_get_setting( 'commissions_grace_period' ) ) ? slicewp_get_setting( 'commissions_grace_period' ) : 0 ) ); ?>">

		</div><!-- / Refund Grace Period -->

		<?php
		
			/**
			 * Hook to add extra fields if needed to the Payouts Settings card.
			 *
			 */
			do_action( 'slicewp_view_settings_section_payouts_settings_bottom' );
		
		?>

	</div>

</div><!-- / Payouts Settings -->

<!-- Affiliate Settings -->
<div class="slicewp-card">

	<?php $affiliate_keyword = ( ! empty( $_POST['settings']['affiliate_keyword'] ) ? $_POST['settings']['affiliate_keyword'] : ( empty( $_POST ) ? slicewp_get_setting( 'affiliate_keyword' ) : '' ) ); ?>

	<div class="slicewp-card-header">
		<span class="slicewp-card-title"><?php echo __( 'Affiliate URL Settings', 'slicewp' ); ?></span>

		<div class="slicewp-card-actions">
			<a href="https://slicewp.com/docs/affiliate-links/" target="_blank" class="slicewp-button-info" title="<?php echo esc_attr( __( 'Click to learn more...', 'slicewp' ) ); ?>"><svg height="18" width="18" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24"><g><path d="M13 9h-2V7h2v2zm0 2h-2v6h2v-6zm-1-7c-4.41 0-8 3.59-8 8s3.59 8 8 8 8-3.59 8-8-3.59-8-8-8m0-2c5.523 0 10 4.477 10 10s-4.477 10-10 10S2 17.523 2 12 6.477 2 12 2z"></path></g></svg></a>
		</div>
	</div>

	<div class="slicewp-card-inner">

		<!-- Affiliate Keyword -->
		<div class="slicewp-field-wrapper slicewp-field-wrapper-inline slicewp-tooltip-wide">

			<div class="slicewp-field-label-wrapper">
				<label for="slicewp-affiliate-keyword">
					<?php echo __( 'Affiliate Keyword', 'slicewp' ); ?>
					<?php echo slicewp_output_tooltip( '<p>' . __( 'The URL query parameter name used for affiliate identification.', 'slicewp' ) . '</p><p>' . sprintf( __( 'Example: %s', 'slicewp' ), '<code style="font-family: inherit;">' . trailingslashit( site_url() ) . '?' . '<strong>' . esc_html( $affiliate_keyword ) . '</strong>' . '=' . $affiliate_id ) . '</code>' . '</p>' . '<hr />' . '<a href="https://slicewp.com/docs/affiliate-links/" target="_blank">' . __( 'Click here to learn more', 'slicewp' ) . '</a>' ); ?>
				</label>
			</div>

			<input id="slicewp-affiliate-keyword" name="settings[affiliate_keyword]" type="text" value="<?php echo esc_attr( ! empty( $_POST['settings']['affiliate_keyword'] ) ? $_POST['settings']['affiliate_keyword'] : ( empty( $_POST ) ? slicewp_get_setting( 'affiliate_keyword' ) : '' ) ); ?>">

		</div><!-- / Affiliate Keyword -->

        <!-- Friendly Affiliate URLs -->
        <div class="slicewp-field-wrapper slicewp-field-wrapper-inline slicewp-tooltip-wide">

            <div class="slicewp-field-label-wrapper">
                <label for="slicewp-friendly-affiliate-urls">
                    <?php echo __( 'Friendly Affiliate URLs', 'slicewp' ); ?>
					<?php echo slicewp_output_tooltip( '<p>' . __( 'When enabled, the affiliate referral links will look like this:', 'slicewp' ) . '<br />' . '<code style="display: inline-block; margin-top: 3px; font-family: inherit;">' . untrailingslashit( site_url() ) . '<strong>' . '/' . '<span>' . esc_html( $affiliate_keyword ) . '</span>' . '/' . $affiliate_id . '/' . '</strong>' . '</code>' . '</p><p>' . __( 'Instead of this:', 'slicewp' ) . '<br />' . '<code style="display: inline-block; margin-top: 3px; font-family: inherit;">' . untrailingslashit( site_url() ) . '<strong>' . '/?' . '<span>' . esc_html( $affiliate_keyword ) . '</span>' . '=' . $affiliate_id . '</strong>' . '</code>' . '</p>' . '<hr />' . '<a href="https://slicewp.com/docs/affiliate-links/" target="_blank">' . __( 'Click here to learn more', 'slicewp' ) . '</a>' ); ?>
                </label>
            </div>

            <div class="slicewp-switch">

                <input id="slicewp-friendly-affiliate-urls" class="slicewp-toggle slicewp-toggle-round" name="settings[friendly_affiliate_url]" type="checkbox" value="1" <?php checked( ! empty( $_POST['settings']['friendly_affiliate_url'] ) ? '1' : ( empty( $_POST ) ? slicewp_get_setting( 'friendly_affiliate_url' ) : '' ), '1' ); ?> />
                <label for="slicewp-friendly-affiliate-urls"></label>

            </div>

            <label for="slicewp-friendly-affiliate-urls"><?php echo __( 'Use friendly affiliate URLs.', 'slicewp' ); ?></label>

        </div><!-- / Friendly Affiliate URLs -->

		<!-- Credit First/Last Affiliate -->
		<div class="slicewp-field-wrapper slicewp-field-wrapper-inline">

			<div class="slicewp-field-label-wrapper">
				<label>
					<?php echo __( 'Credit First/Last Affiliate', 'slicewp' ); ?>
				</label>
			</div>

			<select id="slicewp-affiliate-credit" name="settings[affiliate_credit]" class="slicewp-select2">
				<option value="first" <?php echo selected( ( ! empty( $_POST['settings']['affiliate_credit'] ) ? $_POST['settings']['affiliate_credit'] : ( empty( $_POST ) ? slicewp_get_setting( 'affiliate_credit' ) : '' ) ) , 'first' ); ?>><?php echo __( 'First Affiliate', 'slicewp' ); ?></option>
				<option value="last" <?php echo selected( ( ! empty( $_POST['settings']['affiliate_credit'] ) ? $_POST['settings']['affiliate_credit'] : ( empty( $_POST ) ? slicewp_get_setting( 'affiliate_credit' ) : '' ) ) , 'last' ); ?>><?php echo __( 'Last Affiliate', 'slicewp' ); ?></option>
			</select>

		</div><!-- / Credit First/Last Affiliate -->

		<!-- Referral Links QR Code -->
        <div class="slicewp-field-wrapper slicewp-field-wrapper-inline slicewp-last">

            <div class="slicewp-field-label-wrapper">
                <label for="slicewp-referral-link-qr-code">
                    <?php echo __( 'Affiliate Link QR Code', 'slicewp' ); ?>
                    <?php echo slicewp_output_tooltip( __( 'When enabled, your affiliates will have the option to view and download the QR code for their affiliate referral links.', 'slicewp' ) ); ?>
                </label>
            </div>

            <div class="slicewp-switch">

                <input id="slicewp-referral-link-qr-code" class="slicewp-toggle slicewp-toggle-round" name="settings[referral_link_qr_code]" type="checkbox" value="1" <?php checked( ! empty( $_POST['settings']['referral_link_qr_code'] ) ? '1' : ( empty( $_POST ) ? slicewp_get_setting( 'referral_link_qr_code' ) : '' ), '1' ); ?> />
                <label for="slicewp-referral-link-qr-code"></label>

            </div>

            <label for="slicewp-referral-link-qr-code"><?php echo __( 'Show QR code for affiliate URLs in the affiliate account.', 'slicewp' ); ?></label>

        </div><!-- Referral Links QR Code -->

	</div>

</div><!-- / Affiliate Settings -->


<!-- Currency Settings -->
<div id="slicewp-card-settings-currency" class="slicewp-card">

	<div class="slicewp-card-header">
		<span class="slicewp-card-title"><?php echo __( 'Currency Settings', 'slicewp' ); ?></span>
	</div>

	<div class="slicewp-card-inner">

		<!-- Currency -->
		<div class="slicewp-field-wrapper slicewp-field-wrapper-inline">

			<div class="slicewp-field-label-wrapper">
				<label for="slicewp-active-currency">
					<?php echo __( 'Currency', 'slicewp' ); ?>
				</label>
			</div>

			<select id="slicewp-active-currency" name="settings[active_currency]" class="slicewp-select2">
				<?php foreach( slicewp_get_currencies() as $currency_code => $currency_name ): ?>
					<?php $currency_symbol = slicewp_get_currency_symbol( $currency_code ); ?>
					<option value="<?php echo esc_attr( $currency_code ); ?>" <?php echo selected( ! empty( $_POST['settings']['active_currency'] ) ? $_POST['settings']['active_currency'] : ( empty( $_POST ) ? slicewp_get_setting( 'active_currency' ) : '' ), $currency_code ); ?>><?php echo esc_attr( $currency_name ) . ( ! empty( $currency_symbol ) ? ( ' (' . $currency_symbol . ')' ) : '' ); ?></option>
				<?php endforeach; ?>
			</select>

		</div><!-- / Currency -->

		<!-- Currency Symbol Position -->
		<div class="slicewp-field-wrapper slicewp-field-wrapper-inline">

			<div class="slicewp-field-label-wrapper">
				<label for="slicewp-currency-symbol-position">
					<?php echo __( 'Currency Symbol Position', 'slicewp' ); ?>
					<?php echo slicewp_output_tooltip( __( 'The position of the currency symbol in relation with the amount value, when displaying amounts.', 'slicewp' ) ); ?>
				</label>
			</div>
			
			<select id="slicewp-currency-symbol-position" name="settings[currency_symbol_position]" class="slicewp-select2">
				<option value="before" <?php echo selected( ( ! empty( $_POST['settings']['currency_symbol_position'] ) ? $_POST['settings']['currency_symbol_position'] : ( empty( $_POST ) ? slicewp_get_setting( 'currency_symbol_position' ) : '' ) ) , 'before' ); ?>><?php echo __( 'Before amount', 'slicewp' ); ?></option>
				<option value="after" <?php echo selected( ( ! empty( $_POST['settings']['currency_symbol_position'] ) ? $_POST['settings']['currency_symbol_position'] : ( empty( $_POST ) ? slicewp_get_setting( 'currency_symbol_position' ) : '' ) ) , 'after' ); ?>><?php echo __( 'After amount', 'slicewp' ); ?></option>
				<option value="before_space" <?php echo selected( ( ! empty( $_POST['settings']['currency_symbol_position'] ) ? $_POST['settings']['currency_symbol_position'] : ( empty( $_POST ) ? slicewp_get_setting( 'currency_symbol_position' ) : '' ) ) , 'before_space' ); ?>><?php echo __( 'Before amount with space', 'slicewp' ); ?></option>
				<option value="after_space" <?php echo selected( ( ! empty( $_POST['settings']['currency_symbol_position'] ) ? $_POST['settings']['currency_symbol_position'] : ( empty( $_POST ) ? slicewp_get_setting( 'currency_symbol_position' ) : '' ) ) , 'after_space' ); ?>><?php echo __( 'After amount with space', 'slicewp' ); ?></option>
			</select>

		</div><!-- / Currency Symbol Position -->

		<!-- Thousands Separator -->
		<div class="slicewp-field-wrapper slicewp-field-wrapper-inline">

			<div class="slicewp-field-label-wrapper">
				<label for="slicewp-currency-thousands-separator">
					<?php echo __( 'Thousands Separator', 'slicewp' ); ?>
					<?php echo slicewp_output_tooltip( __( 'The symbol to separate thousands. This is usually a , (comma) or a . (dot).', 'slicewp' ) ); ?>
				</label>
			</div>

			<input id="slicewp-currency-thousands-separator" name="settings[currency_thousands_separator]" type="text" value="<?php echo esc_attr( ! empty( $_POST['settings']['currency_thousands_separator'] ) ? $_POST['settings']['currency_thousands_separator'] : ( empty( $_POST ) ? slicewp_get_setting( 'currency_thousands_separator' ) : '' ) ); ?>">

		</div><!-- / Thousands Separator -->

		<!-- Decimal Separator -->
		<div class="slicewp-field-wrapper slicewp-field-wrapper-inline slicewp-last">

			<div class="slicewp-field-label-wrapper">
				<label for="slicewp-currency-decimal-separator">
					<?php echo __( 'Decimal Separator', 'slicewp' ); ?>
					<?php echo slicewp_output_tooltip( __( 'The symbol to separate decimal points. This is usually a , (comma) or a . (dot).', 'slicewp' ) ); ?>
				</label>
			</div>

			<input id="slicewp-currency-decimal-separator" name="settings[currency_decimal_separator]" type="text" value="<?php echo esc_attr( ! empty( $_POST['settings']['currency_decimal_separator'] ) ? $_POST['settings']['currency_decimal_separator'] : ( empty( $_POST ) ? slicewp_get_setting( 'currency_decimal_separator' ) : '' ) ); ?>">

		</div><!-- / Decimal Separator -->

	</div>

</div><!-- / Currency Settings -->


<!-- Pages Settings -->
<div class="slicewp-card">

	<div class="slicewp-card-header">
		<span class="slicewp-card-title"><?php echo __( 'Pages Settings', 'slicewp' ); ?></span>

		<div class="slicewp-card-actions">
			<a href="https://slicewp.com/docs/category/affiliate-area/" target="_blank" class="slicewp-button-info" title="<?php echo esc_attr( __( 'Click to learn more...', 'slicewp' ) ); ?>"><svg height="18" width="18" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24"><g><path d="M13 9h-2V7h2v2zm0 2h-2v6h2v-6zm-1-7c-4.41 0-8 3.59-8 8s3.59 8 8 8 8-3.59 8-8-3.59-8-8-8m0-2c5.523 0 10 4.477 10 10s-4.477 10-10 10S2 17.523 2 12 6.477 2 12 2z"></path></g></svg></a>
		</div>
	</div>

	<div class="slicewp-card-inner">

		<!-- Affiliate Account Page -->
		<div class="slicewp-field-wrapper slicewp-field-wrapper-inline">

			<div class="slicewp-field-label-wrapper">
				<label for="slicewp-affiliate-account-page">
					<?php echo __( 'Affiliate Account Page', 'slicewp' ); ?>
					<?php echo slicewp_output_tooltip( __( "Select the page you wish to be your affiliates' private area.", 'slicewp' ) . '<hr />' . '<a href="https://slicewp.com/docs/adding-an-affiliate-account-page/" target="_blank">' . __( 'Click here to learn more', 'slicewp' ) . '</a>' ); ?>
				</label>
			</div>

			<select id="slicewp-affiliate-account-page" name="settings[page_affiliate_account]" class="slicewp-select2">
				<option value=""><?php echo( __( 'Select...', 'slicewp' ) ); ?></option>

				<?php

					$pages = get_pages();
					foreach( $pages as $page )
						echo '<option value="' . absint( $page->ID ) . '"' . selected( ! empty( $_POST['settings']['page_affiliate_account'] ) ? absint( $_POST['settings']['page_affiliate_account'] ) : ( empty( $_POST ) ? slicewp_get_setting( 'page_affiliate_account' ) : '' ), $page->ID ) . '>' . $page->post_title . '</option>';

				?>
			</select>

		</div><!-- / Affiliate Account Page -->

		<!-- Affiliate Registration Page -->
		<div class="slicewp-field-wrapper slicewp-field-wrapper-inline">

			<div class="slicewp-field-label-wrapper">
				<label for="slicewp-affiliate-register-page">
					<?php echo __( 'Affiliate Registration Page', 'slicewp' ); ?>
					<?php echo slicewp_output_tooltip( __( "Select the page where your visitors can register for your affiliate program.", 'slicewp' ) . '<hr />' . '<a href="https://slicewp.com/docs/adding-affiliate-registration-page/" target="_blank">' . __( 'Click here to learn more', 'slicewp' ) . '</a>' ); ?>
				</label>
			</div>

			<select id="slicewp-affiliate-register-page" name="settings[page_affiliate_register]" class="slicewp-select2">
				<option value=""><?php echo( __( 'Select...', 'slicewp' ) ); ?></option>
				
				<?php

					$pages = get_pages();
					foreach( $pages as $page )
						echo '<option value="' . absint( $page->ID ) . '"' . selected( ! empty( $_POST['settings']['page_affiliate_register'] ) ? absint( $_POST['settings']['page_affiliate_register'] ) : ( empty( $_POST ) ? slicewp_get_setting( 'page_affiliate_register' ) : '' ), $page->ID ) . '>' . $page->post_title . '</option>';

				?>
			</select>

		</div><!-- / Affiliate Registration Page -->

		<!-- Reset Password Page -->
		<div class="slicewp-field-wrapper slicewp-field-wrapper-inline">

			<div class="slicewp-field-label-wrapper">
				<label for="slicewp-reset-password-page">
					<?php echo __( 'Reset Password Page', 'slicewp' ); ?>
					<?php echo slicewp_output_tooltip( __( 'Select the page where your affiliates can reset their password in case they lost it.', 'slicewp' ) ); ?>
				</label>
			</div>

			<select id="slicewp-reset-password-page" name="settings[page_affiliate_reset_password]" class="slicewp-select2">
				<option value=""><?php echo( __( 'Select...', 'slicewp' ) ); ?></option>

				<?php

					$pages = get_pages();
					foreach( $pages as $page )
						echo '<option value="' . absint( $page->ID ) . '"' . selected( ! empty( $_POST['settings']['page_affiliate_reset_password'] ) ? absint( $_POST['settings']['page_affiliate_reset_password'] ) : ( empty( $_POST ) ? slicewp_get_setting( 'page_affiliate_reset_password' ) : '' ), $page->ID ) . '>' . $page->post_title . '</option>';

				?>
			</select>

		</div><!-- / Reset Password Page -->

		<!-- Terms and Conditions Page -->
		<div class="slicewp-field-wrapper slicewp-field-wrapper-inline">

			<div class="slicewp-field-label-wrapper">
				<label for="slicewp-terms-conditions-page">
					<?php echo __( 'Terms and Conditions Page', 'slicewp' ); ?>
				</label>
			</div>

			<select id="slicewp-terms-conditions-page" name="settings[page_terms_conditions]" class="slicewp-select2">
				<option value=""><?php echo( __( 'Select...', 'slicewp' ) ); ?></option>

				<?php

					$pages = get_pages();
					foreach( $pages as $page )
						echo '<option value="' . absint( $page->ID ) . '"' . selected( ! empty( $_POST['settings']['page_terms_conditions'] ) ? absint( $_POST['settings']['page_terms_conditions'] ) : ( empty( $_POST ) ? slicewp_get_setting( 'page_terms_conditions' ) : '' ), $page->ID ) . '>' . $page->post_title . '</option>';

				?>
			</select>

		</div><!-- / Terms and Conditions Page -->

		<!-- Terms and Conditions Checkbox -->
		<div class="slicewp-field-wrapper slicewp-field-wrapper-inline">

			<div class="slicewp-field-label-wrapper">
				<label for="slicewp-terms-label">
					<?php echo __( 'Terms and Conditions Label', 'slicewp' ); ?>
					<?php echo slicewp_output_tooltip( __( 'This label will acompanion the Terms and Conditions checkbox.', 'slicewp' ) ); ?>
				</label>
			</div>

			<input id="slicewp-terms-label" name="settings[terms_label]" type="text" value="<?php echo esc_attr( ! empty( $_POST['settings']['terms_label'] ) ? $_POST['settings']['terms_label'] : ( empty( $_POST ) ? slicewp_get_setting( 'terms_label' ) : '' ) ); ?>">
			
		</div><!-- / Terms and Conditions Checkbox -->

		<!-- Affiliate Account Logout -->
		<div class="slicewp-field-wrapper slicewp-field-wrapper-inline">

			<div class="slicewp-field-label-wrapper">
				<label for="slicewp-affiliate-account-logout">
					<?php echo __( 'Affiliate Account Logout', 'slicewp' ); ?>
				</label>
			</div>

			<div class="slicewp-switch">

				<input id="slicewp-affiliate-account-logout" class="slicewp-toggle slicewp-toggle-round" name="settings[affiliate_account_logout]" type="checkbox" value="1" <?php checked( ! empty( $_POST['settings']['affiliate_account_logout'] ) ? esc_attr( $_POST['settings']['affiliate_account_logout'] ) : ( empty( $_POST ) ? slicewp_get_setting( 'affiliate_account_logout' ) : '' ), '1' ); ?> />
				<label for="slicewp-affiliate-account-logout"></label>

			</div>

			<label for="slicewp-affiliate-account-logout"><?php echo __( 'Enable to add a logout link in the affiliate account page.', 'slicewp' ); ?></label>

		</div><!-- Affiliate Account Logout -->

		<!-- Affiliate Default Referral URL -->
		<div class="slicewp-field-wrapper slicewp-field-wrapper-inline">

			<div class="slicewp-field-label-wrapper">
				<label for="slicewp-affiliate-referral-url">
					<?php echo __( 'Affiliate Default Referral URL', 'slicewp' ); ?>
					<?php echo slicewp_output_tooltip( __( "The default affiliate referral URL shown in the affiliate account. If left empty, your website's home URL will be used.", 'slicewp' ) ); ?>
				</label>
			</div>

			<input id="slicewp-affiliate-referral-url" name="settings[affiliate_url_base]" type="url" value="<?php echo esc_attr( ! empty( $_POST['settings']['affiliate_url_base'] ) ? $_POST['settings']['affiliate_url_base'] : ( empty( $_POST ) ? slicewp_get_setting( 'affiliate_url_base' ) : '' ) ); ?>">
			
		</div><!-- / Affiliate Default Referral URL -->

		<!-- Required registration fields -->
		<div class="slicewp-field-wrapper slicewp-field-wrapper-inline slicewp-last">

			<div class="slicewp-field-label-wrapper">
				<label>
					<?php echo __( 'Required Affiliate Fields', 'slicewp' ); ?>
					<?php echo slicewp_output_tooltip( __( 'Set which fields should be required for the affiliate to complete on the registration and affiliate account pages.', 'slicewp' ) ); ?>
				</label>
			</div>

			<div style="margin-bottom: 10px;">

				<div class="slicewp-switch">

					<input id="slicewp-required-field-payment-email" class="slicewp-toggle slicewp-toggle-round" name="settings[required_field_payment_email]" type="checkbox" value="1" <?php checked( ! empty( $_POST['settings']['required_field_payment_email'] ) ? '1' : ( empty( $_POST ) ? slicewp_get_setting( 'required_field_payment_email' ) : '' ), '1' ); ?> />
					<label for="slicewp-required-field-payment-email"></label>

				</div>

				<label for="slicewp-required-field-payment-email"><?php echo __( 'Payment Email', 'slicewp' ); ?></label>							

			</div>

			<div style="margin-bottom: 10px;">

				<div class="slicewp-switch">

					<input id="slicewp-required-field-website" class="slicewp-toggle slicewp-toggle-round" name="settings[required_field_website]" type="checkbox" value="1" <?php checked( ! empty( $_POST['settings']['required_field_website'] ) ? '1' : ( empty( $_POST ) ? slicewp_get_setting( 'required_field_website' ) : '' ), '1' ); ?> />
					<label for="slicewp-required-field-website"></label>

				</div>

				<label for="slicewp-required-field-website"><?php echo __( 'Website', 'slicewp' ); ?></label>							
			
			</div>

			<div style="margin-bottom: 10px;">

				<div class="slicewp-switch">

					<input id="slicewp-required-field-promotional-methods" class="slicewp-toggle slicewp-toggle-round" name="settings[required_field_promotional_methods]" type="checkbox" value="1" <?php checked( ! empty( $_POST['settings']['required_field_promotional_methods'] ) ? '1' : ( empty( $_POST ) ? slicewp_get_setting( 'required_field_promotional_methods' ) : '' ), '1' ); ?> />
					<label for="slicewp-required-field-promotional-methods"></label>

				</div>

				<label for="slicewp-required-field-promotional-methods"><?php echo __( 'How will you promote us?', 'slicewp' ); ?></label>							
			
			</div>

		</div>
		<!-- / Required registration fields -->

	</div>

</div><!-- / Pages Settings -->

<?php 

	/**
	 * Hook to add extra cards if needed to the General Settings tab
	 *
	 */
	do_action( 'slicewp_view_settings_tab_general_bottom' );

	/**
	 * Hook to add extra cards if needed to the General Settings tab
	 *
	 * @deprecated 1.0.12 - No longer used in core and not recommended for external usage.
	 * 					    Replaced by "slicewp_view_settings_tab_general_bottom" action.
	 *					    Slated for removal in version 2.0.0
	 *
	 */
	do_action( 'slicewp_view_settings_tab_bottom_general' );

?>

<!-- Save Settings Button -->
<input type="submit" class="slicewp-form-submit slicewp-button-primary" value="<?php echo __( 'Save Settings', 'slicewp' ); ?>" />