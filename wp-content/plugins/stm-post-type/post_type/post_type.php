<?php

require_once $plugin_path . '/post_type/post_type.class.php';

$options = get_option('stm_post_types_options');

$defaultPostTypesOptions = array(
	'testimonial' => array(
		'title' => __( 'Testimonial', 'stm-post-type' ),
		'plural_title' => __( 'Testimonials', 'stm-post-type' ),
		'rewrite' => 'testimonial'
	),
	'sidebar' => array(
		'title' => __( 'Sidebar', 'stm-post-type' ),
		'plural_title' => __( 'Sidebars', 'stm-post-type' ),
		'rewrite' => 'sidebar'
	),
	'teachers' => array(
		'title' => __( 'Teachers', 'stm-post-type' ),
		'plural_title' => __( 'Teachers', 'stm-post-type' ),
		'rewrite' => 'teachers'
	),
	'gallery' => array(
		'title' => __( 'Gallery', 'stm-post-type' ),
		'plural_title' => __( 'Galleries', 'stm-post-type' ),
		'rewrite' => 'gallery'
	),
	'events' => array(
		'title' => __( 'Events', 'stm-post-type' ),
		'plural_title' => __( 'Events', 'stm-post-type' ),
		'rewrite' => 'events'
	),
);

$stm_post_types_options = wp_parse_args( $options, $defaultPostTypesOptions );

STM_PostType::registerPostType( 'testimonial', __( 'Testimonial', 'stm-post-type' ),
	array(
		'menu_icon' => 'dashicons-testimonial',
		'supports' => array( 'title', 'excerpt', 'thumbnail' ),
		'exclude_from_search' => true,
		'publicly_queryable' => false
	)
);

STM_PostType::registerPostType( 'sidebar', __('Sidebar', STM_POST_TYPE),
	array(
		'menu_icon' => 'dashicons-schedule',
		'supports' => array( 'title', 'editor' ),
		'exclude_from_search' => true,
		'publicly_queryable' => false
	)
);

// Teachers post type
STM_PostType::registerPostType( 'teachers', $stm_post_types_options['teachers']['title'],
	array(
		'pluralTitle' => $stm_post_types_options['teachers']['plural_title'],
		'menu_icon' => 'dashicons-awards',
		'supports' => array( 'title', 'editor', 'thumbnail', 'comments', 'excerpt' ) ,
		'rewrite' => array( 'slug' => $stm_post_types_options['teachers']['rewrite'] ),
	)
);

// Gallery
STM_PostType::registerPostType( 'gallery', $stm_post_types_options['gallery']['title'],
	array(
		'menu_icon' => 'dashicons-images-alt',
		'pluralTitle' => $stm_post_types_options['gallery']['plural_title'],
		'supports' => array( 'title', 'thumbnail' ),
		'rewrite' => array( 'slug' => $stm_post_types_options['gallery']['rewrite'] ),
	)
);

STM_PostType::addTaxonomy( 'gallery_category', __( 'Categories', 'stm-post-type' ), 'gallery' );

// Events
STM_PostType::registerPostType( 'events', $stm_post_types_options['events']['plural_title'],
	array(
		'menu_icon' => 'dashicons-calendar-alt',
		'pluralTitle' => $stm_post_types_options['events']['plural_title'],
		'taxonomies' => array('post_tag'),
		'supports' => array( 'title', 'thumbnail', 'editor', 'excerpt', 'revisions' ),
		'rewrite' => array( 'slug' => $stm_post_types_options['events']['rewrite'] ),
	)
);
STM_PostType::registerPostType( 'event_participant', __( 'Participant', 'stm-post-type' ), array( 'supports' => array( 'title', 'excerpt' ), 'exclude_from_search' => true, 'publicly_queryable' => false, 'show_in_menu' => 'edit.php?post_type=events' ) );


// Get experts and list them in dropdown woo products
add_action( 'admin_init', 'expert_list' );

