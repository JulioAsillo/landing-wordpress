<?php
/**
 * Portada de la landing.
 *
 * Las secciones leen su contenido de /inc/content.php, no de literales,
 * para que la capa administrable se pueda enchufar sin tocar el marcado.
 */

get_header();
?>

<!-- ===================== HERO ===================== -->
<section class="gz-hero">
	<div class="gz-container">

		<div class="gz-hero__grid">
			<div class="gz-hero__text">
				<p class="gz-eyebrow"><?php esc_html_e( 'Gestión integral de garantías mobiliarias', 'garantiza' ); ?></p>

				<h1><?php esc_html_e( 'Maximizamos el valor de tu garantía, minimizamos los tiempos del proceso.', 'garantiza' ); ?></h1>

				<p class="gz-hero__lead">
					<?php esc_html_e( 'Acompañamos a empresas y entidades financieras durante todo el ciclo de una garantía mobiliaria: constitución, ejecución, incautación y venta del activo, bajo un solo proceso.', 'garantiza' ); ?>
				</p>

				<div class="gz-hero__actions">
					<a class="gz-btn gz-btn--primary" href="#contacto">
						<?php esc_html_e( 'Solicitar evaluación de cartera', 'garantiza' ); ?>
					</a>
					<a class="gz-btn gz-btn--ghost" href="#servicios">
						<?php esc_html_e( 'Ver cómo trabajamos', 'garantiza' ); ?>
					</a>
				</div>
			</div>

			<div class="gz-hero__media">
				<?php
				// Imagen del LCP: prioridad alta y sin lazy. Ver inc/images.php
				echo gz_hero_image( (int) get_theme_mod( 'gz_hero_image_id', 0 ) );
				?>
			</div>
		</div>

		<div class="gz-pillars">
			<?php foreach ( gz_pillars() as $pillar ) : ?>
				<div class="gz-pillar">
					<h3><?php echo esc_html( $pillar['title'] ); ?></h3>
					<p><?php echo esc_html( $pillar['text'] ); ?></p>
				</div>
			<?php endforeach; ?>
		</div>

	</div>
</section>


<!-- ===================== NÚMEROS ===================== -->
<section class="gz-section gz-section--surface" id="nosotros">
	<div class="gz-container">

		<p class="gz-eyebrow"><?php esc_html_e( 'Nuestros números', 'garantiza' ); ?></p>
		<h2><?php esc_html_e( 'Generamos valor a tu garantía mobiliaria', 'garantiza' ); ?></h2>
		<p class="gz-lead">
			<?php esc_html_e( 'Hemos gestionado un portafolio de más de S/ 2,000 millones en más de 44,000 créditos con garantía mobiliaria.', 'garantiza' ); ?>
		</p>

		<div class="gz-grid gz-grid--4" style="margin-top:var(--gz-sp-lg)">
			<?php foreach ( gz_stats() as $stat ) : ?>
				<div class="gz-stat">
					<div class="gz-stat__value"
						 data-counter="<?php echo esc_attr( $stat['value'] ); ?>"
						 data-suffix="<?php echo esc_attr( $stat['suffix'] ); ?>">0</div>
					<div class="gz-stat__label"><?php echo esc_html( $stat['label'] ); ?></div>
				</div>
			<?php endforeach; ?>
		</div>

		<div class="gz-capacity">
			<h3><?php esc_html_e( 'Nuestra capacidad mensual', 'garantiza' ); ?></h3>
			<div class="gz-grid gz-grid--4">
				<?php foreach ( gz_capacity() as $cap ) : ?>
					<div class="gz-capacity__item">
						<div class="gz-capacity__value"
							 data-counter="<?php echo esc_attr( $cap['value'] ); ?>">0</div>
						<div class="gz-capacity__label"><?php echo esc_html( $cap['label'] ); ?></div>
					</div>
				<?php endforeach; ?>
			</div>
		</div>

	</div>
</section>


<!-- ===================== SERVICIOS ===================== -->
<section class="gz-section" id="servicios">
	<div class="gz-container">

		<p class="gz-eyebrow"><?php esc_html_e( 'Nuestros servicios', 'garantiza' ); ?></p>
		<h2><?php esc_html_e( 'Un proceso, cuatro etapas, un solo responsable', 'garantiza' ); ?></h2>
		<p class="gz-lead">
			<?php esc_html_e( 'Acompañamos la garantía mobiliaria en cada etapa de su ciclo de vida, desde la constitución del contrato hasta la venta del activo recuperado.', 'garantiza' ); ?>
		</p>

		<div class="gz-grid gz-grid--2" style="margin-top:var(--gz-sp-lg)">
			<?php foreach ( gz_services() as $i => $service ) : ?>
				<?php $panel_id = 'gz-service-' . $i; ?>
				<article class="gz-service">

					<button class="gz-service__head"
							type="button"
							aria-expanded="false"
							aria-controls="<?php echo esc_attr( $panel_id ); ?>"
							data-accordion>
						<span class="gz-service__num"><?php echo esc_html( $service['num'] ); ?></span>
						<span>
							<span class="gz-service__title"><?php echo esc_html( $service['title'] ); ?></span>
							<span class="gz-service__desc"><?php echo esc_html( $service['desc'] ); ?></span>
						</span>
						<svg class="gz-service__icon" width="20" height="20" viewBox="0 0 24 24"
							 fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true">
							<path d="M6 9l6 6 6-6"/>
						</svg>
					</button>

					<div class="gz-service__body" id="<?php echo esc_attr( $panel_id ); ?>" hidden>
						<ul>
							<?php foreach ( $service['items'] as $item ) : ?>
								<li><?php echo esc_html( $item ); ?></li>
							<?php endforeach; ?>
						</ul>
					</div>

				</article>
			<?php endforeach; ?>
		</div>

	</div>
