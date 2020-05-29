<?php

add_action('vc_after_init', 'stm_lms_instructors_carousel_vc');

function stm_lms_instructors_carousel_vc()
{

	vc_map(array(
		'name'        => esc_html__('STM LMS Instructors Carousel', 'masterstudy'),
		'base'        => 'stm_lms_instructors_carousel',
		'icon'        => 'stm_lms_instructors_carousel',
		'description' => esc_html__('Display Instructors in Styled Carousel', 'masterstudy'),
		'category'    => array(
			esc_html__('Content', 'masterstudy'),
		),
		'params'      => array(
			array(
				'type'       => 'textfield',
				'heading'    => __('Title', 'masterstudy'),
				'param_name' => 'title',
			),
            array(
                'type'       => 'textfield',
                'heading'    => __('Per row', 'masterstudy'),
                'std' => 6,
                'param_name' => 'per_row',
            ),
            array(
                'type'       => 'textfield',
                'heading'    => __('Per row on Notebook', 'masterstudy'),
                'std' => 4,
                'param_name' => 'per_row_md',
            ),
            array(
                'type'       => 'textfield',
                'heading'    => __('Per row on Tablet', 'masterstudy'),
                'std' => 2,
                'param_name' => 'per_row_sm',
            ),
            array(
                'type'       => 'textfield',
                'heading'    => __('Per row on Mobile', 'masterstudy'),
                'std' => 1,
                'param_name' => 'per_row_xs',
            ),
			array(
				'type'       => 'colorpicker',
				'heading'    => __('Title color', 'masterstudy'),
				'param_name' => 'title_color',
			),
            array(
                'type'       => 'dropdown',
                'heading'    => __('Style', 'masterstudy'),
                'param_name' => 'style',
                'value'      => array(
                    'Style 1'  => 'style_1',
                    'Style 2' => 'style_2',
                ),
                'std'        => 'style_1',
            ),
			array(
				'type'       => 'dropdown',
				'heading'    => __('Sort By', 'masterstudy'),
				'param_name' => 'sort',
				'value'      => array(
					'Default'  => '',
					'Rating' => 'rating',
				),
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
				'type'       => 'css_editor',
				'heading'    => esc_html__('Css', 'masterstudy'),
				'param_name' => 'css',
				'group'      => esc_html__('Design options', 'masterstudy')
			)
		)
	));
}

if (class_exists('WPBakeryShortCode')) {
	class WPBakeryShortCode_Stm_Lms_Instructors_Carousel extends WPBakeryShortCode
	{
	}
}