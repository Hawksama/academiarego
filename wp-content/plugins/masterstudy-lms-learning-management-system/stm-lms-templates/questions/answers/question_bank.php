<?php
/**
 * @var string $type
 * @var array $answers
 * @var string $question
 * @var string $question_explanation
 * @var string $question_hint
 */
$question_id = get_the_ID();

if (!empty($answers[0]) && !empty($answers[0]['categories']) && !empty($answers[0]['number'])) :?>
    <div class="stm_lms_question_bank">
        <?php $number = $answers[0]['number'];
        $categories = wp_list_pluck($answers[0]['categories'], 'slug');

        $questions = get_post_meta($item_id, 'questions', true);
        $questions = (!empty($questions)) ? explode(',', $questions) : array();

        $args = array(
            'post_type' => 'stm-questions',
            'posts_per_page' => $number,
            'post__not_in' => $questions,
            'tax_query' => array(
                array(
                    'taxonomy' => 'stm_lms_question_taxonomy',
                    'field' => 'slug',
                    'terms' => $categories,
                ),
            )
        );

        $q = new WP_Query($args);

        if ($q->have_posts()) {
            while ($q->have_posts()) {
                $q->the_post();

                $question_id = get_the_ID();
                $question_itself = get_the_title();
                $question = STM_LMS_Helpers::parse_meta_field($question_id);

                $number = $q->found_posts - 1;

                if (empty($question['type'])) $question['type'] = 'single_choice';
                $question['user_answer'] = (!empty($last_answers[$question_id])) ? $last_answers[$question_id] : array();
                if (!empty($question['type']) and !empty($question['answers']) and !empty($question_itself)) {
                    STM_LMS_Templates::show_lms_template('questions/wrapper', array_merge($question, compact('item_id', 'last_answers', 'number')));
                }

            }
        }

        wp_reset_postdata();

        ?>
    </div>


<?php endif;