<?php

class STM_LMS_Data_Store_CPT extends WC_Product_Data_Store_CPT
{

    public function read(&$product)
    {

        add_filter('woocommerce_is_purchasable', function () {
            return true;
        }, 10, 1);

        $product->set_defaults();

        if (!$product->get_id() || !($post_object = get_post($product->get_id())) || !(('product' === $post_object->post_type) || ('stm-courses' === $post_object->post_type) || ('stm-course-bundles' === $post_object->post_type))) {
            throw new Exception(__('Invalid product.', 'woocommerce'));
        }

        $product->set_props(array(
            'name' => $post_object->post_title,
            'slug' => $post_object->post_name,
            'date_created' => 0 < $post_object->post_date_gmt ? wc_string_to_timestamp($post_object->post_date_gmt) : null,
            'date_modified' => 0 < $post_object->post_modified_gmt ? wc_string_to_timestamp($post_object->post_modified_gmt) : null,
            'status' => $post_object->post_status,
            'description' => $post_object->post_content,
            'short_description' => $post_object->post_excerpt,
            'parent_id' => $post_object->post_parent,
            'menu_order' => $post_object->menu_order,
            'reviews_allowed' => 'open' === $post_object->comment_status,
        ));

        $this->read_attributes($product);
        $this->read_downloads($product);
        $this->read_visibility($product);
        $this->read_product_data($product);
        $this->read_extra_data($product);
        $product->set_object_read(true);

    }

    public function get_product_type($product_id)
    {

        $post_type = get_post_type($product_id);

        if ('product_variation' === $post_type) {
            return 'variation';
        } elseif ( in_array( $post_type, array( 'stm-courses', 'stm-course-bundles', 'product' ) ) ) {
            $terms = get_the_terms($product_id, 'product_type');
            return !empty($terms) ? sanitize_title(current($terms)->name) : 'simple';
        } else {
            return false;
        }
    }
}

add_filter('woocommerce_data_stores', 'stm_lms_woocommerce_data_stores');

function stm_lms_woocommerce_data_stores($stores)
{
    $stores['product'] = 'STM_LMS_Data_Store_CPT';
    return $stores;
}

add_action('woocommerce_checkout_update_order_meta', 'stm_before_create_order', 200, 1);

function stm_before_create_order($order_id)
{
    $cart = WC()->cart->get_cart();
    $ids = array();
    foreach ($cart as $cart_item) {
        $ids[] = apply_filters('stm_lms_before_create_order', array(
            'item_id' => $cart_item['product_id'],
            'price' => $cart_item['line_total'],
            'quantity' => $cart_item['quantity']
        ), $cart_item);
    }
    update_post_meta($order_id, 'stm_lms_courses', $ids);
}

add_action('woocommerce_order_status_completed', 'stm_lms_woocommerce_order_created');

function stm_lms_woocommerce_order_created($order_id)
{
    $order = new WC_Order($order_id);
    $user_id = $order->get_user_id();

    $courses = get_post_meta($order_id, 'stm_lms_courses', true);

    foreach ($courses as $course) {
        STM_LMS_Course::add_user_course($course['item_id'], $user_id, 0, 0);
        STM_LMS_Course::add_student($course['item_id']);

        do_action('stm_lms_woocommerce_order_approved', $course, $user_id);
    }

}

add_action('woocommerce_order_status_pending', 'stm_lms_woocommerce_order_cancelled');
add_action('woocommerce_order_status_failed', 'stm_lms_woocommerce_order_cancelled');
add_action('woocommerce_order_status_on-hold', 'stm_lms_woocommerce_order_cancelled');
add_action('woocommerce_order_status_processing', 'stm_lms_woocommerce_order_cancelled');
add_action('woocommerce_order_status_refunded', 'stm_lms_woocommerce_order_cancelled');
add_action('woocommerce_order_status_cancelled', 'stm_lms_woocommerce_order_cancelled');

function stm_lms_woocommerce_order_cancelled($order_id)
{
    $order = new WC_Order($order_id);
    $user_id = $order->get_user_id();

    $courses = get_post_meta($order_id, 'stm_lms_courses', true);

    foreach ($courses as $course) {
        stm_lms_get_delete_user_course($user_id, $course['item_id']);
        STM_LMS_Course::remove_student($course['item_id']);

        do_action('stm_lms_woocommerce_order_cancelled', $course, $user_id);
    }
}