<?php

/**
 * @var $field
 * @var $field_id
 * @var $field_value
 * @var $field_label
 * @var $field_name
 * @var $section_name
 *
 */
$field = "data['{$section_name}']['fields']['{$field_name}']";

?>

<wpcfto_notice :field_label="<?php echo esc_attr($field_label); ?>">
</wpcfto_notice>