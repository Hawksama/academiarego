<?php if ( ! defined( 'ABSPATH' ) ) exit; //Exit if accessed directly ?>

<?php
do_action('stm_lms_template_main');
$course_id = (is_string($course_id)) ? $course_id : ''; ?>

<?php get_header(); ?>

    <div class="stm-lms-wrapper">
        <div class="container">
            <?php STM_LMS_Templates::show_lms_template('manage_course/single', array('course_id' => $course_id)); ?>
        </div>
    </div>

<?php get_footer(); ?>