<?php

/**
 * @var $current_user
 */

?>
    <div class="stm_lms_account_classic_tabs">

        <div class="stm-lms-user_create_announcement_btn">
            <a href="<?php echo esc_url(STM_LMS_User::user_page_url()); ?>">
                <i class="fa fa-user"></i>
                <span><?php esc_html_e('Profile', 'masterstudy-lms-learning-management-system-pro'); ?></span>
            </a>
        </div>

        <?php if (STM_LMS_Instructor::is_instructor()) {
            STM_LMS_Templates::show_lms_template('account/private/classic/parts/create_announcement_btn', array('current_user' => $current_user));
        } ?>

        <?php STM_LMS_Templates::show_lms_template('account/private/parts/my_certificates_btn', array('current_user' => $current_user)); ?>

        <?php if (STM_LMS_Instructor::is_instructor()) {
            do_action('stm_lms_after_profile_buttons', $current_user);
        } ?>

        <?php do_action('stm_lms_after_profile_buttons_all', $current_user); ?>

        <?php STM_LMS_Templates::show_lms_template('account/private/classic/parts/messages', array('current_user' => $current_user)); ?>

    </div>

<?php stm_lms_register_script('classic_tabs');