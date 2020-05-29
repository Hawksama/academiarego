<?php if (!defined('ABSPATH')) exit; //Exit if accessed directly ?>

<?php
/**
 * @var $current_user
 */
?>

<div class="row">

    <div class="col-md-12 col-sm-12">
		<?php STM_LMS_Templates::show_lms_template('account/public/classic/parts/top_bar', array('current_user' => $current_user)); ?>
    </div>

    <div class="col-md-12 col-sm-12">
        <div class="stm_lms_user_infoblock">
            <div class="row">
                <div class="col-md-3">
					<?php STM_LMS_Templates::show_lms_template('account/public/classic/instructor_parts/info', array('current_user' => $current_user)); ?>
                </div>
                <div class="col-md-9">
					<?php STM_LMS_Templates::show_lms_template('account/private/parts/bio', array('current_user' => $current_user)); ?>
                </div>
            </div>
        </div>
    </div>

    <div class="col-md-12">
		<?php STM_LMS_Templates::show_lms_template('account/public/instructor_parts/courses', array('current_user' => $current_user)); ?>
    </div>

</div>