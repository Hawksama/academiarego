<?php
/**
 * @var $user
 */

stm_lms_register_script('affiliate_points');
stm_lms_register_style('affiliate_points');


?>
<div class="affiliate_points heading_font">
    <span id="text_to_copy"><?php echo esc_url(add_query_arg(array('affiliate_id' => $user['id']), get_site_url())); ?></span>
    <span class="affiliate_points__btn">
        <i class="fa fa-link"></i>
        <span class="text"><?php esc_html_e('Copy Affiliate link', 'masterstudy-lms-learning-management-system-pro'); ?></span>
    </span>
    <span class="copied">
        <i class="fa fa-check"></i>
    </span>
</div>