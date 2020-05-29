<?php

new STM_LMS_Course_Bundle_Woocommerce;

class STM_LMS_Course_Bundle_Woocommerce
{

    function __construct()
    {
        add_action('stm_lms_woocommerce_order_approved', array($this, 'stm_lms_woocommerce_order_approved'), 10, 2);
        add_action('stm_lms_woocommerce_order_cancelled', array($this, 'stm_lms_woocommerce_order_cancelled'), 10, 2);

        add_filter('stm_lms_before_create_order', array($this, 'stm_lms_before_create_order'), 100, 2);

    }

    function stm_lms_woocommerce_order_approved($course_data, $user_id)
    {

        if (!empty($course_data['bundle_id'])) {

            $courses = get_post_meta($course_data['bundle_id'], STM_LMS_My_Bundle::bundle_courses_key(), true);

            if (!empty($courses)) {
                foreach ($courses as $course_id) {
                    STM_LMS_Course::add_user_course($course_id, $user_id, 0, 0, false, '', $course_data['bundle_id']);
                    STM_LMS_Course::add_student($course_id);
                }
            }
        }

    }

    function stm_lms_woocommerce_order_cancelled($course_data, $user_id)
    {

        if (!empty($course_data['bundle_id'])) {
            $bundle_id = intval($course_data['bundle_id']);

            $bundle_courses = get_post_meta($bundle_id, STM_LMS_My_Bundle::bundle_courses_key(), true);

            if (!empty($bundle_courses)) {
                foreach ($bundle_courses as $id) {
                    global $wpdb;
                    $table = stm_lms_user_courses_name($wpdb);

                    $wpdb->delete(
                        $table,
                        array(
                            'user_id' => $user_id,
                            'course_id' => $id,
                            'bundle_id' => $bundle_id
                        )
                    );
                }
            }

        }

    }

    function stm_lms_before_create_order($order_meta, $cart_item)
    {

        if (!empty($cart_item['bundle_id'])) $order_meta['bundle_id'] = $cart_item['bundle_id'];

        return $order_meta;
    }

}