<?php
$atts = vc_map_get_attributes($this->getShortcode(), $atts);
extract($atts);

$uniq = stm_create_unique_id($atts);

$args = array(
	'post_type'      => 'stm-courses',
	'posts_per_page' => 5,
);

if (!empty($query)) {
	$args = array_merge($args, STM_LMS_Helpers::sort_query($query));
}

if(!empty($taxonomy)) {
	$args['tax_query'] = array(
		array(
			'taxonomy' => 'stm_lms_course_taxonomy',
			'terms'    => $taxonomy,
		),
	);
}

$q = new WP_Query($args);

stm_lms_register_style('course');
wp_enqueue_script('imagesloaded');
wp_enqueue_script('owl.carousel');
wp_enqueue_style('owl.carousel');
stm_module_styles('single_course_carousel');
stm_module_scripts('single_course_carousel', 'style_1');

if ($q->have_posts()): ?>
    <div class="stm_lms_single_course_carousel_wrapper <?php echo esc_attr($uniq); ?> <?php if ($prev_next === 'disable') {echo esc_attr('no-nav'); } ?>"
         data-items="1"
         data-pagination="<?php echo esc_attr($pagination); ?>">
        <div class="stm_lms_single_course_carousel">
			<?php while ($q->have_posts()): $q->the_post();
			    $post_id = get_the_ID();
				$level = get_post_meta($post_id, 'level', true);
				$duration = get_post_meta($post_id, 'duration_info', true);
				$lectures = STM_LMS_Course::curriculum_info(get_post_meta($post_id, 'curriculum', true)); ?>

                <div class="stm_lms_single_course_carousel_item stm_carousel_glitch">

                    <a href="<?php the_permalink(); ?>" class="stm_lms_single_course_carousel_item__image">
                        <?php echo masterstudy_lazyload_image(stm_get_VC_attachment_img_safe(get_post_thumbnail_id(), '504x335')); ?>
                    </a>

                    <div class="stm_lms_single_course_carousel_item__content">
                        <?php STM_LMS_Templates::show_lms_template('course/parts/panel_info', array('number' => 1)); ?>

                        <h2><a class="h2" href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h2>

                        <div class="stm_lms_courses__single--info_meta">
							<?php STM_LMS_Templates::show_lms_template('courses/parts/meta', compact('level', 'duration', 'lectures')); ?>
                        </div>

                        <div class="stm_lms_courses__single__buttons">
                            <?php STM_LMS_Templates::show_lms_template('global/buy-button', array('course_id' => $post_id)); ?>
                            <?php STM_LMS_Templates::show_lms_template('global/wish-list', array('course_id' => $post_id)); ?>
                        </div>

                    </div>

                </div>

			<?php endwhile; ?>
        </div>

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
<?php endif;

wp_reset_postdata();
