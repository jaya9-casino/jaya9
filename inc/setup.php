<?php
/**
 * Theme supports, menus, text domain.
 *
 * @package Jaya9
 */

defined( 'ABSPATH' ) || exit;

add_action(
	'after_setup_theme',
	function () {
		load_theme_textdomain( 'jaya9', J9_DIR . '/languages' );

		add_theme_support( 'title-tag' );
		add_theme_support( 'post-thumbnails' );
		add_theme_support( 'html5', array( 'search-form', 'gallery', 'caption', 'style', 'script', 'navigation-widgets' ) );

		register_nav_menus(
			array(
				'header_menu'   => __( 'Header Menu', 'jaya9' ),
				'footer_menu_1' => __( 'Footer Menu 1', 'jaya9' ),
				'footer_menu_2' => __( 'Footer Menu 2', 'jaya9' ),
			)
		);

		$GLOBALS['content_width'] = 860;
	}
);

/**
 * Links to /go/ in menus get rel="noopener nofollow".
 */
add_filter(
	'nav_menu_link_attributes',
	function ( $atts ) {
		if ( ! empty( $atts['href'] ) && j9_is_go_url( $atts['href'] ) ) {
			$atts['rel'] = 'noopener nofollow';
		}
		return $atts;
	}
);
