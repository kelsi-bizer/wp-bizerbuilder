<?php
/**
 * Admin view for the Settings page.
 *
 * @package WP-Bizerbuilder
 * @since 1.0.0
 * @version 1.0.5
 * @link https://wp-bizerbuilder.com
 * @license GPL-2.0+
 * @license https://www.gnu.org/licenses/gpl-2.0.html
 */

namespace WP_Bizerbuilder;

use WP_Bizerbuilder\Admin\Admin;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

?>
<div class="wrap">
	<h1><?php esc_html_e( 'WP-Bizerbuilder Settings', 'wp-bizerbuilder' ); ?></h1>
	<?php settings_errors(); ?>
	<form method="post" action="options.php">
		<?php
		settings_fields( 'wp_bizerbuilder_settings' );
		do_settings_sections( 'wp_bizerbuilder_settings' );
		?>
		<table class="form-table">
			<tr valign="top">
				<th scope="row"><?php esc_html_e( 'OpenAI API Key', 'wp-bizerbuilder' ); ?></th>
				<td><input type="password" name="wp_bizerbuilder_openai_api_key" value="<?php echo esc_attr( get_option( 'wp_bizerbuilder_openai_api_key' ) ); ?>" class="large-text" /></td>
			</tr>
			<tr valign="top">
				<th scope="row"><?php esc_html_e( 'Anthropic API Key', 'wp-bizerbuilder' ); ?></th>
				<td><input type="password" name="wp_bizerbuilder_anthropic_api_key" value="<?php echo esc_attr( get_option( 'wp_bizerbuilder_anthropic_api_key' ) ); ?>" class="large-text" /></td>
			</tr>
			<tr valign="top">
				<th scope="row"><?php esc_html_e( 'Google Gemini API Key', 'wp-bizerbuilder' ); ?></th>
				<td><input type="password" name="wp_bizerbuilder_google_api_key" value="<?php echo esc_attr( get_option( 'wp_bizerbuilder_google_api_key' ) ); ?>" class="large-text" /></td>
			</tr>
			<tr valign="top">
				<th scope="row"><?php esc_html_e( 'xAI API Key', 'wp-bizerbuilder' ); ?></th>
				<td><input type="password" name="wp_bizerbuilder_xai_api_key" value="<?php echo esc_attr( get_option( 'wp_bizerbuilder_xai_api_key' ) ); ?>" class="large-text" /></td>
			</tr>
