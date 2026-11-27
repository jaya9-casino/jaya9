<?php
/**
 * Admin: readable titles of collapsed blocks, table check, cache flush.
 *
 * @package Jaya9
 */

defined( 'ABSPATH' ) || exit;

/* Collapsed blocks show their content: "H2: Weekly Promotions", "Banner (orange): 20% Sports Reload". */
add_filter(
	'acf/fields/flexible_content/layout_title/name=j9_blocks',
	function ( $title, $field, $layout ) {
		$name    = $layout['name'] ?? '';
		$summary = '';

		switch ( $name ) {
			case 'heading':
				$level   = strtoupper( (string) get_sub_field( 'level' ) );
				$summary = ( $level ? $level : 'H2' ) . ': ' . get_sub_field( 'text' );
				break;
			case 'text':
			case 'callout':
				$summary = wp_trim_words( wp_strip_all_tags( (string) get_sub_field( 'text' === $name ? 'content' : 'text' ) ), 10 );
				break;
			case 'banner':
				$summary = '(' . ( get_sub_field( 'style' ) ? get_sub_field( 'style' ) : 'blue' ) . ') ' . get_sub_field( 'title' );
				break;
			case 'table':
				$head    = j9_split_cells( get_sub_field( 'header' ) );
				$summary = $head ? $head[0] : '';
				$cols    = count( $head );
				foreach ( (array) get_sub_field( 'rows' ) as $row ) {
					if ( $cols && count( j9_split_cells( $row['cells'] ?? '' ) ) !== $cols ) {
						$summary .= ' - WARNING: cell count differs from header';
						break;
					}
				}
				break;
			case 'faq':
				$summary = count( (array) get_sub_field( 'items' ) ) . ' questions';
				break;
			case 'list':
				$summary = get_sub_field( 'type' ) . ', ' . count( (array) get_sub_field( 'items' ) ) . ' items';
				break;
		}

		$summary = trim( wp_strip_all_tags( (string) $summary ) );
		return $summary ? $title . ': <span style="font-weight:400">' . esc_html( $summary ) . '</span>' : $title;
	},
	10,
	3
);

/**
 * Flush W3 Total Cache (when installed). Theme Settings and menus are on every page.
 */
function j9_flush_cache() {
	if ( function_exists( 'w3tc_flush_all' ) ) {
		w3tc_flush_all();
	}
}

add_action(
	'acf/save_post',
	function ( $post_id ) {
		if ( is_string( $post_id ) && 0 === strpos( $post_id, 'options' ) ) {
			j9_flush_cache();
		}
	},
	20
);
add_action( 'wp_update_nav_menu', 'j9_flush_cache' );
