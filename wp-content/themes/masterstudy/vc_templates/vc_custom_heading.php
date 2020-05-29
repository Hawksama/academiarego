<?php
/**
 * @var $this WPBakeryShortCode_VC_Custom_heading
 */
$output = $text = $google_fonts = $font_container = $el_class = $css = $google_fonts_data = $font_container_data = '';
extract( $this->getAttributes( $atts ) );
extract( $this->getStyles( $el_class, $css, $google_fonts_data, $font_container_data, $atts ) );
$settings = get_option( 'wpb_js_google_fonts_subsets' );
$subsets = '';
if ( is_array( $settings ) && ! empty( $settings ) ) {
	$subsets = '&subset=' . implode( ',', $settings );
}
if ( ( ! isset( $atts['use_theme_fonts'] ) || 'yes' !== $atts['use_theme_fonts'] ) && isset( $google_fonts_data['values']['font_family'] ) ) {
    wp_enqueue_style( 'vc_google_fonts_' . vc_build_safe_css_class( $google_fonts_data['values']['font_family'] ), 'https://fonts.googleapis.com/css?family=' . $google_fonts_data['values']['font_family'] . $subsets );
}
$output .= '<div class="' . esc_attr( $css_class ) . '" >';
$style = '';
if ( ! empty( $styles ) ) {
	$style = 'style="' . esc_attr( implode( ';', $styles ) ) . '"';
}

if ( ! empty( $link ) ) {
	$link = vc_build_link( $link );
	$text = '<a href="' . esc_attr( $link['url'] ) . '"'
		. ( $link['target'] ? ' target="' . esc_attr( $link['target'] ) . '"' : '' )
		. ( $link['title'] ? ' title="' . esc_attr( $link['title'] ) . '"' : '' )
		. '>' . $text . '</a>';
}

$output .= '<' . $font_container_data['values']['tag'] . ' ' . $style . ' >';
$output .= $text;
$output .= '</' . $font_container_data['values']['tag'] . '>';
$output .= '</div>';

echo stm_echo_safe_output($output);