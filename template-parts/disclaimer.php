<?php
/**
 * Responsible gaming disclaimer before the footer.
 *
 * @package Jaya9
 */

defined( 'ABSPATH' ) || exit;

$j9_text = j9_option( 'j9_disclaimer' );
if ( ! $j9_text ) {
	return;
}
$j9_age = j9_option( 'j9_age_badge', '18+' );
?>
<section class="disclaimer" aria-label="<?php echo esc_attr( j9_t( 'Responsible gaming disclaimer' ) ); ?>">
	<div class="container disclaimer-inner">
		<?php if ( $j9_age ) : ?>
			<span class="age-badge" aria-hidden="true"><?php echo esc_html( $j9_age ); ?></span>
		<?php endif; ?>
		<div><?php echo wp_kses_post( j9_external_links( wpautop( $j9_text ) ) ); ?></div>
	</div>
</section>
