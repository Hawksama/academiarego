<?php

if (!empty($_GET['lms-layout'])) {

    add_action('init', 'stm_set_layout');

    function stm_set_layout()
    {
        if (!current_user_can('manage_options')) die;
        update_option('stm_lms_layout', $_GET['lms-layout']);
    }

}

if (!function_exists('stm_get_layout')) {
    function stm_get_layout()
    {
        return get_option('stm_lms_layout', 'default');
    }
}

function stm_layout_plugins($layout = 'default', $get_layouts = false)
{
    $required = array(
        'stm-post-type',
        'js_composer',
        'breadcrumb-navxt',
        'contact-form-7',
    );
    $plugins = array(
        'default' => array(
            'revslider',
            'woocommerce',
            'breadcrumb-navxt',
            'contact-form-7',
        ),
        'online-light' => array(
            'masterstudy-lms-learning-management-system',
            'masterstudy-lms-learning-management-system-pro',
            'paid-memberships-pro',
        ),
        'online-dark' => array(
            'revslider',
            'masterstudy-lms-learning-management-system',
            'masterstudy-lms-learning-management-system-pro',
            'paid-memberships-pro',
        ),
        'academy' => array(
            'masterstudy-lms-learning-management-system',
            'masterstudy-lms-learning-management-system-pro',
            'paid-memberships-pro',
        ),
        'course_hub' => array(
            'masterstudy-lms-learning-management-system',
            'masterstudy-lms-learning-management-system-pro',
            'paid-memberships-pro',
        ),
        'classic_lms' => array(
            'revslider',
            'masterstudy-lms-learning-management-system',
            'masterstudy-lms-learning-management-system-pro',
            'paid-memberships-pro',
        ),
        'udemy' => array(
            'revslider',
            'contact-form-7',
            'breadcrumb-navxt',
            'masterstudy-lms-learning-management-system',
            'masterstudy-lms-learning-management-system-pro',
            'paid-memberships-pro',
        ),
        'single_instructor' => array(
            'revslider',
            'contact-form-7',
            'breadcrumb-navxt',
            'masterstudy-lms-learning-management-system',
            'masterstudy-lms-learning-management-system-pro',
        ),
        'language_center' => array(
            'woocommerce',
            'breadcrumb-navxt',
            'contact-form-7',
        ),
        'rtl-demo' => array(
            'revslider',
            'contact-form-7',
            'breadcrumb-navxt',
            'masterstudy-lms-learning-management-system',
            'masterstudy-lms-learning-management-system-pro',
        ),
        'buddypress-demo' => array(
            'revslider',
            'contact-form-7',
            'breadcrumb-navxt',
            'masterstudy-lms-learning-management-system',
            'masterstudy-lms-learning-management-system-pro',
            'buddypress',
        ),
        'classic-lms-2' => array(
            'revslider',
            'masterstudy-lms-learning-management-system',
            'masterstudy-lms-learning-management-system-pro',
            'paid-memberships-pro',
        ),
        'distance-learning' => array(
            'masterstudy-lms-learning-management-system',
            'masterstudy-lms-learning-management-system-pro',
            'eroom-zoom-meetings-webinar'
        ),
    );

    if ($get_layouts) return $plugins;

    return array_merge($required, $plugins[$layout]);
}