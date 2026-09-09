<?php
/**
 * Encolado de recursos.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

add_action(
	'wp_enqueue_scripts',
	function () {
		$css = GZ_DIR . '/style.css';
		wp_enqueue_style(
			'garantiza',
			get_stylesheet_uri(),
			array(),
			file_exists( $css ) ? (string) filemtime( $css ) : GZ_VERSION
		);

		$js = GZ_DIR . '/assets/js/site.js';
		if ( file_exists( $js ) ) {
			wp_enqueue_script(
				'garantiza',
				GZ_URI . '/assets/js/site.js',
				array(),
				(string) filemtime( $js ),
				array( 'strategy' => 'defer', 'in_footer' => true )
			);
		}
	}
);

/**
 * Precarga solo de la variante que aparece en el primer pintado.
 * Precargar todas compite con la imagen del hero y empeora el LCP.
 */
add_action(
	'wp_head',
	function () {
		$fonts = array(
			'/assets/fonts/barlow-400.woff2',
			'/assets/fonts/barlow-condensed-700.woff2',
		);
		foreach ( $fonts as $f ) {
			if ( file_exists( GZ_DIR . $f ) ) {
				printf(
					'<link rel="preload" href="%s" as="font" type="font/woff2" crossorigin>' . "\n",
					esc_url( GZ_URI . $f )
				);
			}
		}
	},
	1
);
