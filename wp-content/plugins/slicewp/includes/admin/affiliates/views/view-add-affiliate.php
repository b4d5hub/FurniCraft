<?php

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

?>

<div class="wrap slicewp-wrap slicewp-wrap-add-affiliate">

	<form action="" method="POST">

		<!-- Page Heading -->
		<h1 class="wp-heading-inline"><?php echo __( 'Add a New Affiliate', 'slicewp' ); ?></h1>
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

						<!-- Affiliate User ID -->
						<?php /* 
						<div class="slicewp-field-wrapper slicewp-field-wrapper-inline slicewp-field-wrapper-users-autocomplete">

							<div class="slicewp-field-label-wrapper">
								<label for="slicewp-affiliate-user-id"><?php echo __( 'User', 'slicewp' ); ?> *</label>
							</div>
							
							<input id="slicewp-affiliate-user-id" class="slicewp-field-users-autocomplete" data-affiliates="exclude" autocomplete="off" name="user_search" type="text" placeholder="<?php echo __( "Type the user's email or name...", 'slicewp' ); ?>" value="<?php echo ( ! empty( $_POST['user_search'] ) ? esc_attr( $_POST['user_search'] ) : '' ); ?>" />
							<input type="hidden" name="user_id" value="<?php echo ( ! empty( $_POST['user_id'] ) ? esc_attr( $_POST['user_id'] ) : '' ); ?>" />

							<?php wp_nonce_field( 'slicewp_user_search', 'slicewp_user_search_token', false ); ?>

						</div>
						*/ ?>

						<div class="slicewp-field-wrapper slicewp-field-wrapper-inline">
							
							<div class="slicewp-field-label-wrapper">
								<label for="slicewp-affiliate-user-id"><?php echo __( 'User', 'slicewp' ); ?> *</label>
							</div>

							<?php slicewp_output_select2_user_search( array( 'id' => 'slicewp-affiliate-user-id', 'name' => 'user_id', 'placeholder' => __( 'Select user...', 'slicewp' ), 'user_type' => 'non_affiliate', 'value' => ( ! empty( $_POST['user_id'] ) ? absint( $_POST['user_id'] ) : '' ) ) ); ?>

						</div>

						<!-- Payout method -->
						<?php

							$payout_methods 	   = slicewp_get_payout_methods();
							$default_payout_method = slicewp_get_default_payout_method();
							
						?>

						<?php if ( count( $payout_methods ) > 1 ): ?>
						
							<div id="slicewp-field-wrapper-payout-method" class="slicewp-field-wrapper slicewp-field-wrapper-inline slicewp-tooltip-wide">

								<div class="slicewp-field-label-wrapper">
									<label for="slicewp-affiliate-payout-method"><?php echo __( 'Payout Method', 'slicewp' ); ?></label>
									<?php echo slicewp_output_tooltip( sprintf( __( "Select the method through which you wish to pay this particular affiliate. By default, if you don't set a certain method for the affiliate, the Default Payout Method from %sSliceWP > Settings%s will be used.", 'slicewp' ), '<a href="' . esc_url( add_query_arg( array( 'page' => 'slicewp-settings' ), admin_url( 'admin.php' ) ) ) . '#slicewp-card-payouts-settings">', '</a>' ) ); ?>
								</div>

								<div class="slicewp-field-locked-wrapper">
									<input type="text" disabled value="<?php echo __( 'Default payout method', 'slicewp' ); ?>" />
									<a class="slicewp-field-unlock" href="#"><span class="dashicons dashicons-edit"></span><?php echo __( 'Change', 'slicewp' ); ?></a>
								</div>

								<select style="display: none;" id="slicewp-affiliate-payout-method" name="payout_method" class="slicewp-select2">
									<option value=""><?php echo __( 'Default payout method', 'slicewp' ); ?></option>

									<?php foreach ( $payout_methods as $slug => $details ): ?>
										<option value="<?php echo esc_attr( $slug ); ?>"><?php echo esc_html( $details['label'] ); ?></option>
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
							do_action( 'slicewp_admin_form_fields', 'add_affiliate' );

						?>

						<!-- Affiliate Status -->
						<div class="slicewp-field-wrapper slicewp-field-wrapper-inline">

							<div class="slicewp-field-label-wrapper">
								<label for="slicewp-affiliate-status"><?php echo __( 'Status', 'slicewp' ); ?> *</label>
							</div>
							
							<select id="slicewp-affiliate-status" name="status" class="slicewp-select2">

								<?php 
									foreach( slicewp_get_affiliate_available_statuses() as $status_slug => $status_name ) {
										echo '<option value="' . esc_attr( $status_slug ) . '">' . $status_name . '</option>';
									} 
								?>

							</select>

						</div>

						<!-- Send Welcome Email -->
						<div class="slicewp-field-wrapper slicewp-field-wrapper-inline slicewp-last">

							<div class="slicewp-field-label-wrapper">
								<label for="slicewp-affiliate-welcome-email"><?php echo __( 'Send Welcome Email', 'slicewp' ); ?></label>
							</div>
							
							<div class="slicewp-switch">

								<input id="slicewp-affiliate-welcome-email" class="slicewp-toggle slicewp-toggle-round" name="welcome_email" type="checkbox" value="1" <?php echo ( ! empty( $_POST['welcome_email'] ) ? 'checked="checked"' : '' ); ?> />
								<label for="slicewp-affiliate-welcome-email"></label>

							</div>

							<label for="slicewp-affiliate-welcome-email"><?php echo __( 'Send a welcome email to your new affiliate after registration.', 'slicewp' ); ?></label>

						</div>
					
					</div>

				</div>

				<?php 

					/**
					 * Hook to add extra cards if needed
					 *
					 */
					do_action( 'slicewp_view_affiliates_add_affiliate_bottom' );

				?>

			</div><!-- / Primary Content -->

			<!-- Sidebar Content -->
			<div id="slicewp-secondary">

				<?php

					/**
					 * Hook to add extra cards if needed in the sidebar
					 *
					 */
					do_action( 'slicewp_view_affiliates_add_affiliate_secondary' );

				?>

			</div><!-- / Sidebar Content -->

		</div>

		<!-- Action and nonce -->
		<input type="hidden" name="slicewp_action" value="add_affiliate" />
		<?php wp_nonce_field( 'slicewp_add_affiliate', 'slicewp_token', false ); ?>

		<!-- Submit -->
		<input type="submit" class="slicewp-form-submit slicewp-button-primary" value="<?php echo __( 'Add Affiliate', 'slicewp' ); ?>" />
		
	</form>

</div>