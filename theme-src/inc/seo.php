<?php
/**
 * SEO on-page.
 *
 * El tema declara add_theme_support( 'title-tag' ), así que WordPress
 * arma el <title> con el nombre del sitio y el eslogan. Con el nombre
 * solo ("Garantiza") el título queda en 9 caracteres, sin servicio ni
 * país: no describe la página y Lighthouse lo penaliza (QA BUG-008).
 *
 * Aquí se fija el título de la portada, la descripción y las tarjetas
 * para redes desde el código, en lugar de depender de opciones que se
 * pueden cambiar sin darse cuenta desde el panel. El resto de páginas
 * conserva el título nativo de WordPress y solo recibe la marca.
 *
 * Todo con hooks nativos: no se añade ningún plugin de SEO.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Textos de SEO. Filtrables por si el cliente quiere ajustarlos sin
 * tocar esta plantilla.
 */
function gz_seo() {
	return apply_filters(
		'gz_seo',
		array(
			/* 61 caracteres: entra completo en el resultado de búsqueda. */
			'title'       => 'Garantiza | Gestión integral de garantías mobiliarias',
			'description' => 'Constitución, ejecución, incautación y venta del activo: gestionamos el ciclo completo de las garantías mobiliarias de bancos, financieras y cajas, con cobertura en todo el Perú.',
			/* Separador y marca para las páginas interiores. */
			'brand'       => 'Garantiza',
			'og_image'    => GZ_URI . '/assets/img/hero-garantiza.jpg',
		)
	);
}

/**
 * Descripción de la página actual.
 * En la portada, la de marca; en una página interior, su extracto si lo
 * tiene, para no repetir el mismo texto en todo el sitio.
 */
function gz_seo_description() {
	$seo = gz_seo();

	if ( is_front_page() || ! is_singular() ) {
		return $seo['description'];
	}

	$excerpt = get_the_excerpt();

	return $excerpt ? wp_html_excerpt( $excerpt, 160, '…' ) : $seo['description'];
}

/**
 * Título del documento.
 *
 * Portada: título de marca completo, sin eslogan detrás.
 * Interiores: título de la página + marca (Política de privacidad |
 * Garantiza), que es lo que se espera en un resultado de búsqueda.
 */
add_filter(
	'document_title_parts',
	function ( $parts ) {
		$seo = gz_seo();

		if ( is_front_page() ) {
			$parts['title'] = $seo['title'];
			unset( $parts['tagline'], $parts['site'] );
			return $parts;
		}

		$parts['site'] = $seo['brand'];
		unset( $parts['tagline'] );

		return $parts;
	}
);

/** Separador coherente con el título de la portada. */
add_filter( 'document_title_separator', fn() => '|' );

/**
 * Descripción, canónica y tarjetas para redes.
 *
 * Prioridad 4 para quedar por encima de las precargas de tipografías y
 * por debajo de la etiqueta charset.
 */
add_action(
	'wp_head',
	function () {
		if ( is_404() || is_search() ) {
			return;
		}

		$seo         = gz_seo();
		$descripcion = gz_seo_description();
		$titulo      = wp_get_document_title();
		$url         = is_singular() ? get_permalink() : home_url( '/' );

		printf( '<meta name="description" content="%s">' . "\n", esc_attr( $descripcion ) );
		printf( '<link rel="canonical" href="%s">' . "\n", esc_url( $url ) );

		printf( '<meta property="og:type" content="website">' . "\n" );
		printf( '<meta property="og:locale" content="%s">' . "\n", esc_attr( get_locale() ) );
		printf( '<meta property="og:site_name" content="%s">' . "\n", esc_attr( $seo['brand'] ) );
		printf( '<meta property="og:title" content="%s">' . "\n", esc_attr( $titulo ) );
		printf( '<meta property="og:description" content="%s">' . "\n", esc_attr( $descripcion ) );
		printf( '<meta property="og:url" content="%s">' . "\n", esc_url( $url ) );
		printf( '<meta property="og:image" content="%s">' . "\n", esc_url( $seo['og_image'] ) );

		printf( '<meta name="twitter:card" content="summary_large_image">' . "\n" );
		printf( '<meta name="twitter:title" content="%s">' . "\n", esc_attr( $titulo ) );
		printf( '<meta name="twitter:description" content="%s">' . "\n", esc_attr( $descripcion ) );
		printf( '<meta name="twitter:image" content="%s">' . "\n", esc_url( $seo['og_image'] ) );
	},
	4
);

/**
 * Datos estructurados de la organización.
 *
 * Solo en la portada y solo con los datos verificados por el cliente. Los
 * canales de contacto (teléfono, WhatsApp) se añadirán aquí cuando el
 * cliente los entregue.
 */
add_action(
	'wp_head',
	function () {
		if ( ! is_front_page() ) {
			return;
		}

		$seo = gz_seo();

		$datos = array(
			'@context'    => 'https://schema.org',
			'@type'       => 'Organization',
			'name'        => 'GARANTIZA S.A.C.',
			'alternateName' => $seo['brand'],
			'url'         => home_url( '/' ),
			'logo'        => GZ_URI . '/assets/img/logo-garantiza.svg',
			'description' => $seo['description'],
			'address'     => array(
				'@type'           => 'PostalAddress',
				'streetAddress'   => 'Calle Augusto Tamayo N° 154, Oficina 403',
				'addressLocality' => 'San Isidro',
				'addressRegion'   => 'Lima',
				'addressCountry'  => 'PE',
			),
			'areaServed'  => array(
				'@type' => 'Country',
				'name'  => 'Perú',
			),
		);

		printf(
			'<script type="application/ld+json">%s</script>' . "\n",
			wp_json_encode( $datos, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE )
		);
	},
	5
);
