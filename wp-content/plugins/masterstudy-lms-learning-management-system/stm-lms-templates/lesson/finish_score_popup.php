<?php

/**
 * @var $post_id
 * @var $item_id
 */

$completed = STM_LMS_Lesson::is_lesson_completed(null, $post_id, $item_id);

stm_lms_register_style('lesson/total_progress');
stm_lms_register_script('lesson/total_progress', array('vue.js', 'vue-resource.js'));
wp_localize_script('stm-lms-lesson/total_progress', 'total_progress', array(
    'course_id' => $post_id,
    'completed' => (bool) $completed
));

?>

<div class="stm_lms_finish_score_popup" style="opacity: 0;">

    <div class="stm_lms_finish_score_popup__overlay"></div>

    <div class="stm_lms_finish_score_popup__inner">

        <i class="stm_lms_finish_score_popup__close fa fa-times"></i>

        <div id="stm_lms_finish_score">
            <h4 v-if="loading" class="loading">
                <?php esc_html_e('Loading your statistics', 'masterstudy-lms-learning-management-system'); ?>
            </h4>

            <div class="stm_lms_finish_score" v-else>

                <div class="stm_lms_finish_score__head">
                    <div class="stm_lms_finish_score__face">
                        <img src="<?php echo STM_LMS_URL ?>/assets/img/faces/crying.svg"
                             v-if="!stats.course_completed"/>
                        <img src="<?php echo STM_LMS_URL ?>/assets/img/faces/kissing.svg" v-else/>
                    </div>
                    <div class="stm_lms_finish_score__score">
                        <span><?php esc_html_e('Your score', 'masterstudy-lms-learning-management-system'); ?></span>
                        <h3 v-html="stats.course.progress_percent + '%'"></h3>
                    </div>
                </div>

                <div class="stm_lms_finish_score__notice">
                    <span v-if="!stats.course_completed"><?php esc_html_e('You have NO completed the course', 'masterstudy-lms-learning-management-system'); ?></span>
                    <span v-else><?php esc_html_e('You have successfully completed the course', 'masterstudy-lms-learning-management-system'); ?></span>
                </div>

                <h2 class="stm_lms_finish_score__title" v-html="stats.title"></h2>

                <div class="stm_lms_finish_score__stats">

                    <div class="stm_lms_finish_score__stat" v-for="(stat, type) in stats.curriculum">

                        <div :class="'stm_lms_finish_score__stat_' + type" v-if="type==='lesson'">
                            <i class="far fa-file-alt"></i>
                            <span>
                                <?php esc_html_e('Pages:', 'masterstudy-lms-learning-management-system'); ?>
                                <strong>{{stat.completed}}/{{stat.total}}</strong>
                            </span>
                        </div>

                        <div :class="'stm_lms_finish_score__stat_' + type" v-if="type==='multimedia'">
                            <i class="far fa-play-circle"></i>
                            <span>
                                <?php esc_html_e('Media:', 'masterstudy-lms-learning-management-system'); ?>
                                <strong>{{stat.completed}}/{{stat.total}}</strong>
                            </span>
                        </div>

                        <div :class="'stm_lms_finish_score__stat_' + type" v-if="type==='quiz'">
                            <i class="far fa-question-circle"></i>
                            <span>
                                <?php esc_html_e('Quizzes:', 'masterstudy-lms-learning-management-system'); ?>
                                <strong>{{stat.completed}}/{{stat.total}}</strong>
                            </span>
                        </div>

                        <div :class="'stm_lms_finish_score__stat_' + type" v-if="type==='assignment'">
                            <i class="fa fa-spell-check"></i>
                            <span>
                                <?php esc_html_e('Assignments:', 'masterstudy-lms-learning-management-system'); ?>
                                <strong>{{stat.completed}}/{{stat.total}}</strong>
                            </span>
                        </div>

                    </div>

                </div>

                <div class="stm_lms_finish_score__buttons">
                    <!--Buttons for passed-->
                    <div class="inner">
                        <a v-if="stats.course_completed" :href="stats.certificate_url" target="_blank" class="btn btn-default">
                            <?php esc_html_e('Certificate', 'masterstudy-lms-learning-management-system'); ?>
                        </a>

                        <a :href="stats.url" class="btn btn-default btn-green">
                            <?php esc_html_e('View course', 'masterstudy-lms-learning-management-system'); ?>
                        </a>
                    </div>

                </div>

            </div>

        </div>

    </div>

</div>
