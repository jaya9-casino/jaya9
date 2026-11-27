<?php
/**
 * Block: Heading. H2 with an icon badge (.h-icon), H3/H4 with a decorative emoji.
 * The <section id> wrapper for H2 is opened by j9_render_blocks().
 *
 * @package Jaya9
 */

defined( 'ABSPATH' ) || exit;

$j9_row   = $args['row'] ?? array();
$j9_level = in_array( $j9_row['level'] ?? 'h2', array( 'h2', 'h3', 'h4' ), true ) ? $j9_row['level'] : 'h2';
$j9_text  = trim( (string) ( $j9_row['text'] ?? '' ) );
if ( '' === $j9_text ) {
	return;
}

$j9_prefix = '';
if ( 'h2' === $j9_level ) {
	$j9_icon = $j9_row['icon'] ?? 'none';
	if ( $j9_icon && 'none' !== $j9_icon ) {
		$j9_prefix = '<span class="h-icon" aria-hidden="true">' . j9_icon( $j9_icon ) . '</span>';
	}
} else {
	$j9_prefix = j9_emoji( $j9_row['emoji'] ?? '' );
}

printf( '<%1$s>%2$s%3$s</%1$s>' . "\n", $j9_level, $j9_prefix, esc_html( $j9_text ) ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
