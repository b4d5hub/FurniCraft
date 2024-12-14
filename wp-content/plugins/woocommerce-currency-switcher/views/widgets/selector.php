<?php if (!defined('ABSPATH')) die('No direct access allowed'); ?>


<?php



if (isset($args['before_widget']))
{
    echo wp_kses_post($args['before_widget']);
}
?>

<div class="widget widget-woocommerce-currency-switcher">
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


    <?php
	$show_flags = true;
	if (isset($instance['show_flags']))	{
		$show_flags = $instance['show_flags'];
	}
	if (!isset($instance['width'])) {
		$instance['width'] = '100%';
	}
	if (!isset($instance['flag_position'])) {
		$instance['flag_position'] = 'right';
	}
	
    if ($show_flags === 'true')
    {
        $show_flags = 1;
    } else
    {
        $show_flags = 0;
    }
    //+++
    $txt_type = 'code';
    if (isset($instance['txt_type']))
    {
        $txt_type = $instance['txt_type'];
    }
    echo do_shortcode("[woocs txt_type='{$txt_type}' show_flags={$show_flags} width='{$instance['width']}' flag_position='{$instance['flag_position']}']");
    ?>
</div>

<?php
if (isset($args['after_widget']))
{
    echo wp_kses_post($args['after_widget']);
}

