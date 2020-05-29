<?php
/**
 * @var $post_id
 * @var $item_id
 */

/*check if url is youtube and get video ID*/

stm_lms_register_script( 'lessons-stream', array( 'jquery-ui-resizable' ) );
stm_lms_register_script( 'lessons' );

$url = get_post_meta( $item_id, 'lesson_video_url', true );

if( class_exists( 'STM_ZOOM' ) ) {
    $meeting_id = get_post_meta( $item_id, 'meeting_created', true );
    if( !empty( $meeting_id ) ):
        ?>
        <div class="stm_zoom_wrap">
            <?php echo do_shortcode( '[stm_zoom_conference post_id="' . $meeting_id . '"]' ); ?>
        </div>

    <?php
    endif;
}
elseif( class_exists( 'Zoom_Video_Conferencing_Api' ) ) {


    /*Check If stream Have to start now*/
    $video_url_params = STM_LMS_Zoom_Conference::get_video_url( $url );

    $is_youtube = strpos( $video_url_params, '&is_youtube' );
    if( $is_youtube ) $video_url_params = str_replace( '&is_youtube', '', $video_url_params );
    $video_url = ( $is_youtube ) ? "https://www.youtube.com/embed/{$video_url_params}?autoplay=1&showinfo=0&controls=0&autohide=1" : $video_url_params;

    $q = new WP_Query( array(
        'posts_per_page' => 1,
        'post_type' => 'stm-lessons',
        'post__in' => array( $item_id )
    ) );

    if( $q->have_posts() ):
        while ( $q->have_posts() ):
            $q->the_post();
            $the_content = get_the_content();
        endwhile;
        wp_reset_postdata();
    endif;

    $single_video = ( empty( $the_content ) and !$is_youtube );

    $classes = array();

    if( $single_video ) $classes[] = 'single_video';
    if( !$is_youtube ) $classes[] = 'no-chat';


    ?>

    <h3 class="stm_lms_stream_lesson__title"><?php echo wp_kses_post( get_the_title( $item_id ) ); ?></h3>

    <div class="stm_lms_stream_lesson <?php echo esc_attr( implode( ' ', $classes ) ); ?>">
        <div class="zoom_conf">
            <?php echo do_shortcode( STM_LMS_Zoom_Conference::create_zoom_shortcode( $item_id, get_the_title( $item_id ) ) ); ?>
        </div>
    </div>
    <?php
}
