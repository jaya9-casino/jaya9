<?php
/**
 * Block: Banner (blue / orange). Empty fields come from Theme Settings -> Banners.
 *
 * @package Jaya9
 */

defined( 'ABSPATH' ) || exit;

$j9_row   = $args['row'] ?? array();
$j9_style = 'orange' === ( $j9_row['style'] ?? 'blue' ) ? 'orange' : 'blue';
$j9_def   = (array) j9_option(
	'j9_banner_' . $j9_style,
	array(
		'title'       => '',
		'subtitle'    => '',
		'button_text' => 'orange' === $j9_style ? 'Play Now' : 'Get Bonus',
		'button_url'  => '',
	)
);

$j9_get = function ( $key ) use ( $j9_row, $j9_def ) {
	$v = trim( (string) ( $j9_row[ $key ] ?? '' ) );
	return '' !== $v ? $v : trim( (string) ( $j9_def[ $key ] ?? '' ) );
};

$j9_title = $j9_get( 'title' );
if ( '' === $j9_title ) {
	return;
}
$j9_btn_class = 'btn btn-lg promo-btn ' . ( 'orange' === $j9_style ? 'btn-light' : 'btn-primary' );
?>
<aside class="promo-banner<?php echo 'orange' === $j9_style ? ' promo-banner--orange' : ''; ?>">
	<?php echo j9_logo_img( 'promo-logo', true ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
	<div class="promo-text">
		<p class="promo-title"><?php echo esc_html( $j9_title ); ?></p>
		<?php if ( $j9_get( 'subtitle' ) ) : ?>
			<p class="promo-subtitle"><?php echo esc_html( $j9_get( 'subtitle' ) ); ?></p>
		<?php endif; ?>
	</div>
	<?php echo j9_button( $j9_get( 'button_text' ), $j9_get( 'button_url' ), $j9_btn_class ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
</aside>
