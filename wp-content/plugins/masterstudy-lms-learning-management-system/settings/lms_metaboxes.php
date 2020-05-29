<?php

add_filter('stm_wpcfto_boxes', function ($boxes) {

    $data_boxes = array(
        'stm_courses_curriculum' => array(
            'post_type' => array('stm-courses'),
            'label' => esc_html__('Course curriculum', 'masterstudy-lms-learning-management-system'),
        ),
        'stm_courses_settings' => array(
            'post_type' => array('stm-courses'),
            'label' => esc_html__('Course Settings', 'masterstudy-lms-learning-management-system'),
        ),
        'stm_lesson_settings' => array(
            'post_type' => array('stm-lessons'),
            'label' => esc_html__('Lesson Settings', 'masterstudy-lms-learning-management-system'),
        ),
        'stm_quiz_questions' => array(
            'post_type' => array('stm-quizzes'),
            'label' => esc_html__('Quiz Questions', 'masterstudy-lms-learning-management-system'),
        ),
        'stm_quiz_settings' => array(
            'post_type' => array('stm-quizzes'),
            'label' => esc_html__('Quiz Settings', 'masterstudy-lms-learning-management-system'),
        ),
        'stm_question_settings' => array(
            'post_type' => array('stm-questions'),
            'label' => esc_html__('Question Settings', 'masterstudy-lms-learning-management-system'),
        ),
        'stm_reviews' => array(
            'post_type' => array('stm-reviews'),
            'label' => esc_html__('Review info', 'masterstudy-lms-learning-management-system'),
        ),
        'stm_order_info' => array(
            'post_type' => array('stm-orders'),
            'label' => esc_html__('Order info', 'masterstudy-lms-learning-management-system'),
            'skip_post_type' => 1
        ),
    );

    $boxes = array_merge($data_boxes, $boxes);

    return $boxes;
});

