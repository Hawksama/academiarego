<?php
$atts = vc_map_get_attributes($this->getShortcode(), $atts);
extract($atts);

$uniq = stm_create_unique_id($atts);
$inline = '';
if (!empty($title_color)) {
	$inline = ".{$uniq} .stm_lms_courses_carousel__top h3,
    .{$uniq} .stm_lms_courses_carousel__top h4, 
    .{$uniq} .stm_lms_courses_carousel__top .h4 {color: {$title_color};}
    .{$uniq} .stm_lms_courses_carousel__buttons .stm_lms_courses_carousel__button i:before {border-bottom-color: {$title_color};border-left-color: {$title_color}}";
}
if(empty($per_row)) $per_row = '6';

if(!empty($remove_border) && $remove_border === 'enable') {
    $inline .= ".{$uniq} .stm_lms_courses__single__inner {
        border: 0;
    }";
}

if (class_exists('STM_LMS_Helpers')):

	wp_enqueue_script('imagesloaded');
	wp_enqueue_script('owl.carousel');
	wp_enqueue_style('owl.carousel');
	stm_module_styles('courses_carousel', 'style_1', array(), $inline);
	stm_module_scripts('courses_carousel', 'style_1');

	$args = array(
		'per_row'      => intval($per_row),
		'posts_per_page' => 12,
		'include_link' => true
	);

	if (!empty($query)) {
		$args = array_merge($args, STM_LMS_Helpers::sort_query($query));
	}

    if(!empty($image_size)) $args['image_size'] = $image_size;

	if(!empty($taxonomy_default)) {
	    $taxonomy_default = explode(',', $taxonomy_default);
	    $args['tax_query'] = array(
            array(
                'taxonomy' => 'stm_lms_course_taxonomy',
				'field'    => 'term_id',
				'terms'    => $taxonomy_default,
            )
        );
    }

	if (!empty($show_categories) and $show_categories == 'enable' and !empty($taxonomy)) {
		$terms = get_terms(array(
				'taxonomy'   => 'stm_lms_course_taxonomy',
				'hide_empty' => false,
				'include'    => $taxonomy
			)
		);
	}

	?>

    <div class="stm_lms_courses_carousel_wrapper <?php echo esc_attr($uniq); ?> <?php if ($prev_next === 'disable') {
		echo esc_attr('no-nav');
	} ?>">
        <div class="stm_lms_courses_carousel"
             data-items="<?php echo esc_attr($per_row); ?>"
             data-offset="1"
             data-template="courses/grid"
             data-args='<?php echo json_encode($args); ?>'
             data-pagination="<?php echo esc_attr($pagination); ?>">

			<?php if (!empty($title) or !empty($terms)): ?>
                <div class="stm_lms_courses_carousel__top">

					<?php if (!empty($title)): ?>
                        <h3><?php echo wp_kses_post($title); ?></h3>
					<?php endif; ?>

					<?php if (!empty($terms)): ?>
                        <div class="stm_lms_courses_carousel__terms">
                            <div class="stm_lms_courses_carousel__term active secondary_color"><?php esc_html_e('All categories', 'masterstudy'); ?></div>
							<?php foreach ($terms as $term): ?>
                                <div data-term="<?php echo esc_attr($term->term_id); ?>"
                                     class="stm_lms_courses_carousel__term secondary_color"><?php echo esc_attr($term->name); ?></div>
							<?php endforeach; ?>
                        </div>
					<?php endif; ?>

					<?php if (!empty($title)): ?>
                        <a href="<?php echo esc_url(STM_LMS_Course::courses_page_url()); ?>" class="h4">
							<?php esc_html_e('View all', 'masterstudy'); ?> <i class="lnr lnr-arrow-right"></i>
                        </a>
					<?php endif; ?>

                </div>
			<?php endif; ?>

			<?php STM_LMS_Templates::show_lms_template('courses/grid', array('args' => $args)); ?>

			<?php if ($prev_next !== 'disable'): ?>
                <div class="stm_lms_courses_carousel__buttons">
                    <div class="stm_lms_courses_carousel__button stm_lms_courses_carousel__button_prev sbc_h sbrc_h">
                        <i class="fa fa-chevron-left"></i>
                    </div>
                    <div class="stm_lms_courses_carousel__button stm_lms_courses_carousel__button_next sbc_h sbrc_h">
                        <i class="fa fa-chevron-right"></i>
                    </div>
                </div>
			<?php endif; ?>
        </div>
    </div>
<?php endif;