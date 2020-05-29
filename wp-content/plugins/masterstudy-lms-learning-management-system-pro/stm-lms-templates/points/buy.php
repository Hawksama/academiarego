<?php
/**
 * @var $course_id
 */

$user = STM_LMS_User::get_current_user();

$my_points = STM_LMS_Point_System::total_points($user['id']);
$course_price = STM_LMS_Point_System::course_price($course_id);

stm_lms_register_style('points-buy');
stm_lms_register_script('points-buy');
wp_localize_script('stm-lms-points-buy', 'stm_lms_points_buy', array(
    'translate' => array(
        'confirm' => sprintf(
            esc_html__('Do you really want to buy %s for %s?', 'masterstudy-lms-learning-management-system-pro'),
            get_the_title($course_id),
            STM_LMS_Point_System::display_points($course_price)
        )
    )
));

$classes = array('stm_lms_buy_for_points');

if($my_points < $course_price) $classes[] = "not-enough-points";

$distribution = '<span class="points_dist" data-href=" ' . esc_url(STM_LMS_Point_Distribution::points_distribution_url()) . '">
<i class="fa fa-question-circle"></i>
</span>';

?>

<a href="#" class="<?php echo esc_attr(implode(' ', $classes)); ?>" data-course="<?php echo esc_attr($course_id); ?>">
    <?php echo STM_LMS_Point_System::display_point_image(); ?>
    <div class="stm_lms_buy_for_points__text">
        <span class="points_price"><?php echo STM_LMS_Point_System::display_points($course_price); ?></span>
        <span class="my_points">
            <?php

            if($my_points < $course_price) {
                printf(
                    esc_html__('You need %s. %s', 'masterstudy-lms-learning-management-system-pro'),
                    STM_LMS_Point_System::display_points($course_price - $my_points),
                    $distribution
                );
            } else {
                printf(
                    esc_html__('You have %s. %s', 'masterstudy-lms-learning-management-system-pro'),
                    STM_LMS_Point_System::display_points($my_points),
                    $distribution
                );
            }

            ?>
        </span>
    </div>
</a>