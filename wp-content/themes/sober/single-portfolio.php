<?php
/**
 * The template for displaying all single portfolios.
 *
 * @link    https://developer.wordpress.org/themes/basics/template-hierarchy/
 *
 * @package Sober
 */

get_header(); ?>

	<div id="primary" class="content-area">
		<main id="main" class="site-main" role="main">

			<?php

			while ( have_posts() ) : the_post();

				get_template_part( 'template-parts/content-single', 'portfolio' );

			endwhile; // End of the loop.

			if ( sober_get_option( 'project_navigation' ) ) {
				the_post_navigation( array(
					'prev_text' => sober_svg_icon( 'icon=left-arrow&echo=0' ) . '<span>' . sober_get_option( 'project_nav_text_prev' ) . '</span>',
					'next_text' => '<span>' . sober_get_option( 'project_nav_text_next' ) . '</span>' . sober_svg_icon( 'icon=right-arrow&echo=0' ),
					'screen_reader_text' => esc_html__( 'Project navigation', 'sober' ),
				) );
			}

			?>

		</main><!-- #main -->
	</div><!-- #primary -->

<?php
get_sidebar();
get_footer();
