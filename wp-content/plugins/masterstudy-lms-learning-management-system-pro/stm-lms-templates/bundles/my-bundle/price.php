<div class="stm_lms_my_bundle__title">

    <h4 class="stm_lms_my_bundle__label">
        <?php printf(esc_html__('Bundle price (%s)', 'masterstudy-lms-learning-management-system-pro'), STM_LMS_Helpers::get_currency()); ?>
    </h4>

    <input type="number"
           class="form-control"
           v-model="bundle_price"
           placeholder="<?php esc_attr_e('Enter bundle price', 'masterstudy-lms-learning-management-system-pro'); ?>" />

</div>
