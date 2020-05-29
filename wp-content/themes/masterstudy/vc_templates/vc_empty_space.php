<?php
if ( ! defined( 'ABSPATH' ) ) {
	die( '-1' );
}
/**
 * Shortcode attributes
 * @var $atts
 * @var $height
 * @var $el_class
 * @var $el_id
 * @var $css
 * Shortcode class
 * @var $this WPBakeryShortCode_VC_Empty_space
 */

$laptop_height = (isset($atts['laptop_height'])) ? $atts['laptop_height'] : '';
$tablet_height = (isset($atts['tablet_height'])) ? $atts['tablet_height'] : '';
$mobile_height = (isset($atts['mobile_height'])) ? $atts['mobile_height'] : '';

$height = $el_class = $el_id = $css = '';
$atts = vc_map_get_attributes( $this->getShortcode(), $atts );

$atts['tablet_height'] = $tablet_height;
$atts['mobile_height'] = $mobile_height;
extract( $atts );

$uniq = stm_lms_generate_uniq_id($atts);

$pattern = '/^(\d*(?:\.\d+)?)\s*(px|\%|in|cm|mm|em|rem|ex|pt|pc|vw|vh|vmin|vmax)?$/';
// allowed metrics: http://www.w3schools.com/cssref/css_units.asp
$regexr = preg_match( $pattern, $height, $matches );
$value = isset( $matches[1] ) ? (float) $matches[1] : (float) WPBMap::getParam( 'vc_empty_space', 'height' );
$unit = isset( $matches[2] ) ? $matches[2] : 'px';
$height = $value . $unit;

$inline_css = ( (float) $height >= 0.0 ) ? ' style="height: ' . esc_attr( $height ) . '"' : '';

$class = $uniq . ' vc_empty_space ' . $this->getExtraClass( $el_class ) . vc_shortcode_custom_css_class( $css, ' ' );
$css_class = apply_filters( VC_SHORTCODE_CUSTOM_CSS_FILTER_TAG, $class, $this->settings['base'], $atts );
$wrapper_attributes = array();
if ( ! empty( $el_id ) ) {
	$wrapper_attributes[] = 'id="' . esc_attr( $el_id ) . '"';
}

if($laptop_height !== ''): ?>
    <style type="text/css">
        @media (max-width: 1440px) {
            .<?php echo esc_attr($uniq) ?> {
                height : <?php echo esc_attr($laptop_height); ?>px !important;
            }
        }
    </style>
<?php endif;

if($tablet_height !== ''): ?>
    <style type="text/css">
        @media (max-width: 1024px) {
            .<?php echo esc_attr($uniq) ?> {
                height : <?php echo esc_attr($tablet_height); ?>px !important;
            }
        }
    </style>
<?php endif;

if($mobile_height !== ''): ?>
    <style type="text/css">
        @media (max-width: 768px) {
            .<?php echo esc_attr($uniq) ?> {
                height : <?php echo esc_attr($mobile_height); ?>px !important;
            }
        }
    </style>
<?php endif; ?>

<div class="<?php echo esc_attr( trim( $css_class ) ); ?>"
    <?php echo implode( ' ', $wrapper_attributes ); ?>
    <?php echo stm_echo_safe_output($inline_css); ?> >
    <span class="vc_empty_space_inner"></span>
</div>