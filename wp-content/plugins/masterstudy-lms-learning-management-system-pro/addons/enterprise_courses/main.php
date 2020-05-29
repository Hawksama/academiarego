<?php

new STM_LMS_Enterprise_Courses;

class STM_LMS_Enterprise_Courses
{

    function __construct()
    {

        /*ACTIONS*/
        add_action('stm_lms_after_profile_buttons_all', array($this, 'enterprise_button'), 10, 1);
        add_action('stm_lms_buy_button_end', array($this, 'add_enterprise_button'), 10, 1);
        add_action('stm_lms_delete_from_cart', array($this, 'delete_from_cart'), 10, 1);
        add_action('stm_lms_order_accepted', array($this, 'order_accepted'), 10, 2);
        add_action('stm_lms_order_remove', array($this, 'order_removed'), 10, 2);
        add_action('stm_lms_group_updated', array($this, 'create_group_users'), 20, 2);
        add_action('stm_lms_group_updated', array($this, 'control_group_courses_after_edit'), 30, 3);
        /*AJAX*/
        add_action('wp_ajax_stm_lms_get_enterprise_groups', array($this, 'stm_lms_get_enterprise_groups'));
        add_action('wp_ajax_stm_lms_add_enterprise_group', array($this, 'stm_lms_add_enterprise_group'));
        add_action('wp_ajax_stm_lms_delete_enterprise_group', array($this, 'stm_lms_delete_enterprise_group'));

        add_action('wp_ajax_stm_lms_add_to_cart_enterprise', array($this, 'add_to_cart_enterprise_course'));
        add_action('stm_lms_woocommerce_order_approved', array($this, 'stm_lms_woocommerce_order_approved'));
        add_action('stm_lms_woocommerce_order_cancelled', array($this, 'stm_lms_woocommerce_order_cancelled'));
        add_action('wp_ajax_stm_lms_import_groups', array($this, 'stm_lms_import_groups'));


        /*FILTERS*/
        add_filter('stm_lms_post_types_array', array($this, 'enterprise_post_type'), 10, 1);
        add_filter('stm_wpcfto_boxes', array($this, 'enterprise_stm_lms_boxes'), 10, 1);
        add_filter('stm_wpcfto_fields', array($this, 'enterprise_stm_lms_fields'), 10, 1);
        add_filter('stm_lms_post_types', array($this, 'enterprise_stm_lms_post_types'), 10, 1);
        add_filter('stm_lms_after_single_item_cart_title', array($this, 'after_single_item_cart_title'));
        add_filter('stm_lms_cart_items_fields', array($this, 'cart_items_fields'));
        add_filter('stm_lms_delete_from_cart_filter', array($this, 'delete_from_cart_filter'), 10, 1);
        add_filter('stm_lms_accept_order', array($this, 'stm_lms_accept_order'));
        add_filter('stm_lms_template_name', array($this, 'button'), 100, 2);
        add_filter('woocommerce_cart_item_name', array($this, 'woo_cart_group_name'), 100, 3);
        add_filter('stm_lms_before_create_order', array($this, 'stm_lms_before_create_order'), 100, 2);


        /*Single Group*/
        add_action('wp_ajax_stm_lms_get_enterprise_group', array($this, 'get_enterprise_group'));
        add_action('wp_ajax_stm_lms_get_user_ent_courses', array($this, 'get_user_ent_courses'));
        add_action('wp_ajax_stm_lms_delete_user_ent_courses', array($this, 'delete_user_ent_courses'));
        add_action('wp_ajax_stm_lms_add_user_ent_courses', array($this, 'add_user_ent_courses'));
        add_action('wp_ajax_stm_lms_change_ent_group_admin', array($this, 'change_ent_group_admin'));
        add_action('wp_ajax_stm_lms_delete_user_from_group', array($this, 'delete_user_from_group'));


        /*THEME OPTIONS*/
        add_filter('wpcfto_options_page_setup', array($this, 'page_setups'));
    }

    public static function get_group_common_limit()
    {
        $options = get_option('stm_lms_enterprise_courses_settings', array());
        return (!empty($options['locked'])) ? $options['locked'] : 99;
    }

    public static function get_group_limit($group)
    {
        return self::get_group_common_limit();
    }

    public static function groups_url()
    {
        return home_url('/') . STM_LMS_WP_Router::route_urls('enterprise_groups');
    }

