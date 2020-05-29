<?php

if (function_exists('stm_lms_register_style')) {
	stm_lms_register_style('enterprise');
	stm_lms_register_script('enterprise');
}

if (is_user_logged_in()):
	$target = 'stm-lms-modal-become-instructor';
	$modal = 'become_instructor';

	if (function_exists('stm_lms_register_style')) {
		stm_lms_register_style('become_instructor');
		stm_lms_register_script('become_instructor');
	}
	?>
    <a href="#"
       class="stm_lms_bi_link normal_font"
       data-target=".<?php echo esc_attr($target); ?>"
       data-lms-modal="<?php echo esc_attr($modal); ?>">
        <i class="lnr lnr-bullhorn secondary_color"></i>
        <span><?php esc_html_e('Become an Instructor', 'masterstudy'); ?></span>
    </a>
<?php else: ?>
	<?php if (class_exists('STM_LMS_User')): ?>
        <a href="<?php echo esc_url(STM_LMS_User::login_page_url()); ?>"
           class="stm_lms_bi_link normal_font">
            <i class="lnr lnr-bullhorn secondary_color"></i>
            <span><?php esc_html_e('Become an Instructor', 'masterstudy'); ?></span>
        </a>
	<?php endif; ?>
<?php endif; ?>

<a href="#" class="stm_lms_bi_link normal_font" data-target=".stm-lms-modal-enterprise" data-lms-modal="enterprise">
    <i class="stmlms-case secondary_color"></i>
    <span><?php esc_html_e('For Enterprise', 'masterstudy'); ?></span>
</a>