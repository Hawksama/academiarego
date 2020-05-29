<?php
function stm_set_content_options( $layout ) {
	$locations = get_theme_mod('nav_menu_locations');
	$menus  = wp_get_nav_menus();

	if(!empty($menus))
	{
		foreach($menus as $menu)
		{
			$menu_names = array(
				'Primary menu',
				'Main Menu'
			);

			if(is_object($menu) && in_array($menu->name, $menu_names))
			{
				$locations['primary'] = $menu->term_id;
			}
			if(is_object($menu) && $menu->name == 'Footer menu')
			{
				$locations['secondary'] = $menu->term_id;
			}
		}
	}

	set_theme_mod('nav_menu_locations', $locations);

	update_option( 'show_on_front', 'page' );

	$front_pages = array(
		'Front Page',
		'Home'
	);

	foreach($front_pages as $front_page_name) {
		$front_page = get_page_by_title($front_page_name);
		if ( isset( $front_page->ID ) ) {
			update_option( 'page_on_front', $front_page->ID );
		}
	}

	$blog_page = get_page_by_title( 'Blog' );
	if ( isset( $blog_page->ID ) ) {
		update_option( 'page_for_posts', $blog_page->ID );
	}
	$shop_page = get_page_by_title( 'All courses' );
	if ( isset( $shop_page->ID ) ) {
		update_option( 'woocommerce_shop_page_id', $shop_page->ID );
	}
	$cart_page = get_page_by_title( 'Your Courses' );
	if ( isset( $cart_page->ID ) ) {
		update_option( 'woocommerce_cart_page_id', $cart_page->ID );
	}
	$checkout_page = get_page_by_title( 'Checkout' );
	if ( isset( $checkout_page->ID ) ) {
		update_option( 'woocommerce_checkout_page_id', $checkout_page->ID );
	}
	$account_page = get_page_by_title( 'My Account' );
	if ( isset( $account_page->ID ) ) {
		update_option( 'woocommerce_myaccount_page_id', $account_page->ID );
	}
	$single = array( 'width' => '840', 'height' => '400', 'crop' => 1 );
	$thumbnail = array( 'width' => '175', 'height' => '100', 'crop' => 1 );
	update_option( 'shop_single_image_size', $single );
	update_option( 'shop_thumbnail_image_size', $thumbnail );


	$fxml = get_temp_dir() . $layout . '.xml';
	$fzip = get_temp_dir() . $layout . '.zip';
	if( file_exists($fxml) ) @unlink($fxml);
	if( file_exists($fzip) ) @unlink($fzip);
}