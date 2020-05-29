<?php
/**
 * @var $icon_align
 * @var $icon_height
 * @var $icon_width
 * @var $icon_width
 * @var $link_color_style
 * @var $css_icon
 * @var $box_bg_color
 * @var $box_text_color
 * @var $title_holder
 * @var $icon_size
 * @var $icon_color
 */


$atts = vc_map_get_attributes( $this->getShortcode(), $atts );
extract( $atts );
$css_class = apply_filters( VC_SHORTCODE_CUSTOM_CSS_FILTER_TAG, vc_shortcode_custom_css_class( $css, ' ' ) );
$css_icon_class = apply_filters( VC_SHORTCODE_CUSTOM_CSS_FILTER_TAG, vc_shortcode_custom_css_class( $css_icon, ' ' ) );

$icon_inline_css = '';
$icon_inline_css = ($icon_align == 'center') ? "height:".esc_attr($icon_height)."px;" : "width:".esc_attr($icon_width)."px;";

$link = vc_build_link( $link );

$unique = stm_create_unique_id($atts);

if(!empty($box_icon_bg_color)) {
    $icon_inline_css .= " background-color: {$box_icon_bg_color};";
}

$inline_css = ".{$unique} {
    background:{$box_bg_color} !important; color:{$box_text_color};
}
.{$unique} .icon {
    {$icon_inline_css}
}
.{$unique} .icon i {
    font-size: {$icon_size}px; 
    color: {$icon_color} !important;
}";

$css_class .= ' stm_icon_box_hover_' . $hover_pos;

stm_module_styles('iconbox', 'style_1', array(), $inline_css);

?>

<?php if(!empty($link['url'])): ?>
	<a
		href="<?php echo esc_url($link['url']) ?>"
		title="<?php if(!empty($link['title'])){ echo esc_attr($link['title']); }; ?>"
		<?php if(!empty($link['target'])): ?>
			target="_blank"
		<?php endif; ?>
	>
<?php endif; ?>

	<div class="icon_box<?php echo esc_attr( $css_class.' '.$link_color_style . ' ' . $unique ); ?> clearfix">
		<div class="icon_alignment_<?php echo esc_attr($icon_align); ?>">
			<?php if( $icon ){ ?>
				<div class="icon<?php echo esc_attr($css_icon_class); ?>">
					<i class="<?php echo esc_attr( $icon ); ?>"></i>
				</div>
			<?php } ?>

			<div class="icon_text">
				<?php if ( $title ) { ?>
					<<?php echo esc_attr($title_holder); ?> style="color:<?php echo esc_attr($box_text_color); ?>">
                        <?php echo sanitize_text_field( $title ); ?>
                    </<?php echo esc_attr($title_holder); ?>>
				<?php } ?>
				<?php echo wpb_js_remove_wpautop( $content, true ); ?>
			</div>
		</div> <!-- align icons -->
	</div>

<?php if(!empty($link['url'])): ?>
	</a>
<?php endif; ?>