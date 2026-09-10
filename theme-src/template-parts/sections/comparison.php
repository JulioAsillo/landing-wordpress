<?php
/**
 * Por qué Garantiza.
 *
 * Tabla comparativa de tres columnas construida con grid, no con
 * <table>: en móvil cada fila se apila y una <table> no lo permite sin
 * romper la semántica.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$comparison = gz_comparison();
?>

<section class="gz-section gz-comparison" id="por-que-garantiza">
	<div class="gz-wrap">

		<div class="gz-section-head">
			<span class="gz-kicker"><?php echo esc_html( $comparison['kicker'] ); ?></span>
			<h2><?php echo esc_html( $comparison['title'] ); ?></h2>
			<p><?php echo esc_html( $comparison['lead'] ); ?></p>
		</div>

		<div class="gz-compare gz-reveal">

			<div class="gz-compare__head">
				<div class="is-label"></div>
				<div class="is-old">
					<span class="gz-compare__tag"><?php echo esc_html( $comparison['col_bad']['tag'] ); ?></span>
					<h4><?php echo esc_html( $comparison['col_bad']['title'] ); ?></h4>
				</div>
				<div class="is-new">
					<span class="gz-compare__tag"><?php echo esc_html( $comparison['col_good']['tag'] ); ?></span>
					<h4><?php echo esc_html( $comparison['col_good']['title'] ); ?></h4>
				</div>
			</div>

			<?php foreach ( $comparison['rows'] as $row ) : ?>
				<div class="gz-compare__row">

					<div class="is-feature">
						<span class="gz-compare__feature-icon">
							<?php echo gz_icon( $row['icon'] ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
						</span>
						<span>
							<?php echo esc_html( $row['feature'] ); ?>
							<span class="is-sublabel"><?php echo esc_html( $row['sublabel'] ); ?></span>
						</span>
					</div>

					<div class="is-old">
						<?php echo gz_icon( 'cross', 'gz-compare__icon is-no' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
						<?php echo esc_html( $row['bad'] ); ?>
					</div>

					<div class="is-new">
						<?php echo gz_icon( 'check', 'gz-compare__icon is-yes' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
						<?php echo esc_html( $row['good'] ); ?>
					</div>

				</div>
			<?php endforeach; ?>

		</div>

	</div>
</section>
