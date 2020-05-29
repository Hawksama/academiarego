<?php

$value = (!empty($_GET['search'])) ? sanitize_text_field($_GET['search']) : '';

?>

<div class="stm_lms_courses__filter stm_lms_courses__search">

    <div class="stm_lms_courses__filter_heading">
        <h3><?php esc_html_e('Search', 'masterstudy-lms-learning-management-system'); ?></h3>
        <div class="toggler"></div>
    </div>

    <div class="stm_lms_courses__filter_content" style="display: none;">
        <input type="text"
               name="search"
               value="<?php echo esc_attr($value); ?>"
               placeholder="<?php esc_attr_e('Keywords', 'masterstudy-lms-learning-management-system'); ?>"/>
    </div>

</div>