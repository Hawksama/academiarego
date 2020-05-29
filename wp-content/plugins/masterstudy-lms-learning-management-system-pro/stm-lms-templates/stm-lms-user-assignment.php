<?php
/**
 * @var $assignment_id
 */
if (!defined('ABSPATH')) exit; //Exit if accessed directly ?>

<?php
get_header();
stm_lms_register_style('user_assignment');
stm_lms_register_script('user_assignment', array('vue.js', 'vue-resource.js'));
wp_localize_script('stm-lms-user_assignment', 'stm_lms_user_assignment', array(
    'assignment_id' => $assignment_id,
    'translation' => array(
        'approve' => esc_html__('Do you really want to approve this Essay?', 'masterstudy-lms-learning-management-system-pro'),
        'reject' => esc_html__('Do you really want to reject this Essay?', 'masterstudy-lms-learning-management-system-pro'),
    ),
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

            <div id="stm_lms_user_assignment">
                <?php STM_LMS_Templates::show_lms_template(
                    'account/private/instructor_parts/user_assignments/main',
                    compact('assignment_id'));
                ?>
            </div>

        </div>

    </div>

<?php get_footer(); ?>