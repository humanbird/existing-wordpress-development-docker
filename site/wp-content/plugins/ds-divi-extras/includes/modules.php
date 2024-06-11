<?php
/**
 * Contains code copied from and/or based on Extra Theme by Elegant Themes
 * See the license.txt file in the root directory for more information and licenses
 *
 */
/*
=== extra-functions.php ===

This code is based on and/or copied from the Extra theme by Elegant Themes. See the license.txt file in this plugin.

This file modified by Jonathan Hall, Dominika Rauk, and/or others. Last modified 2020-03-09.
*/

if (!defined('EXTRA_LAYOUT_POST_TYPE')) {
	define('EXTRA_LAYOUT_POST_TYPE', null);
}
if (!defined('ET_TAXONOMY_META_OPTION_KEY')) {
	define('ET_TAXONOMY_META_OPTION_KEY', 'et_taxonomy_meta');
}
if (!defined('EXTRA_PROJECT_POST_TYPE')) {
	define('EXTRA_PROJECT_POST_TYPE', 'project');
}

// Copied from Extra framework/post-formats.php
if ( !defined( 'ET_POST_FORMAT' ) ) {
	define( 'ET_POST_FORMAT', 'et_post_format' );
}
if ( !defined( 'ET_POST_FORMAT_PREFIX' ) ) {
	define( 'ET_POST_FORMAT_PREFIX', 'et-post-format-' );
}

// Copied from Extra includes/template-tags.php
add_action( 'wp_ajax_extra_blog_feed_get_content', 'extra_blog_feed_get_content' );
add_action( 'wp_ajax_nopriv_extra_blog_feed_get_content', 'extra_blog_feed_get_content' );


function extra_global_accent_color() {
	return '#000000';
}



// Following function from Extra includes/core.php

/**
 * Extra Settings :: Enqueue Google Maps API
 *
 * Read and return the 'Enqueue Google Maps Script' setting in Extra's Theme Options.
 *
 * Possible values for `et_google_api_settings['enqueue_google_maps_script']` include:
 *   'false' string - do not enqueue Google Maps script
 *   'on'    string - enqueue Google Maps script on frontend and in WP Admin Post/Page editors
 *
 * @return bool
 */
function et_extra_enqueue_google_maps_api() {

	$google_api_option = get_option( 'et_google_api_settings' );

	// If for some reason the setting doesn't exist, then we probably shouldn't load the API
	if ( ! isset( $google_api_option['enqueue_google_maps_script'] ) ) {
		return false;
	}

	// If the setting has been disabled, then we shouldn't load the API
	if ( 'false' === $google_api_option['enqueue_google_maps_script'] ) {
		return false;
	}

	// If we've gotten this far, let's build the URL and load that API!

	// Google Maps API address
	$gmap_url_base = 'https://maps.googleapis.com/maps/api/js';

	// If we're not using SSL, switch to the HTTP address for the Google Maps API
	// TODO: Is this actually necessary? Security notices don't trigger in this direction
	if ( ! is_ssl() ) {
		$gmap_url_base = 'http://maps.googleapis.com/maps/api/js';
	}

	// Grab the value of `et_google_api_settings['api_key']` and append it to the API's address
	$gmap_url_full = esc_url( add_query_arg( array(
		'key'      => et_pb_get_google_api_key(),
		'callback' => 'initMap'
	), $gmap_url_base ) );

	wp_enqueue_script( 'google-maps-api', $gmap_url_full, array(), '3', true );

}

function extra_ajax_loader_img( $echo = true ) {
	$img = '<img src="' . esc_url(AGS_DIVI_CAT_MODULES_URL.'extra-images/pagination-loading.gif') .'" alt="' . esc_attr__( "Loading", "ds-divi-extras" ) . '" />';
	if ( $echo ) {
		echo et_core_esc_previously( $img );
	} else {
		return $img;
	}
}

function extra_is_post_rating_enabled() {
	return false;
}

// From Extra includes/template-tags.php
function et_get_post_format_thumb( $post_format, $size =  'icon' ) {

	$size = 'icon' == $size ? 'icon' : 'thumb';

	if ( in_array( $post_format, array( 'video', 'quote', 'link', 'audio', 'map', 'text' ) ) ) {
		$img = 'post-format-' . $size . '-' . $post_format . '.svg';
	} else {
		$img = 'post-format-' . $size . '-text.svg';
	}
	
	return AGS_DIVI_CAT_MODULES_URL.'extra-images/'.$img;
}

// From Extra framework/post-formats.php
add_filter( 'post_class', 'et_has_format_content_class', 10, 3 );
add_filter( 'post_class', 'et_set_post_format_default_class', 10, 3 );

/* === end extra-functions.php === */


// phpcs:disable -- the following code is from the Extra Theme file includes/core.php with minimal or no modifications; it is assumed that all needed security measures are already in place
if (!function_exists('extra_global_sidebar_location')) {
function extra_global_sidebar_location() {
	return et_get_option( 'sidebar_location', 'right' );
}
}
if (!function_exists('extra_global_sidebar')) {
function extra_global_sidebar() {
	return et_get_option( 'sidebar', esc_html__( 'Main Sidebar', 'extra' ) );
}
}
// phpcs:enable -- end of code from the Extra Theme file includes/core.php (with minimal or no modifications)



// phpcs:disable -- the following code is from the Extra Theme file framework/functions.php with minimal or no modifications; it is assumed that all needed security measures are already in place
if (!function_exists('et_extra_get_framework_directory_uri')) {
function et_extra_get_framework_directory_uri() {
	$template_template_dir = get_template_directory_uri();
	$framework_dir = apply_filters( 'et_framework_directory', 'framework' );
	return esc_url( $template_template_dir . '/' . $framework_dir );
}
}
if (!function_exists('et_get_theme_version')) {
	function et_get_theme_version() {
		$theme = wp_get_theme();

		// Get parent theme info if a child theme is used.
		if ( is_child_theme() ) {
			$theme = wp_get_theme( $theme->parent_theme );
		}

		return $theme->display( 'Version' );
	}
}
if (!function_exists('et_get_the_author_posts_link')) {
	function et_get_the_author_posts_link(){
		global $authordata, $themename;

		$link = sprintf(
			'<a href="%1$s" title="%2$s" rel="author">%3$s</a>',
			esc_url( get_author_posts_url( $authordata->ID, $authordata->user_nicename ) ),
			esc_attr( sprintf( et_get_safe_localization( __( 'Posts by %s', $themename ) ), get_the_author() ) ),
			get_the_author()
		);
		return apply_filters( 'the_author_posts_link', $link );
	}
}
if (!function_exists('et_postinfo_meta')) {
	function et_postinfo_meta( $postinfo, $date_format, $comment_zero, $comment_one, $comment_more ){
		global $themename;

		$postinfo_meta = '';

		if ( in_array( 'author', $postinfo ) )
		$postinfo_meta .= ' ' . esc_html__( 'by', $themename ) . ' ' . et_get_the_author_posts_link() . ' | ';

		if ( in_array( 'date', $postinfo ) )
		$postinfo_meta .= get_the_time( $date_format ) . ' | ';

		if ( in_array( 'categories', $postinfo ) )
		$postinfo_meta .= get_the_category_list( ', ' ) . ' | ';

		if ( in_array( 'comments', $postinfo ) )
		$postinfo_meta .= et_get_comments_popup_link( $comment_zero, $comment_one, $comment_more );

		echo $postinfo_meta;
	}
}
if (!function_exists('et_truncate_post')) {
function et_truncate_post( $amount, $echo = true, $post = '' ) {
	return truncate_post( $amount, $echo, $post );
}
}
if (!function_exists('et_wp_trim_words')) {
	function et_wp_trim_words( $text, $num_words = 55, $more = null ) {
		if ( null === $more )
		$more = esc_html__( '&hellip;' );
		$original_text = $text;
		// Completely remove icons so that unicode hex entities representing the icons do not get included in words.
		$text = preg_replace( '/<span class="et-pb-icon .*<\/span>/', '', $text );
		$text = wp_strip_all_tags( $text );

		$text = trim( preg_replace( "/[\n\r\t ]+/", ' ', $text ), ' ' );
		preg_match_all( '/./u', $text, $words_array );
		$words_array = array_slice( $words_array[0], 0, $num_words + 1 );
		$sep = '';

		if ( count( $words_array ) > $num_words ) {
			array_pop( $words_array );
			$text = implode( $sep, $words_array );
			$text = $text . $more;
		} else {
			$text = implode( $sep, $words_array );
		}

		return apply_filters( 'wp_trim_words', $text, $num_words, $more, $original_text );
	}
}
if (!function_exists('et_get_current_url')) {
	function et_get_current_url() {
		return ( is_ssl() ? 'https://' : 'http://' ) . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
	}
}
if (!function_exists('et_options_stored_in_one_row')) {
	function et_options_stored_in_one_row(){
		global $et_store_options_in_one_row;

		return isset( $et_store_options_in_one_row ) ? (bool) $et_store_options_in_one_row : false;
	}
}
if (!function_exists('et_generate_wpml_ids')) {
	function et_generate_wpml_ids( $ids_array, $type ) {
		if ( function_exists( 'icl_object_id' ) ) {
			$wpml_ids = array();
			foreach ( $ids_array as $id ) {
				$translated_id = icl_object_id( $id, $type, false );
				if ( ! is_null( $translated_id ) ) $wpml_ids[] = $translated_id;
			}
			$ids_array = $wpml_ids;
		}

		return array_map( 'intval', $ids_array );
	}
}
if (!function_exists('et_init_options')) {
	function et_init_options() {
		global $et_theme_options, $shortname, $et_theme_options_defaults;

		if ( et_options_stored_in_one_row() ) {
			$et_theme_options_name = 'et_' . $shortname;

			if ( ! isset( $et_theme_options ) ) {
				$et_theme_options = get_option( $et_theme_options_name );
				if ( empty( $et_theme_options ) ) {
					update_option( $et_theme_options_name, $et_theme_options_defaults );
				}
			}
		}
	}
}
if (!function_exists('et_list_pings')) {
	function et_list_pings($comment, $args, $depth) {
		$GLOBALS['comment'] = $comment; ?>
		<li id="comment-<?php comment_ID(); ?>"><?php comment_author_link(); ?> - <?php comment_excerpt(); ?>
	<?php }
}
if (!function_exists('et_get_childmost_taxonomy_meta')) {
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
}
if (!function_exists('et_get_taxonomy_meta')) {
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
}
if (!function_exists('et_update_taxonomy_meta')) {
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
}
if (!function_exists('et_add_taxonomy_meta')) {
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
}
if (!function_exists('et_delete_taxonomy_meta')) {
	function et_delete_taxonomy_meta( $term_id, $meta_key, $meta_value = '' ) {
		global $et_taxonomy_meta;

		if ( !isset( $et_taxonomy_meta ) ) {
			_et_get_taxonomy_meta();
		}

		foreach ( $et_taxonomy_meta[ $term_id ] as $tax_meta_key => $tax_meta ) {
			foreach ( $tax_meta as $_meta_key => $_meta_value ) {
				if ( $meta_key === $_meta_key ) {
					if ( empty( $meta_value ) ) {
						unset( $et_taxonomy_meta[ $term_id ][ $tax_meta_key ] );
					} else {
						if ( $meta_value === $_meta_value  ) {
							unset( $et_taxonomy_meta[ $term_id ][ $tax_meta_key ] );
						}
					}
				}
			}
		}

		_et_update_taxonomy_meta();
	}
}
if (!function_exists('_et_get_taxonomy_meta')) {
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
}
if (!function_exists('_et_update_taxonomy_meta')) {
function _et_update_taxonomy_meta() {
	global $et_taxonomy_meta;
	update_option( ET_TAXONOMY_META_OPTION_KEY, $et_taxonomy_meta );
}
}
if (!function_exists('_et_register_sidebar')) {
function _et_register_sidebar( $args ) {
	global $themename;

	$default_args = array(
		'name'          => '',
		'id'            => '',
		'before_widget' => '<div id="%1$s" class="et_pb_widget %2$s">',
		'after_widget'  => '</div> <!-- end .et_pb_widget -->',
		'before_title'  => '<h4 class="widgettitle">',
		'after_title'   => '</h4>',
	);

	$args = wp_parse_args( $args, $default_args );

	if ( empty( $args['name'] ) ) {
		$version = sprintf( '%s, Theme: %s', et_get_theme_version(), $themename );
		_doing_it_wrong( __FUNCTION__, "'name' argument required", $version );
		return;
	}

	if ( empty( $args['id'] ) ) {
		$args['id'] = sanitize_title_with_dashes( $args['name'] );
		if ( strpos( $args['id'], '-sidebar' ) !== false ) {
			$args['id'] = 'sidebar-' . str_replace( '-sidebar', '', $args['id'] );
		}
	}

	register_sidebar( $args );
}
}
if (!function_exists('et_register_widget_areas')) {
function et_register_widget_areas() {
	if ( !current_theme_supports( 'et_widget_areas' ) ) {
		return;
	}

	$et_widget_areas = get_option( 'et_widget_areas' );

	if ( !empty( $et_widget_areas ) ) {
		foreach ( $et_widget_areas['areas'] as $id => $name ) {
			_et_register_sidebar( array(
				'id'   => $id,
				'name' => $name,
			) );

		}
	}
}
}
if (!function_exists('et_add_wp_version')) {
function et_add_wp_version( $classes ) {
	global $wp_version;

	$is_admin_body_class = 'admin_body_class' === current_filter();

	// add 'et-wp-pre-3_8' class if the current WordPress version is less than 3.8
	if ( version_compare( $wp_version, '3.7.2', '<=' ) ) {
		if ( 'body_class' === current_filter() ) {
			$classes[] = 'et-wp-pre-3_8';
		} else {
			$classes .= ' et-wp-pre-3_8';
		}
	} else if ( $is_admin_body_class ) {
		$classes .= ' et-wp-after-3_8';
	}

	if ( $is_admin_body_class ) {
		$classes = ltrim( $classes );
	}

	return $classes;
}
}
if (!function_exists('et_register_customizer_section')) {
function et_register_customizer_section( $wp_customize, $settings, $section, $section_options = '', $panel = '' ) {
	global $shortname;

	if ( empty( $settings ) ) {
		return;
	}

	$section_args = wp_parse_args( $section_options, array(
		'title'    => $section,
		'priority' => 10,
	) );

	if ( !empty( $panel ) ) {
		$section_args['panel'] = $panel;
	}

	$wp_customize->add_section( $section, $section_args );

	foreach ($settings as $option_key => $options) {

		if ( !is_array( $options ) ) {
			$label = $options;
			$options = array();
			$options['label'] = $label;
		}

		$default_options = array(
			'setting_type'   => 'option',
			'type'           => 'text',
			'transport'      => 'postMessage',
			'capability'     => 'edit_theme_options',
			'default'        => '',
			'description'    => '',
			'choices'        => array(),
			'priority'       => 10,
			'global_option'  => false,
			'theme_supports' => '',
		);

		$options = wp_parse_args( $options, $default_options );

		$option_key = true == $options['global_option'] ? $option_key : sprintf( 'et_%s[%s]', $shortname, $option_key );

		switch ( $options['type'] ) {
			case 'dropdown-font-styles':
				$sanitize_callback = 'et_sanitize_font_style';
				break;

			case 'dropdown-fonts':
				$sanitize_callback = 'et_sanitize_font_choices';
				break;

			case 'color':
				$sanitize_callback = 'sanitize_hex_color';
				break;

			case 'et_coloralpha':
				$sanitize_callback = 'et_sanitize_alpha_color';
				break;

			case 'checkbox':
				$sanitize_callback = 'wp_validate_boolean';
				break;

			case 'range':
				if ( isset( $options['input_attrs']['step'] ) && $options['input_attrs']['step'] < 1 ) {
					$sanitize_callback = 'et_sanitize_float_number';
				} else {
					$sanitize_callback = 'et_sanitize_int_number';
				}
				break;
			default:
				$sanitize_callback = '';
				break;
		}

		$wp_customize->add_setting( $option_key, array(
			'default'           => $options['default'],
			'type'              => $options['setting_type'],
			'capability'        => $options['capability'],
			'transport'         => $options['transport'],
			'theme_supports'    => $options['theme_supports'],
			'sanitize_callback' => $sanitize_callback,
		) );

		$control_options = array(
			'label'       => $options['label'],
			'section'     => $section,
			'description' => $options['description'],
			'settings'    => $option_key,
			'type'        => $options['type'],
			'priority'    => $options['priority'],
		);

		switch ( $options['type'] ) {
			case 'color':
				$wp_customize->add_control( new WP_Customize_Color_Control( $wp_customize, $option_key, $control_options ) );
				break;
			case 'et_coloralpha':
				$wp_customize->add_control( new ET_Color_Alpha_Control( $wp_customize, $option_key, $control_options ) );
				break;
			case 'range':
				$control_options = array_merge( $control_options, array(
					'input_attrs' => $options['input_attrs'],
				) );
				$wp_customize->add_control( new ET_Range_Control( $wp_customize, $option_key, $control_options ) );
				break;
			case 'radio':
				$control_options = array_merge( $control_options, array(
					'choices' => $options['choices'],
				) );
				$wp_customize->add_control( $option_key, $control_options );
				break;
			case 'dropdown-font-styles':
				$control_options = array_merge( $control_options, array(
					'type'    => 'select',
					'choices' => et_extra_font_style_choices(),
				) );
				$wp_customize->add_control( new ET_Font_Style_Control( $wp_customize, $option_key, $control_options ) );
				break;
			case 'dropdown-fonts':
				if ( et_is_one_font_language() ) {
					break;
				}
				$control_options = array_merge( $control_options, array(
					'type'    => 'select',
					'choices' => et_dropdown_google_font_choices(),
				) );
				$wp_customize->add_control( new ET_Font_Select_Control( $wp_customize, $option_key, $control_options ) );
				break;
			case 'select':
			default:
				$control_options = array_merge( $control_options, array(
					'choices' => $options['choices'],
				) );
				$wp_customize->add_control( $option_key, $control_options );
				break;
		}

		$options['priority']++;
	}
}
}
if (!function_exists('et_is_one_font_language')) {
	function et_is_one_font_language() {
		static $et_is_one_font_language = null;

		if ( is_null( $et_is_one_font_language ) ) {
			$site_domain = get_locale();
			$et_one_font_languages = et_get_one_font_languages();

			$et_is_one_font_language = (bool) isset( $et_one_font_languages[$site_domain] );
		}

		return $et_is_one_font_language;
	}
}
if (!function_exists('et_get_one_font_languages')) {
	function et_get_one_font_languages() {
		$one_font_languages = array(
			'he_IL' => array(
				'language_name'   => 'Hebrew',
				'google_font_url' => '//fonts.googleapis.com/earlyaccess/alefhebrew.css',
				'font_family'     => "'Alef Hebrew', serif",
			),
			'ja'    => array(
				'language_name'   => 'Japanese',
				'google_font_url' => '//fonts.googleapis.com/earlyaccess/notosansjapanese.css',
				'font_family'     => "'Noto Sans Japanese', serif",
			),
			'ko_KR' => array(
				'language_name'   => 'Korean',
				'google_font_url' => '//fonts.googleapis.com/earlyaccess/hanna.css',
				'font_family'     => "'Hanna', serif",
			),
			'ar'    => array(
				'language_name'   => 'Arabic',
				'google_font_url' => '//fonts.googleapis.com/earlyaccess/lateef.css',
				'font_family'     => "'Lateef', serif",
			),
			'th'    => array(
				'language_name'   => 'Thai',
				'google_font_url' => '//fonts.googleapis.com/earlyaccess/notosansthai.css',
				'font_family'     => "'Noto Sans Thai', serif",
			),
			'ms_MY' => array(
				'language_name'   => 'Malay',
				'google_font_url' => '//fonts.googleapis.com/earlyaccess/notosansmalayalam.css',
				'font_family'     => "'Noto Sans Malayalam', serif",
			),
			'zh_CN' => array(
				'language_name'   => 'Chinese',
				'google_font_url' => '//fonts.googleapis.com/earlyaccess/cwtexfangsong.css',
				'font_family'     => "'cwTeXFangSong', serif",
			),
		);

		return $one_font_languages;
	}
}
if (!function_exists('et_dropdown_google_font_choices')) {
	function et_dropdown_google_font_choices() {
		static $et_dropdown_google_font_choices = null;

		if ( is_null( $et_dropdown_google_font_choices ) ) {
			$site_domain = get_locale();

			$user_fonts = et_builder_get_custom_fonts();

			$google_fonts = et_builder_get_fonts( array(
				'prepend_standard_fonts' => false,
			) );

			// combine google fonts with custom user fonts
			$google_fonts = array_merge( $user_fonts, $google_fonts );

			$et_domain_fonts = array(
				'ru_RU' => 'cyrillic',
				'uk'    => 'cyrillic',
				'bg_BG' => 'cyrillic',
				'vi'    => 'vietnamese',
				'el'    => 'greek',
				'ar'    => 'arabic',
				'he_IL' => 'hebrew',
				'th'    => 'thai',
				'si_lk' => 'sinhala',
				'bn_bd' => 'bengali',
				'ta_lk' => 'tamil',
				'te'    => 'telegu',
				'km'    => 'khmer',
				'kn'    => 'kannada',
				'ml_in' => 'malayalam',
			);

			$font_choices = array();
			$font_choices['none'] = array(
				'label' => 'Default Theme Font',
			);

			$removed_fonts_mapping = et_builder_old_fonts_mapping();

			foreach ( $google_fonts as $google_font_name => $google_font_properties ) {
				$use_parent_font = false;

				if ( isset( $removed_fonts_mapping[ $google_font_name ] ) ) {
					$parent_font = $removed_fonts_mapping[ $google_font_name ]['parent_font'];
					$google_font_properties['character_set'] = $google_fonts[ $parent_font ]['character_set'];
					$use_parent_font = true;
				}

				if ( '' !== $site_domain && isset( $et_domain_fonts[$site_domain] ) && false === strpos( $google_font_properties['character_set'], $et_domain_fonts[$site_domain] ) ) {
					continue;
				}
				$font_choices[ $google_font_name ] = array(
					'label' => $google_font_name,
					'data'  => array(
						'parent_font'    => $use_parent_font ? $google_font_properties['parent_font'] : '',
						'parent_styles'  => $use_parent_font ? $google_fonts[$parent_font]['styles'] : $google_font_properties['styles'],
						'current_styles' => $use_parent_font && isset( $google_fonts[$parent_font]['styles'] ) && isset( $google_font_properties['styles'] ) ? $google_font_properties['styles'] : '',
						'parent_subset'  => $use_parent_font && isset( $google_fonts[$parent_font]['character_set'] ) ? $google_fonts[$parent_font]['character_set'] : '',
					),
				);
			}

			$et_dropdown_google_font_choices = $font_choices;
		}

		return $et_dropdown_google_font_choices;
	}
}
if (!function_exists('et_print_font_style')) {
function et_print_font_style( $styles = '', $important = '', $boldness = 'bold' ) {
	// Prepare variable
	$font_styles = "";

	if ( '' !== $styles && false !== $styles ) {
		// Convert string into array
		$styles_array = explode( '|', $styles );

		// If $important is in use, give it a space
		if ( $important && '' !== $important ) {
			$important = " " . $important;
		}

		// Use in_array to find values in strings. Otherwise, display default text

		// Font weight
		if ( in_array( 'bold', $styles_array ) ) {
			$font_styles .= "font-weight: {$boldness}{$important}; ";
		} else {
			$font_styles .= "font-weight: normal{$important}; ";
		}

		// Font style
		if ( in_array( 'italic', $styles_array ) ) {
			$font_styles .= "font-style: italic{$important}; ";
		} else {
			$font_styles .= "font-style: normal{$important}; ";
		}

		// Text-transform
		if ( in_array( 'uppercase', $styles_array ) ) {
			$font_styles .= "text-transform: uppercase{$important}; ";
		} else {
			$font_styles .= "text-transform: none{$important}; ";
		}

		// Text-decoration
		if ( in_array( 'underline', $styles_array ) ) {
			$font_styles .= "text-decoration: underline{$important}; ";
		} else {
			$font_styles .= "text-decoration: none{$important}; ";
		}
	}

	return esc_html( $font_styles );
}
}
if (!function_exists('et_paginate_links')) {
function et_paginate_links( $args = '' ) {
	$defaults = array(
		'base'               => '%_%', // http://example.com/all_posts.php%_% : %_% is replaced by format (below)
		'format'             => '?page=%#%', // ?page=%#% : %#% is replaced by the page number
		'total'              => 1,
		'current'            => 0,
		'show_all'           => false,
		'prev_next'          => true,
		'prev_text'          => esc_html__( '&laquo; Previous', 'extra' ),
		'next_text'          => esc_html__( 'Next &raquo;', 'extra' ),
		'beg_size'           => 1,
		'end_size'           => 1,
		'mid_size'           => 2,
		'type'               => 'plain',
		'add_args'           => false, // array of query args to add
		'add_fragment'       => '',
		'before_page_number' => '',
		'after_page_number'  => '',
	);

	$args = wp_parse_args( $args, $defaults );

	// Who knows what else people pass in $args
	$args['total'] = (int) $args['total'];
	if ( $args['total'] < 2 )
		return;
	$args['current']  = (int) $args['current'];
	$args['beg_size'] = 0 < (int) $args['beg_size'] ? (int) $args['beg_size'] : 1; // Out of bounds?  Make it the default.
	$args['end_size'] = 0 < (int) $args['end_size'] ? (int) $args['end_size'] : 1; // Out of bounds?  Make it the default.
	$args['mid_size'] = 0 <= (int) $args['mid_size'] ? (int) $args['mid_size'] : 2;
	$args['add_args'] = is_array( $args['add_args'] ) ? $args['add_args'] : false;
	$r = '';
	$page_links = array();
	$n = 0;
	$dots = false;

	if ( $args['prev_next'] && $args['current'] && 1 < $args['current'] ) :
		$link = str_replace( '%_%', 2 == $args['current'] ? '' : $args['format'], $args['base'] );
		$link = str_replace( '%#%', $args['current'] - 1, $link );
		if ( $args['add_args'] )
			$link = add_query_arg( $args['add_args'], $link );
		$link .= $args['add_fragment'];

		/**
		 * Filter the paginated links for the given archive pages.
		 *
		 * @since 3.0.0
		 *
		 * @param string $link The paginated link URL.
		 */

		$html = '<a class="prev page-numbers" href="' . esc_url( apply_filters( 'paginate_links', $link ) ) . '">' . $args['prev_text'] . '</a>';
		$html = $args['type'] == "list" ? '<li class="prev">' . $html . '</li>' : $html;
		$page_links[] = $html;

	endif;
	for ( $n = 1; $n <= $args['total']; $n++ ) :
		if ( $n == $args['current'] ) :
			$html = "<span class='page-numbers current'>" . $args['before_page_number'] . number_format_i18n( $n ) . $args['after_page_number'] . "</span>";
			$html = $args['type'] == "list" ? '<li class="current">' . $html . '</li>' : $html;
			$page_links[] = $html;
			$dots = true;
		else :
			if ( $args['show_all'] || ( $n <= $args['beg_size'] || ( $args['current'] && $n >= $args['current'] - $args['mid_size'] && $n <= $args['current'] + $args['mid_size'] ) || $n > $args['total'] - $args['end_size'] ) ) :
				$link = str_replace( '%_%', 1 == $n ? '' : $args['format'], $args['base'] );
				$link = str_replace( '%#%', $n, $link );
				if ( $args['add_args'] )
					$link = add_query_arg( $args['add_args'], $link );
				$link .= $args['add_fragment'];

				/** This filter is documented in wp-includes/general-template.php */
				$html = "<a class='page-numbers' href='" . esc_url( apply_filters( 'paginate_links', $link ) ) . "'>" . $args['before_page_number'] . number_format_i18n( $n ) . $args['after_page_number'] . "</a>";
				$html = $args['type'] == "list" ? '<li>' . $html . '</li>' : $html;
				$page_links[] = $html;
				$dots = true;
			elseif ( $dots && !$args['show_all'] ) :
				$html = '<span class="page-numbers dots">' . esc_html__( '&hellip;', 'extra' ) . '</span>';
				$html = $args['type'] == "list" ? '<li class="dots">' . $html . '</li>' : $html;
				$page_links[] = $html;
				$dots = false;
			endif;
		endif;
	endfor;
	if ( $args['prev_next'] && $args['current'] && ( $args['current'] < $args['total'] || -1 == $args['total'] ) ) :
		$link = str_replace( '%_%', $args['format'], $args['base'] );
		$link = str_replace( '%#%', $args['current'] + 1, $link );
		if ( $args['add_args'] )
			$link = add_query_arg( $args['add_args'], $link );
		$link .= $args['add_fragment'];

		/** This filter is documented in wp-includes/general-template.php */
		$html = '<a class="next page-numbers" href="' . esc_url( apply_filters( 'paginate_links', $link ) ) . '">' . $args['next_text'] . '</a>';
		$html = $args['type'] == "list" ? '<li class="next">' . $html . '</li>' : $html;
		$page_links[] = $html;
	endif;
	switch ( $args['type'] ) :
		case 'array' :
			return $page_links;
			break;
		case 'list' :
			$r .= "<ul class='page-numbers'>\n\t";
			$r .= join( "\n\t", $page_links );
			$r .= "\n</ul>\n";
			break;
		default :
			$r = join( "\n", $page_links );
			break;
	endswitch;
	return $r;
}
}
if (!function_exists('et_show_cart_total')) {
	function et_show_cart_total( $args = array() ) {
		global $shortname;

		if ( ! class_exists( 'woocommerce' ) ) {
			return;
		}

		$defaults = array(
			'no_text' => false,
		);

		$args = wp_parse_args( $args, $defaults );

		$cart_count = WC()->cart->get_cart_contents_count();
		$url        = function_exists( 'wc_get_cart_url' ) ? wc_get_cart_url() : WC()->cart->get_cart_url();

		printf(
			'<a href="%1$s" class="et-cart" title="%2$s">
				<span>%3$s</span>
			</a>',
			esc_url( $url ),
			esc_attr( sprintf( _n( '%d Item in Cart', '%d Items in Cart', $cart_count, $shortname ), $cart_count ) ),
			esc_html( ! $args['no_text'] ? sprintf( _n( '%d Item', '%d Items', $cart_count, $shortname ), $cart_count ) : $cart_count )
		);
	}
}
if (!function_exists('et_cart_has_total')) {
	function et_cart_has_total() {
		global $shortname;

		if ( ! class_exists( 'woocommerce' ) ) {
			return;
		}

		$cart_count = WC()->cart->get_cart_contents_count();

		return (bool) $cart_count;
	}
}
if (!function_exists('et_extra_activate_features')) {
	function et_extra_activate_features(){
		define( 'ET_SHORTCODES_VERSION', et_get_theme_version() );

		/* activate shortcodes */
		require_once( get_template_directory() . '/epanel/shortcodes/shortcodes.php' );
	}
}
if (!function_exists('et_extra_theme_options_link')) {
	function et_extra_theme_options_link() {
		return admin_url( 'admin.php?page=et_extra_options' );
	}
}
// phpcs:enable -- end of code from the Extra Theme file framework/functions.php (with minimal or no modifications)



