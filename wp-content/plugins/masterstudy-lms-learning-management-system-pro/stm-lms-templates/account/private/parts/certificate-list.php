<?php
/**
 * @var $current_user
 */

stm_lms_register_style('user-quizzes');
stm_lms_register_style('user-certificates');
$completed = stm_lms_get_user_completed_courses($current_user['id'], array('user_course_id', 'course_id'), -1);
stm_lms_register_script('affiliate_points');
stm_lms_register_style('affiliate_points');

if (!empty($completed)): ?>

    <div class="stm-lms-user-quizzes stm-lms-user-certificates">

        <div class="stm-lms-user-quiz__head heading_font">
            <div class="stm-lms-user-quiz__head_title">
                <?php esc_html_e('Course', 'masterstudy-lms-learning-management-system-pro'); ?>
            </div>
            <div class="stm-lms-user-quiz__head_status">
                <?php esc_html_e('Certificate', 'masterstudy-lms-learning-management-system-pro'); ?>
            </div>
        </div>

        <?php foreach ($completed as $course):
            $code = STM_LMS_Certificates::stm_lms_certificate_code($course['user_course_id'], $course['course_id']);
            ?>
            <div class="stm-lms-user-quiz">
                <div class="stm-lms-user-quiz__title">
                    <a href="<?php echo esc_url(get_the_permalink($course['course_id'])); ?>">
                        <?php echo wp_kses_post(get_the_title($course['course_id'])); ?>
                    </a>
                </div>
                <a href="<?php echo STM_LMS_Course::certificates_page_url($course['course_id']); ?>"
                   target="_blank"
                   class="stm-lms-user-quiz__name">
                    <?php esc_html_e('Download', 'masterstudy-lms-learning-management-system-pro'); ?>
                </a>

                <div class="affiliate_points heading_font" data-copy="<?php echo esc_attr($code); ?>">
                    <span class="hidden" id="<?php echo esc_attr($code); ?>"><?php echo esc_html($code); ?></span>
                    <span class="affiliate_points__btn">
                        <i class="fa fa-link"></i>
                        <span class="text"><?php esc_html_e('Copy code', 'masterstudy-lms-learning-management-system-pro'); ?></span>
                    </span>
                </div>

            </div>
        <?php endforeach; ?>

    </div>

<?php else: ?>

    <h4 class="no-certificates-notice"><?php esc_html_e('You do not have a certificate yet.', 'masterstudy-lms-learning-management-system-pro') ?></h4>
    <h4 class="no-certificates-notice"><?php esc_html_e('Get started easy, select a course here, pass it and get your first certificate', 'masterstudy-lms-learning-management-system-pro') ?></h4>

<?php endif; ?>