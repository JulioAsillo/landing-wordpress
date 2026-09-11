<?php
/**
 * Servicios.
 *
 * Cuatro tarjetas, cada una con su detalle plegable e independiente de
 * las demás: el visitante puede abrir varias a la vez para comparar.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$services = gz_services();
$strip    = gz_services_strip();
?>

<section class="gz-section gz-services" id="servicios">
	<div class="gz-wrap gz-wrap--wide">

		<div class="gz-services__head">
			<div class="gz-section-head">
				<span class="gz-kicker"><?php echo esc_html( $services['kicker'] ); ?></span>
				<h2><?php echo esc_html( $services['title'] ); ?></h2>
				<p><?php echo esc_html( $services['lead'] ); ?></p>
				<a class="gz-services__link" href="<?php echo esc_url( $services['link']['url'] ); ?>">
					<?php echo esc_html( $services['link']['label'] ); ?>
					<?php echo gz_icon( 'arrow-right' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
				</a>
			</div>
		</div>

		<div class="gz-service-grid">
			<?php foreach ( $services['items'] as $i => $service ) : ?>
				<?php $panel_id = 'gz-service-' . $i; ?>
				<article class="gz-service-card">

					<span class="gz-service-card__num"><?php echo esc_html( $service['num'] ); ?></span>
					<h3><?php echo esc_html( $service['title'] ); ?></h3>
					<p><?php echo esc_html( $service['desc'] ); ?></p>

					<button class="gz-service-card__toggle" type="button"
							aria-expanded="false"
							aria-controls="<?php echo esc_attr( $panel_id ); ?>"
							data-accordion>
						<?php esc_html_e( 'Ver detalle', 'garantiza' ); ?>
						<?php echo gz_icon( 'arrow-right' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
					</button>

					<div class="gz-service-card__panel" id="<?php echo esc_attr( $panel_id ); ?>">
						<div class="gz-service-card__panel-inner">
							<ul>
								<?php foreach ( $service['items'] as $item ) : ?>
									<li><?php echo esc_html( $item ); ?></li>
								<?php endforeach; ?>
							</ul>
						</div>
					</div>

				</article>
			<?php endforeach; ?>
		</div>

		<div class="gz-services__strip gz-reveal">
			<p><?php echo esc_html( $strip['text'] ); ?></p>
			<ul>
				<?php foreach ( $strip['items'] as $item ) : ?>
					<li>
						<?php echo esc_html( $item['verb'] ); ?>
						<span><?php echo esc_html( $item['object'] ); ?></span>
					</li>
				<?php endforeach; ?>
			</ul>
		</div>

	</div>
</section>