function expert_list() {
	if ( in_array( 'woocommerce/woocommerce.php', apply_filters( 'active_plugins', get_option( 'active_plugins' ) ) ) ) {
		$experts = array(
			'no_expert' => 'Choose teacher for course',
		);

		$experts_args = array(
			'post_type'		=> 'teachers',
			'post_status' => 'publish',
			'posts_per_page'=> -1,
		);

		$experts_query = new WP_Query($experts_args);

		foreach($experts_query->posts as $expert){
			$experts[$expert->ID] = $expert->post_title;
		}

		if(!empty($experts)) {

			STM_PostType::addMetaBox( 'stm_woo_product_expert', __( 'Course Teacher', 'stm-post-type' ), array( 'product' ), '', '', '',
				array(
					'fields' => array(
						'course_expert'   => array(
							'label' => __( 'Teacher', 'stm-post-type' ),
							'type'  => 'multi-select',
							'options' => $experts,
							'description' => 'Choose Teacher for course'
						),
					)
				)
			);
		}

		STM_PostType::addMetaBox( 'stm_woo_product_status', __( 'Course Details', 'stm-post-type' ), array( 'product' ), '', '', '',
			array(
				'fields' => array(
					'course_status'   => array(
						'label' => __( 'Status', 'stm-post-type' ),
						'type'  => 'select',
						'options' => array(
							'no_status' => __( 'No Status', 'stm-post-type' ),
							'hot'		=> __( 'Hot', 'stm-post-type' ),
							'special'	=> __( 'Special', 'stm-post-type' ),
							'new' 		=> __( 'New', 'stm-post-type' ),
						),
					),
					'duration' => array(
						'label'   => __( 'Duration', 'stm-post-type' ),
						'type'    => 'text'
					),
					'lectures' => array(
						'label'   => __( 'Lectures', 'stm-post-type' ),
						'type'    => 'text'
					),
					'video' => array(
						'label'   => __( 'Video', 'stm-post-type' ),
						'type'    => 'text'
					),
					'certificate' => array(
						'label'   => __( 'Certificate', 'stm-post-type' ),
						'type'    => 'text'
					),
				)
			)
		);

		STM_PostType::addMetaBox( 'stm_woo_product_button_link', __( 'Course Link', 'stm-post-type' ), array( 'product' ), '', '', '',
			array(
				'fields' => array(
					'woo_course_url' => array(
						'label'   => __( 'URL', 'stm-post-type' ),
						'type'    => 'text'
					),
					'woo_course_label' => array(
						'label' => __('Button text', STM_POST_TYPE),
						'type'  => 'text'
					)
				)
			)
		);


	}
}


