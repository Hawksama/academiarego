<?php
/**
 * @var $field
 * @var $field_name
 * @var $section_name
 *
 */

$field_key = "data['{$section_name}']['fields']['{$field_name}']";

include STM_WPCFTO_PATH .'/metaboxes/components_js/faq.php';
?>

<label v-html="<?php echo esc_attr($field_key); ?>['label']"></label>

<stm-faq v-bind:stored_faq="<?php echo esc_attr($field_key); ?>['value']"
         v-on:get-faq="<?php echo esc_attr($field_key); ?>['value'] = $event"></stm-faq>

<input type="hidden"
       name="<?php echo esc_attr($field_name); ?>"
       v-model="<?php echo esc_attr($field_key); ?>['value']" />