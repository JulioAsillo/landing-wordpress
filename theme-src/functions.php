<?php
/**
 * Tema Landing Novaly — bootstrap.
 *
 * Este archivo solo carga módulos. La lógica vive en /inc para que
 * cada preocupación quede aislada y sea fácil de revisar.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

define( 'LANDING_VERSION', wp_get_theme()->get( 'Version' ) );
define( 'LANDING_DIR', get_template_directory() );
define( 'LANDING_URI', get_template_directory_uri() );

require_once LANDING_DIR . '/inc/setup.php';
require_once LANDING_DIR . '/inc/assets.php';
require_once LANDING_DIR . '/inc/performance.php';
require_once LANDING_DIR . '/inc/images.php';
require_once LANDING_DIR . '/inc/patterns.php';
