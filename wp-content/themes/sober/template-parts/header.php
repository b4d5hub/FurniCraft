<?php
/**
 * The template for displaying header.
 *
 * @package Sober
 */
?>
<header id="masthead" class="site-header" role="banner">
	<div class="<?php echo 'full-width' == sober_get_option( 'header_wrapper' ) ? 'sober-container clearfix' : 'container' ?>">
		<?php do_action( 'sober_header' ); ?>
	</div>
</header><!-- #masthead -->