<?php
/**
 * Adobe fonts settings field adobe_fonts_data hidden field template.
 *
 * @var string $field_value
 * @var string $field_name
 */

?>
<input type="hidden" name="<?php esc_attr_e( $field_name ); ?>" value="<?php esc_attr_e( $field_value ); ?>">
