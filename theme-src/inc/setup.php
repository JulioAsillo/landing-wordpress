<?php
/**
 * Soporte del tema y ajustes del editor.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

add_action(
	'after_setup_theme',
	function () {
		// El <title> lo gestiona WordPress, no el tema.
		add_theme_support( 'title-tag' );

		// Imágenes destacadas (las secciones las usan vía patterns).
		add_theme_support( 'post-thumbnails' );

		// Marcado HTML5 limpio en formularios y comentarios.
		add_theme_support(
			'html5',
			array( 'search-form', 'comment-form', 'comment-list', 'gallery', 'caption', 'style', 'script' )
		);

		// Estilos base de los bloques del núcleo.
		add_theme_support( 'wp-block-styles' );

		// Embeds e imágenes que se adaptan al contenedor: evita desbordes en móvil.
		add_theme_support( 'responsive-embeds' );

		// Traducciones.
		load_theme_textdomain( 'landing', LANDING_DIR . '/languages' );
	}
);

/**
 * El cliente edita CONTENIDO, no la línea visual.
 *
 * Se oculta el panel de estilos globales para los roles que no son
 * administrador técnico. Si prefieres abrirlo, elimina este bloque.
 */
add_filter(
	'block_editor_settings_all',
	function ( $settings ) {
		if ( ! current_user_can( 'manage_options' ) ) {
			$settings['__experimentalFeatures']['color']['custom']      = false;
			$settings['__experimentalFeatures']['typography']['customFontSize'] = false;
		}
		return $settings;
	}
);

/**
 * Refuerzo por código de lo ya aplicado en el servidor:
 * sin editor de archivos desde el panel.
 */
if ( ! defined( 'DISALLOW_FILE_EDIT' ) ) {
	define( 'DISALLOW_FILE_EDIT', true );
}
