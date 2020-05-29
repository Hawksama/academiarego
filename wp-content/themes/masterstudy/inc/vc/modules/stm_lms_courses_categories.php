<?php

add_action('vc_after_init', 'stm_lms_courses_categories_vc');

function stm_lms_courses_categories_vc()
{

	$terms = stm_autocomplete_terms('stm_lms_course_taxonomy');

	vc_map(array(
		'name'   => esc_html__('STM LMS Courses Categories', 'masterstudy'),
		'base'   => 'stm_lms_courses_categories',
		'icon'   => 'stm_lms_courses_categories',
		'description' => esc_html__('Show Courses Categories', 'masterstudy'),
		'category' =>array(
			esc_html__('Content', 'masterstudy'),
		),
		'params' => array(
			array(
				'type'       => 'autocomplete',
				'heading'    => esc_html__('Select taxonomy', 'masterstudy'),
				'param_name' => 'taxonomy',
				'settings'   => array(
					'multiple'       => true,
					'sortable'       => true,
					'min_length'     => 1,
					'no_hide'        => true,
					'unique_values'  => true,
					'display_inline' => true,
					'values'         => $terms
				),
			),
			array(
				'type'       => 'dropdown',
				'heading'    => __( 'Style', 'masterstudy' ),
				'param_name' => 'style',
				'value'      => array(
					'Style 1' => 'style_1',
					'Style 2' => 'style_2',
					'Style 3' => 'style_3',
					'Style 4' => 'style_4',
				),
				'std' => 'style_1'
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
	class WPBakeryShortCode_Stm_Lms_Courses_Categories extends WPBakeryShortCode
	{
	}
}