<?php
/**
 * Cabecera del sitio.
 */
?><!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>
	<meta charset="<?php bloginfo( 'charset' ); ?>">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<?php wp_head(); ?>
</head>
<body <?php body_class(); ?>>
<?php wp_body_open(); ?>

<a class="gz-skip-link" href="#contenido"><?php esc_html_e( 'Saltar al contenido', 'garantiza' ); ?></a>

<header class="gz-header">
	<div class="gz-container gz-header__inner">

		<?php if ( has_custom_logo() ) : ?>
			<?php the_custom_logo(); ?>
		<?php else : ?>
			<a class="gz-brand" href="<?php echo esc_url( home_url( '/' ) ); ?>">
				<?php bloginfo( 'name' ); ?>
			</a>
		<?php endif; ?>

		<button class="gz-nav-toggle" type="button"
				aria-expanded="false" aria-controls="gz-nav"
				data-nav-toggle>
			<span class="gz-sr-only"><?php esc_html_e( 'Abrir menú', 'garantiza' ); ?></span>
			<svg width="26" height="26" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true">
				<path d="M3 6h18M3 12h18M3 18h18"/>
			</svg>
		</button>

		<nav class="gz-nav" id="gz-nav" aria-label="<?php esc_attr_e( 'Menú principal', 'garantiza' ); ?>">
			<?php
			wp_nav_menu(
				array(
					'theme_location' => 'principal',
					'container'      => false,
					'depth'          => 1,
					'fallback_cb'    => false,
				)
			);
			?>
		</nav>

		<a class="gz-btn gz-btn--primary gz-header__cta" href="#contacto">
			<?php esc_html_e( 'Solicitar evaluación', 'garantiza' ); ?>
		</a>

	</div>
</header>

<main id="contenido">
