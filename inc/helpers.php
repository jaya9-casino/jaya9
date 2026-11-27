<?php
/**
 * Helpers used by templates.
 *
 * @package Jaya9
 */

defined( 'ABSPATH' ) || exit;

/**
 * ACF post_id of Theme Settings for the current language.
 * Default language (or no Polylang) -> 'options'; other languages -> 'options_{slug}'.
 */
function j9_options_id() {
	if ( function_exists( 'pll_current_language' ) && function_exists( 'pll_default_language' ) ) {
		$lang    = pll_current_language( 'slug' );
		$default = pll_default_language( 'slug' );
		if ( $lang && $default && $lang !== $default ) {
			return 'options_' . $lang;
		}
	}
	return 'options';
}

/**
 * Empty check that treats 0 / '0' as a real value.
 */
function j9_is_empty( $value ) {
	if ( is_array( $value ) ) {
		return empty( array_filter( $value, fn( $v ) => ! j9_is_empty( $v ) ) );
	}
	return null === $value || false === $value || '' === $value;
}

/**
 * Read a Theme Settings field for the current language.
 * Empty value in a translation falls back to the default language; then to $fallback.
 * Group fields are merged sub-field by sub-field.
 *
 * @param string $name     Field name.
 * @param mixed  $fallback Value when nothing is set.
 */
function j9_option( $name, $fallback = '' ) {
	if ( ! function_exists( 'get_field' ) ) {
		return $fallback;
	}
	$id    = j9_options_id();
	$value = get_field( $name, $id );

	if ( 'options' !== $id ) {
		$base = get_field( $name, 'options' );
		if ( is_array( $value ) && is_array( $base ) && ! isset( $value['ID'] ) ) {
			foreach ( $base as $k => $v ) {
				if ( ! isset( $value[ $k ] ) || j9_is_empty( $value[ $k ] ) ) {
					$value[ $k ] = $v;
				}
			}
		} elseif ( j9_is_empty( $value ) ) {
			$value = $base;
		}
	}

	/* Never saved yet: ACF can't resolve the field by name, so use the field's default_value. */
	if ( j9_is_empty( $value ) ) {
		$value = j9_field_default( $name );
	}

	if ( is_array( $fallback ) && is_array( $value ) ) {
		foreach ( $fallback as $k => $v ) {
			if ( ! isset( $value[ $k ] ) || j9_is_empty( $value[ $k ] ) ) {
				$value[ $k ] = $v;
			}
		}
		return $value;
	}

	return j9_is_empty( $value ) ? $fallback : $value;
}

/**
 * default_value of a Theme Settings field (group fields: array of sub-field defaults).
 */
function j9_field_default( $name ) {
	if ( ! function_exists( 'acf_get_field' ) ) {
		return null;
	}
	$field = acf_get_field( $name );
	if ( ! $field ) {
		return null;
	}
	if ( 'group' === $field['type'] ) {
		$out = array();
		foreach ( (array) ( $field['sub_fields'] ?? array() ) as $sub ) {
			$out[ $sub['name'] ] = $sub['default_value'] ?? '';
		}
		return $out;
	}
	$default = $field['default_value'] ?? null;
	if ( is_string( $default ) && 'wysiwyg' === $field['type'] ) {
		$default = wpautop( $default );
	}
	return $default;
}

/**
 * Theme string, translatable in Polylang (Languages -> Translations) or via .po.
 */
function j9_t( $string ) {
	if ( function_exists( 'pll__' ) ) {
		return pll__( $string );
	}
	// phpcs:ignore WordPress.WP.I18n.NonSingularStringLiteralText
	return __( $string, 'jaya9' );
}

/**
 * Is this URL the referral cloak /go/ (with or without language prefix or domain)?
 */
