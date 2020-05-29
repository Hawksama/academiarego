<?php
if (!defined('ABSPATH')) exit; //Exit if accessed directly ?>

<?php
get_header();

do_action('stm_lms_template_main');

$bundle_id = (!empty($bundle_id)) ? intval($bundle_id) : '';

$current_user = STM_LMS_User::get_current_user();
$tpl = (STM_LMS_Instructor::is_instructor()) ? 'instructor_' : '';

stm_lms_register_style('user');
stm_lms_register_style('edit_account');

if (function_exists('vc_asset_url')) {
    wp_enqueue_style('stm_lms_wpb_front_css', vc_asset_url('css/js_composer.min.css'));
}

?>

    <div class="stm-lms-wrapper stm-lms-wrapper--assignments">

        <div class="container">

            <div class="row">

                <div class="col-md-3 col-sm-12">

                    <?php STM_LMS_Templates::show_lms_template("account/private/{$tpl}parts/info", array('current_user' => $current_user)); ?>

                </div>

                <div class="col-md-9 col-sm-12">

                    <?php STM_LMS_Templates::show_lms_template('bundles/my-bundle', compact('bundle_id')); ?>

                </div>

            </div>

        </div>

    </div>

<?php get_footer(); ?>