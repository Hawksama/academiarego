<?php

STM_LMS_Manage_Course::init();

class STM_LMS_Manage_Course
{

    public static function init()
    {
        add_action('wp_ajax_stm_lms_pro_upload_image', 'STM_LMS_Manage_Course::upload_image');

        add_action('wp_ajax_stm_lms_pro_get_image_data', 'STM_LMS_Manage_Course::get_image');

        add_action('wp_ajax_stm_lms_pro_save_quiz', 'STM_LMS_Manage_Course::save_quiz');

        add_action('wp_ajax_stm_lms_pro_save_lesson', 'STM_LMS_Manage_Course::save_lesson');

        add_action('wp_ajax_stm_lms_pro_save_front_course', 'STM_LMS_Manage_Course::save_course');

        add_action('stm_lms_pro_course_data_validated', 'STM_LMS_Manage_Course::stm_lms_pro_course_data_check_user', 10, 2);
    }

    public static function manage_course_url()
    {
        return esc_url(home_url('/') . 'lms-manage');
    }

    public static function i18n()
    {
        return array(
            'title' => esc_html__('Your Course title here...', 'masterstudy-lms-learning-management-system-pro'),
            'title_label' => esc_html__('Course title', 'masterstudy-lms-learning-management-system-pro'),
            'category' => esc_html__('Choose category', 'masterstudy-lms-learning-management-system-pro'),
        );
    }

    public static function localize_script($course_id)
    {
        $localize = array();
        $localize['i18n'] = STM_LMS_Manage_Course::i18n();
        $localize['post_id'] = $course_id;
        $localize['course_file_pack_data'] = stm_lms_course_files_data();
        $localize['lesson_file_pack_data'] = stm_lms_lesson_files_data();
        if (!empty($course_id)) {
            $localize['post_data'] = array(
                'title' => get_the_title($course_id),
                'post_id' => $course_id,
                'content' => get_post_field('post_content', $course_id),
                'image' => get_post_thumbnail_id($course_id)
            );

            $meta = STM_LMS_Helpers::simplify_meta_array(get_post_meta($course_id));
            if (!empty($meta)) $localize['post_data'] = array_merge($localize['post_data'], $meta);

            /*Category*/
            $terms = wp_get_post_terms($course_id, 'stm_lms_course_taxonomy');

            if (!is_wp_error($terms) and !empty($terms)) {
                $terms = wp_list_pluck($terms, 'term_id');
                $localize['post_data']['category'] = $terms[0];
            }

            if (!empty($meta['co_instructor']) and class_exists('STM_LMS_Multi_Instructors')) {
                $localize['post_data']['co_instructor'] = get_user_by('ID', $meta['co_instructor']);
                $localize['post_data']['co_instructor']->data->lms_data = STM_LMS_User::get_current_user($meta['co_instructor']);
            }

            if(!empty($meta['course_files_pack'])) {
                $localize['post_data']['course_files_pack'] = json_decode($meta['course_files_pack']);
            }

        }

        apply_filters('stm_lms_localize_manage_course', $localize, $course_id);

        $r = '';

        if (!empty($course_id)) $r = 'var stm_lms_manage_course_id = ' . $course_id . '; ';

        $r .= 'var stm_lms_manage_course = ' . json_encode($localize);


        return $r;

    }

    public static function get_terms($taxonomy = '', $args = array('parent' => 0), $add_childs = true)
    {

        $terms = get_terms($taxonomy, $args);

        $select = array(
            '' => esc_html__('Choose category', 'masterstudy-lms-learning-management-system-pro')
        );

        foreach ($terms as $term) {
            $select[$term->term_id] = $term->name;

            if ($add_childs) {
                $term_children = get_term_children($term->term_id, $taxonomy);

                foreach ($term_children as $term_child_id) {
                    $term_child = get_term_by('id', $term_child_id, $taxonomy);
                    $select[$term_child_id] = "- {$term_child->name}";
                }
            }

        }

        return $select;
    }

    public static function get_image()
    {

        check_ajax_referer('stm_lms_pro_get_image_data', 'nonce');

        $image_id = intval($_GET['image_id']);

        $image = wp_get_attachment_image_src($image_id, 'img-870-440');

        wp_send_json($image[0]);
    }

