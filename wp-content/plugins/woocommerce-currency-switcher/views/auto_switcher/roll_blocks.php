<?php
if (!defined('ABSPATH'))
    die('No direct access allowed');
global $WOOCS;
$currencies = apply_filters('woocs_currency_manipulation_before_show', $WOOCS->get_currencies());
?>
<?php ob_start() ?> 
.woocs_auto_switcher {
top: <?php echo esc_attr($top) ?>;
} 
.woocs_auto_switcher ul li:hover {
background: <?php echo esc_attr($hover_color) ?>;
}
.woocs_auto_switcher ul li {
background: <?php echo esc_attr($color) ?>;
}
.woocs_auto_switcher ul li.woocs_auto_bg_woocs_curr_curr {
background: <?php echo esc_attr($hover_color) ?>;
}
<?php $roll_px = get_option('woocs_auto_switcher_roll_px', 90); ?>
.woocs_auto_switcher ul li:hover {
-webkit-transform: translate(<?php echo esc_attr($roll_px) ?>px, 0);
-moz-transform: translate(<?php echo esc_attr($roll_px) ?>px, 0);
-ms-transform: translate(<?php echo esc_attr($roll_px) ?>px, 0);
-o-transform: translate(<?php echo esc_attr($roll_px) ?>px, 0);
transform: translate(<?php echo esc_attr($roll_px) ?>px, 0);
}

.woocs_auto_switcher.right ul li:hover {
-webkit-transform: translate(-<?php echo esc_attr($roll_px) ?>px, 0);
-moz-transform: translate(-<?php echo esc_attr($roll_px) ?>px, 0);
-ms-transform: translate(-<?php echo esc_attr($roll_px) ?>px, 0);
-o-transform: translate(-<?php echo esc_attr($roll_px) ?>px, 0);
transform: translate(-<?php echo esc_attr($roll_px) ?>px, 0);
}    
<?php
$data = ob_get_clean();
wp_add_inline_style('woocommerce-currency-switcher', $data);
?>  
<nav class="woocs_auto_switcher <?php echo esc_attr($side) ?>" data-view="roll_blocks">
    <ul>
        <?php
        foreach ($currencies as $key => $item):

            if (isset($item['hide_on_front']) AND $item['hide_on_front']) {
                continue;
            }

            $current = "";
            if ($key == $WOOCS->current_currency) {
                $current = "woocs_curr_curr";
            }
            $base_text = $this->prepare_field_text($item, $basic_field);
            $add_text = $this->prepare_field_text($item, $add_field);
            ?>  
            <li class="woocs_auto_bg_<?php echo esc_attr($current) ?>">
                <a data-currency="<?php echo esc_attr($key) ?>" class="woocs_auto_switcher_link <?php echo esc_attr($current) ?>" href="#">
                    <?php
                    $r_add_text = "";
                    if ($side == 'right') {
                        $r_add_text = $add_text;
                        $add_text = "";
                    }
                    ?><?php echo wp_kses_post($add_text) ?><span class="woocs_add_field"><?php echo wp_kses_post($base_text) ?></span>
                    <?php echo wp_kses_post($r_add_text) ?> 
                </a> 
            </li>
        <?php endforeach; ?>
    </ul>

</nav>
