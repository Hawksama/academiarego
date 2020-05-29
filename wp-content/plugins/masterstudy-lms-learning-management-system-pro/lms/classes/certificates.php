<?php

new STM_LMS_Certificates;

class STM_LMS_Certificates
{

    function __construct()
    {
        add_action('vc_after_init', array($this, 'vc_module'));

        add_action('wp_ajax_stm_lms_check_certificate_code', array($this, 'check_code'));
        add_action('wp_ajax_nopriv_stm_lms_check_certificate_code', array($this, 'check_code'));

        add_filter('stm_lms_after_category_field', array($this, 'add_category'));
    }

    static function stm_lms_certificate_code($user_course_id, $course_id) {
        return "lmsx{$user_course_id}x{$course_id}";
    }

    function vc_module() {
        vc_map(array(
            'name' => esc_html__('STM Certificate Checker', 'masterstudy'),
            'base' => 'stm_lms_certificate_checker',
            'icon' => 'stm_lms_certificate_checker',
            'description' => esc_html__('Certificate Checker', 'masterstudy'),
            'html_template' => STM_LMS_PRO_PATH . '/stm-lms-templates/certificate/checker.php',
            'category' => array(
                esc_html__('Content', 'masterstudy'),
            ),
            'params' => array(
                array(
                    'type' => 'textfield',
                    'heading' => __('Title', 'masterstudy'),
                    'param_name' => 'title',
                ),
            )
        ));
    }

    function check_code() {
        check_ajax_referer('stm_lms_check_certificate_code', 'nonce');

        $r = array(
            'status' => 'error',
            'message' => esc_html__('Enter valid code', 'masterstudy-lms-learning-management-system-pro')
        );

        $code = sanitize_text_field($_GET['code']);

        if(empty($code)) wp_send_json($r);

        $code = self::parse_code($code);

        if(empty($code)) wp_send_json($r);

        global $wpdb;
        $table = stm_lms_user_courses_name($wpdb);

        $fields = (empty($fields)) ? '*' : implode(',', $fields);

        $request = "SELECT {$fields} FROM {$table}
			WHERE
			course_id = {$code[1]} AND
			user_course_id = {$code[0]}";

        $certificate = STM_LMS_Helpers::simplify_db_array($wpdb->get_results($request, ARRAY_A));

        if(empty($certificate)) {
            $r['message'] = esc_html__('Sorry, Certificate not found', 'masterstudy-lms-learning-management-system-pro');
        } else {

            $passing_grade = intval(STM_LMS_Options::get_option('certificate_threshold', 70));
            $user_grade = intval($certificate['progress_percent']);

            if ($user_grade < $passing_grade) {
                $r['message'] = esc_html__('Sorry, Certificate not found', 'masterstudy-lms-learning-management-system-pro');
            } else {
                $user = STM_LMS_User::get_current_user($certificate['user_id']);
                $r['status'] = 'success';
                $r['message'] = sprintf(
                    esc_html__('Certificate is valid. Course "%s" finished by %s', 'masterstudy-lms-learning-management-system-pro'),
                    get_the_title($certificate['course_id']),
                    $user['login']
                );
            }

        }

        wp_send_json($r);

    }

    static function parse_code($code) {
        $code = str_replace('lmsx', '', $code);

        if(empty($code)) return '';

        $code = explode('x', $code);

        if(count($code) !== 2) return '';

        return $code;
    }

    function add_category() {
        $enabled = STM_LMS_Options::get_option('course_allow_new_categories', false);

        if($enabled) STM_LMS_Templates::show_lms_template('manage_course/parts/panel_info/add_new_category');
    }

}

if (class_exists('WPBakeryShortCode')) {
    class WPBakeryShortCode_Stm_Lms_Certificate_Checker extends WPBakeryShortCode
    {
    }
}