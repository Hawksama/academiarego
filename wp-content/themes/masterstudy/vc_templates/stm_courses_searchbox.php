<?php
extract( shortcode_atts( array(
	'css'   => '',
    'style' => 'style_1'
), $atts ) );

if(empty($style)) $style = 'style_1';

$css_class = apply_filters( VC_SHORTCODE_CUSTOM_CSS_FILTER_TAG, vc_shortcode_custom_css_class( $css, ' ' ) );

stm_module_styles('searchbox', $style); ?>

<div class="stm_searchbox <?php echo esc_attr($css_class . ' ' . $style); ?>">
    <form action="<?php echo esc_url(STM_LMS_Course::courses_page_url()); ?>">
        <input name="search" class="form-control" placeholder="<?php esc_attr_e('Search Courses...', 'masterstudy'); ?>" />

        <button type="submit">
            <?php if($style === 'style_1'): ?>
            <i class="lnricons-magnifier"></i>
            <?php else: ?>
            <span class="heading_font"><i class="lnricons-magnifier"></i><?php esc_html_e('Find Course', 'masterstudy'); ?></span>
            <?php endif; ?>
        </button>

    </form>
</div>
