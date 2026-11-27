<?php
/**
 * Table of contents from H2 Heading blocks. Hidden when off or fewer than 3 items.
 *
 * @package Jaya9
 */

defined( 'ABSPATH' ) || exit;

$j9_id   = get_the_ID();
$j9_show = function_exists( 'get_field' ) ? get_field( 'j9_toc_show', $j9_id ) : true;
$j9_show = null === $j9_show ? true : (bool) $j9_show;
$j9_toc  = j9_get_toc( $j9_id );

if ( ! $j9_show || count( $j9_toc ) < 3 ) {
	return;
}

$j9_title = (string) j9_field( 'j9_toc_title', $j9_id, '' );
$j9_title = '' === $j9_title ? j9_t( 'Table of Contents' ) : $j9_title;
$j9_open  = function_exists( 'get_field' ) ? get_field( 'j9_toc_open', $j9_id ) : true;
$j9_open  = null === $j9_open ? true : (bool) $j9_open;
?>
<details class="toc"<?php echo $j9_open ? ' open' : ''; ?>>
	<summary><?php echo esc_html( $j9_title ); ?></summary>
	<ol>
		<?php foreach ( $j9_toc as $j9_item ) : ?>
			<li><a href="#<?php echo esc_attr( $j9_item['anchor'] ); ?>"><?php echo esc_html( $j9_item['text'] ); ?></a></li>
		<?php endforeach; ?>
	</ol>
</details>
