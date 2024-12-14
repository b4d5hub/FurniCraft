<?php
/**
 * Template part for displaying projects
 *
 * @link https://codex.wordpress.org/Template_Hierarchy
 *
 * @package Sober
 */
?>
<div id="project-<?php the_ID() ?>" <?php post_class( 'col-xs-6 col-sm-4 col-md-4' ) ?>>
	<?php if ( has_post_thumbnail() ) : ?>
		<a href="<?php the_permalink() ?>" class="project-thumbnail">
			<?php the_post_thumbnail( 'sober-portfolio' ) ?>
			<span class="view-more">
				<?php sober_svg_icon( 'icon=right-arrow' ) ?>
			</span>
		</a>
	<?php endif; ?>
	<h3 class="project-title"><a href="<?php the_permalink() ?>"><?php the_title(); ?></a></h3>
	<?php
	if ( has_term( null, 'portfolio_type' ) ) {
		the_terms( get_the_ID(), 'portfolio_type', '<div class="project-type">', ', ', '</div>' );
	}
	?>
</div>