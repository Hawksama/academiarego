<?php

add_action('vc_after_init', 'stm_lms_single_course_carousel_vc');

function stm_lms_single_course_carousel_vc()
{

	$terms = stm_autocomplete_terms('stm_lms_course_taxonomy');

	vc_map(array(
		'name'        => esc_html__('STM LMS Single Course Carousel', 'masterstudy'),
		'base'        => 'stm_lms_single_course_carousel',
		'icon'        => 'stm_lms_single_course_carousel',
		'description' => esc_html__('Display Signle Course in Styled Carousel', 'masterstudy'),
		'category'    => array(
			esc_html__('Content', 'masterstudy'),
		),
		'params'      => array(
			array(
				'type'       => 'dropdown',
				'heading'    => __('Sort', 'masterstudy'),
				'param_name' => 'query',
				'value'      => array(
					'None'    => 'none',
					'Popular' => 'popular',
					'Free'    => 'free',
					'Rating'  => 'rating',
				)
			),
			array(
				'type'       => 'dropdown',
				'heading'    => __('Prev/Next Buttons', 'masterstudy'),
				'param_name' => 'prev_next',
				'value'      => array(
					'Enable'  => 'enable',
					'Disable' => 'disable',
				),
				'std'        => 'enable',
			),
			array(
				'type'       => 'dropdown',
				'heading'    => __('Pagination', 'masterstudy'),
				'param_name' => 'pagination',
				'value'      => array(
					'Enable'  => 'enable',
					'Disable' => 'disable',
				),
				'std'        => 'disable',
			),
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
				'dependency' => array(
					'element' => 'show_categories',
					'value' => array( 'enable' )
				),
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
	class WPBakeryShortCode_Stm_Lms_Single_Course_Carousel extends WPBakeryShortCode
	{
	}
}