// phpcs:disable -- the following code is from the Extra Theme file includes/template-tags.php with minimal or no modifications; it is assumed that all needed security measures are already in place
if (!function_exists('extra_the_post_categories')) {
function extra_the_post_categories( $post_id = 0, $before = null, $sep = ', ', $after = '' ) {
	echo extra_get_the_post_categories( $post_id, $before, $sep, $after );
}
}
if (!function_exists('extra_get_the_post_categories')) {
function extra_get_the_post_categories( $post_id = 0, $before = null, $sep = ', ', $after = '' ) {
	return get_the_term_list( $post_id, 'category', $before, $sep, $after );
}
}
if (!function_exists('extra_get_the_post_date')) {
function extra_get_the_post_date( $post = null, $date_format = '' ) {
	$date_format = !empty( $date_format ) ? $date_format : get_option( 'date_format' );
	return '<span class="updated">' . get_the_time( $date_format, $post ) . '</span>';
}
}
if (!function_exists('extra_the_post_date')) {
function extra_the_post_date( $post = null ) {
	echo extra_get_the_post_date( $post );
}
}
if (!function_exists('extra_get_the_post_score_bar')) {
function extra_get_the_post_score_bar( $args = array() ) {
	$default_args = array(
		'post_id' => 0,
	);

	$args = wp_parse_args( $args, $default_args );

	$post_id = $args['post_id'] ? $args['post_id'] : get_the_ID();

	$color = extra_get_post_category_color( $post_id );
	$breakdown_score = get_post_meta( $post_id, '_post_review_box_breakdowns_score', true );

	if ( false === extra_post_review() ) {
		return;
	}

	$bar = sprintf('<span class="score-bar" style="width:%1$d%%;background-color:%2$s;"><span class="score-text">%3$s</span></span>',
		esc_attr( max( 9, intval( $breakdown_score ) ) ),
		esc_attr( $color ),
		sprintf( esc_html__( 'Score %1$d%%', 'extra' ), intval( $breakdown_score ) )
	);
	return $bar;
}
}
if (!function_exists('extra_post_review')) {
function extra_post_review( $post_id = 0 ) {
	$post_id = $post_id ? $post_id : get_the_ID();

	$review = array();

	$review_breakdowns = (array) get_post_meta( $post_id, '_post_review_box_breakdowns', true );
	if ( 1 === count( $review_breakdowns ) && empty( $review_breakdowns[0]['title'] ) && empty( $review_breakdowns[0]['rating'] ) ) {
		return false;
	}

	$review['title'] = get_post_meta( $post_id, '_post_review_box_title', true );
	$review['summary'] = get_post_meta( $post_id, '_post_review_box_summary', true );
	$review['summary_title'] = get_post_meta( $post_id, '_post_review_box_summary_title', true );
	$review['breakdowns'] = $review_breakdowns;
	$review['score'] = get_post_meta( $post_id, '_post_review_box_breakdowns_score', true );
	$review['score_title'] = get_post_meta( $post_id, '_post_review_box_score_title', true );
	return $review;
}
}
if (!function_exists('extra_the_post_comments_link')) {
function extra_the_post_comments_link( $post_id = 0 ) {
	$post_id = $post_id ? $post_id : get_the_ID();

	echo extra_get_the_post_comments_link( $post_id );
}
}
if (!function_exists('extra_get_the_post_comments_link')) {
function extra_get_the_post_comments_link( $post_id = 0 ) {
	$post_id = $post_id ? $post_id : get_the_ID();

	return sprintf(
		'<a class="comments-link" href="%s">%d <span title="%s" class="comment-bubble post-meta-icon"></span></a>',
		esc_attr( get_the_permalink( $post_id ) . '#comments' ),
		esc_html( get_comments_number( $post_id ) ),
		esc_attr( __( 'comment count', 'extra' ) )
	);
}
}
if (!function_exists('extra_the_post_featured_image')) {
function extra_the_post_featured_image( $args = array() ) {
	echo extra_get_the_post_featured_image( $args );
}
}
if (!function_exists('extra_get_the_post_featured_image')) {
function extra_get_the_post_featured_image( $args = array() ) {
	$default_args = array(
		'size'      => 'extra-image-huge',
		'a_class'   => array('featured-image'),
		'img_after' => extra_get_the_post_score_bar(),
	);

	$args = wp_parse_args( $args, $default_args );

	return et_extra_get_post_thumb( $args );
}
}
if (!function_exists('et_extra_get_post_thumb')) {
function et_extra_get_post_thumb( $args = array() ) {
	$default_args = array(
		'post_id'                    => 0,
		'size'                       => '',
		'height'                     => 50,
		'width'                      => 50,
		'title'                      => '',
		'link_wrapped'               => true,
		'permalink'                  => '',
		'a_class'                    => array(),
		'img_class'                  => array(),
		'img_style'                  => '',
		'img_after'                  => '', // Note: this value is not escaped/sanitized, and should be used for internal purposes only, not any user input
		'post_format_thumb_fallback' => false,
		'fallback'                   => '',
		'thumb_src'                  => '',
		'return'                     => 'img',
	);

	$args = wp_parse_args( $args, $default_args );

	$post_id = $args['post_id'] ? $args['post_id'] : get_the_ID();
	$permalink = !empty( $args['permalink'] ) ? $args['permalink'] : get_the_permalink( $post_id );
	$title = !empty( $args['title'] ) ? $args['title'] : get_the_title( $post_id );

	$width = (int) apply_filters( 'et_extra_post_thumbnail_width', $args['width'] );
	$height = (int) apply_filters( 'et_extra_post_thumbnail_height', $args['height'] );
	$size = !empty( $args['size'] ) ? $args['size'] : array( $width, $height );
	$thumb_src = $args['thumb_src'];
	$img_style = $args['img_style'];

	$thumbnail_id = get_post_thumbnail_id( $post_id );

	if ( !$thumbnail_id && !$args['thumb_src'] ) {
		if ( $args['post_format_thumb_fallback'] ) {
			$post_format = et_get_post_format();
			if ( in_array( $post_format, array( 'video', 'quote', 'link', 'audio', 'map', 'text' ) ) ) {
				$thumb_src = et_get_post_format_thumb( $post_format, 'thumb' );
			} else {
				$thumb_src = et_get_post_format_thumb( 'text', 'thumb' );
			}
		} else if ( !empty( $args['fallback'] ) ) {
			return $args['fallback'];
		} else {
			$thumb_src = et_get_post_format_thumb( 'text', 'icon' );
		}
	}

	if ( $thumbnail_id ) {
		list($thumb_src, $thumb_width, $thumb_height) = wp_get_attachment_image_src( $thumbnail_id, $size );
	}

	if ( 'thumb_src' === $args['return'] ) {
		return $thumb_src;
	}

	$image_output = sprintf(
		'<img src="%1$s" alt="%2$s"%3$s %4$s/>%5$s',
		esc_attr( $thumb_src ),
		esc_attr( $title ),
		( !empty( $args['img_class'] ) ? sprintf( ' class="%s"', esc_attr( implode( ' ', $args['img_class'] ) ) ) : '' ),
		( !empty( $img_style ) ? sprintf( ' style="%s"', esc_attr( $img_style ) ) : '' ),
		$args['img_after']
	);

	if ( $args['link_wrapped'] ) {
		$image_output = sprintf(
			'<a href="%1$s" title="%2$s"%3$s%5$s>
				%4$s
			</a>',
			esc_attr( $permalink ),
			esc_attr( $title ),
			( !empty( $args['a_class'] ) ? sprintf( ' class="%s"', esc_attr( implode( ' ', $args['a_class'] ) ) ) : '' ),
			$image_output,
			( !empty( $img_style ) ? sprintf( ' style="%s"', esc_attr( $img_style ) ) : '' )
		);
	}

	return $image_output;
}
}
if (!function_exists('et_thumb_as_style_background')) {
function et_thumb_as_style_background() {
	$thumb_src = et_extra_get_post_thumb( array(
		'size'   => extra_get_column_thumbnail_size(),
		'return' => 'thumb_src',
	) );

	if ( ! empty( $thumb_src ) ) {
		echo 'style="background-image: url(' . esc_attr( $thumb_src ) . ');"';
	}

	return;
}
}
if (!function_exists('et_get_post_format_thumb')) {
function et_get_post_format_thumb( $post_format, $size =  'icon' ) {
	$template_dir = get_template_directory_uri();

	$size = 'icon' == $size ? 'icon' : 'thumb';

	if ( in_array( $post_format, array( 'video', 'quote', 'link', 'audio', 'map', 'text' ) ) ) {
		$img = 'post-format-' . $size . '-' . $post_format . '.svg';
	} else {
		$img = 'post-format-' . $size . '-text.svg';
	}

	return $template_dir . '/images/' . $img;
}
}
if (!function_exists('et_get_gallery_post_format_thumb')) {
function et_get_gallery_post_format_thumb() {
	$attachment_ids = get_post_meta( get_the_ID(), '_gallery_format_attachment_ids', true );
	$attachment_ids = explode( ',', $attachment_ids );
	if ( count( $attachment_ids ) ) {
		foreach ( $attachment_ids as $attachment_id ) {
			list($thumb_src, $thumb_width, $thumb_height) = wp_get_attachment_image_src( $attachment_id, 'full' );
			return $thumb_src;
		}
	} else {
		return et_get_post_format_thumb( 'gallery', 'thumb' );
	}
}
}
if (!function_exists('extra_get_post_rating_stars')) {
function extra_get_post_rating_stars( $post_id = 0 ) {
	$rating = extra_get_post_rating( $post_id );
	$output = '<span class="rating-stars" title="' . esc_attr( sprintf( __( 'Rating: %0.2f', 'extra' ), $rating ) ) .'">' . extra_make_mini_stars( $rating ) . '</span>';
	return $output;
}
}
if (!function_exists('extra_the_post_rating_stars')) {
function extra_the_post_rating_stars( $post_id = 0 ) {
	echo extra_get_post_rating_stars( $post_id );
}
}
if (!function_exists('extra_the_post_rating_stars_with_rating_count')) {
function extra_the_post_rating_stars_with_rating_count( $post_id = 0 ) {
	$rating = extra_get_user_post_rating( $post_id );
	$rating_count = extra_get_post_ratings_count();
	printf(
		'<span class="post-rating-stars">%s<span class="rating">%s</span></span>',
		extra_make_mini_stars( $rating ),
		esc_html( sprintf( _n( '%d rating', '%d ratings', $rating_count ), $rating_count ) )
	);
}
}
if (!function_exists('extra_is_post_rating_enabled')) {
function extra_is_post_rating_enabled( $post_id = 0 ) {
	if ( false === et_get_post_meta_setting( 'all', 'rating_stars' ) ) {
		return false;
	}

	if ( is_single() && false === et_get_post_meta_setting( 'post', 'rating_stars' ) ) {
		return false;
	}

	$post_id = empty( $post_id ) ? get_the_ID() : $post_id;

	$hide_rating = get_post_meta( $post_id, '_post_extra_rating_hide', true );

	$has_post_rating = $hide_rating ? false : true;

	return apply_filters( 'extra_is_post_rating_enabled', $has_post_rating, $post_id );
}
}
if (!function_exists('is_post_extra_element_enabled')) {
function is_post_extra_element_enabled( $element = false, $post_id = 0 ) {
	$allowed_elements = array(
		'title_meta',
		'featured_image',
	);

	if ( ! $element || ! in_array( $element, $allowed_elements ) ) {
		return false;
	}

	$post_id = empty( $post_id ) ? get_the_ID() : $post_id;

	// by default, don't hide element
	$hide_element_setting = false;

	if ( is_singular() ) {
		$hide_element_setting = get_post_meta( $post_id, "_post_extra_{$element}_hide_single", true );
	}

	$has_element = $hide_element_setting ? false : true;

	return apply_filters( "is_post_extra_{$element}_enabled", $has_element, $post_id );
}
}
if (!function_exists('is_post_extra_title_meta_enabled')) {
function is_post_extra_title_meta_enabled( $post_id = 0 ) {
	return is_post_extra_element_enabled( 'title_meta' );
}
}
if (!function_exists('is_post_extra_featured_image_enabled')) {
function is_post_extra_featured_image_enabled( $post_id = 0 ) {
	return is_post_extra_element_enabled( 'featured_image' );
}
}
if (!function_exists('post_extra_class')) {
function post_extra_class( $classes ) {
	$flexible_elements = apply_filters( 'post_extra_class_flexible_elements', array(
		'title_meta',
		'featured_image',
	) );

	if ( ! empty( $flexible_elements ) ) {
		foreach ( $flexible_elements as $element ) {
			if ( ! is_post_extra_element_enabled( $element ) ) {
				$classes[] = "et-doesnt-have-{$element}";
			}
		}
	}

	return $classes;
}
}
if (!function_exists('extra_rating_stars_display')) {
function extra_rating_stars_display( $post_id = 0 ) {
	$post_id = empty( $post_id ) ? get_the_ID() : $post_id;

	if ( $rating = extra_get_user_post_rating( $post_id ) ) {
		$output = '<p id="rate-title" class="rate-title">' . esc_html__( 'Your Rating:', 'extra' ) . '</p>';
		$output .= '<div id="rated-stars">' . extra_make_fixed_stars( $rating ) . '</div>';

	} else {
		$title = esc_html__( 'Rate:', 'extra' );

		$output = '<p id="rate-title" class="rate-title">' . esc_html__( 'Rate:', 'extra' ) . '</p>';
		$output .= '<div id="rating-stars"></div>';
		$output .= '<input type="hidden" id="post_id" value="' . $post_id . '" />';
	}

	echo $output;
}
}
if (!function_exists('extra_make_fixed_stars')) {
function extra_make_fixed_stars( $rating ) {
	$images_base = get_template_directory_uri() . '/images/';

	$output = '';
	for ( $x = 1; $x <= 5; $x++ ) {

		if ( $x <= $rating ) {
			$class = 'star-on';
			$icon = 'full';
		} elseif ( ( $x - 0.5 ) <= $rating ) {
			$class = 'star-on star-half';
			$icon = 'half-full';
		} else {
			$class = 'star-off';
			$icon = 'full';
		}

		$src = $images_base . 'star-' . $icon . '.svg';
		$output .= '<img src="' . $src . '" class="rating-star '. $class . ' rating-star-' . $x . '" alt="' . esc_attr__( "Star", "extra" ) . '" />' . "\n";
	}

	return $output;
}
}
if (!function_exists('extra_make_mini_stars')) {
function extra_make_mini_stars( $rating ) {
	$output = '';
	for ( $x = 1; $x <= 5; $x++ ) {

		if ( $x <= $rating ) {
			$class = 'rating-star-on';
		} elseif ( ( $x - 0.5 ) <= $rating ) {
			$class = 'rating-star-half';
		} else {
			$class = 'rating-star-empty';
		}
		$output .= '<span class="post-meta-icon rating-star '. $class . ' rating-star-' . $x . '"></span>'."\n";
	}

	return $output;
}
}
if (!function_exists('extra_sidebar_class')) {
function extra_sidebar_class() {
	echo extra_get_sidebar_class();
}
}
if (!function_exists('extra_sidebar_body_class')) {
function extra_sidebar_body_class( $classes ){
	$classes = array_merge( $classes, explode( ' ', extra_get_sidebar_class() ) );
	return $classes;
}
}
if (!function_exists('extra_get_sidebar_class')) {
function extra_get_sidebar_class() {
	if ( 'product' == get_query_var( 'post_type' ) ) {
		$post_id = empty( $post_id ) ? get_the_ID() : $post_id;
		$sidebar_location = get_post_meta( $post_id, '_extra_sidebar_location', true );
	} else if ( is_singular() ) {
		$post_id = empty( $post_id ) ? get_the_ID() : $post_id;
		$sidebar_location = get_post_meta( $post_id, '_extra_sidebar_location', true );
	} else if ( is_archive() ) {
		if ( $layout_id = extra_get_tax_layout_id() ) {
			$sidebar_location = get_post_meta( $layout_id, '_extra_sidebar_location', true );
		}
	} else if ( is_home() && et_extra_show_home_layout() ) {
		if ( $layout_id = extra_get_home_layout_id() ) {
			$sidebar_location = get_post_meta( $layout_id, '_extra_sidebar_location', true );
		}
	}

	if ( empty( $sidebar_location ) ) {
		if ( 'product' == get_query_var( 'post_type' ) ) {
			$sidebar_location = et_get_option( 'woocommerce_sidebar_location', extra_global_sidebar_location() );
		} else {
			$sidebar_location = extra_global_sidebar_location();
		}
	}

	// Project's sidebar location overrides
	if ( is_singular( EXTRA_PROJECT_POST_TYPE ) ) {
		$project_details = extra_get_project_details();

		if ( isset( $project_details['location'] ) && 'single_col' === $project_details['location'] ) {
			$sidebar_location = 'none';
		}
	}

	$class = '';
	if ( 'none' != $sidebar_location ) {
		$class .= 'with_sidebar';

		$class .= 'right' == $sidebar_location ? ' with_sidebar_right' : ' with_sidebar_left';
	}

	return $class;
}
}
if (!function_exists('extra_sidebar')) {
function extra_sidebar() {
	$is_woocommerce_sidebar = 'product' === get_query_var( 'post_type' ) || is_tax( 'product_cat' ) || is_tax( 'product_tag' );

	if ( $is_woocommerce_sidebar ) {
		$post_id = empty( $post_id ) ? get_the_ID() : $post_id;
		$sidebar = get_post_meta( $post_id, '_extra_sidebar', true );
		$sidebar_location = get_post_meta( $post_id, '_extra_sidebar_location', true );
	} else if ( is_singular() ) {
		$post_id = empty( $post_id ) ? get_the_ID() : $post_id;
		$sidebar = get_post_meta( $post_id, '_extra_sidebar', true );
		$sidebar_location = get_post_meta( $post_id, '_extra_sidebar_location', true );
	} else if ( is_archive() ) {
		if ( $layout_id = extra_get_tax_layout_id() ) {
			$sidebar = get_post_meta( $layout_id, '_extra_sidebar', true );
			$sidebar_location = get_post_meta( $layout_id, '_extra_sidebar_location', true );
		}
	} else if ( is_home() && et_extra_show_home_layout() ) {
		if ( $layout_id = extra_get_home_layout_id() ) {
			$sidebar = get_post_meta( $layout_id, '_extra_sidebar', true );
			$sidebar_location = get_post_meta( $layout_id, '_extra_sidebar_location', true );
		}
	}

	if ( empty( $sidebar_location ) ) {
		if ( $is_woocommerce_sidebar ) {
			$sidebar_location = et_get_option( 'woocommerce_sidebar_location', extra_global_sidebar_location() );
		} else {
			$sidebar_location = extra_global_sidebar_location();
		}
	}

	if ( 'none' === $sidebar_location ) {
		return;
	}

	if ( empty( $sidebar ) ) {
		if ( $is_woocommerce_sidebar ) {
			$sidebar = et_get_option( 'woocommerce_sidebar', extra_global_sidebar() );
		} else {
			$sidebar = extra_global_sidebar();
		}
	}

	return $sidebar;
}
}
if (!function_exists('extra_get_header_vars')) {
function extra_get_header_vars() {
	$items = array();

	$header_items = array(
		'header_social_icons',
		'header_search_field',
		'header_cart_total',
	);

	foreach ( $header_items as $header_item ) {
		$items['show_' . $header_item ] = extra_customizer_el_visible( extra_get_dynamic_selector( $header_item ) );
		$items['output_' . $header_item] = $items['show_' . $header_item ] || is_customize_preview();
	}

	$items['show_header_trending_bar'] = et_get_option( 'show_header_trending', 'on' );
	$items['output_header_trending_bar'] = $items['show_header_trending_bar'] || is_customize_preview();

	$items['header_search_field_alone'] = false;

	$items['header_cart_total_alone'] = false;

	$items['secondary_nav'] = wp_nav_menu( array(
		'theme_location' => 'secondary-menu',
		'container'      => '',
		'fallback_cb'    => '',
		'menu_class'     => 'nav',
		'menu_id'        => 'et-secondary-menu',
		'echo'           => false,
	) );

	$trending_posts = new WP_Query( apply_filters( 'extra_trending_posts_query', array(
		'post_type'      => 'post',
		'post_status'    => 'publish',
		'posts_per_page' => '3',
		'orderby'        => 'comment_count',
		'order'          => 'DESC',
	) ) );
	$items['trending_posts'] = isset( $trending_posts->posts ) ? $trending_posts : false;

	$items['top_info_defined'] = false;

	$top_info_items = array(
		'show_header_social_icons',
		'secondary_nav',
		'show_header_trending_bar',
		'show_header_search_field',
		'show_header_cart_total',
	);

	$top_info_items_count = 0;
	foreach ( $top_info_items as $top_info_item ) {
		if ( !empty( $items[ $top_info_item ] ) ) {
			$top_info_items_count++;
			$items['top_info_defined'] = true;
		}
	}

	if ( 1 == $top_info_items_count ) {
		if ( !empty( $items['show_header_search_field'] ) ) {
			$items['header_search_field_alone'] = true;
			$items['show_header_search_field'] = false;
		}

		if ( !empty( $items['show_header_cart_total'] ) ) {
			$items['header_cart_total_alone'] = true;
			$items['show_header_cart_total'] = false;
		}

		if ( $items['header_search_field_alone'] || $items['header_cart_total_alone'] ) {
			$items['top_info_defined'] = false;
			add_filter( 'wp_nav_menu_items', 'extra_primary_nav_extended_items', 10, 2 );
		}
	} elseif ( is_customize_preview() ) {
		add_filter( 'wp_nav_menu_items', 'extra_primary_nav_extended_items', 10, 2 );
	}

	$items['header_style'] = et_get_option( 'header_style', 'left-right' );

	$items['header_ad'] = extra_display_ad( 'header', false );

	$header_classes = array();

	if ( !empty( $items['header_style'] ) && 'centered' == $items['header_style'] ) {
		$header_classes[] = 'centered';
	} else {
		$header_classes[] = 'left-right';
	}

	if ( !empty( $header_ad ) ) {
		$header_classes[] = 'has_headerad';
	}

	$items['header_classes'] = extra_classes( $header_classes, 'header', false );

	return $items;
}
}
if (!function_exists('extra_primary_nav_extended_items')) {
function extra_primary_nav_extended_items( $items, $args ) {
	if ( 'primary-menu' === $args->theme_location ) {

		if ( is_customize_preview() || !empty( $args->header_search_field_alone ) ) {
			$show_search_on_primary_nav = !empty( $args->header_search_field_alone ) ? true : false;
			$items .= sprintf(
				'<li class="menu-item et-top-search-primary-menu-item" style="%s"><span id="et-search-icon" class="search-icon"></span><div class="et-top-search">%s</div></li>',
				extra_visible_display_css( $show_search_on_primary_nav, false ),
				extra_header_search_field( false )
			);
		}

		if ( is_customize_preview() || !empty( $args->header_cart_total_alone ) ) {
			$show_cart_on_primary_nav = !empty( $args->header_cart_total_alone ) ? true : false;
			$items .= sprintf(
				'<li class="menu-item et-cart-info-primary-menu-item" style="%s">%s</li>',
				extra_visible_display_css( $show_cart_on_primary_nav, false ),
				extra_header_cart_total( true, false )
			);
		}
	}

	return $items;
}
}
if (!function_exists('extra_header_search_field')) {
function extra_header_search_field( $echo = true ) {
	$output = sprintf(
		'<form role="search" class="et-search-form" method="get" action="%1$s">
			<input type="search" class="et-search-field" placeholder="%2$s" value="%3$s" name="s" title="%4$s" />
			<button class="et-search-submit"></button>
		</form>',
		esc_url( home_url( '/' ) ),
		esc_attr_x( 'Search', 'placeholder', 'extra' ),
		get_search_query(),
		esc_attr_x( 'Search for:', 'label', 'extra' )
	);

	if ( $echo ) {
		echo $output;
	} else {
		return $output;
	}
}
}
if (!function_exists('extra_header_cart_total')) {
function extra_header_cart_total( $no_text = false, $echo = true ) {
	ob_start();
	et_show_cart_total( array(
		'no_text' => $no_text,
	) );
	$output = ob_get_clean();

	if ( $echo ) {
		echo $output;
	} else {
		return $output;
	}
}
}
if (!function_exists('extra_display_stars')) {
function extra_display_stars( $score ) {
	$output = '';
	for ( $x = 0; $x < floor( $score ); $x++ ) {
		$output .= '<span class="rating-star"></span>';
	}

	if ( $score != floor( $score ) ) {
		$output .= '<span class="rating-star rating-star-half"></span>';
	}

	$leftover = 5 - floor( $score );
	if ( $leftover > 1 ) {
		for ( $x = 1; $x < $leftover; $x++ ) {
			$output .= '<span class="rating-star rating-star-empty"></span>';
		}
	}

	return $output;
}
}
if (!function_exists('extra_get_post_author_link')) {
function extra_get_post_author_link( $post_id = 0 ) {
	$post_id = empty( $post_id ) ? get_the_ID() : $post_id;
	$post_author_id = get_post( $post_id )->post_author;
	$author = get_user_by( 'id', $post_author_id );
	$link = sprintf(
		'<a href="%1$s" class="url fn" title="%2$s" rel="author">%3$s</a>',
		esc_url( get_author_posts_url( $author->ID, $author->user_nicename ) ),
		esc_attr( sprintf( __( 'Posts by %s' ), $author->display_name ) ),
		esc_html( $author->display_name )
	);
	return $link;
}
}
if (!function_exists('extra_get_author_contact_methods')) {
function extra_get_author_contact_methods( $user_id = 0 ) {
	$user_id = !empty( $user_id ) ? $user_id : get_the_author_meta( 'ID' );
	$author = get_userdata( $user_id );

	$methods = array();
	foreach ( wp_get_user_contact_methods( $author ) as $name => $desc ) {
		if ( !empty( $author->$name ) ) {
			$methods[$name] = array(
				'slug' => $name,
				'name' => $desc,
				'url'  => $author->$name,
			);
		}
	}

	return $methods;
}
}
if (!function_exists('extra_post_share_links')) {
function extra_post_share_links( $echo = true ) {
	$output = '';
	$networks = array();

	// this is backwards due to how epanel saves checkboxes values
	$excluded_networks = et_get_option( "extra_post_share_icons" );
	$excluded_networks = !empty( $excluded_networks ) ? $excluded_networks : array();
	foreach ( ET_Social_Share::get_networks() as $network ) {
		if ( !in_array( $network->slug, $excluded_networks ) ) {
			$networks[$network->slug] = $network;
		}
	}

	$permalink = get_the_permalink();
	$title = get_the_title();
	foreach ( $networks as $network ) {
		$share_url = $network->create_share_url( $permalink, $title );
		$share_title = sprintf( __( 'Share "%s" via %s', 'extra' ), $title, $network->name );

		$output .= sprintf(
			'<a href="%1$s" class="social-share-link" title="%2$s" data-network-name="%3$s" data-share-title="%4$s" data-share-url="%5$s">
				<span class="et-extra-icon et-extra-icon-%3$s et-extra-icon-background-hover" ></span>
			</a>',
			$share_url,
			esc_attr( $share_title ),
			esc_attr( $network->slug ),
			esc_attr( $title ),
			esc_attr( $permalink )
		);
		?>

		<?php
	}

	if ( $echo ) {
		echo $output;
	} else {
		return $output;
	}
}
}
if (!function_exists('extra_is_builder_built')) {
function extra_is_builder_built( $post_id = 0 ) {
	$post_id = empty( $post_id ) ? get_the_ID() : $post_id;
	return (bool) 'on' == get_post_meta( $post_id, '_et_builder_use_builder', true );
}
}
if (!function_exists('extra_get_timeline_menu_month_groups')) {
function extra_get_timeline_menu_month_groups() {
	global $wpdb;

	$month_groups = $wpdb->get_col( "SELECT DISTINCT DATE_FORMAT( {$wpdb->posts}.post_date, '%M-%Y' ) as date_slug FROM {$wpdb->posts} WHERE {$wpdb->posts}.post_type = 'post' AND {$wpdb->posts}.post_status = 'publish'  ORDER BY {$wpdb->posts}.post_date desc" );
	return $month_groups;
}
}
if (!function_exists('extra_get_timeline_posts')) {
function extra_get_timeline_posts( $args = array() ) {
	$default_args = array(
		'date_query'          => array(
			array(
				'after'     => array(
					'month' => date( 'm', strtotime( '12 months ago' ) ),
					'year'  => date( 'Y', strtotime( '12 months ago' ) ),
					'day'   => 1,
				),
				'inclusive' => true,
			),
		),
		'nopaging'            => true,
		'posts_per_page'      => -1,
		'post_type'           => 'post',
		'orderby'             => 'date',
		'order'               => 'desc',
		'post_status'         => 'publish',
		'ignore_sticky_posts' => true,
	);

	$args = wp_parse_args( $args, $default_args );

	$posts = new WP_Query( $args );

	return $posts;
}
}
if (!function_exists('extra_get_timeline_posts_onload')) {
function extra_get_timeline_posts_onload() {
	// Get all posts published in the last year
	$timeline_posts = extra_get_timeline_posts();

	// Some sites don't publish posts in the last year
	if ( ! $timeline_posts->have_posts() ) {
		// Get last published post
		$last_post = get_posts( array( 'posts_per_page' => 1, 'post_status' => 'publish' ) );

		// If there's any post ever, override WP_Query object made earlier
		if ( isset( $last_post[0] ) ) {
			$post_date           = $last_post[0]->post_date;
			$post_date_timestamp = strtotime( $post_date );
			$args                = array(
				'date_query' => array(
					array(
						'after'     => array(
							'month' => '1',
							'year'  => date( 'Y', $post_date_timestamp ),
						),
						'inclusive' => true,
					),
				),
			);

			// Get all posts published in a year where last published post found
			$timeline_posts = extra_get_timeline_posts( $args );
		}
	}

	return $timeline_posts;
}
}
if (!function_exists('extra_timeline_get_content')) {
function extra_timeline_get_content() {
	if ( !isset( $_POST['timeline_nonce'] ) || !wp_verify_nonce( $_POST['timeline_nonce'], 'timeline_nonce' ) ) {
		die( -1 );
	}

	$last_month = sanitize_text_field( $_POST['last_month'] );
	$last_year = sanitize_text_field( $_POST['last_year'] );

	$before_date = strtotime( $last_month . ' '. $last_year );

	if ( isset( $_POST['through_month'] ) && isset( $_POST['through_year'] ) ) {
		$through_month = sanitize_text_field( $_POST['through_month'] );
		$through_year = sanitize_text_field( $_POST['through_year'] );
		$after_date = strtotime( $through_month . ' 1 ' . $through_year );
	} else if ( isset( $_POST['through_year'] ) ) {
		$through_year = sanitize_text_field( $_POST['through_year'] );
		$after_date = strtotime( 'January 1 ' . $through_year );
	} else {
		$after_date = strtotime( date( 'M d Y', $before_date ) . ' - 6 months' );
	}

	$args = array(
		'date_query' => array(
			array(
				'before'    => array(
					'month' => date( 'm', $before_date ),
					'year'  => date( 'Y', $before_date ),
					'day'   => 1,
				),
				'after'     => array(
					'month' => date( 'm', $after_date ),
					'year'  => date( 'Y', $after_date ),
					'day'   => 1,
				),
				'inclusive' => true,
			),
		),
	);

	$timeline_posts = extra_get_timeline_posts( $args );

	if ( $timeline_posts->have_posts() ) {
		require locate_template( 'timeline-posts-content.php' );
	}

	die();
}
}
if (!function_exists('extra_blog_feed_get_content')) {
function extra_blog_feed_get_content() {
	if ( !isset( $_POST['blog_feed_nonce'] ) || !wp_verify_nonce( $_POST['blog_feed_nonce'], 'blog_feed_nonce' ) ) {
		die( -1 );
	}

	$to_page = absint( $_POST['to_page'] );
	$order = sanitize_text_field( $_POST['order'] );
	$orderby = sanitize_text_field( $_POST['orderby'] );
	$posts_per_page = absint( $_POST['posts_per_page'] );

	$show_featured_image = sanitize_text_field( $_POST['show_featured_image'] );
	$blog_feed_module_type = sanitize_text_field( $_POST['blog_feed_module_type'] );

	$show_author = sanitize_text_field( $_POST['show_author'] );
	$show_date = sanitize_text_field( $_POST['show_date'] );
	$date_format = sanitize_text_field( $_POST['date_format'] );
	$show_categories = sanitize_text_field( $_POST['show_categories'] );
	$categories = !empty( $_POST['categories'] ) ? array_map( 'absint', explode( ',', $_POST['categories'] ) ) : '';
	$order = sanitize_text_field( $_POST['order'] );
	$content_length = sanitize_text_field( $_POST['content_length'] );
	$hover_overlay_icon = et_sanitize_font_icon( $_POST['hover_overlay_icon'] );
	$show_more = sanitize_text_field( $_POST['show_more'] );
	$show_comments = sanitize_text_field( $_POST['show_comments'] );
	$show_rating = sanitize_text_field( $_POST['show_rating'] );
	$use_tax_query = sanitize_text_field( $_POST['use_tax_query'] );
	$tax_query = isset( $_POST['tax_query'] ) ? $_POST['tax_query'] : array();

	// This is normally set in includes/builder/shortcodes.php in et_pb_column(),
	// but since this is an ajax request, we need to pass this data along
	global $et_column_type;
	$et_column_type = sanitize_text_field( $_POST['et_column_type'] );

	$offset = ( $to_page * $posts_per_page ) - $posts_per_page;
	$post_status = array( 'publish' );

	if ( is_user_logged_in() && current_user_can( 'read_private_posts' ) ) {
		$post_status[] = 'private';
	}

	$args = array(
		'post_type'      => 'post',
		'posts_per_page' => $posts_per_page,
		'offset'         => $offset,
		'order'          => $order,
		'orderby'        => $orderby,
		'post_status'    => $post_status,
	);

	if ( !empty( $categories ) ) {
		$args['tax_query'] = array(
			array(
				'taxonomy' => 'category',
				'field'    => 'id',
				'terms'    => $categories,
				'operator' => 'IN',
			),
		);
	}

	if ( $use_tax_query === '1' && ! empty( $tax_query ) ) {
		$valid_taxonomies = get_taxonomies();
		$valid_fields     = array( 'term_id', 'name', 'slug', 'term_taxonomy_id' );
		$valid_operators  = array( 'IN', 'NOT IN', 'AND', 'EXISTS', 'NOT EXISTS' );
		$sanitized_terms  = array();

		foreach ( $tax_query as $taxonomy ) {
			if ( isset( $taxonomy['taxonomy'] ) && 'category' === $taxonomy['taxonomy'] ) {
				continue;
			}

			if ( ! isset( $taxonomy['taxonomy'] ) || ! in_array( $taxonomy['taxonomy'], $valid_taxonomies ) ) {
				continue;
			}

			if ( ! isset( $taxonomy['field'] ) || ! in_array( $taxonomy['field'], $valid_fields ) ) {
				continue;
			}

			if ( ! isset( $taxonomy['operator'] ) || ! in_array( $taxonomy['operator'], $valid_operators ) ) {
				continue;
			}

			if ( ! isset( $taxonomy['terms'] ) || ! is_array( $taxonomy['terms']) || empty( $taxonomy['terms'] ) ) {
				continue;
			}

			foreach ( $taxonomy['terms'] as $taxonomy_term ) {
				$sanitized_terms[] = sanitize_text_field( $taxonomy_term );
			}

			$args['tax_query'][] = array(
				'taxonomy' => sanitize_text_field( $taxonomy['taxonomy'] ),
				'field'    => sanitize_text_field( $taxonomy['field'] ),
				'terms'    => $sanitized_terms,
				'operator' => sanitize_text_field( $taxonomy['operator'] ),
			);
		}

		if ( 1 < count( $args['tax_query'] ) ) {
			$args['tax_query']['relation'] = 'AND';
		}
	}

	$module_posts = new WP_Query( $args );

	$page = $to_page;
	if ( $module_posts->have_posts() ) {
		require (($blogFeedLoopTemplate = locate_template('module-posts-blog-feed-loop.php')) ? $blogFeedLoopTemplate : dirname(__FILE__).'/templates/module-posts-blog-feed-loop.php');
	}

	die();
}
}
if (!function_exists('extra_get_portfolio_projects')) {
function extra_get_portfolio_projects( $args = array() ) {
	$default_args = array(
		'post_type'      => EXTRA_PROJECT_POST_TYPE,
		'nopaging'       => true,
		'posts_per_page' => -1,
	);

	$args = wp_parse_args( $args, $default_args );

	$portfolio_options = extra_get_portfolio_options();
	if ( !empty( $portfolio_options['project_categories'] ) ) {
		$term_ids = array();
		foreach ( $portfolio_options['project_categories'] as $category ) {
			$term_ids[] = $category->term_id;
		}
	}

	if ( !empty( $term_ids ) ) {
		$args['tax_query'] = array(
			array(
				'taxonomy' => EXTRA_PROJECT_CATEGORY_TAX,
				'field'    => 'id',
				'terms'    => $term_ids,
				'operator' => 'IN',
			),
		);
	}

	$projects = new WP_Query( $args );

	return $projects;
}
}
if (!function_exists('extra_get_portfolio_options')) {
function extra_get_portfolio_options( $post_id = 0 ) {
	$post_id = empty( $post_id ) ? get_the_ID() : $post_id;
	$options = array();

	$project_categories = get_post_meta( $post_id, '_portfolio_project_categories', true );

	$args = array(
		'include' => $project_categories,
	);

	$options['project_categories'] = get_terms( EXTRA_PROJECT_CATEGORY_TAX, $args );

	$options['hide_title'] = get_post_meta( $post_id, '_portfolio_hide_title', true );
	$options['hide_categories'] = get_post_meta( $post_id, '_portfolio_hide_categories', true );

	return $options;
}
}
if (!function_exists('extra_get_portfolio_project_category_classes')) {
function extra_get_portfolio_project_category_classes() {
	$categories = extra_get_the_project_categories();

	$classes = "";

	if ( !empty( $categories ) ) {
		$classes_array = array();

		foreach ( $categories as $category ) {
			$classes_array[] = 'project_category_' . $category->slug;
		}

		$classes = implode( ' ', $classes_array );

	}

	return $classes;
}
}
if (!function_exists('extra_get_authors_page_options')) {
function extra_get_authors_page_options( $post_id = 0 ) {
	$post_id = empty( $post_id ) ? get_the_ID() : $post_id;
	$options = array();

	$authors = get_post_meta( $post_id, '_authors_page_authors', true );

	$authors_all = get_post_meta( $post_id, '_authors_page_authors_all', true );

	$query_args = array(
		'who'     => 'authors',
		'order'   => 'ASC',
		'orderby' => 'display_name',
		'include' => !empty( $authors_all ) ? array() : $authors,
	);

	$options['authors'] = get_users( $query_args );

	return $options;
}
}
if (!function_exists('extra_get_blog_feed_page_options')) {
function extra_get_blog_feed_page_options( $post_id = 0 ) {
	$post_id = empty( $post_id ) ? get_the_ID() : $post_id;
	$options = array();

	$options_fields = array(
		'display_style',
		'categories',
		'posts_per_page',
		'order',
		'orderby',
		'show_author',
		'show_categories',
		'show_ratings',
		'show_featured_image',
		'content_length',
		'show_date',
		'date_format',
		'show_comment_count',
	);

	foreach ( $options_fields as $options_field ) {
		$options[$options_field] = get_post_meta( $post_id, '_blog_feed_page_' . $options_field, true );
	}

	$options['border_color_style'] = '';

	$args = array(
		'post_type'      => 'post',
		'posts_per_page' => isset( $options['posts_per_page'] ) && is_numeric( $options['posts_per_page'] ) ? $options['posts_per_page'] : 5,
		'order'          => $options['order'],
		'orderby'        => $options['orderby'],
	);

	$paged = is_front_page() ? get_query_var( 'page' ) : get_query_var( 'paged' );
	$args['paged'] = $paged;

	if ( 'rating' == $options['orderby'] ) {
		$args['orderby'] = 'meta_value_num';
		$args['meta_key'] = '_extra_rating_average';
	}

	if ( !empty( $options['categories'] ) ) {
		$args['tax_query'] = array(
			array(
				'taxonomy' => 'category',
				'field'    => 'id',
				'terms'    => array_map( 'absint', explode( ',', $options['categories'] ) ),
				'operator' => 'IN',
			),
		);

		if ( 'standard' == $options['display_style'] && false === strpos( $options['categories'], ',' ) ) {
			$color = extra_get_category_color( $options['categories'] );
			$options['border_color_style'] = esc_attr( sprintf( 'border-color:%s;', $color ) );
			$options['category_color'] = $color;
		}
	}

	// Automatically add sgiw comment count's default value to empty variable for backward compatibility
	if ( '' === $options['show_comment_count'] ) {
		$options['show_comment_count'] = '1';
	}

	wp_reset_postdata();

	$options['posts_query'] = new WP_Query( $args );

	return $options;
}
}
if (!function_exists('extra_get_sitemap_page_options')) {
function extra_get_sitemap_page_options( $post_id = 0 ) {
	$post_id = empty( $post_id ) ? get_the_ID() : $post_id;
	$options = array();

	$checked_sections = get_post_meta( $post_id, '_sitemap_page_sections', true );

	if ( !empty( $checked_sections ) ) {
		$checked_sections = explode( ',', $checked_sections );
	} else {
		$checked_sections = array();
	}

	$sections = array(
		'pages'        => esc_html__( 'Pages', 'extra' ),
		'categories'   => esc_html__( 'Categories', 'extra' ),
		'tags'         => esc_html__( 'Tags', 'extra' ),
		'recent_posts' => esc_html__( 'Recent Posts', 'extra' ),
		'archives'     => esc_html__( 'Archives', 'extra' ),
		'authors'      => esc_html__( 'Authors', 'extra' ),
	);

	$options['sections'] = array();
	foreach ( $checked_sections as $checked_section ) {
		$options['sections'][$checked_section] = $sections[$checked_section];
	}

	$page_section_option_keys = array(
		'pages_exclude',
		'categories_count',
		'authors_include',
		'archives_limit',
		'archives_count',
		'recent_posts_limit',
	);

	$options['page_section_options'] = array();
	foreach ( $page_section_option_keys as $page_section_option_key ) {
		$options['page_section_options'][ $page_section_option_key ] = get_post_meta( $post_id, '_sitemap_page_' . $page_section_option_key, true );
	}

	return $options;
}
}
if (!function_exists('extra_get_sitemap_page_section_items')) {
function extra_get_sitemap_page_section_items( $section, $options ) {
	$items = '';
	switch( $section ) {
		case 'pages':
			$sortby = 'menu_order, post_title';
			$exclude = !empty( $options['pages_exclude'] ) ? $options['pages_exclude'] : '';
			$items = wp_list_pages( array(
				'title_li'    => '',
				'echo'        => 0,
				'sort_column' => $sortby,
				'exclude'     => $exclude,
			));
			break;
		case 'categories':
			$count = !empty( $options['categories_count'] ) ? $options['categories_count'] : '0';
			$items = wp_list_categories(array(
				'show_count'   => $count,
				'hierarchical' => '1',
				'echo'         => '0',
				'title_li'     => false,
			));
			break;
		case 'tags':
			$tags = get_terms( 'post_tag', array(
				'orderby' => 'count',
				'order'   => 'DESC',
			));
			foreach ( $tags as $tag ) {

				$link = get_term_link( intval( $tag->term_id ), $tag->taxonomy );
				$name = $tag->name;

				$items .= '<li><a href="' . esc_url( $link ) .'">' . esc_html( $name ) . '</a></li>';
			}
			break;
		case 'authors':

			$authors = !empty( $options['authors_include'] ) ? $options['authors_include'] : '';

			$query_args = array(
				'who'     => 'authors',
				'order'   => 'ASC',
				'orderby' => 'display_name',
				'include' => $authors,
			);

			$authors = get_users( $query_args );

			foreach ( $authors as $author ) {
				$items .= sprintf(
					'<li><a href="%s" title="%s" rel="author">%s</a></li>',
					esc_url( get_author_posts_url( $author->ID ) ),
					esc_attr( sprintf( __( 'Posts By: %s', 'extra' ), $author->display_name ) ),
					esc_html( $author->display_name )
				);
			}
			break;
		case 'archives':
			$count = !empty( $options['archives_count'] ) ? $options['archives_count'] : '0';
			$limit = !empty( $options['archives_limit'] ) ? $options['archives_limit'] : '12';
			$items = wp_get_archives( array(
				'type'            => 'monthly',
				'show_post_count' => $count,
				'limit'           => $limit,
				'echo'            => '0',
			) );
			break;
		case 'recent_posts':
			$posts_per_page = !empty( $options['recent_posts_limit'] ) ? $options['recent_posts_limit'] : 10;
			$args = array(
				'post_type'      => 'post',
				'status'         => 'publish',
				'posts_per_page' => $posts_per_page,
				'order'          => 'date',
				'orderby'        => 'DESC',
			);

			$posts = new WP_Query( $args );

			foreach ( $posts->posts as $post ) {
				$items .= sprintf(
					'<li><a href="%s" title="%s">%s</a></li>',
					esc_url( get_the_permalink( $post->ID ) ),
					esc_attr( get_the_title( $post->ID ) ),
					esc_html( get_the_title( $post->ID ) )
				);
			}

			wp_reset_postdata();

			break;
	}

	return $items;
}
}
if (!function_exists('extra_accent_color')) {
function extra_accent_color() {
	echo esc_attr( extra_global_accent_color() );
}
}
if (!function_exists('extra_contact_form_submit')) {
function extra_contact_form_submit() {
	$error = false;

	if ( !isset( $_POST['action'] ) || 'extra_contact_form_submit' != $_POST['action'] ) {
		return array();
	}

	if ( !isset( $_POST['nonce_extra_contact_form'] ) || !wp_verify_nonce( $_POST['nonce_extra_contact_form'], 'extra-contact-form' ) ) {
		$message = array(
			'message' => esc_html__( 'Form submission error, please refresh and try again.', 'extra' ),
			'type'    => 'error',
		);
		$error = true;
	}

	if ( empty( $_POST['contact_name'] ) ) {
		$message = array(
			'message' => esc_html__( 'Name field cannot be empty.', 'extra' ),
			'type'    => 'error',
		);
		$error = true;
	}

	if ( empty( $_POST['contact_email'] ) ) {
		$message = array(
			'message' => esc_html__( 'Email field cannot be empty.', 'extra' ),
			'type'    => 'error',
		);
		$error = true;
	}

	if ( ! is_email( $_POST['contact_email'] ) ) {
		$message = array(
			'message' => esc_html__( 'Please enter a valid email address.', 'extra' ),
			'type'    => 'error',
		);
		$error = true;
	}

	if ( !$error ) {
		$contact_page_options = extra_get_contact_page_options();

		$name = stripslashes( sanitize_text_field( $_POST['contact_name'] ) );
		$email = sanitize_email( $_POST['contact_email'] );

		$email_to = !empty( $contact_page_options['email'] ) ? $contact_page_options['email'] : get_site_option( 'admin_email' );

		$email_to = apply_filters( 'extra_contact_page_email_to', $email_to );

		$email_to = sanitize_email( $email_to );

		$subject = sprintf(
			__( 'New Message From %1$s%2$s: %3$s', 'extra' ),
			sanitize_text_field( get_option( 'blogname' ) ),
			( '' !== $contact_page_options['title'] ? sprintf( esc_html_x( ' - %s', 'contact form title separator', 'extra' ), sanitize_text_field( $contact_page_options['title'] ) ) : '' ),
			sanitize_text_field( $_POST['contact_subject'] )
		);

		$message = stripslashes( wp_strip_all_tags( $_POST['contact_message'] ) );

		$headers  = 'From: ' . $name . ' <' . $email . '>' . "\r\n";
		$headers .= 'Reply-To: ' . $name . ' <' . $email . '>';
		apply_filters( 'et_contact_page_headers', $headers, $name, $email );

		wp_mail( $email_to, $subject, $message, $headers );

		$message = array(
			'message' => esc_html__( 'Thanks for contacting us.', 'extra' ),
			'type'    => 'success',
		);
	}

	if ( empty( $message ) ) {
		$message = array(
			'message' => esc_html__( 'There was a problem, please try again.', 'extra' ),
			'type'    => 'error',
		);
	}

	return $message;
}
}
if (!function_exists('extra_contact_form_submit_ajax')) {
function extra_contact_form_submit_ajax(){
	$message = extra_contact_form_submit();
	exit( json_encode( $message ) );
}
}
if (!function_exists('extra_get_contact_page_options')) {
function extra_get_contact_page_options( $post_id = 0 ) {
	$post_id = empty( $post_id ) ? get_the_ID() : $post_id;
	$options = array();

	$options['title'] = get_post_meta( $post_id, '_contact_form_title', true );
	$options['email'] = get_post_meta( $post_id, '_contact_form_email', true );
	$options['map_zoom'] = get_post_meta( $post_id, '_contact_form_map_zoom', true );
	$options['map_lat'] = get_post_meta( $post_id, '_contact_form_map_address_lat', true );
	$options['map_lng'] = get_post_meta( $post_id, '_contact_form_map_address_lng', true );

	return $options;
}
}
if (!function_exists('extra_ajax_loader_img')) {
function extra_ajax_loader_img( $echo = true ) {
	$img = '<img src="' . esc_url( get_template_directory_uri() ) .'/images/pagination-loading.gif" alt="' . esc_attr__( "Loading", "extra" ) . '" />';
	if ( $echo ) {
		echo $img;
	} else {
		return $img;
	}
}
}
if (!function_exists('extra_get_video_embed')) {
function extra_get_video_embed( $video_urls ) {
	require_once ABSPATH . WPINC . '/class-oembed.php';

	$video_sources = '';
	$video_urls    = explode( ',', $video_urls );
	$local_video   = array();
	$video_index   = 0;

	if ( ! empty( $video_urls ) ) {
		foreach ( $video_urls as $video_url ) {
			$video_index++;

			$video_url = esc_url( $video_url );

			$oembed_args = array(
				'discover' => false,
			);

			$oembed = _wp_oembed_get_object();

			$provider = $oembed->get_provider( $video_url, $oembed_args );

			if ( ! empty( $provider ) ) {
				if ( ! is_singular() && $video_index > 1 ) {
					continue;
				}

				$video_sources .= $oembed->get_html( $video_url, $oembed_args );
			} else {
				$type = wp_check_filetype( $video_url, wp_get_mime_types() );

				if ( !empty( $type['type'] ) ) {
					$local_video[] = sprintf( '<source type="%s" src="%s" />',
						esc_attr( $type['type'] ),
						esc_attr( $video_url )
					);
				}
			}
		}

		if ( ! empty( $local_video ) ) {
			$video_sources = sprintf( '<video controls>%s</video>',
				implode( '', $local_video )
			);

			wp_enqueue_style( 'wp-mediaelement' );
			wp_enqueue_script( 'wp-mediaelement' );
		}
	}

	return $video_sources;
}
}
if (!function_exists('et_get_post_meta_settings')) {
function et_get_post_meta_settings( $type = 'all' ) {
	$default_options = array(
		'author',
		'date',
		'categories',
		'comments',
		'rating_stars',
	);

	switch ( $type ) {
		case 'all':
			$post_meta_options = et_get_option( 'extra_postinfo1', $default_options );
			break;

		case 'post':
			$post_meta_options = et_get_option( 'extra_postinfo2', $default_options );
			break;

		default:
			$post_meta_options = $default_options;
			break;
	}

	$meta_args = array(
		'author_link'    => in_array( 'author', $post_meta_options ),
		'author_link_by' => et_get_safe_localization( __( 'Posted by %s', 'extra' ) ),
		'post_date'      => in_array( 'date', $post_meta_options ),
		'categories'     => in_array( 'categories', $post_meta_options ),
		'comment_count'  => in_array( 'comments', $post_meta_options ),
		'rating_stars'   => in_array( 'rating_stars', $post_meta_options ),
	);

	return apply_filters( 'et_get_post_meta_settings', $meta_args );
}
}
if (!function_exists('et_get_post_meta_setting')) {
function et_get_post_meta_setting( $type = 'all', $option = 'author_link' ) {
	$settings = et_get_post_meta_settings( $type );

	$setting = isset( $settings[ $option ] ) ? $settings[ $option ] : true;

	return apply_filters( 'et_get_post_meta_setting_' . $option, $setting );
}
}
if (!function_exists('extra_display_archive_post_meta')) {
function extra_display_archive_post_meta() {
	$post_meta_options = et_get_option( 'extra_postinfo1', array(
		'author',
		'date',
		'categories',
		'comments',
		'rating_stars',
	) );

	$meta_args = array(
		'author_link'    => in_array( 'author', $post_meta_options ),
		'author_link_by' => et_get_safe_localization( __( 'Posted by %s', 'extra' ) ),
		'post_date'      => in_array( 'date', $post_meta_options ),
		'categories'     => in_array( 'categories', $post_meta_options ),
		'comment_count'  => in_array( 'comments', $post_meta_options ),
		'rating_stars'   => in_array( 'rating_stars', $post_meta_options ),
	);

	return et_extra_display_post_meta( $meta_args );
}
}
if (!function_exists('extra_display_single_post_meta')) {
function extra_display_single_post_meta() {
	$post_meta_options = et_get_option( 'extra_postinfo2', array(
		'author',
		'date',
		'categories',
		'comments',
		'rating_stars',
	) );

	$meta_args = array(
		'author_link'    => in_array( 'author', $post_meta_options ),
		'author_link_by' => et_get_safe_localization( __( 'Posted by %s', 'extra' ) ),
		'post_date'      => in_array( 'date', $post_meta_options ),
		'categories'     => in_array( 'categories', $post_meta_options ),
		'comment_count'  => in_array( 'comments', $post_meta_options ),
		'rating_stars'   => in_array( 'rating_stars', $post_meta_options ),
	);

	return et_extra_display_post_meta( $meta_args );
}
}
if (!function_exists('et_extra_display_post_meta')) {
function et_extra_display_post_meta( $args = array() ) {
	$default_args = array(
		'post_id'        => get_the_ID(),
		'author_link'    => true,
		'author_link_by' => et_get_safe_localization( __( 'by %s', 'extra' ) ),
		'post_date'      => true,
		'date_format'    => et_get_option( 'extra_date_format', '' ),
		'categories'     => true,
		'comment_count'  => true,
		'rating_stars'   => true,
	);

	$args = wp_parse_args( $args, $default_args );

	$meta_pieces = array();

	if ( $args['author_link'] ) {
		$meta_pieces[] = sprintf( $args['author_link_by'], extra_get_post_author_link( $args['post_id'] ) );
	}

	if ( $args['post_date'] ) {
		$meta_pieces[] = extra_get_the_post_date( $args['post_id'], $args['date_format'] );
	}

	if ( $args['categories'] ) {
		$meta_piece_categories = extra_get_the_post_categories( $args['post_id'] );
		if ( !empty( $meta_piece_categories ) ) {
			$meta_pieces[] = $meta_piece_categories;
		}
	}

	if ( $args['comment_count'] ) {
		$meta_piece_comments = extra_get_the_post_comments_link( $args['post_id'] );
		if ( !empty( $meta_piece_comments ) ) {
			$meta_pieces[] = $meta_piece_comments;
		}
	}

	if ( $args['rating_stars'] && extra_is_post_rating_enabled( $args['post_id'] ) ) {
		$meta_piece_rating_stars = extra_get_post_rating_stars( $args['post_id'] );
		if ( !empty( $meta_piece_rating_stars ) ) {
			$meta_pieces[] = $meta_piece_rating_stars;
		}
	}

	$output = implode( ' | ', $meta_pieces );

	return $output;
}
}
if (!function_exists('extra_get_de_buildered_content')) {
function extra_get_de_buildered_content() {
	$content = get_the_content( '' );
	$content = apply_filters( 'the_content', $content );
	$content = wp_strip_all_tags( $content, false );
	$content = wpautop( $content );

	return $content;
}
}
if (!function_exists('extra_get_category_color')) {
function extra_get_category_color( $term_id ) {
	return et_get_childmost_taxonomy_meta( $term_id, 'color', true, extra_global_accent_color() );
}
}
if (!function_exists('extra_get_post_category_color')) {
function extra_get_post_category_color( $post_id = 0 ) {
	$post_id = empty( $post_id ) ? get_the_ID() : $post_id;

	$categories = wp_get_post_categories( $post_id );

	$color = '';
	if ( !empty( $categories ) ) {
		$first_category_id = $categories[0];
		if ( function_exists( 'et_get_childmost_taxonomy_meta' ) ) {
			$color = et_get_childmost_taxonomy_meta( $first_category_id, 'color', true, extra_global_accent_color() );
		} else {
			$color = extra_global_accent_color();
		}

	}
	return $color;
}
}
if (!function_exists('extra_get_post_related_posts')) {
function extra_get_post_related_posts() {
	$post_id = get_the_ID();
	$terms = get_the_terms( $post_id, 'category' );

	$term_ids = array();
	if ( is_array( $terms ) ) {
		foreach ( $terms as $term ) {
			$term_ids[] = $term->term_id;
		}
	}

	$related_posts = new WP_Query( array(
		'tax_query'      => array(
			array(
				'taxonomy' => 'category',
				'field'    => 'id',
				'terms'    => $term_ids,
				'operator' => 'IN',
			),
		),
		'post_type'      => 'post',
		'posts_per_page' => '4',
		'orderby'        => 'rand',
		'post__not_in'   => array( $post_id ),
	) );

	if ( $related_posts->have_posts() ) {
		return $related_posts;
	} else {
		return false;
	}
}
}
if (!function_exists('extra_get_column_thumbnail_size')) {
function extra_get_column_thumbnail_size() {
	global $et_column_type;

	if ( is_singular( 'post' ) || is_singular( EXTRA_PROJECT_POST_TYPE ) || is_page() ) {
		$size = 'extra-image-huge';
	} else {
		switch ( $et_column_type ) {
			case '4_4':
				$size = 'extra-image-huge';
				break;
			case '1_2':
				$size = 'extra-image-medium';
				break;
			case '1_3':
				$size = 'extra-image-small';
				break;
			default:
				$size = 'extra-image-huge';
				break;
		}
	}

	return $size;
}
}
if (!function_exists('et_extra_show_home_layout')) {
function et_extra_show_home_layout() {
	return (bool) 'layout' == get_option( 'show_on_front' ) && extra_get_home_layout_id();
}
}
if (!function_exists('extra_archive_pagination')) {
function extra_archive_pagination( $query = '' ) {
	global $wp_query;

	if ( empty( $query ) || !is_a( $query, 'WP_Query' ) ) {
		$query = $wp_query;
	}

	$big = 999999999; // need an unlikely integer
	$base = str_replace( $big, '%#%', esc_url( get_pagenum_link( $big ) ) );
	$total = $query->max_num_pages;
	$current = max( 1, absint( $query->get( 'paged' ) ) );

	if ( $total - $current < 4 ) {
		$end_size = 3;
	} else {
		$end_size = 1;
	}

	if ( $current < 4 ) {
		$beg_size = 3;
	} else {
		$beg_size = 1;
	}

	$args = array(
		'base'      => $base,
		'format'    => '?paged=%#%',
		'total'     => $total,
		'current'   => $current,
		'beg_size'  => $beg_size,
		'end_size'  => $end_size,
		'mid_size'  => 1,
		'prev_text' => '',
		'next_text' => '',
		'type'      => 'list',
	);

	return et_paginate_links( $args );
}
}
if (!function_exists('extra_display_ad')) {
function extra_display_ad( $location, $echo = true ) {
	if ( 'on' == et_get_option( $location . '_ad_enable' ) ) {
		$output = '';

		$adsense = et_get_option( $location . '_ad_adsense' );
		$enable_responsive_ad = et_get_option( $location . '_responsive_adsense_ad_enable' );
		$image = et_get_option( $location . '_ad_image' );
		$url = et_get_option( $location . '_ad_url' );

		if ( !empty( $adsense ) ) {
			$output = $adsense;

			if ( 'on' === $enable_responsive_ad ) {
				$output = '<div class="adsense-responsive-ad">'. $output .'</div>';
			}
		} elseif ( !empty( $image ) && !empty( $url ) ) {
			$output = '<a href="' . esc_url( $url ) . '"><img src="' . esc_url( $image ) . '" alt="' . esc_attr__( "Advertisement", 'extra' ) . '" /></a>';
		}

		if ( $echo ) {
			echo et_core_fix_unclosed_html_tags( $output );
		} else {
			return et_core_fix_unclosed_html_tags( $output );
		}
	}
}
}
if (!function_exists('extra_footer_classes')) {
function extra_footer_classes() {
	echo extra_customizer_selector_classes( '#footer', false );
}
}
if (!function_exists('extra_element_attribute')) {
function extra_element_attribute( $filter, $attribute, $value = array(), $echo = true ) {
	$value = implode( " ", apply_filters( $filter, $value ) );

	if ( "" === $value ) {
		return;
	}

	$output = $attribute . '="' . esc_attr( $value ) .'"';

	if ( $echo ) {
		echo $output;
	} else {
		return $output;
	}
}
}
if (!function_exists('extra_check_feature_availability_in_post_type')) {
function extra_check_feature_availability_in_post_type( $post_type, $feature_name ) {
	switch ( $feature_name ) {
		case 'hide_featured_image_in_single':
			$availability = array( 'post' );
			break;

		case 'hide_title_meta_in_single':
			$availability = array(
				'post',
				'page',
				'project',
			);
			break;

		default:
			$availability = array();
			break;
	}

	return in_array( $post_type, apply_filters( "extra_feature_availability_{$feature_name}", $availability, $feature_name ) );
}
}
if (!function_exists('extra_is_post_author_box')) {
function extra_is_post_author_box() {
	$epanel_show_author = 'on' == et_get_option( 'extra_show_author_box', 'on' );

	/* Return true if the ePanel author box option is enable and not disabled in the
	 * post meta, and vise versa.
	 */
	if ( $epanel_show_author ) {
		if ( true != get_post_meta( get_the_id(), '_extra_hide_author_box', true ) ) {
			return true;
		}
	} else if ( true == get_post_meta( get_the_id(), '_extra_show_author_box', true ) ) {
		return true;
	}

	return false;
}
}
if (!function_exists('extra_is_post_related_posts')) {
function extra_is_post_related_posts() {
	$epanel_show_related_posts = 'on' == et_get_option( 'extra_show_related_posts', 'on' );

	/* Return true if the ePanel related posts option is enable and not disabled in the
	 * post meta, and vise versa.
	 */
	if ( $epanel_show_related_posts ) {
		if ( true != get_post_meta( get_the_id(), '_extra_hide_related_posts', true ) ) {
			return true;
		}
	} else if ( true == get_post_meta( get_the_id(), '_extra_show_related_posts', true ) ) {
		return true;
	}

	return false;
}
}
// phpcs:enable -- end of code from the Extra Theme file includes/template-tags.php (with minimal or no modifications)



