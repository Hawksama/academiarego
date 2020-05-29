<?php
/**
 * @var $image_size
 * @var $name
 * @var $phone
 * @var $email
 * @var $skype
 */

extract( shortcode_atts( array(
	'name'  => '',
	'image'  => '',
	'image_size'  => 'thumb-124x108',
	'job'  => '',
	'phone'  => '',
	'email'  => '',
	'skype'  => '',
	'css'  => ''
), $atts ) );

$css_class = apply_filters( VC_SHORTCODE_CUSTOM_CSS_FILTER_TAG, vc_shortcode_custom_css_class( $css, ' ' ) );
$image = wpb_getImageBySize( array( 'attach_id' => $image, 'thumb_size' => $image_size ) );


stm_module_styles('contact', 'style_1', array());
?>

<div class="stm_contact<?php echo esc_attr( $css_class ); ?> clearfix">
	<?php if( ! empty( $image['thumbnail'] ) ){ ?>
		<div class="stm_contact_image">
			<?php echo html_entity_decode( $image['thumbnail'] ); ?>
		</div>
	<?php } ?>
	<div class="stm_contact_info">
		<h4 class="name"><?php echo sanitize_text_field( $name ); ?></h4>
		<?php if( $job ){ ?>
			<div class="stm_contact_job heading_font"><?php echo sanitize_text_field( $job ); ?></div>
		<?php } ?>
		<?php if( $phone ){ ?>
			<div class="stm_contact_row"><?php _e( 'Phone: ', 'masterstudy' ); ?><?php echo sanitize_text_field( $phone ); ?></div>
		<?php } ?>
		<?php if( $email ){ ?>
			<div class="stm_contact_row">
                <?php _e( 'Email: ', 'masterstudy' ); ?>
                <a href="mailto:<?php echo stm_echo_safe_output( $email ); ?>"><?php echo stm_echo_safe_output( $email ); ?></a>
            </div>
		<?php } ?>
		<?php if( $skype ){ ?>
			<div class="stm_contact_row">
                <?php _e( 'Skype: ', 'masterstudy' ); ?>
                <a href="skype:<?php echo sanitize_text_field( $skype ); ?>?chat">
                    <?php echo sanitize_text_field( $skype ); ?>
                </a>
            </div>
		<?php } ?>
	</div>
</div>