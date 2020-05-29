<?php
/**
 *
 * @var $udemy_meta
 */


if (!empty($udemy_meta['current_students'])):
	$student_num = $udemy_meta['current_students'];
	?>
    <div class="stm_lms_enrolled_num">
        <i class="fa fa-user-circle"></i>
		<span>
            <?php printf(_n('%s student enrolled', '%s students enrolled', $student_num, 'masterstudy-lms-learning-management-system-pro'), number_format_i18n($student_num)); ?>
        </span>
    </div>
<?php endif;