// phpcs:disable -- the following code is from the Extra Theme file framework/post-formats.php with minimal or no modifications; it is assumed that all needed security measures are already in place
if (!function_exists('et_register_post_format_taxonomy')) {
function et_register_post_format_taxonomy(){
	global $shortname;

	register_taxonomy( ET_POST_FORMAT, 'post', array(
		'public'            => true,
		'hierarchical'      => false,
		'labels'            => array(
			'name'          => esc_html_x( 'Format', $shortname ),
			'singular_name' => esc_html_x( 'Format', $shortname ),
		),
		'query_var'         => true,
		'rewrite'           => false,
		'show_ui'           => false,
		'show_in_nav_menus' => false,
	) );

	add_post_type_support( 'post', 'et-post-formats' );
}
}
if (!function_exists('et_get_post_format')) {
function et_get_post_format( $post = null ) {
	if ( ! $post = get_post( $post ) ) {
		return false;
	}

	if ( ! post_type_supports( $post->post_type, 'et-post-formats' ) ) {
		return false;
	}

	$_format = get_the_terms( $post->ID, ET_POST_FORMAT );

	if ( empty( $_format ) ) {
		return false;
	}

	$format = array_shift( $_format );

	$post_format_string = str_replace( ET_POST_FORMAT_PREFIX, '', $format->slug );

	$post_format = in_array( $post_format_string, array_keys( et_get_post_format_strings() ) ) ? $post_format_string : false;

	return apply_filters( 'et_get_post_format', $post_format, $post->ID );
}
}
if (!function_exists('et_has_post_format')) {
function et_has_post_format( $format = array(), $post = null ) {
	$prefixed = array();

	if ( $format ) {
		foreach ( (array) $format as $single ) {
			$prefixed[] = ET_POST_FORMAT_PREFIX . sanitize_key( $single );
		}
	}

	return has_term( $prefixed, ET_POST_FORMAT, $post );
}
}
if (!function_exists('et_set_post_format')) {
function et_set_post_format( $post, $format ) {
	$post = get_post( $post );

	if ( empty( $post ) )
		return new WP_Error( 'invalid_post', esc_html__( 'Invalid post' ) );

	if ( ! empty( $format ) ) {
		$format = sanitize_key( $format );
		if ( 'standard' === $format || ! in_array( $format, et_get_post_format_slugs() ) )
			$format = '';
		else
			$format = ET_POST_FORMAT_PREFIX . $format;
	}

	return wp_set_post_terms( $post->ID, $format, ET_POST_FORMAT );
}
}
if (!function_exists('et_get_post_format_strings')) {
function et_get_post_format_strings() {
	$strings = array(
		'standard' => esc_html__( 'Standard', 'extra' ), // Special case. any value that evals to false will be considered standard
		'aside'    => esc_html__( 'Aside', 'extra' ),
		'chat'     => esc_html__( 'Chat', 'extra' ),
		'gallery'  => esc_html__( 'Gallery', 'extra' ),
		'link'     => esc_html__( 'Link', 'extra' ),
		'image'    => esc_html__( 'Image', 'extra' ),
		'quote'    => esc_html__( 'Quote', 'extra' ),
		'status'   => esc_html__( 'Status', 'extra' ),
		'video'    => esc_html__( 'Video', 'extra' ),
		'audio'    => esc_html__( 'Audio', 'extra' ),
		'map'      => esc_html__( 'Map', 'extra' ),
	);

	$strings = apply_filters( 'et_post_formats_strings', $strings );
	return $strings;
}
}
if (!function_exists('et_get_post_format_slugs')) {
function et_get_post_format_slugs() {
	$slugs = array_keys( et_get_post_format_strings() );
	return array_combine( $slugs, $slugs );
}
}
if (!function_exists('et_get_theme_post_format_slugs')) {
function et_get_theme_post_format_slugs() {
	$theme_supported_post_formats = get_theme_support( 'et-post-formats' );

	$post_formats = array_intersect( $theme_supported_post_formats[0], array_keys( et_get_post_format_slugs() ) );

	return array_combine( $post_formats, $post_formats );
}
}
if (!function_exists('et_get_post_format_string')) {
function et_get_post_format_string( $slug ) {
	$strings = et_get_post_format_strings();
	if ( !$slug ) {
		return $strings['standard'];
	} else {
		return ( isset( $strings[$slug] ) ) ? $strings[$slug] : '';
	}
}
}
if (!function_exists('et_post_format_body_class')) {
function et_post_format_body_class( $body_class ) {
	if ( $post_format = et_get_post_format() ) {
		$body_class[] = 'et-post-format';
		$body_class[] = 'et-post-format-' . $post_format;
	}

	return $body_class;
}
}
if (!function_exists('_et_post_format_get_term')) {
function _et_post_format_get_term( $term ) {
	if ( isset( $term->slug ) ) {
		$term->name = et_get_post_format_string( str_replace( ET_POST_FORMAT_PREFIX, '', $term->slug ) );
	}
	return $term;
}
}
if (!function_exists('_et_post_format_get_terms')) {
function _et_post_format_get_terms( $terms, $taxonomies, $args ) {
	if ( in_array( ET_POST_FORMAT, (array) $taxonomies ) ) {
		if ( isset( $args['fields'] ) && 'names' == $args['fields'] ) {
			foreach ( $terms as $order => $name ) {
				$terms[$order] = et_get_post_format_string( str_replace( ET_POST_FORMAT_PREFIX, '', $name ) );
			}
		} else {
			foreach ( (array) $terms as $order => $term ) {
				if ( isset( $term->taxonomy ) && ET_POST_FORMAT == $term->taxonomy ) {
					$terms[$order]->name = et_get_post_format_string( str_replace( ET_POST_FORMAT_PREFIX, '', $term->slug ) );
				}
			}
		}
	}
	return $terms;
}
}
if (!function_exists('_et_post_format_wp_get_object_terms')) {
function _et_post_format_wp_get_object_terms( $terms ) {
	foreach ( (array) $terms as $order => $term ) {
		if ( isset( $term->taxonomy ) && ET_POST_FORMAT == $term->taxonomy ) {
			$terms[$order]->name = et_get_post_format_string( str_replace( ET_POST_FORMAT_PREFIX, '', $term->slug ) );
		}
	}
	return $terms;
}
}
if (!function_exists('et_has_format_content')) {
function et_has_format_content( $post_id = 0 ) {
	if ( empty( $post_id ) ) {
		$post_id = get_the_ID();
	}

	if ( 'post' === get_post_type( $post_id ) ) {
		switch ( et_get_post_format( $post_id ) ) {
			case 'video':
				$meta_key = '_video_format_urls';
				break;
			case 'audio':
				$meta_key = '_audio_format_file_url';
				break;
			case 'quote':
				$meta_key = '_quote_format_quote';
				break;
			case 'gallery':
				$meta_key = '_gallery_format_attachment_ids';
				break;
			case 'link':
				$meta_key = '_link_format_link_url';
				break;
			case 'map':
				$meta_key = '_map_format_lat';
				break;
			default:
				$meta_key = '';
				break;
		}

		if ( !empty( $meta_key ) ) {
			$has_format_content_setting = get_post_meta( $post_id, $meta_key, true );
		} else {
			$has_format_content_setting = has_post_thumbnail();
		}

		$has_format_content = $has_format_content_setting ? true : false;
	} else {
		$has_format_content = false;
	}

	return apply_filters( 'et_has_format_content', $has_format_content, $post_id );
}
}
if (!function_exists('et_has_format_content_class')) {
function et_has_format_content_class( $classes, $class, $post_id ) {
	$has_format_content = et_has_format_content( $post_id );

	if ( $has_format_content ) {
		$classes[] = 'et-has-post-format-content';
	} elseif ( 'post' === get_post_type( $post_id ) ) {
		$classes[] = 'et-doesnt-have-format-content';
	}

	return $classes;
}
}
if (!function_exists('et_set_post_format_default_class')) {
function et_set_post_format_default_class( $classes, $class, $post_id ) {
	if ( 'post' === get_post_type( $post_id ) && ! has_term( array(), ET_POST_FORMAT, $post_id ) ) {
		$classes[] = 'et_post_format-et-post-format-standard';
	}

	return $classes;
}
}
if (!function_exists('et_register_writing_admin_settings')) {
function et_register_writing_admin_settings() {
	add_settings_section( 'et_writing_settings', false, false, 'writing' );

	add_settings_field( 'et_default_post_format', esc_html__( 'Default Post Format', 'extra' ), 'et_default_post_format_render', 'writing', 'et_writing_settings' );

	register_setting( 'writing', 'et_default_post_format', 'et_default_post_format_sanitize' );
}
}
if (!function_exists('et_default_post_format_render')) {
function et_default_post_format_render() {
	$post_formats = et_get_post_format_strings();
	?>
	<select name="et_default_post_format" id="et_default_post_format">
		<?php foreach ( $post_formats as $format_slug => $format_name ): ?>
		<option<?php selected( get_option( 'et_default_post_format' ), $format_slug ); ?> value="<?php echo esc_attr( $format_slug ); ?>"><?php echo esc_html( $format_name ); ?></option>
		<?php endforeach; ?>
	</select>
	<script type="text/javascript">
		jQuery('#default_post_format').parents('tr').remove();
	</script>
	<?php
}
}
if (!function_exists('et_default_post_format_sanitize')) {
function et_default_post_format_sanitize( $value ) {
	return in_array( $value, array_keys( et_get_post_format_strings() ) ) ? $value : false;
}
}
// phpcs:enable -- end of code from the Extra Theme file framework/post-formats.php (with minimal or no modifications)



