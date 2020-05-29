<?php

$atts = vc_map_get_attributes($this->getShortcode(), $atts);
extract($atts);

$style = $atts['style'] = (empty($style)) ? 'style_1' : $style;

stm_load_vc_element('testimonials', $atts, $style); ?>