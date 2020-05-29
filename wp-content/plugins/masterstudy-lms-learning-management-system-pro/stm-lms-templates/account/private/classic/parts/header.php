<?php
/**
 * @var $current_user
 */

stm_lms_register_style('user');
stm_lms_register_style('user_classic');
?>

<?php STM_LMS_Templates::show_lms_template('account/private/classic/parts/top_bar', array('current_user' => $current_user)); ?>

<?php STM_LMS_Templates::show_lms_template('account/private/classic/parts/tabs', array('current_user' => $current_user)); ?>