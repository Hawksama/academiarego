<?php
/**
 * @var $current_user
 */

stm_lms_register_style('create_announcement');
stm_lms_register_script('create_announcement', array('vue.js', 'vue-resource.js'));

?>
<div data-container-open=".stm_lms_create_announcement">
    <div class="stm_lms_create_announcement" id="stm_lms_create_announcement">

        <div class="stm_lms_user_info_top">
            <h1><?php esc_html_e('Create Announcement', 'masterstudy-lms-learning-management-system-pro'); ?></h1>
        </div>

        <div class="row">

            <div class="col-md-12">
                <div class="form-group">
                    <label class="heading_font"><?php esc_html_e('Choose Course', 'masterstudy-lms-learning-management-system-pro'); ?></label>
                    <select v-model="post_id" class="disable-select form-control">
                        <option v-for="(item, key) in posts" v-bind:value="key">
                            {{ item }}
                        </option>
                    </select>
                </div>
            </div>

            <div class="col-md-12">
                <div class="form-group">
                    <label class="heading_font">
                        <?php esc_html_e('Message for Course Students', 'masterstudy-lms-learning-management-system-pro'); ?>
                    </label>
                    <textarea v-model="mail"></textarea>
                </div>
            </div>

        </div>

        <div class="row">

            <div class="col-md-12">
                <button @click="createAnnouncement()"
                        v-bind:class="{'loading' : loading}"
                        class="btn btn-default">
                    <span><?php esc_html_e('Create', 'masterstudy-lms-learning-management-system-pro'); ?></span>
                </button>
            </div>

            <div class="col-md-12">
                <transition name="slide-fade">
                    <div class="stm-lms-message" v-bind:class="status" v-if="message">
                        {{ message }}
                    </div>
                </transition>
            </div>

        </div>
    </div>
</div>