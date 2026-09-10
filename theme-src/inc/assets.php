<?php
/**
 * Encolado de recursos.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Familias que usa la maqueta y pesos realmente utilizados.
 * Barlow: 400 (cuerpo), 600 (etiquetas y navegación), 700 (destacados).
 * Barlow Condensed: 600 (cita del hero), 700 (todos los títulos).
 */
function gz_font_files() {
	return array(
		'barlow-400.woff2',
		'barlow-600.woff2',
		'barlow-700.woff2',
		'barlow-condensed-600.woff2',
		'barlow-condensed-700.woff2',
	);
}

/**
 * ¿Están las tipografías auto-alojadas?
 *
 * La ficha técnica pide auto-alojarlas para no depender de una petición
 * externa en el primer pintado. Mientras diseño no entregue los .woff2,
 * el tema cae a Google Fonts para que la maqueta se vea correcta. En
 * cuanto los archivos existan en /assets/fonts, el tema los usa solo y
 * deja de pedir nada fuera del dominio.
 */
function gz_fonts_are_local() {
	foreach ( gz_font_files() as $file ) {
		if ( ! file_exists( GZ_DIR . '/assets/fonts/' . $file ) ) {
			return false;
		}
	}
	return true;
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

		if ( ! gz_fonts_are_local() ) {
			wp_enqueue_style(
				'garantiza-fonts',
				'https://fonts.googleapis.com/css2?family=Barlow:wght@400;600;700&family=Barlow+Condensed:wght@600;700&display=swap',
				array(),
				null
			);
		}

		$js = GZ_DIR . '/assets/js/site.js';
		if ( file_exists( $js ) ) {
			wp_enqueue_script(
				'garantiza',
				GZ_URI . '/assets/js/site.js',
				array(),
				(string) filemtime( $js ),
				array(
					'strategy'  => 'defer',
					'in_footer' => true,
				)
			);
		}
	}
);

/**
 * Declaración de las tipografías auto-alojadas.
 * Va en línea porque son pocas reglas y evita un archivo CSS extra en la
 * ruta crítica.
 */
add_action(
	'wp_head',
	function () {
		if ( ! gz_fonts_are_local() ) {
			/* Sin tipografías locales, al menos abrimos la conexión antes. */
			echo '<link rel="preconnect" href="https://fonts.googleapis.com">' . "\n";
			echo '<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>' . "\n";
			return;
		}

		$faces = array(
			array( 'Barlow', 400, 'normal', 'barlow-400.woff2' ),
			array( 'Barlow', 600, 'normal', 'barlow-600.woff2' ),
			array( 'Barlow', 700, 'normal', 'barlow-700.woff2' ),
			array( 'Barlow Condensed', 600, 'normal', 'barlow-condensed-600.woff2' ),
			array( 'Barlow Condensed', 700, 'normal', 'barlow-condensed-700.woff2' ),
		);

		echo "<style id=\"garantiza-fonts\">\n";
		foreach ( $faces as $face ) {
			printf(
				"@font-face{font-family:\"%s\";font-style:%s;font-weight:%d;font-display:swap;src:url(%s) format(\"woff2\");}\n",
				esc_html( $face[0] ),
				esc_html( $face[2] ),
				(int) $face[1],
				esc_url( GZ_URI . '/assets/fonts/' . $face[3] )
			);
		}
		echo "</style>\n";
	},
	1
);

/**
 * Precarga solo de las dos variantes que aparecen en el primer pintado:
 * el cuerpo del hero y el título. Precargarlas todas compite con la
 * imagen del hero y empeora el LCP.
 */
add_action(
	'wp_head',
	function () {
		if ( ! gz_fonts_are_local() ) {
			return;
		}

		$critical = array( 'barlow-400.woff2', 'barlow-condensed-700.woff2' );

		foreach ( $critical as $file ) {
			printf(
				'<link rel="preload" href="%s" as="font" type="font/woff2" crossorigin>' . "\n",
				esc_url( GZ_URI . '/assets/fonts/' . $file )
			);
		}
	},
	2
);

/**
 * Marca el documento como "con JS" antes de que pinte.
 * Sin esto, los bloques con reveal quedarían invisibles si el script
 * falla o tarda.
 */
add_action(
	'wp_head',
	function () {
		echo '<script>document.documentElement.classList.remove("no-js");</script>' . "\n";
	},
	3
);
