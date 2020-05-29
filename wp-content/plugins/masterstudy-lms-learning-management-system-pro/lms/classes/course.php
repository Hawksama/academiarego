<?php

new STM_LMS_Course_Pro;

class STM_LMS_Course_Pro
{

	function __construct()
	{
		add_action('stm-lms-content-stm-courses', 'STM_LMS_Course_Pro::single_course', 5);

		add_filter('stm_lms_is_udemy_course', 'STM_LMS_Course_Pro::is_udemy_layout');
	}

	public static function is_external_course($course_id) {
		$is_affiliate = get_post_meta($course_id, 'affiliate_course', true);
		$affiliate_text = get_post_meta($course_id, 'affiliate_course_text', true);
		$affiliate_link = get_post_meta($course_id, 'affiliate_course_link', true);

		if(!empty($is_affiliate) and $is_affiliate == 'on' and !empty($affiliate_text) and !empty($affiliate_link)) {
			STM_LMS_Templates::show_lms_template(
				'global/affiliate-button',
				array(
					'text' => $affiliate_text,
					'link' => $affiliate_link,
					'course_id' => $course_id,
				)
			);
			return true;
		}

		return false;
	}

	public static function single_course()
	{
		$style = STM_LMS_Options::get_option('course_style', 'default');

		if($style == 'udemy' and class_exists('STM_LMS_Udemy')) {
			remove_all_actions('stm-lms-content-stm-courses');
			STM_LMS_Templates::show_lms_template('course/udemy/single');
		} elseif($style != 'default') {
			remove_all_actions('stm-lms-content-stm-courses');
			STM_LMS_Templates::show_lms_template("course/{$style}/single");
		}
	}

	public static function is_udemy_layout($meta) {
		$style = STM_LMS_Options::get_option('course_style', 'default');
		if($style == 'udemy' and class_exists('STM_LMS_Udemy')) {
			return 'stm_lms_udemy_course';
		}

		return $meta;
	}

}