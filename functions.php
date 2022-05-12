<?php
/**
 * TestWork 401 functions and definitions
 *
 * @package TestWork_401
 */

if ( ! defined( '_S_VERSION' ) ) {
	define( '_S_VERSION', '1.0.0' );
}


 /**
 * Подключаем css админки.
 */
add_action('admin_enqueue_scripts', 'testwork_401_admin_scripts');
function testwork_401_admin_scripts() {
	wp_enqueue_style( 'testwork-401-admin-style', get_stylesheet_directory_uri() . '/css/testwork-401-admin.css', array(), _S_VERSION );
}

/**
 * Подключаем функции темы.
 */
require get_stylesheet_directory() . '/inc/testwork-401-functions.php';

 /**
 * Подключаем ajax админки.
 */
require get_stylesheet_directory() . '/inc/testwork-401-admin-ajax-actions.php';

 /**
 * Подключаем функции админки.
 */
require get_stylesheet_directory() . '/inc/testwork-401-admin-functions.php';
