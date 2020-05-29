<?php
/*
Plugin Name: MasterStudy LMS Learning Management System PRO
Plugin URI: http://masterstudy.stylemixthemes.com/lms-plugin/
Description: Create brilliant lessons with videos, graphs, images, slides and any other attachments thanks to flexible and user-friendly lesson management tool powered by WYSIWYG editor.
As the ultimate LMS WordPress Plugin, MasterStudy makes it simple and hassle-free to build, customize and manage your Online Education WordPress website.
Author: StylemixThemes
Author URI: https://stylemixthemes.com/
Text Domain: masterstudy-lms-learning-management-system-pro
Version: 2.2.1
*/

if ( ! defined( 'ABSPATH' ) ) exit; //Exit if accessed directly

define('STM_LMS_PRO_FILE', __FILE__);
define('STM_LMS_PRO_PATH', dirname(STM_LMS_PRO_FILE));
define('STM_LMS_PRO_URL', plugin_dir_url(STM_LMS_PRO_FILE));

if (!is_textdomain_loaded('masterstudy-lms-learning-management-system-pro')) {
	load_plugin_textdomain(
		'masterstudy-lms-learning-management-system-pro',
		false,
		'masterstudy-lms-learning-management-system-pro/languages'
	);
}

add_action('plugins_loaded', 'stm_lms_pro_init');

function stm_lms_pro_init()
{
	$lms_installed = defined('STM_LMS_PATH');
	if(!$lms_installed) {
		function stm_lms_pro_admin_notice__success() {
			require_once STM_LMS_PRO_PATH . '/wizard/templates/notice.php';
		}
		add_action( 'admin_notices', 'stm_lms_pro_admin_notice__success' );
		require_once STM_LMS_PRO_PATH . '/wizard/wizard.php';
	} else {
		require_once(STM_LMS_PRO_PATH . '/lms/main.php');
	}
}

add_action('admin_head', 'stm_lms_pro_nonces');
add_action('wp_head', 'stm_lms_pro_nonces');
function stm_lms_pro_nonces()
{

    $nonces = array(
        'stm_lms_pro_install_base',
        'stm_lms_pro_search_courses',
        'stm_lms_pro_udemy_import_courses',
        'stm_lms_pro_udemy_publish_course',
        'stm_lms_pro_udemy_import_curriculum',
        'stm_lms_pro_save_addons',
        'stm_lms_create_announcement',
        'stm_lms_pro_upload_image',
        'stm_lms_pro_get_image_data',
        'stm_lms_pro_save_quiz',
        'stm_lms_pro_save_lesson',
        'stm_lms_pro_save_front_course',
        'stm_lms_get_course_info',
        'stm_lms_get_course_students',
    );

    $nonces_list = array();

    foreach ($nonces as $nonce_name) {
        $nonces_list[$nonce_name] = wp_create_nonce($nonce_name);
    }

    ?>
    <script>
        var stm_lms_pro_nonces = <?php echo json_encode($nonces_list); ?>;
    </script>
    <?php
}