    public static function is_group_admin($user_id, $group_id)
    {
        $group_author_id = intval(get_post_meta($group_id, 'author_id', true));

        return $user_id === $group_author_id;
    }

    public static function get_enterprise_price($course_id)
    {
        return get_post_meta($course_id, 'enterprise_price', true);
    }

    public static function check_enterprise_in_cart($user_id, $item_id, $group_id, $fields = array())
    {

        global $wpdb;
        $table = stm_lms_user_cart_name($wpdb);

        $fields = (empty($fields)) ? '*' : implode(',', $fields);

        $request = "SELECT {$fields} FROM {$table}
			WHERE 
			user_ID = {$user_id} AND
			item_id = {$item_id} AND
			enterprise = {$group_id}";

        $r = $wpdb->get_results($request, ARRAY_N);
        return $r;
    }

    /*Settings*/
    function page_setups($setups)
    {

        $setups[] = array(
            'page' => array(
                'parent_slug' => 'stm-lms-settings',
                'page_title' => 'Enterprise Courses Settings',
                'menu_title' => 'Enterprise Courses Settings',
                'menu_slug' => 'enterprise_courses',
            ),
            'fields' => $this->stm_lms_settings(),
            'option_name' => 'stm_lms_enterprise_courses_settings'
        );

        return $setups;

    }

    function stm_lms_settings()
    {
        return apply_filters('stm_lms_enterprise_courses_settings', array(
            'credentials' => array(
                'name' => esc_html__('Credentials', 'masterstudy-lms-learning-management-system-pro'),
                'fields' => array(
                    'locked' => array(
                        'type' => 'number',
                        'label' => esc_html__('Number of allowed members in group', 'masterstudy-lms-learning-management-system-pro'),
                        'value' => false
                    ),
                )
            ),
        ));
    }

    /*Actions*/
    function enterprise_button($current_user)
    {
        STM_LMS_Templates::show_lms_template('account/private/parts/groups_btn', array('current_user' => $current_user));
    }

    function add_enterprise_button($course_id)
    {
        $price = STM_LMS_Enterprise_Courses::get_enterprise_price($course_id);
        if (!empty($price)) {
            STM_LMS_Templates::show_lms_template('enterprise_groups/buy', compact('course_id', 'price'));
        }
    }

    static function stm_lms_get_enterprise_groups($direct = false)
    {

        if (!$direct) check_ajax_referer('stm_lms_get_enterprise_groups', 'nonce');

        $groups = array();

        $current_user = STM_LMS_User::get_current_user();
        if (empty($current_user['id'])) {
            if (!$direct) wp_send_json($groups);
            return $groups;
        }
        $current_user_id = $current_user['id'];

        $args = array(
            'post_type' => 'stm-ent-groups',
            'posts_per_page' => -1,
            'meta_query' => array(
                array(
                    'key' => 'author_id',
                    'value' => $current_user_id,
                    'compare' => '='
                )
            )
        );

        $q = new WP_Query($args);
        if ($q->have_posts()) {
            while ($q->have_posts()) {
                $q->the_post();

                $post_id = get_the_ID();
                $emails = get_post_meta($post_id, 'emails', true);
                $emails = (!empty($emails)) ? explode(',', $emails) : '';
                $groups[] = array(
                    'title' => get_the_title(),
                    'emails' => $emails,
                    'group_id' => $post_id,
                    'url' => STM_LMS_Enterprise_Courses::groups_url() . '/' . $post_id
                );

            }
        }

        if (!$direct) wp_send_json($groups);

        return $groups;

    }

