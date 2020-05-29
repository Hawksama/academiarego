<div class="stm_image_box stm_image_box__<?php echo esc_attr($style . ' ' . $main_class) ?>">

	<?php if (!empty($image)): ?>
        <div class="stm_image_box__image">
			<?php echo html_entity_decode(stm_get_VC_attachment_img_safe($image, $image_size)); ?>
			<?php if (!empty($icon)): ?>
                <i class="stm_image_box__icon <?php echo esc_attr($icon); ?>"></i>
			<?php endif; ?>
        </div>
	<?php endif; ?>

    <div class="stm_image_box__content">

		<?php if (!empty($title)): ?>
            <div class="stm_image_box__title">
                <h3><?php echo esc_attr($title); ?></h3>
            </div>
		<?php endif; ?>

		<?php if (!empty($textarea)): ?>
            <div class="stm_image_box__textarea"><?php echo esc_attr($textarea); ?></div>
		<?php endif; ?>

		<?php if (!empty($button)) stm_vc_create_button($button); ?>


    </div>
</div>