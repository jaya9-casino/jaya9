<?php
/**
 * Styles and font preload. Fonts are local (assets/fonts), no Google Fonts.
 *
 * @package Jaya9
 */

defined( 'ABSPATH' ) || exit;

add_action(
	'wp_enqueue_scripts',
	function () {
		$file = J9_DIR . '/style.css';
		wp_enqueue_style( 'jaya9', get_stylesheet_uri(), array(), file_exists( $file ) ? (string) filemtime( $file ) : J9_VERSION );
	}
);

/* Preload the main text weight so the first paint uses Readex Pro. */
add_action(
	'wp_head',
	function () {
		printf(
			'<link rel="preload" href="%s" as="font" type="font/woff2" crossorigin>' . "\n",
			esc_url( J9_URI . '/assets/fonts/readex-pro-400.woff2' )
		);
	},
	2
);
