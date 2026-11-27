<?php
/**
 * Block: Callout. info (Note), calc (calculation), warn (18+).
 *
 * @package Jaya9
 */

defined( 'ABSPATH' ) || exit;

$j9_row     = $args['row'] ?? array();
$j9_variant = in_array( $j9_row['variant'] ?? 'info', array( 'info', 'calc', 'warn' ), true ) ? $j9_row['variant'] : 'info';
$j9_text    = $j9_row['text'] ?? '';
if ( '' === trim( wp_strip_all_tags( (string) $j9_text ) ) ) {
	return;
}
$j9_defaults = array(
	'info' => 'ℹ️',
	'calc' => '🧮',
	'warn' => '🔞',
);
$j9_icon  = trim( (string) ( $j9_row['icon'] ?? '' ) );
$j9_icon  = '' === $j9_icon ? $j9_defaults[ $j9_variant ] : $j9_icon;
$j9_class = 'callout' . ( 'info' === $j9_variant ? '' : ' callout--' . $j9_variant );
?>
<div class="<?php echo esc_attr( $j9_class ); ?>">
	<?php echo j9_emoji( $j9_icon, 'callout-icon' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
	<div><?php echo wp_kses_post( $j9_text ); ?></div>
</div>
