<?php
/**
 * Polylang support. Loaded only when Polylang is active (see functions.php).
 *
 * - Theme strings in Languages -> Translations (group "Jaya9").
 * - Theme Settings per language: "Theme Settings (BN)" etc. with post_id options_{slug}.
 *   Empty fields there fall back to the default language (j9_option()).
 * - hreflang and html lang are left to Polylang (inc/seo.php skips them).
 *
 * Do NOT enable custom fields synchronization in Languages -> Settings -> Synchronization:
 * it would overwrite block texts between languages.
 *
 * @package Jaya9
 */

defined( 'ABSPATH' ) || exit;

/* Theme strings. */
add_action(
	'init',
	function () {
		if ( ! function_exists( 'pll_register_string' ) ) {
			return;
		}
		$strings = array(
			'Table of Contents',
			'Page not found',
			'The page you are looking for does not exist or has been moved.',
			'Back to home',
			'Main menu',
			'Footer menu',
			'Information',
			'Jaya9 logo',
			'Responsible gaming disclaimer',
		);
		foreach ( $strings as $string ) {
			pll_register_string( 'jaya9_' . sanitize_title( $string ), $string, 'Jaya9' );
		}
	}
);

/* Theme Settings sub-page for every non-default language. */
add_action(
	'acf/init',
	function () {
		if ( ! function_exists( 'acf_add_options_sub_page' ) || ! function_exists( 'pll_languages_list' ) ) {
			return;
		}
		$default = pll_default_language( 'slug' );
		foreach ( (array) pll_languages_list( array( 'fields' => 'slug' ) ) as $slug ) {
			if ( $slug === $default ) {
				continue;
			}
			$label = 'Theme Settings (' . strtoupper( $slug ) . ')';
			acf_add_options_sub_page(
				array(
					'page_title'  => $label,
					'menu_title'  => $label,
					'menu_slug'   => 'j9-settings-' . $slug,
					'parent_slug' => 'j9-settings',
					'capability'  => 'edit_theme_options',
					'post_id'     => 'options_' . $slug,
				)
			);
		}
	},
	20
);
