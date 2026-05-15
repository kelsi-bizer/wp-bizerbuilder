<?php
/**
 * WP-Bizerbuilder Updater class.
 *
 * @package WP-Bizerbuilder
 */

namespace WP_Bizerbuilder\Admin;

use WP_Bizerbuilder\GitHub_Updater;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Class that handles the GitHub updater.
 */
class Updater {

	/**
	 * Constructor.
	 */
	public function __construct() {
		add_action( 'init', [ $this, 'github_updater_init' ] );
	}

	/**
	 * Initialize the GitHub updater.
	 *
	 * @return void
	 */
	public function github_updater_init() {
		if ( ! is_admin() ) {
			return;
		}

		$config = [
			'slug'               => plugin_basename( WP_BIZERBUILDER_DIR . 'wp-bizerbuilder.php' ),
			'proper_folder_name' => dirname( plugin_basename( WP_BIZERBUILDER_DIR . 'wp-bizerbuilder.php' ) ),
			'api_url'            => 'https://api.github.com/repos/kelsi-bizer/wp-bizerbuilder',
			'raw_url'            => 'https://raw.githubusercontent.com/kelsi-bizer/wp-bizerbuilder/main/',
			'github_url'         => 'https://github.com/kelsi-bizer/wp-bizerbuilder',
			'zip_url'            => 'https://github.com/kelsi-bizer/wp-bizerbuilder/archive/refs/heads/main.zip',
			'requires'           => '6.0',
			'tested'             => '6.6.2',
			'description'        => esc_html__( 'A plugin that generates other plugins on-demand using AI.', 'wp-bizerbuilder' ),
			'homepage'           => 'https://github.com/kelsi-bizer/wp-bizerbuilder',
			'version'            => WP_BIZERBUILDER_VERSION,
		];

		// Instantiate the updater class.
		new GitHub_Updater( $config );
	}
}
