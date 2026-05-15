<?php
/**
 * WP-Bizerbuilder Admin Scripts class.
 *
 * @package WP-Bizerbuilder
 */

namespace WP_Bizerbuilder\Admin;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Class that enqueues admin scripts and styles.
 */
class Scripts {

	/**
	 * Constructor hooks for scripts and inline CSS.
	 */
	public function __construct() {
		add_action( 'admin_enqueue_scripts', [ $this, 'enqueue_scripts' ] );
		add_action( 'admin_head', [ $this, 'admin_css' ] );
	}

	/**
	 * Get localized messages for JavaScript.
	 *
	 * @return array
	 */
	private function get_localized_messages() {
		return [
			'empty_description'              => esc_html__( 'Please enter a plugin description.', 'wp-bizerbuilder' ),
			'generating_plan'                => esc_html__( 'Generating a plan for your plugin.', 'wp-bizerbuilder' ),
			'plan_generation_error'          => esc_html__( 'Error generating the plugin plan.', 'wp-bizerbuilder' ),
			'generating_code'                => esc_html__( 'Generating code.', 'wp-bizerbuilder' ),
			'code_generation_error'          => esc_html__( 'Error generating the plugin code.', 'wp-bizerbuilder' ),
			'plugin_creation_error'          => esc_html__( 'Error creating the plugin.', 'wp-bizerbuilder' ),
			'creating_plugin'                => esc_html__( 'Installing the plugin.', 'wp-bizerbuilder' ),
			'plugin_created'                 => esc_html__( 'Plugin successfully installed.', 'wp-bizerbuilder' ),
			'how_to_test'                    => esc_html__( 'How to test it?', 'wp-bizerbuilder' ),
			'use_fixer'                      => esc_html__( 'If you notice any issues, use the Fix button in the Bizerbuilders list.', 'wp-bizerbuilder' ),
			'activate'                       => esc_html__( 'Activate Plugin', 'wp-bizerbuilder' ),
			'code_updated'                   => esc_html__( 'The plugin code has been updated.', 'wp-bizerbuilder' ),
			'generating_explanation'         => esc_html__( 'Generating explanation...', 'wp-bizerbuilder' ),
			'explanation_error'              => esc_html__( 'Error generating explanation.', 'wp-bizerbuilder' ),
			'security_focus'                 => esc_html__( 'Security Analysis', 'wp-bizerbuilder' ),
			'performance_focus'              => esc_html__( 'Performance Review', 'wp-bizerbuilder' ),
			'code_quality_focus'             => esc_html__( 'Code Quality Analysis', 'wp-bizerbuilder' ),
			'usage_focus'                    => esc_html__( 'Usage Instructions', 'wp-bizerbuilder' ),
			'general_explanation'            => esc_html__( 'General Explanation', 'wp-bizerbuilder' ),
			'copied'                         => esc_html__( 'Explanation copied to clipboard!', 'wp-bizerbuilder' ),
			'copy_failed'                    => esc_html__( 'Failed to copy explanation.', 'wp-bizerbuilder' ),
			'empty_changes_description'      => esc_html__( 'Please describe the changes you want to make to the plugin.', 'wp-bizerbuilder' ),
			'plan_generation_error_dev'      => esc_html__( 'Error generating the development plan.', 'wp-bizerbuilder' ),
			'generating_extended_code'       => esc_html__( 'Generating the extended plugin code.', 'wp-bizerbuilder' ),
			'code_generation_error_extended' => esc_html__( 'Error generating the extended code.', 'wp-bizerbuilder' ),
			'plugin_creation_error_extended' => esc_html__( 'Error creating the extended plugin.', 'wp-bizerbuilder' ),
			'creating_extended_plugin'       => esc_html__( 'Creating the extension plugin.', 'wp-bizerbuilder' ),
			'plugin_activation_error'        => esc_html__( 'Error activating the plugin.', 'wp-bizerbuilder' ),
			'extracting_hooks'               => esc_html__( 'Extracting hooks, please wait...', 'wp-bizerbuilder' ),
			'no_hooks_found'                 => esc_html__( 'No hooks found in the codebase. Cannot extend the plugin.', 'wp-bizerbuilder' ),
			'drop_files_to_attach'           => esc_html__( 'Drop files to attach', 'wp-bizerbuilder' ),
		];
	}