    public static function upload_image()
    {

        check_ajax_referer('stm_lms_pro_upload_image', 'nonce');

        $is_valid_image = Validation::is_valid($_FILES, array(
            'image' => 'required_file|extension,png;jpg;jpeg'
        ));

        if ($is_valid_image !== true) {
            wp_send_json(array(
                'error' => true,
                'message' => $is_valid_image[0]
            ));
        }


        require_once(ABSPATH . 'wp-admin/includes/image.php');
        require_once(ABSPATH . 'wp-admin/includes/file.php');
        require_once(ABSPATH . 'wp-admin/includes/media.php');


        $attachment_id = media_handle_upload('image', 0);

        if (is_wp_error($attachment_id)) {
            wp_send_json(array(
                'error' => true,
                'message' => $attachment_id->get_error_message()
            ));
        }

        $image = wp_get_attachment_image_src($attachment_id, 'img-870-440');

        wp_send_json(array(
            'files' => $_FILES,
            'id' => $attachment_id,
            'url' => $image[0],
            'error' => 'false',
        ));

        die;
    }

    public static function save_quiz()
    {

        check_ajax_referer('stm_lms_pro_save_quiz', 'nonce');

        $post_id = intval($_GET['post_id']);
        $post_title = sanitize_text_field($_GET['post_title']);
        $content = wp_kses_post($_GET['content']);

        if (!empty($post_id) and !empty($post_title) and isset($content)) {
            $post = array(
                'ID' => $post_id,
                'post_content' => $content,
            );

            wp_update_post($post);
        }

        if (isset($_GET['lesson_excerpt'])) {
            update_post_meta($post_id, 'lesson_excerpt', wp_kses_post($_GET['lesson_excerpt']));
        }

        if (isset($_GET['questions'])) {
            update_post_meta($post_id, 'questions', wp_kses_post($_GET['questions']));
        }

        if (isset($_GET['duration'])) {
            update_post_meta($post_id, 'duration', wp_kses_post($_GET['duration']));
        }

        if (isset($_GET['duration_measure'])) {
            update_post_meta($post_id, 'duration_measure', wp_kses_post($_GET['duration_measure']));
        }

        if (isset($_GET['correct_answer'])) {
            $value = ($_GET['correct_answer'] === 'true') ? 'on' : '';
            update_post_meta($post_id, 'correct_answer', $value);
        }

        if (isset($_GET['passing_grade'])) {
            update_post_meta($post_id, 'passing_grade', wp_kses_post($_GET['passing_grade']));
        }

        if (isset($_GET['re_take_cut'])) {
            update_post_meta($post_id, 're_take_cut', wp_kses_post($_GET['re_take_cut']));
        }

        wp_send_json('Saved');

    }