    function stm_lms_add_enterprise_group()
    {
        check_ajax_referer('stm_lms_add_enterprise_group', 'nonce');

        $current_user = STM_LMS_User::get_current_user();
        if (empty($current_user['id'])) {
            $res['status'] = 'error';
            $res['message'] = esc_html__('Log in', 'masterstudy-lms-learning-management-system-pro');
            wp_send_json($res);
        };

        $current_user_id = $current_user['id'];

        $res = array(
            'status' => 'success',
            'message' => ''
        );

        $request_body = file_get_contents('php://input');
        $data = json_decode($request_body, true);

        if (empty($data['title'])) {
            $res['status'] = 'error';
            $res['message'] = esc_html__('Specify group name', 'masterstudy-lms-learning-management-system-pro');
            wp_send_json($res);
        }

        /*Check User edits his own group*/
        if (!empty($current_user_id) and !empty($data['group_id'])) {
            if (!STM_LMS_Enterprise_Courses::is_group_admin($current_user_id, intval($data['group_id']))) {
                $res['status'] = 'error';
                $res['message'] = esc_html__('Error. Try again', 'masterstudy-lms-learning-management-system-pro');
            }
        }

        if (empty($data['group_id'])) {
            /*Create new group*/
            $group_id = wp_insert_post(array(
                'post_title' => sanitize_text_field($data['title']),
                'post_type' => 'stm-ent-groups'
            ));

        } else {
            /*Edit Group*/
            $group_id = intval($data['group_id']);
            wp_update_post(array(
                'ID' => $group_id,
                'post_title' => sanitize_text_field($data['title']),
                'post_type' => 'stm-ent-groups'
            ));
        }

        //Update Emails
        $limit = self::get_group_limit($group_id);

        if (!empty($data['emails'])) $data['emails'] = array_splice($data['emails'], 0, $limit);

        $emails = (!empty($data['emails'])) ? sanitize_text_field(implode(',', $data['emails'])) : '';

        if (apply_filters('stm_lms_allow_group_manage', true)) {

            do_action('stm_lms_group_updated', $data['emails'], $group_id, get_post_meta($group_id, 'emails', true));

            update_post_meta($group_id, 'emails', $emails);
            update_post_meta($group_id, 'author_id', $current_user_id);
        }

        $res['group'] = array(
            'post_id' => $group_id,
            'title' => sanitize_text_field($data['title']),
            'emails' => $data['emails']
        );

        wp_send_json($res);

    }

    function stm_lms_delete_enterprise_group()
    {
        check_ajax_referer('stm_lms_delete_enterprise_group', 'nonce');

        $group_id = intval($_GET['group_id']);

        $current_user = STM_LMS_User::get_current_user();
        if (empty($current_user['id'])) die;

        if (get_post_type($group_id) !== 'stm-ent-groups') die;

        $current_user_id = $current_user['id'];

        if (!STM_LMS_Enterprise_Courses::is_group_admin($current_user_id, $group_id)) die;

        $users = self::get_group_users($group_id);

        if (apply_filters('stm_lms_allow_group_manage', true)) {
            foreach ($users as $user_id) {

                global $wpdb;
                $table = stm_lms_user_courses_name($wpdb);

                $wpdb->delete(
                    $table,
                    array(
                        'user_id' => $user_id,
                        'enterprise_id' => $group_id
                    )
                );

            }

            wp_delete_post($group_id, true);
        }

    }

    function add_to_cart_enterprise_course()
    {

        check_ajax_referer('stm_lms_add_to_cart_enterprise', 'nonce');

        if (!is_user_logged_in() or empty($_GET['course_id'])) die;
        $r = array();

        $user = STM_LMS_User::get_current_user();
        $user_id = $user['id'];
        $item_id = intval($_GET['course_id']);
        $groups = array_map('intval', wp_unslash($_GET['groups']));
        $item_meta = STM_LMS_Helpers::parse_meta_field($item_id);
        $quantity = 1;
        $price = apply_filters('stm_lms_enterprice_price', STM_LMS_Enterprise_Courses::get_enterprise_price($item_id), $item_id, $user_id);


        foreach ($groups as $enterprise) {
            $is_woocommerce = STM_LMS_Cart::woocommerce_checkout_enabled();

            $item_added = count(STM_LMS_Enterprise_Courses::check_enterprise_in_cart($user_id, $item_id, $enterprise, array('user_cart_id', 'enterprise')));

            if (!$item_added) {
                stm_lms_add_user_cart(compact('user_id', 'item_id', 'quantity', 'price', 'enterprise'));
            }

            if (!$is_woocommerce) {
                $r['text'] = esc_html__('Go to Cart', 'masterstudy-lms-learning-management-system-pro');
                $r['cart_url'] = esc_url(STM_LMS_Cart::checkout_url());
            } else {
                update_post_meta($item_id, '_price', $price);
                update_post_meta($item_id, '_product_id', $item_id);
                update_post_meta($item_id, 'product_id', $item_id);
                update_post_meta($item_id, '_enterprise_product_id', $enterprise);
                update_post_meta($item_id, '_variation_id', $enterprise);
                WC()->cart->add_to_cart($item_id, 1, 0, array(), array('enterprise_id' => $enterprise));
                $r['text'] = esc_html__('Go to Cart', 'masterstudy-lms-learning-management-system-pro');
                $r['cart_url'] = esc_url(wc_get_cart_url());
            }
        }

        $r['redirect'] = STM_LMS_Options::get_option('redirect_after_purchase', false);


        wp_send_json($r);
    }

