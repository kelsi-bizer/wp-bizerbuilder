<?php
/**
 * WP-Bizerbuilder Menu class.
 *
 * @package WP-Bizerbuilder
 */

namespace WP_Bizerbuilder\Admin;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Class that handles the admin menu.
 */
class Menu {

	/**
	 * The Admin instance.
	 *
	 * @var Admin
	 */
	protected $admin;

	/**
	 * Constructor.
	 */
	public function __construct( $admin ) {
		$this->admin = $admin;
		add_action( 'admin_menu', [ $this, 'add_admin_menu' ] );
	}

	/**
	 * Initialize the admin menu pages.
	 *
	 * @return void
	 */
	public function add_admin_menu() {
		add_menu_page(
			esc_html__( 'WP-Bizerbuilder', 'wp-bizerbuilder' ),
			esc_html__( 'WP-Bizerbuilder', 'wp-bizerbuilder' ),
			'manage_options',
			'wp-bizerbuilder',
			[ $this, 'render_list_plugins_page' ],
			'dashicons-admin-plugins',
			100
		);

		add_submenu_page(
			'wp-bizerbuilder',
			esc_html__( 'Generate New Plugin', 'wp-bizerbuilder' ),
			esc_html__( 'Generate New Plugin', 'wp-bizerbuilder' ),
			'manage_options',
			'wp-bizerbuilder-generate',
			[ $this, 'render_generate_plugin_page' ]
		);

		add_submenu_page(
			'wp-bizerbuilder',
			esc_html__( 'Settings', 'wp-bizerbuilder' ),
			esc_html__( 'Settings', 'wp-bizerbuilder' ),
			'manage_options',
			'wp-bizerbuilder-settings',
			[ $this, 'render_settings_page' ]
		);

		// Extend and Fix pages (they don't appear in the menu).
		add_submenu_page(
			'options.php',
			esc_html__( 'Extend Plugin', 'wp-bizerbuilder' ),
			esc_html__( 'Extend Plugin', 'wp-bizerbuilder' ),
			'manage_options',
			'wp-bizerbuilder-extend',
			[ $this, 'render_extend_plugin_page' ]
		);

		add_submenu_page(
			'options.php',
			esc_html__( 'Fix Plugin', 'wp-bizerbuilder' ),
			esc_html__( 'Fix Plugin', 'wp-bizerbuilder' ),
			'manage_options',
			'wp-bizerbuilder-fix',
			[ $this, 'render_fix_plugin_page' ]
		);

		add_submenu_page(
			'options.php',
			esc_html__( 'Explain Plugin', 'wp-bizerbuilder' ),
			esc_html__( 'Explain Plugin', 'wp-bizerbuilder' ),
			'manage_options',
			'wp-bizerbuilder-explain',
			[ $this, 'render_explain_plugin_page' ]
		);

		add_submenu_page(
			'options.php',
			esc_html__( 'Create Extension', 'wp-bizerbuilder' ),
			esc_html__( 'Create Extension', 'wp-bizerbuilder' ),
			'manage_options',
			'wp-bizerbuilder-extend-hooks',
			[ $this, 'render_extend_hooks_page' ]
		);

		add_submenu_page(
			'options.php',
			esc_html__( 'Extend Theme', 'wp-bizerbuilder' ),
			esc_html__( 'Extend Theme', 'wp-bizerbuilder' ),
			'manage_options',
			'wp-bizerbuilder-extend-theme',
			[ $this, 'render_extend_theme_page' ]
		);
	}

	/**
	 * Display the list of Bizerbuilders.
	 *
	 * @return void
	 */
	public function render_list_plugins_page() {
		include WP_BIZERBUILDER_DIR . 'views/page-list-plugins.php';
	}

	/**
	 * Display the plugin generation page.
	 *
	 * @return void
	 */
	public function render_generate_plugin_page() {
		include WP_BIZERBUILDER_DIR . 'views/page-generate-plugin.php';
	}

	/**
	 * Display the settings page.
	 *
	 * @return void
	 */
	public function render_settings_page() {
		include WP_BIZERBUILDER_DIR . 'views/page-settings.php';
	}

	/**
	 * Display the extend plugin page.
	 *
	 * @return void
	 */
	public function render_extend_plugin_page() {
		$this->validate_plugin( 'wp-bizerbuilder-extend-plugin' );
		include WP_BIZERBUILDER_DIR . 'views/page-extend-plugin.php';
	}

	/**
	 * Display the fix plugin page.
	 *
	 * @return void
	 */
	public function render_fix_plugin_page() {
		$this->validate_plugin( 'wp-bizerbuilder-fix-plugin' );
		include WP_BIZERBUILDER_DIR . 'views/page-fix-plugin.php';
	}

	/**
	 * Display the explain plugin page.
	 *
	 * @return void
	 */
	public function render_explain_plugin_page() {
		$this->validate_plugin( 'wp-bizerbuilder-explain-plugin' );
		include WP_BIZERBUILDER_DIR . 'views/page-explain-plugin.php';
	}

