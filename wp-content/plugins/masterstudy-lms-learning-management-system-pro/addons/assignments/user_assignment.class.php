<?php
new STM_LMS_User_Assignment;

class STM_LMS_User_Assignment
{

    function __construct()
    {
        add_action('wp_ajax_stm_lms_edit_user_answer', array($this, 'stm_lms_edit_user_answer'));

        add_filter('stm_lms_course_passed_items', array($this, 'essay_passed'), 10, 3);

        add_filter('stm_lms_curriculum_item_status', array($this, 'item_status'), 10, 5);
    }

    static function is_my_assignment($assignment_id, $author_id) {

        $editor_id = intval(get_post_field('post_author', get_post_meta($assignment_id, 'assignment_id', true)));

        return $editor_id === $author_id;

    }

    static function get_assignment($assignment_id) {
        $editor_id = STM_LMS_User::get_current_user();

        if(empty($editor_id)) die;
        $editor_id = $editor_id['id'];

        if(!self::is_my_assignment($assignment_id, $editor_id)) {
            STM_LMS_User::js_redirect(STM_LMS_Instructor_Assignments::assignments_url());
            die;
        };

        $args = array(
            'post_type' => 'stm-user-assignment',
            'post_status' => array('pending', 'publish'),
            'post__in' => array($assignment_id)
        );

        $q = new WP_Query($args);

        $r = array();

        if ($q->have_posts()) {
            while ($q->have_posts()) {
                $q->the_post();

                $status = get_post_status();
                if($status !== 'pending') $status = get_post_meta($assignment_id, 'status', true);

                $r['title'] = get_the_title();
                $r['status'] = $status;
                $r['content'] = get_the_content();
                $r['assignment_title'] = get_the_title(get_post_meta($assignment_id, 'assignment_id', true));
                $r['files'] = STM_LMS_Assignments::get_draft_attachments($assignment_id);
            }
        }

        wp_reset_postdata();

        return $r;

    }

    static function get_file_icon($file) {
        $file_path = get_attached_file($file->ID);
        $file_ext = pathinfo($file_path, PATHINFO_EXTENSION);

        if($file_ext === 'zip') {
            return 'file-archive';
        } else if($file_ext === 'jpg' || $file_ext === 'png' || $file_ext === 'gif') {
            return 'file-image';
        } else if($file_ext === 'ppt' || $file_ext === 'pptx') {
            return 'file-powerpoint';
        } else if($file_ext === 'xls' || $file_ext === 'xlsx') {
            return 'file-excel';
        } else if($file_ext === 'psd') {
            return 'adobe';
        } else if($file_ext === 'pdf') {
            return 'file-pdf';
        } else if($file_ext === 'mp3' || $file_ext === 'ogg' || $file_ext === 'wav') {
            return 'file-audio';
        } else {
            return 'file';
        }
    }

    function stm_lms_edit_user_answer() {

        check_ajax_referer('stm_lms_edit_user_answer', 'nonce');

        $status = ($_POST['status'] === 'approve') ? 'passed' : 'not_passed';
        $assignment_id = intval($_POST['assignment_id']);
        $comment = wp_kses_post($_POST['content']);

        if(get_post_status($assignment_id) !== 'pending') die;

        $student_id = get_post_meta($assignment_id, 'student_id', true);
        $course_id = get_post_meta($assignment_id, 'course_id', true);

        wp_update_post(array(
            'ID' => $assignment_id,
            'post_status' => 'publish',
        ));

        update_post_meta($assignment_id, 'editor_comment', $comment);
        update_post_meta($assignment_id, 'status', $status);

        if($status === 'passed') {
            STM_LMS_Course::update_course_progress($student_id, $course_id);
        }

        $student = STM_LMS_User::get_current_user($student_id);

        $message = esc_html__('Your assignment was checked', 'masterstudy-lms-learning-management-system-pro');
        STM_LMS_Helpers::send_email($student['email'], esc_html__('Assignment status change.', 'masterstudy-lms-learning-management-system-pro'), $message);

        do_action('stm_lms_assignment_' . $status, $student_id, $assignment_id);

        wp_send_json('OK');

    }

    function essay_passed($passed_items, $curriculum, $user_id) {
        $curriculum = STM_LMS_Helpers::only_array_numbers($curriculum);

        foreach($curriculum as $item) {
            if(get_post_type($item) !== 'stm-assignments') continue;

            if(self::assignment_passed($user_id, $item)) $passed_items++;

        }

        return $passed_items;
    }

    function assignment_passed($user_id, $assignment_id) {
        $args = array(
            'post_type' => 'stm-user-assignment',
            'posts_per_page' => 1,
            'post_status' => array('publish'),
            'meta_query' => array(
                'relation' => 'AND',
                array(
                    'key' => 'assignment_id',
                    'value' => $assignment_id,
                    'compare' => '='
                ),
                array(
                    'key' => 'student_id',
                    'value' => $user_id,
                    'compare' => '='
                ),
                array(
                    'key' => 'status',
                    'value' => 'passed',
                    'compare' => '='
                ),
            )
        );

        $q = new WP_Query($args);

        return $q->found_posts;
    }

    function item_status($html, $prev_status, $post_id, $item_id, $user_id) {

        if(get_post_type($item_id) !== 'stm-assignments') return $html;

        if(self::assignment_passed($user_id, $item_id)) $html = str_replace('item__completed', 'item__completed completed', $html);

        return $html;
    }

}