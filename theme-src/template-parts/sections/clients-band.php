<?php
/**
 * Banda de entidades bajo el hero.
 *
 * Replica la "clients-band" de la propuesta aprobada: una etiqueta y una
 * cinta de logos en desplazamiento continuo. Los SVG viven en
 * /assets/img/clients y son monocromos (azul oscuro de marca) para que
 * ninguna marca externa compita con la de Garantiza.
 *
 * La cinta se pinta dos veces para el bucle sin cortes; la segunda copia
 * va con aria-hidden para que el lector de pantalla no la repita.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$band = gz_clients_band();

if ( empty( $band['items'] ) ) {
	return;
}

$gz_band_set = static function ( array $items, bool $is_copy ) {
	?>
	<ul class="gz-logo-band__set"<?php echo $is_copy ? ' aria-hidden="true"' : ''; ?>>
		<?php foreach ( $items as $item ) : ?>
			<li class="gz-logo-band__item">
				<?php
				$file = ! empty( $item['logo'] ) ? '/assets/img/clients/' . $item['logo'] : '';

				if ( $file && file_exists( GZ_DIR . $file ) ) :
					?>
					<img
						src="<?php echo esc_url( GZ_URI . $file ); ?>"
						alt="<?php echo $is_copy ? '' : esc_attr( $item['name'] ); ?>"
						style="--gz-logo-h: <?php echo (int) $item['height']; ?>px"
						width="<?php echo (int) $item['width']; ?>"
						height="<?php echo (int) $item['height']; ?>"
						loading="lazy"
						decoding="async"
					>
				<?php else : ?>
					<span class="gz-logo-band__more"><?php echo esc_html( $item['name'] ); ?></span>
				<?php endif; ?>
			</li>
		<?php endforeach; ?>
	</ul>
	<?php
};
?>

<section class="gz-logo-band" aria-labelledby="gz-logo-band-label">
	<div class="gz-wrap gz-wrap--wide">
		<p class="gz-logo-band__label" id="gz-logo-band-label"><?php echo esc_html( $band['label'] ); ?></p>

		<div class="gz-logo-band__marquee">
			<div class="gz-logo-band__track">
				<?php
				$gz_band_set( $band['items'], false );
				$gz_band_set( $band['items'], true );
				?>
			</div>
		</div>
	</div>
</section>
