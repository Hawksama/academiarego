<?php
extract( shortcode_atts( array(
	'color' => '#fdc735',
	'css'   => ''
), $atts ) );
$css_class = apply_filters( VC_SHORTCODE_CUSTOM_CSS_FILTER_TAG, vc_shortcode_custom_css_class( $css, ' ' ) );

$unique = stm_create_unique_id($atts);
$inline_styles = "
    .{$unique} .triangled_colored_separator {
        background-color: {$color} !important;
    }
    .{$unique} .triangled_colored_separator .triangle {
        border-bottom-color: {$color} !important;
    }
";

stm_module_styles('color_separator', 'style_1', array(), $inline_styles);

?>

	<div class="stm_colored_separator<?php echo esc_attr( $css_class . ' ' . $unique ); ?>">
		<div class="triangled_colored_separator">
			<div class="triangle"></div>
		</div>
	</div>