    function create_group_users($emails, $group_id)
    {
        foreach ($emails as $email) {
            $user = get_user_by('email', $email);

            $subject = sprintf(esc_html__('New group invite', 'masterstudy-lms-learning-management-system-pro'), get_bloginfo('name'));
            $message = sprintf(esc_html__('You were added to group: "%s". Now you can check new courses, bought by group.', 'masterstudy-lms-learning-management-system-pro'), get_bloginfo('name'));

            if ($user) {
                STM_LMS_Mails::send_email($subject, $message, $email);
                continue;
            }

            /*Create User*/
            $username = sanitize_title($email);
            $password = wp_generate_password();

            wp_create_user($username, $password, $email);

            $message = sprintf(
                esc_html__('Login: %s; Password: %s; Site URL: %s', 'masterstudy-lms-learning-management-system-pro'),
                $username,
                $password,
                get_bloginfo('url')
            );

            STM_LMS_Mails::send_email($subject, $message, $email);

        }

    }

    function control_group_courses_after_edit($new_emails, $group_id, $old_emails)
    {

        $old_emails = !empty($old_emails) ? explode(',', $old_emails) : array();

        $new_emails_list = array_diff($new_emails, $old_emails);
        $old_emails_list = array_diff($old_emails, $new_emails);


        if (!empty($new_emails_list) or !empty($old_emails_list)) {
            $group_courses = self::get_group_courses($group_id);

            /*Add courses to new user*/
            if (!empty($new_emails_list) and !empty($group_courses)) {

                foreach ($new_emails_list as $new_email) {

                    $user = get_user_by('email', $new_email);

                    if (!$user) continue;

                    $user_id = $user->ID;

                    foreach ($group_courses as $course) {
                        $course_id = $course['course_id'];

                        STM_LMS_Course::add_user_course($course_id, $user_id, 0, 0, false, $group_id);

                        self::course_added_email($user_id, $group_id, $course_id);
                    }
                }

            }

            /*Delete courses to old users*/
            if (!empty($old_emails_list)) {
                foreach ($old_emails_list as $old_email) {
                    $user = get_user_by('email', $old_email);

                    if (!$user) continue;

                    $user_id = $user->ID;

                    global $wpdb;
                    $table = stm_lms_user_courses_name($wpdb);

                    $wpdb->delete(
                        $table,
                        array(
                            'user_id' => $user_id,
                            'enterprise_id' => $group_id
                        )
                    );

                    self::group_removed_email($user_id, $group_id);

                }
            }
        }

    }

    function stm_lms_woocommerce_order_approved($course_data)
    {

        if (!empty($course_data['enterprise_id'])) {
            /*Get Group Members*/
            $group_id = intval($course_data['enterprise_id']);

            $users = STM_LMS_Enterprise_Courses::get_group_users($group_id);
            $admin_id = get_post_meta($group_id, 'author_id', true);

            if (!empty($users)) {
                foreach ($users as $id) {
                    if ($id === $admin_id) continue;

                    STM_LMS_Course::add_user_course($course_data['item_id'], $id, 0, 0, false, $group_id);
                    STM_LMS_Course::add_student($course_data['item_id']);
                }
            }

        }

    }

    function stm_lms_woocommerce_order_cancelled($course_data)
    {
        if (!empty($course_data['enterprise_id'])) {
            $group_id = intval($course_data['enterprise_id']);

            $users = STM_LMS_Enterprise_Courses::get_group_users($group_id);

            if (!empty($users)) {
                foreach ($users as $id) {
                    global $wpdb;
                    $table = stm_lms_user_courses_name($wpdb);

                    $wpdb->delete(
                        $table,
                        array(
                            'user_id' => $id,
                            'course_id' => $course_data['item_id'],
                            'enterprise_id' => $group_id
                        )
                    );
                }
            }
        }
    }

