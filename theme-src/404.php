<?php
get_header();
?>
<div class="gz-section">
	<div class="gz-container">
		<h1><?php esc_html_e( 'Esta página no existe', 'garantiza' ); ?></h1>
		<p class="gz-lead"><?php esc_html_e( 'El enlace puede estar mal escrito o el contenido ya no está disponible.', 'garantiza' ); ?></p>
		<p><a class="gz-btn gz-btn--solid" href="<?php echo esc_url( home_url( '/' ) ); ?>"><?php esc_html_e( 'Volver al inicio', 'garantiza' ); ?></a></p>
	</div>
</div>
<?php
get_footer();
