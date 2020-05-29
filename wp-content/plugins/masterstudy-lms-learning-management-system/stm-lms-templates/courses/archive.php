<?php

$filter_enabled = STM_LMS_Courses::filter_enabled();

$args = apply_filters('stm_lms_archive_filter_args', array(
    'image_d' => 'img-480-380',
    'per_row' => STM_LMS_Options::get_option('courses_per_row', 4),
    'posts_per_page' => STM_LMS_Options::get_option('courses_per_page', get_option('posts_per_page')),
    'class' => 'archive_grid'
));

//stm_pa($args);

if ($filter_enabled) $args['per_row'] = 3;

?>

<div class="stm_lms_courses_wrapper">

    <?php STM_LMS_Templates::show_lms_template(
        'courses/filters',
        array('args' => $args)
    ); ?>

    <div class="stm_lms_courses__archive_wrapper">

        <?php if ($filter_enabled):
            stm_lms_register_style('courses_filter');
            stm_lms_register_script('courses_filter');
            ?>

            <?php STM_LMS_Templates::show_lms_template(
                'courses/advanced_filters/main',
                array('args' => $args)
            ); ?>

        <?php endif; ?>

        <div class="stm_lms_courses stm_lms_courses__archive">

            <?php STM_LMS_Templates::show_lms_template(
                'courses/' . STM_LMS_Options::get_option('course_view', 'grid'),
                array('args' => $args)
            ); ?>

            <?php STM_LMS_Templates::show_lms_template(
                'courses/load_more',
                array('args' => $args));
            ?>

        </div>

    </div>

</div>