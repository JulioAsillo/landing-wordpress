<?php
/**
 * Utilidades de enlaces.
 *
 * La landing navega por anclas (#servicios, #contacto…). En páginas
 * interiores —política de privacidad, 404— esas anclas no existen, así
 * que se reescriben hacia la portada. Los enlaces legales apuntan a la
 * página real si ya fue creada y a "#" mientras no.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Normaliza un enlace del tema.
 *
 * @param string $url URL, ancla (#seccion) o vacío.
 * @return string
 */
function gz_link_url( $url ) {
	$url = (string) $url;

	if ( strlen( $url ) > 1 && '#' === $url[0] && ! is_front_page() ) {
		return home_url( '/' . $url );
	}

	return $url;
}

/**
 * Enlace a una página por su slug, o "#" si todavía no existe.
 *
 * @param string $slug Slug de la página (p. ej. politica-de-privacidad).
 * @return string
 */
function gz_page_url( $slug ) {
	$page = get_page_by_path( $slug );

	if ( $page instanceof WP_Post && 'publish' === $page->post_status ) {
		return get_permalink( $page );
	}

	return '#';
}
