<div class="stm_lms_my_bundle__select_course">

    <h4 class="stm_lms_my_bundle__label"><?php esc_html_e('Select Course', 'masterstudy-lms-learning-management-system-pro'); ?></h4>

    <div class="stm_lms_my_bundle__select_course_input" v-if="courses.length"
         v-bind:class="{'active' : select_course_open}">

        <div class="stm_lms_my_bundle__select_course_label" @click="select_course_open = true">
            <input type="text"
                   v-model="select_course_search"
                   @blur="select_course_open = false"
                   placeholder="<?php esc_attr_e('Select course', 'masterstudy-lms-learning-management-system-pro'); ?>"/>
        </div>

        <div class="stm_lms_my_bundle__select_course_submit">
            <?php esc_html_e('+ Add course', 'masterstudy-lms-learning-management-system-pro'); ?>
        </div>

        <div class="stm_lms_my_bundle__select_course_list">
            <div class="stm_lms_my_bundle__select_course_single"
                 v-if="!alreadyAdded(course)"
                 @click="addCourseInBundle(course);"
                 v-for="course in filteredList">
                <i class="fa fa-check"></i>
                <div class="stm_lms_my_bundle__select_course_image" v-html="course.image_small"></div>
                <div class="stm_lms_my_bundle__select_course_data heading_font">
                    <div class="stm_lms_my_bundle__select_course_title" v-html="course.title"></div>
                    <div class="stm_lms_my_bundle__select_course_price" v-html="course.price"
                         v-if="course.simple_price"></div>
                </div>
            </div>
        </div>


    </div>

    <div class="stm_lms_my_bundle__select_course_text" v-else>
        <?php

        $add_new = STM_LMS_Manage_Course::manage_course_url();

        printf(
            esc_html__(
                'You have no courses yet. Please %s add some courses %s to create bundle',
                'masterstudy-lms-learning-management-system-pro'
            ), "<a href='{$add_new}' target='_blank'>", "</a>"
        ); ?>
    </div>

    <div class="stm_lms_my_bundle__selected_courses_wrapper" v-if="bundle_courses.length">

        <h4 class="stm_lms_my_bundle__selected_courses__title">
            <?php printf(
                esc_html__('Maximum courses in bundle : %s', 'masterstudy-lms-learning-management-system-pro'),
                STM_LMS_My_Bundle::get_bundle_courses_limit()) ?>
        </h4>

        <div class="stm_lms_my_bundle__selected_courses">
            <div class="stm_lms_my_bundle__selected_courses__single" v-for="(bundle_course, index) in bundle_courses">
                <i class="fa fa-times" @click="bundle_courses.splice(index, 1)"></i>
                <div class="stm_lms_my_bundle__selected_courses_image" v-html="bundle_course.image"></div>
                <div class="stm_lms_my_bundle__selected_courses_data heading_font">
                    <div class="stm_lms_my_bundle__selected_courses_title" v-html="bundle_course.title"></div>
                    <div class="stm_lms_my_bundle__selected_courses_price" v-html="bundle_course.price"
                         v-if="bundle_course.simple_price"></div>
                </div>
            </div>
        </div>

        <div class="stm_lms_my_bundle__selected_courses_total heading_font">
            <?php esc_html_e('Total', 'masterstudy-lms-learning-management-system-pro'); ?> {{totalPrice}}
        </div>

    </div>


</div>