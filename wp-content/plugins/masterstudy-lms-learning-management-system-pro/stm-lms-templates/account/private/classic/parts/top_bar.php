<?php
/**
 * @var $current_user
 */

stm_lms_register_style('user_info_top');

$rating = STM_LMS_Instructor::my_rating($current_user);
?>

<div class="stm_lms_user_info_top">

    <div class="stm_lms_user_info_top__left">
        <h1><?php echo esc_attr($current_user['login']); ?></h1>
		<?php if (!empty($current_user['meta']['position'])): ?>
            <h5><?php echo sanitize_text_field($current_user['meta']['position']); ?></h5>
		<?php endif; ?>
    </div>

    <div class="stm_lms_user_info_top__right">

		<?php if (!empty($rating['total'])): ?>
            <div class="stm-lms-user_rating">
                <div class="star-rating star-rating__big">
                    <span style="width: <?php echo floatval($rating['percent']); ?>%;"></span>
                </div>
                <strong class="rating heading_font"><?php echo floatval($rating['average']); ?></strong>
                <div class="stm-lms-user_rating__total">
					<?php echo sanitize_text_field($rating['total_marks']); ?>
                </div>
            </div>
		<?php endif; ?>

        <?php do_action('stm_lms_before_profile_buttons_all', $current_user); ?>

    </div>

</div>