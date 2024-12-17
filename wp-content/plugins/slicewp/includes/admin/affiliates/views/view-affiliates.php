<?php

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

?>

<div class="wrap slicewp-wrap slicewp-wrap-affiliates">

	<form method="GET">

		<!-- Page Heading -->
		<h1 class="wp-heading-inline"><?php echo __( 'Affiliates', 'slicewp' ); ?></h1>
		<?php $this->output_title_actions(); ?>
		<hr class="wp-header-end" />

		<!-- Affiliates List Table -->
		<?php 
			$table = new SliceWP_WP_List_Table_Affiliates();
			$table->views();
			$table->search_box( __( 'Search Affiliates', 'slicewp' ), 'affiliate_search' );
			$table->display();
		?>

		<!-- Hidden fields needed for the search query -->
		<input type="hidden" name="page" value="slicewp-affiliates">

	</form>

	<?php 

		/**
		 * Hook to add extra cards if needed
		 *
		 */
		do_action( 'slicewp_view_affiliates_bottom' );

	?>

</div>