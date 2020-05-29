<?php
$atts = vc_map_get_attributes($this->getShortcode(), $atts);
extract($atts);

$css_class = apply_filters(VC_SHORTCODE_CUSTOM_CSS_FILTER_TAG, vc_shortcode_custom_css_class($css, ' '));

stm_module_styles('featured_teacher', 'style_1');

if (empty($instructor)) {
    $super_admins = get_super_admins();
    if (!empty($super_admins[0])) {
        $super_admin = get_user_by('login', $super_admins[0]);
        $instructor = $super_admin->ID;
    }
}

$image = (!empty($image)) ? stm_get_VC_attachment_img_safe($image, 'full', 'full', true) : '';
if (!empty($instructor)):
    $instructor_data = STM_LMS_User::get_current_user($instructor);
    $args = array(
        'per_row' => 4,
        'posts_per_page' => 4,
        'author' => $instructor
    );
    ?>

    <div class="stm_lms_featured_teacher <?php echo esc_attr($css_class); ?>"
         style="background-image: url('<?php echo esc_url($image); ?>')">

        <div class="stm_lms_featured_teacher_content">

            <div class="stm_lms_featured_teacher_content__text">

                <a href="<?php echo esc_url(STM_LMS_User::user_public_page_url($instructor)); ?>"
                   class="btn btn-default">
                    <?php esc_html_e('Teacher of month', 'masterstudy'); ?>
                </a>

                <h2><?php echo sanitize_text_field($instructor_data['login']); ?></h2>

                <?php if (!empty($position)): ?>
                    <div class="stm_lms_featured_teacher_content__position">
                        <h4><?php echo esc_attr($position); ?></h4>
                    </div>
                <?php endif; ?>

                <?php if (!empty($bio)): ?>
                    <div class="stm_lms_featured_teacher_content__bio">
                        <?php echo esc_attr($bio); ?>
                    </div>
                <?php endif; ?>

            </div>

        </div>

        <div class="stm_lms_featured_teacher_courses">
            <h4><?php esc_html_e('Teacher Courses:', 'masterstudy'); ?></h4>
            <?php STM_LMS_Templates::show_lms_template('courses/grid', array('args' => $args)); ?>
        </div>

    </div>
<?php endif; ?>