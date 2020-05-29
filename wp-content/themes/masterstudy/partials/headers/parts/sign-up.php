<?php if (class_exists('STM_LMS_User')): ?>
    <a href="<?php echo esc_url(STM_LMS_User::login_page_url()); ?>" class="btn btn-default"
       data-text="<?php esc_html_e('Sign in', 'masterstudy') ?>">
        <span><?php esc_html_e('Sign Up', 'masterstudy'); ?></span>
    </a>
<?php endif;