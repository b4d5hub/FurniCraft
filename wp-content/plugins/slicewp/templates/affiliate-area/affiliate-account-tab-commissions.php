<?php
/**
 * Affiliate account tab: Commissions
 *
 * This template can be overridden by copying it to yourtheme/slicewp/affiliate-area/affiliate-account-tab-commissions.php.
 *
 * HOWEVER, on occasion SliceWP will need to update template files and you
 * (the theme developer) will need to copy the new files to your theme to
 * maintain compatibility.
 *
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

?>

<?php

// Verify if a Payment ID is provided.
if ( ! empty( $_GET['payment_id'] ) ) {

    $payment_id = absint( $_GET['payment_id'] );

    // Read the Payment
    $payment = slicewp_get_payment( $payment_id );

    // Get the Commissions IDs from the Payment.
    if ( ! empty( $payment ) && $payment->get( 'affiliate_id' ) == $args['affiliate_id'] ) {

        $redirect_url = add_query_arg( array( 'affiliate-account-tab' => 'commissions' ), strtok( set_url_scheme( 'http://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'] ), '?' ) );

        echo sprintf( __( 'Showing all the commissions from Payout #%d.<br><a href="%s">View all commissions.</a><br><br>', 'slicewp' ), $payment_id, $redirect_url );

        $table = new SliceWP_List_Table_Affiliate_Account_Payment_Commissions( array(
            'screen_base_url' => add_query_arg( array( 'affiliate-account-tab' => 'commissions' ), strtok( set_url_scheme( 'http://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'] ), '?' ) ), 'args' => array( 'payment' => $payment )
        ));

        $table->output();

    }

} else {

    $table = new SliceWP_List_Table_Affiliate_Account_Commissions( array(
        'screen_base_url' => add_query_arg( array( 'affiliate-account-tab' => 'commissions' ), strtok( set_url_scheme( 'http://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'] ), '?' ) )
    ));
    $table->output();

}