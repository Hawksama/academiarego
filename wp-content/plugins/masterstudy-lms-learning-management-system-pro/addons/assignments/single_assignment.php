<?php
new STM_LMS_Single_Assignment;

class STM_LMS_Single_Assignment
{

    function __construct()
    {
        add_action('wp_ajax_stm_lms_get_user_assingments', array($this, 'stm_lms_get_user_assingments'));
    }

    static function user_assignment_url($id) {
        return home_url('/') . STM_LMS_WP_Router::route_urls('user_assignment') . '/' . $id;
    }

    static function per_page()
    {
        return apply_filters('stm_lms_single_assignment_per_page', 20);
    }

    static function get_user_assignments($assignment_id, $args = array())
    {
        $default_args = array(
            'post_type' => 'stm-user-assignment',
            'post_status' => array('pending'),
            'posts_per_page' => self::per_page(),
            'meta_query' => array(
                'relation' => 'AND',
                array(
                    'key' => 'assignment_id',
                    'value' => $assignment_id,
                    'compare' => '='
                ),
            )
        );

        if(!empty($args['meta_query'])) $args['meta_query'] = wp_parse_args($args['meta_query'], $default_args['meta_query']);

        $args = wp_parse_args($args, $default_args);

        $r = array(
            'assignments' => array(),
        );

        $q = new WP_Query($args);
        if ($q->have_posts()) {
            while ($q->have_posts()) {
                $q->the_post();
                $id = get_the_ID();

                $status = get_post_status();
                if($status !== 'pending') $status = get_post_meta($id, 'status', true);

                $start_time = get_post_meta($id, 'start_time', true);
                if(!empty($start_time)) $start_time = date('j M Y', $start_time / 1000);

                $r['assignments'][] = array(
                    'id' => $id,
                    'url' => self::user_assignment_url($id),
                    'user' => STM_LMS_User::get_current_user(get_post_meta($id, 'student_id', true)),
                    'try_num' => get_post_meta($id, 'try_num', true),
                    'start_time' => $start_time,
                    'end_time' => get_post_meta($id, 'end_time', true),
                    'status' => $status,
                    'course_name' => get_the_title(get_post_meta($id, 'course_id', true))
                );

            }
        }

        $r['pages'] = ceil($q->found_posts/self::per_page());

        return $r;

    }

    function stm_lms_get_user_assingments() {

        check_ajax_referer('stm_lms_get_user_assingments', 'nonce');

        $assignment_id = intval($_GET['id']);
        $status = sanitize_text_field($_GET['status']);

        $page = intval($_GET['page']);
        $per_page = self::per_page();

        $args = array();

        $args['posts_per_page'] = $per_page;
        $args['offset'] = ($page * $per_page) - $per_page;

        if($status === 'not_passed') {
            $args['post_status'] = 'publish';
            $args['meta_query'][] = array(
                'key' => 'status',
                'value' => 'not_passed',
                'compare' => '=',
            );
        }

        if($status === 'passed') {
            $args['post_status'] = 'publish';
            $args['meta_query'][] = array(
                'key' => 'status',
                'value' => 'passed',
                'compare' => '=',
            );
        }

        $assignments = self::get_user_assignments($assignment_id, $args);

        wp_send_json($assignments);
    }

}