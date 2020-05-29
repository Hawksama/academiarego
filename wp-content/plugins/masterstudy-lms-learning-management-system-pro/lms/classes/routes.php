<?php

$routes = new STM_LMS_WP_Router_Pro();
$routes->create_routes();

class STM_LMS_WP_Router_Pro extends STM_LMS_WP_Router
{

	public function routes($routes = array())
	{
		return parent::routes(array(
			'/lms-manage' => 'stm_lms_manage_course',
			'/lms-manage/{course_id}' => 'stm_lms_manage_course',
			'/lms-gradebook' => 'stm_lms_gradebook',
			'/lms-enterprise-groups' => 'stm_lms_enterprise_groups',
			'/lms-enterprise-groups/{group_id}' => 'stm_lms_enterprise_group',
            '/lms-assignments' => 'stm_lms_assignments',
            '/lms-assignment/{assignment_id}' => 'stm_lms_assignment',
            '/lms-user-assignment/{assignment_id}' => 'stm_lms_user_assignment',
            '/lms-user-points-history' => 'stm_lms_points_history',
            '/lms-user-points-distribution' => 'stm_lms_points_distribution',
            '/lms-user-bundles' => 'stm_lms_manage_bundles',
            '/lms-user-bundles/{bundle_id}' => 'stm_lms_manage_bundle',
		));
	}
}