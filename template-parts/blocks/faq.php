<?php
/**
 * Block: FAQ accordion (details + h3). Schema: inc/seo.php.
 *
 * @package Jaya9
 */

defined( 'ABSPATH' ) || exit;

$j9_row   = $args['row'] ?? array();
$j9_items = array_filter( (array) ( $j9_row['items'] ?? array() ), fn( $i ) => '' !== trim( (string) ( $i['question'] ?? '' ) ) );
if ( ! $j9_items ) {
	return;
}
$j9_first_open = ! isset( $j9_row['first_open'] ) || $j9_row['first_open'];
?>
<div class="faq">
	<?php foreach ( array_values( $j9_items ) as $j9_i => $j9_item ) : ?>
		<details<?php echo ( 0 === $j9_i && $j9_first_open ) ? ' open' : ''; ?>>
			<summary><h3><?php echo esc_html( $j9_item['question'] ); ?></h3></summary>
			<?php echo wp_kses_post( $j9_item['answer'] ?? '' ); ?>
		</details>
	<?php endforeach; ?>
</div>
