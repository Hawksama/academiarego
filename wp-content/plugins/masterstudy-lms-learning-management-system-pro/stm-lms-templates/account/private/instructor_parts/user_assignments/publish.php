<?php
/**
 * @var $assignment_id
 * @var $assignment
 */

$editor_comment = get_post_meta($assignment_id, 'editor_comment', true);
?>

<div class="editor_comment">

    <?php echo wp_kses_post($editor_comment); ?>

    <div class="assignment_status_wrapper ">

        <?php if ($assignment['status'] === 'passed'): ?>
            <span class="assignment_status">
                <?php esc_html_e('Approved', 'masterstudy-lms-learning-management-system-pro'); ?>
            </span>
        <?php endif; ?>

        <?php if ($assignment['status'] === 'not_passed'): ?>
            <span class="assignment_status">
                <?php esc_html_e('Rejected', 'masterstudy-lms-learning-management-system-pro'); ?>
            </span>
        <?php endif; ?>

    </div>
</div>