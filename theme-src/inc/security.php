<?php
/**
 * Endurecimiento de WordPress a nivel de aplicación.
 *
 * Complementa las reglas de Nginx (cabeceras, límite de intentos de
 * inicio de sesión y bloqueo de archivos sensibles). Todo se resuelve con
 * hooks nativos, sin plugins.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Enumeración de usuarios por la REST API.
 * Los endpoints de usuarios quedan disponibles solo con sesión iniciada,
 * porque el editor del panel los necesita.
 */
add_filter(
	'rest_endpoints',
	function ( $endpoints ) {
		if ( is_user_logged_in() ) {
			return $endpoints;
		}
		unset( $endpoints['/wp/v2/users'], $endpoints['/wp/v2/users/(?P<id>[\d]+)'] );
		return $endpoints;
	}
);

/**
 * Enumeración de usuarios por ?author=N y archivos de autor.
 * Prioridad 1: antes de redirect_canonical, que revelaría el slug del
 * usuario en la cabecera Location.
 */
add_action(
	'template_redirect',
	function () {
		if ( is_admin() || is_user_logged_in() ) {
			return;
		}
		// phpcs:ignore WordPress.Security.NonceVerification.Recommended -- solo se comprueba la presencia del parámetro.
		if ( isset( $_GET['author'] ) || is_author() ) {
			wp_safe_redirect( home_url( '/' ), 301 );
			exit;
		}
	},
	1
);

/** El sitemap nativo no publica el listado de usuarios. */
add_filter(
	'wp_sitemaps_add_provider',
	function ( $provider, $name ) {
		return 'users' === $name ? false : $provider;
	},
	10,
	2
);

/** oEmbed no expone el nombre ni la URL del autor. */
add_filter(
	'oembed_response_data',
	function ( $data ) {
		unset( $data['author_name'], $data['author_url'] );
		return $data;
	}
);

/**
 * Mensaje de error de inicio de sesión genérico: no confirma si el
 * usuario existe.
 */
add_filter(
	'login_errors',
	function () {
		return __( 'Usuario o contraseña incorrectos.', 'garantiza' );
	}
);
