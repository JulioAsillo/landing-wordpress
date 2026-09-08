<?php
/**
 * Imágenes: tamaños, compresión y prioridad de carga.
 *
 * Este archivo es el que sostiene el compromiso de Core Web Vitals del
 * plan de trabajo. Tres frentes: menos variantes generadas (disco),
 * mejor compresión (peso) y prioridad correcta (LCP).
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/* -------------------------------------------------------------------------
 * 1. Tamaños generados
 * ---------------------------------------------------------------------- */

/**
 * WordPress genera por defecto un abanico de tamaños que esta landing no
 * usa, y cada uno ocupa disco. Se recortan los intermedios del núcleo.
 */
add_filter(
	'intermediate_image_sizes_advanced',
	function ( $sizes ) {
		unset( $sizes['medium_large'] ); // 768px, sin uso en esta maqueta.
		unset( $sizes['1536x1536'] );    // Retina 2x de 'large'.
		unset( $sizes['2048x2048'] );    // Retina 2x de 'large'.
		return $sizes;
	}
);

/**
 * Tamaños propios, alineados a los anchos reales de la maqueta.
 * Definirlos aquí es lo que permite que srcset ofrezca opciones útiles
 * en lugar de servir siempre la imagen completa.
 */
add_action(
	'after_setup_theme',
	function () {
		// Hero: ancho completo del contenedor 'wide'.
		add_image_size( 'landing-hero-sm', 640, 0, false );
		add_image_size( 'landing-hero-md', 1180, 0, false );
		add_image_size( 'landing-hero-lg', 1800, 0, false );

		// Tarjetas de servicios y logos de clientes.
		add_image_size( 'landing-card', 560, 0, false );
		add_image_size( 'landing-logo', 320, 0, false );
	}
);

/* -------------------------------------------------------------------------
 * 2. Compresión
 * ---------------------------------------------------------------------- */

/**
 * 82 es el punto donde la diferencia visual deja de ser perceptible
 * en fotografía y el peso baja de forma notoria frente al 90 por defecto.
 */
add_filter( 'jpeg_quality', fn() => 82 );
add_filter( 'wp_editor_set_quality', fn() => 82 );

/**
 * Salida en WebP para las variantes generadas. El original subido se
 * conserva intacto; solo cambian los tamaños derivados que sirve el sitio.
 *
 * Requiere que la extensión de imagen del contenedor soporte WebP
 * (la imagen base de PHP 8.4 del stack ya lo trae).
 */
add_filter(
	'image_editor_output_format',
	function ( $formats ) {
		$formats['image/jpeg'] = 'image/webp';
		$formats['image/png']  = 'image/webp';
		return $formats;
	}
);

/* -------------------------------------------------------------------------
 * 3. Prioridad de carga  <- el punto crítico del LCP
 * ---------------------------------------------------------------------- */

/**
 * WordPress decide solo a qué imágenes pone loading="lazy" y a cuál le da
 * fetchpriority="high". Su heurística se equivoca con frecuencia en
 * landings de una sola página: manda el hero a lazy y el LCP se degrada
 * varios cientos de milisegundos.
 *
 * Aquí se toma el control explícito. Toda imagen dentro de un bloque con
 * la clase .is-hero se carga con prioridad alta y sin lazy; el resto
 * mantiene el comportamiento diferido.
 */
/**
 * Marca la primera imagen del contenido como candidata a LCP.
 *
 * WordPress ya omite el lazy en las primeras imágenes según
 * 'wp_omit_loading_attr_threshold'. Se fija en 1 porque en esta landing
 * la única imagen sobre el pliegue es la del hero.
 */
add_filter( 'wp_omit_loading_attr_threshold', fn() => 1 );

/**
 * Refuerzo a nivel de bloque: cualquier core/image dentro de un
 * contenedor .is-hero recibe fetchpriority alto y pierde el lazy,
 * sin importar en qué posición del DOM haya quedado.
 */
add_filter(
	'render_block',
	function ( $block_content, $block ) {
		if ( 'core/image' !== ( $block['blockName'] ?? '' ) ) {
			return $block_content;
		}

		$classes = $block['attrs']['className'] ?? '';
		if ( ! str_contains( $classes, 'is-hero' ) ) {
			return $block_content;
		}

		$content = str_replace( ' loading="lazy"', '', $block_content );

		if ( ! str_contains( $content, 'fetchpriority' ) ) {
			$content = str_replace( '<img ', '<img fetchpriority="high" decoding="sync" ', $content );
		}

		return $content;
	},
	10,
	2
);

/* -------------------------------------------------------------------------
 * 4. Estabilidad visual (CLS)
 * ---------------------------------------------------------------------- */

/**
 * Garantiza que ninguna imagen salga sin width/height. Sin esos atributos
 * el navegador no reserva espacio y la página salta al cargar: es la
 * causa más común de un CLS malo.
 */
add_filter(
	'wp_get_attachment_image_attributes',
	function ( $attr, $attachment, $size ) {
		if ( empty( $attr['width'] ) || empty( $attr['height'] ) ) {
			$meta = wp_get_attachment_image_src( $attachment->ID, $size );
			if ( $meta ) {
				$attr['width']  = $meta[1];
				$attr['height'] = $meta[2];
			}
		}
		return $attr;
	},
	10,
	3
);
