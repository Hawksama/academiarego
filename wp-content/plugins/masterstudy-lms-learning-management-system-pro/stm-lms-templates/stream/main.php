<?php
/**
 * @var $post_id
 * @var $item_id
 */

/*check if url is youtube and get video ID*/


$url = get_post_meta($item_id, 'lesson_video_url', true);
$stream_started = STM_LMS_Live_Streams::is_stream_started($item_id);

/*Check If stream Have to start now*/
$video_url_params = STM_LMS_Live_Streams::get_video_url($url);

$is_youtube = strpos($video_url_params, '&is_youtube');
if($is_youtube) $video_url_params = str_replace('&is_youtube', '', $video_url_params);
$video_url = ($is_youtube) ? "https://www.youtube.com/embed/{$video_url_params}?autoplay=1&showinfo=0&controls=0&autohide=1" : $video_url_params;

$q = new WP_Query(array(
    'posts_per_page' => 1,
    'post_type' => 'stm-lessons',
    'post__in' => array($item_id)
));

stm_lms_register_script('lessons-stream', array('jquery-ui-resizable'));
stm_lms_register_script('lessons');

if ($q->have_posts()):
    while ($q->have_posts()):
        $q->the_post();
        $the_content = get_the_content();
    endwhile;
    wp_reset_postdata();
endif;

$single_video = (empty($the_content) and !$is_youtube);

$classes = array();

if($single_video) $classes[] = 'single_video';
if(!$is_youtube) $classes[] = 'no-chat';

?>

    <script>
        var cf7_custom_image = '<?php echo esc_url(get_stylesheet_directory_uri())  ?>/assets/img/';
        var daysStr = '<?php esc_html_e('Days', 'masterstudy'); ?>';
        var hoursStr = '<?php esc_html_e('Hours', 'masterstudy'); ?>';
        var minutesStr = '<?php esc_html_e('Minutes', 'masterstudy'); ?>';
        var secondsStr = '<?php esc_html_e('Seconds', 'masterstudy'); ?>';
    </script>

<?php if ($stream_started): ?>

    <h3 class="stm_lms_stream_lesson__title"><?php echo wp_kses_post(get_the_title($item_id)); ?></h3>

    <div class="stm_lms_stream_lesson <?php echo esc_attr(implode(' ', $classes)); ?>">

        <div class="left">

            <div class="stm_lms_stream_lesson__video">
                <iframe src="<?php echo esc_url($video_url); ?>" frameborder="0"
                        allow="accelerometer; autoplay; encrypted-media; gyroscope; picture-in-picture"
                        allowfullscreen></iframe>
            </div>

        </div>

        <?php if (!empty($the_content) or $is_youtube): ?>
            <div class="right">

                <div class="right_inner">

                    <?php if (!empty($the_content)): ?>
                        <div class="stm_lms_stream_lesson__content">
                            <div class="stm_lms_stream_lesson__content_inner">
                                <?php echo wp_kses_post($the_content); ?>
                            </div>
                        </div>
                    <?php endif; ?>

                    <?php if ($is_youtube): ?>
                        <div class="stm_lms_stream_lesson__chat">
                            <iframe src="https://www.youtube.com/live_chat?v=<?php echo esc_attr($video_url_params); ?>&embed_domain=<?php echo esc_attr(str_replace('www.', '', $_SERVER['SERVER_NAME'])); ?>"
                                    frameborder="0"
                                    allow="accelerometer; autoplay; encrypted-media; gyroscope; picture-in-picture"
                                    allowfullscreen></iframe>
                        </div>

                    <?php endif; ?>

                </div>

            </div>
        <?php endif; ?>

    </div>

<?php else:

    wp_enqueue_script('jquery.countdown');

    stm_module_styles('countdown');
    ?>

    <div class="container stream-starts-soon">

        <h3 class="text-center"><?php printf(esc_html__('%s starts in', 'masterstudy-lms-learning-management-system-pro'), get_the_title($item_id)); ?></h3>

        <div class="stm_countdown text-center"
             data-timer="<?php echo esc_attr(STM_LMS_Live_Streams::stream_start_time($item_id) * 1000) ?>"
             id="countdown_<?php echo esc_attr($item_id); ?>"></div>
    </div>


<?php endif;