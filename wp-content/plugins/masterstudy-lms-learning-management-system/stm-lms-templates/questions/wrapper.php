<?php
/**
 * @var string $type
 * @var array $stm_lms_vars
 * @var array $answers
 * @var string $question
 * @var string $question_explanation
 * @var string $question_hint
 * @var string $item_id
 * @var string $last_answers
 * @var string $question_index
 */

if (!empty($last_answers)) $stm_lms_vars['last_answers'] = $last_answers;
if (!empty($item_id)) $stm_lms_vars['item_id'] = $item_id;
$question_template = (STM_LMS_Quiz::show_answers($item_id)) ? 'questions/answers/' . $type : 'questions/' . $type;
$number = (!empty($number)) ? $number : 1;

?>
<div class="stm-lms-single_question stm-lms-single_question_<?php echo esc_attr($type); ?>"
     data-number-lessons="<?php echo intval($number) ?>">

    <?php if ($type !== 'question_bank'): ?>

        <div class="stm-lms-single_question_text">
            <h3><?php the_title(); ?></h3>
            <div>
                <?php the_content(); ?>
            </div>
        </div>


        <?php if (!empty($question_explanation) and STM_LMS_Quiz::show_answers($item_id)): ?>
            <div class="stm-lms-single_question_explanation">
                <?php echo sanitize_text_field($question_explanation); ?>
            </div>
        <?php endif; ?>

    <?php endif; ?>

    <div class="heading_font">
        <?php STM_LMS_Templates::show_lms_template($question_template, $stm_lms_vars); ?>
    </div>

</div>