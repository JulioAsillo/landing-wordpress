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

/**
 * El hero de la maqueta es una fotografía a sangre detrás del texto, no
 * una imagen en columna. Por eso los recortes son apaisados y anchos.
 */
add_action(
	'after_setup_theme',
	function () {
		add_image_size( 'gz-hero-sm', 900, 700, true );
		add_image_size( 'gz-hero-md', 1400, 800, true );
		add_image_size( 'gz-hero-lg', 2000, 1000, true );
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
 * Fotografía de fondo del hero.
 *
 * Es la imagen del LCP, así que se resuelve aquí y no con un filtro
 * global: en un tema clásico sabemos exactamente cuál es.
 *
 * El cliente la sustituye desde Apariencia > Personalizar sin tocar
 * código; mientras tanto se usa la que trae la maqueta.
 *
 * @param int $attachment_id ID del adjunto elegido en el personalizador.
 * @return string
 */
function gz_hero_image( $attachment_id = 0 ) {
	if ( $attachment_id ) {
		return wp_get_attachment_image(
			$attachment_id,
			'gz-hero-lg',
			false,
			array(
				'class'         => 'gz-hero__photo',
				'alt'           => '',
				'fetchpriority' => 'high',
				'decoding'      => 'sync',
				'loading'       => 'eager',
				'sizes'         => '100vw',
			)
		);
	}

	return sprintf(
		'<img src="%s" alt="" width="1400" height="593" class="gz-hero__photo" fetchpriority="high" decoding="sync" loading="eager">',
		esc_url( GZ_URI . '/assets/img/hero-garantiza.jpg' )
	);
}

/**
 * Registra el control del personalizador para la imagen del hero.
 * Un solo control: no tiene sentido pedirle al cliente que entre a
 * editar plantillas para cambiar una foto.
 */
add_action(
	'customize_register',
	function ( $wp_customize ) {
		$wp_customize->add_section(
			'gz_landing',
			array(
				'title'       => __( 'Landing Garantiza', 'garantiza' ),
				'priority'    => 30,
				'description' => __( 'Ajustes de la portada.', 'garantiza' ),
			)
		);

		$wp_customize->add_setting(
			'gz_hero_image_id',
			array(
				'default'           => 0,
				'sanitize_callback' => 'absint',
				'transport'         => 'refresh',
			)
		);

		$wp_customize->add_control(
			new WP_Customize_Media_Control(
				$wp_customize,
				'gz_hero_image_id',
				array(
					'label'       => __( 'Fotografía del hero', 'garantiza' ),
					'description' => __( 'Apaisada, mínimo 2000 px de ancho. El texto se lee sobre el lado izquierdo.', 'garantiza' ),
					'section'     => 'gz_landing',
					'mime_type'   => 'image',
				)
			)
		);

		$wp_customize->add_setting(
			'gz_form_id',
			array(
				'default'           => 0,
				'sanitize_callback' => 'absint',
				'transport'         => 'refresh',
			)
		);

		$wp_customize->add_control(
			'gz_form_id',
			array(
				'label'       => __( 'ID del formulario de Fluent Forms', 'garantiza' ),
				'description' => __( 'Mientras esté en 0 se muestra la vista previa del formulario de la maqueta.', 'garantiza' ),
				'section'     => 'gz_landing',
				'type'        => 'number',
			)
		);
	}
);