    function stm_lms_import_groups()
    {
        check_ajax_referer('stm_lms_import_groups', 'nonce');

        $res = array();


        $is_valid_image = Validation::is_valid($_FILES, array(
            'file' => 'required_file|extension,csv'
        ));

        if ($is_valid_image !== true) {
            wp_send_json(array(
                'error' => true,
                'message' => esc_html__('Invalid CSV File', 'masterstudy-lms-learning-management-system-pro')
            ));
        }

        $file = $_FILES['file'];

        $csv = array_map('str_getcsv', file($file['tmp_name']));

        $current_user = STM_LMS_User::get_current_user();

        if (empty($current_user['id'])) {
            $res['status'] = 'error';
            $res['message'] = esc_html__('Log in', 'masterstudy-lms-learning-management-system-pro');
            wp_send_json($res);
        };

        $current_user_id = $current_user['id'];

        if (!is_array($csv)) {
            wp_send_json(array(
                'error' => true,
                'message' => esc_html__('Wrong CSV Format', 'masterstudy-lms-learning-management-system-pro')
            ));
        }

        foreach ($csv as $group) {

            if (!is_array($group) && count($group) != 2) continue;

            $group_name = sanitize_text_field($group[0]);

            $group_id = wp_insert_post(array(
                'post_title' => sanitize_text_field($group_name),
                'post_type' => 'stm-ent-groups'
            ));

            //Update Emails
            $limit = self::get_group_limit($group_id);

            $emails_data = (explode('|', sanitize_text_field($group[1])));

            $emails = array();
            if (!empty($emails_data)) {
                foreach ($emails_data as $email) {
                    if ((filter_var($email, FILTER_VALIDATE_EMAIL))) $emails[] = sanitize_email($email);
                }
            }

            $emails = array_splice($emails, 0, $limit);

            if (apply_filters('stm_lms_allow_group_manage', true)) {

                do_action('stm_lms_group_updated', $emails, $group_id, get_post_meta($group_id, 'emails', true));

                update_post_meta($group_id, 'emails', sanitize_text_field(implode(',', $emails)));
                update_post_meta($group_id, 'author_id', $current_user_id);
            }


        }

        wp_send_json('OK');

    }

    /*FILTERS*/
    function enterprise_post_type($posts)
    {

        $posts['stm-ent-groups'] = array(
            'single' => esc_html__('Enterprise Group', 'masterstudy-lms-learning-management-system-pro'),
            'plural' => esc_html__('Enterprise Groups', 'masterstudy-lms-learning-management-system-pro'),
            'args' => array(
                'public' => false,
                'exclude_from_search' => true,
                'publicly_queryable' => false,
                'show_in_menu' => 'admin.php?page=stm-lms-settings',
                'supports' => array('title')
            )
        );

        return $posts;
    }

    function enterprise_stm_lms_boxes($boxes)
    {

        $boxes['stm_enterprise_group'] = array(
            'post_type' => array('stm-ent-groups'),
            'label' => esc_html__('Group Settings', 'masterstudy-lms-learning-management-system-pro'),
        );
        $boxes['stm_enterprise_course'] = array(
            'post_type' => array('stm-courses'),
            'label' => esc_html__('Enterprise Settings', 'masterstudy-lms-learning-management-system-pro'),
        );

        return $boxes;
    }

    function enterprise_stm_lms_fields($fields)
    {

        $fields['stm_enterprise_group'] = array(
            'section_enterprise_group' => array(
                'name' => esc_html__('Curriculum', 'masterstudy-lms-learning-management-system-pro'),
                'fields' => array(
                    'author_id' => array(
                        'type' => 'number',
                        'label' => esc_html__('Author ID', 'masterstudy-lms-learning-management-system-pro'),
                    ),
                    'emails' => array(
                        'type' => 'text',
                        'label' => esc_html__('Emails', 'masterstudy-lms-learning-management-system-pro'),
                    ),
                )
            )
        );

        $fields['stm_enterprise_course'] = array(
            'section_enterprise_group' => array(
                'name' => esc_html__('Enterprise Courses', 'masterstudy-lms-learning-management-system-pro'),
                'fields' => array(
                    'enterprise_price' => array(
                        'type' => 'number',
                        'label' => esc_html__('Enterprise price', 'masterstudy-lms-learning-management-system-pro'),
                    ),
                )
            )
        );

        return $fields;
    }

    function enterprise_stm_lms_post_types($post_types)
    {
        $post_types[] = 'stm-ent-groups';

        return $post_types;
    }

    function after_single_item_cart_title($item)
    {
        $enterprise = '';
        if (!empty($item['enterprise'])) {
            $enterprise = "<span class='enterprise-course-added'> " . sprintf(esc_html__('%sEnterprise%s for group %s', 'masterstudy-lms-learning-management-system-pro'), "<label>", "</label>", "<strong>" . get_the_title($item['enterprise']) . "</strong>") . "</span>";
        }
        echo wp_kses_post($enterprise);
    }

