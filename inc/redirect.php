<?php
/**
 * /go/ -> Referral URL from Theme Settings (302, noindex, no-store).
 * Works with a language prefix too: /bn/go/ uses the BN settings.
 *
 * The path is matched on the request itself, so no rewrite rules or permalink flush are needed
 * and Polylang language rewrites cannot interfere.
 *
 * @package Jaya9
 */

defined( 'ABSPATH' ) || exit;

add_action(
	'parse_request',
	function () {
		$request = wp_parse_url( isset( $_SERVER['REQUEST_URI'] ) ? wp_unslash( $_SERVER['REQUEST_URI'] ) : '', PHP_URL_PATH ); // phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotSanitized
		$base    = rtrim( (string) wp_parse_url( home_url(), PHP_URL_PATH ), '/' );
		if ( $base && 0 === strpos( (string) $request, $base ) ) {
			$request = substr( $request, strlen( $base ) );
		}
		if ( ! preg_match( '#^/(?:([a-z]{2})/)?go/?$#', (string) $request, $m ) ) {
			return;
		}

		$options_id = 'options';
		if ( ! empty( $m[1] ) && function_exists( 'pll_default_language' ) && pll_default_language( 'slug' ) !== $m[1] ) {
			$options_id = 'options_' . $m[1];
		}

		$url = '';
		if ( function_exists( 'get_field' ) ) {
			$url = (string) get_field( 'j9_ref_url', $options_id );
			if ( '' === $url && 'options' !== $options_id ) {
				$url = (string) get_field( 'j9_ref_url', 'options' );
			}
		}
		$url = $url ? $url : home_url( '/' );

		nocache_headers();
		header( 'Cache-Control: no-store, no-cache, must-revalidate, max-age=0' );
		header( 'X-Robots-Tag: noindex, nofollow' );
		// External affiliate URL by design: wp_redirect, not wp_safe_redirect.
		wp_redirect( esc_url_raw( $url ), 302, 'Jaya9' ); // phpcs:ignore WordPress.Security.SafeRedirect.wp_redirect_wp_redirect
		exit;
	},
	1
);

/* W3 Total Cache must never cache /go/ even if it is not excluded in its settings. */
add_action(
	'parse_request',
	function () {
		if ( ! defined( 'DONOTCACHEPAGE' ) && isset( $_SERVER['REQUEST_URI'] ) && preg_match( '#/(?:[a-z]{2}/)?go/?(\?|$)#', (string) $_SERVER['REQUEST_URI'] ) ) { // phpcs:ignore
			define( 'DONOTCACHEPAGE', true );
		}
	},
	0
);