// phpcs:disable -- the following code is from the Extra Theme file includes/ratings.php with minimal or no modifications; it is assumed that all needed security measures are already in place
if (!function_exists('extra_new_rating')) {
function extra_new_rating() {
	if ( ! wp_verify_nonce( $_POST['extra_rating_nonce'], 'extra_rating_nonce' ) ) {
		die( -1 );
	}

	$post_id = absint( sanitize_text_field( $_POST['extra_post_id'] ) );
	$rating = floatval( sanitize_text_field( $_POST['extra_rating'] ) );

	$result = extra_add_post_rating( $post_id, $rating );
	echo json_encode( $result );

	die();
}
}
if (!function_exists('extra_add_post_rating')) {
function extra_add_post_rating( $post_id, $rating ) {
	if ( extra_get_user_post_rating( $post_id ) ) {
		return array();
	}

	$commentdata = array(
		'comment_type'         => EXTRA_RATING_COMMENT_TYPE,
		'comment_author'       => '',
		'comment_author_url'   => '',
		'comment_author_email' => '',
		'comment_post_ID'      => absint( $post_id ),
		'comment_content'      => abs( floatval( $rating ) ),
	);

	$user = wp_get_current_user();
	if ( $user->exists() ) {
		$commentdata['comment_author'] = wp_slash( $user->display_name );
		$commentdata['user_ID'] = $user->ID;
	}

	// prevent notifications
	add_filter( 'extra_rating_notify_intercept', '__return_zero' );

	wp_new_comment( $commentdata );

	return array(
		'rating'  => $rating,
		'average' => extra_set_post_rating_average( $post_id ),
	);
}
}
if (!function_exists('extra_rating_notify_intercept')) {
function extra_rating_notify_intercept( $option ) {
	$intercepted = apply_filters( 'extra_rating_notify_intercept', false );

	return false !== $intercepted ? $intercepted : false;
}
}
if (!function_exists('extra_rating_pre_comment_approved')) {
function extra_rating_pre_comment_approved( $approved, $commentdata ) {
	if ( !empty( $commentdata['comment_type'] ) && EXTRA_RATING_COMMENT_TYPE == $commentdata['comment_type'] ) {
		$approved = 1;
	}
	return $approved;
}
}
if (!function_exists('extra_rating_pre_comment_user_ip')) {
function extra_rating_pre_comment_user_ip() {
	return extra_get_user_ip();
}
}
if (!function_exists('extra_set_post_rating_average')) {
function extra_set_post_rating_average( $post_id ) {
	$ratings = get_comments( array(
		'type'    => EXTRA_RATING_COMMENT_TYPE,
		'post_id' => $post_id,
		'status'  => 'approve',
		'parent'  => 0,
	) );

	if ( empty( $ratings ) ) {
		$rating_avg = 0;
		update_post_meta( $post_id, '_extra_rating_average', 0 );
		update_post_meta( $post_id, '_extra_rating_count', 0 );
		return;
	}

	$rating_values = array();

	foreach ( $ratings as $rating ) {
		$rating_values[] = floatval( trim( $rating->comment_content ) );
	}

	$num = array_sum( $rating_values ) / count( $rating_values );

	$ceil = ceil( $num );

	$half = $ceil - 0.5;

	if ( $num >= $half + 0.25 ) {
		$rating_average = $ceil;
	} else if ( $num < $half - 0.25 ) {
		$rating_average = floor( $num );
	} else {
		$rating_average = $half;
	}

	$rating_count = count( $rating_values );
	update_post_meta( $post_id, '_extra_rating_average', $rating_average );
	update_post_meta( $post_id, '_extra_rating_count',  $rating_count );

	return compact( 'rating_average', 'rating_count' );
}
}
if (!function_exists('extra_get_post_ratings_count')) {
function extra_get_post_ratings_count( $post_id = '' ) {
	$post_id = empty( $post_id ) ? get_the_ID() : $post_id;

	return get_comments( array(
		'type'    => EXTRA_RATING_COMMENT_TYPE,
		'post_id' => $post_id,
		'status'  => 'approve',
		'parent'  => 0,
		'count'   => true,
	) );
}
}
if (!function_exists('extra_get_post_rating')) {
function extra_get_post_rating( $post_id = 0 ) {
	$post_id = empty( $post_id ) ? get_the_ID() : $post_id;

	$rating = get_post_meta( $post_id, '_extra_rating_average', true );
	return $rating ? $rating : 0;
}
}
if (!function_exists('extra_get_user_ip')) {
function extra_get_user_ip() {
	if ( ! empty( $_SERVER['HTTP_CLIENT_IP'] ) ) {
		$ip = $_SERVER['HTTP_CLIENT_IP'];
	} else if ( ! empty( $_SERVER['HTTP_X_FORWARDED_FOR'] ) ) {
		$ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
	} else {
		$ip = $_SERVER['REMOTE_ADDR'];
	}

	// disallow non white listed characters
	preg_replace( '/[^0-9a-fA-F:., ]/', '', $ip );

	if ( '::1' == $ip ) {
		$ip = '127.0.0.1';
	}

	return $ip;
}
}
if (!function_exists('extra_get_user_post_rating')) {
function extra_get_user_post_rating( $post_id = 0 ) {
	$post_id = empty( $post_id ) ? get_the_ID() : $post_id;

	$args = array(
		'type'    => EXTRA_RATING_COMMENT_TYPE,
		'post_id' => $post_id,
		'status'  => 'approve',
		'parent'  => 0,
		'number'  => 1,
	);

	// If the user is logged in
	$user = wp_get_current_user();
	if ( $user->exists() ) {
		$args['user_id'] = $user->ID;
	} else {
		$args['comment_author_IP'] = extra_get_user_ip();
	}

	$rating = get_comments( $args );
	return !empty( $rating ) ? $rating[0]->comment_content : false;
}
}
if (!function_exists('extra_rating_get_comment_author_ip')) {
function extra_rating_get_comment_author_ip( $clauses, $wp_comment_query ) {
	global $wpdb;

	if ( $wp_comment_query->query_vars['type'] == EXTRA_RATING_COMMENT_TYPE && !empty( $wp_comment_query->query_vars['comment_author_IP'] ) ) {
		$clauses['where'] .= $wpdb->prepare( ' AND comment_author_IP = "%s"', $wp_comment_query->query_vars['comment_author_IP'] );
	}

	return $clauses;
}
}
if (!function_exists('et_pre_get_comments_filter')) {
function et_pre_get_comments_filter( $wp_comment_query ) {
	$type_is_set = isset( $wp_comment_query->query_vars['type'] );

	if ( false === $type_is_set || EXTRA_RATING_COMMENT_TYPE !== $wp_comment_query->query_vars['type'] ) {
		$wp_comment_query->query_vars['type__not_in'] = EXTRA_RATING_COMMENT_TYPE;
	}

	return $wp_comment_query;

}
}
if (!function_exists('extra_get_rating_post_types')) {
function extra_get_rating_post_types() {
	return apply_filters( 'extra_rating_post_types', array(
		'post',
	) );
}
}
// phpcs:enable -- end of code from the Extra Theme file includes/ratings.php (with minimal or no modifications)



// phpcs:disable -- the following code is from the Extra Theme file includes/modules.php with minimal or no modifications; it is assumed that all needed security measures are already in place
// Prevent file from being loaded directly
if ( ! defined( 'ABSPATH' ) ) {
	die( '-1' );
}

if (!class_exists('ET_Builder_Module_Posts')) {
class ET_Builder_Module_Posts extends ET_Builder_Module {

	function init() {
		$this->template_name = 'module-posts';
		$this->name = esc_html__( 'Posts', 'extra' );
		$this->slug = 'et_pb_posts';
		$this->post_types = array( EXTRA_LAYOUT_POST_TYPE );

		$this->main_css_element = '%%order_class%%';

		$this->settings_modal_toggles = $this->get_options_toggles();

		$this->advanced_fields = array(
			'fonts'                 => array(
				'header'     => $this->set_frequent_advanced_options( 'header' ),
				'subheader'  => $this->set_frequent_advanced_options( 'subheader' ),
				'main_title' => array(
					'label'          => esc_html__( 'Main Title', 'et_builder' ),
					'css'            => array(
						'main'     => "{$this->main_css_element} .main-post .hentry h2 a",
						'important' => 'all',
					),
					'line_height'    => array(
						'range_settings' => array(
							'min'  => 0,
							'max'  => 3,
							'step' => 0.1,
						),
					),
					'letter_spacing' => array(
						'range_settings' => array(
							'min'  => 0,
							'max'  => 30,
							'step' => 0.1,
						),
					),
				),
				'main_meta'  => array(
					'label' => esc_html__( 'Main Meta', 'et_builder' ),
					'css'   => array(
						'main' => "{$this->main_css_element} .main-post .hentry .post-meta, {$this->main_css_element} .main-post .hentry .post-meta .comment-bubble:before, {$this->main_css_element} .main-post .hentry .post-meta .rating-star:before",
					),
				),
				'main_body'  => array(
					'label'       => esc_html__( 'Main Body', 'et_builder' ),
					'css'         => array(
						'main' => "{$this->main_css_element} .main-post .hentry .excerpt",
					),
					'line_height' => array(
						'range_settings' => array(
							'min'  => 0,
							'max'  => 3,
							'step' => 0.1,
						),
					),
				),
				'list_title' => array(
					'label'       => esc_html__( 'List Title', 'et_builder' ),
					'css'         => array(
						'main'     => "{$this->main_css_element} .posts-list .hentry h3 a",
						'important' => 'all',
					),
					'line_height' => array(
						'range_settings' => array(
							'min'  => 0,
							'max'  => 3,
							'step' => 0.1,
						),
					),
				),
				'list_meta'  => array(
					'label' => esc_html__( 'List Meta', 'et_builder' ),
					'css'   => array(
						'main' => "{$this->main_css_element} .posts-list .hentry .post-meta, {$this->main_css_element} .posts-list .hentry .post-meta .comment-bubble:before, {$this->main_css_element} .posts-list .hentry .post-meta .rating-star:before",
					),
				),
			),
			'background'            => array(
				'css'      => array(
					'main' => "{$this->main_css_element}, {$this->main_css_element} .module-head",
				),
				'settings' => array(
					'color' => 'alpha',
				),
			),
			'border'                => array(
				'css' => array(
					'main'      => "{$this->main_css_element}",
					'important' => 'all',
				),
			),
			'custom_margin_padding' => array(),
		);

		$this->custom_css_fields = array(
			'head'                   => array(
				'label'    => esc_html__( 'Module Head', 'et_builder' ),
				'selector' => '.module-head',
			),
			'header'                 => array(
				'label'    => esc_html__( 'Module Header', 'et_builder' ),
				'selector' => '.module-head h1',
			),
			'subheader'              => array(
				'label'    => esc_html__( 'Module Subheader', 'et_builder' ),
				'selector' => '.module-head .module-filter',
			),
			'main_post'              => array(
				'label'    => esc_html__( 'Main Post Area', 'et_builder' ),
				'selector' => '.main-post',
			),
			'main_post_hentry'       => array(
				'label'    => esc_html__( 'Main Post Entry', 'et_builder' ),
				'selector' => '.main-post .hentry',
			),
			'main_post_title'        => array(
				'label'    => esc_html__( 'Main Post Title', 'et_builder' ),
				'selector' => '.main-post .hentry h2 a',
			),
			'main_post_meta'         => array(
				'label'    => esc_html__( 'Main Post Meta', 'et_builder' ),
				'selector' => '.main-post .hentry .post-meta',
			),
			'main_post_overlay'      => array(
				'label'    => esc_html__( 'Post Overlay', 'et_builder' ),
				'selector' => '.main-post .hentry .et_pb_extra_overlay',
			),
			'main_post_overlay_icon' => array(
				'label'    => esc_html__( 'Post Overlay Icon', 'et_builder' ),
				'selector' => '.main-post .hentry .et_pb_extra_overlay:before',
			),
			'main_post_meta_icon'    => array(
				'label'    => esc_html__( 'Main Post Meta Icons (Rating &amp; Comment)', 'et_builder' ),
				'selector' => '.main-post .hentry .post-meta .post-meta-icon:before',
			),
			'main_post_excerpt'      => array(
				'label'    => esc_html__( 'Main Post Excerpt', 'et_builder' ),
				'selector' => '.main-post .hentry .excerpt',
			),
			'main_post_overlay'      => array(
				'label'    => esc_html__( 'Main Post Overlay', 'et_builder' ),
				'selector' => '.main-post .hentry .et_pb_extra_overlay',
			),
			'main_post_overlay_icon' => array(
				'label'    => esc_html__( 'Main Post Overlay Icon', 'et_builder' ),
				'selector' => '.main-post .hentry .et_pb_extra_overlay:before',
			),
			'posts_list'             => array(
				'label'    => esc_html__( 'Posts List Area', 'et_builder' ),
				'selector' => '.posts-list',
			),
			'posts_list_hentry'      => array(
				'label'    => esc_html__( 'Posts List Entry', 'et_builder' ),
				'selector' => '.posts-list li',
			),
			'posts_list_title'       => array(
				'label'    => esc_html__( 'Posts List Title', 'et_builder' ),
				'selector' => '.posts-list li h3 a',
			),
			'posts_list_meta'        => array(
				'label'    => esc_html__( 'Posts List Meta', 'et_builder' ),
				'selector' => '.posts-list li .post-meta',
			),
			'posts_list_meta_icon'   => array(
				'label'    => esc_html__( 'Posts List Meta Icon', 'et_builder' ),
				'selector' => '.posts-list li .post-meta .post-meta-icon:before',
			),
			'posts_list_thumbnail'   => array(
				'label'    => esc_html__( 'Posts List Thumbnail', 'et_builder' ),
				'selector' => '.posts-list .post-thumbnail img',
			),
		);
	}

	function set_frequent_advanced_options( $key = '', $css = false ) {
		$fields = array();

		switch ( $key ) {
			case 'header':
				$fields = array(
					'label'          => esc_html__( 'Header', 'et_builder' ),
					'css'            => array(
						'main' => "#page-container {$this->main_css_element} .module-head h1",
					),
					'letter_spacing' => array(
						'range_settings' => array(
							'min'  => 0,
							'max'  => 30,
							'step' => 0.1,
						),
					),
				);
				break;

			case 'subheader':
				$fields = array(
					'label' => esc_html__( 'Subheader', 'et_builder' ),
					'css'   => array(
						'main' => "{$this->main_css_element} .module-head .module-filter",
					),
				);
				break;
		}

		// Overwrite css if needed
		if ( $css ) {
			$fields['css'] = $css;
		}

		return $fields;
	}

	function set_fields() {
		$this->fields_defaults = wp_parse_args( $this->set_additional_fields(), array(
			'heading_style' => array(
				'category',
				'only_default_setting',
			),
			'orderby'       => array(
				'date',
				'only_default_setting',
			),
			'order'         => array(
				'desc',
				'only_default_setting',
			),
			'date_format'   => array(
				'M j, Y',
				'add_default_setting',
			),
		) );

		parent::set_fields();
	}

	function get_options_toggles() {
		return array(
			'general'  => array(
				'toggles' => array(
					'main_content' => esc_html__( 'Posts', 'et_builder' ),
					'elements'     => esc_html__( 'Elements', 'et_builder' ),
				),
			),
			'advanced' => array(
				'toggles' => array(
					'layout'     => esc_html__( 'Layout', 'et_builder'),
					'overlay'    => esc_html__( 'Overlay', 'et_builder' ),
					'post_icon'  => esc_html__( 'Post Format Icon', 'et_builder' ),
					'width'      => array(
						'title'    => esc_html__( 'Sizing', 'et_builder' ),
						'priority' => 65,
					),
				),
			),
			'custom_css' => array(
				'toggles' => array(),
			),
		);
	}

	/**
	 * This is meant to be used by sub-class to add additional fields
	 */
	function set_additional_fields() {
		return array();
	}

	function get_fields() {
		$fields = array(
			'category_id'                 => array(
				'label'            => esc_html__( 'Categories', 'extra' ),
				'type'             => 'categories',
				'description'      => esc_html__( 'Choose categories.', 'extra' ),
				'renderer_options' => array(
					'field_name'   => 'et_pb_category_id',
					'use_terms'    => false,
					'custom_items' => array(
						array( 
							'term_id' => '0', 
							'name' => esc_html__( 'All', 'extra' ),
						),
						array( 
							'term_id' => '-1', 
							'name' => esc_html__( 'Current Category / Tag / Taxonomy', 'extra' ),
						),
					),
				),
				'default'         => '0',
				'priority'        => 1,
				'option_category' => 'configuration',
				'toggle_slug'     => 'main_content',
			),
			'display_featured_posts_only' => array(
				'label'           => esc_html__( 'Display Featured Posts Only', 'extra' ),
				'type'            => 'yes_no_button',
				'options'         => array(
					'off' => esc_html__( 'No', 'extra' ),
					'on'  => esc_html__( 'Yes', 'extra' ),
				),
				'description'     => esc_html__( 'Only display featured posts.', 'extra' ),
				'priority'        => 5,
				'option_category' => 'configuration',
				'toggle_slug'     => 'main_content',
			),
			'ignore_displayed_posts' => array(
				'label'           => esc_html__( 'Ignore Displayed Posts', 'extra' ),
				'type'            => 'yes_no_button',
				'options'         => array(
					'off' => esc_html__( 'No', 'extra' ),
					'on'  => esc_html__( 'Yes', 'extra' ),
				),
				'description'     => esc_html__( 'Do not display posts that have been displayed on previous modules.', 'extra' ),
				'priority'        => 5,
				'option_category' => 'configuration',
				'toggle_slug'     => 'main_content',
			),
			'heading_style'               => array(
				'label'           => esc_html__( 'Heading Style', 'extra' ),
				'type'            => 'select',
				'options'         => array(
					'category' => esc_html__( 'Primary Heading: Category Name, Sub Heading: Filter', 'extra' ),
					'filter'   => esc_html__( 'Primary Heading: Filter, Sub Heading: Category Name', 'extra' ),
					'custom'   => esc_html__( 'Custom Title', 'extra' ),
				),
				'description'     => esc_html__( 'Choose a heading style.', 'extra' ),
				'affects'         => array(
					'heading_primary',
					'heading_sub',
				),
				'option_category' => 'configuration',
				'toggle_slug'     => 'elements',
			),
			'heading_primary'             => array(
				'label'           => esc_html__( 'Primary Heading', 'extra' ),
				'type'            => 'text',
				'description'     => esc_html__( 'The primary heading.', 'extra' ),
				'depends_show_if' => 'custom',
				'option_category' => 'configuration',
				'toggle_slug'     => 'elements',
			),
			'heading_sub'                 => array(
				'label'           => esc_html__( 'Sub Heading', 'extra' ),
				'type'            => 'text',
				'description'     => esc_html__( 'The sub heading.', 'extra' ),
				'depends_show_if' => 'custom',
				'option_category' => 'configuration',
				'toggle_slug'     => 'elements',
			),
			'posts_per_page'              => array(
				'label'           => esc_html__( 'Posts Limit', 'extra' ),
				'type'            => 'text',
				'description'     => esc_html__( 'The number of posts shown.', 'extra' ),
				'priority'        => 3,
				'option_category' => 'configuration',
				'toggle_slug'     => 'main_content',
			),
			'orderby'                     => array(
				'label'           => esc_html__( 'Sort Method', 'extra' ),
				'type'            => 'select',
				'options'         => array(
					'date'          => esc_html__( 'Most Recent', 'extra' ),
					'comment_count' => esc_html__( 'Most Popular', 'extra' ),
					'rating'        => esc_html__( 'Highest Rated', 'extra' ),
				),
				'description'     => esc_html__( 'Choose a sort method.', 'extra' ),
				'option_category' => 'configuration',
				'toggle_slug'     => 'main_content',
			),
			'order'                       => array(
				'label'           => esc_html__( 'Sort Order', 'extra' ),
				'type'            => 'select',
				'options'         => array(
					'desc' => esc_html__( 'Descending', 'extra' ),
					'asc'  => esc_html__( 'Ascending', 'extra' ),
				),
				'description'     => esc_html__( 'Choose a sort order.', 'extra' ),
				'option_category' => 'configuration',
				'toggle_slug'     => 'main_content',
			),
			'show_thumbnails'             => array(
				'label'           => esc_html__( 'Show Featured Image', 'extra' ),
				'type'            => 'yes_no_button',
				'options'         => array(
					'on'  => esc_html__( 'Yes', 'extra' ),
					'off' => esc_html__( 'No', 'extra' ),
				),
				'description'     => esc_html__( "Turn the display of each post's featured image on or off.", 'extra' ),
				'priority'        => 5,
				'option_category' => 'configuration',
				'toggle_slug'     => 'elements',
			),
			'show_author'                 => array(
				'label'           => esc_html__( 'Show Author', 'extra' ),
				'type'            => 'yes_no_button',
				'options'         => array(
					'on'  => esc_html__( 'Yes', 'extra' ),
					'off' => esc_html__( 'No', 'extra' ),
				),
				'description'     => esc_html__( "Turn the display of each post's author on or off.", 'extra' ),
				'priority'        => 5,
				'option_category' => 'configuration',
				'toggle_slug'     => 'elements',
			),
			'show_categories'             => array(
				'label'           => esc_html__( 'Show Categories', 'extra' ),
				'type'            => 'yes_no_button',
				'options'         => array(
					'on'  => esc_html__( 'Yes', 'extra' ),
					'off' => esc_html__( 'No', 'extra' ),
				),
				'description'     => esc_html__( "Turn the display of each post's categories on or off.", 'extra' ),
				'priority'        => 5,
				'option_category' => 'configuration',
				'toggle_slug'     => 'elements',
			),
			'show_comments'               => array(
				'label'           => esc_html__( 'Show Comments', 'extra' ),
				'type'            => 'yes_no_button',
				'options'         => array(
					'on'  => esc_html__( 'Yes', 'extra' ),
					'off' => esc_html__( 'No', 'extra' ),
				),
				'description'     => esc_html__( "Turn the display of each post's ccomments on or off.", 'extra' ),
				'priority'        => 5,
				'option_category' => 'configuration',
				'toggle_slug'     => 'elements',
			),
			'show_rating'                 => array(
				'label'           => esc_html__( 'Show Rating', 'extra' ),
				'type'            => 'yes_no_button',
				'options'         => array(
					'on'  => esc_html__( 'Yes', 'extra' ),
					'off' => esc_html__( 'No', 'extra' ),
				),
				'description'     => esc_html__( "Turn the display of each post's rating on or off.", 'extra' ),
				'priority'        => 5,
				'option_category' => 'configuration',
				'toggle_slug'     => 'elements',
			),
			'show_date'                   => array(
				'label'           => esc_html__( 'Show Date', 'extra' ),
				'type'            => 'yes_no_button',
				'options'         => array(
					'on'  => esc_html__( 'Yes', 'extra' ),
					'off' => esc_html__( 'No', 'extra' ),
				),
				'affects'         => array( 'date_format' ),
				'description'     => esc_html__( "Turn the dispay of each post's date on or off.", 'extra' ),
				'priority'        => 5,
				'option_category' => 'configuration',
				'toggle_slug'     => 'elements',
			),
			'date_format'                 => array(
				'label'               => esc_html__( 'Date Format', 'extra' ),
				'type'                => 'text',
				'depends_show_if_not' => "off",
				'description'         => esc_html__( 'The format for the date display in PHP date() format', 'extra' ),
				'option_category'     => 'configuration',
				'toggle_slug'         => 'elements',
			),
			'hover_overlay_color'         => array(
				'label'        => esc_html__( 'Hover Overlay Color', 'et_builder' ),
				'type'         => 'color-alpha',
				'custom_color' => true,
				'tab_slug'     => 'advanced',
				'toggle_slug'  => 'overlay',
				'priority'     => 26,
			),
			'hover_overlay_icon_color'    => array(
				'label'        => esc_html__( 'Hover Overlay Icon Color', 'et_builder' ),
				'type'         => 'color-alpha',
				'custom_color' => true,
				'tab_slug'     => 'advanced',
				'toggle_slug'  => 'overlay',
				'priority'     => 26,
			),
			'hover_overlay_icon'          => array(
				'label'               => esc_html__( 'Hover Overlay Icon Picker', 'et_builder' ),
				'type'                => 'select_icon',
				'option_category'     => 'configuration',
				'class'               => array( 'et-pb-font-icon' ),
				'renderer_with_field' => true,
				'tab_slug'            => 'advanced',
				'toggle_slug'         => 'overlay',
				'priority'            => 26,
			),
			'admin_label'                 => array(
				'label'       => esc_html__( 'Admin Label', 'extra' ),
				'type'        => 'text',
				'description' => esc_html__( 'This will change the label of the module in the builder for easy identification.', 'extra' ),
				'toggle_slug' => 'admin_label',
			),
			'module_id'                   => array(
				'label'           => esc_html__( 'CSS ID', 'extra' ),
				'type'            => 'text',
				'description'     => esc_html__( 'Enter an optional CSS ID to be used for this module. An ID can be used to create custom CSS styling, or to create links to particular sections of your page.', 'extra' ),
				'option_category' => 'configuration',
				'tab_slug'        => 'custom_css',
				'toggle_slug'     => 'classes',
			),
			'module_class'                => array(
				'label'           => esc_html__( 'CSS Class', 'extra' ),
				'type'            => 'text',
				'description'     => esc_html__( 'Enter optional CSS classes to be used for this module. A CSS class can be used to create custom CSS styling. You can add multiple classes, separated with a space.', 'extra' ),
				'option_category' => 'configuration',
				'tab_slug'        => 'custom_css',
				'toggle_slug'     => 'classes',
			),
			'max_width'                   => array(
				'label'           => esc_html__( 'Max Width', 'et_builder' ),
				'type'            => 'text',
				'option_category' => 'layout',
				'tab_slug'        => 'advanced',
				'toggle_slug'     => 'width',
				'validate_unit'   => true,
			),
		);

		$advanced_design_fields = array(
			'post_format_icon_bg_color' => array(
				'label'        => esc_html__( 'Post Format Icon Background Color', 'et_builder' ),
				'type'         => 'color-alpha',
				'custom_color' => true,
				'tab_slug'     => 'advanced',
				'toggle_slug'  => 'post_icon',
				'priority'     => 25,
			),
			'remove_drop_shadow'        => array(
				'label'           => esc_html__( 'Remove Drop Shadow', 'et_builder' ),
				'type'            => 'yes_no_button',
				'option_category' => 'layout',
				'options'         => array(
					'off' => esc_html__( 'No', 'et_builder' ),
					'on'  => esc_html__( 'Yes', 'et_builder' ),
				),
				'tab_slug'        => 'advanced',
				'toggle_slug'     => 'layout',
				'priority'        => 26,
			),
			'border_radius'             => array(
				'label'           => esc_html__( 'Border Radius', 'et_builder' ),
				'type'            => 'range',
				'option_category' => 'layout',
				'tab_slug'        => 'advanced',
				'toggle_slug'     => 'border',
				'priority'        => 27,
				'range_settings'  => array(
					'min'  => '0',
					'max'  => '200',
					'step' => '1',
				),
			),
		);

		return array_merge( $fields, $advanced_design_fields );
	}

	function process_bool_shortcode_atts() {
		foreach ( $this->get_fields() as $field_name => $field ) {
			if ( 'yes_no_button' == $field['type'] ) {
				$this->props[ $field_name ] = 'on' == $this->props[ $field_name ] ? true : false;
			}
		}

		$this->props['use_tax_query'] = false;
	}

	function shortcode_atts() {
		$this->process_bool_shortcode_atts();
	}

	function render( $atts, $content = null, $function_name ) {
		global $extra_displayed_post_ids;

		$args = array(
			'post_type'      => 'post',
			'posts_per_page' => isset( $this->props['posts_per_page'] ) && is_numeric( $this->props['posts_per_page'] ) ? $this->props['posts_per_page'] : 5,
			'order'          => $this->props['order'],
			'orderby'        => $this->props['orderby'],
			'ignore_displayed_posts' => isset( $this->props['ignore_displayed_posts'] ) ? $this->props['ignore_displayed_posts'] : false,
		);

		if ( 'rating' == $this->props['orderby'] ) {
			$args['orderby'] = 'meta_value_num';
			$args['meta_key'] = '_extra_rating_average';
		}

		if ( ! $extra_displayed_post_ids ) {
			$extra_displayed_post_ids = array();
		}

		if ( $args['ignore_displayed_posts'] ) {
			$args['post__not_in'] = $extra_displayed_post_ids;
		}

		$args = $this->_pre_wp_query( $args );

		// need to hook into pre_get_posts to set is_home = true, then unhook afterwards
		add_action( 'pre_get_posts', array( $this, 'make_is_home' ) );

		$this->props['module_posts'] = new WP_Query( $args );

		// unhook afterwards
		remove_action( 'pre_get_posts', array( $this, 'make_is_home' ) );

		$posts_per_page = $this->props['module_posts']->get( 'posts_per_page' );

		// only slice if there is a limit that where trying to enforce respect upon and if it's disrespecting the limit
		if ( $posts_per_page > 0 && $this->props['module_posts']->post_count > $posts_per_page ) {
			$sticky_posts = get_option( 'sticky_posts' );
			if ( is_array( $sticky_posts ) && !empty( $sticky_posts ) ) {
				// make wp_query respect posts_per_page even when sticky posts are involved
				$module_posts = $this->props['module_posts'];
				$module_posts->posts = array_slice( $module_posts->posts, 0, $posts_per_page );
				$module_posts->post_count = $posts_per_page;
				$this->props['module_posts'] = $module_posts;
			}
		}

		if ( ! empty( $this->props['terms_names'] ) ) {
			$category_name = $this->props['terms_names'];
		} else if ( ! empty( $this->props['term_name'] ) ) {
			$category_name = $this->props['term_name'];
		} else {
			$category_name = esc_html__( 'All', 'extra' );
		}

		$this->props['is_all_categories'] = (bool) empty( $this->props['term_name'] );

		switch ( $this->props['orderby'] ) {
			case 'comment_count':
				$filter_title = esc_html__( 'Popular', 'extra' );
				break;
			case 'rating':
				$filter_title = esc_html__( 'Top Rated', 'extra' );
				break;
			case 'date':
			default:
				$filter_title = esc_html__( 'Latest', 'extra' );
				break;
		}

		if ( !empty( $this->props['heading_style'] ) ) {
			switch ( $this->props['heading_style'] ) {
				case 'filter':
					$this->props['title'] = $filter_title;
					$this->props['sub_title'] = $category_name;
					break;
				case 'custom':
					$this->props['title'] = !empty( $this->props['heading_primary'] ) ? esc_html( $this->props['heading_primary'] ) : '';
					$this->props['sub_title'] = !empty( $this->props['heading_sub'] ) ? esc_html( $this->props['heading_sub'] ) : '';
					break;
				case 'category':
				default:
					$this->props['title'] = $category_name;
					$this->props['sub_title'] = $filter_title;
					break;
			}
		}

		if ( !empty( $this->props['term_color'] ) ) {
			$this->props['border_top_color'] = $this->props['term_color'];
			$this->props['module_title_color'] = $this->props['term_color'];
		} else {

			$color = et_builder_accent_color();
			$module_posts = $this->props['module_posts'];

			if ( isset( $module_posts->posts[0] ) ) {
				$featured_post = $module_posts->posts[0];
				$categories = wp_get_post_categories( $featured_post->ID );

				if ( !empty( $categories ) ) {
					$first_category_id = $categories[0];
					if ( function_exists( 'et_get_childmost_taxonomy_meta' ) ) {
						$color = et_get_childmost_taxonomy_meta( $first_category_id, 'color', true, et_builder_accent_color() );
					}
				}
			}

			$this->props['term_color'] = $color;
			$this->props['border_top_color'] = $color;
			$this->props['module_title_color'] = $color;
		}

		if ( isset( $this->props['module_class'] ) ) {
			$this->props['module_class'] = ET_Builder_Element::add_module_order_class( $this->props['module_class'], $this->slug );
		}

		// Adding styling classes to module
		if ( !empty( $this->props['remove_drop_shadow'] ) && 'on' === $this->props['remove_drop_shadow'] ) {
			$this->props['module_class'] = $this->props['module_class'] . ' et_pb_no_drop_shadow';
		}

		// Print styling for general options
		if ( isset( $this->props['border_radius'] ) && '' !== $this->props['border_radius'] ) {
			ET_Builder_Module::set_style( $this->slug, array(
				'selector'    => '%%order_class%%.et_pb_extra_module',
				'declaration' => sprintf(
					'-moz-border-radius: %1$s;
					-webkit-border-radius: %1$s;
					border-radius: %1$s;',
					esc_html( $this->props['border_radius'] )
				),
			) );
		}

		if ( isset( $this->props['max_width'] ) && '' !== $this->props['max_width'] ) {
			ET_Builder_Module::set_style( $this->slug, array(
				'selector'    => '%%order_class%%',
				'declaration' => sprintf(
					'max-width: %1$s;',
					esc_html( et_builder_process_range_value( $this->props['max_width'] ) )
				),
			) );
		}

		if ( isset( $this->props['post_format_icon_bg_color'] ) && '' !== $this->props['post_format_icon_bg_color'] ) {
			ET_Builder_Module::set_style( $this->slug, array(
				'selector'    => '%%order_class%% .post-thumbnail img',
				'declaration' => sprintf(
					'background-color: %1$s !important;',
					esc_html( $this->props['post_format_icon_bg_color'] )
				),
			) );
		}

		if ( isset( $this->props['hover_overlay_color'] ) && '' !== $this->props['hover_overlay_color'] ) {
			ET_Builder_Element::set_style( $this->slug, array(
				'selector'    => '%%order_class%% .et_pb_extra_overlay',
				'declaration' => sprintf(
					'background-color: %1$s;
					border-color: %1$s;',
					esc_html( $this->props['hover_overlay_color'] )
				),
			) );
		}

		if ( isset( $this->props['hover_overlay_icon_color'] ) && '' !== $this->props['hover_overlay_icon_color'] ) {
			ET_Builder_Element::set_style( $this->slug, array(
				'selector'    => '%%order_class%% .et_pb_extra_overlay:before',
				'declaration' => sprintf(
					'color: %1$s;',
					esc_html( $this->props['hover_overlay_icon_color'] )
				),
			) );
		}

		// Overwrite border_color_top attribute if border color is defined by advanced design settings
		if ( isset( $this->props['border_color'] ) && isset( $this->props['use_border_color'] ) && 'on' === $this->props['use_border_color'] ) {
			$this->props['border_top_color'] = $this->props['border_color'];
		}

		if ( is_customize_preview() && $this->props['term_color'] === extra_global_accent_color() ) {
			$this->props['module_class'] = $this->props['module_class'] . ' no-term-color-module';
		}

		if ( isset( $this->props['module_posts']->found_posts ) && 0 < $this->props['module_posts']->found_posts ) {
			$post_ids = wp_list_pluck( $this->props['module_posts']->posts, 'ID' );

			$extra_displayed_post_ids = array_unique( array_merge( $extra_displayed_post_ids, $post_ids ) );
		}
	}

	function make_is_home( $wp_query ) {
		$wp_query->is_home = true;
	}

	function append_tax_query_params( $params ) {
		global $wp_query;

		if ( isset( $wp_query->tax_query->queries ) ) {
			$params['tax_query'] = $wp_query->tax_query->queries;
		}

		return $params;
	}

	function _process_shortcode_atts_category_id() {
		if ( false !== strpos( $this->props['category_id'], '-1' ) ) {
			$this->props['use_tax_query'] = true;

			if ( is_category() ) {
				$current_categoory = get_queried_object_id();
			} else {
				$current_categoory = '';
			}

			if ( '-1' == $this->props['category_id'] ) {
				$this->props['category_id'] = $current_categoory;
			} else {
				$replace = empty( $current_categoory ) ? '-1,' : '-1';
				$this->props['category_id'] = str_ireplace( $replace, $current_categoory, $this->props['category_id'] );
			}
		}

		if ( '0' == substr( $this->props['category_id'], 0, 1 ) ) {
			$this->props['category_id'] = 0;
		}
	}

	function _pre_wp_query( $args ) {
		global $wp_query;

		$this->_process_shortcode_atts_category_id();

		if ( !empty( $this->props['category_id'] ) ) {
			$categories = array_map( 'absint', explode( ',', $this->props['category_id'] ) );

			$args['ignore_sticky_posts'] = 1;

			$args['tax_query'] = array(
				array(
					'taxonomy' => 'category',
					'field'    => 'id',
					'terms'    => $categories,
					'operator' => 'IN',
				),
			);

			if ( count( $categories ) > 1 ) {
				$terms_names = array();
				foreach ( $categories as $category_id ) {
					$terms_names[] = get_term( $category_id, 'category' )->name;
				}
				$terms_names = implode( ', ', $terms_names );
				$this->props['terms_names'] = $terms_names;
			}

			$term = get_term( $categories[0], 'category' );
			if ( !empty( $term ) && empty( $term->errors ) ) {
				$this->props['term_name'] = $term->name;
				$this->props['term_color'] = extra_get_category_color( $term->term_id );
			} else {
				unset( $args['tax_query'] );
			}
		}

		if ( isset( $wp_query->tax_query->queries ) && $this->props['use_tax_query'] ) {
			wp_localize_script( 'extra-scripts', 'EXTRA_TAX_QUERY', $wp_query->tax_query->queries );

			$taxonomies = $wp_query->tax_query->queries;

			foreach ( $taxonomies as $taxonomy ) {
				if ( isset( $taxonomy['taxonomy'] ) && 'category' === $taxonomy['taxonomy'] && ! empty( $this->props['category_id'] ) ) {
					continue;
				}

				$args['tax_query'][] = $taxonomy;
			}

			if ( isset( $args['tax_query'] ) && 1 < count( $args['tax_query'] ) ) {
				$args['tax_query']['relation'] = 'AND';
			}

			if ( ! is_home() ) {
				$args['ignore_sticky_posts'] = 1;
			}
		}

		if ( $this->props['display_featured_posts_only'] ) {
			$args['meta_query'] = array(
				array(
					'key'   => '_extra_featured_post',
					'value' => '1',
				),
			);
		}

		return $args;
	}

}

}


