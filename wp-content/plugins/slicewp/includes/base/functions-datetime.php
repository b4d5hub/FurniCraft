<?php

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;


/**
 * Checks to see if the provided date is a valid format
 *
 * @param string $date
 * @param string $format
 *
 * @return bool
 *
 */
function slicewp_is_date_valid( $date, $format = 'Y-m-d' ) {

    $d = DateTime::createFromFormat( $format, $date );

    return $d && $d->format($format) === $date;

}


/**
 * Returns the date and time format saved in WP's settings page
 *
 * @return string
 *
 */
function slicewp_get_datetime_format() {

    $format = get_option( 'date_format' ) . ' ' . get_option( 'time_format' );

    /**
     * Filter the default date time format before returning
     *
     * @param string $format
     *
     */
    $format = apply_filters( 'slicewp_datetime_format', $format );

    return $format;

}


/**
 * Returns the current date and time in mysql format
 *
 * @return string
 *
 */
function slicewp_mysql_gmdate() {
    
    return current_time( 'mysql', true );

}


/**
 * Returns the date and time in user's language
 * 
 * @param string $date
 * 
 * @return string
 * 
 */
function slicewp_date_i18n( $date ) {

    return date_i18n( slicewp_get_datetime_format(), strtotime( get_date_from_gmt( $date ) ) );

}


/**
 * Returns an array with the predefined date ranges options.
 * 
 * @return array
 * 
 */
function slicewp_get_predefined_date_ranges() {

    return array(
        'past_7_days'   => __( 'Past 7 days', 'slicewp' ),
        'past_30_days'  => __( 'Past 30 days', 'slicewp' ),
        'week_to_date'  => __( 'Week to date', 'slicewp' ),
        'month_to_date' => __( 'Month to date', 'slicewp' ),
        'year_to_date'  => __( 'Year to date', 'slicewp' ),
        'last_week'     => __( 'Last week', 'slicewp' ),
        'last_month'    => __( 'Last month', 'slicewp' ),
        'last_year'     => __( 'Last year', 'slicewp' )
    );

}


/**
 * Returns an array with the start and end dates for the given date range.
 * 
 * @return array|null
 * 
 */
function slicewp_get_date_range_dates( $date_range ) {

    $dates = array();

    $date_start = '';
    $date_end   = '';

    $timezone_object = new DateTimeZone( 'GMT' );

    if ( 'past_7_days' == $date_range ) {

        $date_start = ( new DateTime( 'now', $timezone_object ) )->sub( new DateInterval( 'P6D' ) );
        $date_end   = new DateTime( 'now', $timezone_object );

    }

    if ( 'past_30_days' == $date_range ) {

        $date_start = ( new DateTime( 'now', $timezone_object ) )->sub( new DateInterval( 'P29D' ) );
        $date_end   = new DateTime( 'now', $timezone_object );

    }

    if ( 'week_to_date' == $date_range ) {

        $date_start = new DateTime( 'Monday this week', $timezone_object );
        $date_end   = new DateTime( 'now', $timezone_object );

    }

    if ( 'month_to_date' == $date_range ) {

        $date_start = new DateTime( 'First day of this month', $timezone_object );
        $date_end   = new DateTime( 'now', $timezone_object );

    }

    if ( 'year_to_date' == $date_range ) {
        
        $date_start = new DateTime( 'First day of January this year', $timezone_object );
        $date_end   = new DateTime( 'now', $timezone_object );

    }

    if ( 'last_week' == $date_range ) {

        $date_start = new DateTime( 'Monday last week', $timezone_object );
        $date_end   = new DateTime( 'Sunday last week', $timezone_object );

    }

    if ( 'last_month' == $date_range ) {

        $date_start = new DateTime( 'First day of last month', $timezone_object );
        $date_end   = new DateTime( 'Last day of last month', $timezone_object );

    }

    if ( 'last_year' == $date_range ) {

        $date_start = new DateTime( 'First day of January last year', $timezone_object );
        $date_end   = new DateTime( 'Last day of December last year', $timezone_object );

    }

    if ( ! empty( $date_start ) && ! empty( $date_end ) ) {

        $dates['date_start'] = $date_start->setTime( 00, 00, 00 )->format( 'Y-m-d H:i:s' );
        $dates['date_end']   = $date_end->setTime( 23, 59, 59 )->format( 'Y-m-d H:i:s' );

    }

    return ( ! empty( $dates ) ? $dates : null );

}


