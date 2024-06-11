<?php
/**
 * Contains code copied from and/or based on Extra Theme by Elegant Themes
 * See the license.txt file in the root directory for more information and licenses
 *
 */

// Prevent file from being loaded directly
if ( ! defined( 'ABSPATH' ) ) {
	die( '-1' );
}

/**
 * List of default category colors. This color is automatically assigned upon theme activation
 * and category creation if user doesn't assign custom color
 * @return array
 */
function et_default_category_colors() {
	$default_category_colors = array(
		'#7ac8cc',
		'#8e6ecf',
		'#db509f',
		'#e8533b',
		'#f29e1a',
		'#8bd623',
		'#5496d3',
		'#6dd69c',
		'#7464f2',
		'#e4751b',
		'#c1d51c',
		'#54d0e2',
	);

	return apply_filters( 'et_default_category_colors', $default_category_colors );
}

/**
 * Get saved category colors for easy comparison
 * @return array
 */
function et_get_saved_category_colors() {
	$et_taxonomy_meta = get_option( 'et_taxonomy_meta' );

	$saved_category_colors = array();

	if ( ! empty( $et_taxonomy_meta ) ) {
		foreach ( $et_taxonomy_meta as $et_taxonomy_meta_item ) {
			foreach ( $et_taxonomy_meta_item as $et_taxonomy_meta_record ) {
				if ( isset( $et_taxonomy_meta_record['color'] ) ) {
					$saved_category_colors[] = $et_taxonomy_meta_record['color'];
				}
			}
		}
	}

	return apply_filters( 'et_saved_category_colors', $saved_category_colors );
}

/**
 * Get unused/least used category color for default category value instead of accent color
 * to ensure variety of colors used by category
 * @return string
 */
function et_get_default_category_color() {
	// Get categories
	$categories = get_categories();

	// Available colors
	$default_category_colors = et_default_category_colors();
	$saved_category_colors   = et_get_saved_category_colors();
	$colors                  = array_diff( $default_category_colors, $saved_category_colors );

	// Return unused default color if there's any
	if ( ! empty( $colors ) ) {
		return array_shift( $colors );
	}

	// Find the least used category color
	$colors_count = array();

	foreach ( $saved_category_colors as $saved_category_color ) {
		if ( ! in_array( $saved_category_color, $default_category_colors ) ) {
			continue;
		}

		$colors_count[] = $saved_category_color;
	}

	// Get the counts for each used color
	$colors_count = array_count_values( $colors_count );

	// Sort colors count from low to high
	asort( $colors_count );

	// Splice the first array element. Direct array_flip might cause issue on array with equal values
	$unused_colors = array_splice( $colors_count, 0, 1 );

	// Flip colors count value
	$unused_colors = array_flip( $unused_colors );

	// Get the value
	return array_shift( $unused_colors );
}

function extra_add_category_edit_form_color_picker( $term, $taxonomy, $wrapper_tag = 'tr' ) {
	$term_id = isset( $term->term_id ) ? $term->term_id : 0;
	$color = et_get_childmost_taxonomy_meta( $term_id, 'color', true, et_get_default_category_color() );

	$default_attr = ' data-default-color="' . esc_attr( $color ) . '"';
	$value_attr = ' value="' . esc_attr( $color ) . '"';
	?>
	<?php printf( '<%1$s class="form-field">', tag_escape( $wrapper_tag ) ); ?>
		<th scope="row"><label for="description"><?php esc_html_e( 'Color', 'extra' ); ?></label></th>
		<td><input class="color-picker-hex" name="extra_category_color" type="text" maxlength="7" placeholder="<?php esc_attr_e( 'Hex Value', 'extra' ); ?>"<?php echo $default_attr; ?><?php echo $value_attr; ?> /><br>
		<span class="description"><?php esc_html_e( 'The color used for this category throughout the site.', 'extra' ); ?></span></td>
	<?php printf( '</%1$s>', tag_escape( $wrapper_tag ) ); ?>

	<?php
}

add_action( 'category_edit_form_fields', 'extra_add_category_edit_form_color_picker', 10, 2 );

function extra_add_category_add_form_color_picker( $taxonomy ) {
	extra_add_category_edit_form_color_picker( 0, $taxonomy, 'div' );
}

add_action( 'category_add_form_fields', 'extra_add_category_add_form_color_picker' );

function extra_add_category_edit_form_color_picker_script() {
	wp_enqueue_script( 'wp-color-picker' );
	wp_enqueue_style( 'wp-color-picker' );
	?>
	<script type="text/javascript">
	(function($){
		$(document).ready( function(){
			$('.color-picker-hex').wpColorPicker();
		});
	})(jQuery)
	</script>
	<style>
	.form-field .wp-picker-input-wrap .button.wp-picker-default {
		width: auto;
	}
	</style>
	<?php
}

add_action( 'category_add_form', 'extra_add_category_edit_form_color_picker_script' );
add_action( 'category_edit_form', 'extra_add_category_edit_form_color_picker_script' );

