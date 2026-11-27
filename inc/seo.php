<?php
/**
 * SEO additions on top of Yoast: FAQPage schema, hreflang and lang (without Polylang), robots.txt.
 * Title, description, canonical, OG and WebSite/Organization/WebPage schema are Yoast's job.
 *
 * @package Jaya9
 */

defined( 'ABSPATH' ) || exit;

/**
 * FAQ items of a page from FAQ blocks with "Add FAQ schema" on.
 *
 * @return array[] [ [ 'q' => ..., 'a' => ... ], ... ]
 */
function j9_faq_items( $post_id ) {
	$items = array();
	foreach ( j9_get_blocks( $post_id ) as $row ) {
		if ( 'faq' !== ( $row['acf_fc_layout'] ?? '' ) ) {
			continue;
		}
		if ( isset( $row['schema'] ) && ! $row['schema'] ) {
			continue;
		}
		foreach ( (array) ( $row['items'] ?? array() ) as $item ) {
			$q = trim( wp_strip_all_tags( $item['question'] ?? '' ) );
			$a = trim( preg_replace( '/\s+/', ' ', wp_strip_all_tags( $item['answer'] ?? '' ) ) );
			if ( '' !== $q && '' !== $a ) {
				$items[] = array(
					'q' => $q,
					'a' => $a,
				);
			}
		}
	}
	return $items;
}

/**
 * FAQPage node.
 */
function j9_faq_schema_node( $post_id, $canonical ) {
	$entities = array();
	foreach ( j9_faq_items( $post_id ) as $item ) {
		$entities[] = array(
			'@type'          => 'Question',
			'name'           => $item['q'],
			'acceptedAnswer' => array(
				'@type' => 'Answer',
				'text'  => $item['a'],
			),
		);
	}
	return array(
		'@type'      => 'FAQPage',
		'@id'        => $canonical . '#faq',
		'url'        => $canonical,
		'inLanguage' => j9_html_lang(),
		'mainEntity' => $entities,
	);
}

/**
 * Current page id for FAQ purposes (pages only).
 */
function j9_faq_page_id() {
	return is_page() ? (int) get_queried_object_id() : 0;
}

/* Yoast: FAQPage goes into Yoast's own @graph. */
add_filter(
	'wpseo_schema_graph_pieces',
	function ( $pieces, $context ) {
		if ( ! class_exists( '\Yoast\WP\SEO\Generators\Schema\Abstract_Schema_Piece' ) ) {
			return $pieces;
		}
		if ( ! class_exists( 'J9_FAQ_Schema' ) ) {
			// phpcs:ignore Generic.Classes.DuplicateClassName
			class J9_FAQ_Schema extends \Yoast\WP\SEO\Generators\Schema\Abstract_Schema_Piece {
				public function is_needed() {
					$id = j9_faq_page_id();
					return $id && ! empty( j9_faq_items( $id ) );
				}
				public function generate() {
					$node = j9_faq_schema_node( j9_faq_page_id(), $this->context->canonical );
					if ( ! empty( $this->context->main_schema_id ) ) {
						$node['isPartOf'] = array( '@id' => $this->context->main_schema_id );
					}
					return $node;
				}
			}
		}
		$pieces[] = new J9_FAQ_Schema();
		return $pieces;
	},
	11,
	2
);

/* Without Yoast: standalone FAQPage JSON-LD so the markup is never lost. */
add_action(
	'wp_head',
	function () {
		if ( defined( 'WPSEO_VERSION' ) ) {
			return;
		}
		$id = j9_faq_page_id();
		if ( ! $id || empty( j9_faq_items( $id ) ) ) {
			return;
		}
		$node = array( '@context' => 'https://schema.org' ) + j9_faq_schema_node( $id, j9_canonical_url() );
		echo '<script type="application/ld+json">' . wp_json_encode( $node, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE ) . "</script>\n";
	},
	30
);

/**
 * Canonical of the current page: Yoast first, then WordPress.
 */
function j9_canonical_url() {
	if ( function_exists( 'YoastSEO' ) ) {
		$canonical = YoastSEO()->meta->for_current_page()->canonical;
		if ( $canonical ) {
			return $canonical;
		}
	}
	if ( is_front_page() ) {
		return home_url( '/' );
	}
	$url = is_singular() ? wp_get_canonical_url() : '';
	return $url ? $url : home_url( add_query_arg( array() ) );
}

/**
 * html lang of the site. Filter j9_html_lang to change.
 */
function j9_html_lang() {
	if ( function_exists( 'pll_current_language' ) ) {
		$locale = pll_current_language( 'locale' );
		return $locale ? str_replace( '_', '-', $locale ) : 'en-BD';
	}
	return apply_filters( 'j9_html_lang', 'en-BD' );
}

/* lang="en-BD" while Polylang is off (Polylang sets lang itself). */
add_filter(
	'language_attributes',
	function ( $output ) {
		if ( function_exists( 'pll_current_language' ) ) {
			return $output;
		}
		return preg_replace( '/lang="[^"]*"/', 'lang="' . esc_attr( j9_html_lang() ) . '"', $output );
	}
);

/* hreflang en-BD + x-default on the canonical while Polylang is off. */
add_action(
	'wp_head',
	function () {
		if ( function_exists( 'pll_current_language' ) || ! ( is_singular() || is_front_page() ) ) {
			return;
		}
		$url = j9_canonical_url();
		printf( '<link rel="alternate" hreflang="%s" href="%s">' . "\n", esc_attr( j9_html_lang() ), esc_url( $url ) );
		printf( '<link rel="alternate" hreflang="x-default" href="%s">' . "\n", esc_url( $url ) );
	},
	5
);

/* robots.txt: Disallow: /go/ */
add_filter(
	'robots_txt',
	function ( $output ) {
		if ( false !== strpos( $output, 'Disallow: /go/' ) ) {
			return $output;
		}
		if ( false !== strpos( $output, "User-agent: *\n" ) ) {
			return preg_replace( "/User-agent: \*\n/", "User-agent: *\nDisallow: /go/\n", $output, 1 );
		}
		return rtrim( $output ) . "\n\nUser-agent: *\nDisallow: /go/\n";
	},
	99
);
