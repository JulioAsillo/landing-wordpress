<?php
/**
 * Pie del sitio.
 */
?>
</main>

<footer class="gz-footer">
	<div class="gz-container">
		<div class="gz-footer__grid">

			<div>
				<p class="gz-brand" style="color:#fff"><?php bloginfo( 'name' ); ?></p>
				<p style="font-size:var(--gz-fs-small)">
					<?php echo esc_html( get_bloginfo( 'description' ) ); ?>
				</p>
			</div>

			<div>
				<h4><?php esc_html_e( 'Empresa', 'garantiza' ); ?></h4>
				<?php
				wp_nav_menu(
					array(
						'theme_location' => 'footer',
						'container'      => false,
						'depth'          => 1,
						'fallback_cb'    => false,
					)
				);
				?>
			</div>

			<div>
				<h4><?php esc_html_e( 'Contacto', 'garantiza' ); ?></h4>
				<ul>
					<li><?php esc_html_e( 'Lima, Perú', 'garantiza' ); ?></li>
					<li><?php esc_html_e( 'Cobertura a nivel nacional', 'garantiza' ); ?></li>
				</ul>
			</div>

			<div>
				<h4><?php esc_html_e( 'Legal', 'garantiza' ); ?></h4>
				<?php
				wp_nav_menu(
					array(
						'theme_location' => 'legal',
						'container'      => false,
						'depth'          => 1,
						'fallback_cb'    => false,
					)
				);
				?>
			</div>

		</div>

		<div class="gz-footer__bottom">
			<span>&copy; <?php echo esc_html( gmdate( 'Y' ) ); ?> <?php bloginfo( 'name' ); ?>. <?php esc_html_e( 'Todos los derechos reservados.', 'garantiza' ); ?></span>
		</div>
	</div>
</footer>

<?php wp_footer(); ?>
</body>
</html>
