<?php
/**
 * @var $field_key
 * @var $listener
 *
 */

$listener = (!empty($listener));

?>

<?php STM_LMS_Templates::show_lms_template('manage_course/forms/js/editor'); ?>

<div class="stm_lms_manage_course__editor">
    <stm-editor v-bind:content="fields['<?php echo esc_attr($field_key) ?>']"
                v-bind:listener="'<?php echo $listener; ?>'"
                v-bind:content_edited.sync="fields['<?php echo esc_attr($field_key) ?>']"
                v-on:content-changed="fields['<?php echo esc_attr($field_key) ?>'] = $event"></stm-editor>

    <textarea class="hidden" v-model="fields['<?php echo esc_attr($field_key) ?>']"></textarea>
</div>