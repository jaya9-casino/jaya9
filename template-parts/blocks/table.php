<?php
/**
 * Block: Table. Header and cells are "a | b | c" lines. Always inside .table-wrap
 * (wide tables scroll horizontally inside their frame on mobile).
 *
 * @package Jaya9
 */

defined( 'ABSPATH' ) || exit;

$j9_row   = $args['row'] ?? array();
$j9_head  = j9_split_cells( $j9_row['header'] ?? '' );
$j9_rows  = (array) ( $j9_row['rows'] ?? array() );
$j9_class = 'facts' === ( $j9_row['style'] ?? 'default' ) ? ' class="table-facts"' : '';
if ( ! $j9_rows ) {
	return;
}

$j9_cols = count( $j9_head );
foreach ( $j9_rows as $j9_r ) {
	$j9_cols = max( $j9_cols, count( j9_split_cells( $j9_r['cells'] ?? '' ) ) );
}

$j9_dots = array(
	'dot-orange-dark' => 'pay-dot--bkash',
	'dot-orange'      => 'pay-dot--nagad',
	'dot-blue-light'  => 'pay-dot--rocket',
	'dot-blue-dark'   => 'pay-dot--bank',
);
?>
<div class="table-wrap">
	<table<?php echo $j9_class; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>>
		<?php if ( $j9_head ) : ?>
			<thead>
				<tr>
					<?php for ( $j9_c = 0; $j9_c < $j9_cols; $j9_c++ ) : ?>
						<th><?php echo j9_kses_inline( $j9_head[ $j9_c ] ?? '' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></th>
					<?php endfor; ?>
				</tr>
			</thead>
		<?php endif; ?>
		<tbody>
			<?php foreach ( $j9_rows as $j9_r ) : ?>
				<?php
				$j9_cells  = j9_split_cells( $j9_r['cells'] ?? '' );
				$j9_marker = $j9_r['marker'] ?? 'none';
				$j9_mark   = '';
				if ( 'emoji' === $j9_marker ) {
					$j9_mark = j9_emoji( $j9_r['emoji'] ?? '' );
				} elseif ( isset( $j9_dots[ $j9_marker ] ) ) {
					$j9_mark = '<span class="pay-dot ' . $j9_dots[ $j9_marker ] . '" aria-hidden="true"></span>';
				}
				?>
				<tr>
					<?php for ( $j9_c = 0; $j9_c < $j9_cols; $j9_c++ ) : ?>
						<td><?php echo ( 0 === $j9_c ? $j9_mark : '' ) . j9_kses_inline( $j9_cells[ $j9_c ] ?? '' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></td>
					<?php endfor; ?>
				</tr>
			<?php endforeach; ?>
		</tbody>
	</table>
</div>
