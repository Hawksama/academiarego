<?php

add_action('vc_after_init', 'stm_courses_searchbox_vc');

function stm_courses_searchbox_vc()
{
	vc_map(array(
		'name'   => esc_html__('STM Courses Search box', 'masterstudy'),
		'base'   => 'stm_courses_searchbox',
		'icon'   => 'stm_courses_searchbox',
		'description' => esc_html__('Search in LMS Courses', 'masterstudy'),
		'category' =>array(
			esc_html__('Content', 'masterstudy'),
		),
		'params' => array(
            array(
                'type'       => 'dropdown',
                'heading'    => __( 'Style', 'masterstudy' ),
                'param_name' => 'style',
                'value'      => array(
                    'Style 1' => 'style_1',
                    'Style 2' => 'style_2',
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
	class WPBakeryShortCode_Stm_Courses_Searchbox extends WPBakeryShortCode
	{
	}
}