<?php
new STM_LMS_Courses;

class STM_LMS_Courses
{

    public function __construct()
    {
        add_filter('stm_lms_archive_filter_args', array($this, 'filter'));
    }

    static function filter_enabled()
    {
        return STM_LMS_Options::get_option('enable_courses_filter', '');
    }

    function filter($args)
    {

        $this->filter_categories($args);
        $this->filter_statuses($args);
        $this->filter_level($args);
        $this->filter_rating($args);
        $this->filter_instructor($args);
        $this->filter_price($args);

        return $args;
    }

    function filter_categories(&$args)
    {

        if (!empty($_GET['category'])) {

            $categories = array();

            foreach ($_GET['category'] as $category) {
                $categories[] = intval($category);
            }

            if (empty($args['tax_query'])) $args['tax_query'] = array();

            $args['tax_query']['category'] = array(
                'relation' => 'AND',
                array(
                    'taxonomy' => 'stm_lms_course_taxonomy',
                    'field' => 'term_id',
                    'terms' => $categories,
                ),
            );

            if (!empty($_GET['subcategory'])) {

                $subcategories = array();

                foreach ($_GET['subcategory'] as $subcategory) {
                    $subcategories[] = intval($subcategory);
                }

                if (empty($args['tax_query'])) $args['tax_query'] = array();
                if (empty($args['tax_query']['category'])) $args['tax_query']['category'] = array();

                $args['tax_query']['category'][] = array(
                    'taxonomy' => 'stm_lms_course_taxonomy',
                    'field' => 'term_id',
                    'terms' => $subcategories,
                );

            }

        }

    }

    function filter_statuses(&$args)
    {

        if (!empty($_GET['status']) && is_array($_GET['status'])) {

            if (empty($args['meta_query'])) $args['meta_query'] = array(
                'relation' => 'AND',
                'status' => array(
                    'relation' => 'OR'
                )
            );

            if (in_array('featured', $_GET['status'])) {
                $args['meta_query']['status'][] = array(
                    'key' => 'featured',
                    'value' => 'on',
                    'compare' => '=',
                );
            }

            if (in_array('hot', $_GET['status'])) {
                $args['meta_query']['status'][] = array(
                    'key' => 'status',
                    'value' => 'hot',
                    'compare' => '=',
                );
            }

            if (in_array('new', $_GET['status'])) {
                $args['meta_query']['status'][] = array(
                    'key' => 'status',
                    'value' => 'new',
                    'compare' => '=',
                );
            }

            if (in_array('special', $_GET['status'])) {
                $args['meta_query']['status'][] = array(
                    'key' => 'status',
                    'value' => 'special',
                    'compare' => '=',
                );
            }

        }

    }

    function filter_level(&$args)
    {

        if (!empty($_GET['levels']) && is_array($_GET['levels'])) {

            if (empty($args['meta_query'])) $args['meta_query'] = array(
                'relation' => 'AND',
                'skill_level' => array(
                    'relation' => 'OR'
                )
            );

            if (in_array('beginner', $_GET['levels'])) {
                $args['meta_query']['skill_level'][] = array(
                    'key' => 'skill_level',
                    'value' => 'beginner',
                    'compare' => '=',
                );
            }

            if (in_array('intermediate', $_GET['levels'])) {
                $args['meta_query']['skill_level'][] = array(
                    'key' => 'skill_level',
                    'value' => 'intermediate',
                    'compare' => '=',
                );
            }

            if (in_array('advanced', $_GET['levels'])) {
                $args['meta_query']['skill_level'][] = array(
                    'key' => 'skill_level',
                    'value' => 'advanced',
                    'compare' => '=',
                );
            }

        }

    }

    function filter_rating(&$args)
    {
        if (!empty($_GET['rating'])) {

            if (empty($args['meta_query'])) $args['meta_query'] = array(
                'relation' => 'AND'
            );

            $args['meta_query'][] = array(
                'key' => 'course_mark_average',
                'value' => floatval($_GET['rating']),
                'compare' => '>=',
            );

        }
    }

    function filter_instructor(&$args)
    {
        if (!empty($_GET['instructor'])) {

            $authors = array();

            foreach($_GET['instructor'] as $instructor) $authors[] = intval($instructor);

            $args['author__in'] = $authors;

        }
    }

    function filter_price(&$args)
    {
        if (!empty($_GET['price'])) {

            if (empty($args['meta_query'])) $args['meta_query'] = array(
                'relation' => 'OR'
            );

            if (in_array('free_courses', $_GET['price']) && in_array('paid_courses', $_GET['price'])) {
                $args['meta_query']['prices'][] = array(
                    array(
                        'relation' => 'AND',
                        array(
                            'key' => 'price',
                            'compare' => 'EXISTS',
                        ),
                        array(
                            'relation' => 'OR',
                            array(
                                'key' => 'not_single_sale',
                                'value' => 'on',
                                'compare' => '!='
                            ),
                            array(
                                'key' => 'not_single_sale',
                                'compare' => 'NOT EXISTS',
                            ),
                        )
                    )
                );
            } else {
                if (in_array('free_courses', $_GET['price'])) {
                    $args['meta_query']['free_price'][] = array(
                        array(
                            'relation' => 'AND',
                            array(
                                'relation' => 'OR',
                                array(
                                    'key' => 'price',
                                    'value' => '',
                                    'compare' => '=',
                                ),
                                array(
                                    'key' => 'price',
                                    'compare' => 'NOT EXISTS',
                                ),
                            ),
                            array(
                                'relation' => 'OR',
                                array(
                                    'key' => 'not_single_sale',
                                    'value' => 'on',
                                    'compare' => '!='
                                ),
                                array(
                                    'key' => 'not_single_sale',
                                    'compare' => 'NOT EXISTS',
                                ),
                            )
                        ),
                    );
                }

                if (in_array('paid_courses', $_GET['price'])) {
                    $args['meta_query']['paid_price'][] = array(
                        array(
                            'relation' => 'AND',
                            array(
                                'key' => 'price',
                                'value' => 0,
                                'compare' => '>',
                            ),
                            array(
                                'relation' => 'OR',
                                array(
                                    'key' => 'not_single_sale',
                                    'value' => 'on',
                                    'compare' => '!='
                                ),
                                array(
                                    'key' => 'not_single_sale',
                                    'compare' => 'NOT EXISTS',
                                ),
                            )
                        ),
                    );
                }
            }

            if (in_array('subscription', $_GET['price'])) {
                $args['meta_query']['subscription'][] = array(
                    array(
                        'key' => 'not_single_sale',
                        'value' => 'on',
                        'compare' => '=',
                    ),
                );
            }

        }
    }

}