<?php if (!defined('ABSPATH')) die('No direct access allowed'); ?>
<span class="woocs_current_currency woocs_current_currency_<?php echo esc_attr($currency) ?>">

    <?php if (!empty($text)): ?>
        <strong class="woocs_current_currency_text"><?php echo esc_html($text) ?></strong>
    <?php endif; ?>

    <?php if ($code): ?>
        <strong class="woocs_current_currency_code"><?php echo esc_html($currencies[$currency]['name']) ?></strong>&nbsp;
    <?php endif; ?>

    <?php if ($flag): ?>
        <img class="woocs_current_currency_flag" src="<?php echo esc_attr($currencies[$currency]['flag']) ?>" alt="" />
    <?php endif; ?>

</span>
