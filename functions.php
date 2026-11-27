<?php
/**
 * Jaya9 Satellite theme bootstrap.
 *
 * Only loads the files from inc/. Logic lives there.
 *
 * @package Jaya9
 */

defined( 'ABSPATH' ) || exit;

define( 'J9_VERSION', '1.0.0' );
define( 'J9_DIR', get_template_directory() );
define( 'J9_URI', get_template_directory_uri() );

$j9_includes = array(
	'inc/setup.php',
	'inc/enqueue.php',
	'inc/editor.php',
	'inc/helpers.php',
	'inc/acf.php',
	'inc/flexible.php',
	'inc/seo.php',
	'inc/redirect.php',
	'inc/admin.php',
	'inc/demo.php',
);

foreach ( $j9_includes as $j9_file ) {
	require_once J9_DIR . '/' . $j9_file;
}

/* Polylang support is loaded only when Polylang is active. */
add_action(
	'after_setup_theme',
	function () {
		if ( function_exists( 'pll_current_language' ) ) {
			require_once J9_DIR . '/inc/polylang.php';
		}
	},
	1
);
