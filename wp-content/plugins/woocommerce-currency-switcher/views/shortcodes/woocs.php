<?php if (!defined('ABSPATH')) die('No direct access allowed'); ?>
<?php
//*** hide if there is checkout page
global $post;
if (!class_exists('WooCommerce')) {
    echo "<div class='notice'>" . esc_html__('Warning: Woocommerce is not activated', 'woocommerce-currency-switcher') . "</div>";
    return;
}
if (get_option('woocs_restrike_on_checkout_page', 0)) {
    if (is_object($post)) {
        if ($this->get_checkout_page_id() == $post->ID) {
            return "";
        }
    }
}

$all_currencies = apply_filters('woocs_currency_manipulation_before_show', $this->get_currencies());

//***
if (!isset($shortcode_params['sd'])) {
    $drop_down_view = $this->get_drop_down_view();

//for specials separated skins (style-1, style-2, etc...)
    if (isset($shortcode_params['style']) AND intval($shortcode_params['style']) > 0) {
        $drop_down_view = 'style-' . intval($shortcode_params['style']);
    }

    if (substr($drop_down_view, 0, 5) === 'style') {
        $num = intval(substr(strrev($drop_down_view), 0, 1));
        $styles_link = WOOCS_LINK . 'views/shortcodes/styles/';
        $styles_path = WOOCS_PATH . 'views/shortcodes/styles/';

        wp_enqueue_style('woocs-style-' . $num, $styles_link . "style-{$num}/styles.css", array(), WOOCS_VERSION);
        wp_enqueue_script('woocs-style-' . $num, $styles_link . "style-{$num}/actions.js", array('jquery'), WOOCS_VERSION);
		$this->render_html_e($styles_path . "style-{$num}/index.php", $shortcode_params);
        return FALSE;
    }



//***
    if ($drop_down_view == 'flags') {
        foreach ($all_currencies as $key => $currency) {

            if (isset($currency['hide_on_front']) AND $currency['hide_on_front']) {
                continue;
            }

            if (!empty($currency['flag'])) {
                ?>
                <a href="#" class="woocs_flag_view_item <?php if ($this->current_currency == $key): ?>woocs_flag_view_item_current<?php endif; ?>" data-currency="<?php echo esc_attr($currency['name']) ?>" title="<?php echo esc_attr($currency['name']) . ', ' . esc_attr($currency['symbol']) . ' ' . esc_attr($currency['description']) ?>"><img src="<?php echo esc_attr($currency['flag']) ?>" alt="<?php echo esc_attr($currency['name']) . ', ' . esc_attr($currency['symbol']) ?>" /></a>
                <?php
            }
        }
    } else {
        $empty_flag = WOOCS_LINK . 'img/no_flag.png';
        $show_money_signs = get_option('woocs_show_money_signs', 1);
//***
        if (!isset($show_flags)) {
            $show_flags = get_option('woocs_show_flags', 1);
        }



        if (!isset($width)) {
            $width = '100%';
        }

        if (!isset($flag_position)) {
            $flag_position = 'right';
        }

        if (!isset($css_class)) {
            $css_class = '';
        }

        //+++

        $data = "";
        if ($drop_down_view == 'wselect') {
            $data = " .woocommerce-currency-switcher-form .wSelect, .woocommerce-currency-switcher-form .wSelect-options-holder {width:" . esc_attr($width) . "!important;}";
            if (!$show_flags) {
                $data .= " .woocommerce-currency-switcher-form .wSelect-option-icon{padding-left: 5px !important;}";
            }
        }
        if ($show_flags) {
            $data .= " .woocommerce-currency-switcher{ width:" . esc_attr($width) . ";}";
            foreach ($all_currencies as $key => $currency) {
                $data .= " .woocs_option_img_" . $key;
                $flag = (!empty($currency['flag']) ? $currency['flag'] : $empty_flag);
                $data .= "{ background: url(" . $flag . ") no-repeat 99% 0; background-size: 30px 20px; }";
            }
        }
        wp_add_inline_style('woocommerce-currency-switcher', $data);
        ?>


        <form method="<?php echo esc_attr(apply_filters('woocs_form_method', 'post')) ?>" action="" class="woocommerce-currency-switcher-form <?php if ($show_flags): ?>woocs_show_flags<?php endif; ?>" data-ver="<?php echo esc_attr(WOOCS_VERSION) ?>">
            <input type="hidden" name="woocommerce-currency-switcher" value="<?php echo esc_attr($this->current_currency) ?>" />
            <select name="woocommerce-currency-switcher" data-width="<?php echo esc_attr($width) ?>" data-flag-position="<?php echo esc_attr($flag_position) ?>" class="woocommerce-currency-switcher <?php echo esc_attr($css_class) ?>" onchange="woocs_redirect(this.value); void(0);">
                <?php foreach ($all_currencies as $key => $currency) : ?>

                    <?php
                    if (isset($currency['hide_on_front']) AND $currency['hide_on_front']) {
                        continue;
                    }

                    $option_txt = apply_filters('woocs_currname_in_option', $currency['name']);

                    if ($show_money_signs) {
                        if (!empty($option_txt)) {
                            $option_txt .= ', ' . $currency['symbol'];
                        } else {
                            $option_txt = $currency['symbol'];
                        }
                    }
                    //***
                    if (isset($txt_type)) {
                        if ($txt_type == 'desc') {
                            if (!empty($currency['description'])) {
                                $option_txt = $currency['description'];
                            }
                        }
                    }
                    ?>

                    <option class="woocs_option_img_<?php echo esc_attr($key) ?>" value="<?php echo esc_attr($key) ?>" <?php selected($this->current_currency, $key) ?> data-imagesrc="<?php if ($show_flags) echo esc_attr((!empty($currency['flag']) ? $currency['flag'] : $empty_flag)); ?>" data-icon="<?php if ($show_flags) echo esc_attr((!empty($currency['flag']) ? $currency['flag'] : $empty_flag)); ?>" data-description="<?php echo esc_attr($currency['description']) ?>"><?php echo esc_html($option_txt) ?></option>
                <?php endforeach; ?>
            </select>
            <div class="woocs_display_none" style="display: none;" >FOX v.<?php echo esc_html(WOOCS_VERSION) ?></div>
        </form>
        <?php
    }
} else {
    if (intval($sd_settings) !== -1) {
        global $WOOCS_SD;
        $currencies = $WOOCS_SD->get_currencies();
        $title_value = isset($sd_settings['title_value']) ? $sd_settings : '__CODE__';
        if (isset($_GET['action']) AND $_GET['action'] === 'elementor') {
            echo esc_html("[woocs sd={$sd_id}]"); //fix for elementor to avoid showing loader
        } else {
            ?>
            <div data-woocs-sd='<?php echo wc_esc_json(json_encode($sd_settings)) ?>' data-woocs-ver='<?php echo esc_attr(WOOCS_VERSION) ?>' style="width: <?php echo esc_attr((isset($sd_settings['width']) ? esc_attr($sd_settings['width']) . 'px' : 'auto')) ?>; max-width: 100%;" data-woocs-sd-currencies='<?php echo wc_esc_json(json_encode($currencies)) ?>'><div class="woocs-lds-ellipsis"><div></div><div></div><div></div><div></div></div></div>
            <?php
        }
    }
}

