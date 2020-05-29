<?php if (!defined('ABSPATH')) exit; //Exit if accessed directly ?>

<?php
/**
 * @var $current_user
 */
?>


<div class="row">

    <div class="col-md-12 col-sm-12">
        <?php STM_LMS_Templates::show_lms_template('account/private/classic/parts/header', array('current_user' => $current_user)); ?>
    </div>

    <div class="col-md-12 col-sm-12">
        <div class="stm_lms_user_infoblock">
            <div class="row">
                <div class="col-md-3">
                    <?php STM_LMS_Templates::show_lms_template('account/private/classic/instructor_parts/info', array('current_user' => $current_user)); ?>
                </div>
                <div class="col-md-9">

                    <div data-container-open=".stm_lms_private_information">
                        <?php STM_LMS_Templates::show_lms_template('account/private/parts/bio', array('current_user' => $current_user)); ?>

                        <?php STM_LMS_Templates::show_lms_template('account/private/parts/socials', array('current_user' => $current_user)); ?>
                    </div>

                    <?php STM_LMS_Templates::show_lms_template('account/private/instructor_parts/edit_account', array('current_user' => $current_user)); ?>

                    <?php STM_LMS_Templates::show_lms_template('account/private/instructor_parts/create_announcement', array('current_user' => $current_user)); ?>
                </div>
            </div>
        </div>
    </div>

    <div class="col-md-12">
        <?php STM_LMS_Templates::show_lms_template('account/private/parts/tabs', compact('current_user')); ?>
    </div>

</div>