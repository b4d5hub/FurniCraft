<?php

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

$payments_preview = slicewp_generate_payout_payments_preview( $_GET );

$payments_totals = array_sum( array_column( $payments_preview, 'amount' ) );
$payments_count  = count( $payments_preview );

?>

<div class="wrap slicewp-wrap slicewp-wrap-preview-payout">

	<form method="POST">

		<!-- Page Heading -->
		<h1 class="wp-heading-inline"><?php echo __( 'Preview Payout', 'slicewp' ); ?></h1>
		<a href="<?php echo esc_url( add_query_arg( array_diff_key( array_merge( $_GET, array( 'subpage' => 'create-payout' ) ), array_flip( array( 'payments_count', 'payout_amount' ) ) ), $this->admin_url ) ); ?>" class="page-title-action"><?php echo '&#x2190; ' . __( 'Back to Payout Details', 'slicewp' ); ?></a>
		<hr class="wp-header-end" />

		<?php if ( empty( $payments_preview ) ): ?>

			<div class="notice-warning notice slicewp-notice" style="max-width: 675px; margin-top: 25px;">
				<p><?php echo __( 'No affiliate payments could be generated for the selected payout details.', 'slicewp' ); ?></p>
			</div>

		<?php else: ?>
        
			<!-- Postbox -->
			<div class="slicewp-grid slicewp-grid-columns-2" style="max-width: 675px;">

				<div class="slicewp-grid-item slicewp-card">
					<div class="slicewp-card-inner">
						<span style="font-size: 14px;"><?php echo __( 'Payout Amount', 'slicewp' ); ?></span>
						<div style="font-size: 28px; line-height: 1; margin-top: 15px;"><?php echo esc_attr( slicewp_format_amount( $payments_totals, slicewp_get_setting( 'active_currency', 'USD' ) ) ); ?></div>
					</div>
				</div>

				<div class="slicewp-grid-item slicewp-card">
					<div class="slicewp-card-inner">
						<span style="font-size: 14px;"><?php echo __( 'Affiliate Payments', 'slicewp' ); ?></span>
						<div style="font-size: 28px; line-height: 1; margin-top: 15px;"><?php echo esc_attr( $payments_count ); ?></div>
					</div>
				</div>
					
			</div>
			
			<!-- Payment List Table -->
			<?php 
				$table = new SliceWP_WP_List_Table_Payout_Preview_Payments();
				$table->items = $payments_preview;
				$table->prepare_items();
				$table->display();
			?>        

			<!-- Hidden fields needed for the search query -->
			<input type="hidden" name="page" value="slicewp-payments">

			<!-- Action and nonce -->
			<input type="hidden" name="slicewp_action" value="create_payout" />
			<?php wp_nonce_field( 'slicewp_create_payout', 'slicewp_token', false ); ?>

			<!-- Submit -->
			<input type="submit" class="slicewp-form-submit slicewp-button-primary" name="slicewp_create_payout" value="<?php echo __( 'Create Payout', 'slicewp' ); ?>" />

		<?php endif; ?>

	</form>

</div>