function j9_is_go_url( $url ) {
	$path = wp_parse_url( (string) $url, PHP_URL_PATH );
	$host = wp_parse_url( (string) $url, PHP_URL_HOST );
	if ( $host && wp_parse_url( home_url(), PHP_URL_HOST ) !== $host ) {
		return false;
	}
	return (bool) preg_match( '#^/(?:[a-z]{2}/)?go/?$#', (string) $path );
}

/**
 * Default URL for buttons (Theme Settings -> Buttons URL, default /go/).
 */
function j9_buttons_url() {
	return j9_option( 'j9_buttons_url', '/go/' );
}

/**
 * Button link. Links to /go/ get rel="noopener nofollow".
 */
function j9_button( $text, $url, $class = 'btn btn-primary' ) {
	if ( '' === trim( (string) $text ) ) {
		return '';
	}
	$url = $url ? $url : j9_buttons_url();
	$rel = j9_is_go_url( $url ) ? ' rel="noopener nofollow"' : '';
	return sprintf( '<a href="%s" class="%s"%s>%s</a>', esc_url( $url ), esc_attr( $class ), $rel, esc_html( $text ) );
}

/**
 * Icons available in the SVG sprite (template-parts/svg-sprite.php).
 */
function j9_icons() {
	return array(
		'list'    => 'List',
		'user'    => 'User',
		'flag'    => 'Flag',
		'bat'     => 'Cricket bat',
		'slot'    => 'Slot machine',
		'gift'    => 'Gift',
		'key'     => 'Key',
		'phone'   => 'Phone',
		'wallet'  => 'Wallet',
		'shield'  => 'Shield',
		'headset' => 'Headset',
		'chat'    => 'Chat',
		'help'    => 'Help',
	);
}

/**
 * Icon from the sprite.
 */
function j9_icon( $name, $class = '' ) {
	if ( ! $name || 'none' === $name ) {
		return '';
	}
	$class_attr = $class ? ' class="' . esc_attr( $class ) . '"' : '';
	return sprintf( '<svg%s aria-hidden="true"><use href="#i-%s"/></svg>', $class_attr, esc_attr( $name ) );
}

/**
 * Decorative emoji: span.emo aria-hidden.
 */
function j9_emoji( $emoji, $class = 'emo' ) {
	$emoji = trim( (string) $emoji );
	if ( '' === $emoji ) {
		return '';
	}
	return sprintf( '<span class="%s" aria-hidden="true">%s</span>', esc_attr( $class ), esc_html( $emoji ) );
}

/**
 * Inline HTML allowed in table cells and list items.
 */
function j9_kses_inline( $html ) {
	return wp_kses(
		(string) $html,
		array(
			'strong' => array(),
			'b'      => array(),
			'em'     => array(),
			'i'      => array(),
			'br'     => array(),
			'a'      => array(
				'href'   => true,
				'rel'    => true,
				'target' => true,
				'title'  => true,
			),
		)
	);
}

/**
 * Split "a | b | c" into cells.
 */
function j9_split_cells( $line ) {
	if ( '' === trim( (string) $line ) ) {
		return array();
	}
	return array_map( 'trim', explode( '|', (string) $line ) );
}

/**
 * Logo <img> from Theme Settings, theme logo as fallback.
 */
function j9_logo_img( $class = '', $lazy = false ) {
	$logo = j9_option( 'j9_logo' );
	$alt  = j9_t( 'Jaya9 logo' );
	$atts = $class ? ' class="' . esc_attr( $class ) . '"' : '';
	$atts .= $lazy ? ' loading="lazy"' : '';

	if ( is_array( $logo ) && ! empty( $logo['url'] ) ) {
		return sprintf(
			'<img src="%s" alt="%s" width="%d" height="%d"%s>',
			esc_url( $logo['url'] ),
			esc_attr( $logo['alt'] ? $logo['alt'] : $alt ),
			(int) $logo['width'],
			(int) $logo['height'],
			$atts
		);
	}
	return sprintf( '<img src="%s" alt="%s" width="360" height="91"%s>', esc_url( J9_URI . '/assets/img/logo.png' ), esc_attr( $alt ), $atts );
}
