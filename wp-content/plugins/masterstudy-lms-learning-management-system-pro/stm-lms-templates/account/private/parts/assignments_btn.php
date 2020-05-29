<?php

if (STM_LMS_Instructor::is_instructor()):
    $total_pending_assignments = STM_LMS_Instructor_Assignments::total_pending_assignments();

    ?>

    <div class="stm-lms-user_create_announcement_btn stm_assignment_btn stm_assignment_btn_stm">
        <a href="<?php echo STM_LMS_Instructor_Assignments::assignments_url(); ?>">
            <i class="fa fa-book-open"></i>
            <span><?php esc_html_e('Assignments', 'masterstudy-lms-learning-management-system-pro'); ?></span>
            <?php if (!empty($total_pending_assignments)): ?>
                <label style="color: #385bce;"><?php echo intval($total_pending_assignments); ?></label>
            <?php endif; ?>
        </a>
    </div>

<?php endif;