    function cart_items_fields($fields)
    {
        $fields[] = 'enterprise';
        return $fields;
    }

    function delete_from_cart_filter()
    {
        return false;
    }

    function delete_from_cart($user_id)
    {

        $group_id = (!empty($_GET['group_id'])) ? intval($_GET['group_id']) : '';
        $item_id = intval($_GET['item_id']);

        if (!empty($group_id) and !empty($item_id)) {
            global $wpdb;
            $table = stm_lms_user_cart_name($wpdb);

            $wpdb->delete(
                $table,
                array(
                    'user_id' => $user_id,
                    'item_id' => $item_id,
                    'enterprise' => $group_id
                )
            );
        }

        if (empty($group_id)) {
            stm_lms_get_delete_cart_item($user_id, $item_id);
        }

    }

    function stm_lms_accept_order()
    {
        return false;
    }

    static function get_group_users($group_id)
    {

        $users = array();

        $emails = get_post_meta($group_id, 'emails', true);
        $author = get_post_meta($group_id, 'author_id', true);
        if (!empty($emails)) $emails = explode(',', $emails);

        if (!is_array($emails)) $emails = array();

        $author_data = get_userdata($author);
        $emails[] = $author_data->user_email;
        $emails = array_unique($emails);

        foreach ($emails as $email) {
            $user = get_user_by('email', $email);
            if ($user) $users[] = $user->ID;
        }

        return $users;
    }

    function order_accepted($user_id, $cart_items)
    {

        if (!empty($cart_items)) {
            foreach ($cart_items as $cart_item) {
                if (!empty($cart_item['enterprise'])) {
                    /*Get Group Members*/
                    $group_id = intval($cart_item['enterprise']);

                    $users = STM_LMS_Enterprise_Courses::get_group_users($group_id);

                    if (!empty($users)) {
                        foreach ($users as $id) {
                            STM_LMS_Course::add_user_course($cart_item['item_id'], $id, 0, 0, false, $group_id);
                            STM_LMS_Course::add_student($cart_item['item_id']);
                        }
                    }

                } else {
                    STM_LMS_Course::add_user_course($cart_item['item_id'], $user_id, 0, 0);
                    STM_LMS_Course::add_student($cart_item['item_id']);
                }
            }
        }

        /*Delete Cart*/
        stm_lms_get_delete_cart_items($user_id);
    }

    function order_removed($course_id, $cart_item)
    {

        if (!empty($cart_item['enterprise'])) {
            $group_id = intval($cart_item['enterprise']);

            $users = STM_LMS_Enterprise_Courses::get_group_users($group_id);

            if (!empty($users)) {
                foreach ($users as $id) {
                    global $wpdb;
                    $table = stm_lms_user_courses_name($wpdb);

                    $wpdb->delete(
                        $table,
                        array(
                            'user_id' => $id,
                            'course_id' => $course_id,
                            'enterprise_id' => $group_id
                        )
                    );
                }
            }
        }

    }

    function button($template_name, $vars)
    {
        switch ($template_name) {
            case('/stm-lms-templates/global/buy-button.php'):
                $template_name = '/stm-lms-templates/global/buy-button/mixed.php';
                break;
            default :
                break;
        }
        return $template_name;
    }

    function woo_cart_group_name($title, $cart_item, $cart_item_key)
    {
        if (!empty($cart_item['enterprise_id'])) {
            $group_id = $cart_item['enterprise_id'];

            $sub_title = "<span class='product-enterprise-group'>" . sprintf(esc_html__('Enterprise for %s', 'masterstudy-lms-learning-management-system-pro'), get_the_title($group_id)) . "</span>";

            $title .= $sub_title;
        }
        return $title;
    }

    function stm_lms_before_create_order($order_meta, $cart_item)
    {

        if (!empty($cart_item['enterprise_id'])) $order_meta['enterprise_id'] = $cart_item['enterprise_id'];

        return $order_meta;
    }

    /*Single Group*/
    function get_enterprise_group()
    {

        check_ajax_referer('stm_lms_get_enterprise_group', 'nonce');

        $group_id = intval($_GET['group_id']);
        $current_user = STM_LMS_User::get_current_user();
        if (empty($current_user) or empty($group_id)) die;
        $user_id = $current_user['id'];

        $r = array(
            'users' => array()
        );

        if (!STM_LMS_Enterprise_Courses::is_group_admin($user_id, $group_id)) die;

        $group_users = STM_LMS_Enterprise_Courses::get_group_users($group_id);

        foreach ($group_users as $group_user_id) {
            if ($group_user_id === $user_id) continue;
            $r['users'][] = STM_LMS_User::get_current_user($group_user_id);
        }

        wp_send_json($r);

    }

