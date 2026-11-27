<?php
/**
 * Block: Chips (pills such as "🍭 Sweet Bonanza").
 *
 * @package Jaya9
 */

defined( 'ABSPATH' ) || exit;

$j9_row   = $args['row'] ?? array();
$j9_items = array_filter( (array) ( $j9_row['items'] ?? array() ), fn( $i ) => '' !== trim( (string) ( $i['text'] ?? '' ) ) );
if ( ! $j9_items ) {
	return;
}
$j9_decorative = ! isset( $j9_row['decorative'] ) || $j9_row['decorative'];
?>
<div class="chips"<?php echo $j9_decorative ? ' aria-hidden="true"' : ''; ?>>
	<?php foreach ( $j9_items as $j9_item ) : ?>
		<span class="chip"><?php echo esc_html( trim( ( $j9_item['emoji'] ?? '' ) . ' ' . $j9_item['text'] ) ); ?></span>
	<?php endforeach; ?>
</div>