</section>


<!-- ===================== COMPARATIVA ===================== -->
<section class="gz-section gz-section--tint" id="por-que-garantiza">
	<div class="gz-container">

		<p class="gz-eyebrow"><?php esc_html_e( 'Por qué Garantiza', 'garantiza' ); ?></p>
		<h2><?php esc_html_e( 'Gestión tradicional vs. gestión Garantiza', 'garantiza' ); ?></h2>
		<p class="gz-lead">
			<?php esc_html_e( 'La fragmentación entre proveedores es lo que más tiempo y valor le cuesta a una garantía mobiliaria. Nosotros lo resolvemos con un solo equipo.', 'garantiza' ); ?>
		</p>

		<div class="gz-compare" style="margin-top:var(--gz-sp-lg)">

			<div class="gz-compare__col gz-compare__col--bad">
				<h3><?php esc_html_e( 'Gestión tradicional', 'garantiza' ); ?></h3>
				<p class="gz-compare__verdict"><?php esc_html_e( 'Genera pérdidas', 'garantiza' ); ?></p>
				<?php foreach ( gz_comparison() as $row ) : ?>
					<div class="gz-compare__item">
						<span class="gz-compare__mark" aria-hidden="true">&times;</span>
						<span>
							<strong><?php echo esc_html( $row['bad'][0] ); ?></strong>
							<span><?php echo esc_html( $row['bad'][1] ); ?></span>
						</span>
					</div>
				<?php endforeach; ?>
			</div>

			<div class="gz-compare__col gz-compare__col--good">
				<h3><?php esc_html_e( 'Gestión Garantiza', 'garantiza' ); ?></h3>
				<p class="gz-compare__verdict"><?php esc_html_e( 'Genera ganancias', 'garantiza' ); ?></p>
				<?php foreach ( gz_comparison() as $row ) : ?>
					<div class="gz-compare__item">
						<span class="gz-compare__mark" aria-hidden="true">&check;</span>
						<span>
							<strong><?php echo esc_html( $row['good'][0] ); ?></strong>
							<span><?php echo esc_html( $row['good'][1] ); ?></span>
						</span>
					</div>
				<?php endforeach; ?>
			</div>

		</div>

	</div>
</section>


<!-- ===================== CLIENTES ===================== -->
<section class="gz-section" id="clientes">
	<div class="gz-container">

		<p class="gz-eyebrow" style="text-align:center"><?php esc_html_e( 'Nuestros clientes', 'garantiza' ); ?></p>
		<h2 style="text-align:center"><?php esc_html_e( 'Trabajamos con entidades de primer nivel del sector financiero', 'garantiza' ); ?></h2>

		<div class="gz-client-marks" style="margin-top:var(--gz-sp-lg)">
			<?php foreach ( gz_clients() as $client ) : ?>
				<span class="gz-client-mark"><?php echo esc_html( $client ); ?></span>
			<?php endforeach; ?>
		</div>

	</div>
</section>


<!-- ===================== CONTACTO ===================== -->
<section class="gz-section gz-section--surface" id="contacto">
	<div class="gz-container">

		<div class="gz-contact__grid">

			<div>
				<p class="gz-eyebrow"><?php esc_html_e( 'Contacto', 'garantiza' ); ?></p>
				<h2><?php esc_html_e( 'Solicita una evaluación de tu cartera', 'garantiza' ); ?></h2>
				<p class="gz-lead">
					<?php esc_html_e( 'Cuéntanos sobre tu cartera de garantías mobiliarias y te contactamos para revisar cómo podemos gestionar el ciclo completo.', 'garantiza' ); ?>
				</p>

				<div style="margin-top:var(--gz-sp-lg)">
					<div class="gz-info-card">
						<h3><?php esc_html_e( 'Cobertura nacional', 'garantiza' ); ?></h3>
						<p><?php esc_html_e( 'Operamos en Lima y provincias durante todo el proceso de incautación.', 'garantiza' ); ?></p>
					</div>
					<div class="gz-info-card">
						<h3><?php esc_html_e( 'Respuesta en 1 día hábil', 'garantiza' ); ?></h3>
						<p><?php esc_html_e( 'Un especialista revisa tu caso y te contacta por el canal que prefieras.', 'garantiza' ); ?></p>
					</div>
				</div>
			</div>

			<div class="gz-form-wrap">
				<?php
				// El ID se ajusta al del formulario real de Fluent Forms.
				$form_id = (int) get_theme_mod( 'gz_form_id', 0 );

				if ( $form_id && shortcode_exists( 'fluentform' ) ) {
					echo do_shortcode( '[fluentform id="' . $form_id . '"]' );
				} else {
					echo '<p style="color:var(--gz-ink-soft);font-size:var(--gz-fs-small)">';
					esc_html_e( 'Formulario pendiente de configurar en Fluent Forms.', 'garantiza' );
					echo '</p>';
				}
				?>
			</div>

		</div>

	</div>
</section>

<?php
get_footer();
