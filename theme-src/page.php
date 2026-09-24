<?php
/**
 * Página suelta: política de privacidad, términos, libro de reclamaciones.
 * El contenido lo edita el cliente desde el editor de WordPress.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

get_header();
?>
<div class="gz-page gz-wrap">
	<?php
	while ( have_posts() ) :
		the_post();
		?>
		<article <?php post_class(); ?>>
			<header class="gz-page__header">
				<span class="gz-kicker"><?php bloginfo( 'name' ); ?></span>
				<h1><?php the_title(); ?></h1>
			</header>
			<div class="gz-prose">
				<?php the_content(); ?>
			</div>
			<?php if ( get_the_modified_date() ) : ?>
				<p class="gz-form-note">
					<?php
					/* translators: %s: fecha de última actualización. */
					printf( esc_html__( 'Última actualización: %s', 'garantiza' ), esc_html( get_the_modified_date() ) );
					?>
				</p>
			<?php endif; ?>
		</article>
		<?php
	endwhile;
	?>
</div>
<?php
get_footer();
