<?php

require_once dirname(__FILE__) . '/tgm-plugin-activation.php';

add_action('tgmpa_register', 'stm_require_plugins');

function stm_require_plugins($return = false)
{

    $plugins_path = get_template_directory() . '/inc/tgm/plugins';

    $plugins = array(
        'stm-post-type' => array(
            'name' => 'STM Configurations',
            'slug' => 'stm-post-type',
            'source' => get_package( 'stm-post-type', 'zip' ),
            'version' => '4.0.4',
        ),
        'stm-gdpr-compliance' => array(
            'name' => 'GDPR Compliance & Cookie Consent',
            'slug' => 'stm-gdpr-compliance',
            'source' => get_package('stm-gdpr-compliance', 'zip'),
            'version' => '1.1',
        ),
        'masterstudy-lms-learning-management-system' => array(
            'name' => 'MasterStudy LMS',
            'slug' => 'masterstudy-lms-learning-management-system',
        ),
        'masterstudy-lms-learning-management-system-pro' => array(
            'name' => 'MasterStudy LMS PRO',
            'slug' => 'masterstudy-lms-learning-management-system-pro',
            'source' => get_package( 'masterstudy-lms-learning-management-system-pro', 'zip' ),
            'version' => '2.2.1',
        ),
        'js_composer' => array(
            'name' => 'WPBakery Visual Composer',
            'slug' => 'js_composer',
            'source' => get_package('js_composer', 'zip'),
            'version' => '6.2.0',
            'required' => true,
            'external_url' => 'http://vc.wpbakery.com',
        ),
        'revslider' => array(
            'name' => 'Revolution Slider',
            'slug' => 'revslider',
            'source' => get_package('revslider', 'zip'),
            'version' => '6.2.6',
            'required' => false,
            'external_url' => 'http://www.themepunch.com/revolution/',
        ),
        'paid-memberships-pro' => array(
            'name' => 'Paid Memberships Pro',
            'slug' => 'paid-memberships-pro',
            'required' => false,
        ),
        'breadcrumb-navxt' => array(
            'name' => 'Breadcrumb NavXT',
            'slug' => 'breadcrumb-navxt',
            'required' => false,
        ),
        'contact-form-7' => array(
            'name' => 'Contact Form 7',
            'slug' => 'contact-form-7',
            'required' => false,
        ),
        'buddypress' => array(
            'name' => 'BuddyPress',
            'slug' => 'buddypress',
            'required' => false,
        ),
        'woocommerce' => array(
            'name' => 'Woocommerce',
            'slug' => 'woocommerce',
        ),
        'eroom-zoom-meetings-webinar' => array(
            'name' => 'eRoom – Zoom Meetings & Webinar',
            'slug' => 'eroom-zoom-meetings-webinar',
        ),
    );

    if ($return) {
        return $plugins;
    } else {
        foreach ($plugins as $plugin => $plugin_data) {
            tgmpa($plugins);
        }
    };

}

;