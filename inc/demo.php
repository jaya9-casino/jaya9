<?php
/**
 * Demo import: Appearance -> Jaya9 Demo Import.
 *
 * Creates the 11 pages, the three menus, uploads the logo and the cover, fills the homepage
 * with the content of the design (assets/demo/home.json) and sets it as the front page.
 * Existing pages (same slug) are never overwritten.
 *
 * @package Jaya9
 */

defined( 'ABSPATH' ) || exit;

/**
 * Pages of the site: slug => [ title, hero type ].
 */
function j9_demo_pages() {
	return array(
		'home'               => array( 'Home', 'full' ),
		'login'              => array( 'Login', 'full' ),
		'app'                => array( 'App', 'full' ),
		'slots'              => array( 'Slots', 'full' ),
		'bonuses'            => array( 'Bonuses', 'full' ),
		'providers'          => array( 'Providers', 'full' ),
		'about-us'           => array( 'About Us', 'simple' ),
		'contacts'           => array( 'Contacts', 'simple' ),
		'terms-of-use'       => array( 'Terms of Use', 'simple' ),
		'privacy-policy'     => array( 'Privacy Policy', 'simple' ),
		'responsible-gaming' => array( 'Responsible Gaming', 'simple' ),
	);
}

add_action(
	'admin_menu',
	function () {
		add_theme_page( 'Jaya9 Demo Import', 'Jaya9 Demo Import', 'edit_theme_options', 'j9-demo', 'j9_demo_screen' );
	}
);

/* Hint after theme activation. */
add_action(
	'admin_notices',
	function () {
		if ( get_option( 'j9_demo_imported' ) || ! current_user_can( 'edit_theme_options' ) ) {
			return;
		}
		$screen = get_current_screen();
		if ( $screen && in_array( $screen->id, array( 'themes', 'dashboard' ), true ) ) {
			echo '<div class="notice notice-info"><p><strong>Jaya9:</strong> pages, menus and the homepage from the design can be created in one click: <a href="' . esc_url( admin_url( 'themes.php?page=j9-demo' ) ) . '">Appearance &rarr; Jaya9 Demo Import</a>.</p></div>';
		}
	}
);

/**
 * Admin screen.
 */
function j9_demo_screen() {
	if ( ! current_user_can( 'edit_theme_options' ) ) {
		return;
	}
	$log = null;
	if ( isset( $_POST['j9_demo_nonce'] ) && wp_verify_nonce( sanitize_key( $_POST['j9_demo_nonce'] ), 'j9_demo' ) ) {
		$log = j9_demo_import();
	}
	?>
	<div class="wrap">
		<h1>Jaya9 Demo Import</h1>
		<p>Creates 11 pages (Home, Login, App, Slots, Bonuses, Providers, About Us, Contacts, Terms of Use, Privacy Policy, Responsible Gaming), the Header Menu and two footer menus, uploads the logo and the cover, fills the homepage with the content of the design and sets it as the front page.</p>
		<p>Pages that already exist (same slug) are skipped and never overwritten. Requires ACF Pro.</p>
		<?php if ( is_array( $log ) ) : ?>
			<div class="notice notice-success"><ul style="list-style:disc;padding-left:20px">
				<?php foreach ( $log as $line ) : ?>
					<li><?php echo esc_html( $line ); ?></li>
				<?php endforeach; ?>
			</ul></div>
		<?php endif; ?>
		<form method="post">
			<?php wp_nonce_field( 'j9_demo', 'j9_demo_nonce' ); ?>
			<?php submit_button( 'Run import', 'primary', 'submit', false, function_exists( 'update_field' ) ? array() : array( 'disabled' => 'disabled' ) ); ?>
		</form>
	</div>
	<?php
}

/**
 * Upload a theme image to the media library once.
 */
function j9_demo_media( $file, $title, $alt ) {
	$existing = get_posts(
		array(
			'post_type'   => 'attachment',
			'meta_key'    => '_j9_demo_file', // phpcs:ignore WordPress.DB.SlowDBQuery.slow_db_query_meta_key
			'meta_value'  => $file, // phpcs:ignore WordPress.DB.SlowDBQuery.slow_db_query_meta_value
			'numberposts' => 1,
			'fields'      => 'ids',
		)
	);
	if ( $existing ) {
		return (int) $existing[0];
	}
	require_once ABSPATH . 'wp-admin/includes/file.php';
	require_once ABSPATH . 'wp-admin/includes/media.php';
	require_once ABSPATH . 'wp-admin/includes/image.php';

	$tmp = wp_tempnam( $file );
	copy( J9_DIR . '/assets/img/' . $file, $tmp );
	$id = media_handle_sideload(
		array(
			'name'     => $file,
			'tmp_name' => $tmp,
		),
		0,
		$title
	);
	if ( is_wp_error( $id ) ) {
		wp_delete_file( $tmp );
		return 0;
	}
	update_post_meta( $id, '_wp_attachment_image_alt', $alt );
	update_post_meta( $id, '_j9_demo_file', $file );
	return (int) $id;
}

/**
 * Run the import. Returns log lines.
 */
