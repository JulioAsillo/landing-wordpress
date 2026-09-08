<?php
/**
 * Title: Contacto
 * Slug: landing/contacto
 * Categories: landing
 * Description: Sección de contacto con el formulario de Fluent Forms.
 * Keywords: contacto, formulario
 * Viewport Width: 1400
 */
?>
<!-- wp:group {"align":"full","anchor":"contacto","style":{"spacing":{"padding":{"top":"var:preset|spacing|70","bottom":"var:preset|spacing|70"}}},"backgroundColor":"surface-alt","layout":{"type":"constrained"}} -->
<div id="contacto" class="wp-block-group alignfull has-surface-alt-background-color has-background" style="padding-top:var(--wp--preset--spacing--70);padding-bottom:var(--wp--preset--spacing--70)">

	<!-- wp:heading {"level":2} -->
	<h2 class="wp-block-heading">Contacto</h2>
	<!-- /wp:heading -->

	<!-- wp:paragraph {"textColor":"text-muted"} -->
	<p class="has-text-muted-color has-text-color">Déjanos tus datos y te escribimos.</p>
	<!-- /wp:paragraph -->

	<!-- wp:shortcode -->
	[fluentform id="1"]
	<!-- /wp:shortcode -->

</div>
<!-- /wp:group -->