if (!class_exists('ET_Builder_Module_Tabbed_Posts')) {
class ET_Builder_Module_Tabbed_Posts extends ET_Builder_Module {

	public static $global_shortcode_atts;

	public static $tabs_data = array();

	function init() {
		$this->template_name = 'module-tabbed-posts';
		$this->name = esc_html__( 'Tabbed Posts', 'extra' );
		$this->slug = 'et_pb_tabbed_posts';
		$this->post_types = array( EXTRA_LAYOUT_POST_TYPE );
		$this->child_slug = 'et_pb_tabbed_posts_tab';

		$this->main_css_element = '%%order_class%%';

		$this->settings_modal_toggles = array(
			'general'  => array(
				'toggles' => array(
					'main_content' => esc_html__( 'Posts', 'et_builder' ),
					'elements'     => esc_html__( 'Elements', 'et_builder' ),
				),
			),
			'advanced' => array(
				'toggles' => array(
					'layout'     => esc_html__( 'Layout', 'et_builder'),
					'overlay'    => esc_html__( 'Overlay', 'et_builder' ),
					'post_icon'  => esc_html__( 'Post Format Icon', 'et_builder' ),
					'tabs_bg'    => esc_html__( 'Tabs Background', 'et_builder' ),
					'width'      => array(
						'title'    => esc_html__( 'Sizing', 'et_builder' ),
						'priority' => 65,
					),
				),
			),
		);

		$this->advanced_fields = array(
			'fonts'                 => array(
				'tab'        => array(
					'label'          => esc_html__( 'Tab', 'et_builder' ),
					'css'            => array(
						'main' => "{$this->main_css_element} .tabs ul li",
					),
					'letter_spacing' => array(
						'range_settings' => array(
							'min'  => 0,
							'max'  => 30,
							'step' => 0.1,
						),
					),
				),
				'main_title' => array(
					'label'          => esc_html__( 'Main Title', 'et_builder' ),
					'css'            => array(
						'main'     => "{$this->main_css_element} .main-post .hentry h2 a",
						'important' => 'all',
					),
					'line_height'    => array(
						'range_settings' => array(
							'min'  => 0,
							'max'  => 3,
							'step' => 0.1,
						),
					),
					'letter_spacing' => array(
						'range_settings' => array(
							'min'  => 0,
							'max'  => 30,
							'step' => 0.1,
						),
					),
				),
				'main_meta'  => array(
					'label' => esc_html__( 'Main Meta', 'et_builder' ),
					'css'   => array(
						'main' => "{$this->main_css_element} .main-post .hentry .post-meta, {$this->main_css_element} .main-post .hentry .post-meta .comment-bubble:before, {$this->main_css_element} .main-post .hentry .post-meta .rating-star:before",
					),
				),
				'main_body'  => array(
					'label'       => esc_html__( 'Main Body', 'et_builder' ),
					'css'         => array(
						'main' => "{$this->main_css_element} .main-post .hentry .excerpt",
					),
					'line_height' => array(
						'range_settings' => array(
							'min'  => 0,
							'max'  => 3,
							'step' => 0.1,
						),
					),
				),
				'list_title' => array(
					'label'       => esc_html__( 'List Title', 'et_builder' ),
					'css'         => array(
						'main'     => "{$this->main_css_element} .posts-list .hentry h3 a",
						'important' => 'all',
					),
					'line_height' => array(
						'range_settings' => array(
							'min'  => 0,
							'max'  => 3,
							'step' => 0.1,
						),
					),
				),
				'list_meta'  => array(
					'label' => esc_html__( 'List Meta', 'et_builder' ),
					'css'   => array(
						'main' => "{$this->main_css_element} .posts-list .hentry .post-meta, {$this->main_css_element} .posts-list .hentry .post-meta .comment-bubble:before, {$this->main_css_element} .posts-list .hentry .post-meta .rating-star:before",
					),
				),
			),
			'background'            => array(
				'css'      => array(
					'main' => "{$this->main_css_element}.tabbed-post-module",
				),
				'settings' => array(
					'color' => 'alpha',
				),
			),
			'border'                => array(
				'css' => array(
					'main'      => "{$this->main_css_element}.tabbed-post-module",
					'important' => 'all',
				),
			),
			'custom_margin_padding' => array(),
		);

		$this->custom_css_fields = array(
			'tab'                    => array(
				'label'    => esc_html__( 'Tab', 'et_builder' ),
				'selector' => '.tabs',
			),
			'tab_item'               => array(
				'label'    => esc_html__( 'Tab Item', 'et_builder' ),
				'selector' => '.tabs li',
			),
			'tab_item_hover'         => array(
				'label'    => esc_html__( 'Tab Item Hover', 'et_builder' ),
				'selector' => '.tabs li:hover',
			),
			'tab_item_active'        => array(
				'label'    => esc_html__( 'Tab Item Active', 'et_builder' ),
				'selector' => '.tabs li.active',
			),
			'main_post'              => array(
				'label'    => esc_html__( 'Main Post Area', 'et_builder' ),
				'selector' => '.main-post',
			),
			'main_post_hentry'       => array(
				'label'    => esc_html__( 'Main Post Entry', 'et_builder' ),
				'selector' => '.main-post .hentry',
			),
			'main_post_title'        => array(
				'label'    => esc_html__( 'Main Post Title', 'et_builder' ),
				'selector' => '.main-post .hentry h2 a',
			),
			'main_post_meta'         => array(
				'label'    => esc_html__( 'Main Post Meta', 'et_builder' ),
				'selector' => '.main-post .hentry .post-meta',
			),
			'main_post_meta_icon'    => array(
				'label'    => esc_html__( 'Main Post Meta Icons (Rating &amp; Comment)', 'et_builder' ),
				'selector' => '.main-post .hentry .post-meta .post-meta-icon:before',
			),
			'main_post_excerpt'      => array(
				'label'    => esc_html__( 'Main Post Excerpt', 'et_builder' ),
				'selector' => '.main-post .hentry .excerpt',
			),
			'main_post_overlay'      => array(
				'label'    => esc_html__( 'Main Post Overlay', 'et_builder' ),
				'selector' => '.main-post .hentry .et_pb_extra_overlay',
			),
			'main_post_overlay_icon' => array(
				'label'    => esc_html__( 'Main Post Overlay Icon', 'et_builder' ),
				'selector' => '.main-post .hentry .et_pb_extra_overlay:before',
			),
			'posts_list'             => array(
				'label'    => esc_html__( 'Posts List Area', 'et_builder' ),
				'selector' => '.posts-list',
			),
			'posts_list_hentry'      => array(
				'label'    => esc_html__( 'Posts List Entry', 'et_builder' ),
				'selector' => '.posts-list li',
			),
			'posts_list_title'       => array(
				'label'    => esc_html__( 'Posts List Title', 'et_builder' ),
				'selector' => '.posts-list li h3 a',
			),
			'posts_list_meta'        => array(
				'label'    => esc_html__( 'Posts List Meta', 'et_builder' ),
				'selector' => '.posts-list li .post-meta',
			),
			'posts_list_meta_icon'   => array(
				'label'    => esc_html__( 'Posts List Meta Icon', 'et_builder' ),
				'selector' => '.posts-list li .post-meta .post-meta-icon:before',
			),
			'posts_list_thumbnail'   => array(
				'label'    => esc_html__( 'Posts List Thumbnail', 'et_builder' ),
				'selector' => '.posts-list .post-thumbnail img',
			),
		);
	}

	function set_fields() {
		$this->fields_defaults = array(
			'date_format' => array(
				'M j, Y',
				'add_default_setting',
			),
		);

		parent::set_fields();
	}

	function get_fields() {
		$fields = array(
			'posts_per_page'  => array(
				'label'           => esc_html__( 'Posts Limit', 'extra' ),
				'type'            => 'text',
				'description'     => esc_html__( 'The number of posts shown.', 'extra' ),
				'priority'        => 3,
				'option_category' => 'configuration',
				'toggle_slug'     => 'main_content',
			),
			'show_thumbnails' => array(
				'label'           => esc_html__( 'Show Featured Image', 'extra' ),
				'type'            => 'yes_no_button',
				'options'         => array(
					'on'  => esc_html__( 'Yes', 'extra' ),
					'off' => esc_html__( 'No', 'extra' ),
				),
				'description'     => esc_html__( "Turn the display of each post's featured image on or off.", 'extra' ),
				'priority'        => 5,
				'option_category' => 'configuration',
				'toggle_slug'     => 'elements',
			),
			'show_author'     => array(
				'label'           => esc_html__( 'Show Author', 'extra' ),
				'type'            => 'yes_no_button',
				'options'         => array(
					'on'  => esc_html__( 'Yes', 'extra' ),
					'off' => esc_html__( 'No', 'extra' ),
				),
				'description'     => esc_html__( "Turn the display of each post's author on or off.", 'extra' ),
				'priority'        => 5,
				'option_category' => 'configuration',
				'toggle_slug'     => 'elements',
			),
			'show_categories' => array(
				'label'           => esc_html__( 'Show Categories', 'extra' ),
				'type'            => 'yes_no_button',
				'options'         => array(
					'on'  => esc_html__( 'Yes', 'extra' ),
					'off' => esc_html__( 'No', 'extra' ),
				),
				'description'     => esc_html__( "Turn the display of each post's categories on or off.", 'extra' ),
				'priority'        => 5,
				'option_category' => 'configuration',
				'toggle_slug'     => 'elements',
			),
			'show_comments'   => array(
				'label'           => esc_html__( 'Show Comments', 'extra' ),
				'type'            => 'yes_no_button',
				'options'         => array(
					'on'  => esc_html__( 'Yes', 'extra' ),
					'off' => esc_html__( 'No', 'extra' ),
				),
				'description'     => esc_html__( "Turn the display of each post's ccomments on or off.", 'extra' ),
				'priority'        => 5,
				'option_category' => 'configuration',
				'toggle_slug'     => 'elements',
			),
			'show_rating'     => array(
				'label'           => esc_html__( 'Show Rating', 'extra' ),
				'type'            => 'yes_no_button',
				'options'         => array(
					'on'  => esc_html__( 'Yes', 'extra' ),
					'off' => esc_html__( 'No', 'extra' ),
				),
				'description'     => esc_html__( "Turn the display of each post's rating on or off.", 'extra' ),
				'priority'        => 5,
				'option_category' => 'configuration',
				'toggle_slug'     => 'elements',
			),
			'show_date'       => array(
				'label'           => esc_html__( 'Show Date', 'extra' ),
				'type'            => 'yes_no_button',
				'options'         => array(
					'on'  => esc_html__( 'Yes', 'extra' ),
					'off' => esc_html__( 'No', 'extra' ),
				),
				'affects'         => array( 'date_format' ),
				'description'     => esc_html__( "Turn the dispay of each post's date on or off.", 'extra' ),
				'priority'        => 5,
				'option_category' => 'configuration',
				'toggle_slug'     => 'elements',
			),
			'date_format'     => array(
				'label'               => esc_html__( 'Date Format', 'extra' ),
				'type'                => 'text',
				'depends_show_if_not' => "off",
				'description'         => esc_html__( 'The format for the date display in PHP date() format', 'extra' ),
				'option_category'     => 'configuration',
				'toggle_slug'     => 'elements',
			),
			'admin_label'     => array(
				'label'       => esc_html__( 'Admin Label', 'extra' ),
				'type'        => 'text',
				'description' => esc_html__( 'This will change the label of the module in the builder for easy identification.', 'extra' ),
				'toggle_slug' => 'admin_label',
			),
			'module_id'       => array(
				'label'           => esc_html__( 'CSS ID', 'extra' ),
				'type'            => 'text',
				'description'     => esc_html__( 'Enter an optional CSS ID to be used for this module. An ID can be used to create custom CSS styling, or to create links to particular sections of your page.', 'extra' ),
				'option_category' => 'configuration',
				'tab_slug'        => 'custom_css',
				'toggle_slug'     => 'classes',
			),
			'module_class'    => array(
				'label'           => esc_html__( 'CSS Class', 'extra' ),
				'type'            => 'text',
				'description'     => esc_html__( 'Enter optional CSS classes to be used for this module. A CSS class can be used to create custom CSS styling. You can add multiple classes, separated with a space.', 'extra' ),
				'option_category' => 'configuration',
				'tab_slug'        => 'custom_css',
				'toggle_slug'     => 'classes',
			),
		);

		$advanced_design_fields = array(
			'max_width'                     => array(
				'label'           => esc_html__( 'Max Width', 'et_builder' ),
				'type'            => 'text',
				'option_category' => 'layout',
				'tab_slug'        => 'advanced',
				'toggle_slug'     => 'width',
				'validate_unit'   => true,
			),
			'active_tab_text_color'         => array(
				'label'        => esc_html__( 'Active Tab Text Color', 'et_builder' ),
				'type'         => 'color-alpha',
				'custom_color' => true,
				'tab_slug'     => 'advanced',
				'toggle_slug'  => 'tab',
			),
			'active_tab_background_color'   => array(
				'label'        => esc_html__( 'Active Tab Background Color', 'et_builder' ),
				'type'         => 'color-alpha',
				'custom_color' => true,
				'tab_slug'     => 'advanced',
				'toggle_slug'  => 'tabs_bg',
			),
			'inactive_tab_background_color' => array(
				'label'        => esc_html__( 'Inactive Tab Background Color', 'et_builder' ),
				'type'         => 'color-alpha',
				'custom_color' => true,
				'tab_slug'     => 'advanced',
				'toggle_slug'  => 'tabs_bg',
			),
			'hover_overlay_color'           => array(
				'label'        => esc_html__( 'Hover Overlay Color', 'et_builder' ),
				'type'         => 'color-alpha',
				'custom_color' => true,
				'tab_slug'     => 'advanced',
				'toggle_slug'  => 'overlay',
				'priority'     => 26,
			),
			'hover_overlay_icon_color'      => array(
				'label'        => esc_html__( 'Hover Overlay Icon Color', 'et_builder' ),
				'type'         => 'color-alpha',
				'custom_color' => true,
				'tab_slug'     => 'advanced',
				'toggle_slug'  => 'overlay',
				'priority'     => 26,
			),
			'hover_overlay_icon'            => array(
				'label'               => esc_html__( 'Hover Overlay Icon Picker', 'et_builder' ),
				'type'                => 'select_icon',
				'option_category'     => 'configuration',
				'class'               => array( 'et-pb-font-icon' ),
				'renderer_with_field' => true,
				'tab_slug'            => 'advanced',
				'toggle_slug'         => 'overlay',
				'priority'            => 26,
			),
			'post_format_icon_bg_color'     => array(
				'label'        => esc_html__( 'Post Format Icon Background Color', 'et_builder' ),
				'type'         => 'color-alpha',
				'custom_color' => true,
				'tab_slug'     => 'advanced',
				'toggle_slug'  => 'post_icon',
				'priority'     => 25,
			),
			'remove_drop_shadow'            => array(
				'label'           => esc_html__( 'Remove Drop Shadow', 'et_builder' ),
				'type'            => 'yes_no_button',
				'option_category' => 'layout',
				'options'         => array(
					'off' => esc_html__( 'No', 'et_builder' ),
					'on'  => esc_html__( 'Yes', 'et_builder' ),
				),
				'tab_slug'        => 'advanced',
				'toggle_slug'     => 'layout',
				'priority'        => 26,
			),
			'border_radius'                 => array(
				'label'           => esc_html__( 'Border Radius', 'et_builder' ),
				'type'            => 'range',
				'option_category' => 'layout',
				'tab_slug'        => 'advanced',
				'toggle_slug'     => 'border',
				'priority'        => 27,
				'range_settings'  => array(
					'min'  => '0',
					'max'  => '200',
					'step' => '1',
				),
			),
		);

			return array_merge( $fields, $advanced_design_fields );
	}

	function add_new_child_text() {
		return esc_html__( 'Add New Tab', 'extra' );
	}

	function before_render() {
		$global_shortcode_atts = array(
			'posts_per_page',
			'show_thumbnails',
			'show_author',
			'show_categories',
			'show_date',
			'show_rating',
			'show_comments',
			'date_format',
			'hover_overlay_color',
			'hover_overlay_icon_color',
			'hover_overlay_icon',
		);

		$this->shortcode_atts();

		foreach ( $global_shortcode_atts as $att ) {
			self::$global_shortcode_atts[$att] = $this->props[ $att ];
		}

		if ( isset( $this->props['active_tab_text_color'] ) && '' !== $this->props['active_tab_text_color'] ) {
			ET_Builder_Element::set_style( $this->slug, array(
				'selector'    => '%%order_class%% .tabs ul li.active',
				'declaration' => sprintf(
					'color: %1$s !important;',
					esc_html( $this->props['active_tab_text_color'] )
				),
			) );
		}

		if ( isset( $this->props['active_tab_background_color'] ) && '' !== $this->props['active_tab_background_color'] ) {
			ET_Builder_Element::set_style( $this->slug, array(
				'selector'    => '%%order_class%% .tabs ul li.active',
				'declaration' => sprintf(
					'background-color: %1$s;',
					esc_html( $this->props['active_tab_background_color'] )
				),
			) );
		}

		if ( isset( $this->props['inactive_tab_background_color'] ) && '' !== $this->props['inactive_tab_background_color'] ) {
			ET_Builder_Element::set_style( $this->slug, array(
				'selector'    => '%%order_class%% .tabs',
				'declaration' => sprintf(
					'background-color: %1$s;',
					esc_html( $this->props['inactive_tab_background_color'] )
				),
			) );
		}

		if ( isset( $this->props['max_width'] ) && '' !== $this->props['max_width'] ) {
			ET_Builder_Module::set_style( $this->slug, array(
				'selector'    => '%%order_class%%',
				'declaration' => sprintf(
					'max-width: %1$s;',
					esc_html( et_builder_process_range_value( $this->props['max_width'] ) )
				),
			) );
		}
	}

	static function get_global_shortcode_atts() {
		return self::$global_shortcode_atts;
	}

	static function add_child_data( $tabs_data ) {
		self::$tabs_data[] = $tabs_data;
		$tab_id = count( self::$tabs_data );
		$tab_id = $tab_id - 1;// make it be zero based
		return $tab_id;
	}

	function process_bool_shortcode_atts() {
		foreach ( $this->get_fields() as $field_name => $field ) {
			if ( 'yes_no_button' == $field['type'] ) {
				$this->props[ $field_name ] = 'on' == $this->props[ $field_name ] ? true : false;
			}
		}
	}

	function shortcode_atts() {
		$this->process_bool_shortcode_atts();
	}

	function render( $atts, $content = null, $function_name ) {
		$this->props['border_top_color'] = et_get_option( 'accent_color', '#00A8FF' );

		$this->props['terms'] = array();
		foreach ( self::$tabs_data as $tabs_data ) {
			$term = array(
				'name'  => $tabs_data['term_name'],
				'color' => $tabs_data['term_color'],
			);
			$this->props[ 'terms' ][] = $term;
		}

		$this->props['module_class'] = ET_Builder_Element::add_module_order_class( $this->props['module_class'], $this->slug );

		// Adding styling classes to module
		if ( !empty( $this->props['remove_drop_shadow'] ) && 'on' === $this->props['remove_drop_shadow'] ) {
			$this->props['module_class'] = $this->props['module_class'] . ' et_pb_no_drop_shadow';
		}

		// Print styling for general options
		if ( isset( $this->props['border_radius'] ) && '' !== $this->props['border_radius'] ) {
			ET_Builder_Module::set_style( $this->slug, array(
				'selector'    => '%%order_class%%.et_pb_extra_module',
				'declaration' => sprintf(
					'-moz-border-radius: %1$s;
					-webkit-border-radius: %1$s;
					border-radius: %1$s;',
					esc_html( $this->props['border_radius'] )
				),
			) );
		}

		if ( isset( $this->props['post_format_icon_bg_color'] ) && '' !== $this->props['post_format_icon_bg_color'] ) {
			ET_Builder_Module::set_style( $this->slug, array(
				'selector'    => '%%order_class%% .post-thumbnail img',
				'declaration' => sprintf(
					'background-color: %1$s !important;',
					esc_html( $this->props['post_format_icon_bg_color'] )
				),
			) );
		}

		// Overwrite border_color_top attribute if border color is defined by advanced design settings
		if ( isset( $this->props['border_color'] ) && isset( $this->props['use_border_color'] ) && 'on' === $this->props['use_border_color'] ) {
			$this->props['border_top_color'] = $this->props['border_color'];
		}

		if ( is_customize_preview() && $this->props['border_top_color'] === extra_global_accent_color() ) {
			$this->props['module_class'] = $this->props['module_class'] . ' no-term-color-module';
		}

		self::$tabs_data = array(); // reset
		self::$global_shortcode_atts = array(); // reset
	}

}

}


function init_extra_walker_categorydropdown() {
	class Extra_Walker_CategoryDropdown extends Walker_CategoryDropdown {

		function start_el( &$output, $category, $depth = 0, $args = array(), $id = 0 ) {
			$pad = str_repeat( '&nbsp;', $depth * 3 );
			/** This filter is documented in wp-includes/category-template.php */
			$cat_name = apply_filters( 'list_cats', $category->name, $category );
			$output .= "\t<option class=\"level-$depth\" value=\"".$category->term_id."\"";
			$output .= '<%= typeof( data.' . $args['name'] . ' ) !== \'undefined\' && \'' . $category->term_id .'\' === data.' . $args['name'] . ' ? \' selected="selected"\' : \'\' %>';
			$output .= '>';
			$output .= $pad.$cat_name;
			if ( $args['show_count'] )
				$output .= '&nbsp;&nbsp;('. number_format_i18n( $category->count ) .')';
			$output .= "</option>\n";
		}

	}
}

if (!class_exists('ET_Builder_Module_Tabbed_Posts_Tab')) {
class ET_Builder_Module_Tabbed_Posts_Tab extends ET_Builder_Module_Posts {

	function init() {
		$this->template_name = 'module-tabbed-posts-tab';
		$this->name = esc_html__( 'Tab', 'extra' );
		$this->slug = 'et_pb_tabbed_posts_tab';
		$this->type = 'child';
		$this->post_types = array( EXTRA_LAYOUT_POST_TYPE );
		$this->child_title_var = 'category_name';

		$this->advanced_setting_title_text = esc_html__( 'New Tab', 'extra' );
		$this->settings_text               = esc_html__( 'Tab Settings', 'extra' );

		$this->settings_modal_toggles = parent::get_options_toggles();

		$this->custom_css_fields = array(
			'main_post'              => array(
				'label'    => esc_html__( 'Main Post Area', 'et_builder' ),
				'selector' => '.main-post',
			),
			'main_post_hentry'       => array(
				'label'    => esc_html__( 'Main Post Entry', 'et_builder' ),
				'selector' => '.main-post .hentry',
			),
			'main_post_title'        => array(
				'label'    => esc_html__( 'Main Post Title', 'et_builder' ),
				'selector' => '.main-post .hentry h2 a',
			),
			'main_post_meta'         => array(
				'label'    => esc_html__( 'Main Post Meta', 'et_builder' ),
				'selector' => '.main-post .hentry .post-meta',
			),
			'main_post_meta_icon'    => array(
				'label'    => esc_html__( 'Main Post Meta Icons (Rating &amp; Comment)', 'et_builder' ),
				'selector' => '.main-post .hentry .post-meta .post-meta-icon:before',
			),
			'main_post_excerpt'      => array(
				'label'    => esc_html__( 'Main Post Excerpt', 'et_builder' ),
				'selector' => '.main-post .hentry .excerpt',
			),
			'main_post_overlay'      => array(
				'label'    => esc_html__( 'Main Post Overlay', 'et_builder' ),
				'selector' => '.main-post .hentry .et_pb_extra_overlay',
			),
			'main_post_overlay_icon' => array(
				'label'    => esc_html__( 'Main Post Overlay Icon', 'et_builder' ),
				'selector' => '.main-post .hentry .et_pb_extra_overlay:before',
			),
			'posts_list'             => array(
				'label'    => esc_html__( 'Posts List Area', 'et_builder' ),
				'selector' => '.posts-list',
			),
			'posts_list_hentry'      => array(
				'label'    => esc_html__( 'Posts List Entry', 'et_builder' ),
				'selector' => '.posts-list li',
			),
			'posts_list_title'       => array(
				'label'    => esc_html__( 'Posts List Title', 'et_builder' ),
				'selector' => '.posts-list li h3 a',
			),
			'posts_list_meta'        => array(
				'label'    => esc_html__( 'Posts List Meta', 'et_builder' ),
				'selector' => '.posts-list li .post-meta',
			),
			'posts_list_meta_icon'   => array(
				'label'    => esc_html__( 'Posts List Meta Icon', 'et_builder' ),
				'selector' => '.posts-list li .post-meta .post-meta-icon:before',
			),
			'posts_list_thumbnail'   => array(
				'label'    => esc_html__( 'Posts List Thumbnail', 'et_builder' ),
				'selector' => '.posts-list .post-thumbnail img',
			),
		);
	}

	function get_fields() {
		$fields = array(
			'category_id'                 => array(
				'label'           => esc_html__( 'Category', 'extra' ),
				'type'            => 'select',
				'description'     => esc_html__( 'Choose a category.', 'extra' ),
				'options'         => $this->get_categories_list(),
				'default'         => '-1',
				'option_category' => 'configuration',
				'toggle_slug'     => 'main_content',
			),
			'category_name'               => array(
				'label' => '',
				'type'  => 'hidden',
			),
			'display_featured_posts_only' => array(
				'label'           => esc_html__( 'Display Featured Posts Only', 'extra' ),
				'type'            => 'yes_no_button',
				'options'         => array(
					'off' => esc_html__( 'No', 'extra' ),
					'on'  => esc_html__( 'Yes', 'extra' ),
				),
				'description'     => esc_html__( 'Only display featured posts.', 'extra' ),
				'option_category' => 'configuration',
				'toggle_slug'     => 'main_content',
			),
			'ignore_displayed_posts' => array(
				'label'           => esc_html__( 'Ignore Displayed Posts', 'extra' ),
				'type'            => 'yes_no_button',
				'options'         => array(
					'off' => esc_html__( 'No', 'extra' ),
					'on'  => esc_html__( 'Yes', 'extra' ),
				),
				'description'     => esc_html__( 'Do not display posts that have been displayed on previous modules.', 'extra' ),
				'option_category' => 'configuration',
				'toggle_slug'     => 'main_content',
			),
		);

		return $fields;
	}

	function get_categories_list() {
		$cats_array = get_categories( 'hide_empty=0' );
		$output = array( '-1' => esc_html__( 'All', 'extra' ) );

		if ( ! empty( $cats_array ) ) {
			foreach( $cats_array as $category ) {
				$id_string = (string) $category->term_id;
				$output[$id_string] = $category->name;
			}
		}
		
		return $output;
	}

	function render( $atts, $content = null, $function_name ) {
		$this->_process_shortcode_atts_category_id();

		if ( !empty( $this->props['category_id'] ) ) {
			$categories = array_map( 'absint', explode( ',', $this->props['category_id'] ) );

			$term = get_term( absint( $categories[0] ), 'category' );

			if ( !empty( $term ) && empty( $term->errors ) ) {
				$this->props['term_name'] = $term->name;
				$this->props['term_color'] = extra_get_category_color( $term->term_id );
			}
		}

		if ( empty( $term ) || ! empty( $term->errors ) ) {
			$this->props['term_name'] = esc_html__( 'All', 'extra' );
			$this->props['term_color'] = et_builder_accent_color();
		}

		$this->props['module_class'] = ET_Builder_Element::add_module_order_class( '', $this->slug );

		$this->props['tab_id'] = ET_Builder_Module_Tabbed_Posts::add_child_data( $this->props );

		$this->props['order'] = 'desc';
		$this->props['orderby'] = 'date';

		$this->props = array_merge( $this->props, ET_Builder_Module_Tabbed_Posts::get_global_shortcode_atts() );

		return parent::render( $atts, $content, $function_name );
	}

}

}


if (!class_exists('ET_Builder_Module_Posts_Carousel')) {
class ET_Builder_Module_Posts_Carousel extends ET_Builder_Module_Posts {

	function init() {
		$this->template_name = 'module-posts-carousel';
		$this->name = esc_html__( 'Posts Carousel', 'extra' );
		$this->slug = 'et_pb_posts_carousel';
		$this->post_types = array( EXTRA_LAYOUT_POST_TYPE );

		$this->main_css_element = '%%order_class%%';

		$this->settings_modal_toggles = $this->get_options_toggles();

		$this->advanced_fields = array(
			'fonts'                 => array(
				'header'    => $this->set_frequent_advanced_options( 'header' ),
				'subheader' => $this->set_frequent_advanced_options( 'subheader' ),
				'title'     => array(
					'label'          => esc_html__( 'Title', 'et_builder' ),
					'css'            => array(
						'main'     => "{$this->main_css_element} .hentry h3 a",
						'important' => 'all',
					),
					'letter_spacing' => array(
						'range_settings' => array(
							'min'  => 0,
							'max'  => 30,
							'step' => 0.1,
						),
					),
				),
				'meta'      => array(
					'label' => esc_html__( 'Meta', 'et_builder' ),
					'css'   => array(
						'main' => "{$this->main_css_element} .post-meta",
					),
				),
			),
			'background'            => array(
				'css'      => array(
					'main' => "{$this->main_css_element}",
				),
				'settings' => array(
					'color' => 'alpha',
				),
			),
			'border'                => array(
				'css' => array(
					'main'      => "{$this->main_css_element}",
					'important' => 'all',
				),
			),
			'custom_margin_padding' => array(),
		);

		$this->custom_css_fields = array(
			'head'              => array(
				'label'    => esc_html__( 'Module Head', 'et_builder' ),
				'selector' => '.module-head',
			),
			'header'            => array(
				'label'    => esc_html__( 'Module Header', 'et_builder' ),
				'selector' => '.module-head h1',
			),
			'subheader'         => array(
				'label'    => esc_html__( 'Module Subheader', 'et_builder' ),
				'selector' => '.module-head .module-filter',
			),
			'post_hentry'       => array(
				'label'    => esc_html__( 'Post Entry', 'et_builder' ),
				'selector' => '.hentry',
			),
			'post_title'        => array(
				'label'    => esc_html__( 'Post Title', 'et_builder' ),
				'selector' => '.hentry h3 a',
			),
			'post_meta'         => array(
				'label'    => esc_html__( 'Post Meta', 'et_builder' ),
				'selector' => '.hentry .post-meta',
			),
			'post_overlay'      => array(
				'label'    => esc_html__( 'Post Overlay', 'et_builder' ),
				'selector' => '.hentry .et_pb_extra_overlay',
			),
			'post_overlay_icon' => array(
				'label'    => esc_html__( 'Post Overlay Icon', 'et_builder' ),
				'selector' => '.hentry .et_pb_extra_overlay:before',
			),
			'nav'               => array(
				'label'    => esc_html__( 'Nav', 'et_builder' ),
				'selector' => '.et-pb-slider-arrows a',
			),
			'nav_hover'         => array(
				'label'    => esc_html__( 'Nav Hover', 'et_builder' ),
				'selector' => '.et-pb-slider-arrows a:hover',
			),
			'nav_icon'          => array(
				'label'    => esc_html__( 'Nav Icon', 'et_builder' ),
				'selector' => '.et-pb-slider-arrows a:before',
			),
			'nav_icon_hover'    => array(
				'label'    => esc_html__( 'Nav Icon Hover', 'et_builder' ),
				'selector' => '.et-pb-slider-arrows a:hover:before',
			),
		);
	}

	function set_additional_fields() {
		return array(
			'enable_autoplay' => array(
				'off',
				'add_default_setting',
			),
			'autoplay_speed'  => array(
				'5',
				'add_default_setting',
			),
			'max_title_characters'  => array(
				40,
				'add_default_setting',
			),
		);
	}

	function get_fields() {
		$fields = parent::get_fields();

		$_fields = array();

		$new_fields = array(
			'enable_autoplay'    => array(
				'label'           => esc_html__( 'Enable Autoplay', 'extra' ),
				'type'            => 'yes_no_button',
				'options'         => array(
					'off' => esc_html__( 'No', 'extra' ),
					'on'  => esc_html__( 'Yes', 'extra' ),
				),
				'affects'         => array( 'autoplay_speed' ),
				'description'     => esc_html__( 'Turn the autoplay feature on or off.', 'extra' ),
				'priority'        => 6,
				'option_category' => 'configuration',
				'tab_slug'        => 'custom_css',
				'toggle_slug'     => 'autoplay',
			),
			'autoplay_speed'     => array(
				'label'           => esc_html__( 'Autoplay Speed', 'extra' ),
				'type'            => 'text',
				'depends_show_if' => "on",
				'description'     => esc_html__( 'The speed, in seconds, in which it will auto rotate to the next slide.', 'extra' ),
				'priority'        => 6,
				'option_category' => 'configuration',
				'tab_slug'        => 'custom_css',
				'toggle_slug'     => 'autoplay',
			),
			'max_title_characters'=> array(
				'label'           => esc_html__( 'Max. Title Characters', 'extra' ),
				'type'            => 'text',
				'description'     => esc_html__( 'Length of the title need to be limited to prevent inappropriate caption height in narrow column', 'extra' ),
				'option_category' => 'configuration',
				'toggle_slug'     => 'main_content',
			),
			'nav_arrow_color'    => array(
				'label'        => esc_html__( 'Nav Arrow Color', 'et_builder' ),
				'type'         => 'color-alpha',
				'custom_color' => true,
				'tab_slug'     => 'advanced',
				'toggle_slug'  => 'navigation',
			),
			'nav_arrow_bg_color' => array(
				'label'        => esc_html__( 'Nav Arrow Background Color', 'et_builder' ),
				'type'         => 'color-alpha',
				'custom_color' => true,
				'tab_slug'     => 'advanced',
				'toggle_slug'  => 'navigation',
			),
		);

		foreach ( $fields as $field_key => $field ) {
			$_fields[ $field_key ] = $field;

			if ( 'order' == $field_key ) {
				$_fields[ 'enable_autoplay' ] = $new_fields['enable_autoplay'];
				$_fields[ 'autoplay_speed' ] = $new_fields['autoplay_speed'];
			}

			$_fields[ 'max_title_characters' ] = $new_fields['max_title_characters'];
			$_fields[ 'nav_arrow_color' ]    = $new_fields['nav_arrow_color'];
			$_fields[ 'nav_arrow_bg_color' ] = $new_fields['nav_arrow_bg_color'];
		}

		$fields = $_fields;

		$fields = $this->unset_fields( $fields );

		return $fields;
	}

	function before_render() {
		if ( isset( $this->props['nav_arrow_color'] ) && '' !== $this->props['nav_arrow_color'] ) {
			ET_Builder_Element::set_style( $this->slug, array(
				'selector'    => '%%order_class%% .et-pb-slider-arrows a:before',
				'declaration' => sprintf(
					'color: %1$s;',
					esc_html( $this->props['nav_arrow_color'] )
				),
			) );
		}

		if ( isset( $this->props['nav_arrow_bg_color'] ) && '' !== $this->props['nav_arrow_bg_color'] ) {
			ET_Builder_Element::set_style( $this->slug, array(
				'selector'    => '%%order_class%% .et-pb-slider-arrows a',
				'declaration' => sprintf(
					'background-color: %1$s;',
					esc_html( $this->props['nav_arrow_bg_color'] )
				),
			) );
		}
	}

	function unset_fields( $fields ) {
		unset( $fields['show_thumbnails'] );
		unset( $fields['show_author'] );
		unset( $fields['show_categories'] );
		unset( $fields['show_rating'] );
		unset( $fields['show_comments'] );
		unset( $fields['post_format_icon_bg_color'] );
		return $fields;
	}

	function _pre_wp_query( $args ) {
		$args = parent::_pre_wp_query( $args );

		$thumbnail_meta_query = array(
			'key'     => '_thumbnail_id',
			'compare' => 'EXISTS',
		);

		if ( empty( $args['meta_query'] ) ) {
			$args['meta_query'] = array();
		}

		$args['meta_query'][] = $thumbnail_meta_query;

		$args['posts_per_page'] = !empty( $this->props['posts_per_page'] ) && is_numeric( $this->props['posts_per_page'] ) ? $this->props['posts_per_page'] : -1;

		return $args;
	}

	function get_options_toggles() {
		$options_toggles = parent::get_options_toggles();

		$options_toggles['advanced']['toggles']['navigation'] = esc_html__( 'Navigation', 'et_builder');
		$options_toggles['custom_css']['toggles']['autoplay'] = array(
			'title'    => esc_html__( 'Autoplay', 'et_builder' ),
			'priority' => 90,
		);

		return $options_toggles;
	}

}

}


