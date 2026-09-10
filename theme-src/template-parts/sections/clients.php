<?php
/**
 * Clientes.
 *
 * Tiles de texto, no logos: el cliente todavía no ha entregado los
 * archivos ni el permiso de uso de las marcas. Cada tile acepta un ID
 * de adjunto en 'image' y pinta la imagen en cuanto lleguen.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$clients = gz_clients();
?>

<section class="gz-section gz-clients" id="clientes">
	<div class="gz-wrap gz-wrap--wide gz-clients__row">

		<div class="gz-clients__copy">
			<span class="gz-kicker"><?php echo esc_html( $clients['kicker'] ); ?></span>
			<h2><?php echo esc_html( $clients['title'] ); ?></h2>
		</div>

		<div class="gz-client-marks gz-reveal">
			<?php foreach ( $clients['items'] as $client ) : ?>
				<div class="gz-client-mark">
					<?php
					if ( ! empty( $client['image'] ) ) {
						echo wp_get_attachment_image(
							(int) $client['image'],
							'gz-logo',
							false,
							array( 'alt' => $client['name'] )
						);
					} else {
						echo esc_html( $client['name'] );
					}
					?>
				</div>
			<?php endforeach; ?>
		</div>

	</div>
</section>