STM_PostType::addMetaBox( 'page_options', __( 'Page Options', 'stm-post-type' ), array( 'page', 'post', 'service', 'project', 'product', 'teachers', 'gallery', 'events' ), '', '', '', array(
	'fields' => array(
		'transparent_header' => array(
			'label'   => __( 'Transparent Header', 'stm-post-type' ),
			'type'    => 'checkbox'
		),
		/*
'separator_page_background' => array(
			'label'   => __( 'Page Background', 'stm-post-type' ),
			'type'    => 'separator'
		),
		'page_bg_image' => array(
			'label' => __( 'Background Image', 'stm-post-type' ),
			'type'  => 'image'
		),
		'page_bg_position' => array(
			'label'   => __( 'Background Position', 'stm-post-type' ),
			'type'    => 'text'
		),
		'page_bg_repeat' => array(
			'label'   => __( 'Background Repeat', 'stm-post-type' ),
			'type'    => 'select',
			'options' => array(
				'repeat' => __( 'Repeat', 'stm-post-type' ),
				'no-repeat' => __( 'No Repeat', 'stm-post-type' ),
				'repeat-x' => __( 'Repeat-X', 'stm-post-type' ),
				'repeat-y' => __( 'Repeat-Y', 'stm-post-type' )
			)
		),
*/
		'separator_title_box' => array(
			'label'   => __( 'Title Box', 'stm-post-type' ),
			'type'    => 'separator'
		),
		'title' => array(
			'label'   => __( 'Title', 'stm-post-type' ),
			'type'    => 'select',
			'options' => array(
				'show' => __( 'Show', 'stm-post-type' ),
				'hide' => __( 'Hide', 'stm-post-type' )
			)
		),
		'sub_title' => array(
			'label'   => __( 'Sub Title', 'stm-post-type' ),
			'type'    => 'text'
		),
		'title_box_bg_color' => array(
			'label' => __( 'Background Color', 'stm-post-type' ),
			'type'  => 'color_picker'
		),
		'title_box_font_color' => array(
			'label' => __( 'Font Color', 'stm-post-type' ),
			'type'  => 'color_picker'
		),
		'title_box_line_color' => array(
			'label' => __( 'Line Color', 'stm-post-type' ),
			'type'  => 'color_picker'
		),
		'title_box_subtitle_font_color' => array(
			'label' => __( 'Sub Title Font Color', 'stm-post-type' ),
			'type'  => 'color_picker'
		),
		'title_box_custom_bg_image' => array(
			'label' => __( 'Custom Background Image', 'stm-post-type' ),
			'type'  => 'image'
		),
		'title_box_bg_position' => array(
			'label'   => __( 'Background Position', 'stm-post-type' ),
			'type'    => 'text'
		),
		'title_box_bg_repeat' => array(
			'label'   => __( 'Background Repeat', 'stm-post-type' ),
			'type'    => 'select',
			'options' => array(
				'repeat' => __( 'Repeat', 'stm-post-type' ),
				'no-repeat' => __( 'No Repeat', 'stm-post-type' ),
				'repeat-x' => __( 'Repeat-X', 'stm-post-type' ),
				'repeat-y' => __( 'Repeat-Y', 'stm-post-type' )
			)
		),
		'title_box_overlay' => array(
			'label'   => __( 'Overlay', 'stm-post-type' ),
			'type'    => 'checkbox'
		),
		'title_box_small' => array(
			'label'   => __( 'Small', 'stm-post-type' ),
			'type'    => 'checkbox'
		),
		'separator_breadcrumbs' => array(
			'label'   => __( 'Breadcrumbs', 'stm-post-type' ),
			'type'    => 'separator'
		),
		'breadcrumbs' => array(
			'label'   => __( 'Breadcrumbs', 'stm-post-type' ),
			'type'    => 'select',
			'options' => array(
				'show' => __( 'Show', 'stm-post-type' ),
				'hide' => __( 'Hide', 'stm-post-type' )
			)
		),
		'breadcrumbs_font_color' => array(
			'label' => __( 'Breadcrumbs Color', 'stm-post-type' ),
			'type'  => 'color_picker'
		),
		/*
'separator_title_box_button' => array(
			'label'   => __( 'Title Box Button', 'stm-post-type' ),
			'type'    => 'separator'
		),
		'title_box_button_text' => array(
			'label'   => __( 'Button Text', 'stm-post-type' ),
			'type'    => 'text'
		),
		'title_box_button_url' => array(
			'label'   => __( 'Button URL', 'stm-post-type' ),
			'type'    => 'text'
		),
		'title_box_button_border_color' => array(
			'label' => __( 'Border Color', 'stm-post-type' ),
			'type'  => 'color_picker',
			'default' => '#ffffff'
		),
		'title_box_button_font_color' => array(
			'label' => __( 'Font Color', 'stm-post-type' ),
			'type'  => 'color_picker',
			'default' => '#333333'
		),
		'title_box_button_font_color_hover' => array(
			'label' => __( 'Font Color (hover)', 'stm-post-type' ),
			'type'  => 'color_picker',
			'default' => '#333333'
		),
		'title_box_button_font_arrow_color' => array(
			'label' => __( 'Arrow Color', 'stm-post-type' ),
			'type'  => 'color_picker',
			'default' => '#ffffff'
		),
*/
		/*
'prev_next_buttons_title_box' => array(
			'label'   => __( 'Prev/Next Buttons', 'stm-post-type' ),
			'type'    => 'separator'
		),
		'prev_next_buttons' => array(
			'label'   => __( 'Enable', 'stm-post-type' ),
			'type'    => 'checkbox'
		),
		'prev_next_buttons_border_color' => array(
			'label' => __( 'Border Color', 'stm-post-type' ),
			'type'  => 'color_picker',
			'default' => '#ffffff'
		),
		'prev_next_buttons_arrow_color_hover' => array(
			'label' => __( 'Arrow Color (hover)', 'stm-post-type' ),
			'type'  => 'color_picker',
			'default' => '#dac725'
		),
*/
	)
) );