<?php
function render_model_dropdown( $name, $selected_value ) {
	$models        = Admin::get_models();
	$custom_models = get_option( 'wp_bizerbuilder_custom_models', [] );

	echo '<select name="' . esc_attr( $name ) . '" id="' . esc_attr( $name ) . '">';
	echo '<option value="" ' . selected( $selected_value, '', false ) . '>' . esc_html__( 'Use Default Model', 'wp-bizerbuilder' ) . '</option>';

	foreach ( $models as $provider => $model ) {
		echo '<optgroup label="' . esc_attr( $provider ) . '">';
		foreach ( $model as $key => $value ) {
			echo '<option value="' . esc_attr( $key ) . '" ' . selected( $selected_value, $key, false ) . '>' . esc_html( $value ) . '</option>';
		}
		echo '</optgroup>';
	}

	if ( ! empty( $custom_models ) ) {
		echo '<optgroup label="' . esc_attr__( 'Custom Models', 'wp-bizerbuilder' ) . '">';
		foreach ( $custom_models as $model ) {
			echo '<option value="' . esc_attr( $model['name'] ) . '" ' . selected( $selected_value, $model['name'], false ) . '>' . esc_html( $model['name'] ) . '</option>';
		}
		echo '</optgroup>';
	}

	echo '</select>';
}
?>
			<tr valign="top">
				<th scope="row"><?php esc_html_e( 'Default Model', 'wp-bizerbuilder' ); ?></th>
				<td>
					<select name="wp_bizerbuilder_model" id="wp_bizerbuilder_model">
						<?php
						$models = Admin::get_models();
						foreach ( $models as $provider => $model ) {
							echo '<optgroup label="' . esc_attr( $provider ) . '">';
							foreach ( $model as $key => $value ) {
								echo '<option value="' . esc_attr( $key ) . '" ' . selected( get_option( 'wp_bizerbuilder_model' ), $key ) . '>' . esc_html( $value ) . '</option>';
							}
							echo '</optgroup>';
						}
						?>
						<optgroup label="<?php esc_attr_e( 'Custom Models', 'wp-bizerbuilder' ); ?>" id="custom-models">
							<?php
							$custom_models = get_option( 'wp_bizerbuilder_custom_models', [] );
							foreach ( $custom_models as $model ) {
								echo '<option value="' . esc_attr( $model['name'] ) . '" ' . selected( get_option( 'wp_bizerbuilder_model' ), $model['name'] ) . '>' . esc_html( $model['name'] ) . '</option>';
							}
							?>
						</optgroup>
					</select>
					<p>
						<button type="button" id="toggle-specialized-models" class="button-link"><?php esc_html_e( 'Show specialized model settings', 'wp-bizerbuilder' ); ?> <span class="dashicons dashicons-arrow-down-alt2"></span></button>
					</p>
				</td>
			</tr>
		</table>
		
		<?php
		// Check if any specialized model is set.
		$planner_model          = get_option( 'wp_bizerbuilder_planner_model' );
		$coder_model            = get_option( 'wp_bizerbuilder_coder_model' );
		$reviewer_model         = get_option( 'wp_bizerbuilder_reviewer_model' );
		$has_specialized_models = ! empty( $planner_model ) || ! empty( $coder_model ) || ! empty( $reviewer_model );
		?>
		<div class="wp-bizerbuilder-per-step-models" style="<?php echo $has_specialized_models ? '' : 'display: none;'; ?>">
			<table class="form-table">
				<tr valign="top">
					<th scope="row"><?php esc_html_e( 'Planner Model', 'wp-bizerbuilder' ); ?></th>
					<td>
						<?php render_model_dropdown( 'wp_bizerbuilder_planner_model', get_option( 'wp_bizerbuilder_planner_model' ) ); ?>
						<p class="description"><?php esc_html_e( 'Used for planning plugin extensions and analyzing hooks. Falls back to Default Model if not set.', 'wp-bizerbuilder' ); ?></p>
					</td>
				</tr>
				<tr valign="top">
					<th scope="row"><?php esc_html_e( 'Coder Model', 'wp-bizerbuilder' ); ?></th>
					<td>
						<?php render_model_dropdown( 'wp_bizerbuilder_coder_model', get_option( 'wp_bizerbuilder_coder_model' ) ); ?>
						<p class="description"><?php esc_html_e( 'Used for generating and fixing code. Falls back to Default Model if not set.', 'wp-bizerbuilder' ); ?></p>
					</td>
				</tr>
				<tr valign="top">
					<th scope="row"><?php esc_html_e( 'Reviewer Model', 'wp-bizerbuilder' ); ?></th>
					<td>
						<?php render_model_dropdown( 'wp_bizerbuilder_reviewer_model', get_option( 'wp_bizerbuilder_reviewer_model' ) ); ?>
						<p class="description"><?php esc_html_e( 'Used for explaining code and reviewing generated plugins. Falls back to Default Model if not set.', 'wp-bizerbuilder' ); ?></p>
					</td>
				</tr>
			</table>
		</div>
		<table class="form-table">
			<tr valign="top">
				<th scope="row"><?php esc_html_e( 'Generate Plugins', 'wp-bizerbuilder' ); ?></th>
				<td>
					<select name="wp_bizerbuilder_plugin_mode" id="wp_bizerbuilder_plugin_mode">
						<?php $current_mode = get_option( 'wp_bizerbuilder_plugin_mode', 'simple' ); ?>
						<option value="simple" <?php selected( $current_mode, 'simple' ); ?>><?php esc_html_e( 'Simple plugin mode (single-file plugins)', 'wp-bizerbuilder' ); ?></option>
						<option value="complex" <?php selected( $current_mode, 'complex' ); ?>><?php esc_html_e( 'Complex plugin mode (multi-file plugins)', 'wp-bizerbuilder' ); ?></option>
					</select>
					<p class="description">
						<?php esc_html_e( 'Complex mode uses more tokens. For best results, use capable models.', 'wp-bizerbuilder' ); ?>
					</p>
				</td>
			</tr>
			<tr valign="top">
				<th scope="row"><?php esc_html_e( 'Custom Models', 'wp-bizerbuilder' ); ?></th>
				<td>
					<div id="custom-models-list">
						<!-- List will be populated via JS -->
						<div class="custom-models-items"></div>
					</div>

					<div id="add-custom-model-form">
						<input type="text" id="custom-model-name" placeholder="<?php esc_attr_e( 'Model Name (User-defined Label)', 'wp-bizerbuilder' ); ?>" class="large-text">
						<input type="url" id="custom-model-url" placeholder="<?php esc_attr_e( 'API Endpoint URL', 'wp-bizerbuilder' ); ?>" class="large-text">
						<input type="text" id="custom-model-parameter" placeholder="<?php esc_attr_e( '"model" Parameter Value', 'wp-bizerbuilder' ); ?>" class="large-text">
						<input type="password" id="custom-model-api-key" placeholder="<?php esc_attr_e( 'API Key', 'wp-bizerbuilder' ); ?>" class="large-text">
						<textarea id="custom-model-headers" placeholder="<?php esc_attr_e( 'Additional Headers (one per line, name=value)', 'wp-bizerbuilder' ); ?>" rows="4" class="large-text"></textarea>
						<button type="button" id="add-custom-model" class="button"><?php esc_html_e( 'Add Custom Model', 'wp-bizerbuilder' ); ?></button>
					</div>

					<input type="hidden" name="wp_bizerbuilder_custom_models" id="wp_bizerbuilder_custom_models" value="<?php echo esc_attr( wp_json_encode( get_option( 'wp_bizerbuilder_custom_models', [] ) ) ); ?>">
					<input type="hidden" id="wp_bizerbuilder_settings_nonce" value="<?php echo esc_attr( wp_create_nonce( 'wp_bizerbuilder_nonce' ) ); ?>">

					<p class="description"><?php esc_html_e( 'Add any custom models you want to use with WP-Bizerbuilder. These models will be available in the model selection dropdown. The API must be compatible with the OpenAI API.', 'wp-bizerbuilder' ); ?></p>
				</td>
			</tr>
		</table>
		<?php submit_button(); ?>
	</form>
