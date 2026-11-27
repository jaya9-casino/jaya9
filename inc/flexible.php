<?php
/**
 * Flexible Content renderer and table of contents.
 *
 * Every H2 Heading block closes the previous <section> and opens <section id="anchor">.
 *
 * @package Jaya9
 */

defined( 'ABSPATH' ) || exit;

/**
 * Layouts that have a template in template-parts/blocks/.
 */
function j9_layouts() {
	return array( 'heading', 'text', 'table', 'list', 'callout', 'chips', 'platforms', 'banner', 'faq', 'image' );
}

/**
 * Rows of the Content blocks field (cached per post).
 */
function j9_get_blocks( $post_id = null ) {
	static $cache = array();
	$post_id = $post_id ? $post_id : get_the_ID();
	if ( ! isset( $cache[ $post_id ] ) ) {
		$rows              = function_exists( 'get_field' ) ? get_field( 'j9_blocks', $post_id ) : array();
		$cache[ $post_id ] = is_array( $rows ) ? $rows : array();
	}
	return $cache[ $post_id ];
}

/**
 * Unique anchors for H2 blocks: row index => anchor.
 */
function j9_block_anchors( $post_id = null ) {
	static $cache = array();
	$post_id = $post_id ? $post_id : get_the_ID();
	if ( isset( $cache[ $post_id ] ) ) {
		return $cache[ $post_id ];
	}
	$used    = array();
	$anchors = array();
	foreach ( j9_get_blocks( $post_id ) as $i => $row ) {
		if ( 'heading' !== ( $row['acf_fc_layout'] ?? '' ) || 'h2' !== ( $row['level'] ?? 'h2' ) ) {
			continue;
		}
		$base = sanitize_title( ! empty( $row['anchor'] ) ? $row['anchor'] : ( $row['text'] ?? '' ) );
		$base = $base ? $base : 'section';
		$slug = $base;
		$n    = 2;
		while ( isset( $used[ $slug ] ) ) {
			$slug = $base . '-' . $n++;
		}
		$used[ $slug ]   = true;
		$anchors[ $i ] = $slug;
	}
	$cache[ $post_id ] = $anchors;
	return $anchors;
}

/**
 * Table of contents items: [ [ 'anchor' => ..., 'text' => ... ], ... ].
 */
function j9_get_toc( $post_id = null ) {
	$rows    = j9_get_blocks( $post_id );
	$anchors = j9_block_anchors( $post_id );
	$items   = array();
	foreach ( $anchors as $i => $anchor ) {
		$row = $rows[ $i ];
		if ( isset( $row['in_toc'] ) && ! $row['in_toc'] ) {
			continue;
		}
		$items[] = array(
			'anchor' => $anchor,
			'text'   => $row['text'] ?? '',
		);
	}
	return $items;
}

/**
 * Output all blocks of the page.
 */
function j9_render_blocks( $post_id = null ) {
	$post_id = $post_id ? $post_id : get_the_ID();
	$rows    = j9_get_blocks( $post_id );
	$anchors = j9_block_anchors( $post_id );
	$open    = false;

	foreach ( $rows as $i => $row ) {
		$layout = $row['acf_fc_layout'] ?? '';
		if ( ! in_array( $layout, j9_layouts(), true ) ) {
			continue;
		}

		if ( isset( $anchors[ $i ] ) ) {
			if ( $open ) {
				echo "</section>\n";
			}
			printf( '<section id="%s">' . "\n", esc_attr( $anchors[ $i ] ) );
			$open = true;
		} elseif ( ! $open ) {
			echo "<section>\n";
			$open = true;
		}

		get_template_part(
			'template-parts/blocks/' . $layout,
			null,
			array(
				'row'     => $row,
				'index'   => $i,
				'post_id' => $post_id,
			)
		);
	}

	if ( $open ) {
		echo "</section>\n";
	}
}