    public static function save_lesson()
    {

        check_ajax_referer('stm_lms_pro_save_lesson', 'nonce');

        $post_id = intval($_POST['post_id']);
        $post_title = sanitize_text_field($_POST['post_title']);
        $allowed_tags = stm_lms_pro_allowed_html();
        $content = wp_kses($_POST['content'], $allowed_tags);

        do_action('stm_lms_pro_before_save_lesson');


        if (!empty($_FILES)) {
            $is_valid_image = Validation::is_valid($_FILES, array(
                'image' => 'required_file|extension,png;jpg;jpeg'
            ));

            if ($is_valid_image) {
                require_once(ABSPATH . 'wp-admin/includes/image.php');
                require_once(ABSPATH . 'wp-admin/includes/file.php');
                require_once(ABSPATH . 'wp-admin/includes/media.php');


                $attachment_id = media_handle_upload('image', 0);

                update_post_meta($post_id, 'lesson_video_poster', $attachment_id);
            }
        }

        if (!empty($post_id) and !empty($post_title) and isset($content)) {

            kses_remove_filters();

            $post = array(
                'ID' => $post_id,
                'post_content' => $content,
            );

            wp_update_post($post);

            kses_init_filters();
        }

        if (isset($_POST['lesson_video_url'])) {
            update_post_meta($post_id, 'lesson_video_url', wp_kses_post($_POST['lesson_video_url']));
        }

        if (isset($_POST['lesson_files_pack'])) {
            update_post_meta($post_id, 'lesson_files_pack', wp_kses_post($_POST['lesson_files_pack']));
        }

        if (isset($_POST['lesson_excerpt'])) {
            update_post_meta($post_id, 'lesson_excerpt', wp_kses_post($_POST['lesson_excerpt']));
        }

        if (isset($_POST['type'])) {
            update_post_meta($post_id, 'type', wp_kses_post($_POST['type']));
        }

        if (isset($_POST['duration'])) {
            update_post_meta($post_id, 'duration', wp_kses_post($_POST['duration']));
        }

        if (isset($_POST['stream_start_date'])) {
            update_post_meta($post_id, 'stream_start_date', wp_kses_post($_POST['stream_start_date']));
        }

        if (isset($_POST['stream_start_time'])) {
            update_post_meta($post_id, 'stream_start_time', wp_kses_post($_POST['stream_start_time']));
        }

        if (isset($_POST['lesson_lock_from_start'])) {
            update_post_meta($post_id, 'lesson_lock_from_start', sanitize_text_field($_POST['lesson_lock_from_start']));
        }

        if (isset($_POST['lesson_start_date'])) {
            update_post_meta($post_id, 'lesson_start_date', wp_kses_post($_POST['lesson_start_date']));
        }

        if (isset($_POST['lesson_start_time'])) {
            update_post_meta($post_id, 'lesson_start_time', wp_kses_post($_POST['lesson_start_time']));
        }

        if (isset($_POST['lesson_lock_start_days'])) {
            update_post_meta($post_id, 'lesson_lock_start_days', sanitize_text_field($_POST['lesson_lock_start_days']));
        }

        if (isset($_POST['stream_end_date'])) {
            update_post_meta($post_id, 'stream_end_date', wp_kses_post($_POST['stream_end_date']));
        }

        if (isset($_POST['stream_end_time'])) {
            update_post_meta($post_id, 'stream_end_time', wp_kses_post($_POST['stream_end_time']));
        }

        if (!empty($_POST['timezone'])) {
            update_post_meta($post_id, 'timezone', wp_kses_post($_POST['timezone']));
        }

        if (isset($_POST['preview'])) {
            $value = ($_POST['preview'] === 'true') ? 'on' : '';
            update_post_meta($post_id, 'preview', $value);
        }

        if (isset($_POST['zoom_password'])) {
            update_post_meta($post_id, 'zoom_password', sanitize_text_field($_POST['zoom_password']));
        }

        if (isset($_POST['join_before_host'])) {
            $value = ($_POST['join_before_host'] === 'true') ? 'on' : '';
            update_post_meta($post_id, 'join_before_host', $value);
        }

        if (isset($_POST['option_host_video'])) {
            $value = ($_POST['option_host_video'] === 'true') ? 'on' : '';
            update_post_meta($post_id, 'option_host_video', $value);
        }

        if (isset($_POST['option_participants_video'])) {
            $value = ($_POST['option_participants_video'] === 'true') ? 'on' : '';
            update_post_meta($post_id, 'option_participants_video', $value);
        }

        if (isset($_POST['option_mute_participants'])) {
            $value = ($_POST['option_mute_participants'] === 'true') ? 'on' : '';
            update_post_meta($post_id, 'option_mute_participants', $value);
        }

        if (isset($_POST['option_enforce_login'])) {
            $value = ($_POST['option_enforce_login'] === 'true') ? 'on' : '';
            update_post_meta($post_id, 'option_enforce_login', $value);
        }

        do_action('stm_lms_save_lesson_after_validation', $post_id, $_POST);

        wp_send_json('Saved');

    }