function j9_demo_import() {
	$log = array();
	if ( ! function_exists( 'update_field' ) ) {
		return array( 'ACF Pro is not active - nothing was imported.' );
	}

	$data = json_decode( (string) file_get_contents( J9_DIR . '/assets/demo/home.json' ), true ); // phpcs:ignore WordPress.WP.AlternativeFunctions.file_get_contents_file_get_contents

	/* Media. */
	$logo  = j9_demo_media( 'logo.png', 'Jaya9 logo', 'Jaya9 logo' );
	$cover = j9_demo_media( 'cover.webp', 'Jaya9 cover', 'Jaya9 cricket betting, slots and live casino in Bangladesh' );
	$log[] = sprintf( 'Media: logo #%d, cover #%d.', $logo, $cover );

	/* Theme Settings: logo (other fields use their defaults until edited). */
	if ( $logo && ! get_field( 'j9_logo', 'options' ) ) {
		update_field( 'field_j9_s_logo', $logo, 'options' );
		$log[] = 'Theme Settings: logo set.';
	}

	/* Pages. */
	$ids = array();
	foreach ( j9_demo_pages() as $slug => $page ) {
		$found = get_page_by_path( $slug );

		/* WordPress creates a draft "Privacy Policy" with placeholder text on install: reuse and publish it. */
		if ( $found && 'draft' === $found->post_status && (int) get_option( 'wp_page_for_privacy_policy' ) === (int) $found->ID ) {
			wp_update_post(
				array(
					'ID'           => $found->ID,
					'post_status'  => 'publish',
					'post_content' => '',
				)
			);
			update_field( 'field_j9_p_hero_type', $page[1], $found->ID );
			update_field( 'field_j9_p_btn_show', 0, $found->ID );
			$ids[ $slug ] = $found->ID;
			$log[]        = sprintf( 'Page "%s": WordPress draft with placeholder text published empty (hero: %s).', $page[0], $page[1] );
			continue;
		}

		if ( $found ) {
			$ids[ $slug ] = $found->ID;
			$log[]        = sprintf( 'Page "%s" exists - skipped.', $page[0] );
			continue;
		}
		$id = wp_insert_post(
			array(
				'post_type'   => 'page',
				'post_status' => 'publish',
				'post_title'  => $page[0],
				'post_name'   => $slug,
			)
		);
		if ( ! $id || is_wp_error( $id ) ) {
			continue;
		}
		$ids[ $slug ] = $id;
		update_field( 'field_j9_p_hero_type', $page[1], $id );
		update_field( 'field_j9_p_btn_show', 'full' === $page[1] ? 1 : 0, $id );

		if ( 'home' === $slug && $data ) {
			update_field( 'field_j9_p_h1', $data['h1'], $id );
			update_field( 'field_j9_p_hero_text', $data['intro'], $id );
			if ( $cover ) {
				update_field( 'field_j9_p_hero_image', $cover, $id );
			}
			update_field( 'field_j9_p_blocks', $data['blocks'], $id );
			update_post_meta( $id, '_yoast_wpseo_title', $data['title'] );
			update_post_meta( $id, '_yoast_wpseo_metadesc', $data['description'] );
			$log[] = sprintf( 'Homepage created with %d blocks from the design.', count( $data['blocks'] ) );
		} else {
			$log[] = sprintf( 'Page "%s" created (hero: %s).', $page[0], $page[1] );
		}
	}

	/* Front page. */
	if ( ! empty( $ids['home'] ) ) {
		update_option( 'show_on_front', 'page' );
		update_option( 'page_on_front', $ids['home'] );
		$log[] = 'Front page set to "Home".';
	}

	/* Menus. */
	$menus     = array(
		'header_menu'   => array( 'Header Menu', array( 'login', 'app', 'slots', 'bonuses', 'providers' ) ),
		'footer_menu_1' => array( 'Footer Menu 1', array( 'login', 'app', 'slots', 'bonuses', 'providers' ) ),
		'footer_menu_2' => array( 'Footer Menu 2', array( 'about-us', 'contacts', 'terms-of-use', 'privacy-policy', 'responsible-gaming' ) ),
	);
	$locations = (array) get_theme_mod( 'nav_menu_locations', array() );
	foreach ( $menus as $location => $menu ) {
		if ( ! empty( $locations[ $location ] ) && wp_get_nav_menu_object( $locations[ $location ] ) ) {
			$log[] = sprintf( '%s already assigned - skipped.', $menu[0] );
			continue;
		}
		$menu_obj = wp_get_nav_menu_object( $menu[0] );
		$menu_id  = $menu_obj ? $menu_obj->term_id : wp_create_nav_menu( $menu[0] );
		if ( is_wp_error( $menu_id ) ) {
			continue;
		}
		if ( ! $menu_obj ) {
			foreach ( $menu[1] as $slug ) {
				if ( empty( $ids[ $slug ] ) ) {
					continue;
				}
				wp_update_nav_menu_item(
					$menu_id,
					0,
					array(
						'menu-item-object-id' => $ids[ $slug ],
						'menu-item-object'    => 'page',
						'menu-item-type'      => 'post_type',
						'menu-item-status'    => 'publish',
					)
				);
			}
		}
		$locations[ $location ] = $menu_id;
		$log[]                  = sprintf( '%s created and assigned.', $menu[0] );
	}
	set_theme_mod( 'nav_menu_locations', $locations );

	update_option( 'j9_demo_imported', time() );
	j9_flush_cache();
	$log[] = 'Done. Set the Referral URL in Theme Settings.';
	return $log;
}
