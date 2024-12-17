<?php

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;


/**
 * Dashboard card: Latest Referral Visits.
 *
 */
class SliceWP_Admin_Dashboard_Card_Latest_Visits extends SliceWP_Admin_Dashboard_Card {

	/**
	 * Initialize the card.
	 *
	 */
	protected function init() {

		$this->slug    = 'latest_visits';
		$this->name    = __( 'Latest Referral Visits', 'slicewp' );
		$this->context = 'secondary';

	}


	/**
	 * Output the card's content.
	 *
	 */
	public function output() {

		$visits 	  = slicewp_get_visits( array( 'number' => 5 ) );
		$visits_count = slicewp_get_visits( array( 'number' => 10 ), true );

		?>

			<table class="slicewp-card-table-full-width">

				<thead>
					<tr>
						<th class="slicewp-column-affiliate"><?php echo __( 'Affiliate', 'slicewp' ); ?></th>
						<th class="slicewp-column-landing-url"><?php echo __( 'URL', 'slicewp' ); ?></th>
						<th class="slicewp-column-status"><?php echo __( 'Converted', 'slicewp' ); ?></th>
					</tr>
				</thead>

				<tbody>

					<?php if ( ! empty( $visits ) ): ?>

						<?php foreach ( $visits as $visit ): ?>

							<?php 
								
								$affiliate = slicewp_get_affiliate( $visit->get( 'affiliate_id' ) );
								
								if ( is_null( $affiliate ) )
									continue;
									
							?>

							<tr>
								<td class="slicewp-column-affiliate"><a href="<?php echo add_query_arg( array( 'page' => 'slicewp-affiliates', 'subpage' => ( $affiliate->get( 'status' ) == 'pending' ? 'review-affiliate' : 'edit-affiliate' ), 'affiliate_id' => $affiliate->get( 'id' ) ), admin_url( 'admin.php' ) ); ?>"><?php echo slicewp_get_affiliate_name( $affiliate->get( 'id' ) ); ?></a></td>
								<td class="slicewp-column-landing-url"><a href="<?php echo esc_url( $visit->get( 'landing_url' ) ); ?>" target="_blank"><?php echo $visit->get( 'landing_url' ); ?></a></td>
								<td class="slicewp-column-status">
									<?php if ( empty( $visit->get( 'commission_id' ) ) ): ?>
										<span class="slicewp-status-icon"><?php echo slicewp_get_svg( 'outline-x' ); ?></span>
									<?php else: ?>
										<span class="slicewp-status-icon slicewp-status-converted"><?php echo slicewp_get_svg( 'outline-check' ); ?></span> <a href="<?php echo add_query_arg( array( 'page' => 'slicewp-commissions', 'subpage' => 'edit-commission', 'commission_id' => $visit->get( 'commission_id' ) ) , admin_url( 'admin.php' ) ); ?>">#<?php echo absint( $visit->get( 'commission_id' ) ); ?></a>
									<?php endif; ?>
								</td>
							</tr>

						<?php endforeach; ?>

						<?php if ( $visits_count > 5 ): ?>

							<tr class="slicewp-card-table-row-footer"><td colspan="3"><a href="<?php echo esc_url( add_query_arg( array( 'page' => 'slicewp-visits' ), admin_url( 'admin.php' ) ) ); ?>"><?php echo __( 'View all visits', 'slicewp' ); ?> &#8594;</a></td></tr>

						<?php endif; ?>

					<?php else: ?>

						<tr class="slicewp-no-items"><td colspan="3"><?php echo __( 'No referral visits found yet.', 'slicewp' ); ?></td></tr>

					<?php endif; ?>

				</tbody>

			</table>

		<?php

	}

}