if (!class_exists('ET_Builder_Module_Featured_Posts_Slider')) {
class ET_Builder_Module_Featured_Posts_Slider extends ET_Builder_Module_Posts_Carousel {

	function init() {
		$this->template_name = 'module-featured-posts-slider';
		$this->name = esc_html__( 'Featured Posts Slider', 'extra' );
		$this->slug = 'et_pb_featured_posts_slider';
		$this->post_types = array( EXTRA_LAYOUT_POST_TYPE );

		$this->main_css_element = '%%order_class%%';

		$this->settings_modal_toggles = parent::get_options_toggles();
		$this->settings_modal_toggles['advanced']['toggles']['caption'] = esc_html__( 'Caption', 'et_builder');

		$this->advanced_fields = array(
			'fonts'                 => array(
				'title' => array(
					'label'          => esc_html__( 'Title', 'et_builder' ),
					'css'            => array(
						'main'     => "{$this->main_css_element} .hentry h3 a",
						'important' => 'all',
					),
					'letter_spacing' => array(
						'range_settings' => array(
							'min'  => 0,
							'max'  => 30,
							'step' => 0.1,
						),
					),
				),
				'meta'  => array(
					'label' => esc_html__( 'Meta', 'et_builder' ),
					'css'   => array(
						'main' => "{$this->main_css_element} .hentry .post-meta, {$this->main_css_element} .hentry .post-meta .comment-bubble:before, {$this->main_css_element} .hentry .post-meta .rating-star:before",
					),
				),
			),
			'custom_margin_padding' => array(),
		);

		$this->custom_css_fields = array(
			'post_hentry'            => array(
				'label'    => esc_html__( 'Post Entry', 'et_builder' ),
				'selector' => '.hentry',
			),
			'post_caption'           => array(
				'label'    => esc_html__( 'Post Caption', 'et_builder' ),
				'selector' => '.hentry .post-content-box',
			),
			'post_title'             => array(
				'label'    => esc_html__( 'Post Title', 'et_builder' ),
				'selector' => '.hentry h3 a',
			),
			'post_meta'              => array(
				'label'    => esc_html__( 'Post Meta', 'et_builder' ),
				'selector' => '.hentry .post-meta',
			),
			'post_meta_icon'         => array(
				'label'    => esc_html__( 'Post Meta Icons (Rating &amp; Comment)', 'et_builder' ),
				'selector' => '.hentry .post-meta .post-meta-icon:before',
			),
			'pagination_item'        => array(
				'label'    => esc_html__( 'Pagination Item', 'et_builder' ),
				'selector' => '.slick-dots li button',
			),
			'pagination_item_active' => array(
				'label'    => esc_html__( 'Pagination Item Active', 'et_builder' ),
				'selector' => '.slick-dots li.slick-active button',
			),
			'nav'                    => array(
				'label'    => esc_html__( 'Nav', 'et_builder' ),
				'selector' => '.et-pb-slider-arrows a',
			),
			'nav_hover'              => array(
				'label'    => esc_html__( 'Nav Hover', 'et_builder' ),
				'selector' => '.et-pb-slider-arrows a:hover',
			),
			'nav_icon'               => array(
				'label'    => esc_html__( 'Nav Icon', 'et_builder' ),
				'selector' => '.et-pb-slider-arrows a:before',
			),
			'nav_icon_hover'         => array(
				'label'    => esc_html__( 'Nav Icon Hover', 'et_builder' ),
				'selector' => '.et-pb-slider-arrows a:hover:before',
			),
		);
	}

	function set_additional_fields() {
		return array(
			'enable_autoplay' => array(
				'off',
				'add_default_setting',
			),
			'autoplay_speed'  => array(
				'5',
				'add_default_setting',
			),
			'max_title_characters'  => array(
				50,
				'add_default_setting',
			),
		);
	}

	function get_fields() {
		$fields = parent::get_fields();

		$fields['posts_per_page']['default'] = 6;

		$fields['slide_caption_background'] = array(
			'label'        => esc_html__( 'Caption Background Color', 'et_builder' ),
			'type'         => 'color-alpha',
			'custom_color' => true,
			'tab_slug'     => 'advanced',
			'toggle_slug'  => 'caption',
		);

		$fields = $this->unset_fields( $fields );

		return $fields;
	}

	function before_render() {
		if ( isset( $this->props['slide_caption_background'] ) && '' !== $this->props['slide_caption_background'] ) {
			ET_Builder_Element::set_style( $this->slug, array(
				'selector'    => '%%order_class%%.featured-posts-slider-module .carousel-item .post-content-box',
				'declaration' => sprintf(
					'background-color: %1$s;',
					esc_html( $this->props['slide_caption_background'] )
				),
			) );
		}

		// Print styling for general options
		if ( isset( $this->props['border_radius'] ) && '' !== $this->props['border_radius'] ) {
			ET_Builder_Module::set_style( $this->slug, array(
				'selector'    => '%%order_class%%.et_pb_extra_module .hentry',
				'declaration' => sprintf(
					'-moz-border-radius: %1$s;
					-webkit-border-radius: %1$s;
					border-radius: %1$s;',
					esc_html( $this->props['border_radius'] )
				),
			) );
		}

		if ( isset( $this->props['nav_arrow_color'] ) && '' !== $this->props['nav_arrow_color'] ) {
			ET_Builder_Element::set_style( $this->slug, array(
				'selector'    => '%%order_class%% .et-pb-slider-arrows .et-pb-arrow-prev:before, %%order_class%% .et-pb-slider-arrows .et-pb-arrow-next:before',
				'declaration' => sprintf(
					'color: %1$s;',
					esc_html( $this->props['nav_arrow_color'] )
				),
			) );
		}

		if ( isset( $this->props['nav_arrow_bg_color'] ) && '' !== $this->props['nav_arrow_bg_color'] ) {
			ET_Builder_Element::set_style( $this->slug, array(
				'selector'    => '%%order_class%% .et-pb-slider-arrows .et-pb-arrow-prev, %%order_class%% .et-pb-slider-arrows .et-pb-arrow-next',
				'declaration' => sprintf(
					'background-color: %1$s;',
					esc_html( $this->props['nav_arrow_bg_color'] )
				),
			) );
		}
	}

	function unset_fields( $fields ) {
		unset( $fields['heading_style'] );
		unset( $fields['hover_overlay_color'] );
		unset( $fields['hover_overlay_icon'] );
		unset( $fields['hover_overlay_icon_color'] );
		unset( $fields['post_format_icon_bg_color'] );
		unset( $fields['show_thumbnails'] );
		return $fields;
	}

	function _pre_wp_query( $args ) {
		$args = parent::_pre_wp_query( $args );

		$args['posts_per_page'] = !empty( $this->props['posts_per_page'] ) && is_numeric( $this->props['posts_per_page'] ) ? $this->props['posts_per_page'] : 6;

		return $args;
	}

}

}


if (!class_exists('ET_Builder_Module_Posts_Blog_Feed')) {
class ET_Builder_Module_Posts_Blog_Feed extends ET_Builder_Module_Posts {

	function init() {
		$this->template_name = 'module-posts-blog-feed';
		$this->name = esc_html__( 'Blog Feed Standard', 'extra' );
		$this->slug = 'et_pb_posts_blog_feed_standard';
		$this->post_types = array( EXTRA_LAYOUT_POST_TYPE );

		$this->main_css_element = '%%order_class%%';

		$this->settings_modal_toggles = $this->get_options_toggles();

		$this->advanced_fields = array(
			'fonts'                 => array(
				'header' => $this->set_frequent_advanced_options( 'header' ),
				'title'  => array(
					'label'          => esc_html__( 'Title', 'et_builder' ),
					'css'            => array(
						'main'     => "{$this->main_css_element} .hentry h2 a",
						'important' => 'all',
					),
					'letter_spacing' => array(
						'range_settings' => array(
							'min'  => 0,
							'max'  => 30,
							'step' => 0.1,
						),
					),
				),
				'meta'   => array(
					'label' => esc_html__( 'Meta', 'et_builder' ),
					'css'   => array(
						'main' => "{$this->main_css_element} .hentry .post-meta, {$this->main_css_element} .hentry .post-meta .comment-bubble:before, {$this->main_css_element} .hentry .post-meta .rating-star:before",
					),
				),
				'body'   => array(
					'label' => esc_html__( 'Body', 'et_builder' ),
					'css'   => array(
						'main' => "{$this->main_css_element} .hentry .excerpt p",
					),
				),
			),
			'button'                => array(
				'read_more' => array(
					'label' => esc_html__( 'Read More Button', 'et_builder' ),
					'css'   => array(
						'main' => "{$this->main_css_element} .hentry .read-more-button",
					),
				),
			),
			'background'            => array(
				'css'      => array(
					'main' => "{$this->main_css_element}, {$this->main_css_element} .module-head",
				),
				'settings' => array(
					'color' => 'alpha',
				),
			),
			'custom_margin_padding' => array(),
		);

		$this->custom_css_fields = array(
			'head'                               => array(
				'label'    => esc_html__( 'Module Head', 'et_builder' ),
				'selector' => '.module-head',
			),
			'header'                             => array(
				'label'    => esc_html__( 'Module Header', 'et_builder' ),
				'selector' => '.module-head h1',
			),
			'post_hentry'                        => array(
				'label'    => esc_html__( 'Post Entry', 'et_builder' ),
				'selector' => '.hentry',
			),
			'post_title'                         => array(
				'label'    => esc_html__( 'Post Title', 'et_builder' ),
				'selector' => '.hentry h2 a',
			),
			'post_meta'                          => array(
				'label'    => esc_html__( 'Post Meta', 'et_builder' ),
				'selector' => '.hentry .post-meta',
			),
			'post_meta_icon'                     => array(
				'label'    => esc_html__( 'Post Meta Icons (Rating &amp; Comment)', 'et_builder' ),
				'selector' => '.hentry .post-meta .post-meta-icon:before',
			),
			'post_excerpt'                       => array(
				'label'    => esc_html__( 'Post Excerpt', 'et_builder' ),
				'selector' => '.hentry .excerpt',
			),
			'post_read_more'                     => array(
				'label'    => esc_html__( 'Post Read More', 'et_builder' ),
				'selector' => '.hentry .read-more-button',
			),
			'post_read_more_icon'                => array(
				'label'    => esc_html__( 'Post Read More Icon', 'et_builder' ),
				'selector' => '.hentry .read-more-button:after',
			),
			'post_featured_image'                => array(
				'label'    => esc_html__( 'Post Featured Image', 'et_builder' ),
				'selector' => '.hentry .featured-image img',
			),
			'post_overlay'                       => array(
				'label'    => esc_html__( 'Post Overlay', 'et_builder' ),
				'selector' => '.hentry .et_pb_extra_overlay',
			),
			'post_overlay_icon'                  => array(
				'label'    => esc_html__( 'Post Overlay Icon', 'et_builder' ),
				'selector' => '.hentry .et_pb_extra_overlay:before',
			),
			'post_review_score_bar'              => array(
				'label'    => esc_html__( 'Post Review Score Bar', 'et_builder' ),
				'selector' => '.hentry .score-bar',
			),
			'post_format_gallery_nav'            => array(
				'label'    => esc_html__( 'Post Format Gallery Nav', 'et_builder' ),
				'selector' => '.hentry .et-pb-slider-arrows a',
			),
			'post_format_gallery_nav_icon'       => array(
				'label'    => esc_html__( 'Post Format Gallery Nav Icon', 'et_builder' ),
				'selector' => '.hentry .et-pb-slider-arrows a:before',
			),
			'post_format_gallery_nav_hover'      => array(
				'label'    => esc_html__( 'Post Format Gallery Nav Hover', 'et_builder' ),
				'selector' => '.hentry .et-pb-slider-arrows a:hover',
			),
			'post_format_gallery_nav_hover_icon' => array(
				'label'    => esc_html__( 'Post Format Gallery Nav Icon Hover', 'et_builder' ),
				'selector' => '.hentry .et-pb-slider-arrows a:hover:before',
			),
			'post_format_audio_wrapper'          => array(
				'label'    => esc_html__( 'Post Format Audio Wrapper', 'et_builder' ),
				'selector' => '.hentry .audio-wrapper',
			),
			'post_format_audio_player'           => array(
				'label'    => esc_html__( 'Post Format Audio Player', 'et_builder' ),
				'selector' => '.hentry .mejs-container',
			),
			'post_format_link_background'        => array(
				'label'    => esc_html__( 'Post Format Link Background', 'et_builder' ),
				'selector' => '.hentry .link-format',
			),
			'post_format_quote_background'       => array(
				'label'    => esc_html__( 'Post Format Quote Background', 'et_builder' ),
				'selector' => '.hentry .quote-format',
			),
			'pagination_background'              => array(
				'label'    => esc_html__( 'Pagination Background', 'et_builder' ),
				'selector' => '.pagination',
			),
			'pagination_item'                    => array(
				'label'    => esc_html__( 'Pagination Item', 'et_builder' ),
				'selector' => '.pagination li',
			),
			'pagination_item_active'             => array(
				'label'    => esc_html__( 'Pagination Item Active', 'et_builder' ),
				'selector' => '.pagination li.active',
			),
		);
	}

	function set_additional_fields() {
		return array(
			'date_format' => array(
				'M j, Y',
				'add_default_setting',
			),
		);
	}

	function get_fields() {
		$fields = parent::get_fields();

		$new_fields = array(
			'feed_title'                 => array(
				'label'           => esc_html__( 'Blog Feed Title', 'extra' ),
				'type'            => 'text',
				'description'     => esc_html__( 'This is an optional title to display for this module.', 'extra' ),
				'priority'        => 2,
				'option_category' => 'configuration',
				'toggle_slug'     => 'main_text',
			),
			'posts_per_page'             => array(
				'label'           => esc_html__( 'Posts Per Page', 'extra' ),
				'type'            => 'text',
				'description'     => esc_html__( 'The number of posts shown per page.', 'extra' ),
				'priority'        => 3,
				'option_category' => 'configuration',
				'toggle_slug'     => 'main_content',
			),
			'show_pagination'            => array(
				'label'           => esc_html__( 'Show Pagination', 'extra' ),
				'type'            => 'yes_no_button',
				'options'         => array(
					'on'  => esc_html__( 'Yes', 'extra' ),
					'off' => esc_html__( 'No', 'extra' ),
				),
				'description'     => esc_html__( 'Turn pagination on or off.', 'extra' ),
				'priority'        => 5,
				'option_category' => 'configuration',
				'toggle_slug'     => 'elements',
			),
			'show_author'                => array(
				'label'           => esc_html__( 'Show Author', 'extra' ),
				'type'            => 'yes_no_button',
				'options'         => array(
					'on'  => esc_html__( 'Yes', 'extra' ),
					'off' => esc_html__( 'No', 'extra' ),
				),
				'description'     => esc_html__( "Turn the display of each post's author on or off.", 'extra' ),
				'priority'        => 5,
				'option_category' => 'configuration',
				'toggle_slug'     => 'elements',
			),
			'show_categories'            => array(
				'label'           => esc_html__( 'Show Categories', 'extra' ),
				'type'            => 'yes_no_button',
				'options'         => array(
					'on'  => esc_html__( 'Yes', 'extra' ),
					'off' => esc_html__( 'No', 'extra' ),
				),
				'description'     => esc_html__( "Turn the display of each post's categories on or off.", 'extra' ),
				'priority'        => 5,
				'option_category' => 'configuration',
				'toggle_slug'     => 'elements',
			),
			'show_featured_image'        => array(
				'label'           => esc_html__( 'Show Featured Image', 'extra' ),
				'type'            => 'yes_no_button',
				'options'         => array(
					'on'  => esc_html__( 'Yes', 'extra' ),
					'off' => esc_html__( 'No', 'extra' ),
				),
				'description'     => esc_html__( "Turn the display of each post's featured image on or off.", 'extra' ),
				'priority'        => 5,
				'option_category' => 'configuration',
				'toggle_slug'     => 'elements',
			),
			'content_length'             => array(
				'label'           => esc_html__( 'Content', 'extra' ),
				'type'            => 'select',
				'options'         => array(
					'excerpt' => esc_html__( 'Show Excerpt', 'extra' ),
					'full'    => esc_html__( "Show Full Content", 'extra' ),
				),
				'affects'         => array(
					'show_more',
				),
				'description'     => esc_html__( "Display the post's exceprt or full content. If full content, then it will truncate to the more tag if used.", 'extra' ),
				'option_category' => 'configuration',
				'toggle_slug'     => 'main_content',
			),
			'show_more'                  => array(
				'label'           => esc_html__( 'Show Read More Button', 'extra' ),
				'type'            => 'yes_no_button',
				'options'         => array(
					'on'  => esc_html__( 'Yes', 'extra' ),
					'off' => esc_html__( 'No', 'extra' ),
				),
				'depends_show_if' => 'excerpt',
				'description'     => esc_html__( 'Here you can define whether to show "read more" link after the excerpts or not.', 'extra' ),
				'option_category' => 'configuration',
				'toggle_slug'     => 'elements',
			),
			'show_date'                  => array(
				'label'           => esc_html__( 'Show Date', 'extra' ),
				'type'            => 'yes_no_button',
				'options'         => array(
					'on'  => esc_html__( 'Yes', 'extra' ),
					'off' => esc_html__( 'No', 'extra' ),
				),
				'affects'         => array( 'date_format' ),
				'description'     => esc_html__( "Turn the dispay of each post's date on or off.", 'extra' ),
				'priority'        => 5,
				'option_category' => 'configuration',
				'toggle_slug'     => 'elements',
			),
			'date_format'                => array(
				'label'               => esc_html__( 'Date Format', 'extra' ),
				'type'                => 'text',
				'depends_show_if_not' => "off",
				'description'         => esc_html__( 'The format for the date display in PHP date() format', 'extra' ),
				'option_category'     => 'configuration',
				'toggle_slug'         => 'elements',
			),
			'pagination_color'           => array(
				'label'        => esc_html__( 'Pagination Color', 'et_builder' ),
				'type'         => 'color-alpha',
				'custom_color' => true,
				'tab_slug'     => 'advanced',
				'toggle_slug'  => 'pagination',
				'priority'     => 28,
			),
			'pagination_bg_color'        => array(
				'label'        => esc_html__( 'Pagination Background Color', 'et_builder' ),
				'type'         => 'color-alpha',
				'custom_color' => true,
				'tab_slug'     => 'advanced',
				'toggle_slug'  => 'pagination',
				'priority'     => 29,
			),
			'pagination_active_color'    => array(
				'label'        => esc_html__( 'Pagination Active Color', 'et_builder' ),
				'type'         => 'color-alpha',
				'custom_color' => true,
				'tab_slug'     => 'advanced',
				'toggle_slug'  => 'pagination',
				'priority'     => 30,
			),
			'pagination_active_bg_color' => array(
				'label'        => esc_html__( 'Pagination Active Background Color', 'et_builder' ),
				'type'         => 'color-alpha',
				'custom_color' => true,
				'tab_slug'     => 'advanced',
				'toggle_slug'  => 'pagination',
				'priority'     => 31,
			),
		);

		// unset parent version of this field in favor of local in $new_fields
		unset( $fields['posts_per_page'] );
		unset( $fields['show_thumbnails'] );
		unset( $fields['post_format_icon_bg_color'] );
		unset( $fields['heading_style'] );
		unset( $fields['heading_primary'] );
		unset( $fields['heading_sub'] );

		$fields = array_merge( $new_fields, $fields );

		return $fields;
	}

	function enqueue_scripts() {
		wp_enqueue_style( 'wp-mediaelement' );
		wp_enqueue_script( 'wp-mediaelement' );
		et_extra_enqueue_google_maps_api();
	}

	function shortcode_atts() {
		global $et_column_type;

		parent::shortcode_atts();

		$this->enqueue_scripts();

		$this->props['_et_column_type'] = $et_column_type;
		$this->props['blog_feed_module_type'] = 'standard';

		if ( '' === $this->props['posts_per_page'] ) {
			$this->props['posts_per_page'] = 5;
		}
	}

	function get_options_toggles() {
		$options_toggles = parent::get_options_toggles();
		$options_toggles['general']['toggles']['main_text'] = esc_html__( 'Text', 'et_builder' );
		$options_toggles['advanced']['toggles']['pagination'] = esc_html__( 'Pagination', 'et_builder');

		return $options_toggles;
	}

	function before_render() {
		if ( isset( $this->props['read_more_background'] ) && '' !== $this->props['read_more_background'] ) {
			ET_Builder_Module::set_style( $this->slug, array(
				'selector'    => '%%order_class%% .read-more-button',
				'declaration' => sprintf(
					'background-color: %1$s;',
					esc_html( $this->props['read_more_background'] )
				),
			) );
		}

		if ( isset( $this->props['pagination_color'] ) && '' !== $this->props['pagination_color'] ) {
			ET_Builder_Element::set_style( $this->slug, array(
				'selector'    => '%%order_class%%.paginated .pagination li, %%order_class%%.paginated .pagination li a, %%order_class%%.paginated .pagination li a:before',
				'declaration' => sprintf(
					'color: %1$s;',
					esc_html( $this->props['pagination_color'] )
				),
			) );
		}

		if ( isset( $this->props['pagination_bg_color'] ) && '' !== $this->props['pagination_bg_color'] ) {
			ET_Builder_Element::set_style( $this->slug, array(
				'selector'    => '%%order_class%%.paginated .pagination li, %%order_class%%.paginated .pagination li a',
				'declaration' => sprintf(
					'background-color: %1$s;',
					esc_html( $this->props['pagination_bg_color'] )
				),
			) );
		}

		if ( isset( $this->props['pagination_active_color'] ) && '' !== $this->props['pagination_active_color'] ) {
			ET_Builder_Element::set_style( $this->slug, array(
				'selector'    => '%%order_class%%.paginated .pagination li.active a',
				'declaration' => sprintf(
					'color: %1$s;',
					esc_html( $this->props['pagination_active_color'] )
				),
			) );
		}

		if ( isset( $this->props['pagination_active_bg_color'] ) && '' !== $this->props['pagination_active_bg_color'] ) {
			ET_Builder_Element::set_style( $this->slug, array(
				'selector'    => '%%order_class%%.paginated .pagination li.active a',
				'declaration' => sprintf(
					'background-color: %1$s;',
					esc_html( $this->props['pagination_active_bg_color'] )
				),
			) );
		}
	}

	function _pre_wp_query( $args ) {
		$args = parent::_pre_wp_query( $args );

		$args['posts_per_page'] = is_numeric( $this->props['posts_per_page'] ) ? $this->props['posts_per_page'] : 5;

		return $args;
	}

}

}


if (!class_exists('ET_Builder_Module_Posts_Blog_Feed_Masonry')) {
class ET_Builder_Module_Posts_Blog_Feed_Masonry extends ET_Builder_Module_Posts_Blog_Feed {

	function init() {
		$this->template_name = 'module-posts-blog-feed';
		$this->name = esc_html__( 'Blog Feed Masonry', 'extra' );
		$this->slug = 'et_pb_posts_blog_feed_masonry';
		$this->post_types = array( EXTRA_LAYOUT_POST_TYPE );

		$this->main_css_element = '%%order_class%%';

		$this->settings_modal_toggles = parent::get_options_toggles();
		$this->settings_modal_toggles['advanced']['toggles']['post'] = esc_html__( 'Post', 'et_builder');

		$this->advanced_fields = array(
			'fonts'                 => array(
				'title' => array(
					'label'          => esc_html__( 'Title', 'et_builder' ),
					'css'            => array(
						'main'      => "{$this->main_css_element} .hentry h2 a",
						'important' => 'all',
					),
					'letter_spacing' => array(
						'range_settings' => array(
							'min'  => 0,
							'max'  => 30,
							'step' => 0.1,
						),
					),
				),
				'meta'  => array(
					'label' => esc_html__( 'Meta', 'et_builder' ),
					'css'   => array(
						'main' => "{$this->main_css_element} .hentry .post-meta, {$this->main_css_element} .hentry .post-meta .comment-bubble:before, {$this->main_css_element} .hentry .post-meta .rating-star:before",
					),
				),
				'body'  => array(
					'label' => esc_html__( 'Body', 'et_builder' ),
					'css'   => array(
						'main' => "{$this->main_css_element} .hentry p",
					),
				),
			),
			'border'                => array(
				'css' => array(
					'main' => ".posts-blog-feed-module.masonry{$this->main_css_element} .hentry",
				),
			),
			'button'                => array(
				'read_more' => array(
					'label' => esc_html__( 'Read More Button', 'et_builder' ),
					'css'   => array(
						'main' => "{$this->main_css_element} .hentry .read-more-button",
					),
				),
			),
			'custom_margin_padding' => array(),
		);

		$this->custom_css_fields = array(
			'post_hentry'                        => array(
				'label'    => esc_html__( 'Post Entry', 'et_builder' ),
				'selector' => '.hentry',
			),
			'post_title'                         => array(
				'label'    => esc_html__( 'Post Title', 'et_builder' ),
				'selector' => '.hentry h2 a',
			),
			'post_meta'                          => array(
				'label'    => esc_html__( 'Post Meta', 'et_builder' ),
				'selector' => '.hentry .post-meta',
			),
			'post_meta_icon'                     => array(
				'label'    => esc_html__( 'Post Meta Icons (Rating &amp; Comment)', 'et_builder' ),
				'selector' => '.hentry .post-meta .post-meta-icon:before',
			),
			'post_excerpt'                       => array(
				'label'    => esc_html__( 'Post Excerpt', 'et_builder' ),
				'selector' => '.hentry .excerpt',
			),
			'post_read_more'                     => array(
				'label'    => esc_html__( 'Post Read More', 'et_builder' ),
				'selector' => '.hentry .read-more-button',
			),
			'post_read_more_icon'                => array(
				'label'    => esc_html__( 'Post Read More Icon', 'et_builder' ),
				'selector' => '.hentry .read-more-button:after',
			),
			'post_featured_image'                => array(
				'label'    => esc_html__( 'Post Featured Image', 'et_builder' ),
				'selector' => '.hentry .featured-image img',
			),
			'post_overlay'                       => array(
				'label'    => esc_html__( 'Post Overlay', 'et_builder' ),
				'selector' => '.hentry .et_pb_extra_overlay',
			),
			'post_overlay_icon'                  => array(
				'label'    => esc_html__( 'Post Overlay Icon', 'et_builder' ),
				'selector' => '.hentry .et_pb_extra_overlay:before',
			),
			'post_review_score_bar'              => array(
				'label'    => esc_html__( 'Post Review Score Bar', 'et_builder' ),
				'selector' => '.hentry .score-bar',
			),
			'post_format_gallery_nav'            => array(
				'label'    => esc_html__( 'Post Format Gallery Nav', 'et_builder' ),
				'selector' => '.hentry .et-pb-slider-arrows a',
			),
			'post_format_gallery_nav_icon'       => array(
				'label'    => esc_html__( 'Post Format Gallery Nav Icon', 'et_builder' ),
				'selector' => '.hentry .et-pb-slider-arrows a:before',
			),
			'post_format_gallery_nav_hover'      => array(
				'label'    => esc_html__( 'Post Format Gallery Nav Hover', 'et_builder' ),
				'selector' => '.hentry .et-pb-slider-arrows a:hover',
			),
			'post_format_gallery_nav_hover_icon' => array(
				'label'    => esc_html__( 'Post Format Gallery Nav Icon Hover', 'et_builder' ),
				'selector' => '.hentry .et-pb-slider-arrows a:hover:before',
			),
			'post_format_audio_wrapper'          => array(
				'label'    => esc_html__( 'Post Format Audio Wrapper', 'et_builder' ),
				'selector' => '.hentry .audio-wrapper',
			),
			'post_format_audio_player'           => array(
				'label'    => esc_html__( 'Post Format Audio Player', 'et_builder' ),
				'selector' => '.hentry .mejs-container',
			),
			'post_format_link_background'        => array(
				'label'    => esc_html__( 'Post Format Link Background', 'et_builder' ),
				'selector' => '.hentry .link-format',
			),
			'post_format_quote_background'       => array(
				'label'    => esc_html__( 'Post Format Quote Background', 'et_builder' ),
				'selector' => '.hentry .quote-format',
			),
			'pagination_item'                    => array(
				'label'    => esc_html__( 'Pagination Item', 'et_builder' ),
				'selector' => '.pagination li',
			),
			'pagination_item_active'             => array(
				'label'    => esc_html__( 'Pagination Item Active', 'et_builder' ),
				'selector' => '.pagination li.active',
			),
		);
	}

	function get_fields() {
		$fields = parent::get_fields();

		unset( $fields['feed_title'] );

		unset( $fields['read_more_background'] );

		$fields['post_bg_color'] = array(
			'label'        => esc_html__( 'Post Background Color', 'et_builder' ),
			'type'         => 'color-alpha',
			'custom_color' => true,
			'tab_slug'     => 'advanced',
			'toggle_slug'  => 'post',
			'priority'     => 5,
		);

		return $fields;
	}

	function shortcode_atts() {
		global $et_column_type;

		parent::shortcode_atts();

		$this->enqueue_scripts();

		$this->props['_et_column_type'] = $et_column_type;
		$this->props['blog_feed_module_type'] = 'masonry';
	}

	function before_render() {
		wp_enqueue_script( 'salvattore' );

		if ( '' !== $this->props['border_radius'] ) {
			ET_Builder_Module::set_style( $this->slug, array(
				'selector'    => '%%order_class%%.posts-blog-feed-module.masonry .hentry, %%order_class%%.posts-blog-feed-module.masonry .et-format-link .header div, %%order_class%%.posts-blog-feed-module.masonry .et-format-quote .header div',
				'declaration' => sprintf(
					'-moz-border-radius: %1$s;
					-webkit-border-radius: %1$s;
					border-radius: %1$s;',
					esc_html( $this->props['border_radius'] )
				),
			) );
		}

		if ( isset( $this->props['post_bg_color'] ) && '' !== $this->props['post_bg_color'] ) {
			ET_Builder_Element::set_style( $this->slug, array(
				'selector'    => '%%order_class%%.masonry .hentry',
				'declaration' => sprintf(
					'background-color: %1$s;',
					esc_html( $this->props['post_bg_color'] )
				),
			) );
		}

		if ( isset( $this->props['pagination_color'] ) && '' !== $this->props['pagination_color'] ) {
			ET_Builder_Element::set_style( $this->slug, array(
				'selector'    => '%%order_class%%.paginated .pagination li, %%order_class%%.paginated .pagination li a, %%order_class%%.paginated .pagination li a:before',
				'declaration' => sprintf(
					'color: %1$s;',
					esc_html( $this->props['pagination_color'] )
				),
			) );
		}

		if ( isset( $this->props['pagination_bg_color'] ) && '' !== $this->props['pagination_bg_color'] ) {
			ET_Builder_Element::set_style( $this->slug, array(
				'selector'    => '%%order_class%%.paginated .pagination li, %%order_class%%.paginated .pagination li a',
				'declaration' => sprintf(
					'background-color: %1$s;',
					esc_html( $this->props['pagination_bg_color'] )
				),
			) );
		}

		if ( isset( $this->props['pagination_active_color'] ) && '' !== $this->props['pagination_active_color'] ) {
			ET_Builder_Element::set_style( $this->slug, array(
				'selector'    => '%%order_class%%.paginated .pagination li.active a',
				'declaration' => sprintf(
					'color: %1$s;',
					esc_html( $this->props['pagination_active_color'] )
				),
			) );
		}

		if ( isset( $this->props['pagination_active_bg_color'] ) && '' !== $this->props['pagination_active_bg_color'] ) {
			ET_Builder_Element::set_style( $this->slug, array(
				'selector'    => '%%order_class%%.paginated .pagination li.active a',
				'declaration' => sprintf(
					'background-color: %1$s;',
					esc_html( $this->props['pagination_active_bg_color'] )
				),
			) );
		}
	}

}

}


