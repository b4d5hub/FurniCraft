<?php

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;


/**
 * Includes the batch processors files
 *
 */
function slicewp_include_files_batch_processors() {

	// Get legend dir path
	$dir_path = plugin_dir_path( __FILE__ );

	// Include batch processor manager class
	if( file_exists( $dir_path . 'class-batch-processor-manager.php' ) )
		include $dir_path . 'class-batch-processor-manager.php';

}
add_action( 'slicewp_include_files', 'slicewp_include_files_batch_processors' );