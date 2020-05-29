<?php

$rates = STM_LMS_Course_Bundle::get_bundle_rating(get_the_ID());

if (!empty($rates['count'])):
    $average = round($rates['average'] / $rates['count'], 2);
    $percent = round($rates['percent'] / $rates['count'], 2);

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
                $rates['count'],
                'masterstudy-lms-learning-management-system-pro'
            ), $rates['count']); ?>
        </div>

    </div>

<?php endif; ?>
