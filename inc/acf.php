<?php
/**
 * ACF Pro: Theme Settings page, acf-json, field helpers.
 *
 * @package Jaya9
 */

defined( 'ABSPATH' ) || exit;

/* Field groups are stored in the theme: acf-json/ (save + load). */
add_filter(
	'acf/settings/save_json',
	function () {
		return J9_DIR . '/acf-json';
	}
);
add_filter(
	'acf/settings/load_json',
	function ( $paths ) {
		$paths[] = J9_DIR . '/acf-json';
		return array_unique( $paths );
	}
);

/* Theme Settings (post_id "options" = default language). */
add_action(
	'acf/init',
	function () {
		if ( ! function_exists( 'acf_add_options_page' ) ) {
			return;
		}
		acf_add_options_page(
			array(
				'page_title' => 'Theme Settings',
				'menu_title' => 'Theme Settings',
				'menu_slug'  => 'j9-settings',
				'capability' => 'edit_theme_options',
				'post_id'    => 'options',
				'position'   => 59,
				'icon_url'   => 'dashicons-admin-customizer',
				'redirect'   => false,
			)
		);
	}
);

/* Theme Settings field group also shows on language sub-pages j9-settings-{lang}. */
add_filter(
	'acf/location/rule_match/options_page',
	function ( $match, $rule, $screen ) {
		if ( $match || empty( $screen['options_page'] ) || 'j9-settings' !== ( $rule['value'] ?? '' ) ) {
			return $match;
		}
		$is_lang_page = 0 === strpos( (string) $screen['options_page'], 'j9-settings-' );
		return '==' === $rule['operator'] ? $is_lang_page : ! $is_lang_page;
	},
	10,
	3
);

/* Warn when ACF Pro (or a compatible plugin with Flexible Content) is missing. */
add_action(
	'admin_notices',
	function () {
		if ( function_exists( 'acf_add_options_page' ) && function_exists( 'get_field' ) ) {
			if ( current_user_can( 'edit_theme_options' ) && '' === (string) j9_option( 'j9_ref_url' ) ) {
				echo '<div class="notice notice-warning"><p><strong>Jaya9:</strong> Referral URL is empty. /go/ redirects to the homepage until it is set in <a href="' . esc_url( admin_url( 'admin.php?page=j9-settings' ) ) . '">Theme Settings</a>.</p></div>';
			}
			return;
		}
		echo '<div class="notice notice-error"><p><strong>Jaya9:</strong> the theme needs <strong>ACF Pro</strong> (Flexible Content, Repeater, Options Page). Install and activate it.</p></div>';
	}
);

/**
 * Page field with fallback when ACF is missing.
 */
function j9_field( $name, $post_id = false, $fallback = '' ) {
	if ( ! function_exists( 'get_field' ) ) {
		return $fallback;
	}
	$value = get_field( $name, $post_id );
	return j9_is_empty( $value ) ? $fallback : $value;
}

/* Links to /go/ inside WYSIWYG fields get rel="noopener nofollow" automatically. */
add_filter(
	'acf/format_value/type=wysiwyg',
	function ( $value ) {
		if ( ! is_string( $value ) || false === strpos( $value, 'go' ) ) {
			return $value;
		}
		return preg_replace_callback(
			'#<a\s[^>]*href=(["\'])([^"\']+)\1[^>]*>#i',
			function ( $m ) {
				if ( ! j9_is_go_url( $m[2] ) ) {
					return $m[0];
				}
				$tag = preg_replace( '#\srel=(["\'])[^"\']*\1#i', '', $m[0] );
				return preg_replace( '#^<a\s#i', '<a rel="noopener nofollow" ', $tag );
			},
			$value
		);
	},
	20
);

/* External links in the disclaimer open in a new tab with rel="noopener nofollow". */
function j9_external_links( $html ) {
	$home = wp_parse_url( home_url(), PHP_URL_HOST );
	return preg_replace_callback(
		'#<a\s[^>]*href=(["\'])(https?://[^"\']+)\1[^>]*>#i',
		function ( $m ) use ( $home ) {
			if ( wp_parse_url( $m[2], PHP_URL_HOST ) === $home ) {
				return $m[0];
			}
			$tag = preg_replace( '#\s(rel|target)=(["\'])[^"\']*\2#i', '', $m[0] );
			return preg_replace( '#^<a\s#i', '<a target="_blank" rel="noopener nofollow" ', $tag );
		},
		(string) $html
	);
}
