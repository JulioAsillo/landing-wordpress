<?php
/**
 * Iconos SVG en línea.
 *
 * Van en línea y no como sprite ni fuente de iconos: son pocos, pesan
 * menos que una petición extra y heredan el color con currentColor.
 * Centralizarlos aquí evita repetir el mismo path en varias plantillas.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Devuelve el marcado de un icono.
 *
 * @param string $name  Clave del icono.
 * @param string $class Clase CSS opcional para el <svg>.
 * @return string
 */
function gz_icon( $name, $class = '' ) {
	$paths = array(
		'book'        => '<path d="M5 4.5A1.5 1.5 0 016.5 3H19v15H6.5A1.5 1.5 0 005 19.5v-15zM5 19.5A1.5 1.5 0 006.5 21H19" stroke="currentColor" stroke-width="1.8" stroke-linejoin="round"/><path d="M9 8h6M9 11.5h4" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"/>',
		'clock'       => '<circle cx="12" cy="12" r="8.5" stroke="currentColor" stroke-width="1.8"/><path d="M12 7.5V12l3 2" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/>',
		'mail'        => '<rect x="3.5" y="5.5" width="17" height="13" rx="2" stroke="currentColor" stroke-width="1.8"/><path d="M4 7l8 6 8-6" stroke="currentColor" stroke-width="1.8" stroke-linejoin="round"/>',
		'arrow-right' => '<path d="M5 12h14M13 6l6 6-6 6" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>',
		'arrow-up'    => '<path d="M12 20V5M6 11l6-6 6 6" stroke="currentColor" stroke-width="2.4" stroke-linecap="round" stroke-linejoin="round"/>',
		'shield'      => '<path d="M12 3l7 3v6c0 4.5-3 8-7 9-4-1-7-4.5-7-9V6l7-3z" stroke="currentColor" stroke-width="1.8" stroke-linejoin="round"/>',
		'bolt'        => '<path d="M13 3L4 14h6l-1 7 9-11h-6l1-7z" stroke="currentColor" stroke-width="1.8" stroke-linejoin="round"/>',
		'chart'       => '<path d="M4 19h16M7 15l4-5 3 3 5-7" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/>',
		'grid'        => '<rect x="3" y="3" width="7" height="7" rx="1.5" stroke="currentColor" stroke-width="1.7"/><rect x="14" y="3" width="7" height="7" rx="1.5" stroke="currentColor" stroke-width="1.7"/><rect x="3" y="14" width="7" height="7" rx="1.5" stroke="currentColor" stroke-width="1.7"/><rect x="14" y="14" width="7" height="7" rx="1.5" stroke="currentColor" stroke-width="1.7"/>',
		'search'      => '<circle cx="11" cy="11" r="7" stroke="currentColor" stroke-width="1.7"/><path d="M21 21l-4.3-4.3" stroke="currentColor" stroke-width="1.7" stroke-linecap="round"/>',
		'check'       => '<path d="M5 13l4 4L19 7" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"/>',
		'cross'       => '<path d="M6 6l12 12M18 6L6 18" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>',
		'phone'       => '<path d="M3 5.5A2.5 2.5 0 0 1 5.5 3h1.6a1 1 0 0 1 .95.68l1.2 3.5a1 1 0 0 1-.27 1.06L7.4 9.7a13 13 0 0 0 6.9 6.9l1.46-1.58a1 1 0 0 1 1.06-.27l3.5 1.2a1 1 0 0 1 .68.95v1.6A2.5 2.5 0 0 1 18.5 21 15.5 15.5 0 0 1 3 5.5z" stroke="currentColor" stroke-width="1.6" stroke-linejoin="round"/>',
		'chevron'     => '<path d="M6 9l6 6 6-6" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>',
	);

	/* Sin clase no se emite el atributo, para no dejar class="" suelto. */
	$attr = $class ? sprintf( ' class="%s"', esc_attr( $class ) ) : '';

	/* LinkedIn se dibuja con relleno, no con trazo. */
	if ( 'linkedin' === $name ) {
		return sprintf(
			'<svg%s viewBox="0 0 24 24" fill="currentColor" aria-hidden="true"><path d="M6.94 5a1.94 1.94 0 1 1-3.88 0 1.94 1.94 0 0 1 3.88 0zM3.4 8.75h3.08V21H3.4V8.75zm5.6 0h2.95v1.68h.04c.41-.78 1.42-1.6 2.93-1.6 3.13 0 3.71 2.06 3.71 4.74V21h-3.08v-5.7c0-1.36-.02-3.1-1.89-3.1-1.9 0-2.19 1.48-2.19 3v5.8H9V8.75z"/></svg>',
			$attr
		);
	}

	if ( empty( $paths[ $name ] ) ) {
		return '';
	}

	return sprintf(
		'<svg%s viewBox="0 0 24 24" fill="none" aria-hidden="true">%s</svg>',
		$attr,
		$paths[ $name ]
	);
}

/**
 * Logotipo oficial de Garantiza (SVG aprobado por el cliente).
 *
 * Se usa cuando no hay logo subido por el personalizador. Cuando lo
 * suba, `the_custom_logo()` lo sustituye en la cabecera.
 *
 * @param bool $on_dark Variante con texto blanco para fondos oscuros (pie).
 * @return string
 */
function gz_logo( $on_dark = false ) {
	$file = $on_dark ? 'logo-garantiza-blanco.svg' : 'logo-garantiza.svg';
	$src  = get_template_directory_uri() . '/assets/img/' . $file;
	ob_start();
	?>
	<a class="gz-logo<?php echo $on_dark ? ' gz-logo--on-dark' : ''; ?>"
	   href="<?php echo esc_url( home_url( '/' ) ); ?>"
	   aria-label="<?php echo esc_attr( sprintf( '%s — inicio', get_bloginfo( 'name' ) ) ); ?>">
		<img class="gz-logo__img" src="<?php echo esc_url( $src ); ?>"
		     alt="<?php bloginfo( 'name' ); ?>" width="218" height="40" decoding="async">
	</a>
	<?php
	return (string) ob_get_clean();
}
