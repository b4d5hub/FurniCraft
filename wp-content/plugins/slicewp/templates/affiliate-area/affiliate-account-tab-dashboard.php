<?php
/**
 * Affiliate account tab: Dashboard
 *
 * This template can be overridden by copying it to yourtheme/slicewp/affiliate-area/affiliate-account-tab-dashboard.php.
 *
 * HOWEVER, on occasion SliceWP will need to update template files and you
 * (the theme developer) will need to copy the new files to your theme to
 * maintain compatibility.
 *
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) exit;

$dashboard_data = slicewp_build_affiliate_dashboard_data( $args['affiliate_id'] );

$chart_args = array(
    'id' => 'affiliate_dashboard',
    'datasets' => array(
        'visits' => array(
            'label' => __( 'Visits', 'slicewp' ),
            'color' => '#00aadc',
            'data'  => ( ! empty( $dashboard_data['datasets']['visits']['timeline_current'] ) ? $dashboard_data['datasets']['visits']['timeline_current'] : array() )
        ),
        'commissions' => array(
            'label' => __( 'Commissions', 'slicewp' ),
            'color' => '#f39c12',
            'data'  => ( ! empty( $dashboard_data['datasets']['commissions']['timeline_current'] ) ? $dashboard_data['datasets']['commissions']['timeline_current'] : array() )
        ),
        'earnings' => array(
            'label'     => __( 'Earnings', 'slicewp' ),
            'color'     => '#26a387',
            'is_amount' => true,
            'data'      => ( ! empty( $dashboard_data['datasets']['earnings']['timeline_current'] ) ? $dashboard_data['datasets']['earnings']['timeline_current'] : array() )
        )
    )
);

$chart_args = apply_filters( 'slicewp_chart_args_affiliate_dashboard', $chart_args );

?>

<div class="slicewp-affiliate-dashboard-filters">

    <form action="slicewp_action_ajax_apply_affiliate_dashboard_filters">

        <?php echo slicewp_element_date_range_picker( array( 'input_name' => 'dashboard-filter' ) ); ?>

        <button class="slicewp-button-primary" type="submit"><?php echo __( 'Apply', 'slicewp' ); ?></button>

    </form>

</div>

<div class="slicewp-grid slicewp-grid-affiliate-dashboard slicewp-grid-affiliate-dashboard-last-30-days">

    <div class="slicewp-card slicewp-card-affiliate-dashboard" data-kpi="visits" data-is-filtrable="true">

        <div class="slicewp-card-inner">

            <div class="slicewp-card-title"><?php echo __( 'Visits', 'slicewp' ); ?></div>

            <div class="slicewp-kpi-value">

                <span><?php echo $dashboard_data['datasets']['visits']['current']; ?></span>

                <div class="slicewp-kpi-direction <?php echo ( is_numeric( $dashboard_data['datasets']['visits']['comparison_change'] ) && ! empty( $dashboard_data['datasets']['visits']['comparison_change'] ) ? ( $dashboard_data['datasets']['visits']['comparison_change'] > 0 ? 'slicewp-positive' : 'slicewp-negative' ) : '' ); ?>">
                    <span class="slicewp-arrow-up"><?php echo slicewp_get_svg( 'outline-arrow-up' ); ?></span>
                    <span class="slicewp-arrow-down"><?php echo slicewp_get_svg( 'outline-arrow-down' ); ?></span>
                    <span><?php echo sprintf( '%s', ( is_numeric( $dashboard_data['datasets']['visits']['comparison_change'] ) ? $dashboard_data['datasets']['visits']['comparison_change'] . '%' : '-' ) ); ?></span>
                </div>

            </div>

        </div>

        <div class="slicewp-card-footer">
            <a href="<?php echo esc_url( add_query_arg( array( 'affiliate-account-tab' => 'visits' ) ) ); ?>" data-slicewp-tab="visits"><?php echo __( 'View all visits', 'slicewp' ); ?></a>
        </div>

    </div>

    <div class="slicewp-card slicewp-card-affiliate-dashboard" data-kpi="commissions" data-is-filtrable="true">

        <div class="slicewp-card-inner">

            <div class="slicewp-card-title"><?php echo __( 'Commissions', 'slicewp' ); ?></div>

            <div class="slicewp-kpi-value">

                <span><?php echo $dashboard_data['datasets']['commissions']['current']; ?></span>

                <div class="slicewp-kpi-direction <?php echo ( is_numeric( $dashboard_data['datasets']['commissions']['comparison_change'] ) && ! empty( $dashboard_data['datasets']['commissions']['comparison_change'] ) ? ( $dashboard_data['datasets']['commissions']['comparison_change'] > 0 ? 'slicewp-positive' : 'slicewp-negative' ) : '' ); ?>">
                    <span class="slicewp-arrow-up"><?php echo slicewp_get_svg( 'outline-arrow-up' ); ?></span>
                    <span class="slicewp-arrow-down"><?php echo slicewp_get_svg( 'outline-arrow-down' ); ?></span>
                    <span><?php echo sprintf( '%s', ( is_numeric( $dashboard_data['datasets']['commissions']['comparison_change'] ) ? $dashboard_data['datasets']['commissions']['comparison_change'] . '%' : '-' ) ); ?></span>
                </div>

            </div>

        </div>

        <div class="slicewp-card-footer">
            <a href="<?php echo esc_url( add_query_arg( array( 'affiliate-account-tab' => 'commissions' ) ) ); ?>" data-slicewp-tab="commissions"><?php echo __( 'View all commissions', 'slicewp' ); ?></a>
        </div>

    </div>

    <div class="slicewp-card slicewp-card-affiliate-dashboard" data-kpi="earnings" data-is-filtrable="true">

        <div class="slicewp-card-inner">

            <div class="slicewp-card-title"><?php echo __( 'Earnings', 'slicewp' ); ?></div>

            <div class="slicewp-kpi-value">

                <span><?php echo $dashboard_data['datasets']['earnings']['current_formatted']; ?></span>

                <div class="slicewp-kpi-direction <?php echo ( is_numeric( $dashboard_data['datasets']['earnings']['comparison_change'] ) && ! empty( $dashboard_data['datasets']['earnings']['comparison_change'] ) ? ( $dashboard_data['datasets']['earnings']['comparison_change'] > 0 ? 'slicewp-positive' : 'slicewp-negative' ) : '' ); ?>">
                    <span class="slicewp-arrow-up"><?php echo slicewp_get_svg( 'outline-arrow-up' ); ?></span>
                    <span class="slicewp-arrow-down"><?php echo slicewp_get_svg( 'outline-arrow-down' ); ?></span>
                    <span><?php echo sprintf( '%s', ( is_numeric( $dashboard_data['datasets']['earnings']['comparison_change'] ) ? $dashboard_data['datasets']['earnings']['comparison_change'] . '%' : '-' ) ); ?></span>
                </div>

            </div>

        </div>

        <div class="slicewp-card-footer">
            <a href="<?php echo esc_url( add_query_arg( array( 'affiliate-account-tab' => 'commissions' ) ) ); ?>" data-slicewp-tab="commissions"><?php echo __( 'View all commissions', 'slicewp' ); ?></a>
        </div>

    </div>

</div>

<div class="slicewp-card" data-is-filtrable="true">

    <div class="slicewp-card-inner">

        <div class="slicewp-chart-before">

            <div class="slicewp-chart-legend"><!-- --></div>

            <select class="slicewp-chart-time-unit-selector" data-id="affiliate_dashboard">
                <option value="day"><?php echo __( 'Daily', 'slicewp' ); ?></option>
                <option value="week"><?php echo __( 'Weekly', 'slicewp' ); ?></option>
                <option value="month" disabled><?php echo __( 'Monthly', 'slicewp' ); ?></option>
            </select>

        </div>

        <div>
            <canvas class="slicewp-chart" height="325" data-id="affiliate_dashboard" data-datasets="<?php echo esc_attr( json_encode( $chart_args['datasets'] ) ); ?>"></canvas>
        </div>
    </div>

</div>

<p class="slicewp-section-heading"><?php echo __( 'All time', 'slicewp' ); ?>

<div class="slicewp-grid slicewp-grid-affiliate-dashboard slicewp-grid-affiliate-dashboard-all-time">

    <div class="slicewp-card slicewp-card-affiliate-dashboard">

        <div class="slicewp-card-inner">

            <div class="slicewp-card-title"><?php echo __( 'Visits', 'slicewp' ); ?></div>
            <div class="slicewp-kpi-value"><?php echo $dashboard_data['datasets']['visits']['total']; ?></div>

        </div>

    </div>

    <div class="slicewp-card slicewp-card-affiliate-dashboard">

        <div class="slicewp-card-inner">

            <div class="slicewp-card-title"><?php echo __( 'Commissions', 'slicewp' ); ?></div>
            <div class="slicewp-kpi-value"><?php echo $dashboard_data['datasets']['commissions']['total']; ?></div>

        </div>

    </div>

    <div class="slicewp-card slicewp-card-affiliate-dashboard">

        <div class="slicewp-card-inner">

            <div class="slicewp-card-title"><?php echo __( 'Paid Earnings', 'slicewp' ); ?></div>
            <div class="slicewp-kpi-value"><?php echo $dashboard_data['datasets']['earnings']['total_paid_formatted']; ?></div>

        </div>

    </div>

    <div class="slicewp-card slicewp-card-affiliate-dashboard">

        <div class="slicewp-card-inner">

            <div class="slicewp-card-title"><?php echo __( 'Unpaid Earnings', 'slicewp' ); ?></div>
            <div class="slicewp-kpi-value"><?php echo $dashboard_data['datasets']['earnings']['total_unpaid_formatted']; ?></div>

        </div>

    </div>

</div>

<p class="slicewp-section-heading"><?php echo __( 'Program details', 'slicewp' ); ?>

<div class="slicewp-grid slicewp-grid-affiliate-dashboard slicewp-grid-affiliate-dashboard-program-details">
    
    <?php
        
        // Get the supported commissions.
        $available_commission_types = slicewp_get_available_commission_types();
        $affiliate_commission_rates = slicewp_get_affiliate_commission_rates( $args['affiliate_id'] );

    ?>

    <div class="slicewp-card slicewp-card-affiliate-dashboard">

        <div class="slicewp-card-inner">

            <div class="slicewp-card-title"><?php echo ( count( $affiliate_commission_rates ) > 1 ? __( 'Commission Rates', 'slicewp' ) : __( 'Commission Rate', 'slicewp' ) ); ?></div>
            
            <?php

                foreach ( $affiliate_commission_rates as $key => $details ) {

                    if ( empty( $available_commission_types[$key] ) )
                        continue;

                    echo '<span class="slicewp-commission-rate-tag-' . str_replace( '_', '-', esc_attr( $key ) ) . '">' . sprintf ( __( '%s rate: %s', 'slicewp' ), $available_commission_types[$key]['label'], ( $details['rate_type'] == 'percentage' ? $details['rate'] . '%' : slicewp_format_amount( $details['rate'], slicewp_get_currency_symbol( slicewp_get_setting( 'active_currency', 'USD' ) ) ) ) ) . '</span>';

                }

            ?>

        </div>

    </div>

    <div class="slicewp-card slicewp-card-affiliate-dashboard">

        <div class="slicewp-card-inner">

            <div class="slicewp-card-title"><?php echo __( 'Cookie Duration', 'slicewp' ); ?></div>
            <div class="slicewp-kpi-value"><?php echo sprintf( __( '%s days', 'slicewp'), slicewp_get_setting( 'cookie_duration' ) );?></div>

        </div>

    </div>

</div>