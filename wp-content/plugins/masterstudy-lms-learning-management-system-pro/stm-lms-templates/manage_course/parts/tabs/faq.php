<?php
stm_lms_register_style('faq');

ob_start();
include STM_LMS_PATH . '/wp-custom-fields-theme-options/metaboxes/components_js/faq.php';
$script = ob_get_clean();
wp_add_inline_script(
	'vue-select2.js',
	str_replace(array('<script>', '</script>'), '', $script),
	'after'
);
?>

<div class="stm_metaboxes_grid">
    <div class="stm_metaboxes_grid__inner">

        <label v-html="'<?php esc_html_e('FAQ', 'masterstudy-lms-learning-management-system-pro'); ?>'"></label>

        <stm-faq v-bind:stored_faq="fields['faq']"
                 v-on:get-faq="fields['faq'] = $event"></stm-faq>

        <input type="hidden"
               name="faq"
               v-model="fields['faq']"/>

    </div>
</div>

<input type="hidden" v-model="fields['faq']"/>