add_filter('stm_wpcfto_fields', function ($fields) {

    $users = STM_Metaboxes::get_users();

    $courses = (class_exists('STM_LMS_Settings')) ? STM_LMS_Settings::stm_get_post_type_array('stm-courses') : array();

    $data_fields = array(
        'stm_courses_curriculum' => array(
            'section_curriculum' => array(
                'name' => esc_html__('Curriculum', 'masterstudy-lms-learning-management-system'),
                'fields' => array(
                    'curriculum' => array(
                        'type' => 'post_type_repeat',
                        'post_type' => apply_filters('stm_lms_curriculum_post_types', array('stm-lessons', 'stm-quizzes', 'stm-assignments')),
                        'sanitize' => 'stm_lms_sanitize_curriculum'
                    ),
                )
            )
        ),
        'stm_courses_settings' => array(
            'section_settings' => array(
                'name' => esc_html__('Settings', 'masterstudy-lms-learning-management-system'),
                'fields' => array(
                    'featured' => array(
                        'type' => 'checkbox',
                        'label' => esc_html__('Featured Course', 'masterstudy-lms-learning-management-system'),
                    ),
                    'views' => array(
                        'type' => 'number',
                        'label' => esc_html__('Course Views', 'masterstudy-lms-learning-management-system'),
                        'sanitize' => 'stm_lms_save_number'
                    ),
                    'level' => array(
                        'type' => 'select',
                        'label' => esc_html__('Course Level', 'masterstudy-lms-learning-management-system'),
                        'options' => array(
                            'beginner' => esc_html__('Beginner', 'masterstudy-lms-learning-management-system'),
                            'intermediate' => esc_html__('Intermediate', 'masterstudy-lms-learning-management-system'),
                            'advanced' => esc_html__('Advanced', 'masterstudy-lms-learning-management-system'),
                        )
                    ),
                    'current_students' => array(
                        'type' => 'number',
                        'label' => esc_html__('Current students', 'masterstudy-lms-learning-management-system'),
                        'sanitize' => 'stm_lms_save_number'
                    ),
//						'featured_course'   => array(
//							'type'  => 'checkbox',
//							'label' => esc_html__('Featured Course', 'masterstudy-lms-learning-management-system'),
//						),
//						'external_buy_link' => array(
//							'type'  => 'text',
//							'label' => esc_html__('External Buy link', 'masterstudy-lms-learning-management-system'),
//						),
                    'duration_info' => array(
                        'type' => 'text',
                        'label' => esc_html__('Duration info', 'masterstudy-lms-learning-management-system'),
                    ),
                    'video_duration' => array(
                        'type' => 'text',
                        'label' => esc_html__('Video Duration', 'masterstudy-lms-learning-management-system'),
                    ),
                    'skill_level' => array(
                        'type' => 'select',
                        'label' => esc_html__('Skill level', 'masterstudy-lms-learning-management-system'),
                        'options' => array(
                            '' => esc_html__('No skill required', 'masterstudy-lms-learning-management-system'),
                            'beginner' => esc_html__('Beginner', 'masterstudy-lms-learning-management-system'),
                            'medium' => esc_html__('Medium', 'masterstudy-lms-learning-management-system'),
                            'advanced' => esc_html__('Advanced', 'masterstudy-lms-learning-management-system'),
                        )
                    ),
                    'retake' => array(
                        'type' => 'checkbox',
                        'label' => esc_html__('Retake Course', 'masterstudy-lms-learning-management-system'),
                    ),
                    'status' => array(
                        'type' => 'select',
                        'label' => esc_html__('Status', 'masterstudy-lms-learning-management-system'),
                        'options' => array(
                            '' => esc_html__('No status', 'masterstudy-lms-learning-management-system'),
                            'hot' => esc_html__('Hot', 'masterstudy-lms-learning-management-system'),
                            'new' => esc_html__('New', 'masterstudy-lms-learning-management-system'),
                            'special' => esc_html__('Special', 'masterstudy-lms-learning-management-system'),
                        )
                    ),
                    'status_dates' => array(
                        'type' => 'dates',
                        'label' => esc_html__('Status Dates', 'masterstudy-lms-learning-management-system'),
                        'sanitize' => 'stm_lms_save_dates',
                        'dependency' => array(
                            'key' => 'status',
                            'value' => 'not_empty'
                        )
                    ),
                    'course_files_pack' => stm_lms_course_files_data()
                )
            ),
            'section_accessibility' => array(
                'name' => esc_html__('Accessibility', 'masterstudy-lms-learning-management-system'),
                'fields' => array(
                    'not_single_sale' => array(
                        'type' => 'checkbox',
                        'label' => esc_html__('Disable one-time purchase (Courses will be available only for subscription plans)', 'masterstudy-lms-learning-management-system'),
                    ),
                    'price' => array(
                        'type' => 'number',
                        'label' => esc_html__('Course Price (leave blank to make the course free)', 'masterstudy-lms-learning-management-system'),
                        'sanitize' => 'stm_lms_save_number',
                        'step' => '0.01'
                    ),
                    'sale_price' => array(
                        'type' => 'number',
                        'label' => esc_html__('Sale Price', 'masterstudy-lms-learning-management-system'),
                        'sanitize' => 'stm_lms_save_number',
                        'step' => '0.01'
                    ),
                    'sale_price_dates' => array(
                        'type' => 'dates',
                        'label' => esc_html__('Sale Price Dates', 'masterstudy-lms-learning-management-system'),
                        'sanitize' => 'stm_lms_save_dates',
                        'dependency' => array(
                            'key' => 'sale_price',
                            'value' => 'not_empty'
                        ),
                        'pro' => true,
                    ),
                    'not_membership' => array(
                        'type' => 'checkbox',
                        'label' => esc_html__('Not included in membership', 'masterstudy-lms-learning-management-system'),
                    ),
                    'affiliate_course' => array(
                        'type' => 'checkbox',
                        'label' => esc_html__('Affiliate course', 'masterstudy-lms-learning-management-system'),
                        'pro' => true,
                    ),
                    'affiliate_course_text' => array(
                        'type' => 'text',
                        'label' => esc_html__('Button Text', 'masterstudy-lms-learning-management-system'),
                        'dependency' => array(
                            'key' => 'affiliate_course',
                            'value' => 'not_empty'
                        ),
                        'pro' => true,
                    ),
                    'affiliate_course_link' => array(
                        'type' => 'text',
                        'label' => esc_html__('Button Link', 'masterstudy-lms-learning-management-system'),
                        'dependency' => array(
                            'key' => 'affiliate_course',
                            'value' => 'not_empty'
                        ),
                        'pro' => true,
                    ),
                )
            ),
            'section_announcement' => array(
                'name' => esc_html__('Announcement', 'masterstudy-lms-learning-management-system'),
                'fields' => array(
                    'announcement' => array(
                        'type' => 'editor',
                        'label' => esc_html__('Announcement', 'masterstudy-lms-learning-management-system'),
                    ),
                )
            ),
            'section_faq' => array(
                'name' => esc_html__('FAQ', 'masterstudy-lms-learning-management-system'),
                'fields' => array(
                    'faq' => array(
                        'type' => 'faq',
                        'label' => esc_html__('FAQ', 'masterstudy-lms-learning-management-system'),
                    ),
                )
            ),
        ),
        'stm_lesson_settings' => array(
            'section_lesson_settings' => array(
                'name' => esc_html__('Lesson Settings', 'masterstudy-lms-learning-management-system'),
                'fields' => array(
                    'type' => array(
                        'type' => 'select',
                        'label' => esc_html__('Lesson type', 'masterstudy-lms-learning-management-system'),
                        'options' => array(
                            'text' => esc_html__('Text', 'masterstudy-lms-learning-management-system'),
                            'video' => esc_html__('Video', 'masterstudy-lms-learning-management-system'),
                            'slide' => esc_html__('Slide', 'masterstudy-lms-learning-management-system'),
                        ),
                        'value' => 'text'
                    ),
                    'duration' => array(
                        'type' => 'text',
                        'label' => esc_html__('Lesson duration', 'masterstudy-lms-learning-management-system'),
                    ),
                    'preview' => array(
                        'type' => 'checkbox',
                        'label' => esc_html__('Lesson preview', 'masterstudy-lms-learning-management-system'),
                    ),
                    'lesson_excerpt' => array(
                        'type' => 'editor',
                        'label' => esc_html__('Lesson Frontend description', 'masterstudy-lms-learning-management-system'),
                    ),
                    'lesson_video_poster' => array(
                        'type' => 'image',
                        'label' => esc_html__('Lesson video poster', 'masterstudy-lms-learning-management-system'),
                    ),
                    'lesson_video_url' => array(
                        'type' => 'text',
                        'label' => esc_html__('Lesson video URL', 'masterstudy-lms-learning-management-system'),
                    ),
                    'lesson_files_pack' => stm_lms_lesson_files_data()
                )
            )
        ),
        'stm_quiz_questions' => array(
            'section_questions' => array(
                'name' => esc_html__('Questions', 'masterstudy-lms-learning-management-system'),
                'fields' => array(
                    'questions' => array(
                        'type' => 'questions',
                        'label' => esc_html__('Questions', 'masterstudy-lms-learning-management-system'),
                        'post_type' => array('stm-questions')
                    ),
                )
            )
        ),
        'stm_quiz_settings' => array(
            'section_quiz_settings' => array(
                'name' => esc_html__('Quiz Settings', 'masterstudy-lms-learning-management-system'),
                'fields' => array(
                    'lesson_excerpt' => array(
                        'type' => 'editor',
                        'label' => esc_html__('Quiz Frontend description', 'masterstudy-lms-learning-management-system'),
                    ),
                    'duration' => array(
                        'type' => 'duration',
                        'label' => esc_html__('Quiz duration', 'masterstudy-lms-learning-management-system'),
                    ),
                    'duration_measure' => array(
                        'type' => 'not_exist',
                    ),
                    'correct_answer' => array(
                        'type' => 'checkbox',
                        'label' => esc_html__('Show correct answer', 'masterstudy-lms-learning-management-system'),
                    ),
                    'passing_grade' => array(
                        'type' => 'number',
                        'label' => esc_html__('Passing grade (%)', 'masterstudy-lms-learning-management-system'),
                    ),
                    're_take_cut' => array(
                        'type' => 'number',
                        'label' => esc_html__('Points total cut after re-take (%)', 'masterstudy-lms-learning-management-system'),
                    ),
                    'random_questions' => array(
                        'type' => 'checkbox',
                        'label' => esc_html__('Randomize questions', 'masterstudy-lms-learning-management-system'),
                    ),
                )
            )
        ),
        'stm_question_settings' => array(
            'section_question_settings' => array(
                'name' => esc_html__('Question Settings', 'masterstudy-lms-learning-management-system'),
                'fields' => array(
                    'type' => array(
                        'type' => 'select',
                        'label' => esc_html__('Question type', 'masterstudy-lms-learning-management-system'),
                        'options' => array(
                            'single_choice' => esc_html__('Single choice', 'masterstudy-lms-learning-management-system'),
                            'multi_choice' => esc_html__('Multi choice', 'masterstudy-lms-learning-management-system'),
                            'true_false' => esc_html__('True or False', 'masterstudy-lms-learning-management-system'),
                            'item_match' => esc_html__('Item Match', 'masterstudy-lms-learning-management-system'),
                            'keywords' => esc_html__('Keywords', 'masterstudy-lms-learning-management-system'),
                            'fill_the_gap' => esc_html__('Fill the Gap', 'masterstudy-lms-learning-management-system'),
                        ),
                        'value' => 'single_choice'
                    ),
                    'answers' => array(
                        'type' => 'answers',
                        'label' => esc_html__('Answers', 'masterstudy-lms-learning-management-system'),
                        'requirements' => 'type'
                    ),
                    'question_explanation' => array(
                        'type' => 'textarea',
                        'label' => esc_html__('Question result explanation', 'masterstudy-lms-learning-management-system'),
                    ),
                )
            )
        ),
        'stm_reviews' => array(
            'section_data' => array(
                'name' => esc_html__('Review info', 'masterstudy-lms-learning-management-system'),
                'fields' => array(
                    'review_course' => array(
                        'type' => 'select',
                        'label' => esc_html__('Course Reviewed', 'masterstudy-lms-learning-management-system'),
                        'options' => $courses,
                    ),
                    'review_user' => array(
                        'type' => 'select',
                        'label' => esc_html__('User Reviewed', 'masterstudy-lms-learning-management-system'),
                        'options' => $users,
                    ),
                    'review_mark' => array(
                        'type' => 'select',
                        'label' => esc_html__('User Review mark', 'masterstudy-lms-learning-management-system'),
                        'options' => array(
                            '5' => '5',
                            '4' => '4',
                            '3' => '3',
                            '2' => '2',
                            '1' => '1'
                        )
                    ),
                )
            )
        ),
        'stm_order_info' => array(
            'order_info' => array(
                'name' => esc_html__('Order', 'masterstudy-lms-learning-management-system'),
                'fields' => array(
                    'order' => array(
                        'type' => 'order',
                    ),
                )
            )
        ),
    );

    $fields = array_merge($data_fields, $fields);

    return $fields;
});