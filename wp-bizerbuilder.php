<?php
/**
 * Plugin Name: WP-Bizerbuilder
 * Description: A plugin that generates other plugins on-demand using AI.
 * Version: 1.7.1
 * Author: WP-Bizerbuilder
 * Text Domain: wp-bizerbuilder
 * Domain Path: /languages
 *
 * @package WP-Bizerbuilder
 * @since 1.0.0
 * @version 1.7.1
 * @license GPL-2.0+
 * @license https://www.gnu.org/licenses/gpl-2.0.html
 *
 * @wordpress-plugin
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

// Define constants.
define( 'WP_BIZERBUILDER_VERSION', '1.7.1' );
define( 'WP_BIZERBUILDER_DIR', plugin_dir_path( __FILE__ ) );
define( 'WP_BIZERBUILDER_URL', plugin_dir_url( __FILE__ ) );

// Include the autoloader.
require_once WP_BIZERBUILDER_DIR . 'vendor/autoload.php';

/**
 * Initialize the plugin.
 *
 * @return void
 */
function wp_bizerbuilder_init() {
	load_plugin_textdomain( 'wp-bizerbuilder', false, dirname( plugin_basename( __FILE__ ) ) . '/languages' );
	$admin_pages = new \WP_Bizerbuilder\Admin\Admin();
}
add_action( 'plugins_loaded', 'wp_bizerbuilder_init' );
