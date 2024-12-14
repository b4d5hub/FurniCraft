<?php
/**
 * The template for displaying the footer.
 *
 * Contains the closing of the #content div and all content after.
 *
 * @link https://developer.wordpress.org/themes/basics/template-files/#template-partials
 *
 * @package Sober
 */
?>
		<?php do_action( 'sober_after_content' ); ?>
	</div><!-- #content -->

	<?php do_action( 'sober_before_footer' ) ?>

	<?php
	do_action( 'sober_before_footer' );

	// Elementor `footer` location
	if ( ! function_exists( 'elementor_theme_do_location' ) || ! elementor_theme_do_location( 'footer' ) ) {
		get_template_part( 'template-parts/footer' );
	}
	?>

</div><!-- #page -->

<?php do_action( 'sober_after_site' ) ?>

<?php wp_footer(); ?>

</body>
</html>