STM_PostType::addMetaBox( 'testimonial_info', __( 'Testimonial Info', 'stm-post-type' ), array( 'testimonial' ), '', '', '', array(
	'fields' => array(
		'testimonial_user'   => array(
			'label' => __( 'Name', 'stm-post-type' ),
			'type'  => 'text'
		),
		'testimonial_profession'   => array(
			'label' => __( 'Profession', 'stm-post-type' ),
			'type'  => 'text'
		),
	)
) );

STM_PostType::addMetaBox( 'expert_info', __( 'Expert Info', 'stm-post-type' ), array( 'teachers' ), '', '', '', array(
	'fields' => array(
		'expert_sphere'   => array(
			'label' => __( 'Teacher Sphere', 'stm-post-type' ),
			'type'  => 'text'
		),
		'expert_certified'   => array(
			'label' => __( 'Teacher Certified By', 'stm-post-type' ),
			'type'  => 'text'
		),
	)
) );

STM_PostType::addMetaBox( 'expert_socials', __( 'Expert Socials', 'stm-post-type' ), array( 'teachers' ), '', '', '', array(
	'fields' => array(
		'facebook'   => array(
			'label' => __( 'Facebook', 'stm-post-type' ),
			'type'  => 'text'
		),
		'twitter'   => array(
			'label' => __( 'Twitter', 'stm-post-type' ),
			'type'  => 'text'
		),
		'google-plus'   => array(
			'label' => __( 'Google +', 'stm-post-type' ),
			'type'  => 'text'
		),
		'linkedin'   => array(
			'label' => __( 'LinkedIn', 'stm-post-type' ),
			'type'  => 'text'
		),
		'youtube-play'   => array(
			'label' => __( 'Youtube', 'stm-post-type' ),
			'type'  => 'text'
		),
	)
) );

STM_PostType::addMetaBox( 'event_info', __( 'Event Info', 'stm-post-type' ), array( 'events' ), '', '', '', array(
	'fields' => array(
		'event_start'   => array(
			'label' => __( 'Event Start', 'stm-post-type' ),
			'type'  => 'date_picker'
		),
		'event_end'   => array(
			'label' => __( 'Event End', 'stm-post-type' ),
			'type'  => 'date_picker'
		),
		'event_location'   => array(
			'label' => __( 'Event Location', 'stm-post-type' ),
			'type'  => 'text'
		),
		'event_price'   => array(
			'label' => __( 'Event Price', 'stm-post-type' ),
			'type'  => 'text'
		)
	)
) );

STM_PostType::addMetaBox( 'participant_info', __( 'Participant Info', 'stm-post-type' ), array( 'event_participant' ), '', '', '', array(
	'fields' => array(
		'participant_email'   => array(
			'label' => __( 'Email', 'stm-post-type' ),
			'type'  => 'text'
		),
		'participant_phone'   => array(
			'label' => __( 'Phone', 'stm-post-type' ),
			'type'  => 'text'
		),
		'participant_event'   => array(
			'label' => __( 'Event ID', 'stm-post-type' ),
			'type'  => 'text'
		)
	)
) );

function stm_plugin_styles() {
	$plugin_url =  plugins_url('', __FILE__);

	wp_enqueue_style( 'admin-styles', $plugin_url . '/assets/css/admin.css', null, null, 'all' );

	wp_enqueue_style( 'wp-color-picker' );
	wp_enqueue_script( 'wp-color-picker');

	wp_enqueue_style( 'stmcss-datetimepicker', $plugin_url . '/assets/css/jquery.stmdatetimepicker.css', null, null, 'all' );
	wp_enqueue_script( 'stmjs-datetimepicker', $plugin_url . '/assets/js/jquery.stmdatetimepicker.js', array( 'jquery' ), null, true );

	wp_enqueue_media();
}

add_action( 'admin_enqueue_scripts', 'stm_plugin_styles' );

