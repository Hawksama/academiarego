<?php
require_once($plugin_path . '/importer/helpers/content.php');
require_once($plugin_path . '/importer/helpers/theme_options.php');
require_once($plugin_path . '/importer/helpers/set_lms_content.php');
require_once($plugin_path . '/importer/helpers/slider.php');
require_once($plugin_path . '/importer/helpers/addons.php');
require_once($plugin_path . '/importer/helpers/widgets.php');
require_once($plugin_path . '/importer/helpers/set_content.php');
require_once($plugin_path . '/importer/helpers/set_hb_options.php');
require_once($plugin_path . '/importer/helpers/megamenu/config.php');

function stm_demo_import_content()
{

    if (current_user_can('manage_options')) {

        $layout = 'default';

        if (!empty($_GET['demo_template'])) {
            $layout = sanitize_title($_GET['demo_template']);
        }

        update_option('stm_lms_layout', $layout);

        /*Import content*/
        stm_theme_import_content($layout);

        /*Import theme options*/
        stm_set_layout_options($layout);

        /*Import sliders*/
        stm_theme_import_sliders($layout);

        /*Import Widgets*/
        stm_theme_import_widgets($layout);

        /*Set menu and pages*/
        stm_set_content_options($layout);

        /*Set LMS Options*/
        stm_set_lms_options($layout);

        /*Addons*/
        stm_theme_enable_addons($layout);

        do_action('stm_masterstudy_importer_done');

        wp_send_json(array(
            'url' => get_home_url('/'),
            'title' => esc_html__('View site', 'stm-configurations'),
            'theme_options_title' => esc_html__('Theme options', 'stm-configurations'),
            'theme_options' => esc_url_raw(admin_url('customize.php'))
        ));

        die();

    }

}

add_action('wp_ajax_stm_demo_import_content', 'stm_demo_import_content');