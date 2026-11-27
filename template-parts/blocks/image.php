<?php
/**
 * Block: Image in the text with an optional caption.
 *
 * @package Jaya9
 */

defined( 'ABSPATH' ) || exit;

$j9_row = $args['row'] ?? array();
$j9_img = (int) ( $j9_row['image'] ?? 0 );
if ( ! $j9_img ) {
	return;
}
?>
<figure class="j9-figure">
	<?php echo wp_get_attachment_image( $j9_img, 'large', false, array( 'loading' => 'lazy', 'sizes' => '(max-width: 900px) 100vw, 860px' ) ); ?>
	<?php if ( ! empty( $j9_row['caption'] ) ) : ?>
		<figcaption><?php echo esc_html( $j9_row['caption'] ); ?></figcaption>
	<?php endif; ?>
</figure>
