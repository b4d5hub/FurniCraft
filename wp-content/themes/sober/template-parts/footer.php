<?php
/**
 * The template for displaying footer.
 *
 * @package Sober
 */
?>

<footer id="colophon" class="site-footer <?php echo esc_attr( implode( ' ', (array) apply_filters( 'sober_footer_class', array() ) ) ); ?>" role="contentinfo">
	<?php do_action( 'sober_footer' ) ?>
</footer><!-- #colophon -->