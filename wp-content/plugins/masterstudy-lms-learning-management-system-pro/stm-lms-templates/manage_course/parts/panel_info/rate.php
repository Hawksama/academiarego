<?php
$average = $percent = $reviews = 0;
?>

<div class="average-rating-stars">
    <div class="average-rating-stars__top">
        <div class="star-rating"
             title="<?php printf(esc_html__('Rated %s out of 5', 'masterstudy-lms-learning-management-system-pro'), $average); ?>">
        <span style="width:<?php echo esc_attr($percent); ?>%">
            <strong class="rating"><?php echo esc_attr($average); ?></strong>
            <?php esc_html_e('out of 5', 'masterstudy-lms-learning-management-system-pro'); ?>
        </span>
        </div>
        <div class="average-rating-stars__av heading_font"><?php echo floatval($average); ?></div>
    </div>

    <div class="average-rating-stars__reviews">
        <?php printf(_n(
            '%s review',
            '%s reviews',
            $reviews,
            'masterstudy-lms-learning-management-system-pro'
        ), $reviews); ?>
    </div>

</div>
