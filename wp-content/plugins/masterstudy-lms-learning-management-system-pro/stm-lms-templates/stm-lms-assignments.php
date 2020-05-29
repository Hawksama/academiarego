<?php if (!defined('ABSPATH')) exit; //Exit if accessed directly ?>

<?php
get_header();

if (!is_user_logged_in()) STM_LMS_User::js_redirect(STM_LMS_User::login_page_url());

stm_lms_register_style('instructor_assignments');
stm_lms_register_script('instructor_assignments', array('vue.js', 'vue-resource.js'));
wp_localize_script('stm-lms-instructor_assignments', 'stm_lms_assignments', array(
    'tasks' => STM_LMS_Instructor_Assignments::get_instructor_assignments(array('posts_per_page' => STM_LMS_Instructor_Assignments::per_page())),
    'translations' => array(
        'group_limit' => esc_html__('Group Limit:', 'masterstudy-lms-learning-management-system-pro'),
    )
));

do_action('stm_lms_template_main');

$style = STM_LMS_Options::get_option('profile_style', 'default');

?>

    <div class="stm-lms-wrapper stm-lms-wrapper--assignments">

        <div class="container">

            <div class="stm-lms-wrapper--gradebook_header">

                <?php if ($style === 'classic'):

                    STM_LMS_Templates::show_lms_template(
                        'account/private/classic/parts/header',
                        array('current_user' => STM_LMS_User::get_current_user('', true, true))
                    );

                else: ?>

                    <a href="<?php echo esc_url(STM_LMS_User::user_page_url()); ?>">
                        <i class="lnricons-arrow-left"></i>
                        <?php esc_html_e('Back to account', 'masterstudy-lms-learning-management-system-pro'); ?>
                    </a>

                <?php endif; ?>

            </div>

            <div id="stm_lms_instructor_assignments">
                <?php STM_LMS_Templates::show_lms_template('account/private/instructor_parts/assignments/grid'); ?>
            </div>


        </div>

    </div>

<?php get_footer(); ?>