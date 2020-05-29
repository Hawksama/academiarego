<?php
/**
 * @var $current_user
 */

?>

<div class="stm_lms_user_side">

	<?php if (!empty($current_user['avatar'])): ?>
        <div class="stm-lms-user_avatar">
			<?php echo wp_kses_post($current_user['avatar']); ?>
        </div>
	<?php endif; ?>

	<?php STM_LMS_Templates::show_lms_template('account/private/parts/socials', array('current_user' => $current_user)); ?>


</div>