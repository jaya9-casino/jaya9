<?php
/**
 * Header: logo, Login / Get Bonus buttons, orange menu bar, SVG sprite.
 *
 * @package Jaya9
 */

defined( 'ABSPATH' ) || exit;
?><!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>
	<meta charset="<?php bloginfo( 'charset' ); ?>">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<meta name="theme-color" content="#142590">
	<?php wp_head(); ?>
</head>
<body <?php body_class(); ?>>
<?php wp_body_open(); ?>

<header class="site-header">
	<div class="container header-bar">
		<a href="<?php echo esc_url( function_exists( 'pll_home_url' ) ? pll_home_url() : home_url( '/' ) ); ?>" class="logo" aria-label="<?php echo esc_attr( get_bloginfo( 'name' ) ); ?>">
			<?php echo j9_logo_img(); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
		</a>
		<div class="header-actions">
			<?php
			do_action( 'j9_header_actions_before' );
			echo j9_button( j9_option( 'j9_login_text', 'Login' ), j9_buttons_url(), 'btn btn-outline' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
			echo j9_button( j9_option( 'j9_bonus_text', 'Get Bonus' ), j9_buttons_url(), 'btn btn-primary' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
			?>
		</div>
	</div>

	<?php if ( has_nav_menu( 'header_menu' ) ) : ?>
	<nav class="main-nav" aria-label="<?php echo esc_attr( j9_t( 'Main menu' ) ); ?>">
		<?php
		wp_nav_menu(
			array(
				'theme_location' => 'header_menu',
				'container'      => false,
				'items_wrap'     => '<ul class="container nav-list">%3$s</ul>',
				'depth'          => 1,
				'fallback_cb'    => false,
			)
		);
		?>
	</nav>
	<?php endif; ?>
</header>

<?php get_template_part( 'template-parts/svg-sprite' ); ?>