function extra_edit_terms_save_color( $term_id ) {
	if ( !empty( $_POST['extra_category_color'] ) ) {
		et_update_taxonomy_meta( $term_id, 'color', sanitize_text_field( $_POST['extra_category_color'] ) );
	}
}

add_action( 'edit_terms', 'extra_edit_terms_save_color', 10, 1 ); // fired when existing category saved
add_action( 'created_category', 'extra_edit_terms_save_color', 10, 1 ); // fired when new category saved



// Extra/framework/functions.php
define( 'ET_TAXONOMY_META_OPTION_KEY', "et_taxonomy_meta" );

// Extra/framework/functions.php
if ( !function_exists( 'et_get_childmost_taxonomy_meta' ) ):

	function et_get_childmost_taxonomy_meta( $term_id, $meta_key, $single = false, $default = '',  $taxonomy = 'category' ) {
		global $et_taxonomy_meta;

		if ( !$term = get_term( $term_id, $taxonomy ) ) {
			return $default;
		}

		$result = et_get_taxonomy_meta( $term_id, $meta_key, $single );

		if ( empty( $result ) && isset( $term->parent ) && $term->parent !== 0 ) {
			return et_get_childmost_taxonomy_meta( $term->parent, $meta_key, $single, $default, $taxonomy );
		}

		if ( !empty( $result ) ) {
			return $result;
		}

		return $default;
	}

endif;

// Extra/framework/functions.php
if ( !function_exists( 'et_get_taxonomy_meta' ) ):

	function et_get_taxonomy_meta( $term_id, $meta_key = '', $single = false ) {
		global $et_taxonomy_meta;

		if ( !isset( $et_taxonomy_meta ) ) {
			_et_get_taxonomy_meta();
		}

		if ( !isset( $et_taxonomy_meta[ $term_id ] ) ) {
			$et_taxonomy_meta[ $term_id ] = array();
		}

		if ( empty( $meta_key ) ) {
			return $et_taxonomy_meta[ $term_id ];
		}

		$result = $single ? '' : array();

		foreach ( $et_taxonomy_meta[ $term_id ] as $tax_meta_key => $tax_meta ) {
			foreach ( $tax_meta as $_meta_key => $_meta_value ) {
				if ( $_meta_key === $meta_key ) {
					if ( $single ) {
						$result = $_meta_value;
						break;
					}
					$result[] = $_meta_value;
				}
			}
		}

		return $result;
	}

endif;

// Extra/framework/functions.php
function _et_get_taxonomy_meta() {
	global $et_taxonomy_meta;

	if ( !isset( $et_taxonomy_meta ) ) {
		$et_taxonomy_meta = maybe_unserialize( get_option( ET_TAXONOMY_META_OPTION_KEY, null ) );
		if ( null === $et_taxonomy_meta ) {
			update_option( ET_TAXONOMY_META_OPTION_KEY, array() );
			$et_taxonomy_meta = array();
		}
	}
}

// Extra/framework/functions.php
if ( !function_exists( 'et_update_taxonomy_meta' ) ):

	function et_update_taxonomy_meta( $term_id, $meta_key, $meta_value, $prev_value = '' ) {
		global $et_taxonomy_meta;

		if ( !isset( $et_taxonomy_meta ) ) {
			_et_get_taxonomy_meta();
		}

		if ( !isset( $et_taxonomy_meta[ $term_id ] ) ) {
			$et_taxonomy_meta[ $term_id ] = array();
		}

		$meta_key_found = false;
		foreach ( $et_taxonomy_meta[ $term_id ] as $tax_meta_key => $tax_meta ) {
			foreach ( $tax_meta as $_meta_key => $_meta_value ) {
				if ( $meta_key === $_meta_key ) {
					$meta_key_found = true;
					if ( empty( $prev_value ) ) {
						$et_taxonomy_meta[ $term_id ][ $tax_meta_key ][ $_meta_key  ] = $meta_value;
					} else {
						if ( $prev_value === $_meta_value  ) {
							$et_taxonomy_meta[ $term_id ][ $tax_meta_key ][ $_meta_key  ] = $meta_value;
						}
					}
				}
			}
		}

		if ( !$meta_key_found ) {
			et_add_taxonomy_meta( $term_id, $meta_key, $meta_value );
		}

		_et_update_taxonomy_meta();
	}

endif;

// Extra/framework/functions.php
if ( !function_exists( 'et_add_taxonomy_meta' ) ):

	function et_add_taxonomy_meta( $term_id, $meta_key, $meta_value ) {
		global $et_taxonomy_meta;

		if ( !isset( $et_taxonomy_meta ) ) {
			_et_get_taxonomy_meta();
		}

		if ( !isset( $et_taxonomy_meta[ $term_id ] ) ) {
			$et_taxonomy_meta[ $term_id ] = array();
		}

		$et_taxonomy_meta[ $term_id ][] = array( $meta_key => $meta_value );

		_et_update_taxonomy_meta();
	}

endif;

// Extra/framework/functions.php
function _et_update_taxonomy_meta() {
	global $et_taxonomy_meta;
	update_option( ET_TAXONOMY_META_OPTION_KEY, $et_taxonomy_meta );
}