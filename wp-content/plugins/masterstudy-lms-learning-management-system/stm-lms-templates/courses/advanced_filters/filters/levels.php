<?php

$values = (!empty($_GET['levels'])) ? $_GET['levels'] : array();

$statuses = array(
    'beginner' => esc_html__('Beginner', 'masterstudy-lms-learning-management-system'),
    'intermediate' => esc_html__('Medium', 'masterstudy-lms-learning-management-system'),
    'advanced' => esc_html__('Advanced', 'masterstudy-lms-learning-management-system'),
);

if (!empty($statuses)) : ?>

    <div class="stm_lms_courses__filter stm_lms_courses__search">

        <div class="stm_lms_courses__filter_heading">
            <h3><?php esc_html_e('Level', 'masterstudy-lms-learning-management-system'); ?></h3>
            <div class="toggler"></div>
        </div>

        <div class="stm_lms_courses__filter_content" style="display: none;">

            <?php foreach ($statuses as $status => $status_label): ?>

                <div class="stm_lms_courses__filter_category">
                    <label class="stm_lms_styled_checkbox">
                    <span class="stm_lms_styled_checkbox__inner">
                        <input type="checkbox"
                               <?php if ( in_array(sanitize_text_field($status), $values) ) echo 'checked="checked"'; ?>
                               value="<?php echo sanitize_text_field($status); ?>"
                               name="levels[]"/>
                        <span><i class="fa fa-check"></i> </span>
                    </span>
                        <span><?php echo esc_html($status_label); ?></span>
                    </label>
                </div>

            <?php endforeach; ?>

        </div>

    </div>

<?php endif;