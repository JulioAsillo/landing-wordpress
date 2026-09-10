<?php
/**
 * Números y capacidad mensual.
 *
 * Las cuatro tarjetas van escalonadas en altura y una polilínea naranja
 * las cruza dibujando la flecha ascendente del brand book. En pantallas
 * angostas la flecha se oculta y las tarjetas igualan su altura.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$stats    = gz_stats();
$capacity = gz_capacity();
?>

<section class="gz-section gz-stats gz-wrap gz-wrap--wide">

	<div class="gz-section-head">
		<span class="gz-kicker"><?php echo esc_html( $stats['kicker'] ); ?></span>
		<h2><?php echo esc_html( $stats['title'] ); ?></h2>
		<p><?php echo wp_kses( $stats['intro'], array( 'strong' => array(), 'em' => array(), 'br' => array() ) ); ?></p>
	</div>

	<div class="gz-stats__steps">

		<?php foreach ( $stats['items'] as $stat ) : ?>
			<div class="gz-stat-step gz-reveal">
				<span class="gz-stat-step__num" data-count="<?php echo esc_attr( $stat['value'] ); ?>">0</span>
				<span class="gz-stat-step__label"><?php echo esc_html( $stat['label'] ); ?></span>
			</div>
		<?php endforeach; ?>

		<svg class="gz-stats__trend" viewBox="0 0 100 100" preserveAspectRatio="none" aria-hidden="true">
			<polyline points="0,32.7 24.7,32.7 24.7,21 50,21 50,9.3 75.3,9.3 75.3,-4 88,-4"
					  fill="none" stroke="currentColor" stroke-width="2.2"
					  stroke-linecap="round" stroke-linejoin="round"
					  vector-effect="non-scaling-stroke"/>
		</svg>
		<svg class="gz-stats__trend-head" viewBox="0 0 24 24" fill="none" aria-hidden="true">
			<path d="M12 20V5M6 11l6-6 6 6" stroke="currentColor" stroke-width="2.4" stroke-linecap="round" stroke-linejoin="round"/>
		</svg>

	</div>

	<div class="gz-stats__support">
		<div class="gz-capacity">
			<h3><?php echo esc_html( $capacity['title'] ); ?></h3>
			<div class="gz-capacity__bars">
				<?php foreach ( $capacity['items'] as $bar ) : ?>
					<div class="gz-capacity-bar">
						<span class="gz-capacity-bar__label"><?php echo esc_html( $bar['label'] ); ?></span>
						<div class="gz-capacity-bar__track">
							<div class="gz-capacity-bar__fill" data-width="<?php echo esc_attr( $bar['width'] ); ?>">
								<?php echo esc_html( $bar['value'] ); ?>
							</div>
						</div>
					</div>
				<?php endforeach; ?>
			</div>
		</div>
	</div>

</section>