</div>
<script>
	jQuery(document).ready(function($) {
		let customModels = JSON.parse($('#wp_bizerbuilder_custom_models').val() || '[]');
		const nonce = $('#wp_bizerbuilder_settings_nonce').val();
		
		// Toggle specialized models section
		$('#toggle-specialized-models').on('click', function() {
			const $section = $('.wp-bizerbuilder-per-step-models');
			const $button = $(this);
			const $icon = $button.find('.dashicons');
			
			if ($section.is(':visible')) {
				$section.hide();
				$button.html('<?php esc_html_e( 'Show specialized model settings', 'wp-bizerbuilder' ); ?> <span class="dashicons dashicons-arrow-down-alt2"></span>');
			} else {
				$section.show();
				$button.html('<?php esc_html_e( 'Hide specialized model settings', 'wp-bizerbuilder' ); ?> <span class="dashicons dashicons-arrow-up-alt2"></span>');
			}
		});
		
		// Update toggle button text based on initial visibility
		if ($('.wp-bizerbuilder-per-step-models').is(':visible')) {
			$('#toggle-specialized-models').html('<?php esc_html_e( 'Hide specialized model settings', 'wp-bizerbuilder' ); ?> <span class="dashicons dashicons-arrow-up-alt2"></span>');
		}
		
		// Later this may be moved to a wp_localize_script call
		const wp_bizerbuilder_i18n = {
			details: '<?php echo esc_js( __( 'Details', 'wp-bizerbuilder' ) ); ?>',
			url: '<?php echo esc_js( __( 'URL', 'wp-bizerbuilder' ) ); ?>',
			modelParameter: '<?php echo esc_js( __( 'Model Parameter', 'wp-bizerbuilder' ) ); ?>',
			apiKey: '<?php echo esc_js( __( 'API Key', 'wp-bizerbuilder' ) ); ?>',
			headers: '<?php echo esc_js( __( 'Headers', 'wp-bizerbuilder' ) ); ?>',
			remove: '<?php echo esc_js( __( 'Remove', 'wp-bizerbuilder' ) ); ?>',
			fillOutFields: '<?php echo esc_js( __( 'Please fill out all required fields.', 'wp-bizerbuilder' ) ); ?>',
			removeModel: '<?php echo esc_js( __( 'Are you sure you want to remove this model?', 'wp-bizerbuilder' ) ); ?>',
			errorSavingModel: '<?php echo esc_js( __( 'Error saving model', 'wp-bizerbuilder' ) ); ?>',
		};

		function updateCustomModelsList() {
			const selectedDefault = $('#wp_bizerbuilder_model').val();
			const selectedPlanner = $('#wp_bizerbuilder_planner_model').val();
			const selectedCoder = $('#wp_bizerbuilder_coder_model').val();
			const selectedReviewer = $('#wp_bizerbuilder_reviewer_model').val();
			
			const $list = $('.custom-models-items').empty();
			const $optgroup = $('#custom-models').empty();
			
			// Clear custom model options from all dropdowns
			$('#wp_bizerbuilder_planner_model optgroup[label="Custom Models"]').remove();
			$('#wp_bizerbuilder_coder_model optgroup[label="Custom Models"]').remove();
			$('#wp_bizerbuilder_reviewer_model optgroup[label="Custom Models"]').remove();
			
			customModels.forEach((model, index) => {
				const $item = $('<div class="custom-model-item">')
					.append(`<strong>${model.name}</strong>`)
					.append(`<details><summary>${wp_bizerbuilder_i18n.details}</summary><p><strong>${wp_bizerbuilder_i18n.url}:</strong> ${model.url}</p><p><strong>${wp_bizerbuilder_i18n.modelParameter}:</strong> ${model.modelParameter}</p><p><strong>${wp_bizerbuilder_i18n.apiKey}:</strong> ***${model.apiKey.substr(-3)}</p><p><strong>${wp_bizerbuilder_i18n.headers}:</strong> ${model.headers.join(', ')}</p></details>`)
					.append(`<button type="button" class="button remove-model" data-index="${index}">${wp_bizerbuilder_i18n.remove}</button>`);
				$list.append($item);

				$optgroup.append(`<option value="${model.name}">${model.name}</option>`);
			});
			
			// Add custom models to specialized dropdowns if any exist
			if (customModels.length > 0) {
				const customOptgroup = `<optgroup label="<?php esc_attr_e( 'Custom Models', 'wp-bizerbuilder' ); ?>">
					${customModels.map(model => `<option value="${model.name}">${model.name}</option>`).join('')}
				</optgroup>`;
				
				$('#wp_bizerbuilder_planner_model').append(customOptgroup);
				$('#wp_bizerbuilder_coder_model').append(customOptgroup);
				$('#wp_bizerbuilder_reviewer_model').append(customOptgroup);
			}
			
			$('#wp_bizerbuilder_custom_models').val(JSON.stringify(customModels));
			
			// Restore selected values
			$('#wp_bizerbuilder_model').val(selectedDefault);
			$('#wp_bizerbuilder_planner_model').val(selectedPlanner);
			$('#wp_bizerbuilder_coder_model').val(selectedCoder);
			$('#wp_bizerbuilder_reviewer_model').val(selectedReviewer);
		}

		$('#add-custom-model').on('click', function() {
			const name = $('#custom-model-name').val();
			const url = $('#custom-model-url').val();
			const modelParameter = $('#custom-model-parameter').val();
			const apiKey = $('#custom-model-api-key').val();
			const headers = $('#custom-model-headers').val();

			if (!name || !url || !apiKey) {
				alert(wp_bizerbuilder_i18n.fillOutFields);
				return;
			}

			const model = {
				name: name,
				url: url,
				modelParameter: modelParameter,
				apiKey: apiKey,
				headers: headers.split('\n').filter(h => h.trim())
			};

			$.ajax({
				url: ajaxurl,
				method: 'POST',
				data: {
					action: 'wp_bizerbuilder_add_model',
					model: model,
					nonce: nonce
				},
				success: function(response) {
					if (response.success) {
						customModels = response.data.models;
						updateCustomModelsList();
						$('#custom-model-name, #custom-model-url, #custom-model-param, #custom-model-api-key, #custom-model-headers').val('');
					} else {
						alert(response.data.message || 'Error saving model');
					}
				}
			});
		});

		$(document).on('click', '.remove-model', function() {
			const index = $(this).data('index');
			if (confirm(wp_bizerbuilder_i18n.removeModel)) {
				$.ajax({
					url: ajaxurl,
					method: 'POST',
					data: {
						action: 'wp_bizerbuilder_remove_model',
						index: index,
						nonce: nonce
					},
					success: function(response) {
						if (response.success) {
							customModels = response.data.models;
							updateCustomModelsList();
						} else {
							alert(response.data.message || wp_bizerbuilder_i18n.errorSavingModel);
						}
					}
				});
			}
		});

		updateCustomModelsList();
	});
