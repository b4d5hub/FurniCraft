<?php if (!defined('ABSPATH')) die('No direct access allowed'); ?>


<?php
if (isset($args['before_widget']))
{
    echo wp_kses_post($args['before_widget']);
}
?>

<div class="widget widget-woocommerce-currency-converter">
    <?php
    if (!empty($instance['title']))
    {
        if (isset($args['before_title']))
        {
            echo wp_kses_post($args['before_title']);
            echo esc_html($instance['title']);
            echo wp_kses_post($args['after_title']);
        } else
        {
            ?>
            <h3 class="widget-title"><?php echo esc_html($instance['title']) ?></h3>
            <?php
        }
    }
    ?>
    <?php echo do_shortcode('[woocs_converter exclude="' . $instance['exclude'] . '" precision="' . $instance['precision'] . '"]'); ?>
</div>

<?php
if (isset($args['after_widget']))
{
    echo wp_kses_post($args['after_widget']);
}