	/**
	 * Display the extend plugin with hooks page.
	 *
	 * @return void
	 */
	public function render_extend_hooks_page() {
		// Capability check.
		if ( ! current_user_can( 'manage_options' ) ) {
			wp_die( esc_html__( 'You do not have sufficient permissions to access this page.', 'wp-bizerbuilder' ) );
		}

		// Required params and nonce.
		if ( ! isset( $_GET['plugin'] ) ) { // phpcs:ignore WordPress.Security.NonceVerification
			wp_die( esc_html__( 'No plugin specified.', 'wp-bizerbuilder' ) );
		}
		$nonce_value = isset( $_GET['nonce'] ) ? sanitize_text_field( wp_unslash( $_GET['nonce'] ) ) : '';
		if ( ! $nonce_value || ! wp_verify_nonce( $nonce_value, 'wp-bizerbuilder-extend-hooks' ) ) {
			wp_die( esc_html__( 'Security check failed.', 'wp-bizerbuilder' ) );
		}

		// Sanitize and constrain plugin path inside plugins directory.
		$plugin_file  = sanitize_text_field( wp_unslash( $_GET['plugin'] ) ); // phpcs:ignore WordPress.Security.NonceVerification
		$plugin_file  = ltrim( str_replace( [ '..\\', '../', '\\' ], '/', $plugin_file ), '/' );
		$plugin_path  = wp_normalize_path( WP_PLUGIN_DIR . '/' . $plugin_file );
		$plugins_base = wp_normalize_path( trailingslashit( WP_PLUGIN_DIR ) );
		if ( strpos( $plugin_path, $plugins_base ) !== 0 || ! file_exists( $plugin_path ) ) {
			wp_die( esc_html__( 'The specified plugin does not exist.', 'wp-bizerbuilder' ) );
		}

		$plugin_data = get_plugin_data( $plugin_path );
		include WP_BIZERBUILDER_DIR . 'views/page-extend-hooks.php';
	}

	/**
	 * Display the extend theme page.
	 *
	 * @return void
	 */
	public function render_extend_theme_page() {
		$this->validate_theme( 'wp-bizerbuilder-extend-theme' );
		include WP_BIZERBUILDER_DIR . 'views/page-extend-theme.php';
	}

	/**
	 * Validate plugin access and existence.
	 *
	 * @param string $nonce_action Nonce action name.
	 * @return void
	 */
	protected function validate_plugin( $nonce_action ) {
		if ( ! current_user_can( 'manage_options' ) ) {
			wp_die( esc_html__( 'You do not have sufficient permissions to access this page.', 'wp-bizerbuilder' ) );
		}

		if ( ! isset( $_GET['plugin'] ) ) { // phpcs:ignore WordPress.Security.NonceVerification
			wp_die( esc_html__( 'No plugin specified.', 'wp-bizerbuilder' ) );
		}
		$nonce_value = isset( $_GET['nonce'] ) ? sanitize_text_field( wp_unslash( $_GET['nonce'] ) ) : '';
		if ( ! $nonce_value || ! wp_verify_nonce( $nonce_value, $nonce_action ) ) {
			wp_die( esc_html__( 'Security check failed.', 'wp-bizerbuilder' ) );
		}

		$plugin_file  = sanitize_text_field( wp_unslash( $_GET['plugin'] ) ); // phpcs:ignore WordPress.Security.NonceVerification
		$plugin_file  = ltrim( str_replace( [ '..\\', '../', '\\' ], '/', $plugin_file ), '/' );
		$plugin_path  = wp_normalize_path( WP_PLUGIN_DIR . '/' . $plugin_file );
		$plugins_base = wp_normalize_path( trailingslashit( WP_PLUGIN_DIR ) );
		if ( strpos( $plugin_path, $plugins_base ) !== 0 || ! file_exists( $plugin_path ) ) {
			wp_die( esc_html__( 'The specified plugin does not exist.', 'wp-bizerbuilder' ) );
		}
	}

	/**
	 * Validate theme access and existence.
	 *
	 * @param string $nonce_action Nonce action name.
	 * @return void
	 */
	protected function validate_theme( $nonce_action ) {
		if ( ! current_user_can( 'manage_options' ) ) {
			wp_die( esc_html__( 'You do not have sufficient permissions to access this page.', 'wp-bizerbuilder' ) );
		}

		if ( ! isset( $_GET['theme'] ) ) { // phpcs:ignore WordPress.Security.NonceVerification
			wp_die( esc_html__( 'No theme specified.', 'wp-bizerbuilder' ) );
		}
		$nonce_value = isset( $_GET['nonce'] ) ? sanitize_text_field( wp_unslash( $_GET['nonce'] ) ) : '';
		if ( ! $nonce_value || ! wp_verify_nonce( $nonce_value, $nonce_action ) ) {
			wp_die( esc_html__( 'Security check failed.', 'wp-bizerbuilder' ) );
		}

		$theme_slug = sanitize_text_field( wp_unslash( $_GET['theme'] ) ); // phpcs:ignore WordPress.Security.NonceVerification
		$theme      = wp_get_theme( $theme_slug );
		if ( ! $theme->exists() ) {
			wp_die( esc_html__( 'The specified theme does not exist.', 'wp-bizerbuilder' ) );
		}
	}
}
