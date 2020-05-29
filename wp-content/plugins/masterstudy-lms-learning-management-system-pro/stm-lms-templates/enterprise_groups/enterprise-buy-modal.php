<?php
/**
 * @var $course_id
 */
$groups = STM_LMS_Enterprise_Courses::stm_lms_get_enterprise_groups(true);
$price = STM_LMS_Enterprise_Courses::get_enterprise_price($course_id);

$user = STM_LMS_User::get_current_user();
$user_id = $user['id'];
?>

<h2><?php esc_html_e('Buy for group', 'masterstudy-lms-learning-management-system-pro'); ?></h2>
<div class="course_name"><?php printf(esc_html__("%s", 'masterstudy-lms-learning-management-system-pro'), get_the_title($course_id)); ?></div>

<?php if (!empty($groups)): ?>
    <div class="stm_lms_select_group__list">
        <h4><?php esc_html_e('Choose Group:', 'masterstudy-lms-learning-management-system-pro'); ?></h4>
        <?php foreach ($groups as $group):
            $user_course = stm_lms_get_user_course($user_id, $course_id, array('start_time'), $group['group_id']);
            $class = array();
            $class[] = (!empty($user_course)) ? 'stm_lms_selected_group' : 'stm_lms_select_group';
            if (!empty($user_course)) $class[] = 'active';
            ?>
            <div class="<?php echo esc_attr(implode(' ', $class)); ?>"
                 data-group-id="<?php echo intval($group['group_id']); ?>">
                <span><?php echo sanitize_text_field($group['title']); ?></span>
                <?php if (in_array('stm_lms_selected_group', $class)):
                    $date = date_i18n('j F Y', $user_course[0]['start_time']);
                    ?>
                    <label>
                        <?php printf(esc_html__('Bought at %s', 'masterstudy-lms-learning-management-system-pro'), $date); ?>
                    </label>
                <?php endif; ?>
            </div>
        <?php endforeach; ?>
    </div>
<?php else: ?>

    <div class="stm_lms_select_group__list">
        <h4><?php esc_html_e('Choose Group:', 'masterstudy-lms-learning-management-system-pro'); ?></h4>
        <div class="no-groups-message">
            <?php printf(esc_html__('You do not have any groups yet. %sCreate a group%s and add group members.', 'masterstudy-lms-learning-management-system-pro'), '<a href="#" class="create_group">', '</a>') ?>
        </div>
    </div>
<?php endif; ?>

<div class="actions <?php echo (empty($groups)) ? 'no-groups' : 'have-groups' ?>">
    <a href="#"
       data-course-id="<?php echo intval($course_id); ?>"
       class="btn btn-default buy-enterprise disabled"
       data-enterprise-price="<?php echo esc_attr($price); ?>">
        <?php printf(esc_html__('Add to cart %s', 'masterstudy-lms-learning-management-system-pro'), "<span>" . STM_LMS_Helpers::display_price('0') . "</span>"); ?>
    </a>

    <a href="#"
       class="create_group"><?php esc_html_e('Add Group', 'masterstudy-lms-learning-management-system-pro'); ?></a>

</div>


<div class="stm_lms_popup_create_group">

    <div class="stm_lms_popup_create_group__inner">

        <div class="row">
            <div class="col-sm-6">
                <label>
                    <span class="heading_font"><?php esc_html_e('Group name:', 'masterstudy-lms-learning-management-system-pro'); ?></span>
                    <input type="text" class="form-control" placeholder="<?php esc_attr_e('Enter group name...', 'masterstudy-lms-learning-management-system-pro') ?>" name="group_name" id="group_name"/>
                </label>
            </div>

            <div class="col-sm-6">

                <label>
                    <span class="heading_font"><?php printf(__('Add users: <span>(Max : %s)</span>', 'masterstudy-lms-learning-management-system-pro'), STM_LMS_Enterprise_Courses::get_group_common_limit()); ?></span>
                    <input type="text" placeholder="<?php esc_attr_e('Enter member E-mail...', 'masterstudy-lms-learning-management-system-pro') ?>" class="form-control" name="group_emails" id="group_email"/>
                    <span class="add_email"><i class="lnricons-arrow-return"></i></span>
                </label>

            </div>

            <div class="col-sm-12">

                <div class="group-emails"></div>

                <a href="#" class="btn btn-default btn-add-group">
                    <?php esc_html_e('Create group', 'masterstudy-lms-learning-management-system-pro'); ?>
                </a>

                <div class="stm_lms_group_new_error"></div>

            </div>
        </div>


    </div>

</div>