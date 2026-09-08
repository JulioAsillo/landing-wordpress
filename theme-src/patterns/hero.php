<?php
/**
 * Title: Hero
 * Slug: landing/hero
 * Categories: landing
 * Description: Sección de apertura con titular, bajada, botón e imagen principal.
 * Keywords: hero, portada, cabecera
 * Viewport Width: 1400
 */
?>
<!-- wp:group {"align":"full","className":"is-hero","style":{"spacing":{"padding":{"top":"var:preset|spacing|60","bottom":"var:preset|spacing|60"}}},"backgroundColor":"surface-alt","layout":{"type":"constrained","wideSize":"1180px"}} -->
<div class="wp-block-group alignfull is-hero has-surface-alt-background-color has-background" style="padding-top:var(--wp--preset--spacing--60);padding-bottom:var(--wp--preset--spacing--60)">

	<!-- wp:columns {"align":"wide","verticalAlignment":"center"} -->
	<div class="wp-block-columns alignwide are-vertically-aligned-center">

		<!-- wp:column {"verticalAlignment":"center","width":"48%"} -->
		<div class="wp-block-column is-vertically-aligned-center" style="flex-basis:48%">

			<!-- wp:heading {"level":1,"fontSize":"xx-large"} -->
			<h1 class="wp-block-heading has-xx-large-font-size">Titular principal de la empresa</h1>
			<!-- /wp:heading -->

			<!-- wp:paragraph {"textColor":"text-muted","fontSize":"large"} -->
			<p class="has-text-muted-color has-text-color has-large-font-size">Una o dos líneas que expliquen en lenguaje simple qué hace la empresa y para quién.</p>
			<!-- /wp:paragraph -->

			<!-- wp:buttons {"style":{"spacing":{"margin":{"top":"var:preset|spacing|40"}}}} -->
			<div class="wp-block-buttons" style="margin-top:var(--wp--preset--spacing--40)">

				<div class="wp-block-button"><a class="wp-block-button__link wp-element-button" href="#contacto">Conversemos</a></div>

			</div>


		</div>

		<div class="wp-block-column is-vertically-aligned-center">
            <figure class="wp-block-image is-hero"><img src="<?php echo esc_url( get_template_directory_uri() . '/assets/img/placeholder-hero.svg' ); ?>" alt="" width="1180" height="740"/></figure>

		</div>


	</div>
	<!-- /wp:columns -->

</div>
<!-- /wp:group -->
