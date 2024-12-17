<?php

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;


/**
 * Dashboard card: Latest Registered Affiliates.
 *
 */
class SliceWP_Admin_Dashboard_Card_Latest_Affiliates extends SliceWP_Admin_Dashboard_Card {

	/**
	 * Initialize the card.
	 *
	 */
	protected function init() {

		$this->slug    = 'latest_affiliates';
		$this->name    = __( 'Latest Registered Affiliates', 'slicewp' );
		$this->context = 'primary';

	}


	/**
	 * Output the card's content.
	 *
	 */
	public function output() {

		$affiliates 	  = slicewp_get_affiliates( array( 'number' => 5 ) );
		$affiliates_count = slicewp_get_affiliates( array( 'number' => 5 ), true );

		?>

			<table class="slicewp-card-table-full-width">

				<thead>
					<tr>
						<th class="slicewp-column-affiliate"><?php echo __( 'Affiliate', 'slicewp' ); ?></th>
						<th class="slicewp-column-status"><?php echo __( 'Status', 'slicewp' ); ?></th>
					</tr>
				</thead>

				<tbody>

					<?php if ( ! empty( $affiliates ) ): ?>

						<?php foreach ( $affiliates as $affiliate ): ?>

							<?php $statuses = slicewp_get_affiliate_available_statuses(); ?>

							<tr>
								<td class="slicewp-column-affiliate">
									<a class="slicewp-affiliate-name" href="<?php echo esc_url( add_query_arg( array( 'page' => 'slicewp-affiliates', 'subpage' => ( $affiliate->get( 'status' ) == 'pending' ? 'review-affiliate' : 'edit-affiliate' ), 'affiliate_id' => absint( $affiliate->get( 'id' ) ) ), admin_url( 'admin.php' ) ) ); ?>">
										<?php echo get_avatar( $affiliate->get( 'user_id' ), 64 ); ?>
										<span><?php echo slicewp_get_affiliate_name( $affiliate ); ?></span>
									</a>
								</td>
								<td class="slicewp-column-status"><span class="slicewp-status-pill slicewp-status-<?php echo esc_attr( $affiliate->get( 'status' ) ); ?>"><?php echo ( isset( $statuses[$affiliate->get( 'status' )] ) ? $statuses[$affiliate->get( 'status' )] : $affiliate->get( 'status' ) ); ?></span></td>
							</tr>

						<?php endforeach; ?>

						<?php if ( $affiliates_count > 5 ): ?>

							<tr class="slicewp-card-table-row-footer"><td colspan="2"><a href="<?php echo esc_url( add_query_arg( array( 'page' => 'slicewp-affiliates' ), admin_url( 'admin.php' ) ) ); ?>"><?php echo __( 'View all affiliates', 'slicewp' ); ?> &#8594;</a></td></tr>

						<?php endif; ?>

					<?php else: ?>

						<tr class="slicewp-no-items"><td colspan="2"><?php echo __( 'No affiliates found yet.', 'slicewp' ); ?></td></tr>

					<?php endif; ?>

				</tbody>

			</table>

		<?php

	}

}