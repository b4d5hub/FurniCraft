<?php
/**
 * Template part for displaying a message that posts cannot be found.
 *
 * @link https://codex.wordpress.org/Template_Hierarchy
 *
 * @package Sober
 */

?>

<section class="no-results not-found">
	<header class="page-header-none">
		<h1 class="page-title"><?php esc_html_e( 'Nothing Found', 'sober' ); ?></h1>
	</header><!-- .page-header -->

	<div class="page-content">
		<?php if ( is_home() && current_user_can( 'publish_posts' ) ) : ?>

			<p>
				<?php
					printf(
						wp_kses(
							/* translators: %s link to post editor. */
							__( 'Ready to publish your first post? <a href="%s">Get started here</a>.', 'sober' ),
							array( 'a' => array( 'href' => array() ) )
						),
						esc_url( admin_url( 'post-new.php' ) )
					);
				?>
			</p>

		<?php elseif ( is_search() ) : ?>

			<p><?php esc_html_e( 'Sorry, but nothing matched your search terms. Please try again with some different keywords.', 'sober' ); ?></p>
			<?php get_search_form(); ?>

		<?php else : ?>

			<p><?php esc_html_e( 'It seems we can&rsquo;t find what you&rsquo;re looking for. Perhaps searching can help.', 'sober' ); ?></p>
			<?php get_search_form(); ?>

		<?php endif; ?>
	</div><!-- .page-content -->
</section><!-- .no-results -->