</script>
<style>
	/* Specialized Models Toggle */
	#toggle-specialized-models {
		margin-top: 8px;
		display: block;
		color: #2271b1;
		text-decoration: none;
		padding: 0;
	}
	
	#toggle-specialized-models:focus {
		box-shadow: none;
		outline: none;
	}
	
	#toggle-specialized-models .dashicons {
		font-size: 16px;
		line-height: 1.5;
		vertical-align: middle;
	}
	
	.wp-bizerbuilder-per-step-models h3 {
		margin-top: 0;
		padding-top: 0;
	}
	
	/* Custom Models Section Styling */
	#custom-models-list {
		margin-bottom: 20px;
	}

	.custom-model-item {
		background: #fff;
		border: 1px solid #ccd0d4;
		border-radius: 4px;
		padding: 15px 15px 5px;
		margin-bottom: 15px;
		position: relative;
	}

	.custom-model-item strong {
		font-size: 14px;
		color: #1d2327;
	}

	.custom-model-item details {
		margin: 10px 0;
	}

	.custom-model-item summary {
		cursor: pointer;
		color: #2271b1;
		padding: 5px 0;
	}

	.custom-model-item summary:hover {
		color: #135e96;
	}

	.custom-model-item p {
		margin: 8px 0;
		color: #50575e;
	}

	.custom-model-item .remove-model {
		position: absolute;
		right: 15px;
		top: 12px;
		color: #b32d2e;
		border-color: #b32d2e;
	}

	.custom-model-item .remove-model:hover {
		background: #b32d2e;
		color: #fff;
	}

	/* Add Custom Model Form */
	#add-custom-model-form {
		background: #f6f7f7;
		border: 1px solid #c3c4c7;
		border-radius: 4px;
		padding: 20px;
		margin-bottom: 15px;
	}

	#add-custom-model-form input,
	#add-custom-model-form textarea {
		margin-bottom: 15px;
	}

	#add-custom-model-form .button {
		margin-top: 5px;
	}

	/* Description Text */
	.description {
		color: #646970;
		font-style: italic;
		margin-top: 15px;
	}

	/* Input Focus States */
	#add-custom-model-form input:focus,
	#add-custom-model-form textarea:focus {
		border-color: #2271b1;
		box-shadow: 0 0 0 1px #2271b1;
		outline: 2px solid transparent;
	}

	/* make it a box */
	.wp-bizerbuilder-per-step-models {
		background: #fff;
		border: 1px solid #ccd0d4;
		border-radius: 4px;
		padding: 0 2rem;
		margin-bottom: 15px;
	}
</style>
