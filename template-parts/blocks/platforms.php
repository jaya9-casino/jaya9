<?php
/**
 * Block: Platform cards (Android / iPhone).
 *
 * @package Jaya9
 */

defined( 'ABSPATH' ) || exit;

$j9_cards = array_filter( (array) ( $args['row']['cards'] ?? array() ), fn( $c ) => '' !== trim( (string) ( $c['title'] ?? '' ) ) );
if ( ! $j9_cards ) {
	return;
}
?>
<div class="platforms">
	<?php foreach ( $j9_cards as $j9_card ) : ?>
		<div class="platform">
			<?php echo j9_emoji( $j9_card['emoji'] ?? '', 'platform-icon' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
			<div><strong><?php echo esc_html( $j9_card['title'] ); ?></strong><span><?php echo esc_html( $j9_card['subtitle'] ?? '' ); ?></span></div>
		</div>
	<?php endforeach; ?>
</div>
