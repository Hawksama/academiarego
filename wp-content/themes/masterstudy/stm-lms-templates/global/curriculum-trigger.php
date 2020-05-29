<?php
/**
 * @var $item_id
 */

stm_lms_register_style('curriculum_trigger');
stm_lms_register_script('curriculum_trigger');
stm_module_styles('curriculum_trigger');
?>

<div class="stm-lms-curriculum-trigger">
    <i class="fa fa-list-ul"></i>
</div>

<?php
/*LOGO*/
$type = get_post_meta($item_id, 'type', true);
$logo = ($type === 'video' || $type === 'stream' || $type === 'zoom_conference') ? 'logo_transparent' : 'logo';

$logo = stm_option($logo, false, 'url');
?>

<div class="logo-unit">
    <?php if ($logo): ?>
        <a href="<?php echo esc_url(home_url('/')); ?>">
            <img class="img-responsive logo_transparent_static visible" src="<?php echo esc_url($logo); ?>"
                 style="width: <?php echo stm_option('logo_width', '246'); ?>px;" alt="<?php bloginfo('name'); ?>"/>
        </a>
    <?php else: ?>
        <a href="<?php echo esc_url(home_url('/')); ?>"><span class="logo"><?php bloginfo('name'); ?></span></a>
    <?php endif; ?>
</div>
