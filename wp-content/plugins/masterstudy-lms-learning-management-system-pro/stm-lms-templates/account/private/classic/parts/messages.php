<?php
/**
 * @var $current_user
 */

$new_messages = STM_LMS_Chat::user_new_messages($current_user['id']);

?>

<div class="stm-lms-user_create_announcement_btn stm_assignment_btn stm_assignment_btn_stm">
    <a href="<?php echo esc_url(STM_LMS_Chat::chat_url()); ?>">
        <i class="fa fa-envelope"></i>
        <?php if (!empty($new_messages)): ?>
            <label style="color: #385bce;"><?php echo intval($new_messages); ?></label>
        <?php endif; ?>
        <span><?php esc_html_e('My messages', 'masterstudy-lms-learning-management-system-pro'); ?></span>
    </a>
</div>