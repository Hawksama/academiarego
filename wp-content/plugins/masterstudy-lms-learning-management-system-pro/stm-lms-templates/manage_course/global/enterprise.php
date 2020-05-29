<?php

if (class_exists('STM_LMS_Enterprise_Courses')): ?>

    <div class="stm-lms-manage stm-lms-manage-enterprise-price">
        <h4><?php esc_html_e('Enterprise Price', 'masterstudy-lms-learning-management-system-pro'); ?></h4>

        <input type="number"
               name="enterprise_price"
               v-model="fields['enterprise_price']"/>

    </div>

<?php endif;