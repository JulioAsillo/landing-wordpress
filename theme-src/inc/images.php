<?php
/**
 * Imágenes: tamaños, compresión y prioridad de carga.
 * Este archivo sostiene el compromiso de Core Web Vitals del plan.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/* Menos variantes generadas: menos disco y menos trabajo en cada subida. */
add_filter(
	'intermediate_image_sizes_advanced',
	function ( $sizes ) {
		unset( $sizes['medium_large'], $sizes['1536x1536'], $sizes['2048x2048'] );
		return $sizes;
	}
);

add_action(
	'after_setup_theme',
	function () {
		add_image_size( 'gz-hero-sm', 720, 540, true );
		add_image_size( 'gz-hero-md', 1100, 825, true );
		add_image_size( 'gz-hero-lg', 1600, 1200, true );
		add_image_size( 'gz-logo', 320, 0, false );
	}
);

/* 82 es donde la diferencia visual deja de percibirse y el peso baja. */
add_filter( 'jpeg_quality', fn() => 82 );
add_filter( 'wp_editor_set_quality', fn() => 82 );

/* Variantes en WebP. El original subido queda intacto. */
add_filter(
	'image_editor_output_format',
	function ( $formats ) {
		$formats['image/jpeg'] = 'image/webp';
		$formats['image/png']  = 'image/webp';
		return $formats;
	}
);

/* Solo el hero está sobre el pliegue en esta landing. */
add_filter( 'wp_omit_loading_attr_threshold', fn() => 1 );

/**
 * Devuelve el marcado de una imagen del hero con prioridad alta.
 *
 * Se resuelve aquí y no con un filtro global porque en un tema clásico
 * sabemos exactamente cuál es la imagen del LCP.
 */
function gz_hero_image( $attachment_id = 0, $fallback = '' ) {
	if ( $attachment_id ) {
		return wp_get_attachment_image(
			$attachment_id,
			'gz-hero-md',
			false,
			array(
				'class'         => 'gz-hero__img',
				'fetchpriority' => 'high',
				'decoding'      => 'sync',
				'loading'       => 'eager',
				'sizes'         => '(max-width: 899px) 100vw, 46vw',
			)
		);
	}

	$src = $fallback ? $fallback : GZ_URI . '/assets/img/placeholder-hero.svg';

	return sprintf(
		'<img src="%s" alt="" width="1100" height="825" class="gz-hero__img" fetchpriority="high" decoding="sync" loading="eager">',
		esc_url( $src )
	);
}