	/**
	 * Enqueue scripts/styles depending on the current admin page.
	 *
	 * @return void
	 */
	public function enqueue_scripts() {
		$screen = get_current_screen();

		// A small utility script, used on multiple pages.
		wp_register_script(
			'wp-bizerbuilder-utils',
			WP_BIZERBUILDER_URL . 'assets/admin/js/utils.js',
			[],
			WP_BIZERBUILDER_VERSION,
			true
		);

		// Common scripts.
		wp_enqueue_script(
			'wp-bizerbuilder-common',
			WP_BIZERBUILDER_URL . 'assets/admin/js/common.js',
			[ 'wp-bizerbuilder-utils' ],
			WP_BIZERBUILDER_VERSION,
			true
		);

		$localized_data = [
			'ajax_url'               => esc_url( admin_url( 'admin-ajax.php' ) ),
			'nonce'                  => wp_create_nonce( 'wp_bizerbuilder_generate' ),
			'messages'               => $this->get_localized_messages(),
			'supported_image_models' => \WP_Bizerbuilder\AI_Utils::get_supported_image_models(),
		];

		// The main list page.
		if ( $screen->id === 'toplevel_page_wp-bizerbuilder' ) {
			wp_enqueue_script(
				'wp-bizerbuilder',
				WP_BIZERBUILDER_URL . 'assets/admin/js/list-plugins.js',
				[],
				WP_BIZERBUILDER_VERSION,
				true
			);
			wp_enqueue_style(
				'wp-bizerbuilder',
				WP_BIZERBUILDER_URL . 'assets/admin/css/list-plugins.css',
				[],
				WP_BIZERBUILDER_VERSION
			);
		} elseif ( $screen->id === 'wp-bizerbuilder_page_wp-bizerbuilder-generate' ) {
			// Code editor (CodeMirror) for displaying plugin code.
			$settings = wp_enqueue_code_editor( [ 'type' => 'application/x-httpd-php' ] );
			if ( false !== $settings ) {
				wp_enqueue_script( 'wp-theme-plugin-editor' );
				wp_enqueue_style( 'wp-codemirror' );
			}

			wp_enqueue_script(
				'wp-bizerbuilder-generator',
				WP_BIZERBUILDER_URL . 'assets/admin/js/generator.js',
				[ 'wp-bizerbuilder-utils' ],
				WP_BIZERBUILDER_VERSION,
				true
			);

			$localized_data['fix_url']         = esc_url( admin_url( 'admin.php?page=wp-bizerbuilder-fix&nonce=' . wp_create_nonce( 'wp-bizerbuilder-fix-plugin' ) ) );
			$localized_data['activate_url']    = esc_url( admin_url( 'admin.php?page=wp-bizerbuilder&action=activate&nonce=' . wp_create_nonce( 'wp-bizerbuilder-activate-plugin' ) ) );
			$localized_data['testing_plan']    = '';
			$localized_data['plugin_examples'] = [
				esc_html__( 'A simple contact form with honeypot spam protection.', 'wp-bizerbuilder' ),
				esc_html__( 'A custom post type for testimonials.', 'wp-bizerbuilder' ),
				esc_html__( 'A widget that displays recent posts.', 'wp-bizerbuilder' ),
				esc_html__( 'A simple image compression tool.', 'wp-bizerbuilder' ),
			];

			wp_enqueue_style(
				'wp-bizerbuilder-generator',
				WP_BIZERBUILDER_URL . 'assets/admin/css/generator.css',
				[],
				WP_BIZERBUILDER_VERSION
			);
		} elseif ( $screen->id === 'admin_page_wp-bizerbuilder-fix' ) {
			// Code editor for Fix page.
			$settings = wp_enqueue_code_editor( [ 'type' => 'application/x-httpd-php' ] );
			if ( false !== $settings ) {
				wp_enqueue_script( 'wp-theme-plugin-editor' );
				wp_enqueue_style( 'wp-codemirror' );
			}

			$is_plugin_active = false;
			if ( isset( $_GET['plugin'] ) ) { // phpcs:ignore WordPress.Security.NonceVerification -- Nonce verification is not needed here.
				$plugin_file      = sanitize_text_field( wp_unslash( $_GET['plugin'] ) ); // phpcs:ignore WordPress.Security.NonceVerification -- Nonce verification is not needed here.
				$plugin_file      = str_replace( '../', '', $plugin_file );
				$is_plugin_active = is_plugin_active( $plugin_file );
			}

			wp_enqueue_script(
				'wp-bizerbuilder-fix',
				WP_BIZERBUILDER_URL . 'assets/admin/js/fixer.js',
				[ 'wp-bizerbuilder-utils' ],
				WP_BIZERBUILDER_VERSION,
				true
			);

			$localized_data['activate_url']     = esc_url( admin_url( 'admin.php?page=wp-bizerbuilder&action=activate&nonce=' . wp_create_nonce( 'wp-bizerbuilder-activate-plugin' ) ) );
			$localized_data['is_plugin_active'] = $is_plugin_active;

			wp_enqueue_style(
				'wp-bizerbuilder-fix',
				WP_BIZERBUILDER_URL . 'assets/admin/css/fixer.css',
				[],
				WP_BIZERBUILDER_VERSION
			);

			// Reuse generator styles for multi-file editor UI
			wp_enqueue_style(
				'wp-bizerbuilder-generator-shared',
				WP_BIZERBUILDER_URL . 'assets/admin/css/generator.css',
				[],
				WP_BIZERBUILDER_VERSION
			);
		} elseif ( $screen->id === 'admin_page_wp-bizerbuilder-extend' ) {
			// Code editor for Extend page.
			$settings = wp_enqueue_code_editor( [ 'type' => 'application/x-httpd-php' ] );
			if ( false !== $settings ) {
				wp_enqueue_script( 'wp-theme-plugin-editor' );
				wp_enqueue_style( 'wp-codemirror' );
			}

			$is_plugin_active = false;
			if ( isset( $_GET['plugin'] ) ) { // phpcs:ignore WordPress.Security.NonceVerification -- Nonce verification is not needed here.
				$plugin_file      = sanitize_text_field( wp_unslash( $_GET['plugin'] ) ); // phpcs:ignore WordPress.Security.NonceVerification -- Nonce verification is not needed here.
				$plugin_file      = str_replace( '../', '', $plugin_file );
				$is_plugin_active = is_plugin_active( $plugin_file );
			}

			wp_enqueue_script(
				'wp-bizerbuilder-extend',
				WP_BIZERBUILDER_URL . 'assets/admin/js/extender.js',
				[ 'wp-bizerbuilder-utils' ],
				WP_BIZERBUILDER_VERSION,
				true
			);

			$localized_data['activate_url']     = esc_url( admin_url( 'admin.php?page=wp-bizerbuilder&action=activate&nonce=' . wp_create_nonce( 'wp-bizerbuilder-activate-plugin' ) ) );
			$localized_data['is_plugin_active'] = $is_plugin_active;

			wp_enqueue_style(
				'wp-bizerbuilder-extend',
				WP_BIZERBUILDER_URL . 'assets/admin/css/extender.css',
				[],
				WP_BIZERBUILDER_VERSION
			);

			// Reuse generator styles for multi-file editor UI
			wp_enqueue_style(
				'wp-bizerbuilder-generator-shared',
				WP_BIZERBUILDER_URL . 'assets/admin/css/generator.css',
				[],
				WP_BIZERBUILDER_VERSION
			);
		} elseif ( $screen->id === 'admin_page_wp-bizerbuilder-explain' ) {
			// Enqueue marked.js, purify.min.js for markdown rendering.
			wp_enqueue_script(
				'wp-bizerbuilder-marked',
				WP_BIZERBUILDER_URL . 'assets/admin/js/marked.min.js',
				[],
				WP_BIZERBUILDER_VERSION,
				true
			);
			wp_enqueue_script(
				'wp-bizerbuilder-purify',
				WP_BIZERBUILDER_URL . 'assets/admin/js/purify.min.js',
				[],
				WP_BIZERBUILDER_VERSION,
				true
			);

			// Enqueue scripts and styles for the Explain Plugin page.
			wp_enqueue_script(
				'wp-bizerbuilder-explainer',
				WP_BIZERBUILDER_URL . 'assets/admin/js/explainer.js',
				[ 'wp-bizerbuilder-utils' ],
				WP_BIZERBUILDER_VERSION,
				true
			);

			wp_enqueue_style(
				'wp-bizerbuilder-explainer',
				WP_BIZERBUILDER_URL . 'assets/admin/css/explainer.css',
				[],
				WP_BIZERBUILDER_VERSION
			);
		} elseif ( $screen->id === 'admin_page_wp-bizerbuilder-extend-hooks' ) {
			$settings = wp_enqueue_code_editor( [ 'type' => 'application/x-httpd-php' ] );
			if ( false !== $settings ) {
				wp_enqueue_script( 'wp-theme-plugin-editor' );
				wp_enqueue_style( 'wp-codemirror' );
			}

			$is_plugin_active = false;
			if ( isset( $_GET['plugin'] ) ) { // phpcs:ignore WordPress.Security.NonceVerification -- Nonce verification is not needed here.
				$plugin_file      = sanitize_text_field( wp_unslash( $_GET['plugin'] ) ); // phpcs:ignore WordPress.Security.NonceVerification -- Nonce verification is not needed here.
				$plugin_file      = str_replace( '../', '', $plugin_file );
				$is_plugin_active = is_plugin_active( $plugin_file );
			}

			wp_enqueue_script(
				'wp-bizerbuilder-extend-hooks',
				WP_BIZERBUILDER_URL . 'assets/admin/js/hooks-extender.js',
				[ 'wp-bizerbuilder-utils' ],
				WP_BIZERBUILDER_VERSION,
				true
			);

			$localized_data['activate_url']     = esc_url( admin_url( 'admin.php?page=wp-bizerbuilder&action=activate&nonce=' . wp_create_nonce( 'wp-bizerbuilder-activate-plugin' ) ) );
			$localized_data['is_plugin_active'] = $is_plugin_active;

			wp_enqueue_style(
				'wp-bizerbuilder-extend-hooks',
				WP_BIZERBUILDER_URL . 'assets/admin/css/extender.css',
				[],
				WP_BIZERBUILDER_VERSION
			);

			// Reuse generator styles so Project Structure table matches other flows
			wp_enqueue_style(
				'wp-bizerbuilder-generator-shared',
				WP_BIZERBUILDER_URL . 'assets/admin/css/generator.css',
				[],
				WP_BIZERBUILDER_VERSION
			);
		} elseif ( $screen->id === 'admin_page_wp-bizerbuilder-extend-theme' ) {
			$settings = wp_enqueue_code_editor( [ 'type' => 'application/x-httpd-php' ] );
			if ( false !== $settings ) {
				wp_enqueue_script( 'wp-theme-plugin-editor' );
				wp_enqueue_style( 'wp-codemirror' );
			}

			wp_enqueue_script(
				'wp-bizerbuilder-extend-theme',
				WP_BIZERBUILDER_URL . 'assets/admin/js/theme-extender.js',
				[ 'wp-bizerbuilder-utils' ],
				WP_BIZERBUILDER_VERSION,
				true
			);

			$localized_data['activate_url'] = esc_url( admin_url( 'admin.php?page=wp-bizerbuilder&action=activate&nonce=' . wp_create_nonce( 'wp-bizerbuilder-activate-plugin' ) ) );

			wp_enqueue_style(
				'wp-bizerbuilder-extend-theme',
				WP_BIZERBUILDER_URL . 'assets/admin/css/extender.css',
				[],
				WP_BIZERBUILDER_VERSION
			);

			// Reuse generator styles for multi-file editor UI
			wp_enqueue_style(
				'wp-bizerbuilder-generator-shared',
				WP_BIZERBUILDER_URL . 'assets/admin/css/generator.css',
				[],
				WP_BIZERBUILDER_VERSION
			);
		}

		// Footer script with localized data.
		wp_enqueue_script(
			'wp-bizerbuilder-footer',
			WP_BIZERBUILDER_URL . 'assets/admin/js/footer.js',
			[ 'jquery' ],
			WP_BIZERBUILDER_VERSION,
			true
		);

		$api_handler = new \WP_Bizerbuilder\Admin\API_Handler();

		wp_localize_script(
			'wp-bizerbuilder-common',
			'wp_bizerbuilder',
			$localized_data
		);

		$default_step = 'default';

		// Set default step based on page context.
		if ( $screen ) {
			switch ( $screen->id ) {
				case 'wp-bizerbuilder_page_wp-bizerbuilder-generate':
					$default_step = 'generatePlan';
					break;
				case 'admin_page_wp-bizerbuilder-fix':
					$default_step = 'generatePlan';
					break;
				case 'admin_page_wp-bizerbuilder-extend':
					$default_step = 'generatePlan';
					break;
				case 'admin_page_wp-bizerbuilder-extend-hooks':
					$default_step = 'generatePlan';
					break;
				case 'admin_page_wp-bizerbuilder-extend-theme':
					$default_step = 'generatePlan';
					break;
				case 'admin_page_wp-bizerbuilder-explain':
					$default_step = 'askQuestion';
					break;
			}
		}

		wp_localize_script(
			'wp-bizerbuilder-footer',
			'wpBizerbuilderFooter',
			[
				'nonce'                    => wp_create_nonce( 'wp_bizerbuilder_nonce' ),
				'models'                   => [
					'default'  => get_option( 'wp_bizerbuilder_model' ),
					'planner'  => $api_handler->get_planner_model(),
					'coder'    => $api_handler->get_coder_model(),
					'reviewer' => $api_handler->get_reviewer_model(),
				],
				'default_step'             => $default_step,
				'no_token_data'            => esc_html__( 'No token usage data available yet.', 'wp-bizerbuilder' ),
				'total_usage'              => esc_html__( 'Total Usage', 'wp-bizerbuilder' ),
				'step_breakdown'           => esc_html__( 'Step Breakdown', 'wp-bizerbuilder' ),
				'error_saving_models'      => esc_html__( 'Failed to save models.', 'wp-bizerbuilder' ),
				'error_saving_models_ajax' => esc_html__( 'An error occurred while saving models.', 'wp-bizerbuilder' ),
			]
		);
	}

	/**
	 * Add inline CSS to fix the menu icon in the admin.
	 *
	 * @return void
	 */
	public function admin_css() {
		?>
		<style>
			li.toplevel_page_wp-bizerbuilder .wp-menu-image::after {
				content: "";
				display: block;
				width: 20px;
				height: 20px;
				border: 2px solid;
				border-radius: 100px;
				position: absolute;
				top: 5px;
				left: 6px;
			}
			li.toplevel_page_wp-bizerbuilder:not(.wp-menu-open) a:not(:hover) .wp-menu-image::after {
				color: #a7aaad;
				color: rgba(240, 246, 252, 0.6);
			}
		</style>
		<?php
	}
}
