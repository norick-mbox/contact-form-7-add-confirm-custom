<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
/**
 *
 *
 * Created by PhpStorm.
 * Author: Eyeta Co.,Ltd.(http://www.eyeta.jp)
 * 
 */





function wpcf7c_load_modules() {
	$dir = WPCF7C_PLUGIN_MODULES_DIR;

	if ( empty( $dir ) || ! is_dir( $dir ) ) {
		return false;
	}

	$mods = array(
		'confirm', 'back' );

	foreach ( $mods as $mod ) {
		$file = trailingslashit( $dir ) . $mod . '.php';

		if ( file_exists( $file ) ) {
			include_once $file;
		}
	}
}


function wpcf7c_plugin_url( $path = '' ) {
	$url = untrailingslashit( WPCF7C_PLUGIN_URL );

	if ( ! empty( $path ) && is_string( $path ) && false === strpos( $path, '..' ) )
		$url .= '/' . ltrim( $path, '/' );

	return $url;
}

