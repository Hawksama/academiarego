<?php


define( 'MASTERSTUDY_CHILD_VERSION', '1.0' );
if( is_user_logged_in() ) {
    $user = wp_get_current_user();
    $roles = ( array ) $user->roles;

    if (in_array('administrator', $roles) || in_array('stm_lms_instructor', $roles)) {
        add_action( 'bp_setup_nav', 'woocommerce_payment' );
    }
}

function woocommerce_payment() {
    global $bp;

    bp_core_new_nav_item( array( 
          'name' => _x( 'Payments', 'No need translation...', 'yith-stripe-connect-for-woocommerce' ), 
          'slug' => 'payments', 
          'screen_function' => 'trigger_woocommerce_payment', 
          'position' => 100,
          'parent_url'      => bp_loggedin_user_domain() . '/payments/',
          'parent_slug'     => $bp->profile->slug
    ) );
}
add_action( 'bp_setup_nav', 'woocommerce_payment' );


function trigger_woocommerce_payment() {
  // Add title and content here - last is to call the members plugin.php template.
  add_action( 'bp_template_title', 'woocommerce_payment_title' );
  add_action( 'bp_template_content', 'woocommerce_payment_content' );
  bp_core_load_template( 'buddypress/members/single/plugins' );
}

function woocommerce_payment_title() {
    echo _x( 'Stripe Connect', 'No need translation...', 'yith-stripe-connect-for-woocommerce' );
}

function woocommerce_payment_content() { 
    do_action('woocommerce_account_stripe-connect_endpoint');
}

function child_theme_styling() {
    wp_dequeue_style( 'stm_theme_styles' );
    wp_deregister_style( 'stm_theme_styles' );

    wp_enqueue_style('stm_theme_styles', get_stylesheet_directory_uri() . '/assets/css/styles.css', NULL, MASTERSTUDY_CHILD_VERSION, 'all');
}
add_action( 'wp_enqueue_scripts', 'child_theme_styling', 10 );

function woocommerce_cart_button_text() {
    if( class_exists('WooCommerce' ) ){
        remove_filter( 'woocommerce_product_single_add_to_cart_text', 'woo_custom_cart_button_text' );
    }
}
add_action( 'init', 'woocommerce_cart_button_text' );

function my_callback() {
    if($_SERVER['REQUEST_URI'] == '/stm_lms_course_category/trainers/') {
        wp_redirect($_SERVER['REQUEST_SCHEME'] . '://' .$_SERVER['SERVER_NAME'] . '/members', 301);
        exit();
    }
}
add_action( 'template_redirect', 'my_callback' );


add_filter( 'site_transient_update_plugins', 'remove_plugin_updates' );
function remove_plugin_updates( $value ) {
    unset( $value->response['jetpack/modules/woocommerce-analytics/wp-woocommerce-analytics.php'] );
    return $value;
}