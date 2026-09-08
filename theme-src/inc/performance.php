<?php
/**
 * Reducción del peso que WordPress carga por defecto.
 *
 * Cada bloque de abajo quita algo que esta landing no usa. Si más adelante
 * se incorpora una función que dependa de alguno, se revierte el bloque
 * correspondiente y se documenta el motivo.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Emojis: ~15 KB de JS más una hoja de estilos, para sustituir emojis que
 * los navegadores actuales ya renderizan de forma nativa.
 */
add_action(
	'init',
	function () {
		remove_action( 'wp_head', 'print_emoji_detection_script', 7 );
		remove_action( 'admin_print_scripts', 'print_emoji_detection_script' );
		remove_action( 'wp_print_styles', 'print_emoji_styles' );
		remove_action( 'admin_print_styles', 'print_emoji_styles' );
		remove_filter( 'the_content_feed', 'wp_staticize_emoji' );
		remove_filter( 'comment_text_rss', 'wp_staticize_emoji' );
		remove_filter( 'wp_mail', 'wp_staticize_emoji_for_email' );
	}
);

add_filter( 'emoji_svg_url', '__return_false' );

/**
 * Hojas de estilo del núcleo que theme.json ya cubre.
 *
 * OJO: no se quita 'wp-block-library'. Esa sí es necesaria para el layout
 * de los bloques (columnas, group, cover). Quitarla rompe la maqueta.
 */
add_action(
	'wp_enqueue_scripts',
	function () {
		wp_dequeue_style( 'wp-block-library-theme' ); // Estilos opinables del tema por defecto.
		wp_dequeue_style( 'classic-theme-styles' );   // Compatibilidad con temas clásicos.
	},
	20
);

/**
 * Cabeceras del <head> que exponen información y no aportan a la landing.
 */
add_action(
	'init',
	function () {
		remove_action( 'wp_head', 'wp_generator' );                      // Versión de WP.
		remove_action( 'wp_head', 'wlwmanifest_link' );                  // Windows Live Writer.
		remove_action( 'wp_head', 'rsd_link' );                          // Really Simple Discovery.
		remove_action( 'wp_head', 'wp_shortlink_wp_head' );
		remove_action( 'wp_head', 'feed_links_extra', 3 );               // Feeds por categoría.
		remove_action( 'wp_head', 'adjacent_posts_rel_link_wp_head', 10 );
	}
);

/**
 * XML-RPC: superficie de ataque para fuerza bruta y sin uso en esta landing.
 */
add_filter( 'xmlrpc_enabled', '__return_false' );

/**
 * jQuery Migrate: solo existe para compatibilidad con código antiguo.
 */
add_action(
	'wp_default_scripts',
	function ( $scripts ) {
		if ( is_admin() || empty( $scripts->registered['jquery'] ) ) {
			return;
		}
		$scripts->registered['jquery']->deps = array_diff(
			$scripts->registered['jquery']->deps,
			array( 'jquery-migrate' )
		);
	}
);

/**
 * Comentarios: la landing no los usa. Se desactivan de raíz para no
 * arrastrar sus estilos ni sus scripts.
 */
add_filter( 'comments_open', '__return_false', 20 );
add_filter( 'pings_open', '__return_false', 20 );

add_action(
	'wp_enqueue_scripts',
	function () {
		wp_dequeue_script( 'comment-reply' );
	},
	25
);
