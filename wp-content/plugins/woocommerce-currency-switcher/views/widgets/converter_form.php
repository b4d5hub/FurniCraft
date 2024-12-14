<?php if (!defined('ABSPATH')) die('No direct access allowed'); ?>


<p>
    <label for="<?php echo esc_attr($widget->get_field_id('title')); ?>"><?php esc_html_e('Title', 'woocommerce-currency-switcher') ?>:</label>
    <input class="widefat" type="text" id="<?php echo esc_attr($widget->get_field_id('title')); ?>" name="<?php echo esc_attr($widget->get_field_name('title')); ?>" value="<?php echo esc_html($instance['title']); ?>" />
</p>


<p>
    <label for="<?php echo esc_attr($widget->get_field_id('exclude')); ?>"><?php esc_html_e('Currencies excluding from view', 'woocommerce-currency-switcher') ?>:</label>
    <input class="widefat" type="text" id="<?php echo esc_attr($widget->get_field_id('exclude')); ?>" name="<?php echo esc_attr($widget->get_field_name('exclude')); ?>" value="<?php echo esc_html($instance['exclude']); ?>" />
    <br /><i><?php esc_html_e('Examples: EUR,GBP,UAH', 'woocommerce-currency-switcher') ?></i>
</p>


<p>
    <label for="<?php echo esc_attr($widget->get_field_id('precision')); ?>"><?php esc_html_e('Precision', 'woocommerce-currency-switcher') ?>:</label>
    <input class="widefat" type="text" id="<?php echo esc_attr($widget->get_field_id('precision')); ?>" name="<?php echo esc_attr($widget->get_field_name('precision')); ?>" value="<?php echo esc_html($instance['precision']); ?>" />
    <br /><i><?php esc_html_e('Count of digits after point', 'woocommerce-currency-switcher') ?></i>
</p>


