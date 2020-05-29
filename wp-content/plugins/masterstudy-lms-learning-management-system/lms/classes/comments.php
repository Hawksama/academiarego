<?php

STM_LMS_Comments::init();

class STM_LMS_Comments
{

    public static function init()
    {
        add_action('wp_ajax_stm_lms_add_comment', 'STM_LMS_Comments::add_comment');

        add_action('wp_ajax_stm_lms_get_comments', 'STM_LMS_Comments::get_comments');
    }

    public static function add_comment()
    {

        check_ajax_referer('stm_lms_add_comment', 'nonce');

        if (empty($_GET['post_id'])) die;
        $lesson_id = intval($_GET['post_id']);

        $current_user = STM_LMS_User::get_current_user();
        if (empty($current_user['id'])) die;

        $comment = (!empty($_GET['comment'])) ? wp_kses_post($_GET['comment']) : '';
        $parent = (!empty($_GET['parent'])) ? intval($_GET['parent']) : 0;

        $r = self::_add_comment($current_user, $lesson_id, $comment, $parent);

        wp_send_json($r);
    }

    public static function _add_comment($user, $lesson_id, $comment, $parent)
    {
        $r = array(
            'error' => false,
            'status' => 'success',
            'message' => esc_html__('Your comment was added.', 'masterstudy-lms-learning-management-system'),
        );

        if (empty($comment)) {
            $r = array(
                'error' => true,
                'status' => 'error',
                'message' => esc_html__('Please, write a comment.', 'masterstudy-lms-learning-management-system')
            );
        }

        if (!$r['error']) {
            /*Add comment*/
            $time = current_time('mysql');

            $data = array(
                'comment_post_ID' => $lesson_id,
                'comment_author' => $user['login'],
                'comment_author_email' => $user['email'],
                'comment_content' => $comment,
                'comment_parent' => $parent,
                'user_id' => $user['id'],
                'comment_date' => $time,
                'comment_approved' => 1,
            );

            $comment = @wp_new_comment($data, true);

            $comment = get_comment($comment);

            if (empty($comment->comment_ID)) {
                return (array(
                    'error' => true,
                    'status' => 'error',
                    'message' => esc_html__('You already added this comment', 'masterstudy-lms-learning-management-system')
                ));
            }

            $r['comment'] = array(
                'comment_ID' => $comment->comment_ID,
                'content' => wp_kses_post($comment->comment_content),
                'author' => STM_LMS_User::get_current_user($comment->user_id),
                'datetime' => stm_lms_time_elapsed_string(get_gmt_from_date($comment->comment_date)),
                'replies_count' => sprintf(_n(
                    '%s reply',
                    '%s replies',
                    0,
                    'masterstudy-lms-learning-management-system'
                ), 0),
                'replies' => array()
            );


            /*Send message to instructor*/
            $author_data = get_userdata(get_post_field('post_author', $lesson_id));
            $subject = esc_html__('New comment on lesson', 'masterstudy-lms-learning-management-system');
            $message = sprintf(
                esc_html__('%s commented - "%s" on lesson %s', 'masterstudy-lms-learning-management-system'),
                $user['login'],
                wp_kses_post($comment->comment_content),
                get_the_title($lesson_id)
            );

            STM_LMS_Helpers::send_email($author_data->user_email, $subject, $message);
        }

        return $r;
    }


    public static function get_comments()
    {

        check_ajax_referer('stm_lms_get_comments', 'nonce');

        if (empty($_GET['post_id'])) die;
        $lesson_id = intval($_GET['post_id']);

        $current_user = STM_LMS_User::get_current_user();
        if (empty($current_user['id'])) die;
        $user_id = $current_user['id'];

        $offset = (!empty($_GET['offset'])) ? intval($_GET['offset']) : 0;
        $search = (!empty($_GET['search'])) ? sanitize_text_field($_GET['search']) : '';
        $author__in = (!empty($_GET['user_comments']) and $_GET['user_comments']) ? $user_id : '';

        $r = self::_get_user_comments($user_id, $lesson_id, $offset, $search, $author__in);

        wp_send_json($r);
    }

    public static function _get_user_comments($user_id, $lesson_id, $offset, $search, $author__in)
    {
        $r = array(
            'posts' => array()
        );

        $pp = get_option('posts_per_page');

        $offset = $offset * $pp;

        $args = array(
            'post_id' => $lesson_id,
            'number' => $pp,
            'offset' => $offset,
            'search' => $search,
            'parent' => 0
        );

        if (!empty($author__in)) $args['author__in'] = $user_id;

        $comments_query = new WP_Comment_Query;
        $comments = $comments_query->query($args);

        $r['args'] = $args;

        if ($comments) {
            foreach ($comments as $comment) {

                /*Get answers*/
                $args = array(
                    'post_id' => $lesson_id,
                    'number' => 5,
                    'parent' => $comment->comment_ID
                );

                $replies_query = new WP_Comment_Query;
                $replies = $replies_query->query($args);

                $post = array(
                    'comment_ID' => $comment->comment_ID,
                    'content' => $comment->comment_content,
                    'author' => STM_LMS_User::get_current_user($comment->user_id),
                    'datetime' => stm_lms_time_elapsed_string(get_gmt_from_date($comment->comment_date)),
                    'replies_count' => sprintf(_n(
                        '%s reply',
                        '%s replies',
                        STM_LMS_Comments::comment_replies_count($comment->comment_ID),
                        'masterstudy-lms-learning-management-system'
                    ), STM_LMS_Comments::comment_replies_count($comment->comment_ID)),
                    'replies' => array()
                );

                if (!empty($replies)) {
                    foreach ($replies as $reply) {
                        $post['replies'][] = array(
                            'comment_ID' => $reply->comment_ID,
                            'content' => $reply->comment_content,
                            'author' => STM_LMS_User::get_current_user($reply->user_id),
                            'datetime' => stm_lms_time_elapsed_string(get_gmt_from_date($reply->comment_date)),
                        );
                    }
                }

                $r['posts'][] = $post;
            }
        }

        return $r;
    }

    public static function comment_replies_count($id)
    {
        global $wpdb;
        $query = "SELECT COUNT(comment_post_id) AS count FROM $wpdb->comments WHERE `comment_approved` = 1 AND `comment_parent` = $id";
        $parents = $wpdb->get_row($query);
        return $parents->count;
    }

}