<?php if (!defined('ABSPATH')) exit; //Exit if accessed directly ?>

<?php
ob_start();
include STM_LMS_PATH . '/wp-custom-fields-theme-options/metaboxes/components_js/curriculum.php';
$script = ob_get_clean();
wp_add_inline_script(
	'vue-select2.js',
	str_replace(array(
		'<script>', '</script>', 'stm_lms_edit_item_action"'),
		array('', '', 'stm_lms_edit_item_action" @click.prevent="emitMethod(item)"'),
		$script
	),
	'after'
);

ob_start();
include STM_LMS_PATH . '/wp-custom-fields-theme-options/metaboxes/components_js/answers.php';
include STM_LMS_PATH . '/wp-custom-fields-theme-options/metaboxes/components_js/questions.php';
$script = ob_get_clean();
wp_add_inline_script(
	'stm-lms-manage_course',
	str_replace(array(
		'<script>', '</script>'),
		array('', ''),
		$script
	),
	'before'
)
?>

<?php STM_LMS_Templates::show_lms_template('manage_course/forms/js/modal'); ?>
<?php STM_LMS_Templates::show_lms_template('manage_course/forms/js/lesson'); ?>
<?php STM_LMS_Templates::show_lms_template('manage_course/forms/js/assignment'); ?>
<?php STM_LMS_Templates::show_lms_template('manage_course/forms/js/quiz'); ?>
<stm-modal></stm-modal>

<div class="stm_metaboxes_grid">
    <div class="stm_metaboxes_grid__inner">

        <?php do_action('stm_lms_manage_course_before_curriculum') ?>

        <stm-curriculum v-bind:posts="['stm-lessons', 'stm-quizzes', 'stm-assignments']"
                        v-bind:stored_ids="fields['curriculum']"
                        <?php do_action('stm_lms_manage_course_curriculum_atts'); ?>
                        v-on:get-ids="fields['curriculum'] = $event">
        </stm-curriculum>
    </div>
</div>


<input type="hidden"
       name="curriculum"
       v-model="fields['curriculum']"/>