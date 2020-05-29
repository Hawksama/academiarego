<?php
$atts = vc_map_get_attributes($this->getShortcode(), $atts);
extract($atts);
$css_class = apply_filters( VC_SHORTCODE_CUSTOM_CSS_FILTER_TAG, vc_shortcode_custom_css_class( $css, ' ' ) );

$uniq_class = stm_lms_generate_uniq_id($atts);

$css_class .= ' ' . $uniq_class;

$inline_styles = '';

if(!empty($button_color)) {
	$inline_styles = '.' . $uniq_class . ' .button {
	    background-color: ' . $button_color . '!important
	}';
}

stm_module_styles('mailchimp', 'style_1', array(), $inline_styles);


// Mailchimp widget settings
$widget = 'Stm_Mailchimp_Widget';
$instance = array(
	'title' => $title,
);

$args = array();
if(!empty($title_color)) $args['title_color'] = $title_color;
?>

	<div class="stm_subscribe <?php echo esc_attr($css_class); ?>">
		<?php the_widget( $widget, $instance, $args ); ?>
	</div> <!-- stm_subscribe -->