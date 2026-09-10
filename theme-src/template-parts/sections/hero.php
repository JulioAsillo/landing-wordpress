<?php
/**
 * Hero.
 *
 * La fotografía va a sangre detrás del texto, con el degradé de marca
 * encima para sostener la lectura. La franja flotante con la cita y los
 * tres pilares monta sobre la sección siguiente.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$hero = gz_hero();
?>

<section class="gz-hero" id="nosotros">

	<div class="gz-hero__bg" aria-hidden="true">
		<?php echo gz_hero_image( (int) get_theme_mod( 'gz_hero_image_id', 0 ) ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
	</div>

	<div class="gz-wrap gz-wrap--wide">
		<div class="gz-hero__content">

			<span class="gz-hero__eyebrow">
				<span aria-hidden="true"></span><?php echo esc_html( $hero['eyebrow'] ); ?>
			</span>

			<h1>
				<?php foreach ( $hero['title_lines'] as $line ) : ?>
					<span class="gz-hero__title-line<?php echo $line['accent'] ? ' is-accent' : ''; ?>">
						<?php echo esc_html( $line['text'] ); ?>
					</span>
				<?php endforeach; ?>
			</h1>

			<p class="gz-hero__lead"><?php echo esc_html( $hero['lead'] ); ?></p>

			<div class="gz-hero__ctas">
				<a class="gz-btn gz-btn--primary" href="<?php echo esc_url( $hero['cta_primary']['url'] ); ?>">
					<?php echo esc_html( $hero['cta_primary']['label'] ); ?>
				</a>
				<a class="gz-btn gz-btn--ghost" href="<?php echo esc_url( $hero['cta_ghost']['url'] ); ?>">
					<?php echo esc_html( $hero['cta_ghost']['label'] ); ?>
					<?php echo gz_icon( 'arrow-right' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
				</a>
			</div>

		</div>
	</div>

	<div class="gz-wrap gz-wrap--wide gz-hero__floatbar">
		<div class="gz-hero-bar gz-reveal">

			<p class="gz-hero-bar__quote"><?php echo esc_html( $hero['quote'] ); ?></p>

			<div class="gz-hero-bar__cols">
				<?php foreach ( gz_pillars() as $pillar ) : ?>
					<div class="gz-hero-bar__col">
						<span class="gz-icon-circle">
							<?php echo gz_icon( $pillar['icon'] ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
						</span>
						<span><?php echo esc_html( $pillar['title'] ); ?></span>
					</div>
				<?php endforeach; ?>
			</div>

		</div>
	</div>

</section>
