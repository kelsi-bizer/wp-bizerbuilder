<?php
/**
 * WP-Bizerbuilder Admin Settings class.
 *
 * @package WP-Bizerbuilder
 */

namespace WP_Bizerbuilder\Admin;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Class that registers plugin settings in the admin.
 */
class Settings {

	/**
	 * Constructor hooks into 'admin_init'.
	 */
	public function __construct() {
		add_action( 'admin_init', [ $this, 'register_settings' ] );
	}

	/**
	 * Register the plugin settings fields.
	 *
	 * @return void
	 */
	public function register_settings() {
		register_setting( 'wp_bizerbuilder_settings', 'wp_bizerbuilder_openai_api_key' );
		register_setting( 'wp_bizerbuilder_settings', 'wp_bizerbuilder_anthropic_api_key' );
		register_setting( 'wp_bizerbuilder_settings', 'wp_bizerbuilder_google_api_key' );
		register_setting( 'wp_bizerbuilder_settings', 'wp_bizerbuilder_xai_api_key' );
		register_setting( 'wp_bizerbuilder_settings', 'wp_bizerbuilder_model' );
		register_setting( 'wp_bizerbuilder_settings', 'wp_bizerbuilder_planner_model' );
		register_setting( 'wp_bizerbuilder_settings', 'wp_bizerbuilder_coder_model' );
		register_setting( 'wp_bizerbuilder_settings', 'wp_bizerbuilder_reviewer_model' );
		register_setting( 'wp_bizerbuilder_settings', 'wp_bizerbuilder_plugin_mode' );
	}
}
