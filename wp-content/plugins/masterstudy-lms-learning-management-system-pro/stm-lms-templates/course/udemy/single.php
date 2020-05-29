<?php if (!defined('ABSPATH')) exit; //Exit if accessed directly ?>

<?php
stm_lms_register_style('course');
stm_lms_register_style('course-udemy');

stm_lms_register_script('sticky-sidebar', array('resize-sensor', 'sticky-sidebar'));

$udemy_meta = STM_LMS_Helpers::simplify_meta_array(get_post_meta(get_the_ID()));

?>

<?php if (function_exists('bcn_display')) {
	STM_LMS_Templates::show_lms_template('course/udemy/parts/breadcrumbs');
} ?>

    <div class="row cols-same-height">

        <div class="col-md-8 col-sm-7">

            <div class="stm_lms_udemy_bar">

				<?php STM_LMS_Templates::show_lms_template('course/udemy/parts/panel_info/rate'); ?>

                <h1 class="stm_lms_course__title"><?php the_title(); ?></h1>

				<?php STM_LMS_Templates::show_lms_template('course/udemy/parts/headline', array('udemy_meta' => $udemy_meta)); ?>

				<?php STM_LMS_Templates::show_lms_template('course/udemy/parts/panel_info', array('udemy_meta' => $udemy_meta)); ?>

            </div>

			<?php STM_LMS_Templates::show_lms_template('course/udemy/parts/objectives', array('udemy_meta' => $udemy_meta)); ?>

            <div class="stm_lms_course__tabs">
			    <?php STM_LMS_Templates::show_lms_template('course/udemy/parts/tabs'); ?>
            </div>

        </div>

        <div class="col-md-4 col-sm-5 udemy-sidebar-holder">

			<?php STM_LMS_Templates::show_lms_template('course/udemy/sidebar'); ?>

        </div>

    </div>

<?php STM_LMS_Udemy::affiliate_automate_links();

STM_LMS_Templates::show_lms_template('course/sticky/panel');