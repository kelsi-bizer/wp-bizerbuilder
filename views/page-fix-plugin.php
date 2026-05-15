<?php
/**
 * Admin view for the Fix Bizerbuilder page.
 *
 * @package WP-Bizerbuilder
 * @since 1.0.0
 * @version 1.0.5
 * @link https://wp-bizerbuilder.com
 * @license GPL-2.0+
 * @license https://www.gnu.org/licenses/gpl-2.0.html
 */

namespace WP_Bizerbuilder;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! isset( $_GET['nonce'] ) || ! wp_verify_nonce( sanitize_text_field( wp_unslash( $_GET['nonce'] ) ), 'wp-bizerbuilder-fix-plugin' ) ) {
	wp_die( esc_html__( 'Nonce verification failed. Please try again.', 'wp-bizerbuilder' ) );
}

$plugin_file      = '';
$is_plugin_active = false;
if ( isset( $_GET['plugin'] ) ) {
	$plugin_file      = sanitize_text_field( wp_unslash( $_GET['plugin'] ) );
	$plugin_file      = str_replace( '../', '', $plugin_file );
	$plugin_path      = WP_CONTENT_DIR . '/plugins/' . $plugin_file;
	$plugin_data      = get_plugin_data( $plugin_path );
	$is_plugin_active = is_plugin_active( $plugin_file );
} else {
	$plugin_data = [ 'Name' => __( 'Unknown Plugin', 'wp-bizerbuilder' ) ];
}

$value = '';
if ( isset( $_GET['error_message'] ) ) {
	$value = sprintf(
		// translators: %s: error message.
		esc_html__( 'Error while activating the plugin: %s', 'wp-bizerbuilder' ),
		esc_html( sanitize_text_field( wp_unslash( $_GET['error_message'] ) ) )
	);
}

?>
<div class="wp-bizerbuilder-admin-container">
	<div class="wrap wp-bizerbuilder step-1-fix">
		<?php /* translators: %s: plugin name. */ ?>
		<h1><?php printf( esc_html__( 'Fix This Plugin: %s', 'wp-bizerbuilder' ), esc_html( $plugin_data['Name'] ) ); ?></h1>
		<form method="post" action="" id="fix-plugin-form">
			<?php wp_nonce_field( 'fix_plugin', 'fix_plugin_nonce' ); ?>
			<p><?php esc_html_e( 'Describe the issue you are experiencing with the plugin. Include as much detail as possible and any error messages you are seeing:', 'wp-bizerbuilder' ); ?></p>
			<textarea name="plugin_issue" id="plugin_issue" rows="10" cols="100"><?php echo esc_textarea( $value ); ?></textarea>
			<?php submit_button( esc_html__( 'Fix Plugin', 'wp-bizerbuilder' ), 'primary', 'fix_plugin' ); ?>
			<input type="hidden" name="plugin_file" value="<?php echo esc_attr( $plugin_file ); ?>" id="plugin_file" />
		</form>
		<div id="fix-plugin-message" class="bizerbuilder-message"></div>
	</div>
	<div class="wrap wp-bizerbuilder step-2-plan" style="display: none;">
		<h1><?php esc_html_e( 'Generated Plan', 'wp-bizerbuilder' ); ?></h1>
		<form method="post" action="" id="fix-code-form">
			<?php wp_nonce_field( 'fix_code', 'fix_code_nonce' ); ?>
			<p><?php esc_html_e( 'Review or edit the generated plan:', 'wp-bizerbuilder' ); ?></p>
			<div id="plugin_plan_container"></div>
			<div class="bizerbuilder-actions">
				<button type="button" id="edit-issue" class="button"><?php esc_html_e( '&laquo; Edit Issue', 'wp-bizerbuilder' ); ?></button>
				<?php submit_button( esc_html__( 'Generate Plugin Code', 'wp-bizerbuilder' ), 'primary', 'fix_code' ); ?>
			</div>
		</form>
		<div id="fix-code-message" class="bizerbuilder-message"></div>
	</div>
	<div class="wrap wp-bizerbuilder step-3-done" style="display: none;">
		<?php /* translators: %s: plugin name. */ ?>
		<h1><?php printf( esc_html__( 'Fixed Plugin: %s', 'wp-bizerbuilder' ), esc_html( $plugin_data['Name'] ) ); ?></h1>
		<form method="post" action="" id="fixed-plugin-form">
			<?php wp_nonce_field( 'fixed_plugin', 'fixed_plugin_nonce' ); ?>
			<p><?php esc_html_e( 'The plugin has been fixed successfully. You can review the changes before activating it:', 'wp-bizerbuilder' ); ?></p>

			<!-- Generation progress (complex flow) -->
			<div class="generation-progress" style="display: none;">
				<div class="progress-bar-container">
					<div class="progress-bar" id="file-generation-progress"></div>
				</div>
				<span class="progress-text" id="progress-text"><?php esc_html_e( 'Generating files...', 'wp-bizerbuilder' ); ?></span>
			</div>

			<!-- Multi-file editor UI -->
			<div class="generated-files-container" id="fixed-files-container">
				<div class="files-tabs" id="files-tabs">
					<!-- Tabs populated by JS -->
				</div>
				<div class="file-content" id="file-content">
					<!-- Editors populated by JS -->
				</div>
			</div>

			<!-- Fallback textarea kept (hidden) for backward compatibility -->
			<textarea name="fixed_plugin_code" id="fixed_plugin_code" rows="20" cols="100" style="display:none"></textarea>
			
			<?php if ( $is_plugin_active ) : ?>
				<div class="bizerbuilder-code-warning">
					<strong><?php esc_html_e( 'Warning:', 'wp-bizerbuilder' ); ?></strong> <?php esc_html_e( 'This plugin is active, changes will take effect immediately.', 'wp-bizerbuilder' ); ?>
				</div>
			<?php endif; ?>
			
			<div class="bizerbuilder-actions">
				<button type="button" id="edit-plan" class="button"><?php esc_html_e( '&laquo; Edit Plan', 'wp-bizerbuilder' ); ?></button>
				<?php submit_button( esc_html__( 'Save Changes', 'wp-bizerbuilder' ), 'primary', 'fixed_plugin' ); ?>
			</div>
		</form>
		<div id="fixed-plugin-message" class="bizerbuilder-message"></div>
	</div>
	<?php $this->admin->output_admin_footer(); ?>
</div>
