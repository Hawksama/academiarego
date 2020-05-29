<?php
/**
 * @var $assignment_id
 */
if (!defined('ABSPATH')) exit; //Exit if accessed directly ?>

<?php
get_header();
stm_lms_register_style('instructor_assignment');
stm_lms_register_script('instructor_assignment', array('vue.js', 'vue-resource.js'));
wp_localize_script('stm-lms-instructor_assignment', 'stm_lms_assignment', array(
    'assignment_id' => $assignment_id,
    'response' => STM_LMS_Single_Assignment::get_user_assignments($assignment_id),
    'sort' => array(
        'pending' => sprintf(esc_html__('Status: %sPending%s', 'masterstudy-lms-learning-management-system-pro'), "<span>", "</span>"),
        'passed' => sprintf(esc_html__('Status: %sPassed%s', 'masterstudy-lms-learning-management-system-pro'), "<span>", "</span>"),
        'not_passed' => sprintf(esc_html__('Status: %sNon passed%s', 'masterstudy-lms-learning-management-system-pro'), "<span>", "</span>"),
    ),
    'translations' => array(
        'group_limit' => esc_html__('Group Limit:', 'masterstudy-lms-learning-management-system-pro'),
    )
));
do_action('stm_lms_template_main');
?>

    <div class="stm-lms-wrapper stm-lms-wrapper--assignments">

        <div class="container">

            <div class="stm-lms-wrapper--gradebook_header">

                <a href="<?php echo esc_url(STM_LMS_Instructor_Assignments::assignments_url()); ?>">
                    <i class="lnricons-arrow-left"></i>
                    <?php esc_html_e('Back to Assignments', 'masterstudy-lms-learning-management-system-pro'); ?>
                </a>

            </div>

            <div id="stm_lms_instructor_assignment">
                <?php STM_LMS_Templates::show_lms_template(
                    'account/private/instructor_parts/assignments/single/main',
                    compact('assignment_id'));
                ?>
            </div>

        </div>

    </div>

<?php get_footer(); ?>