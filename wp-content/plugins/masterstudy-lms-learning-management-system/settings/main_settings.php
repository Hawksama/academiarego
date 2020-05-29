<?php
add_filter('wpcfto_options_page_setup', function ($setups) {


    $pages = STM_LMS_Settings::stm_get_post_type_array('page');

    $setups[] = array(
        'option_name' => 'stm_lms_settings',
        'page' => array(
            'page_title' => 'LMS Settings',
            'menu_title' => 'STM LMS',
            'menu_slug' => 'stm-lms-settings',
            'icon' => 'dashicons-welcome-learn-more',
            'position' => 5,
        ),
        'fields' => array(
            'section_1' => array(
                'name' => esc_html__('General', 'masterstudy-lms-learning-management-system'),
                'fields' => array(
                    'main_color' => array(
                        'type' => 'color',
                        'label' => esc_html__('Main color', 'masterstudy-lms-learning-management-system'),
                        'columns' => '50'
                    ),
                    'secondary_color' => array(
                        'type' => 'color',
                        'label' => esc_html__('Secondary color', 'masterstudy-lms-learning-management-system'),
                        'columns' => '50'
                    ),
                    'currency_symbol' => array(
                        'type' => 'text',
                        'label' => esc_html__('Currency symbol', 'masterstudy-lms-learning-management-system'),
                        'columns' => '50'
                    ),
                    'currency_position' => array(
                        'type' => 'select',
                        'label' => esc_html__('Currency Position', 'masterstudy-lms-learning-management-system'),
                        'value' => 'left',
                        'options' => array(
                            'left' => esc_html__('Left', 'masterstudy-lms-learning-management-system'),
                            'right' => esc_html__('Right', 'masterstudy-lms-learning-management-system'),
                        ),
                        'columns' => '50'
                    ),
                    'currency_thousands' => array(
                        'type' => 'text',
                        'label' => esc_html__('Thousands Separator', 'masterstudy-lms-learning-management-system'),
                        'value' => ','
                    ),
                    'wocommerce_checkout' => array(
                        'type' => 'checkbox',
                        'label' => esc_html__('Enable WooCommerce Checkout', 'masterstudy-lms-learning-management-system'),
                        'description' => esc_html__('Note, you need to install WooCommerce, and set Cart and Checkout Pages'),
                        'pro' => true
                    ),

                    'wocommerce_checkout' => array(
                        'type' => 'checkbox',
                        'label' => esc_html__('Enable WooCommerce Checkout', 'masterstudy-lms-learning-management-system'),
                        'description' => esc_html__('Note, you need to install WooCommerce, and set Cart and Checkout Pages'),
                        'pro' => true
                    ),
                    'author_fee' => array(
                        'type' => 'number',
                        'label' => esc_html__('Author Fee (%)', 'masterstudy-lms-learning-management-system'),
                        'columns' => '50',
                        'value' => '10',
                        'pro' => true
                    ),
                    'courses_featured_num' => array(
                        'type' => 'number',
                        'label' => esc_html__('Number of free featured', 'masterstudy-lms-learning-management-system'),
                        'columns' => '50',
                        'value' => 1,
                        'pro' => true
                    ),
                )
            ),
            'section_2' => array(
                'name' => esc_html__('Courses', 'masterstudy-lms-learning-management-system'),
                'fields' => array(
                    'courses_page' => array(
                        'type' => 'select',
                        'label' => esc_html__('Courses Page', 'masterstudy-lms-learning-management-system'),
                        'options' => $pages,
                        'columns' => '33'
                    ),
                    'courses_view' => array(
                        'type' => 'select',
                        'label' => esc_html__('Courses Page Layout', 'masterstudy-lms-learning-management-system'),
                        'options' => array(
                            'grid' => esc_html__('Grid', 'masterstudy-lms-learning-management-system'),
                            //'list' => esc_html__('List', 'masterstudy-lms-learning-management-system'),
                        ),
                        'value' => 'grid',
                        'columns' => '33'
                    ),
                    'courses_per_page' => array(
                        'type' => 'number',
                        'label' => esc_html__('Courses per page', 'masterstudy-lms-learning-management-system'),
                        'value' => '9',
                        'columns' => '33'
                    ),
                    'courses_per_row' => array(
                        'type' => 'select',
                        'label' => esc_html__('Courses per row', 'masterstudy-lms-learning-management-system'),
                        'options' => array(
                            '2' => 2,
                            '3' => 3,
                            '4' => 4,
                            '6' => 6,
                        ),
                        'value' => '4',
                        'columns' => '33'
                    ),
                    'course_card_view' => array(
                        'type' => 'select',
                        'label' => esc_html__('Course Card Info', 'masterstudy-lms-learning-management-system'),
                        'options' => array(
                            'center' => esc_html__('Center', 'masterstudy-lms-learning-management-system'),
                            'right' => esc_html__('Right', 'masterstudy-lms-learning-management-system'),
                        ),
                        'value' => 'center',
                        'columns' => '33'
                    ),
                    'course_card_style' => array(
                        'type' => 'select',
                        'label' => esc_html__('Course Card Style', 'masterstudy-lms-learning-management-system'),
                        'options' => array(
                            'style_1' => esc_html__('Style 1', 'masterstudy-lms-learning-management-system'),
                            'style_2' => esc_html__('Style 2', 'masterstudy-lms-learning-management-system'),
                        ),
                        'value' => 'style_1',
                        'columns' => '33'
                    ),
                    'courses_categories_slug' => array(
                        'type' => 'text',
                        'label' => esc_html__('Courses category parent slug', 'masterstudy-lms-learning-management-system'),
                        'value' => 'stm_lms_course_category',
                        'columns' => '33',
                    ),
                    'courses_image_size' => array(
                        'type' => 'text',
                        'label' => esc_html__('Courses Image Size (Ex.: 200x100)', 'masterstudy-lms-learning-management-system'),
                        'value' => '',
                        'columns' => '33',
                    ),
                    'load_more_type' => array(
                        'type' => 'select',
                        'label' => esc_html__('Load More Type', 'masterstudy-lms-learning-management-system'),
                        'options' => array(
                            'button' => esc_html__('Button', 'masterstudy-lms-learning-management-system'),
                            'infinite' => esc_html__('Infinite Scrolling', 'masterstudy-lms-learning-management-system'),
                        ),
                        'value' => 'button',
                        'columns' => '33',
                    ),
                    'enable_courses_filter' => array(
                        'type' => 'checkbox',
                        'label' => esc_html__('Enable Archive Filter', 'masterstudy-lms-learning-management-system'),
                    ),
                    'enable_courses_filter_notice' => array(
                        'type' => 'notice',
                        'label' => esc_html__('With enabled filters, default courses per row will be 3 with adaptive settings for best look.', 'masterstudy-lms-learning-management-system'),
                        'dependency' => array(
                            'key' => 'enable_courses_filter',
                            'value' => 'not_empty'
                        ),
                    ),
                    'enable_courses_filter_category' => array(
                        'type' => 'checkbox',
                        'label' => esc_html__('Enable Archive Filter - Category', 'masterstudy-lms-learning-management-system'),
                        'dependency' => array(
                            'key' => 'enable_courses_filter',
                            'value' => 'not_empty'
                        ),
                        'columns' => '33',
                    ),
                    'enable_courses_filter_subcategory' => array(
                        'type' => 'checkbox',
                        'label' => esc_html__('Enable Archive Filter - Subcategory', 'masterstudy-lms-learning-management-system'),
                        'dependency' => array(
                            'key' => 'enable_courses_filter',
                            'value' => 'not_empty'
                        ),
                        'columns' => '33',
                    ),
                    'enable_courses_filter_levels' => array(
                        'type' => 'checkbox',
                        'label' => esc_html__('Enable Archive Filter - Levels', 'masterstudy-lms-learning-management-system'),
                        'dependency' => array(
                            'key' => 'enable_courses_filter',
                            'value' => 'not_empty'
                        ),
                        'columns' => '33',
                    ),
                    'enable_courses_filter_rating' => array(
                        'type' => 'checkbox',
                        'label' => esc_html__('Enable Archive Filter - Rating', 'masterstudy-lms-learning-management-system'),
                        'dependency' => array(
                            'key' => 'enable_courses_filter',
                            'value' => 'not_empty'
                        ),
                        'columns' => '33',
                    ),
                    'enable_courses_filter_status' => array(
                        'type' => 'checkbox',
                        'label' => esc_html__('Enable Archive Filter - Status', 'masterstudy-lms-learning-management-system'),
                        'dependency' => array(
                            'key' => 'enable_courses_filter',
                            'value' => 'not_empty'
                        ),
                        'columns' => '33',
                    ),
                    'enable_courses_filter_instructor' => array(
                        'type' => 'checkbox',
                        'label' => esc_html__('Enable Archive Filter - Instructor', 'masterstudy-lms-learning-management-system'),
                        'dependency' => array(
                            'key' => 'enable_courses_filter',
                            'value' => 'not_empty'
                        ),
                        'columns' => '33',
                    ),
                    'enable_courses_filter_price' => array(
                        'type' => 'checkbox',
                        'label' => esc_html__('Enable Archive Filter - Price', 'masterstudy-lms-learning-management-system'),
                        'dependency' => array(
                            'key' => 'enable_courses_filter',
                            'value' => 'not_empty'
                        ),
                        'columns' => '33',
                    ),
                    'disable_lazyload' => array(
                        'type' => 'checkbox',
                        'label' => esc_html__('Disable Lazyload', 'masterstudy-lms-learning-management-system'),
                    ),
                )
            ),
            'section_course' => array(
                'name' => esc_html__('Course', 'masterstudy-lms-learning-management-system'),
                'fields' => array(
                    'course_style' => array(
                        'type' => 'select',
                        'label' => esc_html__('Courses Page Style', 'masterstudy-lms-learning-management-system'),
                        'options' => array(
                            'default' => esc_html__('Default', 'masterstudy'),
                            'classic' => esc_html__('Classic', 'masterstudy'),
                            'udemy' => esc_html__('Udemy (Udemy Addon required)', 'masterstudy'),
                        ),
                        'value' => 'default',
                        'pro' => true,
                    ),
                    'redirect_after_purchase' => array(
                        'type' => 'checkbox',
                        'label' => esc_html__('Redirect to Checkout after adding to Cart', 'masterstudy-lms-learning-management-system'),
                    ),
                    'course_allow_new_categories' => array(
                        'type' => 'checkbox',
                        'label' => esc_html__('Allow instructors to create new categories', 'masterstudy-lms-learning-management-system'),
                        'description' => esc_html__('Allow instructors create new categories for courses.', 'masterstudy-lms-learning-management-system'),
                    ),
                    'enable_sticky' => array(
                        'type' => 'checkbox',
                        'label' => esc_html__('Enable bottom sticky panel', 'masterstudy-lms-learning-management-system'),
                    ),
                    'enable_sticky_title' => array(
                        'type' => 'checkbox',
                        'label' => esc_html__('Enable Title in bottom sticky panel', 'masterstudy-lms-learning-management-system'),
                        'dependency' => array(
                            'key' => 'enable_sticky',
                            'value' => 'not_empty'
                        ),
                        'columns' => '50'
                    ),
                    'enable_sticky_rating' => array(
                        'type' => 'checkbox',
                        'label' => esc_html__('Enable Rating in bottom sticky panel', 'masterstudy-lms-learning-management-system'),
                        'dependency' => array(
                            'key' => 'enable_sticky',
                            'value' => 'not_empty'
                        ),
                        'columns' => '50'
                    ),
                    'enable_sticky_teacher' => array(
                        'type' => 'checkbox',
                        'label' => esc_html__('Enable Teacher in bottom sticky panel', 'masterstudy-lms-learning-management-system'),
                        'dependency' => array(
                            'key' => 'enable_sticky',
                            'value' => 'not_empty'
                        ),
                        'columns' => '50'
                    ),
                    'enable_sticky_category' => array(
                        'type' => 'checkbox',
                        'label' => esc_html__('Enable Category in bottom sticky panel', 'masterstudy-lms-learning-management-system'),
                        'dependency' => array(
                            'key' => 'enable_sticky',
                            'value' => 'not_empty'
                        ),
                        'columns' => '50'
                    ),
                    'enable_sticky_price' => array(
                        'type' => 'checkbox',
                        'label' => esc_html__('Enable Price in bottom sticky panel', 'masterstudy-lms-learning-management-system'),
                        'dependency' => array(
                            'key' => 'enable_sticky',
                            'value' => 'not_empty'
                        ),
                        'columns' => '50'
                    ),
                    'enable_sticky_button' => array(
                        'type' => 'checkbox',
                        'label' => esc_html__('Enable Buy Button in bottom sticky panel', 'masterstudy-lms-learning-management-system'),
                        'dependency' => array(
                            'key' => 'enable_sticky',
                            'value' => 'not_empty'
                        ),
                        'columns' => '50'
                    ),
                )
            ),
            'section_routes' => array(
                'name' => esc_html__('Routes', 'masterstudy-lms-learning-management-system'),
                'fields' => array(
                    'user_url' => array(
                        'type' => 'text',
                        'label' => esc_html__('User Private Base Url (Default /lms-user)', 'masterstudy-lms-learning-management-system'),
                        'columns' => '50'
                    ),
                    'user_url_profile' => array(
                        'type' => 'text',
                        'label' => esc_html__('User Public Base Url (Default /lms-user_profile)', 'masterstudy-lms-learning-management-system'),
                        'columns' => '50'
                    ),
                    'certificate_url' => array(
                        'type' => 'text',
                        'label' => esc_html__('Certificates Base Url (Default /lms-certificates)', 'masterstudy-lms-learning-management-system'),
                        'columns' => '50'
                    ),
                    'login_url' => array(
                        'type' => 'text',
                        'label' => esc_html__('Login Url (Default /lms-login)', 'masterstudy-lms-learning-management-system'),
                        'columns' => '50'
                    ),
                    'chat_url' => array(
                        'type' => 'text',
                        'label' => esc_html__('Chat Url (Default /lms-chats)', 'masterstudy-lms-learning-management-system'),
                        'columns' => '50'
                    ),
                    'wishlist_url' => array(
                        'type' => 'text',
                        'label' => esc_html__('Wishlist Url (Default /lms-wishlist)', 'masterstudy-lms-learning-management-system'),
                        'columns' => '50'
                    ),
                    'checkout_url' => array(
                        'type' => 'text',
                        'label' => esc_html__('Checkout Url (Default /lms-checkout)', 'masterstudy-lms-learning-management-system'),
                        'columns' => '50'
                    ),
                )
            ),
            'section_3' => array(
                'name' => esc_html__('Payment Methods', 'masterstudy-lms-learning-management-system'),
                'fields' => array(
                    'payment_methods' => array(
                        'type' => 'payments',
                        'label' => esc_html__('Payment Methods', 'masterstudy-lms-learning-management-system'),
                    ),
                )
            ),
            'section_5' => array(
                'name' => esc_html__('Google API', 'masterstudy-lms-learning-management-system'),
                'fields' => array(
                    'recaptcha_site_key' => array(
                        'type' => 'text',
                        'label' => esc_html__('Recaptcha Site Key', 'masterstudy-lms-learning-management-system'),
                    ),
                    'recaptcha_private_key' => array(
                        'type' => 'text',
                        'label' => esc_html__('Recaptcha Private Key', 'masterstudy-lms-learning-management-system'),
                    ),
                )
            ),
            'section_4' => array(
                'name' => esc_html__('Profiles', 'masterstudy-lms-learning-management-system'),
                'fields' => array(
                    'user_premoderation' => array(
                        'type' => 'checkbox',
                        'label' => esc_html__('Enable Email Confirmation', 'masterstudy-lms-learning-management-system'),
                        'description' => esc_html__('All new registration will have an E-mail with account verification', 'masterstudy-lms-learning-management-system'),
                    ),
                    'instructors_page' => array(
                        'type' => 'select',
                        'label' => esc_html__('Instructors Archive Page', 'masterstudy-lms-learning-management-system'),
                        'options' => $pages,
                        'columns' => 50
                    ),
                    'profile_style' => array(
                        'type' => 'select',
                        'label' => esc_html__('Profile Page Style', 'masterstudy-lms-learning-management-system'),
                        'options' => array(
                            'default' => esc_html__('Default', 'masterstudy-lms-learning-management-system'),
                            'classic' => esc_html__('Classic', 'masterstudy-lms-learning-management-system'),
                        ),
                        'value' => 'default',
                        'columns' => 50
                    ),
                    'cancel_subscription' => array(
                        'type' => 'select',
                        'label' => esc_html__('Cancel subscription page', 'masterstudy-lms-learning-management-system'),
                        'options' => $pages,
                        'description' => esc_html__('If you want to display link to Cancel Subscription page, choose page and add to page content shortcode [pmpro_cancel].', 'masterstudy-lms-learning-management-system'),
                    ),
                    'course_premoderation' => array(
                        'type' => 'checkbox',
                        'label' => esc_html__('Enable Course Pre-moderation', 'masterstudy-lms-learning-management-system'),
                        'description' => esc_html__('Course will have Pending status, until you approve it', 'masterstudy-lms-learning-management-system'),
                        'pro' => true,
                    ),

                )
            ),
            'section_6' => array(
                'name' => esc_html__('Certificates', 'masterstudy-lms-learning-management-system'),
                'fields' => array(
                    'certificate_threshold' => array(
                        'type' => 'number',
                        'pro' => true,
                        'label' => esc_html__('Certificate threshold (%)', 'masterstudy-lms-learning-management-system'),
                        'value' => 70
                    ),
                    'certificate_image' => array(
                        'pro' => true,
                        'type' => 'image',
                        'label' => esc_html__('Certificate Image', 'masterstudy-lms-learning-management-system'),
                    ),
                    /*TITLE*/
                    'certificate_title' => array(
                        'pro' => true,
                        'type' => 'text',
                        'label' => esc_html__('Certificate Title', 'masterstudy-lms-learning-management-system'),
                    ),
                    'certificate_title_color' => array(
                        'type' => 'color',
                        'pro' => true,
                        'label' => esc_html__('Certificate title color', 'masterstudy-lms-learning-management-system'),
                        'columns' => '50'
                    ),
                    'certificate_title_fsz' => array(
                        'type' => 'number',
                        'pro' => true,
                        'label' => esc_html__('Certificate title font size (px)', 'masterstudy-lms-learning-management-system'),
                        'columns' => '50',
                        'value' => 60
                    ),
                    /*SUBTITLE*/
                    'certificate_subtitle' => array(
                        'pro' => true,
                        'type' => 'text',
                        'label' => esc_html__('Certificate subtitle', 'masterstudy-lms-learning-management-system'),
                    ),
                    'certificate_subtitle_color' => array(
                        'type' => 'color',
                        'pro' => true,
                        'label' => esc_html__('Certificate subtitle color', 'masterstudy-lms-learning-management-system'),
                        'columns' => '50'
                    ),
                    'certificate_subtitle_fsz' => array(
                        'type' => 'number',
                        'pro' => true,
                        'label' => esc_html__('Certificate subtitle font size (px)', 'masterstudy-lms-learning-management-system'),
                        'columns' => '50',
                        'value' => 40
                    ),
                    /*TEXT*/
                    'certificate_text' => array(
                        'pro' => true,
                        'type' => 'textarea',
                        'label' => esc_html__('Certificate Text', 'masterstudy-lms-learning-management-system'),
                        'description' => esc_html__(
                            'Available shortcodes: Username - {username}; Course name - {course}; User First name - {user_first_name}; User Last name - {user_last_name}',
                            'masterstudy-lms-learning-management-system'),
                    ),
                    'certificate_text_color' => array(
                        'type' => 'color',
                        'pro' => true,
                        'label' => esc_html__('Certificate text color', 'masterstudy-lms-learning-management-system'),
                        'columns' => '50'
                    ),
                    'certificate_text_fsz' => array(
                        'type' => 'number',
                        'pro' => true,
                        'label' => esc_html__('Certificate text font size (px)', 'masterstudy-lms-learning-management-system'),
                        'columns' => '50',
                        'value' => 17
                    ),
                )
            ),
            'addons' => array(
                'name' => esc_html__('Addons', 'masterstudy-lms-learning-management-system'),
                'fields' => array(
                    'addons' => array(
                        'pro' => true,
                        'type' => 'addons',
                        'label' => esc_html__('Masterstudy LMS PRO Addons', 'masterstudy-lms-learning-management-system'),
                    ),
                )
            ),
            'payout' => array(
                'name' => esc_html__('Payout', 'masterstudy-lms-learning-management-system'),
                'fields' => array(
                    'payout' => array(
                        'pro' => true,
                        'type' => 'payout',
                        'label' => esc_html__('Masterstudy LMS PRO Payout', 'masterstudy-lms-learning-management-system'),
                    ),
                )
            ),
            'gdpr' => array(
                'name' => esc_html__('GDPR', 'masterstudy-lms-learning-management-system'),
                'fields' => array(
                    'gdpr_warning' => array(
                        'type' => 'text',
                        'label' => esc_html__('GDPR Label', 'masterstudy-lms-learning-management-system'),
                        'value' => 'I agree with storage and handling of my data by this website.',
                        'columns' => 50
                    ),
                    'gdpr_page' => array(
                        'type' => 'select',
                        'label' => esc_html__('GDPR Privacy Policy Page', 'masterstudy-lms-learning-management-system'),
                        'options' => $pages,
                        'columns' => 50
                    ),
                )
            ),
        )
    );

    return $setups;
}, 5, 1);

add_action("wpcfto_screen_stm_lms_settings_added", function () {

    add_submenu_page(
        'stm-lms-settings',
        'STM LMS',
        'STM LMS Settings',
        'manage_options',
        'stm-lms-settings'
    );

    $post_types = array(
        'stm-courses',
        'stm-lessons',
        'stm-quizzes',
        'stm-questions',
        'stm-assignments',
        'stm-reviews',
        'stm-orders',
        'stm-ent-groups',
        'stm-payout'
    );

    $taxonomies = array(
        'stm_lms_course_taxonomy'
    );


    foreach ($post_types as $post_type) {
        $post_type_data = get_post_type_object($post_type);

        if (empty($post_type_data)) continue;

        add_submenu_page(
            'stm-lms-settings',
            $post_type_data->label,
            $post_type_data->label,
            'manage_options',
            '/edit.php?post_type=' . $post_type
        );
    }

    foreach ($taxonomies as $taxonomy) {
        $tax_data = get_taxonomy($taxonomy);

        add_submenu_page(
            'stm-lms-settings',
            $tax_data->label,
            $tax_data->label,
            'manage_options',
            'edit-tags.php?taxonomy=' . $taxonomy
        );
    }

}, -1, 10);