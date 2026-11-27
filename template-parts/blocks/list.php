<?php
/**
 * Block: List. bullet (ul), numbered (ol), cards (.list-check + emoji), checks (.list-check + green check).
 *
 * @package Jaya9
 */

defined( 'ABSPATH' ) || exit;

$j9_row   = $args['row'] ?? array();
$j9_type  = $j9_row['type'] ?? 'bullet';
$j9_items = array_filter( (array) ( $j9_row['items'] ?? array() ), fn( $i ) => '' !== trim( (string) ( $i['text'] ?? '' ) ) );
if ( ! $j9_items ) {
	return;
}

$j9_tag   = 'numbered' === $j9_type ? 'ol' : 'ul';
$j9_class = in_array( $j9_type, array( 'cards', 'checks' ), true ) ? ' class="list-check"' : '';

echo '<' . $j9_tag . $j9_class . ">\n"; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
foreach ( $j9_items as $j9_item ) {
	$j9_prefix = '';
	if ( 'cards' === $j9_type ) {
		$j9_prefix = j9_emoji( $j9_item['emoji'] ?? '' );
	} elseif ( 'checks' === $j9_type ) {
		$j9_prefix = '<svg class="li-icon" aria-hidden="true"><use href="#i-check"/></svg>';
	}
	echo "\t<li>" . $j9_prefix . j9_kses_inline( $j9_item['text'] ) . "</li>\n"; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
}
echo '</' . $j9_tag . ">\n"; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
