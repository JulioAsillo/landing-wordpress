<?php
/**
 * Libro de Reclamaciones.
 *
 * WordPress usa esta plantilla automáticamente para la página con slug
 * "libro-de-reclamaciones". Reutiliza la grilla y la tarjeta de Contacto,
 * así el formulario de Fluent Forms hereda los mismos estilos.
 *
 * El formulario sale del ID en Apariencia > Personalizar > Landing
 * Garantiza. Si está en 0, se muestra el contenido de la página (por
 * ejemplo, un shortcode pegado a mano).
 */

get_header();

$gz_claims_id = (int) get_theme_mod( 'gz_claims_form_id', 0 );
?>

<section class="gz-section gz-contact gz-claims">
	<div class="gz-wrap gz-wrap--wide gz-contact__grid">

		<div class="gz-contact__copy">
			<span class="gz-kicker"><?php esc_html_e( 'Atención al cliente', 'garantiza' ); ?></span>
			<h1><?php the_title(); ?></h1>
			<p class="gz-claims__lead">
				<?php esc_html_e( 'Registra tu reclamo o queja. Te enviaremos una copia con el número de hoja al correo que indiques.', 'garantiza' ); ?>
			</p>

			<div class="gz-claims__provider">
				<strong>GARANTIZA S.A.C.</strong>
				RUC N° 20613185608<br>
				Calle Augusto Tamayo N° 154, Oficina 403, San Isidro, Lima
			</div>

			<div class="gz-claims__types">
				<div class="gz-claims__type">
					<h4><?php esc_html_e( 'Reclamo', 'garantiza' ); ?></h4>
					<p><?php esc_html_e( 'Disconformidad relacionada con los productos o servicios.', 'garantiza' ); ?></p>
				</div>
				<div class="gz-claims__type">
					<h4><?php esc_html_e( 'Queja', 'garantiza' ); ?></h4>
					<p><?php esc_html_e( 'Disconformidad no relacionada con los productos o servicios, o malestar respecto a la atención al público.', 'garantiza' ); ?></p>
				</div>
			</div>

			<div class="gz-contact-info">
				<div class="gz-contact-info__item">
					<?php echo gz_icon( 'clock' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
					<div>
						<h4><?php esc_html_e( 'Plazo de respuesta', 'garantiza' ); ?></h4>
						<p><?php esc_html_e( 'Máximo 15 días hábiles, a tu correo electrónico.', 'garantiza' ); ?></p>
					</div>
				</div>
				<div class="gz-contact-info__item">
					<?php echo gz_icon( 'mail' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
					<div>
						<h4><?php esc_html_e( 'Copia de tu hoja', 'garantiza' ); ?></h4>
						<p><?php esc_html_e( 'Recibirás el registro completo apenas envíes el formulario.', 'garantiza' ); ?></p>
					</div>
				</div>
			</div>

			<p class="gz-contact__note">
				<?php esc_html_e( 'La formulación del reclamo no impide acudir a otras vías de solución de controversias ni es requisito previo para interponer una denuncia ante el Indecopi.', 'garantiza' ); ?>
			</p>
		</div>

		<div class="gz-contact-form">
			<div class="gz-claims__card-head">
				<h2><?php esc_html_e( 'Hoja de reclamación', 'garantiza' ); ?></h2>
				<p><?php echo esc_html( sprintf( __( 'Fecha: %s', 'garantiza' ), wp_date( 'd/m/Y' ) ) ); ?></p>
			</div>

			<?php if ( $gz_claims_id && shortcode_exists( 'fluentform' ) ) : ?>
				<?php echo do_shortcode( '[fluentform id="' . $gz_claims_id . '"]' ); ?>
			<?php else : ?>
				<?php
				while ( have_posts() ) :
					the_post();
					the_content();
				endwhile;
				?>
				<?php if ( current_user_can( 'manage_options' ) && ! $gz_claims_id ) : ?>
					<p class="gz-form-note"><?php esc_html_e( 'Indica el ID del formulario en Apariencia > Personalizar > Landing Garantiza.', 'garantiza' ); ?></p>
				<?php endif; ?>
			<?php endif; ?>
		</div>

	</div>
</section>

<?php
get_footer();