    public static function save_course()
    {

        check_ajax_referer('stm_lms_pro_save_front_course', 'nonce');

        $validation = new Validation();

        $required_fields = apply_filters('stm_lms_manage_course_required_fields', array(
            'title' => 'required',
            'category' => 'required',
            'image' => 'required|integer',
            'content' => 'required',
            'price' => 'float',
            'curriculum' => 'required',
        ));

        $validation->validation_rules($required_fields);

        $validation->filter_rules(array(
            'title' => 'trim|sanitize_string',
            'category' => 'trim|sanitize_string',
            'image' => 'sanitize_numbers',
            'content' => 'trim',
            'price' => 'sanitize_floats',
            'sale_price' => 'sanitize_floats',
            'curriculum' => 'sanitize_string',
            'duration' => 'sanitize_string',
            'video' => 'sanitize_string',
            'prerequisites' => 'sanitize_string',
            'prerequisite_passing_level' => 'sanitize_floats',
            'enterprise_price' => 'sanitize_floats',
            'co_instructor' => 'sanitize_floats',
        ));

        $validated_data = $validation->run($_POST);

        if ($validated_data === false) {
            wp_send_json(array(
                'status' => 'error',
                'message' => $validation->get_readable_errors(true)
            ));
        }

        $user = STM_LMS_User::get_current_user();

        do_action('stm_lms_pro_course_data_validated', $validated_data, $user);

        $course_id = STM_LMS_Manage_Course::create_course($validated_data, $user);

        STM_LMS_Manage_Course::update_course_meta($course_id, $validated_data);

        STM_LMS_Manage_Course::update_course_category($course_id, $validated_data);

        STM_LMS_Manage_Course::update_course_image($course_id, $validated_data);

        do_action('stm_lms_pro_course_added', $validated_data, $course_id);

        $course_url = get_the_permalink($course_id);


        wp_send_json(array(
            'status' => 'success',
            'message' => esc_html__('Course Saved, redirecting...', 'masterstudy-lms-learning-management-system-pro'),
            'url' => $course_url
        ));

    }

    public static function create_course($data, $user)
    {

        $premoderation = STM_LMS_Options::get_option('course_premoderation', false);

        $post_status = ($premoderation) ? 'pending' : 'publish';

        $post = array(
            'post_type' => 'stm-courses',
            'post_title' => $data['title'],
            'post_content' => $data['content'],
            'post_status' => $post_status,
            'post_author' => $user['id']
        );

        if (!empty($data['post_id'])) {
            $post['ID'] = $data['post_id'];
            $post['post_author'] = intval(get_post_field('post_author', $data['post_id']));
        }

        kses_remove_filters();
        $r = wp_insert_post($post);
        kses_init_filters();
        return $r;
    }

    public static function update_course_meta($course_id, $data)
    {
        /*Update Course Post Meta*/
        $post_metas = array(
            'price',
            'sale_price',
            'curriculum',
            'faq',
            'announcement',
            'duration_info',
            'level',
            'prerequisites',
            'prerequisite_passing_level',
            'enterprise_price',
            'co_instructor',
            'course_files_pack',
        );

        foreach ($post_metas as $post_meta_key) {
            if (isset($data[$post_meta_key])) {
                update_post_meta($course_id, $post_meta_key, $data[$post_meta_key]);
            }
        }

    }

    public static function update_course_category($course_id, $data)
    {

        $category = $data['category'];
        $add_new = empty(intval($category));

        $parent = (!empty($data['parent_category'])) ? intval($data['parent_category']) : 0;

        if ($add_new) {
            $term = wp_insert_term($category, 'stm_lms_course_taxonomy', compact('parent'));
            $data['category'] = $term['term_id'];
        }

        wp_set_post_terms($course_id, $data['category'], 'stm_lms_course_taxonomy');
    }

    public static function update_course_image($course_id, $data)
    {
        set_post_thumbnail($course_id, $data['image']);
    }

    public static function stm_lms_pro_course_data_check_user($data, $user)
    {

        if (empty($user['id'])) {
            wp_send_json(array(
                'status' => 'error',
                'message' => esc_html__('Please log-in', 'masterstudy-lms-learning-management-system-pro')
            ));
        }

        /*Check author*/
        if (!empty($data['post_id'])) {
            $authors = array();
            $authors[] = intval(get_post_field('post_author', $data['post_id']));
            $authors[] = get_post_meta($data['post_id'], 'co_instructor', true);

            if (!in_array($user['id'], $authors)) {
                wp_send_json(array(
                    'status' => 'error',
                    'message' => esc_html__('It is not your course.', 'masterstudy-lms-learning-management-system-pro')
                ));
            }

        }

    }

}