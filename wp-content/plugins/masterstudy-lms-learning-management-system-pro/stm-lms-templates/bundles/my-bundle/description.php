<?php
/**
 * @var $bundle_id
 * @var $bundle_data
 */

wp_enqueue_editor();

$post_content = (!empty($bundle_data->post_content)) ? $bundle_data->post_content : '';
?>

<div class="stm_lms_my_bundle__description">

    <h4 class="stm_lms_my_bundle__label">
        <?php esc_html_e('Bundle description', 'masterstudy-lms-learning-management-system-pro'); ?>
    </h4>

    <?php wp_editor($post_content, "stm_lms_bundle_name_{$bundle_id}"); ?>

</div>
