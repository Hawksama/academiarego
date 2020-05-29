<?php

$cancel_subscription = STM_LMS_Options::get_option('cancel_subscription', false);

if ($cancel_subscription): ?>
    <div class="stm-lms-user_create_announcement_btn">
        <a href="<?php echo esc_url(get_permalink($cancel_subscription)) ?>" target="_blank">
            <i class="fa fa-ban"></i>
            <span><?php esc_html_e('Cancel subscription', 'masterstudy-lms-learning-management-system-pro'); ?></span>
        </a>
    </div>
<?php endif;