// STM Post Type Rewrite subplugin
add_action( 'admin_menu', 'stm_register_post_types_options_menu' );

if( ! function_exists( 'stm_register_post_types_options_menu' ) ){
	function stm_register_post_types_options_menu(){
		add_submenu_page( 'tools.php', __('STM Post Types', STM_POST_TYPE), __('STM Post Types', STM_POST_TYPE), 'manage_options', 'stm_post_types', 'stm_post_types_options' );
	}
}

if( ! function_exists( 'stm_post_types_options' ) ){
	function stm_post_types_options(){

		if ( ! empty( $_POST['stm_post_types_options'] ) ) {
			update_option( 'stm_post_types_options', $_POST['stm_post_types_options'] );
		}

		$options = get_option('stm_post_types_options');

		$defaultPostTypesOptions = array(
			/*
'testimonial' => array(
				'title' => __( 'Testimonial', 'stm-post-type' ),
				'plural_title' => __( 'Testimonials', 'stm-post-type' ),
				'rewrite' => 'testimonial'
			),
			'sidebar' => array(
				'title' => __( 'Sidebar', 'stm-post-type' ),
				'plural_title' => __( 'Sidebars', 'stm-post-type' ),
				'rewrite' => 'sidebar'
			),
*/
			'teachers' => array(
				'title' => __( 'Teachers', 'stm-post-type' ),
				'plural_title' => __( 'Teachers', 'stm-post-type' ),
				'rewrite' => 'teachers'
			),
			'gallery' => array(
				'title' => __( 'Gallery', 'stm-post-type' ),
				'plural_title' => __( 'Galleries', 'stm-post-type' ),
				'rewrite' => 'gallery'
			),
			'events' => array(
				'title' => __( 'Events', 'stm-post-type' ),
				'plural_title' => __( 'Events', 'stm-post-type' ),
				'rewrite' => 'events'
			),
		);

		$options = wp_parse_args( $options, $defaultPostTypesOptions );

		$content = '';

		$content .= '
			<div class="wrap">
		        <h2>' . __( 'Custom Post Type Renaming Settings', 'stm-post-type' ) . '</h2>

		        <form method="POST" action="">
		            <table class="form-table">';
		foreach ($defaultPostTypesOptions as $key => $value){
			$content .= '
								<tr valign="top">
									<th scope="row">
										<label for="'.$key.'_title">' . __( '"'.$value['title'].'" title (admin panel tab name)', 'stm-post-type' ) . '</label>
									</th>
									<td>
				                        <input type="text" id="'.$key.'_title" name="stm_post_types_options['.$key.'][title]" value="' . $options[$key]['title'] . '"  size="25" />
				                    </td>
								</tr>
								<tr valign="top">
				                    <th scope="row">
				                        <label for="'.$key.'_plural_title">' . __( '"'.$value['plural_title'].'" plural title', 'stm-post-type' ) . '</label>
				                    </th>
				                    <td>
				                        <input type="text" id="'.$key.'_plural_title" name="stm_post_types_options['.$key.'][plural_title]" value="' . $options[$key]['plural_title'] . '"  size="25" />
				                    </td>
				                </tr>
				                <tr valign="top">
				                    <th scope="row">
				                        <label for="'.$key.'_rewrite">' . __( '"'.$value['plural_title'].'" rewrite (URL text)', 'stm-post-type' ) . '</label>
				                    </th>
				                    <td>
				                        <input type="text" id="'.$key.'_rewrite" name="stm_post_types_options['.$key.'][rewrite]" value="' . $options[$key]['rewrite'] . '"  size="25" />
				                    </td>
				                </tr>
				                <tr valign="top"><th scope="row"></th></tr>
			                ';
		}
		$content .='</table>
		            <p>' . __( "NOTE: After you change the rewrite field values, you'll need to refresh permalinks under Settings -> Permalinks", 'stm-post-type' ) . '</p>
		            <br/>
		            <p>
						<input type="submit" value="' . __( 'Save settings', 'stm-post-type' ) . '" class="button-primary"/>
					</p>
		        </form>
		    </div>
		';

		echo stm_post_type_filtered_output($content);
	}
}