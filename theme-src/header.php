<?php
/**
 * Cabecera del sitio.
 */
?><!DOCTYPE html>
<html <?php language_attributes(); ?> class="no-js">
<head>
	<meta charset="<?php bloginfo( 'charset' ); ?>">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<?php wp_head(); ?>
</head>
<body <?php body_class(); ?>>
<?php wp_body_open(); ?>

<a class="gz-skip-link" href="#contenido"><?php esc_html_e( 'Saltar al contenido', 'garantiza' ); ?></a>

<header class="gz-header" data-header>
	<div class="gz-wrap gz-wrap--wide gz-header__inner">

		<?php
		if ( has_custom_logo() ) {
			the_custom_logo();
		} else {
			echo gz_logo(); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- marcado propio ya escapado.
		}
		?>

		<nav class="gz-nav" id="gz-nav" aria-label="<?php esc_attr_e( 'Menú principal', 'garantiza' ); ?>">
			<?php
			if ( has_nav_menu( 'principal' ) ) {
				wp_nav_menu(
					array(
						'theme_location' => 'principal',
						'container'      => false,
						'depth'          => 1,
						'fallback_cb'    => false,
					)
				);
			} else {
				/* Respaldo mientras el menú no esté asignado: las mismas
				   anclas de la maqueta, para que la navegación funcione. */
				echo '<ul>';
				foreach ( gz_nav_fallback_links() as $link ) {
					printf(
						'<li><a href="%s">%s</a></li>',
						esc_url( $link['url'] ),
						esc_html( $link['label'] )
					);
				}
				echo '</ul>';
			}
			?>
		</nav>

		<div class="gz-header__actions">
			<a class="gz-btn gz-btn--outline" href="#contacto">
				<?php esc_html_e( 'Solicita información', 'garantiza' ); ?>
			</a>
			<button class="gz-nav-toggle" type="button"
					aria-label="<?php esc_attr_e( 'Abrir menú', 'garantiza' ); ?>"
					aria-expanded="false" aria-controls="gz-nav"
					data-nav-toggle>
				<span></span><span></span><span></span>
			</button>
		</div>

	</div>
</header>

<main id="contenido">
