<?php
function stm_theme_import_content($layout) {
	set_time_limit(0);

	if (!defined('WP_LOAD_IMPORTERS')) {
		define('WP_LOAD_IMPORTERS', true);
	}

	require_once(STM_POST_TYPE_PATH . '/importer/wordpress-importer/wordpress-importer.php');

	$wp_import = new WP_Import();
	$wp_import->fetch_attachments = true;

	$ready = prepare_demo( $layout );

	if( $ready ){
		ob_start();
		$wp_import->import($ready, $layout);
		ob_end_clean();
	}
}

function prepare_demo( $layout ){
	
	$tempDir = get_temp_dir();
	$fzip = $tempDir . $layout .'.zip';
	$fxml = $tempDir . $layout .'.xml';

	if( file_exists($fxml) ){
		return $fxml;
	}

	global $wp_filesystem;
	if ( empty( $wp_filesystem ) ) {
		require_once ABSPATH . '/wp-admin/includes/file.php';
		WP_Filesystem();
	}

	$response = wp_remote_get( get_package($layout, 'zip'), array('timeout' => 30) );
	if ( is_wp_error( $response ) ) {
		return false;
	}
	$body = wp_remote_retrieve_body( $response );

	// file_get_contents if body is empty.
	if ( empty( $body ) ) {
		if ( function_exists( 'ini_get' ) && ini_get( 'allow_url_fopen' ) ) {
			$body = @file_get_contents( get_package($layout, 'zip') );
		}
	}

	if ( ! $wp_filesystem->put_contents( $fzip , $body ) ) {
		@unlink( $fzip );
		$fp = @fopen( $fzip, 'w' );

		@fwrite( $fp, $body );
		@fclose( $fp );
	}

	if ( class_exists( 'ZipArchive' ) ) {
		$zip = new ZipArchive();
		if ( true === $zip->open( $fzip ) ) {
			$zip->extractTo( $tempDir );
			$zip->close();
			return $fxml;
		}
	}

	$unzip = unzip_file( $fzip, $tempDir );
	if($unzip){
		return $fxml;
	}

	return false;
}