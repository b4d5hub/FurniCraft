<?php
/**
 * Template for element param group content.
 */

if ( ! defined( 'ABSPATH' ) ) {
	die( '-1' );
}

$template = vc_include_template( 'params/param_group/inner_content.tpl.php' );

return '<li class="vc_param wpb_vc_row vc_param_group-collapsed">' . $template . '</li>';