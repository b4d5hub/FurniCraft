<?php
/**
 * Affiliate account tab: Payments
 *
 * This template can be overridden by copying it to yourtheme/slicewp/affiliate-area/affiliate-account-tab-payments.php.
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

    $table = new SliceWP_List_Table_Affiliate_Account_Payments( array(
        'screen_base_url' => add_query_arg( array( 'affiliate-account-tab' => 'payments' ), strtok( set_url_scheme( 'http://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'] ), '?' ) )
    ));

    $table->output();
    
?>