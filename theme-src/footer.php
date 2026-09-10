<?php
/**
 * Pie del sitio.
 */

$gz_footer = gz_footer();
?>
</main>

<footer class="gz-footer">

	<div class="gz-wrap gz-wrap--wide gz-footer__top">

		<div class="gz-footer__brand">
			<?php echo gz_logo( true ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
			<p><?php echo esc_html( $gz_footer['about'] ); ?></p>

			<?php if ( ! empty( $gz_footer['social'] ) ) : ?>
				<div class="gz-footer__social">
					<?php foreach ( $gz_footer['social'] as $gz_social ) : ?>
						<a href="<?php echo esc_url( $gz_social['url'] ); ?>"
						   class="gz-footer__social-link"
						   aria-label="<?php echo esc_attr( $gz_social['label'] ); ?>">
							<?php echo gz_icon( $gz_social['icon'] ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
						</a>
					<?php endforeach; ?>
				</div>
			<?php endif; ?>
		</div>

		<?php foreach ( $gz_footer['columns'] as $gz_col ) : ?>
			<div class="gz-footer__col">
				<h5><?php echo esc_html( $gz_col['title'] ); ?></h5>
				<?php
				if ( $gz_col['menu'] && has_nav_menu( $gz_col['menu'] ) ) {
					wp_nav_menu(
						array(
							'theme_location' => $gz_col['menu'],
							'container'      => false,
							'depth'          => 1,
							'fallback_cb'    => false,
						)
					);
				} else {
					echo '<ul>';
					foreach ( $gz_col['links'] as $gz_link ) {
						if ( $gz_link['url'] ) {
							printf(
								'<li><a href="%s">%s</a></li>',
								esc_url( $gz_link['url'] ),
								esc_html( $gz_link['label'] )
							);
						} else {
							printf( '<li>%s</li>', esc_html( $gz_link['label'] ) );
						}
					}
					echo '</ul>';
				}
				?>
			</div>
		<?php endforeach; ?>

	</div>

	<div class="gz-wrap gz-wrap--wide gz-footer__bottom">
		<span>
			&copy; <?php echo esc_html( gmdate( 'Y' ) ); ?> <?php bloginfo( 'name' ); ?>.
			<?php esc_html_e( 'Todos los derechos reservados.', 'garantiza' ); ?>
		</span>
		<?php if ( ! empty( $gz_footer['bottom'] ) ) : ?>
			<ul>
				<?php foreach ( $gz_footer['bottom'] as $gz_link ) : ?>
					<li><a href="<?php echo esc_url( $gz_link['url'] ); ?>"><?php echo esc_html( $gz_link['label'] ); ?></a></li>
				<?php endforeach; ?>
			</ul>
		<?php endif; ?>
	</div>

</footer>

<?php wp_footer(); ?>
</body>
</html>
