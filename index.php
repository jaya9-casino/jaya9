<?php
/**
 * Fallback template: title + the_content() in .content.
 *
 * @package Jaya9
 */

defined( 'ABSPATH' ) || exit;

get_header();
?>
<main>
	<section class="hero hero--single">
		<div class="container hero-grid">
			<div class="hero-text">
				<h1><?php echo esc_html( is_singular() ? get_the_title() : wp_get_document_title() ); ?></h1>
			</div>
		</div>
	</section>

	<div class="container content-wrap">
		<article class="content">
			<section>
			<?php
			if ( have_posts() ) :
				while ( have_posts() ) :
					the_post();
					if ( is_singular() ) {
						the_content();
					} else {
						printf( '<h2><a href="%s">%s</a></h2>', esc_url( get_permalink() ), esc_html( get_the_title() ) );
						the_excerpt();
					}
				endwhile;
				the_posts_pagination();
			endif;
			?>
			</section>
		</article>
	</div>
</main>
<?php
get_footer();
