<?php

require_once STM_LMS_PRO_PATH . '/addons/course_bundle/settings.php';
require_once STM_LMS_PRO_PATH . '/addons/course_bundle/my-bundles.php';
require_once STM_LMS_PRO_PATH . '/addons/course_bundle/my-bundle.php';
require_once STM_LMS_PRO_PATH . '/addons/course_bundle/cart.php';
require_once STM_LMS_PRO_PATH . '/addons/course_bundle/woocommerce.php';
require_once STM_LMS_PRO_PATH . '/addons/course_bundle/vc_module.php';

new STM_LMS_Course_Bundle;

class STM_LMS_Course_Bundle
{

    function __construct()
    {
        add_filter('stm_lms_post_types_array', array($this, 'assignment_post_type'), 10, 1);
        add_filter('stm_lms_post_types', array($this, 'bundles_stm_lms_post_types'), 5, 1);

        add_action('stm_lms_after_profile_buttons_all', array($this, 'bundles'), 10, 1);
        add_action('stm_lms_after_wishlist_list', array($this, 'wishlist_list'), 10, 1);
    }

    /*FILTERS*/
    function assignment_post_type($posts)
    {

        $posts['stm-course-bundles'] = array(
            'single' => esc_html__('Course Bundles', 'masterstudy-lms-learning-management-system-pro'),
            'plural' => esc_html__('Course Bundles', 'masterstudy-lms-learning-management-system-pro'),
            'args' => array(
                'public' => true,
                'exclude_from_search' => false,
                'publicly_queryable' => true,
                'show_in_menu' => false,
                'supports' => array('title', 'editor', 'thumbnail', 'revisions', 'author'),
            )
        );

        return $posts;
    }


    function bundles_stm_lms_post_types($post_types)
    {
        $post_types[] = 'stm-course-bundles';

        return $post_types;
    }


    /*ACTIONS*/
    function bundles()
    {
        STM_LMS_Templates::show_lms_template('account/private/parts/bundles_btn');
    }

    function wishlist_list($wishlist) {
        $columns = 2;
        $title = esc_html__('Bundles', 'masterstudy-lms-learning-management-system-pro');
        STM_LMS_Templates::show_lms_template(
            'bundles/card/php/list',
            compact('wishlist', 'columns', 'title')
        );
    }

    /*FUNCTIONS*/
    static function url()
    {
        return home_url('/') . STM_LMS_WP_Router::route_urls('bundles');
    }

    static function get_bundle_courses_price($bundle_id) {
        $price = 0;
        $courses = get_post_meta($bundle_id, STM_LMS_My_Bundle::bundle_courses_key(), true);

        if (empty($courses)) return $price;

        foreach ($courses as $course_id) {
            $price += STM_LMS_Course::get_course_price($course_id);
        }

        return $price;
    }

    static function get_bundle_price($bundle_id) {
        return get_post_meta($bundle_id, STM_LMS_My_Bundle::bundle_price_key(), true);
    }

    static function get_bundle_rating($bundle_id)
    {
        $r = array(
            'count' => 0,
            'average' => 0,
            'percent' => 0
        );

        $courses = get_post_meta($bundle_id, STM_LMS_My_Bundle::bundle_courses_key(), true);

        if (empty($courses)) return $r;

        foreach ($courses as $course_id) {
            $reviews = get_post_meta($course_id, 'course_marks', true);
            if (!empty($reviews)) {
                $rates = STM_LMS_Course::course_average_rate($reviews);
                $r['count'] ++;
                $r['average'] += $rates['average'];
                $r['percent'] += $rates['percent'];
            }
        }

        return $r;
    }

}