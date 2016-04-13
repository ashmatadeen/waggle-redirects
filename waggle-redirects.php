<?php
/**
 * Plugin Name: Waggle Redirects
 * Plugin URI: http://fatbeehive.com
 * Description: Set up and manage redirects
 * Author: Ash Matadeen
 * Author URI: http://ashmatadeen.com
 * Version: 1.0
 */

add_filter( 'title_save_pre', 'waggle_save_title' );
function waggle_save_title( $title ) {
	if ( $_POST['post_type'] == 'redirect' ){
		$title = $_POST['fields']['field_570e1a44d5123'];
	}
	return $title;
}