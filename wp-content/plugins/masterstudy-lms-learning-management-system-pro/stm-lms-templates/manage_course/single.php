<?php
/**
 * @var $course_id
 */

if (!defined('ABSPATH')) exit; //Exit if accessed directly ?>

<?php

$v = time();
$base = STM_LMS_URL . '/wp-custom-fields-theme-options/metaboxes/assets/';
$assets = STM_LMS_URL . 'assets';

stm_lms_register_style('course');
stm_lms_register_style('lesson');
stm_lms_register_style('manage_course');
wp_enqueue_style('stm-lms-metaboxes.css', $base . 'css/main.css', array(), $v);
wp_enqueue_style('stm-lms-icons', STM_LMS_URL . 'assets/icons/style.css', array(), $v);
wp_enqueue_style('linear-icons', $base . 'css/linear-icons.css', array('stm-lms-metaboxes.css'), $v);
wp_enqueue_style('font-awesome-min', $assets . '/vendors/font-awesome.min.css', NULL, $v, 'all');

stm_lms_pro_register_script('vue-tinymce/tinymce.min');
stm_lms_pro_register_script('vue-tinymce/vue-easy-tinymce.min');
wp_enqueue_script('sortable.js', $base . 'js/sortable.min.js', array('vue.js'), $v);
wp_enqueue_script('vue-draggable.js', $base . 'js/vue-draggable.min.js', array('sortable.js'), 1, false);
wp_add_inline_script('vue-draggable.js', 'const STM_LMS_EventBus = new Vue();');

wp_enqueue_script('vue2-editor.js', $base . 'js/vue2-editor.min.js', array('vue.js'), $v);
wp_enqueue_script('vue-select2.js', $base . 'js/vue-select.js', array(), 1, false);

wp_enqueue_script('vue2-datepicker.js', $base . 'js/vue2-datepicker.min.js', array(), $v);

stm_lms_pro_register_script('manage_course', array('vue.js', 'vue-resource.js'));

wp_enqueue_media();
wp_enqueue_script('stm_lms_mixins.js', $base . 'js/mixins.js', array('vue.js'), $v);

wp_add_inline_script('stm-lms-manage_course', STM_LMS_Manage_Course::localize_script($course_id), 'before');

/*GENERAL COMPONENTS*/
$components = array(
    'text',
//    'time',
//    'number',
    'image',
//    'checkbox',
//    'date',
//    'dates',
//    'select',
//    'radio',
//    'textarea',
//    'color',
    'autocomplete',
//    'editor',
    'repeater',
    'file',
);

foreach ($components as $component) {
    wp_enqueue_script(
        "wpcfto_{$component}_component",
        STM_WPCFTO_URL . "/metaboxes/general_components/js/{$component}.js",
        array('stm-lms-manage_course'),
        $v,
        true
    );
}

wp_enqueue_editor(); ?>

<a href="<?php echo esc_url(STM_LMS_User::user_page_url()); ?>" class="back-to-account">
    <i class="lnricons-arrow-left"></i>
    <?php esc_html_e('Back to account', 'masterstudy-lms-learning-management-system-pro'); ?>
</a>

<div id="stm_lms_manage_course" v-bind:class="{'loading' : loading}">

    <div class="container">

        <div class="row">

            <div class="col-md-9">

                <div class="stm_lms_manage_course stm_lms_manage_course__text stm_lms_manage_course__title stm_lms_wizard_step_1">
                    <h1 class="stm_lms_course__title" v-html="fields['title']" v-if="fields['title']"></h1>
                    <h1 class="stm_lms_course__title stm_lms_phantom" v-html="i18n['title']"
                        v-if="!fields['title']"></h1>
                    <?php STM_LMS_Templates::show_lms_template(
                        'manage_course/forms/text',
                        array('field_key' => 'title')
                    ); ?>
                </div>

                <?php STM_LMS_Templates::show_lms_template('manage_course/parts/panel_info', array('course_id' => $course_id)); ?>

                <?php STM_LMS_Templates::show_lms_template('manage_course/parts/tabs'); ?>

            </div>

            <div class="col-md-3">

                <?php STM_LMS_Templates::show_lms_template('manage_course/sidebar', array('post_id' => $course_id)); ?>

            </div>

        </div>

        <div class="stm_lms_manage_course__actions">
            <a href="#" class="btn btn-default" @click.prevent="saveCourse()">
                <span v-if="!fields['post_id']"><?php esc_html_e('Publish Course', 'masterstudy-lms-learning-management-system-pro'); ?></span>
                <span v-else><?php esc_html_e('Update Course', 'masterstudy-lms-learning-management-system-pro'); ?></span>
            </a>
        </div>

        <transition name="slide-fade">
            <div class="stm-lms-message" v-bind:class="status" v-if="message" v-html="message"></div>
        </transition>

        <?php STM_LMS_Templates::show_lms_template('manage_course/wizard/main'); ?>

    </div>

</div>