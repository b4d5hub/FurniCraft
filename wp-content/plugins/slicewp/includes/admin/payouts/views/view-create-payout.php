<?php

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) exit;

$request = ( ! empty( $_POST ) ? $_POST : $_GET );

?>

<div class="wrap slicewp-wrap slicewp-wrap-payments">

	<form method="POST">

		<!-- Hidden fields needed at submit -->
		<input type="hidden" name="page" value="slicewp-payouts">
		<input type="hidden" name="subpage" value="preview-payout">

		<!-- Page Heading -->
		<h1 class="wp-heading-inline"><?php echo __( 'Create Payout', 'slicewp' ); ?></h1>
		<hr class="wp-header-end" />
        
		<!-- Postbox -->
		<div class="slicewp-card">

			<div class="slicewp-card-header">
				<span class="slicewp-card-title"><?php echo __( 'Payout Details', 'slicewp' ); ?></span>
				<p class="slicewp-card-header-subheading"><?php echo __( 'Filter which unpaid commissions you wish to pay.', 'slicewp' ); ?></p>
			</div>

			<div class="slicewp-card-inner">

				<!-- Date range -->
				<div class="slicewp-field-wrapper slicewp-field-wrapper-inline" style="margin-bottom: 10px;">

					<div class="slicewp-field-label-wrapper">
						<label for="slicewp-payout-date-range"><?php echo __( 'Included Commissions', 'slicewp' ); ?></label>
					</div>
					
					<select id="slicewp-payout-date-range" name="date_range" class="slicewp-select2">
						<option value="up_to" <?php echo ( ( ! empty( $request['date_range'] ) && $request['date_range'] == 'up_to' ) ? 'selected="selected"' : '' ); ?>><?php echo __( 'Unpaid commissions up to...', 'slicewp' ); ?></option>
						<option value="custom_range" <?php echo ( ( ! empty( $request['date_range'] ) && $request['date_range'] == 'custom_range' ) ? 'selected="selected"' : '' ); ?>><?php echo __( 'Unpaid commission from custom range...', 'slicewp' ); ?></option>
					</select>

				</div>

				<!-- Date up to -->
				<div class="slicewp-field-wrapper slicewp-field-wrapper-inline">

					<div class="slicewp-datepicker-wrapper slicewp-icon-left">
						<?php echo slicewp_get_svg( 'outline-calendar' ); ?>
						<input id="slicewp-date-up-to" type="text" name="date_up_to" class="slicewp-datepicker" autocomplete="off" placeholder="<?php echo __( 'Today', 'slicewp' ); ?>" value="<?php echo ( ! empty( $request['date_up_to'] ) ? esc_attr( $request['date_up_to'] ) : '' )?>"/>
					</div>

				</div>

				<!-- Date min/max -->
				<div class="slicewp-field-wrapper slicewp-field-wrapper-inline slicewp-flex" style="display: none;">

					<div class="slicewp-datepicker-wrapper slicewp-icon-left">
						<?php echo slicewp_get_svg( 'outline-calendar' ); ?>
						<input id="slicewp-date-min" type="text" name="date_min" class="slicewp-datepicker" autocomplete="off" placeholder="<?php echo __( 'From', 'slicewp' ); ?>" value="<?php echo ( ! empty( $request['date_min'] ) ? esc_attr( $request['date_min'] ) : '' )?>"/>
					</div>

					<div class="slicewp-datepicker-wrapper slicewp-icon-left">
						<?php echo slicewp_get_svg( 'outline-calendar' ); ?>
						<input id="slicewp-date-max" type="text" name="date_max" class="slicewp-datepicker" autocomplete="off" placeholder="<?php echo __( 'To', 'slicewp' ); ?>" value="<?php echo ( ! empty( $request['date_max'] ) ? esc_attr( $request['date_max'] ) : '' )?>"/>
					</div>

				</div>

				<!-- Grace period -->
				<?php if ( ! empty( slicewp_get_setting( 'commissions_grace_period' ) ) ): ?>

					<div class="slicewp-field-wrapper slicewp-field-wrapper-inline slicewp-tooltip-wide">

						<div class="slicewp-field-label-wrapper">
							<label for="slicewp-include-grace-period"><?php echo __( 'Grace Period Commissions', 'slicewp' ); ?></label>
							<?php echo slicewp_output_tooltip( '<p>' . sprintf( __( 'By default, to protect you in case of refunds, commissions that are in the %d days grace period will not be included in the payout.', 'slicewp' ), absint( slicewp_get_setting( 'commissions_grace_period', 0 ) ) ) . '</p><p>' . __( 'If you wish to also pay commissions that are in the grace period in this payout, enable this option.', 'slicewp' ) . '</p>' ); ?>
						</div>

						<div class="slicewp-switch">

							<input id="slicewp-include-grace-period" class="slicewp-toggle slicewp-toggle-round" name="include_grace_period" type="checkbox" value="1" <?php checked( ! empty( $request['include_grace_period'] ) ? '1' : '' ); ?> />
							<label for="slicewp-include-grace-period"></label>

						</div>

						<label for="slicewp-include-grace-period"><?php echo __( 'Pay commissions that are in the grace period.', 'slicewp' ); ?></label>

					</div>

				<?php endif; ?>

				<!-- Minimum amount -->
            	<div class="slicewp-field-wrapper slicewp-field-wrapper-inline slicewp-tooltip-wide">

                    <div class="slicewp-field-label-wrapper">
                        <label for="slicewp-payments-minimum-amount"><?php echo __( 'Payments Minimum Amount', 'slicewp' ); ?></label>
						<?php echo slicewp_output_tooltip( __( 'The payment for an affiliate will be generated only if the sum of all eligible commissions is greater than this value. If set to 0, the payments will be generated regardless of the commissions totals.', 'slicewp' ) ); ?>
                    </div>

					<input id="slicewp-payments-minimum-amount" name="payments_minimum_amount" type="number" step="any" min="0" value="<?php echo ( isset( $request['payments_minimum_amount'] ) ? esc_attr( $request['payments_minimum_amount'] ) : ( ! empty( slicewp_get_setting( 'payments_minimum_amount' ) ) ? slicewp_get_setting( 'payments_minimum_amount' ) : 0 ) ); ?>" />
                
                </div>

				<!-- Affiliate Name -->
				<div class="slicewp-field-wrapper slicewp-field-wrapper-inline slicewp-tooltip-wide slicewp-last">

					<div class="slicewp-field-label-wrapper">
						<label for="slicewp-payout-included-affiliates"><?php echo __( 'Included Affiliates', 'slicewp' ); ?></label>
					</div>
					
					<select id="slicewp-payout-included-affiliates" name="included_affiliates" class="slicewp-select2">
						<option value="all" <?php echo ( ( ! empty( $request['included_affiliates'] ) && $request['included_affiliates'] == 'all' ) ? 'selected="selected"' : '' ); ?>><?php echo __( 'All eligible affiliates', 'slicewp' ); ?></option>
						<option value="selected" <?php echo ( ( ! empty( $request['included_affiliates'] ) && $request['included_affiliates'] == 'selected' ) ? 'selected="selected"' : '' ); ?>><?php echo __( 'Only selected affiliates...', 'slicewp' ); ?></option>
					</select>

				</div>

				<div class="slicewp-field-wrapper slicewp-field-wrapper-inline slicewp-tooltip-wide slicewp-last" style="display: none; margin-top: 10px;">

					<select id="slicewp-payout-selected-affiliates" name="selected_affiliates[]" class="slicewp-select2-users-autocomplete" data-affiliates="include" data-return-value="affiliate_id" multiple placeholder="<?php echo __( 'Search for an affiliate...', 'slicewp' ); ?>" data-nonce="<?php echo wp_create_nonce( 'slicewp_user_search' ); ?>">

						<?php
						
							$affiliate_ids = array_values( array_filter( ( ! empty( $request['selected_affiliates'] ) ? array_map( 'absint', $request['selected_affiliates'] ) : array() ) ) );

							if ( ! empty( $affiliate_ids ) ) {

								foreach ( $affiliate_ids as $affiliate_id ) {

									$affiliate = slicewp_get_affiliate( $affiliate_id );

									if ( is_null( $affiliate ) ) {
										continue;
									}

									$user = get_userdata( $affiliate->get( 'user_id' ) );

									echo '<option value="' . $affiliate_id . '" selected="selected">' . $user->first_name . ' ' . $user->last_name . ' (' . $user->user_email . ')' . '</option>';

								}

							}

						?>

					</select>

					<?php wp_nonce_field( 'slicewp_user_search', 'slicewp_user_search_token', false ); ?>

				</div>

			</div>

		</div>

		<!-- Action and nonce -->
		<input type="hidden" name="slicewp_action" value="send_to_preview_payout" />

		<!-- Submit -->
		<input type="submit" class="slicewp-form-submit slicewp-button-primary" name="slicewp_preview_payout" value="<?php echo __( 'Preview Payout', 'slicewp' ); ?>" />

	</form>

</div>