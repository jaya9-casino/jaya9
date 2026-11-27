<?php
/**
 * 404 in the Hero simple style.
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
				<span class="badge">404</span>
				<h1><?php echo esc_html( j9_t( 'Page not found' ) ); ?></h1>
				<p><?php echo esc_html( j9_t( 'The page you are looking for does not exist or has been moved.' ) ); ?></p>
				<div class="hero-buttons">
					<?php echo j9_button( j9_t( 'Back to home' ), function_exists( 'pll_home_url' ) ? pll_home_url() : home_url( '/' ), 'btn btn-primary btn-lg' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
				</div>
			</div>
		</div>
	</section>
</main>
<?php
get_footer();
