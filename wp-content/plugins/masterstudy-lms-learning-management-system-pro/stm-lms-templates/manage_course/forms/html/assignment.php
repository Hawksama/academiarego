<div>

    <div class="stm_lms_item_modal__inner" v-bind:class="{'loading' : loading}">
        <h3 class="text-center">{{title}}</h3>

        <?php STM_LMS_Templates::show_lms_template('manage_course/forms/editor', array('field_key' => 'content', 'listener' => true)); ?>
    </div>

    <div class="stm_lms_item_modal__bottom">
        <a href="#" @click.prevent="saveChanges()"
           class="btn btn-default"><?php esc_html_e('Save Changes', 'masterstudy-lms-learning-management-system-pro'); ?></a>
        <a href="#" @click.prevent="discardChanges()"
           class="btn btn-default"><?php esc_html_e('Cancel', 'masterstudy-lms-learning-management-system-pro'); ?></a>
    </div>

</div>