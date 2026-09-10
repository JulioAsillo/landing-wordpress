<?php
/**
 * Tema Garantiza — bootstrap.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

define( 'GZ_VERSION', wp_get_theme()->get( 'Version' ) );
define( 'GZ_DIR', get_template_directory() );
define( 'GZ_URI', get_template_directory_uri() );

require_once GZ_DIR . '/inc/setup.php';
require_once GZ_DIR . '/inc/assets.php';
require_once GZ_DIR . '/inc/performance.php';
require_once GZ_DIR . '/inc/images.php';
require_once GZ_DIR . '/inc/icons.php';
require_once GZ_DIR . '/inc/content.php';
