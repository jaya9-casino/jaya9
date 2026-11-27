<?php
/**
 * Template for all pages (front page included): Hero -> table of contents -> Flexible Content blocks.
 *
 * @package Jaya9
 */

defined( 'ABSPATH' ) || exit;

get_header();

while ( have_posts() ) :
	the_post();
	?>
	<main>
		<?php get_template_part( 'template-parts/hero' ); ?>

		<div class="container content-wrap">
			<?php get_template_part( 'template-parts/toc' ); ?>

			<article class="content">
				<?php
				if ( j9_get_blocks() ) {
					j9_render_blocks();
				} elseif ( '' !== trim( (string) get_post_field( 'post_content', get_the_ID() ) ) ) {
					// Pages created before the theme: show the classic content as is.
					echo '<section>';
					the_content();
					echo '</section>';
				}
				?>
			</article>
		</div>
	</main>
	<?php
endwhile;

get_footer();
