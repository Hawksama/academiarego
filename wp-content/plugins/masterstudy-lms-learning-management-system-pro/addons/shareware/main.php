<?php

new STM_LMS_Shareware;

class STM_LMS_Shareware
{

    function __construct()
    {
        add_filter('stm_wpcfto_fields', array($this, 'stm_lms_fields_shareware'));

        add_filter('stm_lms_global/price', array($this, 'global_price'), 10, 2);

        add_filter('stm_lms_has_course_access', array($this, 'course_access'), 10, 3);


        /*TO*/
        add_filter('wpcfto_options_page_setup', array($this, 'stm_lms_settings_page'), 100);
    }

    function stm_lms_settings_page($setups)
    {
        $setups[] = array(
            'page' => array(
                'parent_slug' => 'stm-lms-settings',
                'page_title' => 'Shareware',
                'menu_title' => 'Shareware',
                'menu_slug' => 'stm-lms-shareware',
            ),
            'fields' => $this->stm_lms_settings(),
            'option_name' => 'stm_lms_shareware_settings'
        );

        return $setups;
    }

    function stm_lms_settings()
    {
        return apply_filters('stm_lms_shareware_settings', array(
            'credentials' => array(
                'name' => esc_html__('Credentials', 'masterstudy-lms-learning-management-system-pro'),
                'fields' => array(
                    'shareware_count' => array(
                        'type' => 'number',
                        'label' => esc_html__('Number of free lessons', 'masterstudy-lms-learning-management-system-pro'),
                    ),
                )
            ),
        ));
    }

    function stm_lms_fields_shareware($fields)
    {

        $shareware = array(
            'shareware' => array(
                'type' => 'checkbox',
                'label' => esc_html__('Trial Course', 'masterstudy-lms-learning-management-system-pro'),
            ),
        );

        $fields['stm_courses_settings']['section_settings']['fields'] = $shareware + $fields['stm_courses_settings']['section_settings']['fields'];

        return $fields;
    }

    function is_shareware($post_id)
    {
        $shareware = get_post_meta($post_id, 'shareware', true);

        return ($shareware === 'on');
    }

    function global_price($content, $vars)
    {

        if (!empty($vars['post_id'])) $course_id = $vars['post_id'];
        if (!empty($vars['id'])) $course_id = $vars['id'];

        if (!empty($course_id)) {
            $shareware = self::is_shareware($course_id);
            if ($shareware) return '';
        }


        return $content;
    }

    function course_access($access, $course_id, $item_id)
    {

        if (!empty($course_id)) {

            $shareware_lessons = get_option('stm_lms_shareware_settings', array());

            $shareware_count = (!empty($shareware_lessons['shareware_count'])) ? intval($shareware_lessons['shareware_count']) : 1;

            $shareware = self::is_shareware($course_id);

            if ($shareware and !empty($item_id)) {

                $curriculum = get_post_meta($course_id, 'curriculum', true);
                if (!empty($curriculum)) {
                    $curriculum = explode(',', $curriculum);
                    $curriculum = array_values(array_filter($curriculum, function ($value) {
                        return is_numeric($value);
                    }));
                }

                $item_order = array_search($item_id, $curriculum);

                if (isset($item_order) and $item_order < $shareware_count) {
                    return true;
                }

            } elseif ($shareware) {
                return true;
            }

        }

        return $access;
    }

}