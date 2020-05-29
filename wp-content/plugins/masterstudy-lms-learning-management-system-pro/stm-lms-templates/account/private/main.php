<?php if ( ! defined( 'ABSPATH' ) ) exit; //Exit if accessed directly ?>

<?php
/**
 * @var $current_user
 */

$is_instructor = STM_LMS_Instructor::is_instructor();
$tpl = ($is_instructor) ? 'instructor' : 'student';

$style = STM_LMS_Options::get_option('profile_style', 'default');
$style_path = ($style !== 'default') ? $style . '/' : '';

if(!empty($style_path)) stm_lms_register_style('user_' . sanitize_title($style_path), array('stm-lms-user_info_top'));

STM_LMS_Templates::show_lms_template("account/private/{$style_path}{$tpl}", compact('current_user'));