if (!class_exists('ET_Builder_Module_Ads')) {
class ET_Builder_Module_Ads extends ET_Builder_Module {

	public static $ads_data = array();

	function init() {
		$this->template_name = 'module-ads';
		$this->name = esc_html__( 'Ads', 'extra' );
		$this->slug = 'et_pb_ads';
		$this->post_types = array( EXTRA_LAYOUT_POST_TYPE );
		$this->child_slug = 'et_pb_ads_ad';

		$this->main_css_element = '%%order_class%%';

		$this->settings_modal_toggles = array(
			'general'  => array(
				'toggles' => array(
					'main_content' => esc_html__( 'Text', 'et_builder' ),
				),
			),
			'advanced' => array(
				'toggles' => array(
					'layout'     => esc_html__( 'Layout', 'et_builder'),
					'border'     => array(
						'title'    => esc_html__( 'Border', 'et_builder' ),
						'priority' => 60,
					),
					'width'      => array(
						'title'    => esc_html__( 'Sizing', 'et_builder' ),
						'priority' => 65,
					),
				),
			),
		);

		$this->advanced_fields = array(
			'fonts'                 => array(
				'header' => array(
					'label'          => esc_html__( 'Header', 'et_builder' ),
					'css'            => array(
						'main'      => "{$this->main_css_element} .module-head h1",
						'important' => 'all',
					),
					'letter_spacing' => array(
						'range_settings' => array(
							'min'  => 0,
							'max'  => 30,
							'step' => 0.1,
						),
					),
				),
			),
			'background'            => array(
				'css'      => array(
					'main' => "{$this->main_css_element}, {$this->main_css_element} .module-head",
				),
				'settings' => array(
					'color' => 'alpha',
				),
			),
			'custom_margin_padding' => array(),
		);

		$this->custom_css_fields = array(
			'ad_link'  => array(
				'label'    => esc_html__( 'Ad Link', 'et_builder' ),
				'selector' => 'a',
			),
			'ad_image' => array(
				'label'    => esc_html__( 'Ad Image', 'et_builder' ),
				'selector' => 'a img',
			),
		);
	}

	function set_fields() {
		$color = $this->get_post_default_color();

		$this->fields_defaults = array(
			'header_text_color' => array(
				'#444444',
				'add_default_setting',
			),
			'border_color'      => array(
				$color,
				'add_default_setting',
			),
			'border'            => array(
				'none',
				'only_default_setting',
			),
		);

		parent::set_fields();
	}

	function get_post_default_color() {
		global $post;

		if ( isset( $post->ID ) ) {
			$categories = wp_get_post_categories( $post->ID );
		}

		$color = '';
		if ( !empty( $categories ) ) {
			$first_category_id = $categories[0];
			if ( function_exists( 'et_get_childmost_taxonomy_meta' ) ) {
				$color = et_get_childmost_taxonomy_meta( $first_category_id, 'color', true, et_builder_accent_color() );
			} else {
				$color = et_builder_accent_color();
			}

		} else {
			$color = et_builder_accent_color();
		}

		return $color;
	}

	function get_border_style_output() {
		$style = '';
		if ( 'none' !== $this->props['border'] ) {
			switch ( $this->props['border'] ) {
				case 'full':
					$border = 'solid solid solid solid';
					break;
				case 'top':
				case 'on':
					$border = 'solid none none none';
					break;
				case 'right':
					$border = 'none solid none none';
					break;
				case 'bottom':
					$border = 'none none solid none';
					break;
				case 'left':
					$border = 'none none none solid';
					break;
				case 'left-right':
					$border = 'none solid none solid';
				case 'top-bottom':
					$border = 'solid none solid none';
					break;
				default:
					$border = 'none';
					break;

			}

			$style .= sprintf( 'border-style:%s;',
				esc_attr( $border )
			);

			if ( '' !== $this->props['border_color'] ) {
				$color_rgba = $this->props['border_color'];
				$style .= sprintf( 'border-color:%s;',
					esc_attr( $color_rgba )
				);
			}

			return $style;
		}
	}

	function get_fields() {
		$fields = array(
			'header_text'       => array(
				'label'           => esc_html__( 'Header Text', 'extra' ),
				'type'            => 'text',
				'description'     => esc_html__( 'Text for the header of the module. Leave blank for no header for the module.', 'extra' ),
				'option_category' => 'configuration',
				'toggle_slug'     => 'main_content',
			),
			'header_text_color' => array(
				'label'           => esc_html__( 'Header Text Color', 'extra' ),
				'type'            => 'color-alpha',
				'description'     => esc_html__( 'This will be used as the text color for the header text of the module.', 'extra' ),
				'option_category' => 'configuration',
				'tab_slug'        => 'advanced',
				'toggle_slug'     => 'header',
			),
			'border'            => array(
				'label'           => esc_html__( 'Show Top Border?', 'extra' ),
				'type'            => 'yes_no_button',
				'options'         => array(
					'off' => esc_html__( 'No', 'extra' ),
					'on'  => esc_html__( 'Yes', 'extra' ),
				),
				'affects'         => array( 'border_color' ),
				'description'     => esc_html__( 'This will add a border to the top side of the module.', 'extra' ),
				'option_category' => 'configuration',
				'tab_slug'        => 'advanced',
				'toggle_slug'     => 'border',
			),
			'border_color'      => array(
				'label'               => esc_html__( 'Border Color', 'extra' ),
				'type'                => 'color-alpha',
				'depends_show_if_not' => 'off',
				'description'         => esc_html__( 'This will be used as the border color for this module.', 'extra' ),
				'tab_slug'            => 'advanced',
				'toggle_slug'         => 'border',
			),
			'admin_label'       => array(
				'label'       => esc_html__( 'Admin Label', 'extra' ),
				'type'        => 'text',
				'description' => esc_html__( 'This will change the label of the module in the builder for easy identification.', 'extra' ),
				'toggle_slug' => 'admin_label',
			),
			'module_id'         => array(
				'label'           => esc_html__( 'CSS ID', 'extra' ),
				'type'            => 'text',
				'description'     => esc_html__( 'Enter an optional CSS ID to be used for this module. An ID can be used to create custom CSS styling, or to create links to particular sections of your page.', 'extra' ),
				'option_category' => 'configuration',
				'tab_slug'        => 'custom_css',
				'toggle_slug'     => 'classes',
			),
			'module_class'      => array(
				'label'           => esc_html__( 'CSS Class', 'extra' ),
				'type'            => 'text',
				'description'     => esc_html__( 'Enter optional CSS classes to be used for this module. A CSS class can be used to create custom CSS styling. You can add multiple classes, separated with a space.', 'extra' ),
				'option_category' => 'configuration',
				'tab_slug'        => 'custom_css',
				'toggle_slug'     => 'classes',
			),
		);

		$advanced_design_fields = array(
			'remove_drop_shadow' => array(
				'label'           => esc_html__( 'Remove Drop Shadow', 'et_builder' ),
				'type'            => 'yes_no_button',
				'option_category' => 'layout',
				'options'         => array(
					'off' => esc_html__( 'No', 'et_builder' ),
					'on'  => esc_html__( 'Yes', 'et_builder' ),
				),
				'tab_slug'        => 'advanced',
				'toggle_slug'     => 'layout',
				'priority'        => 24,
			),
			'border_radius'      => array(
				'label'           => esc_html__( 'Border Radius', 'et_builder' ),
				'type'            => 'range',
				'option_category' => 'layout',
				'tab_slug'        => 'advanced',
				'toggle_slug'     => 'border',
				'priority'        => 25,
				'range_settings'  => array(
					'min'  => '0',
					'max'  => '200',
					'step' => '1',
				),
			),
			'max_width'          => array(
				'label'           => esc_html__( 'Max Width', 'et_builder' ),
				'type'            => 'text',
				'option_category' => 'layout',
				'tab_slug'        => 'advanced',
				'toggle_slug'     => 'width',
				'validate_unit'   => true,
			),
		);

		return array_merge( $fields, $advanced_design_fields );
	}

	function add_new_child_text() {
		return esc_html__( 'Add New Ad', 'extra' );
	}

	static function add_child_data( $ads_data ) {
		self::$ads_data[] = $ads_data;
	}

	function process_bool_shortcode_atts() {
		foreach ( $this->get_fields() as $field_name => $field ) {
			if ( 'yes_no_button' == $field['type'] ) {
				$this->props[ $field_name ] = 'on' == $this->props[ $field_name ] ? true : false;
			}
		}
	}

	function shortcode_atts() {
		$this->process_bool_shortcode_atts();
	}

	function render( $atts, $content = null, $function_name ) {
		$this->props['ads'] = self::$ads_data;
		self::$ads_data = array(); // reset

		$border_style = $this->get_border_style_output();
		if ( !empty( $border_style ) ) {
			$this->props['border_style'] = $border_style;
			$this->props['border_class'] = 'bordered';
		} else {
			$this->props['border_style'] = '';
			$this->props['border_class'] = '';
		}

		$this->props['header_text_color'] = $this->props['header_text_color'];

		$this->props['module_class'] = ET_Builder_Element::add_module_order_class( $this->props['module_class'], $this->slug );

		// Adding styling classes to module
		if ( !empty( $this->props['remove_drop_shadow'] ) && 'on' === $this->props['remove_drop_shadow'] ) {
			$this->props['module_class'] = $this->props['module_class'] . ' et_pb_no_drop_shadow';
		}

		// Print styling for general options
		if ( isset( $this->props['border_radius'] ) && '' !== $this->props['border_radius'] ) {
			ET_Builder_Module::set_style( $this->slug, array(
				'selector'    => '%%order_class%%.et_pb_extra_module',
				'declaration' => sprintf(
					'-moz-border-radius: %1$s;
					-webkit-border-radius: %1$s;
					border-radius: %1$s;',
					esc_html( $this->props['border_radius'] )
				),
			) );
		}

		if ( isset( $this->props['max_width'] ) && '' !== $this->props['max_width'] ) {
			ET_Builder_Module::set_style( $this->slug, array(
				'selector'    => '%%order_class%%',
				'declaration' => sprintf(
					'max-width: %1$s;',
					esc_html( et_builder_process_range_value( $this->props['max_width'] ) )
				),
			) );
		}
	}

}

}


if (!class_exists('ET_Builder_Module_Ads_Ad')) {
class ET_Builder_Module_Ads_Ad extends ET_Builder_Module {

	function init() {
		$this->template_name = '';
		$this->name = esc_html__( 'Ad', 'extra' );
		$this->slug = 'et_pb_ads_ad';
		$this->type = 'child';
		$this->post_types = array( EXTRA_LAYOUT_POST_TYPE );
		$this->child_title_var = 'ad_internal_name';

		$this->settings_modal_toggles = array(
			'general'  => array(
				'toggles' => array(
					'main_content' => esc_html__( 'Text', 'et_builder' ),
					'image'        => esc_html__( 'Image', 'et_builder' ),
					'link'         => esc_html__( 'Link', 'et_builder' ),
				),
			),
			'custom_css' => array(
				'toggles' => array(
					'attributes' => array(
						'title'    => esc_html__( 'Attributes', 'et_builder' ),
						'priority' => 95,
					),
				),
			),
		);

		$this->custom_css_fields = array(
			'ad_link'  => array(
				'label'    => esc_html__( 'Ad Link', 'et_builder' ),
				'selector' => 'a',
			),
			'ad_image' => array(
				'label'    => esc_html__( 'Ad Image', 'et_builder' ),
				'selector' => 'a img',
			),
		);
	}

	function get_fields() {
		$fields = array(
			'ad_internal_name' => array(
				'label'       => esc_html__( 'Admin Label', 'extra' ),
				'type'        => 'text',
				'description' => esc_html__( 'Name displayed internally in builder for this ad', 'extra' ),
				'toggle_slug' => 'admin_label',
		),
			'img_url'          => array(
				'label'              => esc_html__( 'Image URL', 'extra' ),
				'type'               => 'upload',
				'upload_button_text' => esc_attr__( 'Upload an Image', 'extra' ),
				'choose_text'        => esc_attr__( 'Choose an Image', 'extra' ),
				'update_text'        => esc_attr__( 'Set as Image', 'extra' ),
				'description'        => esc_html__( 'URL of the ad image.', 'extra' ),
				'option_category'    => 'basic_option',
				'toggle_slug'        => 'image',
			),
			'img_alt_text'         => array(
				'label'           => esc_html__( 'Image Alt Text', 'extra' ),
				'type'            => 'text',
				'description'     => esc_html__( 'Alternative text for image', 'extra' ),
				'option_category' => 'basic_option',
				'tab_slug'        => 'custom_css',
				'toggle_slug'     => 'attributes',
			),
			'link_url'         => array(
				'label'           => esc_html__( 'Link URL', 'extra' ),
				'type'            => 'text',
				'description'     => esc_html__( 'URL the ad\'s image links to.', 'extra' ),
				'option_category' => 'basic_option',
				'toggle_slug'     => 'link',
			),
			'new_line'         => array(
				'label'           => esc_html__( 'Start on New Line?', 'extra' ),
				'type'            => 'yes_no_button',
				'options'         => array(
					'off' => esc_html__( 'No', 'extra' ),
					'on'  => esc_html__( 'Yes', 'extra' ),
				),
				'description'     => esc_html__( 'Start the ad\'s output on a new line?', 'extra' ),
				'option_category' => 'configuration',
				'toggle_slug'     => 'main_content',
			),
			'content'      => array(
				'label'              => esc_html__( 'Ad HTML', 'extra' ),
				'type'               => 'tiny_mce',
				'tiny_mce_html_mode' => true,
				'description'        => esc_html__( 'The Ad HTML, if not using the Image URL and Link above.', 'extra' ),
				'option_category'    => 'basic_option',
				'toggle_slug'        => 'main_content',
			),
		);

		return $fields;
	}

	function process_bool_shortcode_atts() {
		foreach ( $this->get_fields() as $field_name => $field ) {
			if ( 'yes_no_button' == $field['type'] ) {
				$this->props[ $field_name ] = 'on' == $this->props[ $field_name ] ? true : false;
			}
		}
	}

	function shortcode_atts() {
		$this->process_bool_shortcode_atts();
	}

	function render( $atts, $content = null, $function_name ) {
		$this->props['ad_html'] = $this->shortcode_content;

		$this->props['module_class'] = ET_Builder_Element::add_module_order_class( '', $this->slug );

		ET_Builder_Module_Ads::add_child_data( $this->props );
	}

}


}


// phpcs:enable -- end of code from the Extra Theme file includes/modules.php (with minimal or no modifications)


/* The following code includes code copied from and based on Divi and Extra by Elegant Themes, licensed here under GPLv3+.
		See the Credits section in the main plugin file (ds-divi-extras.php) for more details.
		Modified by Jonathan Hall and/or others; last modified 2020-06-08. */
