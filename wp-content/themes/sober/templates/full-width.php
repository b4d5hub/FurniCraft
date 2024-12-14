<?php
/**
 * Template Name: Full Width
 *
 * The template file for displaying page content in full-width
 */

get_header(); ?>

<?php
if ( have_posts() ) :
	while ( have_posts() ) : the_post();
		the_content();
	endwhile;
endif;
?>

<?php get_footer(); ?>
