<?php
/**
 * @var $field_key
 * @var $select
 *
 */

?>

<div class="stm_lms_manage_course__form">
	<div class="stm_lms_manage_course__add">

        <select class="form-control disable-select"
                @change='getSelectedOption(fields["<?php echo esc_attr($field_key); ?>"], <?php echo json_encode($select); ?>, "<?php echo esc_attr($field_key); ?>")'
                v-init='getSelectedOption(fields["<?php echo esc_attr($field_key); ?>"], <?php echo json_encode($select); ?>, "<?php echo esc_attr($field_key); ?>")'
                v-model="fields['<?php echo esc_attr($field_key); ?>']" name="<?php echo esc_attr($field_key); ?>">
            <?php foreach($select as $key => $label): ?>
                <option value="<?php echo esc_attr($key) ?>"
                        label="<?php echo sanitize_text_field($label); ?>">
                    <?php echo sanitize_text_field($label); ?>
                </option>
            <?php endforeach; ?>
        </select>

        <?php do_action("stm_lms_after_{$field_key}_field"); ?>

	</div>
</div>