if ( class_exists( 'ET_Builder_Module_Posts' ) && ! class_exists( 'ET_Builder_Module_Posts_AGSDCM' ) ) {
	class ET_Builder_Module_Posts_AGSDCM extends ET_Builder_Module_Posts {
		function init() {
			parent::init();
			$this->fb_support = false;
			$this->vb_support = 'partial';
			$this->post_types = array();
			$this->icon_path        =  plugin_dir_path( __FILE__ ) . 'icons/posts_module.svg';
			$this->slug       .= '_agsdcm';
			if ( $this->child_slug ) {
				$this->child_slug .= '_agsdcm';
			}
			if ( ! empty( $this->advanced_fields['fonts']['header']['css'] ) ) {
				foreach ( $this->advanced_fields['fonts']['header']['css'] as &$selector ) {
					if ( substr( $selector, 0, 16 ) == '#page-container ' ) {
						$selector = substr( $selector, 16 );
					}
				}
			}
			if ( isset( $this->advanced_fields['border']['css']['main'] ) ) {
				$this->advanced_fields['border']['css']['main'] = array(
					'border_styles' => $this->advanced_fields['border']['css']['main'],
					'border_radii'  => $this->advanced_fields['border']['css']['main'],
				);
				$this->advanced_fields['borders']               = array( 'default' => $this->advanced_fields['border'], );
			}
			self::createAndSet( $this->advanced_fields, array(
				'borders',
				'default',
				'defaults',
				'border_radii',
				'on|3px|3px|3px|3px'
			) );
		}

		static function createAndSet( &$target, $set ) {
			if ( count( $set ) < 2 ) {
				return;
			}
			if ( count( $set ) == 2 ) {
				$target[ $set[0] ] = $set[1];
			} else {
				$newKey            = array_shift( $set );
				$target[ $newKey ] = array();
				self::createAndSet( $target[ $newKey ], $set );
			}
		}

		function shortcode_atts() {
			if ( empty( $GLOBALS['et_fb_processing_shortcode_object'] ) ) {
				$oldProps = $this->props;
				parent::shortcode_atts();
				foreach ( $this->props as $propName => &$propValue ) {
					if ( $propName == 'remove_drop_shadow' && isset( $oldProps[ $propName ] ) && ( $propValue === true || $propValue === false ) ) {
						$propValue = $oldProps[ $propName ];
					}
				}
			} else {
				$oldProps = $this->props;
				parent::shortcode_atts();
				foreach ( $this->props as $propName => &$propValue ) {
					if ( isset( $oldProps[ $propName ] ) && ( $propValue === true || $propValue === false ) ) {
						$propValue = $oldProps[ $propName ];
					}
				}
			}
		}

		function _add_link_options_fields() {
		}

		function _add_text_fields() {
		}

		function process_advanced_text_options( $function_name ) {
		}

		function get_fields() {
			$fields = parent::get_fields();
			unset( $fields['show_rating'] );

			return agsdcm_process_module_fields( $fields, $this );
		}

		function _pre_wp_query( $args ) {
			return agsdcm_process_module_query( parent::_pre_wp_query( $args ), $this );
		}

		function render( $atts, $content = null, $function_name, $parent_address = '', $global_parent = '', $global_parent_type = '' ) {
			if ( empty( $GLOBALS['et_fb_processing_shortcode_object'] ) ) {
				$return = call_user_func_array( array( __CLASS__, 'parent::render' ), func_get_args() );
			} else {
				$templateName = $this->template_name;
				unset( $this->template_name );
				$return              = call_user_func_array( array( __CLASS__, 'parent::render' ), func_get_args() );
				$this->template_name = $templateName;
			}

			return $return;
		}


		function _render( $attrs, $content = null, $render_slug, $parent_address = '', $global_parent = '', $global_parent_type = '', $parent_type = '', $theme_builder_area = '' ) {
			add_filter( 'et_pb_set_style_selector', array( $this, 'filter_selector' ) );
			add_filter( 'et_builder_processed_range_value', array( $this, 'fix_range_value' ) );
			$return = call_user_func_array( array( __CLASS__, 'parent::_render' ), func_get_args() );
			remove_filter( 'et_pb_set_style_selector', array( $this, 'filter_selector' ) );
			remove_filter( 'et_builder_processed_range_value', array( $this, 'fix_range_value' ) );

			return $return;
		}


		function fix_range_value( $value ) {
			if ( $value == '0none' ) {
				return 'none';
			}

			return $value;
		}

		function before_render() {
			self::createAndSet( $this->advanced_fields, array( 'border', 'css', 'important', 'all' ) );
			$this->props['border_radius'] = '';

			return call_user_func_array( array( __CLASS__, 'parent::before_render' ), func_get_args() );
		}

		function filter_selector( $selector ) {
			$selector = explode( ',', $selector );
			foreach ( $selector as &$selectorPart ) {
				$selectorPart = trim( $selectorPart );
				if ( strpos( $selectorPart, '.ags-divi-extras-module ' ) === false ) {
					$moduleClassPos = strpos( $selectorPart, '_agsdcm_' );
					if ( $moduleClassPos ) {
						$moduleClassPos = strrpos( $selectorPart, ' ', $moduleClassPos - strlen( $selectorPart ) );
						$selectorPart   = $moduleClassPos ? substr( $selectorPart, 0, $moduleClassPos ) . ' .ags-divi-extras-module' . substr( $selectorPart, $moduleClassPos ) : '.ags-divi-extras-module ' . $selectorPart;
					}
				}
			}

			return implode( ',', $selector );
		}

		function _set_fields_unprocessed( $fields ) {
			$unsupportedFields = array(
				'module_alignment'             => true,
				'module_alignment_tablet'      => true,
				'module_alignment_phone'       => true,
				'module_alignment_last_edited' => true,
				'border_radius'                => true
			);

			return parent::_set_fields_unprocessed( array_diff_key( $fields, $unsupportedFields ) );
		}

		function output() {
			$this->props['show_rating'] = 'off';
			$this->props['content']     = $this->content;
			extract( $this->props );
			ob_start();
			$templatePath = locate_template( $this->template_name . '.php' );
			require( empty( $templatePath ) ? dirname( __FILE__ ) . '/templates/' . $this->template_name . '.php' : $templatePath );

			return '<div class="ags-divi-extras-module">' . ob_get_clean() . '</div>';
		}
	}

	new ET_Builder_Module_Posts_AGSDCM;
}
if ( class_exists( 'ET_Builder_Module_Tabbed_Posts' ) && ! class_exists( 'ET_Builder_Module_Tabbed_Posts_AGSDCM' ) ) {
	class ET_Builder_Module_Tabbed_Posts_AGSDCM extends ET_Builder_Module_Tabbed_Posts {
		function init() {
			parent::init();
			$this->fb_support = false;
			$this->vb_support = 'partial';
			$this->post_types = array();
			$this->icon_path        =  plugin_dir_path( __FILE__ ) . 'icons/tabbed_posts_module.svg';
			$this->slug       .= '_agsdcm';
			if ( $this->child_slug ) {
				$this->child_slug .= '_agsdcm';
			}
			if ( ! empty( $this->advanced_fields['fonts']['header']['css'] ) ) {
				foreach ( $this->advanced_fields['fonts']['header']['css'] as &$selector ) {
					if ( substr( $selector, 0, 16 ) == '#page-container ' ) {
						$selector = substr( $selector, 16 );
					}
				}
			}
			if ( isset( $this->advanced_fields['border']['css']['main'] ) ) {
				$this->advanced_fields['border']['css']['main'] = array(
					'border_styles' => $this->advanced_fields['border']['css']['main'],
					'border_radii'  => $this->advanced_fields['border']['css']['main'],
				);
				$this->advanced_fields['borders']               = array( 'default' => $this->advanced_fields['border'], );
			}
			self::createAndSet( $this->advanced_fields, array(
				'borders',
				'default',
				'defaults',
				'border_radii',
				'on|3px|3px|3px|3px'
			) );
		}

		static function createAndSet( &$target, $set ) {
			if ( count( $set ) < 2 ) {
				return;
			}
			if ( count( $set ) == 2 ) {
				$target[ $set[0] ] = $set[1];
			} else {
				$newKey            = array_shift( $set );
				$target[ $newKey ] = array();
				self::createAndSet( $target[ $newKey ], $set );
			}
		}

		function shortcode_atts() {
			if ( empty( $GLOBALS['et_fb_processing_shortcode_object'] ) ) {
				$oldProps = $this->props;
				parent::shortcode_atts();
				foreach ( $this->props as $propName => &$propValue ) {
					if ( $propName == 'remove_drop_shadow' && isset( $oldProps[ $propName ] ) && ( $propValue === true || $propValue === false ) ) {
						$propValue = $oldProps[ $propName ];
					}
				}
			} else {
				$oldProps = $this->props;
				parent::shortcode_atts();
				foreach ( $this->props as $propName => &$propValue ) {
					if ( isset( $oldProps[ $propName ] ) && ( $propValue === true || $propValue === false ) ) {
						$propValue = $oldProps[ $propName ];
					}
				}
			}
		}

		function _add_link_options_fields() {
		}

		function _add_text_fields() {
		}

		function process_advanced_text_options( $function_name ) {
		}

		function _pre_wp_query( $args ) {
			return agsdcm_process_module_query( parent::_pre_wp_query( $args ), $this );
		}

		function render( $atts, $content = null, $function_name, $parent_address = '', $global_parent = '', $global_parent_type = '' ) {
			if ( empty( $GLOBALS['et_fb_processing_shortcode_object'] ) ) {
				$return = call_user_func_array( array( __CLASS__, 'parent::render' ), func_get_args() );
			} else {
				$templateName = $this->template_name;
				unset( $this->template_name );
				$return              = call_user_func_array( array( __CLASS__, 'parent::render' ), func_get_args() );
				$this->template_name = $templateName;
			}

			return $return;
		}

		function _render( $attrs, $content = null, $render_slug, $parent_address = '', $global_parent = '', $global_parent_type = '', $parent_type = '', $theme_builder_area = '' ) {
			add_filter( 'et_pb_set_style_selector', array( $this, 'filter_selector' ) );
			add_filter( 'et_builder_processed_range_value', array( $this, 'fix_range_value' ) );
			$return = call_user_func_array( array( __CLASS__, 'parent::_render' ), func_get_args() );
			remove_filter( 'et_pb_set_style_selector', array( $this, 'filter_selector' ) );
			remove_filter( 'et_builder_processed_range_value', array( $this, 'fix_range_value' ) );

			return $return;
		}

		function fix_range_value( $value ) {
			if ( $value == '0none' ) {
				return 'none';
			}

			return $value;
		}

		function before_render() {
			self::createAndSet( $this->advanced_fields, array( 'border', 'css', 'important', 'all' ) );
			$this->props['border_radius'] = '';

			return call_user_func_array( array( __CLASS__, 'parent::before_render' ), func_get_args() );
		}

		function filter_selector( $selector ) {
			$selector = explode( ',', $selector );
			foreach ( $selector as &$selectorPart ) {
				$selectorPart = trim( $selectorPart );
				if ( strpos( $selectorPart, '.ags-divi-extras-module ' ) === false ) {
					$moduleClassPos = strpos( $selectorPart, '_agsdcm_' );
					if ( $moduleClassPos ) {
						$moduleClassPos = strrpos( $selectorPart, ' ', $moduleClassPos - strlen( $selectorPart ) );
						$selectorPart   = $moduleClassPos ? substr( $selectorPart, 0, $moduleClassPos ) . ' .ags-divi-extras-module' . substr( $selectorPart, $moduleClassPos ) : '.ags-divi-extras-module ' . $selectorPart;
					}
				}
			}

			return implode( ',', $selector );
		}

		function _set_fields_unprocessed( $fields ) {
			$unsupportedFields = array(
				'module_alignment'             => true,
				'module_alignment_tablet'      => true,
				'module_alignment_phone'       => true,
				'module_alignment_last_edited' => true,
				'border_radius'                => true
			);

			return parent::_set_fields_unprocessed( array_diff_key( $fields, $unsupportedFields ) );
		}

		function output() {
			$this->props['show_rating'] = 'off';
			$this->props['content']     = $this->content;
			extract( $this->props );
			ob_start();
			$templatePath = locate_template( $this->template_name . '.php' );
			require( empty( $templatePath ) ? dirname( __FILE__ ) . '/templates/' . $this->template_name . '.php' : $templatePath );

			return '<div class="ags-divi-extras-module">' . ob_get_clean() . '</div>';
		}
	}

	new ET_Builder_Module_Tabbed_Posts_AGSDCM;
}
if ( class_exists( 'ET_Builder_Module_Tabbed_Posts_Tab' ) && ! class_exists( 'ET_Builder_Module_Tabbed_Posts_Tab_AGSDCM' ) ) {
	class ET_Builder_Module_Tabbed_Posts_Tab_AGSDCM extends ET_Builder_Module_Tabbed_Posts_Tab {
		function init() {
			parent::init();
			$this->fb_support = false;
			$this->vb_support = 'partial';
			$this->post_types = array();
			$this->slug       .= '_agsdcm';
			if ( $this->child_slug ) {
				$this->child_slug .= '_agsdcm';
			}
			if ( ! empty( $this->advanced_fields['fonts']['header']['css'] ) ) {
				foreach ( $this->advanced_fields['fonts']['header']['css'] as &$selector ) {
					if ( substr( $selector, 0, 16 ) == '#page-container ' ) {
						$selector = substr( $selector, 16 );
					}
				}
			}
			if ( isset( $this->advanced_fields['border']['css']['main'] ) ) {
				$this->advanced_fields['border']['css']['main'] = array(
					'border_styles' => $this->advanced_fields['border']['css']['main'],
					'border_radii'  => $this->advanced_fields['border']['css']['main'],
				);
				$this->advanced_fields['borders']               = array( 'default' => $this->advanced_fields['border'], );
			}
		}

		static function createAndSet( &$target, $set ) {
			if ( count( $set ) < 2 ) {
				return;
			}
			if ( count( $set ) == 2 ) {
				$target[ $set[0] ] = $set[1];
			} else {
				$newKey            = array_shift( $set );
				$target[ $newKey ] = array();
				self::createAndSet( $target[ $newKey ], $set );
			}
		}

		function shortcode_atts() {
			if ( empty( $GLOBALS['et_fb_processing_shortcode_object'] ) ) {
				$oldProps = $this->props;
				parent::shortcode_atts();
				foreach ( $this->props as $propName => &$propValue ) {
					if ( $propName == 'remove_drop_shadow' && isset( $oldProps[ $propName ] ) && ( $propValue === true || $propValue === false ) ) {
						$propValue = $oldProps[ $propName ];
					}
				}
			} else {
				$oldProps = $this->props;
				parent::shortcode_atts();
				foreach ( $this->props as $propName => &$propValue ) {
					if ( isset( $oldProps[ $propName ] ) && ( $propValue === true || $propValue === false ) ) {
						$propValue = $oldProps[ $propName ];
					}
				}
			}
		}

		function _add_link_options_fields() {
		}

		function _add_text_fields() {
		}

		function process_advanced_text_options( $function_name ) {
		}

		function get_fields() {
			$fields = parent::get_fields();
			unset( $fields['show_rating'] );

			return agsdcm_process_module_fields( $fields, $this, false );
		}

		function _pre_wp_query( $args ) {
			return agsdcm_process_module_query( parent::_pre_wp_query( $args ), $this );
		}

		function render( $atts, $content = null, $function_name, $parent_address = '', $global_parent = '', $global_parent_type = '' ) {
			if ( empty( $GLOBALS['et_fb_processing_shortcode_object'] ) ) {
				$return = call_user_func_array( array( __CLASS__, 'parent::render' ), func_get_args() );
			} else {
				$templateName = $this->template_name;
				unset( $this->template_name );
				$return              = call_user_func_array( array( __CLASS__, 'parent::render' ), func_get_args() );
				$this->template_name = $templateName;
			}

			return $return;
		}

		function _render( $attrs, $content = null, $render_slug, $parent_address = '', $global_parent = '', $global_parent_type = '', $parent_type = '', $theme_builder_area = '' ) {
			add_filter( 'et_pb_set_style_selector', array( $this, 'filter_selector' ) );
			add_filter( 'et_builder_processed_range_value', array( $this, 'fix_range_value' ) );
			$return = call_user_func_array( array( __CLASS__, 'parent::_render' ), func_get_args() );
			remove_filter( 'et_pb_set_style_selector', array( $this, 'filter_selector' ) );
			remove_filter( 'et_builder_processed_range_value', array( $this, 'fix_range_value' ) );

			return $return;
		}

		function fix_range_value( $value ) {
			if ( $value == '0none' ) {
				return 'none';
			}

			return $value;
		}

		function before_render() {
			$this->props['border_radius'] = '';

			return call_user_func_array( array( __CLASS__, 'parent::before_render' ), func_get_args() );
		}

		function filter_selector( $selector ) {
			$selector = explode( ',', $selector );
			foreach ( $selector as &$selectorPart ) {
				$selectorPart = trim( $selectorPart );
				if ( strpos( $selectorPart, '.ags-divi-extras-module ' ) === false ) {
					$moduleClassPos = strpos( $selectorPart, '_agsdcm_' );
					if ( $moduleClassPos ) {
						$moduleClassPos = strrpos( $selectorPart, ' ', $moduleClassPos - strlen( $selectorPart ) );
						$selectorPart   = $moduleClassPos ? substr( $selectorPart, 0, $moduleClassPos ) . ' .ags-divi-extras-module' . substr( $selectorPart, $moduleClassPos ) : '.ags-divi-extras-module ' . $selectorPart;
					}
				}
			}

			return implode( ',', $selector );
		}

		function _set_fields_unprocessed( $fields ) {
			$unsupportedFields = array(
				'module_alignment'             => true,
				'module_alignment_tablet'      => true,
				'module_alignment_phone'       => true,
				'module_alignment_last_edited' => true,
				'border_radius'                => true
			);

			return parent::_set_fields_unprocessed( array_diff_key( $fields, $unsupportedFields ) );
		}

		function output() {
			$this->props['show_rating'] = 'off';
			$this->props['content']     = $this->content;
			extract( $this->props );
			ob_start();
			$templatePath = locate_template( $this->template_name . '.php' );
			require( empty( $templatePath ) ? dirname( __FILE__ ) . '/templates/' . $this->template_name . '.php' : $templatePath );

			return ob_get_clean();
		}
	}

	new ET_Builder_Module_Tabbed_Posts_Tab_AGSDCM;
}
if ( class_exists( 'ET_Builder_Module_Posts_Carousel' ) && ! class_exists( 'ET_Builder_Module_Posts_Carousel_AGSDCM' ) ) {
	class ET_Builder_Module_Posts_Carousel_AGSDCM extends ET_Builder_Module_Posts_Carousel {
		function init() {
			parent::init();
			$this->fb_support = false;
			$this->vb_support = 'partial';
			$this->post_types = array();
			$this->icon_path        =  plugin_dir_path( __FILE__ ) . 'icons/posts_carousel_module.svg';
			$this->slug       .= '_agsdcm';
			if ( $this->child_slug ) {
				$this->child_slug .= '_agsdcm';
			}
			if ( ! empty( $this->advanced_fields['fonts']['header']['css'] ) ) {
				foreach ( $this->advanced_fields['fonts']['header']['css'] as &$selector ) {
					if ( substr( $selector, 0, 16 ) == '#page-container ' ) {
						$selector = substr( $selector, 16 );
					}
				}
			}
			if ( isset( $this->advanced_fields['border']['css']['main'] ) ) {
				$this->advanced_fields['border']['css']['main'] = array(
					'border_styles' => $this->advanced_fields['border']['css']['main'],
					'border_radii'  => $this->advanced_fields['border']['css']['main'],
				);
				$this->advanced_fields['borders']               = array( 'default' => $this->advanced_fields['border'], );
			}
			self::createAndSet( $this->advanced_fields, array(
				'borders',
				'default',
				'defaults',
				'border_radii',
				'on|3px|3px|3px|3px'
			) );
		}

		static function createAndSet( &$target, $set ) {
			if ( count( $set ) < 2 ) {
				return;
			}
			if ( count( $set ) == 2 ) {
				$target[ $set[0] ] = $set[1];
			} else {
				$newKey            = array_shift( $set );
				$target[ $newKey ] = array();
				self::createAndSet( $target[ $newKey ], $set );
			}
		}

		function shortcode_atts() {
			if ( empty( $GLOBALS['et_fb_processing_shortcode_object'] ) ) {
				$oldProps = $this->props;
				parent::shortcode_atts();
				foreach ( $this->props as $propName => &$propValue ) {
					if ( $propName == 'remove_drop_shadow' && isset( $oldProps[ $propName ] ) && ( $propValue === true || $propValue === false ) ) {
						$propValue = $oldProps[ $propName ];
					}
				}
			} else {
				$oldProps = $this->props;
				parent::shortcode_atts();
				foreach ( $this->props as $propName => &$propValue ) {
					if ( isset( $oldProps[ $propName ] ) && ( $propValue === true || $propValue === false ) ) {
						$propValue = $oldProps[ $propName ];
					}
				}
			}
		}

		function _add_link_options_fields() {
		}

		function _add_text_fields() {
		}

		function process_advanced_text_options( $function_name ) {
		}

		function get_fields() {
			$fields = parent::get_fields();
			unset( $fields['show_rating'] );

			return agsdcm_process_module_fields( $fields, $this );
		}

		function _pre_wp_query( $args ) {
			return agsdcm_process_module_query( parent::_pre_wp_query( $args ), $this );
		}

		function render( $atts, $content = null, $function_name, $parent_address = '', $global_parent = '', $global_parent_type = '' ) {
			if ( empty( $GLOBALS['et_fb_processing_shortcode_object'] ) ) {
				$return = call_user_func_array( array( __CLASS__, 'parent::render' ), func_get_args() );
			} else {
				$templateName = $this->template_name;
				unset( $this->template_name );
				$return              = call_user_func_array( array( __CLASS__, 'parent::render' ), func_get_args() );
				$this->template_name = $templateName;
			}

			return $return;
		}

		function _render( $attrs, $content = null, $render_slug, $parent_address = '', $global_parent = '', $global_parent_type = '', $parent_type = '', $theme_builder_area = '' ) {
			add_filter( 'et_pb_set_style_selector', array( $this, 'filter_selector' ) );
			add_filter( 'et_builder_processed_range_value', array( $this, 'fix_range_value' ) );
			$return = call_user_func_array( array( __CLASS__, 'parent::_render' ), func_get_args() );
			remove_filter( 'et_pb_set_style_selector', array( $this, 'filter_selector' ) );
			remove_filter( 'et_builder_processed_range_value', array( $this, 'fix_range_value' ) );

			return $return;
		}

		function fix_range_value( $value ) {
			if ( $value == '0none' ) {
				return 'none';
			}

			return $value;
		}

		function before_render() {
			self::createAndSet( $this->advanced_fields, array( 'border', 'css', 'important', 'all' ) );
			$this->props['border_radius'] = '';

			return call_user_func_array( array( __CLASS__, 'parent::before_render' ), func_get_args() );
		}

		function filter_selector( $selector ) {
			$selector = explode( ',', $selector );
			foreach ( $selector as &$selectorPart ) {
				$selectorPart = trim( $selectorPart );
				if ( strpos( $selectorPart, '.ags-divi-extras-module ' ) === false ) {
					$moduleClassPos = strpos( $selectorPart, '_agsdcm_' );
					if ( $moduleClassPos ) {
						$moduleClassPos = strrpos( $selectorPart, ' ', $moduleClassPos - strlen( $selectorPart ) );
						$selectorPart   = $moduleClassPos ? substr( $selectorPart, 0, $moduleClassPos ) . ' .ags-divi-extras-module' . substr( $selectorPart, $moduleClassPos ) : '.ags-divi-extras-module ' . $selectorPart;
					}
				}
			}

			return implode( ',', $selector );
		}

		function _set_fields_unprocessed( $fields ) {
			$unsupportedFields = array(
				'module_alignment'             => true,
				'module_alignment_tablet'      => true,
				'module_alignment_phone'       => true,
				'module_alignment_last_edited' => true,
				'border_radius'                => true
			);

			return parent::_set_fields_unprocessed( array_diff_key( $fields, $unsupportedFields ) );
		}

		function output() {
			$this->props['show_rating'] = 'off';
			$this->props['content']     = $this->content;
			extract( $this->props );
			ob_start();
			$templatePath = locate_template( $this->template_name . '.php' );
			require( empty( $templatePath ) ? dirname( __FILE__ ) . '/templates/' . $this->template_name . '.php' : $templatePath );

			return '<div class="ags-divi-extras-module">' . ob_get_clean() . '</div>';
		}
	}

	new ET_Builder_Module_Posts_Carousel_AGSDCM;
}
if ( class_exists( 'ET_Builder_Module_Featured_Posts_Slider' ) && ! class_exists( 'ET_Builder_Module_Featured_Posts_Slider_AGSDCM' ) ) {
	class ET_Builder_Module_Featured_Posts_Slider_AGSDCM extends ET_Builder_Module_Featured_Posts_Slider {
		function init() {
			parent::init();
			$this->fb_support = false;
			$this->vb_support = 'partial';
			$this->post_types = array();
			$this->icon_path        =  plugin_dir_path( __FILE__ ) . 'icons/posts_slider_module.svg';
			$this->slug       .= '_agsdcm';
			if ( $this->child_slug ) {
				$this->child_slug .= '_agsdcm';
			}
			if ( ! empty( $this->advanced_fields['fonts']['header']['css'] ) ) {
				foreach ( $this->advanced_fields['fonts']['header']['css'] as &$selector ) {
					if ( substr( $selector, 0, 16 ) == '#page-container ' ) {
						$selector = substr( $selector, 16 );
					}
				}
			}
			if ( isset( $this->advanced_fields['border']['css']['main'] ) ) {
				$this->advanced_fields['border']['css']['main'] = array(
					'border_styles' => $this->advanced_fields['border']['css']['main'],
					'border_radii'  => $this->advanced_fields['border']['css']['main'],
				);
				$this->advanced_fields['borders']               = array( 'default' => $this->advanced_fields['border'], );
			}
			self::createAndSet( $this->advanced_fields, array(
				'borders',
				'default',
				'defaults',
				'border_radii',
				'on|3px|3px|3px|3px'
			) );
		}

		static function createAndSet( &$target, $set ) {
			if ( count( $set ) < 2 ) {
				return;
			}
			if ( count( $set ) == 2 ) {
				$target[ $set[0] ] = $set[1];
			} else {
				$newKey            = array_shift( $set );
				$target[ $newKey ] = array();
				self::createAndSet( $target[ $newKey ], $set );
			}
		}

		function shortcode_atts() {
			if ( empty( $GLOBALS['et_fb_processing_shortcode_object'] ) ) {
				$oldProps = $this->props;
				parent::shortcode_atts();
				foreach ( $this->props as $propName => &$propValue ) {
					if ( $propName == 'remove_drop_shadow' && isset( $oldProps[ $propName ] ) && ( $propValue === true || $propValue === false ) ) {
						$propValue = $oldProps[ $propName ];
					}
				}
			} else {
				$oldProps = $this->props;
				parent::shortcode_atts();
				foreach ( $this->props as $propName => &$propValue ) {
					if ( isset( $oldProps[ $propName ] ) && ( $propValue === true || $propValue === false ) ) {
						$propValue = $oldProps[ $propName ];
					}
				}
			}
		}

		function _add_link_options_fields() {
		}

		function _add_text_fields() {
		}

		function process_advanced_text_options( $function_name ) {
		}

		function get_fields() {
			$fields = parent::get_fields();
			unset( $fields['heading_primary'] );
			unset( $fields['heading_sub'] );
			unset( $fields['show_rating'] );

			return agsdcm_process_module_fields( $fields, $this );
		}

		function _pre_wp_query( $args ) {
			return agsdcm_process_module_query( parent::_pre_wp_query( $args ), $this );
		}

		function render( $atts, $content = null, $function_name, $parent_address = '', $global_parent = '', $global_parent_type = '' ) {
			if ( empty( $GLOBALS['et_fb_processing_shortcode_object'] ) ) {
				$return = call_user_func_array( array( __CLASS__, 'parent::render' ), func_get_args() );
			} else {
				$templateName = $this->template_name;
				unset( $this->template_name );
				$return              = call_user_func_array( array( __CLASS__, 'parent::render' ), func_get_args() );
				$this->template_name = $templateName;
			}

			return $return;
		}

		function _render( $attrs, $content = null, $render_slug, $parent_address = '', $global_parent = '', $global_parent_type = '', $parent_type = '', $theme_builder_area = '' ) {
			add_filter( 'et_pb_set_style_selector', array( $this, 'filter_selector' ) );
			add_filter( 'et_builder_processed_range_value', array( $this, 'fix_range_value' ) );
			$return = call_user_func_array( array( __CLASS__, 'parent::_render' ), func_get_args() );
			remove_filter( 'et_pb_set_style_selector', array( $this, 'filter_selector' ) );
			remove_filter( 'et_builder_processed_range_value', array( $this, 'fix_range_value' ) );

			return $return;
		}

		function fix_range_value( $value ) {
			if ( $value == '0none' ) {
				return 'none';
			}

			return $value;
		}

		function before_render() {
			$this->props['border_radius'] = '';

			return call_user_func_array( array( __CLASS__, 'parent::before_render' ), func_get_args() );
		}

		function filter_selector( $selector ) {
			$selector = explode( ',', $selector );
			foreach ( $selector as &$selectorPart ) {
				$selectorPart = trim( $selectorPart );
				if ( strpos( $selectorPart, '.ags-divi-extras-module ' ) === false ) {
					$moduleClassPos = strpos( $selectorPart, '_agsdcm_' );
					if ( $moduleClassPos ) {
						$moduleClassPos = strrpos( $selectorPart, ' ', $moduleClassPos - strlen( $selectorPart ) );
						$selectorPart   = $moduleClassPos ? substr( $selectorPart, 0, $moduleClassPos ) . ' .ags-divi-extras-module' . substr( $selectorPart, $moduleClassPos ) : '.ags-divi-extras-module ' . $selectorPart;
					}
				}
			}

			return implode( ',', $selector );
		}

		function _set_fields_unprocessed( $fields ) {
			$unsupportedFields = array(
				'module_alignment'             => true,
				'module_alignment_tablet'      => true,
				'module_alignment_phone'       => true,
				'module_alignment_last_edited' => true,
				'border_radius'                => true
			);

			return parent::_set_fields_unprocessed( array_diff_key( $fields, $unsupportedFields ) );
		}

		function output() {
			$this->props['show_rating'] = 'off';
			$this->props['content']     = $this->content;
			extract( $this->props );
			ob_start();
			$templatePath = locate_template( $this->template_name . '.php' );
			require( empty( $templatePath ) ? dirname( __FILE__ ) . '/templates/' . $this->template_name . '.php' : $templatePath );

			return '<div class="ags-divi-extras-module">' . ob_get_clean() . '</div>';
		}
	}

	new ET_Builder_Module_Featured_Posts_Slider_AGSDCM;
}
if ( class_exists( 'ET_Builder_Module_Posts_Blog_Feed' ) && ! class_exists( 'ET_Builder_Module_Posts_Blog_Feed_AGSDCM' ) ) {
	class ET_Builder_Module_Posts_Blog_Feed_AGSDCM extends ET_Builder_Module_Posts_Blog_Feed {
		function init() {
			parent::init();
			$this->fb_support = false;
			$this->vb_support = 'partial';
			$this->post_types = array();
			$this->icon_path        =  plugin_dir_path( __FILE__ ) . 'icons/blogfeed_standard_module.svg';
			$this->slug       .= '_agsdcm';
			if ( $this->child_slug ) {
				$this->child_slug .= '_agsdcm';
			}
			if ( ! empty( $this->advanced_fields['fonts']['header']['css'] ) ) {
				foreach ( $this->advanced_fields['fonts']['header']['css'] as &$selector ) {
					if ( substr( $selector, 0, 16 ) == '#page-container ' ) {
						$selector = substr( $selector, 16 );
					}
				}
			}
			$this->advanced_fields['borders'] = array(
				'default' => array(
					'css' => array(
						'main'      => $this->main_css_element,
						'important' => 'all'
					)
				)
			);
			self::createAndSet( $this->advanced_fields, array(
				'borders',
				'default',
				'defaults',
				'border_radii',
				'on|3px|3px|3px|3px'
			) );
		}

		static function createAndSet( &$target, $set ) {
			if ( count( $set ) < 2 ) {
				return;
			}
			if ( count( $set ) == 2 ) {
				$target[ $set[0] ] = $set[1];
			} else {
				$newKey            = array_shift( $set );
				$target[ $newKey ] = array();
				self::createAndSet( $target[ $newKey ], $set );
			}
		}

		function shortcode_atts() {
			if ( empty( $GLOBALS['et_fb_processing_shortcode_object'] ) ) {
				$oldProps = $this->props;
				parent::shortcode_atts();
				foreach ( $this->props as $propName => &$propValue ) {
					if ( $propName == 'remove_drop_shadow' && isset( $oldProps[ $propName ] ) && ( $propValue === true || $propValue === false ) ) {
						$propValue = $oldProps[ $propName ];
					}
				}
			} else {
				$oldProps = $this->props;
				parent::shortcode_atts();
				foreach ( $this->props as $propName => &$propValue ) {
					if ( isset( $oldProps[ $propName ] ) && ( $propValue === true || $propValue === false ) ) {
						$propValue = $oldProps[ $propName ];
					}
				}
			}
		}

		function _add_link_options_fields() {
		}

		function _add_text_fields() {
		}

		function process_advanced_text_options( $function_name ) {
		}

		function get_fields() {
			$fields = parent::get_fields();
			unset( $fields['show_rating'] );

			return agsdcm_process_module_fields( $fields, $this );
		}

		function _pre_wp_query( $args ) {
			return agsdcm_process_module_query( parent::_pre_wp_query( $args ), $this );
		}

		function render( $atts, $content = null, $function_name, $parent_address = '', $global_parent = '', $global_parent_type = '' ) {
			if ( empty( $GLOBALS['et_fb_processing_shortcode_object'] ) ) {
				$return = call_user_func_array( array( __CLASS__, 'parent::render' ), func_get_args() );
			} else {
				$templateName = $this->template_name;
				unset( $this->template_name );
				$return              = call_user_func_array( array( __CLASS__, 'parent::render' ), func_get_args() );
				$this->template_name = $templateName;
			}

			return $return;
		}

		function _render( $attrs, $content = null, $render_slug, $parent_address = '', $global_parent = '', $global_parent_type = '', $parent_type = '', $theme_builder_area = '' ) {
			add_filter( 'et_pb_set_style_selector', array( $this, 'filter_selector' ) );
			add_filter( 'et_builder_processed_range_value', array( $this, 'fix_range_value' ) );
			$return = call_user_func_array( array( __CLASS__, 'parent::_render' ), func_get_args() );
			remove_filter( 'et_pb_set_style_selector', array( $this, 'filter_selector' ) );
			remove_filter( 'et_builder_processed_range_value', array( $this, 'fix_range_value' ) );

			return $return;
		}

		function fix_range_value( $value ) {
			if ( $value == '0none' ) {
				return 'none';
			}

			return $value;
		}

		function before_render() {
			wp_enqueue_script( 'salvattore' );
			$this->props['border_radius'] = '';

			return call_user_func_array( array( __CLASS__, 'parent::before_render' ), func_get_args() );
		}

		function filter_selector( $selector ) {
			$selector = explode( ',', $selector );
			foreach ( $selector as &$selectorPart ) {
				$selectorPart = trim( $selectorPart );
				if ( strpos( $selectorPart, '.ags-divi-extras-module ' ) === false ) {
					$moduleClassPos = strpos( $selectorPart, '_agsdcm_' );
					if ( $moduleClassPos ) {
						$moduleClassPos = strrpos( $selectorPart, ' ', $moduleClassPos - strlen( $selectorPart ) );
						$selectorPart   = $moduleClassPos ? substr( $selectorPart, 0, $moduleClassPos ) . ' .ags-divi-extras-module' . substr( $selectorPart, $moduleClassPos ) : '.ags-divi-extras-module ' . $selectorPart;
					}
				}
			}

			return implode( ',', $selector );
		}

		function _set_fields_unprocessed( $fields ) {
			$unsupportedFields = array(
				'module_alignment'             => true,
				'module_alignment_tablet'      => true,
				'module_alignment_phone'       => true,
				'module_alignment_last_edited' => true,
				'border_radius'                => true
			);

			return parent::_set_fields_unprocessed( array_diff_key( $fields, $unsupportedFields ) );
		}

		function output() {
			$this->props['show_rating'] = 'off';
			$this->props['content']     = $this->content;
			extract( $this->props );
			ob_start();
			$templatePath = locate_template( $this->template_name . '.php' );
			require( empty( $templatePath ) ? dirname( __FILE__ ) . '/templates/' . $this->template_name . '.php' : $templatePath );

			return '<div class="ags-divi-extras-module">' . ob_get_clean() . '</div>';
		}
	}

	new ET_Builder_Module_Posts_Blog_Feed_AGSDCM;
}
if ( class_exists( 'ET_Builder_Module_Posts_Blog_Feed_Masonry' ) && ! class_exists( 'ET_Builder_Module_Posts_Blog_Feed_Masonry_AGSDCM' ) ) {
	class ET_Builder_Module_Posts_Blog_Feed_Masonry_AGSDCM extends ET_Builder_Module_Posts_Blog_Feed_Masonry {
		function init() {
			parent::init();
			$this->fb_support = false;
			$this->vb_support = 'partial';
			$this->post_types = array();
			$this->icon_path        =  plugin_dir_path( __FILE__ ) . 'icons/blogfeed_masonry_module.svg';
			$this->slug       .= '_agsdcm';
			if ( $this->child_slug ) {
				$this->child_slug .= '_agsdcm';
			}
			if ( ! empty( $this->advanced_fields['fonts']['header']['css'] ) ) {
				foreach ( $this->advanced_fields['fonts']['header']['css'] as &$selector ) {
					if ( substr( $selector, 0, 16 ) == '#page-container ' ) {
						$selector = substr( $selector, 16 );
					}
				}
			}
			if ( isset( $this->advanced_fields['border']['css']['main'] ) ) {
				$this->advanced_fields['border']['css']['main'] = array(
					'border_styles' => $this->advanced_fields['border']['css']['main'],
					'border_radii'  => $this->advanced_fields['border']['css']['main'],
				);
				$this->advanced_fields['borders']               = array( 'default' => $this->advanced_fields['border'], );
			}
			$this->advanced_fields['box_shadow'] = array( 'default' => array( 'css' => array( 'main' => ".posts-blog-feed-module.masonry{$this->main_css_element} .hentry" ) ) );
		}

		static function createAndSet( &$target, $set ) {
			if ( count( $set ) < 2 ) {
				return;
			}
			if ( count( $set ) == 2 ) {
				$target[ $set[0] ] = $set[1];
			} else {
				$newKey            = array_shift( $set );
				$target[ $newKey ] = array();
				self::createAndSet( $target[ $newKey ], $set );
			}
		}

		function shortcode_atts() {
			if ( empty( $GLOBALS['et_fb_processing_shortcode_object'] ) ) {
				$oldProps = $this->props;
				parent::shortcode_atts();
				foreach ( $this->props as $propName => &$propValue ) {
					if ( $propName == 'remove_drop_shadow' && isset( $oldProps[ $propName ] ) && ( $propValue === true || $propValue === false ) ) {
						$propValue = $oldProps[ $propName ];
					}
				}
			} else {
				$oldProps = $this->props;
				parent::shortcode_atts();
				foreach ( $this->props as $propName => &$propValue ) {
					if ( isset( $oldProps[ $propName ] ) && ( $propValue === true || $propValue === false ) ) {
						$propValue = $oldProps[ $propName ];
					}
				}
			}
		}

		function _add_link_options_fields() {
		}

		function _add_text_fields() {
		}

		function _add_background_fields() {
		}

		function process_advanced_text_options( $function_name ) {
		}

		function get_fields() {
			$fields = parent::get_fields();
			unset( $fields['show_rating'] );

			return agsdcm_process_module_fields( $fields, $this );
		}

		function _pre_wp_query( $args ) {
			return agsdcm_process_module_query( parent::_pre_wp_query( $args ), $this );
		}

		function render( $atts, $content = null, $function_name, $parent_address = '', $global_parent = '', $global_parent_type = '' ) {
			if ( empty( $GLOBALS['et_fb_processing_shortcode_object'] ) ) {
				$return = call_user_func_array( array( __CLASS__, 'parent::render' ), func_get_args() );
			} else {
				$templateName = $this->template_name;
				unset( $this->template_name );
				$return              = call_user_func_array( array( __CLASS__, 'parent::render' ), func_get_args() );
				$this->template_name = $templateName;
			}

			return $return;
		}

		function _render( $attrs, $content = null, $render_slug, $parent_address = '', $global_parent = '', $global_parent_type = '', $parent_type = '', $theme_builder_area = '' ) {
			add_filter( 'et_pb_set_style_selector', array( $this, 'filter_selector' ) );
			add_filter( 'et_builder_processed_range_value', array( $this, 'fix_range_value' ) );
			$return = call_user_func_array( array( __CLASS__, 'parent::_render' ), func_get_args() );
			remove_filter( 'et_pb_set_style_selector', array( $this, 'filter_selector' ) );
			remove_filter( 'et_builder_processed_range_value', array( $this, 'fix_range_value' ) );

			return $return;
		}

		function fix_range_value( $value ) {
			if ( $value == '0none' ) {
				return 'none';
			}

			return $value;
		}

		function before_render() {
			$this->props['border_radius'] = '';

			return call_user_func_array( array( __CLASS__, 'parent::before_render' ), func_get_args() );
		}

		function filter_selector( $selector ) {
			$selector = explode( ',', $selector );
			foreach ( $selector as &$selectorPart ) {
				$selectorPart = trim( $selectorPart );
				if ( strpos( $selectorPart, '.ags-divi-extras-module ' ) === false ) {
					$moduleClassPos = strpos( $selectorPart, '_agsdcm_' );
					if ( $moduleClassPos ) {
						$moduleClassPos = strrpos( $selectorPart, ' ', $moduleClassPos - strlen( $selectorPart ) );
						$selectorPart   = $moduleClassPos ? substr( $selectorPart, 0, $moduleClassPos ) . ' .ags-divi-extras-module' . substr( $selectorPart, $moduleClassPos ) : '.ags-divi-extras-module ' . $selectorPart;
					}
				}
			}

			return implode( ',', $selector );
		}

		function _set_fields_unprocessed( $fields ) {
			$unsupportedFields = array(
				'module_alignment'             => true,
				'module_alignment_tablet'      => true,
				'module_alignment_phone'       => true,
				'module_alignment_last_edited' => true,
				'border_radius'                => true
			);

			return parent::_set_fields_unprocessed( array_diff_key( $fields, $unsupportedFields ) );
		}

		function output() {
			$this->props['show_rating'] = 'off';
			$this->props['content']     = $this->content;
			extract( $this->props );
			ob_start();
			$templatePath = locate_template( $this->template_name . '.php' );
			require( empty( $templatePath ) ? dirname( __FILE__ ) . '/templates/' . $this->template_name . '.php' : $templatePath );

			return '<div class="ags-divi-extras-module">' . ob_get_clean() . '</div>';
		}
	}

	new ET_Builder_Module_Posts_Blog_Feed_Masonry_AGSDCM;
}
if ( class_exists( 'ET_Builder_Module_Ads' ) && ! class_exists( 'ET_Builder_Module_Ads_AGSDCM' ) ) {
	class ET_Builder_Module_Ads_AGSDCM extends ET_Builder_Module_Ads {
		function init() {
			parent::init();
			$this->fb_support = false;
			$this->vb_support = 'partial';
			$this->post_types = array();
			$this->icon_path        =  plugin_dir_path( __FILE__ ) . 'icons/ad.svg';
			$this->slug       .= '_agsdcm';
			if ( $this->child_slug ) {
				$this->child_slug .= '_agsdcm';
			}
			if ( ! empty( $this->advanced_fields['fonts']['header']['css'] ) ) {
				foreach ( $this->advanced_fields['fonts']['header']['css'] as &$selector ) {
					if ( substr( $selector, 0, 16 ) == '#page-container ' ) {
						$selector = substr( $selector, 16 );
					}
				}
			}
			if ( isset( $this->advanced_fields['border']['css']['main'] ) ) {
				$this->advanced_fields['border']['css']['main'] = array(
					'border_styles' => $this->advanced_fields['border']['css']['main'],
					'border_radii'  => $this->advanced_fields['border']['css']['main'],
				);
				$this->advanced_fields['borders']               = array( 'default' => $this->advanced_fields['border'], );
			}
			self::createAndSet( $this->advanced_fields, array(
				'borders',
				'default',
				'defaults',
				'border_radii',
				'on|3px|3px|3px|3px'
			) );
		}

		static function createAndSet( &$target, $set ) {
			if ( count( $set ) < 2 ) {
				return;
			}
			if ( count( $set ) == 2 ) {
				$target[ $set[0] ] = $set[1];
			} else {
				$newKey            = array_shift( $set );
				$target[ $newKey ] = array();
				self::createAndSet( $target[ $newKey ], $set );
			}
		}

		function shortcode_atts() {
			if ( empty( $GLOBALS['et_fb_processing_shortcode_object'] ) ) {
				$oldProps = $this->props;
				parent::shortcode_atts();
				foreach ( $this->props as $propName => &$propValue ) {
					if ( $propName == 'remove_drop_shadow' && isset( $oldProps[ $propName ] ) && ( $propValue === true || $propValue === false ) ) {
						$propValue = $oldProps[ $propName ];
					}
				}
			} else {
				$oldProps = $this->props;
				parent::shortcode_atts();
				foreach ( $this->props as $propName => &$propValue ) {
					if ( isset( $oldProps[ $propName ] ) && ( $propValue === true || $propValue === false ) ) {
						$propValue = $oldProps[ $propName ];
					}
				}
			}
		}

		function _add_link_options_fields() {
		}

		function _add_text_fields() {
		}

		function process_advanced_text_options( $function_name ) {
		}

		function _pre_wp_query( $args ) {
			return agsdcm_process_module_query( parent::_pre_wp_query( $args ), $this );
		}

		function render( $atts, $content = null, $function_name, $parent_address = '', $global_parent = '', $global_parent_type = '' ) {
			if ( empty( $GLOBALS['et_fb_processing_shortcode_object'] ) ) {
				$return = call_user_func_array( array( __CLASS__, 'parent::render' ), func_get_args() );
			} else {
				$templateName = $this->template_name;
				unset( $this->template_name );
				$return              = call_user_func_array( array( __CLASS__, 'parent::render' ), func_get_args() );
				$this->template_name = $templateName;
			}

			return $return;
		}

		function _render( $attrs, $content = null, $render_slug, $parent_address = '', $global_parent = '', $global_parent_type = '', $parent_type = '', $theme_builder_area = '' ) {
			add_filter( 'et_pb_set_style_selector', array( $this, 'filter_selector' ) );
			add_filter( 'et_builder_processed_range_value', array( $this, 'fix_range_value' ) );
			$return = call_user_func_array( array( __CLASS__, 'parent::_render' ), func_get_args() );
			remove_filter( 'et_pb_set_style_selector', array( $this, 'filter_selector' ) );
			remove_filter( 'et_builder_processed_range_value', array( $this, 'fix_range_value' ) );

			return $return;
		}

		function fix_range_value( $value ) {
			if ( $value == '0none' ) {
				return 'none';
			}

			return $value;
		}

		function before_render() {
			$this->props['border_radius'] = '';

			return call_user_func_array( array( __CLASS__, 'parent::before_render' ), func_get_args() );
		}

		function filter_selector( $selector ) {
			$selector = explode( ',', $selector );
			foreach ( $selector as &$selectorPart ) {
				$selectorPart = trim( $selectorPart );
				if ( strpos( $selectorPart, '.ags-divi-extras-module ' ) === false ) {
					$moduleClassPos = strpos( $selectorPart, '_agsdcm_' );
					if ( $moduleClassPos ) {
						$moduleClassPos = strrpos( $selectorPart, ' ', $moduleClassPos - strlen( $selectorPart ) );
						$selectorPart   = $moduleClassPos ? substr( $selectorPart, 0, $moduleClassPos ) . ' .ags-divi-extras-module' . substr( $selectorPart, $moduleClassPos ) : '.ags-divi-extras-module ' . $selectorPart;
					}
				}
			}

			return implode( ',', $selector );
		}

		function _set_fields_unprocessed( $fields ) {
			$unsupportedFields = array(
				'module_alignment'             => true,
				'module_alignment_tablet'      => true,
				'module_alignment_phone'       => true,
				'module_alignment_last_edited' => true,
				'border_radius'                => true
			);

			return parent::_set_fields_unprocessed( array_diff_key( $fields, $unsupportedFields ) );
		}

		function output() {
			$this->props['show_rating'] = 'off';
			$this->props['content']     = $this->content;
			extract( $this->props );
			ob_start();
			$templatePath = locate_template( $this->template_name . '.php' );
			require( empty( $templatePath ) ? dirname( __FILE__ ) . '/templates/' . $this->template_name . '.php' : $templatePath );

			return '<div class="ags-divi-extras-module">' . ob_get_clean() . '</div>';
		}
	}

	new ET_Builder_Module_Ads_AGSDCM;
}
if ( class_exists( 'ET_Builder_Module_Ads_Ad' ) && ! class_exists( 'ET_Builder_Module_Ads_Ad_AGSDCM' ) ) {
	class ET_Builder_Module_Ads_Ad_AGSDCM extends ET_Builder_Module_Ads_Ad {
		function init() {
			parent::init();
			$this->fb_support = false;
			$this->vb_support = 'partial';
			$this->post_types = array();
			$this->slug       .= '_agsdcm';
			if ( $this->child_slug ) {
				$this->child_slug .= '_agsdcm';
			}
			if ( ! empty( $this->advanced_fields['fonts']['header']['css'] ) ) {
				foreach ( $this->advanced_fields['fonts']['header']['css'] as &$selector ) {
					if ( substr( $selector, 0, 16 ) == '#page-container ' ) {
						$selector = substr( $selector, 16 );
					}
				}
			}
			if ( isset( $this->advanced_fields['border']['css']['main'] ) ) {
				$this->advanced_fields['border']['css']['main'] = array(
					'border_styles' => $this->advanced_fields['border']['css']['main'],
					'border_radii'  => $this->advanced_fields['border']['css']['main'],
				);
				$this->advanced_fields['borders']               = array( 'default' => $this->advanced_fields['border'], );
			}
		}

		static function createAndSet( &$target, $set ) {
			if ( count( $set ) < 2 ) {
				return;
			}
			if ( count( $set ) == 2 ) {
				$target[ $set[0] ] = $set[1];
			} else {
				$newKey            = array_shift( $set );
				$target[ $newKey ] = array();
				self::createAndSet( $target[ $newKey ], $set );
			}
		}

		function shortcode_atts() {
			if ( empty( $GLOBALS['et_fb_processing_shortcode_object'] ) ) {
				$oldProps = $this->props;
				parent::shortcode_atts();
				foreach ( $this->props as $propName => &$propValue ) {
					if ( $propName == 'remove_drop_shadow' && isset( $oldProps[ $propName ] ) && ( $propValue === true || $propValue === false ) ) {
						$propValue = $oldProps[ $propName ];
					}
				}
			} else {
				$oldProps = $this->props;
				parent::shortcode_atts();
				foreach ( $this->props as $propName => &$propValue ) {
					if ( isset( $oldProps[ $propName ] ) && ( $propValue === true || $propValue === false ) ) {
						$propValue = $oldProps[ $propName ];
					}
				}
			}
		}

		function _add_link_options_fields() {
		}

		function _add_text_fields() {
		}

		function process_advanced_text_options( $function_name ) {
		}

		function _pre_wp_query( $args ) {
			return agsdcm_process_module_query( parent::_pre_wp_query( $args ), $this );
		}

		function render( $atts, $content = null, $function_name, $parent_address = '', $global_parent = '', $global_parent_type = '' ) {
			if ( empty( $GLOBALS['et_fb_processing_shortcode_object'] ) ) {
				$return = call_user_func_array( array( __CLASS__, 'parent::render' ), func_get_args() );
			} else {
				$templateName = $this->template_name;
				unset( $this->template_name );
				$return              = call_user_func_array( array( __CLASS__, 'parent::render' ), func_get_args() );
				$this->template_name = $templateName;
			}

			return $return;
		}

		function _render( $attrs, $content = null, $render_slug, $parent_address = '', $global_parent = '', $global_parent_type = '', $parent_type = '', $theme_builder_area = '' ) {
			add_filter( 'et_pb_set_style_selector', array( $this, 'filter_selector' ) );
			add_filter( 'et_builder_processed_range_value', array( $this, 'fix_range_value' ) );
			$return = call_user_func_array( array( __CLASS__, 'parent::_render' ), func_get_args() );
			remove_filter( 'et_pb_set_style_selector', array( $this, 'filter_selector' ) );
			remove_filter( 'et_builder_processed_range_value', array( $this, 'fix_range_value' ) );

			return $return;
		}

		function fix_range_value( $value ) {
			if ( $value == '0none' ) {
				return 'none';
			}

			return $value;
		}

		function before_render() {
			$this->props['border_radius'] = '';

			return call_user_func_array( array( __CLASS__, 'parent::before_render' ), func_get_args() );
		}

		function filter_selector( $selector ) {
			$selector = explode( ',', $selector );
			foreach ( $selector as &$selectorPart ) {
				$selectorPart = trim( $selectorPart );
				if ( strpos( $selectorPart, '.ags-divi-extras-module ' ) === false ) {
					$moduleClassPos = strpos( $selectorPart, '_agsdcm_' );
					if ( $moduleClassPos ) {
						$moduleClassPos = strrpos( $selectorPart, ' ', $moduleClassPos - strlen( $selectorPart ) );
						$selectorPart   = $moduleClassPos ? substr( $selectorPart, 0, $moduleClassPos ) . ' .ags-divi-extras-module' . substr( $selectorPart, $moduleClassPos ) : '.ags-divi-extras-module ' . $selectorPart;
					}
				}
			}

			return implode( ',', $selector );
		}

		function _set_fields_unprocessed( $fields ) {
			$unsupportedFields = array(
				'module_alignment'             => true,
				'module_alignment_tablet'      => true,
				'module_alignment_phone'       => true,
				'module_alignment_last_edited' => true,
				'border_radius'                => true
			);

			return parent::_set_fields_unprocessed( array_diff_key( $fields, $unsupportedFields ) );
		}

		function output() {
			$this->props['show_rating'] = 'off';
			$this->props['content']     = $this->content;
			extract( $this->props );
			ob_start();
			$templatePath = locate_template( $this->template_name . '.php' );
			require( empty( $templatePath ) ? dirname( __FILE__ ) . '/templates/' . $this->template_name . '.php' : $templatePath );

			return ob_get_clean();
		}
	}

	new ET_Builder_Module_Ads_Ad_AGSDCM;
}
