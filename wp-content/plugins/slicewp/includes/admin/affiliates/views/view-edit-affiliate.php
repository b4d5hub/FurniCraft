<?php

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) exit;

// Verify for Affiliate ID.
$affiliate_id = ( ! empty( $_GET['affiliate_id'] ) ? absint( $_GET['affiliate_id'] ) : 0 );

if ( empty( $affiliate_id ) ) {
	return;
}

// Get the Affiliate Data
$affiliate = slicewp_get_affiliate( $affiliate_id );

if ( is_null( $affiliate ) ) {
	return;
}

$affiliate_reject_reason = slicewp_get_affiliate_meta( $affiliate_id, 'reject_reason', true );

// Get the Affiliate's User
$user = get_user_by( 'id', $affiliate->get('user_id') );

if ( ! $user ) {
	return;
}

?>

<div class="wrap slicewp-wrap slicewp-wrap-edit-affiliate">

	<form action="" method="POST">

		<!-- Page Heading -->
		<h1 class="wp-heading-inline"><?php echo __( 'Edit Affiliate', 'slicewp' ); ?></h1>
		<hr class="wp-header-end" />

		<div id="slicewp-content-wrapper">

			<!-- Primary Content -->
			<div id="slicewp-primary">

				<!-- Postbox -->
				<div class="slicewp-card slicewp-first">

					<div class="slicewp-card-header">
						<span class="slicewp-card-title"><?php echo __( 'Affiliate Details', 'slicewp' ); ?></span>
					</div>

					<!-- Form Fields -->
					<div class="slicewp-card-inner">

						<!-- Affiliate Name -->
						<div class="slicewp-field-wrapper slicewp-field-wrapper-inline">

							<div class="slicewp-field-label-wrapper">
								<label for="slicewp-affiliate-name"><?php echo __( 'Affiliate Name', 'slicewp' ); ?></label>
								<?php echo slicewp_output_tooltip( sprintf( __( 'This is the display name of the user attached to this affiliate. You can change this value from the %suser edit page%s.', 'slicewp' ), '<a href="' . esc_url( add_query_arg( array( 'user_id' => $affiliate->get('user_id') ), admin_url( 'user-edit.php' ) ) ) . '">', '</a>' ) ); ?>
							</div>
							
							<input id="slicewp-affiliate-name" name="affiliate_name" disabled type="text" value="<?php echo esc_attr( slicewp_get_affiliate_name( $affiliate ) ); ?>" />

						</div>

						<!-- Affiiate Email -->
						<div class="slicewp-field-wrapper slicewp-field-wrapper-inline">

							<div class="slicewp-field-label-wrapper">
								<label for="slicewp-affiliate-email"><?php echo __( 'Email', 'slicewp' ); ?></label>
							</div>
							
							<input id="slicewp-affiliate-email" name="affiliate_email" disabled type="text" value="<?php echo esc_attr( $user->get('user_email') ); ?>" />

						</div>

						<!-- Affiliate ID -->
						<div class="slicewp-field-wrapper slicewp-field-wrapper-inline">

							<div class="slicewp-field-label-wrapper">
								<label for="slicewp-affiliate-affiliate-id"><?php echo __( 'Affiliate ID', 'slicewp' ); ?></label>
							</div>
							
							<input id="slicewp-affiliate-affiliate-id" name="affiliate_id" disabled type="text" value="<?php echo esc_attr( $affiliate->get('id') ); ?>" />

						</div>

						<!-- User ID -->
						<div class="slicewp-field-wrapper slicewp-field-wrapper-inline">

							<div class="slicewp-field-label-wrapper">
								<label for="slicewp-affiliate-user-id"><?php echo __( 'User ID', 'slicewp' ); ?></label>
							</div>
							
							<input id="slicewp-affiliate-user-id" name="user_id" disabled type="text" value="<?php echo esc_attr( $affiliate->get('user_id') ); ?>" />

						</div>

						<!-- Registration Date -->
						<div class="slicewp-field-wrapper slicewp-field-wrapper-inline">

							<div class="slicewp-field-label-wrapper">
								<label for="slicewp-affiliate-registration-date"><?php echo __( 'Registration Date', 'slicewp' ); ?></label>
							</div>
							
							<input id="slicewp-affiliate-registration-date" name="registration_date" disabled type="text" value="<?php echo slicewp_date_i18n( esc_attr( $affiliate->get('date_created') ) ); ?>" />

						</div>

						<!-- Payout method -->
						<?php

							$payout_methods 	    = slicewp_get_payout_methods();
							$default_payout_method  = slicewp_get_default_payout_method();

							$selected_payout_method = slicewp_get_affiliate_meta( $affiliate_id, 'payout_method', true );
							$selected_payout_method = ( ! empty( $selected_payout_method ) && in_array( $selected_payout_method, array_keys( $payout_methods ) ) ? $selected_payout_method : '' );
							
						?>

						<?php if ( count( $payout_methods ) > 1 ): ?>
						
							<div id="slicewp-field-wrapper-payout-method" class="slicewp-field-wrapper slicewp-field-wrapper-inline slicewp-tooltip-wide">

								<div class="slicewp-field-label-wrapper">
									<label for="slicewp-affiliate-payout-method"><?php echo __( 'Payout Method', 'slicewp' ); ?></label>
									<?php echo slicewp_output_tooltip( sprintf( __( "Select the method through which you wish to pay this particular affiliate. By default, if you don't set a certain method for the affiliate, the Default Payout Method from %sSliceWP > Settings%s will be used.", 'slicewp' ), '<a href="' . esc_url( add_query_arg( array( 'page' => 'slicewp-settings' ), admin_url( 'admin.php' ) ) ) . '#slicewp-card-payouts-settings">', '</a>' ) ); ?>
								</div>

								<div class="slicewp-field-locked-wrapper">
									<input type="text" disabled value="<?php echo ( empty( $selected_payout_method ) ? __( 'Default payout method', 'slicewp' ) : $payout_methods[$selected_payout_method]['label'] ); ?>" />
									<a class="slicewp-field-unlock" href="#"><span class="dashicons dashicons-edit"></span><?php echo __( 'Change', 'slicewp' ); ?></a>
								</div>

								<select style="display: none;" id="slicewp-affiliate-payout-method" name="payout_method" class="slicewp-select2">
									<option value=""><?php echo __( 'Default payout method', 'slicewp' ); ?></option>

									<?php foreach ( $payout_methods as $slug => $details ): ?>
										<option value="<?php echo esc_attr( $slug ); ?>" <?php selected( $selected_payout_method, $slug ); ?>><?php echo esc_html( $details['label'] ); ?></option>
									<?php endforeach; ?>
								</select>

							</div>
						
						<?php endif; ?>
						
						<?php

							/**
							 * Hooks to output form fields
							 *
							 * @param string $form
							 *
							 */
							do_action( 'slicewp_admin_form_fields', 'edit_affiliate' );

						?>
						
						<!-- Affiliate Status -->
						<div class="slicewp-field-wrapper slicewp-field-wrapper-inline <?php echo ( $affiliate->get('status') != 'rejected' ? 'slicewp-last' : '' )?>">

							<div class="slicewp-field-label-wrapper">
								<label for="slicewp-affiliate-status"><?php echo __( 'Status', 'slicewp' ); ?> *</label>
							</div>
							
							<select id="slicewp-affiliate-status" name="status" class="slicewp-select2">

								<?php 
									foreach( slicewp_get_affiliate_available_statuses() as $status_slug => $status_name ) {
										echo '<option value="' . esc_attr( $status_slug ) . '" ' . selected( $affiliate->get('status'), $status_slug, false ) . '>' . $status_name . '</option>';
									} 
								?>

							</select>

						</div>

						<?php if ( $affiliate->get('status') == 'rejected' ): ?>

							<!-- Reject Reason -->
							<div class="slicewp-field-wrapper slicewp-field-wrapper-inline slicewp-last">

								<div class="slicewp-field-label-wrapper">
									<label for="slicewp-affiliate-reject-reason"><?php echo __( 'Reject Reason', 'slicewp' ); ?></label>
								</div>
								
								<textarea id="slicewp-affiliate-reject-reason" name="reject_reason" disabled><?php echo $affiliate_reject_reason; ?></textarea>

							</div>

						<?php endif; ?>

					</div>

				</div>

				<?php

					/**
					 * Hook to add extra cards if needed
					 *
					 */
					do_action( 'slicewp_view_affiliates_edit_affiliate_bottom' );

				?>

			</div><!-- / Primary Content -->

			<!-- Sidebar Content -->
			<div id="slicewp-secondary">

				<?php 

					/**
					 * Hook to add extra cards if needed in the sidebar
					 *
					 */
					do_action( 'slicewp_view_affiliates_edit_affiliate_secondary' );

				?>

			</div><!-- / Sidebar Content -->

		</div>

		<!-- Hidden affiliate id field -->
		<input type="hidden" name="affiliate_id" value="<?php echo $affiliate_id; ?>" />

		<!-- Action and nonce -->
		<input type="hidden" name="slicewp_action" value="update_affiliate" />
		<?php wp_nonce_field( 'slicewp_update_affiliate', 'slicewp_token', false ); ?>

		<!-- Submit -->
		<div id="slicewp-content-actions">
			
			<input type="submit" class="slicewp-form-submit slicewp-button-primary" value="<?php echo __( 'Update Affiliate', 'slicewp' ); ?>" />

			<span class="slicewp-trash"><a onclick="return confirm( '<?php echo __( "Are you sure you want to delete this affiliate?", "slicewp" ); ?>' )" href="<?php echo wp_nonce_url( add_query_arg( array( 'page' => 'slicewp-affiliates', 'slicewp_action' => 'delete_affiliate', 'affiliate_id' => absint( $affiliate->get( 'id' ) ) ) , admin_url( 'admin.php' ) ), 'slicewp_delete_affiliate', 'slicewp_token' ); ?>"><?php echo __( 'Delete affiliate', 'slicewp' ) ?></a></span>

		</div>

	</form>

</div>