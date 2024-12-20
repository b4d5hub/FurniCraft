<?php
/**
 * Prompt template.
 */

if ( ! defined( 'ABSPATH' ) ) {
	die( '-1' );
}

?>
<form class="vc_ui-prompt vc_ui-prompt-templates">
	<div class="vc_ui-prompt-controls">
		<button type="button" class="vc_general vc_ui-control-button vc_ui-prompt-close">
			<i class="vc-composer-icon vc-c-icon-close"></i>
		</button>
	</div>
	<div class="vc_ui-prompt-title">
		<label for="prompt_title" class="wpb_element_label"><?php esc_html_e( 'Template Title', 'js_composer' ); ?></label>
	</div>
	<div class="vc_ui-prompt-content">
		<div class="vc_ui-prompt-column">
			<div class="wpb_el_type_textfield vc_wrapper-param-type-textfield vc_properties-list">
				<div class="edit_form_line">
					<input name="title" id="prompt_templates_title" class="wpb_vc_param_value wpb-textinput h4 textfield"
						type="text" value="" data-vc-disable-empty="#vc_ui-save-templates-btn">
					<span
						class="vc_description vc_clearfix"><?php esc_html_e( 'Enter element template title.', 'js_composer' ); ?></span>
				</div>
			</div>
		</div>
		<div class="vc_ui-prompt-column">
			<button type="buttom"
				class="vc_general vc_ui-button vc_ui-button-size-md vc_ui-button-action vc_ui-button-shape-rounded" id="vc_ui-save-templates-btn" disabled><?php esc_html_e( 'Save changes', 'js_composer' ); ?></button>
		</div>
	</div>
</form>
