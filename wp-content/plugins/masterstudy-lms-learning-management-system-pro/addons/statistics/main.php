<?php
new Stm_Lms_Statistics();

class Stm_Lms_Statistics {

	function __construct() {
		if(class_exists("\stmLms\Classes\Models\StmStatistics")){
			$statistics = new \stmLms\Classes\Models\StmStatistics();
			$statistics->admin_menu();
			$user = new \stmLms\Classes\Models\StmUser(get_current_user_id());
			if($user){
				$user_role = $user->getRole();
				if($user_role['id'] == "stm_lms_instructor")
					add_filter("account-private-parts-tabs",[$this, "add_account_private_parts_tab"]);
			}
		}
	}

	/**
	 * @param $tabs
	 *
	 * @return mixed
	 */
	public function add_account_private_parts_tab($tabs) {
		$tabs['statistics'] = esc_html__('Statistics', 'masterstudy-lms-learning-management-system-pro');
		return $tabs;
	}
}
