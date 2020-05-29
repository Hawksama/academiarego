<?php

add_action('vc_after_init', 'stm_lms_courses_carousel_vc');

function stm_lms_courses_carousel_vc()
{

	$terms = stm_autocomplete_terms('stm_lms_course_taxonomy');

	vc_map(array(
		'name'        => esc_html__('STM LMS Courses Carousel', 'masterstudy'),
		'base'        => 'stm_lms_courses_carousel',
		'icon'        => 'stm_lms_courses_carousel',
		'description' => esc_html__('Display Courses in Styled Carousel', 'masterstudy'),
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
				'type'       => 'colorpicker',
				'heading'    => __('Title color', 'masterstudy'),
				'param_name' => 'title_color',
			),
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
                'heading'    => __('Remove border', 'masterstudy'),
                'param_name' => 'remove_border',
                'value'      => array(
                    'Enable'  => 'enable',
                    'Disable' => 'disable',
                ),
                'std'        => 'disable',
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
				'type'       => 'dropdown',
				'heading'    => __('Show categories', 'masterstudy'),
				'param_name' => 'show_categories',
				'value'      => array(
					'Enable'  => 'enable',
					'Disable' => 'disable',
				),
				'std'        => 'disable',
			),
            array(
                'type'       => 'textfield',
                'heading'    => __('Courses per row', 'masterstudy'),
                'param_name' => 'per_row',
                'std'        => '6',
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
				'type'       => 'autocomplete',
				'heading'    => esc_html__('Show Courses From categories:', 'masterstudy'),
				'param_name' => 'taxonomy_default',
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
					'value' => array( 'disable' )
				),
			),
            array(
                'type'       => 'textfield',
                'heading'    => __( 'Image size (Ex. : 200x100)', 'masterstudy' ),
                'param_name' => 'image_size',
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
	class WPBakeryShortCode_Stm_Lms_Courses_Carousel extends WPBakeryShortCode
	{
	}
}