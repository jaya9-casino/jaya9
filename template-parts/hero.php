<?php
/**
 * Hero: badge, H1, intro, buttons, cover. Types: full (as in the design) / simple (one column).
 *
 * @package Jaya9
 */

defined( 'ABSPATH' ) || exit;

$j9_id    = get_the_ID();
$j9_type  = j9_field( 'j9_hero_type', $j9_id, 'full' );
$j9_badge = (string) j9_field( 'j9_hero_badge', $j9_id, '' );
$j9_badge = '' === $j9_badge ? (string) j9_option( 'j9_hero_badge', 'Bangladesh · BDT' ) : $j9_badge;
$j9_badge = '-' === trim( $j9_badge ) ? '' : $j9_badge;
$j9_h1    = (string) j9_field( 'j9_h1', $j9_id, get_the_title() );
$j9_text  = j9_field( 'j9_hero_text', $j9_id, '' );
$j9_cover = 'full' === $j9_type ? (int) j9_field( 'j9_hero_image', $j9_id, 0 ) : 0;

$j9_show_buttons = function_exists( 'get_field' ) ? get_field( 'j9_hero_buttons_show', $j9_id ) : null;
if ( null === $j9_show_buttons ) {
	$j9_show_buttons = ( 'full' === $j9_type );
}
$j9_buttons = (array) j9_field( 'j9_hero_buttons', $j9_id, array() );

$j9_classes = 'hero' . ( $j9_cover ? '' : ' hero--single' );
?>
<section class="<?php echo esc_attr( $j9_classes ); ?>">
	<div class="container hero-grid">
		<div class="hero-text">
			<?php if ( $j9_badge ) : ?>
				<span class="badge"><?php echo esc_html( $j9_badge ); ?></span>
			<?php endif; ?>

			<h1><?php echo esc_html( $j9_h1 ); ?></h1>

			<?php
			if ( $j9_text ) {
				echo wp_kses_post( $j9_text );
			}
			?>

			<?php if ( $j9_show_buttons ) : ?>
				<div class="hero-buttons">
					<?php
					echo j9_button( // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
						! empty( $j9_buttons['primary_text'] ) ? $j9_buttons['primary_text'] : j9_option( 'j9_bonus_text', 'Get Bonus' ),
						! empty( $j9_buttons['primary_url'] ) ? $j9_buttons['primary_url'] : j9_buttons_url(),
						'btn btn-primary btn-lg'
					);
					echo j9_button( // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
						! empty( $j9_buttons['secondary_text'] ) ? $j9_buttons['secondary_text'] : j9_option( 'j9_login_text', 'Login' ),
						! empty( $j9_buttons['secondary_url'] ) ? $j9_buttons['secondary_url'] : j9_buttons_url(),
						'btn btn-outline btn-lg'
					);
					?>
				</div>
			<?php endif; ?>
		</div>

		<?php if ( $j9_cover ) : ?>
			<figure class="hero-cover">
				<?php
				echo wp_get_attachment_image(
					$j9_cover,
					'large',
					false,
					array(
						'class'         => 'no-lazy skip-lazy',
						'data-no-lazy'  => '1',
						'loading'       => 'eager',
						'fetchpriority' => 'high',
						'decoding'      => 'async',
						'sizes'         => '(max-width: 960px) 100vw, 600px',
					)
				);
				?>
			</figure>
		<?php endif; ?>
	</div>
</section>
