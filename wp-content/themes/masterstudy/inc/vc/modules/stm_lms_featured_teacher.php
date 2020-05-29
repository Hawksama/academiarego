<?php

add_action('vc_after_init', 'stm_courses_featured_teacher_vc');

function stm_courses_featured_teacher_vc()
{

	$users = array();
	if(is_admin()) {
		$blog_users = get_users( "blog_id={$GLOBALS['blog_id']}" );
		foreach ($blog_users as $user) {
			$name = (!empty($user->data->display_name)) ? $user->data->display_name : $user->data->user_login;
			$users[$name] = $user->ID;
		}
	}

	vc_map(array(
		'name'   => esc_html__('STM LMS Featured Teacher', 'masterstudy'),
		'base'   => 'stm_lms_featured_teacher',
		'icon'   => 'stm_lms_featured_teacher',
		'description' => esc_html__('Place Single Teacher', 'masterstudy'),
		'category' =>array(
			esc_html__('Content', 'masterstudy'),
		),
		'params' => array(
			array(
				'type'       => 'dropdown',
				'heading'    => __('Instructor', 'masterstudy'),
				'param_name' => 'instructor',
				'value'      => $users
			),
			array(
				'type'       => 'textfield',
				'heading'    => __( 'Instructor Position', 'masterstudy' ),
				'param_name' => 'position',
			),
			array(
				'type'       => 'textarea',
				'heading'    => __( 'Instructor Bio', 'masterstudy' ),
				'param_name' => 'bio',
			),
			array(
				'type' => 'attach_image',
				'heading' => __( 'Image', 'masterstudy' ),
				'param_name' => 'image'
			),
			array(
				'type'       => 'css_editor',
				'heading'    => esc_html__('Css', 'masterstudy'),
				'param_name' => 'css',
				'group'      => esc_html__('Design options', 'masterstudy')
			)
		)
	));
}

if (class_exists('WPBakeryShortCode')) {
	class WPBakeryShortCode_Stm_Lms_Featured_Teacher extends WPBakeryShortCode
	{
	}
}