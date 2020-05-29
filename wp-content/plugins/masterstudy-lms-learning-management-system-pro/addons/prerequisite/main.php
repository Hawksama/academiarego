<?php

new STM_LMS_Prerequisites;

class STM_LMS_Prerequisites
{

    function __construct()
    {
        add_filter('stm_wpcfto_boxes', array($this, 'prerequisites_boxes'));
        add_filter('stm_wpcfto_fields', array($this, 'prerequisites_fields'));

        add_filter('stm_lms_pro_show_button', array($this, 'is_prerequisite'), 100, 2);

        add_action('stm_lms_pro_instead_buttons', array($this, 'instead_buy_buttons'));
    }

    function prerequisites_boxes($boxes)
    {
        $boxes['stm_courses_prereq'] = array(
            'post_type' => array('stm-courses'),
            'label' => esc_html__('Course Prerequisites', 'masterstudy-lms-learning-management-system-pro'),
        );
        return $boxes;
    }

    function prerequisites_fields($fields)
    {
        $fields['stm_courses_prereq'] = array(
            'section_prereqs' => array(
                'name' => esc_html__('Curriculum', 'masterstudy-lms-learning-management-system-pro'),
                'fields' => array(
                    'prerequisites' => array(
                        'type' => 'autocomplete',
                        'post_type' => array('stm-courses'),
                        'label' => esc_html__('Prerequisite Courses', 'masterstudy-lms-learning-management-system-pro'),
                    ),
                    'prerequisite_passing_level' => array(
                        'type' => 'text',
                        'label' => esc_html__('Prerequisite Courses Passing Percent (%)', 'masterstudy-lms-learning-management-system-pro'),
                    ),
                )
            )
        );

        return $fields;
    }

    public static function get_prereq_courses($course_id)
    {
        return get_post_meta($course_id, 'prerequisites', true);
    }

    public static function prereq_passed($prereq, $course_id)
    {
        if (empty($prereq)) return true;
        $prereq = explode(',', $prereq);
        $passing_value = get_post_meta($course_id, 'prerequisite_passing_level', true);
        $passing_value = (!empty($passing_value)) ? $passing_value : 0;
        $user = STM_LMS_User::get_current_user();
        $user_id = (!empty($user['id'])) ? $user['id'] : 0;

        foreach ($prereq as $course) {
            $user_course = STM_LMS_Helpers::simplify_db_array(stm_lms_get_user_course($user_id, $course, array('progress_percent')));
            $progress = (!empty($user_course['progress_percent'])) ? $user_course['progress_percent'] : 0;
            if ($progress < $passing_value) return false;
        }

        return true;
    }

    public static function is_prerequisite($show, $course_id)
    {
        $prereq = self::get_prereq_courses($course_id);
        $passed = self::prereq_passed($prereq, $course_id);

        return $passed;
    }

    function instead_buy_buttons($course_id)
    {
        if (!$this->is_prerequisite(true, $course_id)) {
            $prereq = explode(',', $this->get_prereq_courses($course_id));
            $user = STM_LMS_User::get_current_user();
            $user_id = (!empty($user['id'])) ? $user['id'] : 0;

            $user_courses = array();

            foreach ($prereq as $course) {
                $user_course = STM_LMS_Helpers::simplify_db_array(stm_lms_get_user_course($user_id, $course, array('course_id', 'progress_percent')));
                $user_courses[] = (!empty($user_course)) ? $user_course : array(
                    'course_id' => $course,
                    'progress_percent' => 0
                );
            }

            STM_LMS_Templates::show_lms_template('global/prerequisite', array('courses' => $user_courses));
        }
    }

}