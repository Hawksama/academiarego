<?php if (STM_LMS_Instructor::is_instructor()): ?>

    <div class="stm-lms-user_create_announcement_btn stm_assignment_btn">
        <a href="<?php echo STM_LMS_Course_Bundle::url(); ?>">
            <i class="fa fa-layer-group"></i>
            <span><?php esc_html_e('Bundles', 'masterstudy-lms-learning-management-system-pro'); ?></span>
        </a>
    </div>

<?php endif;