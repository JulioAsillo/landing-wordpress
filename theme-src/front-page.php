<?php
/**
 * Portada de la landing.
 *
 * Compone las secciones de la maqueta. Cada una vive en su propio
 * archivo bajo /template-parts/sections y lee su contenido de
 * /inc/content.php, no de literales, para que la capa administrable se
 * pueda enchufar sin tocar el marcado.
 */

get_header();

// 'clients' retirada a pedido del cliente (v0.3.0); la plantilla se conserva.
$gz_sections = array( 'hero', 'statistics', 'services', 'comparison', 'contact' );

/**
 * Permite reordenar o quitar secciones sin editar esta plantilla.
 */
$gz_sections = apply_filters( 'gz_front_page_sections', $gz_sections );

foreach ( $gz_sections as $gz_section ) {
	get_template_part( 'template-parts/sections/' . $gz_section );
}

get_footer();
