<h1>{{{ data.title }}}</h1>

<# if ( data.depth ) { #>
	<button type="button" class="button-link smm-button-back-settings">
		<i class="dashicons dashicons-arrow-left-alt"></i>
		<?php esc_html_e( 'Back to mega settings', 'sober' ) ?>
	</button>
<# } #>