    function get_group_courses($group_id)
    {
        $author_id = get_post_meta($group_id, 'author_id', true);

        global $wpdb;
        $table = stm_lms_user_courses_name($wpdb);

        $request = "SELECT course_id FROM {$table}
			WHERE
			user_ID = {$author_id} AND
			enterprise_id = {$group_id}";

        return $wpdb->get_results($request, ARRAY_A);


    }

    function get_user_ent_courses()
    {

        check_ajax_referer('stm_lms_get_user_ent_courses', 'nonce');

        $group_id = intval($_GET['group_id']);
        $user_id = intval($_GET['user_id']);

        $r = array();

        $group_courses = self::get_group_courses($group_id);

        foreach ($group_courses as $group_course) {
            $course_id = $group_course['course_id'];
            $user_course = self::get_user_group_course($user_id, $course_id, $group_id);
            if (!empty($user_course)) $user_course = $user_course[0];

            $added = (!empty($user_course)) ? true : false;

            $course_data = array(
                'course_id' => $course_id,
                'group_id' => $group_id,
                'user_data' => $user_course,
                'data' => array(
                    'title' => get_the_title($course_id)
                ),
                'added' => $added
            );

            $r[] = $course_data;

        }

        wp_send_json($r);

    }

    function get_user_group_course($user_id, $course_id, $group_id)
    {
        global $wpdb;
        $table = stm_lms_user_courses_name($wpdb);

        $request = "SELECT * FROM {$table}
			WHERE
			user_ID = {$user_id} AND
			course_id = {$course_id} AND
			enterprise_id = {$group_id}";

        return $wpdb->get_results($request, ARRAY_A);
    }

    function delete_user_ent_courses($args = array())
    {
        if (empty($args['group_id']) and empty($args['course_id']) and empty($args['user_id'])) {
            check_ajax_referer('stm_lms_delete_user_ent_courses', 'nonce');

            $group_id = intval($_GET['group_id']);
            $course_id = intval($_GET['course_id']);
            $user_id = intval($_GET['user_id']);
        } else {
            $group_id = intval($args['group_id']);
            $course_id = intval($args['course_id']);
            $user_id = intval($args['user_id']);
        }

        $current_user = STM_LMS_User::get_current_user();
        $current_user_id = $current_user['id'];

        $is_admin = STM_LMS_Enterprise_Courses::is_group_admin($current_user_id, $group_id);
        $group_users = STM_LMS_Enterprise_Courses::get_group_users($group_id);
        if (!$is_admin || !in_array($user_id, $group_users)) die;

        global $wpdb;
        $table = stm_lms_user_courses_name($wpdb);

        if (apply_filters('stm_lms_allow_group_manage', true)) {
            $wpdb->delete(
                $table,
                array(
                    'user_id' => $user_id,
                    'course_id' => $course_id,
                    'enterprise_id' => $group_id
                )
            );
        }

        self::course_removed_email($user_id, $group_id, $course_id);

        wp_send_json('OK');
    }

    function add_user_ent_courses()
    {
        check_ajax_referer('stm_lms_add_user_ent_courses', 'nonce');

        $group_id = intval($_GET['group_id']);
        $course_id = intval($_GET['course_id']);
        $user_id = intval($_GET['user_id']);

        $current_user = STM_LMS_User::get_current_user();
        $current_user_id = $current_user['id'];

        $is_admin = STM_LMS_Enterprise_Courses::is_group_admin($current_user_id, $group_id);
        $group_users = STM_LMS_Enterprise_Courses::get_group_users($group_id);
        if (!$is_admin || !in_array($user_id, $group_users)) die;

        if (apply_filters('stm_lms_allow_group_manage', true)) {
            STM_LMS_Course::add_user_course($course_id, $user_id, 0, 0, false, $group_id);

            self::course_added_email($user_id, $group_id, $course_id);
        }

        wp_send_json('OK');
    }