/**
 * Returns an array with the start and end dates of the comparison date range for the given date range.
 * 
 * @return array|null
 * 
 */
function slicewp_get_date_range_comparison_dates( $date_range ) {

    $dates = array();

    $date_start = '';
    $date_end   = '';

    $timezone_object = new DateTimeZone( 'GMT' );

    if ( 'past_7_days' == $date_range ) {

        $date_start = ( new DateTime( 'now', $timezone_object ) )->sub( new DateInterval( 'P13D' ) );
        $date_end   = ( new DateTime( 'now', $timezone_object ) )->sub( new DateInterval( 'P7D' ) );

    }

    if ( 'past_30_days' == $date_range ) {

        $date_start = ( new DateTime( 'now', $timezone_object ) )->sub( new DateInterval( 'P59D' ) );
        $date_end   = ( new DateTime( 'now', $timezone_object ) )->sub( new DateInterval( 'P30D' ) );

    }

    if ( 'week_to_date' == $date_range ) {

        $date_start = new DateTime( 'Monday last week', $timezone_object );
        $date_end   = new DateTime( '-1 week', $timezone_object );

    }

    if ( 'month_to_date' == $date_range ) {

        $now = new DateTime( 'now', $timezone_object );

        $date_start = new DateTime( 'First day of last month', $timezone_object );

        $date_end = clone $date_start;

        if ( $now->format( 'j' ) <= $date_end->format( 't' ) ) {
            $date_end->setDate( $date_end->format( 'Y' ), $date_end->format( 'm' ), $now->format( 'd' ) );
        } else {
            $date_end->setDate( $date_end->format( 'Y' ), $date_end->format( 'm' ), $date_end->format( 't' ) );
        }

    }

    if ( 'year_to_date' == $date_range ) {

        $now = new DateTime( 'now', $timezone_object );

        $date_start = new DateTime( 'First day of January last year', $timezone_object );

        $date_end = clone $date_start;

        $date_end->setDate( $date_start->format( 'Y' ), $now->format( 'm' ), $now->format( 'd' ) );

        // Account for February 29th.
        if ( $now->format( 'n' ) == 2 && $now->format( 'd' ) == 29 ) {
            $date_end->setDate( $date_start->format( 'Y' ), $now->format( 'm' ), 28 );
        }

    }

    if ( 'last_week' == $date_range ) {

        $date_start = ( new DateTime( 'Monday last week', $timezone_object ) )->modify( '-7 days' );
        $date_end   = ( new DateTime( 'Sunday last week', $timezone_object ) )->modify( '-7 days' );

    }

    if ( 'last_month' == $date_range ) {

        $date_start = ( new DateTime( 'First day of last month', $timezone_object ) )->modify( '-15 days' )->modify( 'First day of this month' );
        
        $date_end = clone $date_start;
        $date_end->modify( 'Last day of this month' );

    }

    if ( 'last_year' == $date_range ) {

        $date_start = new DateTime( 'First day of January last year', $timezone_object );
        $date_start->setDate( ( $date_start->format( 'Y' ) - 1 ), $date_start->format( 'm' ), $date_start->format( 'd' ) );

        $date_end   = new DateTime( 'Last day of December last year', $timezone_object );
        $date_end->setDate( ( $date_end->format( 'Y' ) - 1 ), $date_end->format( 'm' ), $date_end->format( 'd' ) );

    }

    if ( ! empty( $date_start ) && ! empty( $date_end ) ) {

        $dates['date_start'] = $date_start->setTime( 00, 00, 00 )->format( 'Y-m-d H:i:s' );
        $dates['date_end']   = $date_end->setTime( 23, 59, 59 )->format( 'Y-m-d H:i:s' );

    }

    return ( ! empty( $dates ) ? $dates : null );

}