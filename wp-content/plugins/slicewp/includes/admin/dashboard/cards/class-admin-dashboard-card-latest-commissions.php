<?php

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;


/**
 * Dashboard card: Latest Commissions.
 *
 */
class SliceWP_Admin_Dashboard_Card_Latest_Commissions extends SliceWP_Admin_Dashboard_Card {

	/**
	 * Initialize the card.
	 *
	 */
	protected function init() {

		$this->slug    = 'latest_commissions';
		$this->name    = __( 'Latest Commissions', 'slicewp' );
		$this->context = 'secondary';

	}


	/**
	 * Output the card's content.
	 *
	 */
	public function output() {

		$commissions 	   = slicewp_get_commissions( array( 'number' => 5 ) );
		$commissions_count = slicewp_get_commissions( array( 'number' => 10 ), true );

		?>

			<table class="slicewp-card-table-full-width">

				<thead>
					<tr>
						<th class="slicewp-column-affiliate"><?php echo __( 'Affiliate', 'slicewp' ); ?></th>
						<th class="slicewp-column-amount"><?php echo __( 'Amount', 'slicewp' ); ?></th>
						<th class="slicewp-column-reference"><?php echo __( 'Reference', 'slicewp' ); ?></th>
						<th class="slicewp-column-status"><?php echo __( 'Status', 'slicewp' ); ?></th>
					</tr>
				</thead>

				<tbody>

					<?php if ( ! empty( $commissions ) ): ?>

						<?php foreach ( $commissions as $commission ): ?>

							<?php

								$affiliate = slicewp_get_affiliate( $commission->get( 'affiliate_id' ) );

								if ( is_null( $affiliate ) )
									continue;

								$statuses = slicewp_get_commission_available_statuses();

							?>

							<tr>
								<td class="slicewp-column-affiliate"><a href="<?php echo add_query_arg( array( 'page' => 'slicewp-affiliates', 'subpage' => ( $affiliate->get( 'status' ) == 'pending' ? 'review-affiliate' : 'edit-affiliate' ), 'affiliate_id' => $affiliate->get( 'id' ) ), admin_url( 'admin.php' ) ); ?>"><?php echo slicewp_get_affiliate_name( $affiliate->get( 'id' ) ); ?></a></td>
								<td class="slicewp-column-amount"><?php echo slicewp_format_amount( $commission->get( 'amount' ), slicewp_get_setting( 'active_currency', 'USD' ) ); ?></td>
								<td class="slicewp-column-reference"><?php echo apply_filters( 'slicewp_list_table_commissions_column_reference', $commission->get( 'reference' ), $commission->to_array() ); ?></td>
								<td class="slicewp-column-status"><span class="slicewp-status-pill slicewp-status-<?php echo esc_attr( $commission->get( 'status' ) ); ?>"><?php echo ( isset( $statuses[$commission->get( 'status' )] ) ? $statuses[$commission->get( 'status' )] : $commission->get( 'status' ) ); ?></span></td>
							</tr>

						<?php endforeach; ?>

						<?php if ( $commissions_count > 5 ): ?>

							<tr class="slicewp-card-table-row-footer"><td colspan="4"><a href="<?php echo esc_url( add_query_arg( array( 'page' => 'slicewp-commissions' ), admin_url( 'admin.php' ) ) ); ?>"><?php echo __( 'View all commissions', 'slicewp' ); ?> &#8594;</a></td></tr>

						<?php endif; ?>

					<?php else: ?>

						<tr class="slicewp-no-items"><td colspan="4"><?php echo __( 'No commissions found yet.', 'slicewp' ); ?></td></tr>

					<?php endif; ?>

				</tbody>

			</table>

		<?php

	}

}