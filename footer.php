<?php
/**
 * Footer: responsible gaming disclaimer, logo, two menus, copyright.
 *
 * @package Jaya9
 */

defined( 'ABSPATH' ) || exit;

$j9_age       = j9_option( 'j9_age_badge', '18+' );
$j9_copyright = str_replace( '{year}', gmdate( 'Y' ), (string) j9_option( 'j9_copyright', '© {year} Jaya9 Bangladesh. All rights reserved. Play responsibly.' ) );
?>

<?php get_template_part( 'template-parts/disclaimer' ); ?>

<footer class="site-footer">
	<div class="container footer-grid">
		<div class="footer-brand">
			<a href="<?php echo esc_url( function_exists( 'pll_home_url' ) ? pll_home_url() : home_url( '/' ) ); ?>" class="logo" aria-label="<?php echo esc_attr( get_bloginfo( 'name' ) ); ?>">
				<?php echo j9_logo_img( '', true ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
			</a>
			<?php $j9_footer_text = j9_option( 'j9_footer_text' ); ?>
			<?php if ( $j9_footer_text ) : ?>
				<p><?php echo esc_html( $j9_footer_text ); ?></p>
			<?php endif; ?>
		</div>

		<?php
		$j9_cols = array(
			'footer_menu_1' => array( j9_option( 'j9_footer_menu1_title', 'Jaya9' ), j9_t( 'Footer menu' ) ),
			'footer_menu_2' => array( j9_option( 'j9_footer_menu2_title', 'Information' ), j9_t( 'Information' ) ),
		);
		foreach ( $j9_cols as $j9_location => $j9_col ) :
			if ( ! has_nav_menu( $j9_location ) ) {
				continue;
			}
			?>
			<nav class="footer-col" aria-label="<?php echo esc_attr( $j9_col[1] ); ?>">
				<p class="footer-title"><?php echo esc_html( $j9_col[0] ); ?></p>
				<?php
				wp_nav_menu(
					array(
						'theme_location' => $j9_location,
						'container'      => false,
						'items_wrap'     => '<ul>%3$s</ul>',
						'depth'          => 1,
						'fallback_cb'    => false,
					)
				);
				?>
			</nav>
		<?php endforeach; ?>
	</div>

	<div class="footer-bottom">
		<div class="container footer-bottom-inner">
			<?php if ( $j9_age ) : ?>
				<span class="age-badge age-badge--sm" aria-hidden="true"><?php echo esc_html( $j9_age ); ?></span>
			<?php endif; ?>
			<p><?php echo esc_html( $j9_copyright ); ?></p>
		</div>
	</div>
</footer>

<?php wp_footer(); ?>
</body>
</html>
