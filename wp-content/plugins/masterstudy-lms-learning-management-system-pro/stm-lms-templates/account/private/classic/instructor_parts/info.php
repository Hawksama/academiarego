<?php
/**
 * @var $current_user
 */

$rating = STM_LMS_Instructor::my_rating();

?>

<div class="stm_lms_user_side">

    <div class="stm-lms-user-avatar-edit ">
        <?php $my_avatar = get_user_meta($current_user['id'], 'stm_lms_user_avatar', true); ?>
        <input type="file"/>
        <?php if (!empty($my_avatar)): ?>
            <i class="lnricons-cross delete_avatar"></i>
        <?php endif; ?>
        <i class="lnricons-pencil"></i>
        <?php if (!empty($current_user['avatar'])): ?>
            <div class="stm-lms-user_avatar">
                <?php echo wp_kses_post($current_user['avatar']); ?>
            </div>
        <?php endif; ?>
    </div>


    <div class="stm_lms_profile_buttons_set 1">
        <div class="stm_lms_profile_buttons_set__inner">

            <?php STM_LMS_Templates::show_lms_template('account/private/parts/edit_profile_btn', array('current_user' => $current_user)); ?>


        </div>

    </div>


</div>