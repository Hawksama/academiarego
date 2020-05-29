<?php

add_action('vc_after_init', 'stm_lms_courses_grid_vc');

function stm_lms_courses_grid_vc()
{

	$terms = stm_autocomplete_terms('stm_lms_course_taxonomy');

	vc_map(array(
		'name'   => esc_html__('STM LMS Courses Grid', 'masterstudy'),
		'base'   => 'stm_lms_courses_grid',
		'icon'   => 'stm_lms_courses_grid',
		'description' => esc_html__('Show Recent Courses', 'masterstudy'),
		'category' =>array(
			esc_html__('Content', 'masterstudy'),
		),
		'params' => array(
			array(
				'type'       => 'dropdown',
				'heading'    => __( 'Hide Top bar', 'masterstudy' ),
				'param_name' => 'hide_top_bar',
				'value'      => array(
					'Hide' => 'hidden',
					'Show' => 'showing',
				),
				'std' => 'showing'
			),
			array(
				'type'       => 'textfield',
				'heading'    => __( 'Title', 'masterstudy' ),
				'param_name' => 'title',
				'dependency' => array(
					'element' => 'hide_top_bar',
					'value' => array( 'showing' )
				)
			),
			array(
				'type'       => 'dropdown',
				'heading'    => __( 'Load more', 'masterstudy' ),
				'param_name' => 'hide_load_more',
				'value'      => array(
					'Hide' => 'hidden',
					'Show' => 'showing',
				),
				'std' => 'showing'
			),
			array(
				'type'       => 'dropdown',
				'heading'    => __( 'Sort', 'masterstudy' ),
				'param_name' => 'hide_sort',
				'value'      => array(
					'Hide' => 'hidden',
					'Show' => 'showing',
				),
				'std' => 'showing'
			),
			array(
				'type'       => 'dropdown',
				'heading'    => __( 'Courses Per Row', 'masterstudy' ),
				'param_name' => 'per_row',
				'value'      => array(
					'6' => '6',
					'4' => '4',
					'3' => '3',
				),
				'std' => '6'
			),
            array(
                'type'       => 'textfield',
                'heading'    => __( 'Image size (Ex. : 200x100)', 'masterstudy' ),
                'param_name' => 'image_size',
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
				'heading'    => __( 'Number of courses to show', 'masterstudy' ),
				'param_name' => 'posts_per_page',
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
	class WPBakeryShortCode_Stm_Lms_Courses_Grid extends WPBakeryShortCode
	{
	}
}