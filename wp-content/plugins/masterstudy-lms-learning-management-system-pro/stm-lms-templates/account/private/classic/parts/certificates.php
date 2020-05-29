<?php

stm_lms_register_style('user');
stm_lms_register_style('user_classic');

?>
<div class="stm-lms-wrapper">

    <div class="container">

        <div class="row">

            <div class="col-md-12 col-sm-12">

                <?php STM_LMS_Templates::show_lms_template('account/private/classic/parts/header', array('current_user' => $current_user)); ?>

                <h2 class="my-certificates-title"><?php esc_html_e('My Certificates', 'masterstudy-lms-learning-management-system-pro'); ?></h2>

                <?php STM_LMS_Templates::show_lms_template('account/private/parts/certificate-list', array('current_user' => $current_user)); ?>

            </div>

        </div>

    </div>

</div>