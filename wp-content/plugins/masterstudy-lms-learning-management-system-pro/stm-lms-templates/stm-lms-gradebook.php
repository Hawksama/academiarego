<?php if (!defined('ABSPATH')) exit; //Exit if accessed directly ?>

<?php
get_header();
stm_lms_register_style('gradebook');
stm_lms_register_script('gradebook', array('vue.js', 'vue-resource.js'));
do_action('stm_lms_template_main');
$style = STM_LMS_Options::get_option('profile_style', 'default');
?>

    <div class="stm-lms-wrapper stm-lms-wrapper--gradebook">
        <div class="container">

            <div class="stm-lms-wrapper--gradebook_header">

                <?php if ($style === 'classic'):

                    STM_LMS_Templates::show_lms_template(
                        'account/private/classic/parts/header',
                        array('current_user' => STM_LMS_User::get_current_user('', true, true))
                    );

                else: ?>

                    <a href="<?php echo esc_url(STM_LMS_User::user_page_url()); ?>">
                        <i class="lnricons-arrow-left"></i>
                        <?php esc_html_e('Back to account', 'masterstudy-lms-learning-management-system-pro'); ?>
                    </a>

                    <h2><?php esc_html_e('The Gradebook', 'masterstudy-lms-learning-management-system-pro'); ?></h2>

                <?php endif; ?>

            </div>

            <div id="stm_lms_gradebook">

                <div class="stm_lms_gradebook__filter">

                    <div class="form-group">
                        <label><?php esc_html_e('Search Courses', 'masterstudy-lms-learning-management-system-pro'); ?></label>
                        <input type="text"
                               class="form-control"
                               v-model="search"
                               placeholder="<?php esc_html_e('Enter keyword here', 'masterstudy-lms-learning-management-system-pro'); ?>"/>
                    </div>

                    <a href="#"
                       class="by_views_sorter"
                       @click.prevent="by_views = !by_views; sortBy();"
                       v-bind:class="{'by-views' : by_views}">
                        <?php esc_html_e('Order by Views', 'masterstudy-lms-learning-management-system-pro'); ?>
                    </a>

                </div>

                <div class="stm_lms_gradebook__courses" v-bind:class="{'loading' : loading}">
                    <div class="stm_lms_gradebook__course"
                         v-for="course in filteredList">

                        <div class="stm_lms_gradebook__course__inner" @click.prevent="openCourse(course)">

                            <a v-bind:href="course.link"
                               class="stm_lms_gradebook__course__image"
                               v-if="course.image_small"
                               target="_blank"
                               v-html="course.image_small"></a>

                            <h4 class="stm_lms_gradebook__course__title" v-html="course.title"></h4>

                            <div class="stm_lms_gradebook__course__views">
                                <i class="lnricons-eye"></i><span v-html="course.views"></span>
                            </div>
                        </div>

                        <?php STM_LMS_Templates::show_lms_template('gradebook/course-details'); ?>

                        <?php STM_LMS_Templates::show_lms_template('gradebook/students-details'); ?>

                    </div>
                </div>

                <a href="#"
                   class="btn btn-default"
                   v-bind:class="{'loading' : loading}"
                   @click.prevent="loadMore()" v-if="!total">
                    <?php esc_html_e('Load more', 'masterstudy-lms-learning-management-system-pro'); ?>
                </a>

            </div>

        </div>
    </div>

<?php get_footer(); ?>