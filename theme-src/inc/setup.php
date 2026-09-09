<?php
/**
 * Soporte del tema y menús.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

add_action(
	'after_setup_theme',
	function () {
		add_theme_support( 'title-tag' );
		add_theme_support( 'post-thumbnails' );
		add_theme_support( 'custom-logo', array( 'height' => 48, 'flex-width' => true ) );
		add_theme_support(
			'html5',
			array( 'search-form', 'comment-form', 'gallery', 'caption', 'style', 'script' )
		);

		register_nav_menus(
			array(
				'principal' => __( 'Menú principal', 'garantiza' ),
				'footer'    => __( 'Menú del pie', 'garantiza' ),
				'legal'     => __( 'Enlaces legales', 'garantiza' ),
			)
		);

		load_theme_textdomain( 'garantiza', GZ_DIR . '/languages' );
	}
);

if ( ! defined( 'DISALLOW_FILE_EDIT' ) ) {
	define( 'DISALLOW_FILE_EDIT', true );
}
