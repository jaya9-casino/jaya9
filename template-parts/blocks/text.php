<?php
/**
 * Block: Text (WYSIWYG paragraphs; lists inside get the design styles automatically).
 *
 * @package Jaya9
 */

defined( 'ABSPATH' ) || exit;

$j9_content = $args['row']['content'] ?? '';
if ( '' !== trim( wp_strip_all_tags( (string) $j9_content ) ) ) {
	echo wp_kses_post( $j9_content ) . "\n";
}
