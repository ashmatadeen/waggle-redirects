<?php
/**
 * Plugin Name: Waggle Redirects
 * Plugin URI: http://fatbeehive.com
 * Description: Set up and manage redirects
 * Author: Ash Matadeen
 * Author URI: http://ashmatadeen.com
 * Version: 1.0
 */

if( ! class_exists('acf') ) {
	add_filter( 'acf/settings/path', 'waggle_acf_settings_path' );
	add_filter( 'acf/settings/dir', 'waggle_acf_settings_dir' );
	add_filter( 'acf/settings/show_admin', '__return_false' );
	add_action( 'admin_menu', 'waggle_remove_acf_from_admin_menu', 100 );
	include_once( plugin_dir_path( __FILE__ ) . '/acf/acf.php' );
}

function waggle_redirect_on_404() {
	if ( is_404() ) {
		$redirect_to = waggle_get_redirect_target();
		if ( $redirect_to ) {
			wp_redirect( $redirect_to, 301 );
			exit();
		}
	}
}
add_action( 'template_redirect', 'waggle_redirect_on_404' );

function waggle_get_redirect_target() {
	$path = waggle_get_the_path();

	$args = array(
				'post_type'			=>	'redirect',
				'posts_per_page'	=>	1,
				'meta_key'			=>	'source_path',
				'meta_value'		=> 	$path,
			);

	$redirects = new WP_Query($args);
	if ( $redirects && count( $redirects->posts ) >= 1 ) {
		$the_target_id = $redirects->posts[0]->ID;
		$the_target = waggle_get_the_target( $the_target_id );
		return $the_target;
	}
	return false;
}

function waggle_get_the_target( $id ) {
	$target_type = get_field( 'redirect_to', $id );

	switch ( $target_type ) {
		case 'external':
			return get_field( 'external_target_path', $id );
			break;
		
		case 'internal':
			return get_field( 'internal_target_path', $id );
			break;
	}
}

function waggle_get_the_path() {
	return ltrim( $_SERVER['REQUEST_URI'], '/' );
}

function waggle_save_title( $title ) {
	if ( isset( $_POST['post_type'] ) && $_POST['post_type'] == 'redirect' ){
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

function waggle_get_post_types_for_internal_targets() {
	$post_types = get_post_types( array( 'public' => true ) );
	return $post_types;
}


function waggle_register_fields() {
	if ( ! function_exists( 'register_field_group' ) ) {
		return false;
	}
	
	register_field_group(array (
		'id' => 'acf_waggle-redirects',
		'title' => 'Waggle Redirects',
		'fields' => array (
			array (
				'key' => 'field_570e1a44d5123',
				'label' => 'Source path',
				'name' => 'source_path',
				'type' => 'text',
				'instructions' => 'This is the path you want to redirect from. e.g. if you would like to redirect http://my-domain.org/donate to http://my-domain.org/support-us enter donate in this field.',
				'required' => 1,
				'default_value' => '',
				'placeholder' => '',
				'prepend' => '',
				'append' => '',
				'formatting' => 'none',
				'maxlength' => '',
			),
			array (
				'key' => 'field_570e1ac0d5124',
				'label' => 'Redirect to',
				'name' => 'redirect_to',
				'type' => 'radio',
				'required' => 1,
				'choices' => array (
					'external' => 'External URL',
					'internal' => 'Internal content',
				),
				'other_choice' => 0,
				'save_other_choice' => 0,
				'default_value' => 'internal',
				'layout' => 'horizontal',
			),
			array (
				'key' => 'field_570e1af6d5125',
				'label' => 'External target path',
				'name' => 'external_target_path',
				'type' => 'text',
				'instructions' => 'This is the path you want to redirect to. e.g. if you would like to redirect http://my-domain.org/donate to http://donations-portal.org/donate enter http://donations-portal.org/donate in this field.',
				'required' => 1,
				'conditional_logic' => array (
					'status' => 1,
					'rules' => array (
						array (
							'field' => 'field_570e1ac0d5124',
							'operator' => '==',
							'value' => 'external',
						),
					),
					'allorany' => 'all',
				),
				'default_value' => '',
				'placeholder' => '',
				'prepend' => '',
				'append' => '',
				'formatting' => 'none',
				'maxlength' => '',
			),
			array (
				'key' => 'field_570e1b75d5126',
				'label' => 'Internal target path',
				'name' => 'internal_target_path',
				'type' => 'page_link',
				'required' => 1,
				'conditional_logic' => array (
					'status' => 1,
					'rules' => array (
						array (
							'field' => 'field_570e1ac0d5124',
							'operator' => '==',
							'value' => 'internal',
						),
					),
					'allorany' => 'all',
				),
				'post_type' => waggle_get_post_types_for_internal_targets(),
				'allow_null' => 0,
				'multiple' => 0,
			),
		),
		'location' => array (
			array (
				array (
					'param' => 'post_type',
					'operator' => '==',
					'value' => 'redirect',
					'order_no' => 0,
					'group_no' => 0,
				),
			),
		),
		'options' => array (
			'position' => 'normal',
			'layout' => 'no_box',
			'hide_on_screen' => array (
			),
		),
		'menu_order' => 0,
	));
}
add_action( 'acf/register_fields', 'waggle_register_fields' );

function waggle_acf_settings_path( $path = '' ) {
    $path = plugin_dir_path( __FILE__ ) . 'acf/';
    return $path;
}

function waggle_acf_settings_dir( $path = '' ) {
    $dir = plugin_dir_url( __FILE__ ) . 'acf/';
    return $dir;
}

function waggle_remove_acf_from_admin_menu() {
  remove_menu_page( 'edit.php?post_type=acf' ); 
}