<?php
/**
 * Plantilla de respaldo.
 */
get_header();
?>
<div class="gz-page gz-wrap">
	<?php
	if ( have_posts() ) :
		while ( have_posts() ) :
			the_post();
			?>
			<article <?php post_class(); ?>>
				<header class="gz-page__header"><h1><?php the_title(); ?></h1></header>
				<div class="gz-prose"><?php the_content(); ?></div>
			</article>
			<?php
		endwhile;
	else :
		?>
		<h1><?php esc_html_e( 'No hay contenido', 'garantiza' ); ?></h1>
		<?php
	endif;
	?>
</div>
<?php
get_footer();