    function change_ent_group_admin()
    {

        check_ajax_referer('stm_lms_change_ent_group_admin', 'nonce');

        $user_id = intval($_GET['user_id']);
        $group_id = intval($_GET['group_id']);

        $current_user = STM_LMS_User::get_current_user();
        $current_user_id = $current_user['id'];

        $is_admin = STM_LMS_Enterprise_Courses::is_group_admin($current_user_id, $group_id);

        if (!$is_admin) die;

        if (apply_filters('stm_lms_allow_group_manage', true)) {
            update_post_meta($group_id, 'author_id', $user_id);
        }

        wp_send_json(STM_LMS_Enterprise_Courses::groups_url());
    }

    function delete_user_from_group()
    {

        check_ajax_referer('stm_lms_delete_user_from_group', 'nonce');

        $user_id = intval($_GET['user_id']);
        $user_email = sanitize_text_field($_GET['user_email']);
        $group_id = intval($_GET['group_id']);

        $current_user = STM_LMS_User::get_current_user();
        $current_user_id = $current_user['id'];

        $is_admin = STM_LMS_Enterprise_Courses::is_group_admin($current_user_id, $group_id);

        if (!$is_admin) die;

        $emails = get_post_meta($group_id, 'emails', true);
        $emails = (!empty($emails)) ? explode(',', $emails) : array();

        if (($key = array_search($user_email, $emails)) !== false) {
            unset($emails[$key]);
        }

        /*Delete User Group Courses*/

        global $wpdb;
        $table = stm_lms_user_courses_name($wpdb);

        if (apply_filters('stm_lms_allow_group_manage', true)) {
            $wpdb->delete(
                $table,
                array(
                    'user_id' => $user_id,
                    'enterprise_id' => $group_id
                )
            );

            update_post_meta($group_id, 'emails', implode(',', $emails));
        }

        self::group_removed_email($user_id, $group_id);

        wp_send_json('OK');
    }

    /*EMAILs*/
    function course_added_email($user_id, $group_id, $course_id)
    {

        $blog_name = get_bloginfo('name');

        $group_name = get_the_title($group_id);

        $course_title = get_the_title($course_id);

        $admin_id = get_post_meta($group_id, 'author_id', true);
        $admin = STM_LMS_User::get_current_user($admin_id);

        $user = STM_LMS_User::get_current_user($user_id);

        $user_url = STM_LMS_User::user_page_url($user_id);

        $email = $user['email'];
        $subject = sprintf(esc_html__("Hello %s", 'masterstudy-lms-learning-management-system-pro'), $user['login']);
        $message = sprintf(__(
            "<p>{$admin['login']} invited you to a group {$group_name} on {$blog_name} and You were added to the {$course_title} course.</p>
                
                <a href='{$user_url}' target='_blank'>My Account</a>

                <p>Thanks for your time,</p>
                <p>The {$blog_name} Team</p>
        ", 'masterstudy-lms-learning-management-system-pro'));


        STM_LMS_Helpers::send_email($email, $subject, $message);


    }

    function course_removed_email($user_id, $group_id, $course_id)
    {

        $blog_name = get_bloginfo('name');

        $course_title = get_the_title($course_id);

        $admin_id = get_post_meta($group_id, 'author_id', true);
        $admin = STM_LMS_User::get_current_user($admin_id);

        $user = STM_LMS_User::get_current_user($user_id);

        $email = $user['email'];
        $subject = sprintf(esc_html__("Hello %s", 'masterstudy-lms-learning-management-system-pro'), $user['login']);
        $message = sprintf(__(
            "<p>{$admin['login']} removed you from the {$course_title} course.</p>
                
                <p>Thanks for your time,</p>
                <p>The {$blog_name} Team</p>
        ", 'masterstudy-lms-learning-management-system-pro'));


        STM_LMS_Helpers::send_email($email, $subject, $message);


    }

    function group_removed_email($user_id, $group_id)
    {
        $blog_name = get_bloginfo('name');

        $group_name = get_the_title($group_id);

        $admin_id = get_post_meta($group_id, 'author_id', true);
        $admin = STM_LMS_User::get_current_user($admin_id);

        $user = STM_LMS_User::get_current_user($user_id);

        $email = $user['email'];
        $subject = sprintf(esc_html__("Hello %s", 'masterstudy-lms-learning-management-system-pro'), $user['login']);
        $message = sprintf(__(
            "<p>{$admin['login']} removed you from the {$group_name} group.</p>
                
                <p>Thanks for your time,</p>
                <p>The {$blog_name} Team</p>
        ", 'masterstudy-lms-learning-management-system-pro'));


        STM_LMS_Helpers::send_email($email, $subject, $message);
    }


}