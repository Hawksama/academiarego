<?php

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

?>