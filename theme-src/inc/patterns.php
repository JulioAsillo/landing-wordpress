<?php
/**
 * Patterns del tema.
 *
 * WordPress registra automáticamente todos los .php de /patterns.
 * Aquí solo se declara la categoría bajo la que aparecen en el
 * insertador, para que el cliente los encuentre agrupados.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

add_action(
	'init',
	function () {
		register_block_pattern_category(
			'landing',
			array( 'label' => __( 'Secciones de la landing', 'landing' ) )
		);
	}
);
