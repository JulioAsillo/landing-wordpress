<?php
/**
 * Title: Clientes
 * Slug: landing/clientes
 * Categories: landing
 * Description: Franja de logos de clientes.
 * Keywords: clientes, logos, marcas
 * Viewport Width: 1400
 */
?>
<!-- wp:group {"align":"full","style":{"spacing":{"padding":{"top":"var:preset|spacing|60","bottom":"var:preset|spacing|60"}}},"layout":{"type":"constrained","wideSize":"1180px"}} -->
<div class="wp-block-group alignfull" style="padding-top:var(--wp--preset--spacing--60);padding-bottom:var(--wp--preset--spacing--60)">

	<!-- wp:heading {"textAlign":"center","level":2} -->
	<h2 class="wp-block-heading has-text-align-center">Clientes</h2>
	<!-- /wp:heading -->

	<!-- wp:group {"align":"wide","className":"logo-grid","style":{"spacing":{"margin":{"top":"var:preset|spacing|50"},"blockGap":"var:preset|spacing|50"}},"layout":{"type":"flex","justifyContent":"center","flexWrap":"wrap"}} -->
	<div class="wp-block-group alignwide logo-grid" style="margin-top:var(--wp--preset--spacing--50)">

		<!-- wp:image {"className":"client-logo"} -->
        <figure class="wp-block-image client-logo"><img src="<?php echo esc_url( get_template_directory_uri() . '/assets/img/placeholder-logo.svg' ); ?>" alt="" width="320" height="120"/></figure>
		<!-- /wp:image -->

		<!-- wp:image {"className":"client-logo"} -->
        <figure class="wp-block-image client-logo"><img src="<?php echo esc_url( get_template_directory_uri() . '/assets/img/placeholder-logo.svg' ); ?>" alt="" width="320" height="120"/></figure>
		<!-- /wp:image -->

		<!-- wp:image {"className":"client-logo"} -->
        <figure class="wp-block-image client-logo"><img src="<?php echo esc_url( get_template_directory_uri() . '/assets/img/placeholder-logo.svg' ); ?>" alt="" width="320" height="120"/></figure>
        <!-- /wp:image -->

		<!-- wp:image {"className":"client-logo"} -->
        <figure class="wp-block-image client-logo"><img src="<?php echo esc_url( get_template_directory_uri() . '/assets/img/placeholder-logo.svg' ); ?>" alt="" width="320" height="120"/></figure>
		<!-- /wp:image -->

	</div>
	<!-- /wp:group -->

</div>
<!-- /wp:group -->
