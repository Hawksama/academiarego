<?php if (!defined('ABSPATH')) exit; //Exit if accessed directly ?>

<?php get_header();

stm_lms_register_style('user');
do_action('stm_lms_template_main');

if (function_exists('vc_asset_url')) {
    wp_enqueue_style('stm_lms_wpb_front_css', vc_asset_url('css/js_composer.min.css'));
}

$current_user = STM_LMS_User::get_current_user('', false, true);

$style = STM_LMS_Options::get_option('profile_style', 'default');

if ($style === 'classic'): ?>
    <?php STM_LMS_Templates::show_lms_template('/account/private/classic/parts/certificates', array('current_user' => $current_user)); ?>
<?php else : ?>

    <div class="stm-lms-wrapper stm-lms-wrapper-wishlist">
        <div class="container">
            <?php STM_LMS_Templates::show_lms_template('/account/private/parts/certificates', array('current_user' => $current_user)); ?>
        </div>
    </div>
<?php endif; ?>

<?php get_footer();