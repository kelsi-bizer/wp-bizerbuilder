<?php
/**
 * Admin view for the Create Extension page.
 *
 * @package WP-Bizerbuilder
 */

namespace WP_Bizerbuilder;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! isset( $_GET['nonce'] ) || ! wp_verify_nonce( sanitize_text_field( wp_unslash( $_GET['nonce'] ) ), 'wp-bizerbuilder-extend-hooks' ) ) {
	wp_die( esc_html__( 'Nonce verification failed. Please try again.', 'wp-bizerbuilder' ) );
}

$is_plugin_active = is_plugin_active( $plugin_file );

?>
<div class="wp-bizerbuilder-admin-container">
	<div class="wrap wp-bizerbuilder step-1-extend">
		<?php /* translators: %s: plugin name. */ ?>
		<h1><?php printf( esc_html__( 'Create Extension for: %s', 'wp-bizerbuilder' ), esc_html( $plugin_data['Name'] ) ); ?></h1>
		<!-- Loading message, visible by default -->
		<div id="hooks-loading" style="display: block;">
			<p><?php esc_html_e( 'Extracting plugin hooks, please wait...', 'wp-bizerbuilder' ); ?></p>
		</div>
		<!-- Hooks list and form, hidden by default -->
		<div id="hooks-content" style="display: none;">
			<details id="hooks-list">
				<summary id="hooks-summary"></summary>
				<ul id="hooks-ul"></ul>
				<p class="copy-hooks-description">
					<button type="button" id="copy-hooks" class="button button-small button-secondary">
						<?php esc_html_e( 'Copy Hooks', 'wp-bizerbuilder' ); ?>
					</button>
					<?php esc_html_e( 'Copy all hooks to clipboard, along with relevant context, to use with your LLM of choice.', 'wp-bizerbuilder' ); ?>
				</p>
			</details>
			<form method="post" action="" id="extend-hooks-form">
				<p><?php esc_html_e( 'Describe the extension you would like to create:', 'wp-bizerbuilder' ); ?></p>
				<textarea name="plugin_issue" id="plugin_issue" rows="10" cols="100"></textarea>
				<?php submit_button( esc_html__( 'Generate Extension Plan', 'wp-bizerbuilder' ), 'primary', 'generate_plan' ); ?>
				<input type="hidden" name="plugin_file" value="<?php echo esc_attr( $plugin_file ); ?>" id="plugin_file" />
			</form>
		</div>
		<div id="extend-hooks-message" class="bizerbuilder-message"></div>
	</div>
	<div class="wrap wp-bizerbuilder step-2-plan" style="display: none;">
		<h1><?php esc_html_e( 'Generated Extension Plan', 'wp-bizerbuilder' ); ?></h1>
		<form method="post" action="" id="extend-hooks-code-form">
			<p><?php esc_html_e( 'Review or edit the generated plan for the extension plugin:', 'wp-bizerbuilder' ); ?></p>
			<div id="plugin_plan_container"></div>
			<div class="bizerbuilder-actions">
				<button type="button" id="edit-issue" class="button"><?php esc_html_e( '« Edit Description', 'wp-bizerbuilder' ); ?></button>
				<?php submit_button( esc_html__( 'Generate Extension Code', 'wp-bizerbuilder' ), 'primary', 'extend_code' ); ?>
			</div>
		</form>
		<div id="extend-hooks-code-message" class="bizerbuilder-message"></div>
	</div>
	<div class="wrap wp-bizerbuilder step-3-done" style="display: none;">
		<?php /* translators: %s: theme/plugin name. */ ?>
		<h1><?php printf( esc_html__( 'Extension Plugin for: %s', 'wp-bizerbuilder' ), esc_html( $plugin_data['Name'] ) ); ?></h1>
		<form method="post" action="" id="extended-hooks-plugin-form">
			<p><?php esc_html_e( 'Review the generated code before saving:', 'wp-bizerbuilder' ); ?></p>

			<!-- Simple (single-file) mode -->
			<div id="simple-plugin-content">
				<textarea name="extended_plugin_code" id="extended_plugin_code" rows="20" cols="100"></textarea>
			</div>

			<!-- Complex (multi-file) mode -->
			<div id="complex-plugin-content" style="display: none;">
				<div class="generation-progress" style="display: none;">
					<div class="progress-bar-container">
						<div class="progress-bar" id="file-generation-progress"></div>
					</div>
					<span class="progress-text" id="progress-text"><?php esc_html_e( 'Generating files...', 'wp-bizerbuilder' ); ?></span>
				</div>

				<div class="generated-files-container" id="extended-files-container">
					<div class="files-tabs" id="files-tabs"></div>
					<div class="file-content" id="file-content"></div>
				</div>
			</div>

			<div class="bizerbuilder-code-warning">
				<strong><?php esc_html_e( 'Warning:', 'wp-bizerbuilder' ); ?></strong> <?php esc_html_e( 'AI-generated code may be unstable or insecure; use only after careful review and testing.', 'wp-bizerbuilder' ); ?>
			</div>

			<div class="bizerbuilder-actions">
				<button type="button" id="edit-plan" class="button"><?php esc_html_e( '« Edit Plan', 'wp-bizerbuilder' ); ?></button>
				<?php submit_button( esc_html__( 'Save Extension Plugin', 'wp-bizerbuilder' ), 'primary', 'extended_plugin' ); ?>
			</div>
		</form>
		<div id="extended-hooks-plugin-message" class="bizerbuilder-message"></div>
	</div>
	<?php $this->admin->output_admin_footer(); ?>
</div>
