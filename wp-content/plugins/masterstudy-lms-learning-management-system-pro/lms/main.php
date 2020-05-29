<?php
require_once STM_LMS_PRO_PATH . '/lms/helpers.php';

require_once STM_LMS_PRO_PATH . '/lms/actions/templates.php';
require_once STM_LMS_PRO_PATH . '/lms/actions/create_announcement.php';
require_once STM_LMS_PRO_PATH . '/lms/actions/sale_price.php';
require_once STM_LMS_PRO_PATH . '/lms/actions/instructor_links.php';

if (class_exists('SitePress')) {
	require_once STM_LMS_PRO_PATH . '/lms/actions/multilanguage.php';
}

if (STM_LMS_Cart::woocommerce_checkout_enabled()) {
	require_once STM_LMS_PRO_PATH . '/lms/classes/WooCommerce.php';
	require_once STM_LMS_PRO_PATH . '/lms/actions/woocommerce_orders.php';
}

require_once STM_LMS_PRO_PATH . '/lms/classes/routes.php';
require_once STM_LMS_PRO_PATH . '/lms/classes/manage_course.php';
require_once STM_LMS_PRO_PATH . '/lms/classes/course.php';
require_once STM_LMS_PRO_PATH . '/lms/classes/addons.php';
require_once STM_LMS_PRO_PATH . '/lms/classes/certificates.php';

if(is_admin()) {
    require_once STM_LMS_PRO_PATH . '/lms/classes/plugin_installer.php';
}