<?php
/**
 * @var $item_id
 * @var $content
 */

$q = new WP_Query(array(
    'posts_per_page' => 1,
    'post_type' => 'stm-assignments',
    'post__in' => array($item_id)
));

?>

<a href="#" class="all_requirements">
    <span><?php esc_html_e('All Requirements', 'masterstudy-lms-learning-management-system-pro'); ?></span>
</a>

<div class="clearfix assignment-task">
    <?php if ($q->have_posts()): ?>
        <?php while ($q->have_posts()): $q->the_post(); ?>

            <?php the_content(); ?>

        <?php endwhile; ?>

    <?php endif; ?>
</div>