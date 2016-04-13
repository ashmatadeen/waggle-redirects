<?php
/**
 * Plugin Name: Waggle Redirects
 * Plugin URI: http://fatbeehive.com
 * Description: Set up and manage redirects
 * Author: Ash Matadeen
 * Author URI: http://ashmatadeen.com
 * Version: 1.0
 */


function waggle_save_title( $title ) {
	if ( $_POST['post_type'] == 'redirect' ){
		$title = $_POST['fields']['field_570e1a44d5123'];
	}
	return $title;
}
add_filter( 'title_save_pre', 'waggle_save_title' );

function waggle_register_redirect_cpt() {
	$labels = array(
		'name' => 'Redirects',
		'singular_name' => 'Redirect',
		);

	$args = array(
		'label' => 'Redirects',
		'labels' => $labels,
		'description' => '',
		'public' => false,
		'show_ui' => true,
		'show_in_rest' => false,
		'rest_base' => '',
		'has_archive' => false,
		'show_in_menu' => true,
		'exclude_from_search' => true,
		'capability_type' => 'post',
		'map_meta_cap' => true,
		'hierarchical' => false,
		'rewrite' => false,
		'query_var' => false,
		'menu_icon' => 'dashicons-arrow-right-alt',		
		'supports' => false,				
	);
	register_post_type( 'redirect', $args );
}
add_action( 'init', 'waggle_register_redirect_cpt' );

