<?php
/**
 * Encolado de recursos.
 *
 * Regla del proyecto: una sola hoja de estilos propia, versionada por
 * filemtime para que el caché largo de Nginx no sirva una versión vieja.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

add_action(
	'wp_enqueue_scripts',
	function () {
		$path = LANDING_DIR . '/assets/css/theme.css';

		wp_enqueue_style(
			'landing-theme',
			LANDING_URI . '/assets/css/theme.css',
			array(),
			file_exists( $path ) ? (string) filemtime( $path ) : LANDING_VERSION
		);
	}
);

/**
 * Precarga de la tipografía del primer pintado.
 *
 * Solo la variante 400. Precargar todas las variantes compite por ancho
 * de banda con la imagen del hero y termina empeorando el LCP.
 */
add_action(
	'wp_head',
	function () {
		$font = LANDING_URI . '/assets/fonts/landing-sans-400.woff2';
		printf(
			'<link rel="preload" href="%s" as="font" type="font/woff2" crossorigin>' . "\n",
			esc_url( $font )
		);
	},
	1
);
