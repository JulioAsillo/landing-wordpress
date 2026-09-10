<?php
/**
 * Contacto.
 *
 * El formulario real lo pinta Fluent Forms: su ID se define en
 * Apariencia > Personalizar > Landing Garantiza. Mientras no esté
 * conectado se muestra la vista previa de la maqueta, con los mismos
 * campos y nombres, para que la sección se pueda revisar completa. La
 * vista previa no envía nada y lo advierte al intentarlo.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! function_exists( 'gz_preview_field' ) ) {
	/**
	 * Pinta un campo de la vista previa.
	 *
	 * Vive en esta plantilla y no en /inc porque solo existe mientras el
	 * formulario real no esté conectado: cuando lo esté, se va con ella.
	 *
	 * @param array $field Definición del campo.
	 */
	function gz_preview_field( $field ) {
		$id          = 'gz-field-' . $field['name'];
		$placeholder = isset( $field['placeholder'] ) ? $field['placeholder'] : '';
		?>
		<div class="gz-field">
			<label for="<?php echo esc_attr( $id ); ?>"><?php echo esc_html( $field['label'] ); ?></label>

			<?php if ( 'select' === $field['type'] ) : ?>
				<select id="<?php echo esc_attr( $id ); ?>" name="<?php echo esc_attr( $field['name'] ); ?>">
					<?php foreach ( $field['options'] as $option ) : ?>
						<option><?php echo esc_html( $option ); ?></option>
					<?php endforeach; ?>
				</select>

			<?php elseif ( 'textarea' === $field['type'] ) : ?>
				<textarea id="<?php echo esc_attr( $id ); ?>"
						  name="<?php echo esc_attr( $field['name'] ); ?>"
						  placeholder="<?php echo esc_attr( $placeholder ); ?>"></textarea>

			<?php else : ?>
				<input type="<?php echo esc_attr( $field['type'] ); ?>"
					   id="<?php echo esc_attr( $id ); ?>"
					   name="<?php echo esc_attr( $field['name'] ); ?>"
					   placeholder="<?php echo esc_attr( $placeholder ); ?>">
			<?php endif; ?>
		</div>
		<?php
	}
}

if ( ! function_exists( 'gz_preview_rows' ) ) {
	/**
	 * Agrupa los campos en filas.
	 *
	 * Los marcados como 'half' se emparejan de dos en dos; el resto ocupa
	 * la fila completa, igual que en la maqueta.
	 *
	 * @param array $fields Campos del formulario.
	 * @return array Lista de filas, cada una con uno o dos campos.
	 */
	function gz_preview_rows( $fields ) {
		$rows    = array();
		$pending = null;

		foreach ( $fields as $field ) {
			if ( empty( $field['half'] ) ) {
				if ( $pending ) {
					$rows[]  = array( $pending );
					$pending = null;
				}
				$rows[] = array( $field );
				continue;
			}

			if ( $pending ) {
				$rows[]  = array( $pending, $field );
				$pending = null;
				continue;
			}

			$pending = $field;
		}

		if ( $pending ) {
			$rows[] = array( $pending );
		}

		return $rows;
	}
}

$contact   = gz_contact();
$form_copy = gz_form_copy();
$form_id   = (int) get_theme_mod( 'gz_form_id', 0 );
?>

<section class="gz-section gz-contact" id="contacto">
	<div class="gz-wrap gz-wrap--wide gz-contact__grid">

		<div class="gz-contact__copy">
			<span class="gz-kicker"><?php echo esc_html( $contact['kicker'] ); ?></span>
			<h2><?php echo esc_html( $contact['title'] ); ?></h2>
			<p><?php echo esc_html( $contact['lead'] ); ?></p>

			<div class="gz-contact-info">
				<?php foreach ( $contact['items'] as $item ) : ?>
					<div class="gz-contact-info__item">
						<?php echo gz_icon( $item['icon'] ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
						<div>
							<h4><?php echo esc_html( $item['title'] ); ?></h4>
							<?php if ( ! empty( $item['text'] ) ) : ?>
								<p><?php echo esc_html( $item['text'] ); ?></p>
							<?php endif; ?>
							<?php if ( ! empty( $item['link_label'] ) ) : ?>
								<a class="gz-contact-info__link" href="<?php echo esc_url( $item['link_url'] ); ?>">
									<?php echo esc_html( $item['link_label'] ); ?>
									<?php echo gz_icon( 'arrow-right' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
								</a>
							<?php endif; ?>
						</div>
					</div>
				<?php endforeach; ?>
			</div>

			<p class="gz-contact__note"><?php echo esc_html( $contact['note'] ); ?></p>
		</div>

		<div class="gz-contact-form">
			<?php if ( $form_id && shortcode_exists( 'fluentform' ) ) : ?>

				<?php echo do_shortcode( '[fluentform id="' . $form_id . '"]' ); ?>

			<?php else : ?>

				<?php if ( current_user_can( 'manage_options' ) ) : ?>
					<p class="gz-form-note">
						<?php esc_html_e( 'Vista previa. El formulario aún no está enlazado a Fluent Forms: indica su ID en Apariencia > Personalizar > Landing Garantiza.', 'garantiza' ); ?>
					</p>
				<?php endif; ?>

				<form data-form-preview novalidate>
					<?php foreach ( gz_preview_rows( gz_form_fields() ) as $row ) : ?>
						<?php if ( count( $row ) > 1 ) : ?>
							<div class="gz-form-row">
								<?php foreach ( $row as $field ) : ?>
									<?php gz_preview_field( $field ); ?>
								<?php endforeach; ?>
							</div>
						<?php else : ?>
							<?php gz_preview_field( $row[0] ); ?>
						<?php endif; ?>
					<?php endforeach; ?>

					<button type="submit" class="gz-btn gz-btn--primary">
						<?php echo esc_html( $form_copy['submit'] ); ?>
					</button>
					<p class="gz-form-note"><?php echo esc_html( $form_copy['note'] ); ?></p>
				</form>

			<?php endif; ?>
		</div>

	</div>
</section>
