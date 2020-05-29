<?php
/**
 * @var $post_id
 * @var $item_id
 */

stm_lms_register_style('scorm/scorm');
stm_lms_register_script('scorm/pipwerks');
stm_lms_register_script('scorm/scorm');
$scorm_url = STM_LMS_Scorm_Packages::get_iframe_url($post_id);
$scorm_meta = STM_LMS_Scorm_Packages::get_scorm_meta($post_id);

if(!empty($scorm_url)): ?>
    <div class="stm-lms-course__overlay"></div>

    <div class="stm-lms-wrapper scorm">
        <iframe id="stm-lms-scorm-iframe"
            data-src="<?php echo esc_url($scorm_url); ?>"
            data-course-id="<?php echo esc_attr($post_id) ?>"
            data-scorm-version="<?php echo (!empty($scorm_meta['scorm_version'])) ? esc_attr($scorm_meta['scorm_version']) : '1.2'; ?>"
        ></iframe>
    </div>
<?php else : ?>

<?php endif;