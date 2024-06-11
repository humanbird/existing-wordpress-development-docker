<?php
/**
 * Plugin Name: Divi Extras
 * Description: Extra Theme blog modules added to Divi Builder
 * Plugin URI: https://divi.space/product/divi-extras/
 * Version: 1.1.11
 * Author: Divi Space
 * Author URI: https://divi.space/
 * License: GNU General Public License version 3 or later
 * License URI: https://www.gnu.org/licenses/gpl-3.0.en.html
 * Text Domain: ds-divi-extras
 * Domain Path: /languages
 * GitLab Plugin URI: https://gitlab.com/aspengrovestudios/ds-divi-extras
 * AGS Info: ids.aspengrove 297196 ids.divispace 297149 legacy.key AGSDCM_license_key legacy.status AGSDCM_license_status adminPage admin.php?page=ds-divi-extras docs https://support.aspengrovestudios.com/article/404-divi-extras
 */

/*
	Divi Extras plugin
	Copyright (C) 2021  Aspen Grove Studios

    This program is free software: you can redistribute it and/or modify
    it under the terms of the GNU General Public License as published by
    the Free Software Foundation, either version 3 of the License, or
    (at your option) any later version.

    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    You should have received a copy of the GNU General Public License
    along with this program.  If not, see <https://www.gnu.org/licenses/>.


Credits:

This plugin contains code based on and/or copied from the Extra theme by Elegant Themes.
Files in the ./extra-images directory also copied from the Extra theme by Elegant Themes.
The ./css/core and ./css/fonts directories, including the names and contents of those directories and all
subdirectories, also copied from the Extra theme by Elegant Themes.
All code and files copied from the Extra theme by Elegant Themes are released under the GNU General Public License version 2 or later
and licensed here under the GNU General Public License version 3 or later.

See ./license.txt for the text of the GNU General Public License, version 3.

*/

define('AGS_DIVI_CAT_MODULES_VERSION', '1.1.11');
define('AGS_DIVI_CAT_MODULES_URL', plugins_url('/', __FILE__));
define('AGS_DIVI_CAT_MODULES_FILE', __FILE__);


include_once(dirname(__FILE__).'/updater/updater.php');


function agsdcm_admin_scripts() {
	wp_enqueue_style('ds-divi-extras', AGS_DIVI_CAT_MODULES_URL.'css/admin.css', array(), AGS_DIVI_CAT_MODULES_VERSION);
	wp_enqueue_style('agsdcm-visual-builder', AGS_DIVI_CAT_MODULES_URL.'css/visual-builder.css', array(), AGS_DIVI_CAT_MODULES_VERSION ); // classic editor
	
	wp_enqueue_script('agsdcm-builder', AGS_DIVI_CAT_MODULES_URL.'js/builder.js', array('jquery'), AGS_DIVI_CAT_MODULES_VERSION, true);

	
	if (!AGSDCM_has_license_key()) {
		add_action('admin_notices', 'agsdcm_activate_notice');
	}

}
add_action('admin_enqueue_scripts', 'agsdcm_admin_scripts');

function agsdcm_divi_scripts() {
	wp_enqueue_script('agsdcm', AGS_DIVI_CAT_MODULES_URL.'js/extra.js', array('jquery'), AGS_DIVI_CAT_MODULES_VERSION);
	// from wp-layouts\integrations\Divi\Divi.php
	if ( function_exists('et_core_is_fb_enabled') && et_core_is_fb_enabled() ) {
		wp_enqueue_script('agsdcm-builder', AGS_DIVI_CAT_MODULES_URL.'js/builder.js', array('jquery'), AGS_DIVI_CAT_MODULES_VERSION, true);
		wp_enqueue_style('agsdcm-visual-builder', AGS_DIVI_CAT_MODULES_URL.'css/visual-builder.css', array(), AGS_DIVI_CAT_MODULES_VERSION );
	}
	wp_enqueue_script('agsdcm-imagesloaded', AGS_DIVI_CAT_MODULES_URL.'js/imagesloaded.js', array(), AGS_DIVI_CAT_MODULES_VERSION);
	wp_enqueue_style('agsdcm', AGS_DIVI_CAT_MODULES_URL.'css/divi.css', array(), AGS_DIVI_CAT_MODULES_VERSION);
	
	// From Extra includes/core.php
	wp_localize_script( 'agsdcm', 'EXTRA', array(
			'ajaxurl'                      => set_url_scheme( admin_url( 'admin-ajax.php' ) ),
			'blog_feed_nonce'              => wp_create_nonce( 'blog_feed_nonce' ),
		) );
}
function agsdcm_extra_scripts() {
	wp_enqueue_style('agsdcm', AGS_DIVI_CAT_MODULES_URL.'css/extra.css', array(), AGS_DIVI_CAT_MODULES_VERSION);
	// from wp-layouts\integrations\Divi\Divi.php
	if ( function_exists('et_core_is_fb_enabled') && et_core_is_fb_enabled() ) {
		wp_enqueue_script('agsdcm-builder', AGS_DIVI_CAT_MODULES_URL.'js/builder.js', array('jquery'), AGS_DIVI_CAT_MODULES_VERSION, true);
		wp_enqueue_style('agsdcm-visual-builder', AGS_DIVI_CAT_MODULES_URL.'css/visual-builder.css', array(), AGS_DIVI_CAT_MODULES_VERSION );
	}

	wp_enqueue_script('agsdcm', AGS_DIVI_CAT_MODULES_URL.'js/extra.js', array('jquery'), AGS_DIVI_CAT_MODULES_VERSION);

	wp_enqueue_script('agsdcm-imagesloaded', AGS_DIVI_CAT_MODULES_URL.'js/imagesloaded.js', array(), AGS_DIVI_CAT_MODULES_VERSION);
	wp_enqueue_style('agsdcm', AGS_DIVI_CAT_MODULES_URL.'css/divi.css', array(), AGS_DIVI_CAT_MODULES_VERSION);
	
	// From Extra includes/core.php
	wp_localize_script( 'agsdcm', 'EXTRA', array(
			'ajaxurl'                      => set_url_scheme( admin_url( 'admin-ajax.php' ) ),
			'blog_feed_nonce'              => wp_create_nonce( 'blog_feed_nonce' ),
		) );
}

function agsdcm_process_module_styles($styles) {
	if (is_array($styles)) {
		foreach ($styles as &$styles2) {
			$styles2 = agsdcm_process_module_styles($styles2);
		}
	} else if (strpos($styles, 'ags-divi-extras-module') === false) {
		return preg_replace('/\\.et_pb_([^\\S\\{])_agsdcm/U', '.ags-divi-extras-module .et_pb_$1_agsdcm', $styles);
	}
	return $styles;
}
add_filter('et_core_page_resource_get_data', 'agsdcm_process_module_styles');

function agsdcm_intercept_fb_ajax_render_shortcode() {
	ob_start();
	add_filter('wp_die_ajax_handler',  function() {
		return function() {
			$response = json_decode(ob_get_clean(), true);
			// Will be false the second time this filter runs (due to the wp_send_json_success
			// call in this function) since there won't be an output buffer anymore
			if (!empty($response['data'])) {
				$styleClassPos = strpos($response['data'], 'et-builder-advanced-style');
				$styleStartPos = strpos($response['data'], '>', $styleClassPos) + 1;
				$styleEndPos = strpos($response['data'], '</style>', $styleStartPos); // exclusive
				if ($styleClassPos !== false && $styleStartPos !== false && $styleEndPos !== false) {
					$styles = trim(substr($response['data'], $styleStartPos, $styleEndPos - $styleStartPos));
					$styles = agsdcm_process_module_styles($styles);
					$response['data'] = substr($response['data'], 0, $styleStartPos).$styles.substr($response['data'], $styleEndPos);
					wp_send_json_success($response['data']);
				}
			}
		};
	}, 1);
}
add_action('wp_ajax_et_fb_ajax_render_shortcode', 'agsdcm_intercept_fb_ajax_render_shortcode', 1);



function agsdcm_init() {
	$theme = wp_get_theme();
	define('AGS_DIVI_CAT_MODULES_THEME_IS_EXTRA', 'Extra' == $theme->name || 'Extra' == $theme->parent_theme || apply_filters('divi_ghoster_ghosted_theme', null) === 'Extra');

	if (!AGS_DIVI_CAT_MODULES_THEME_IS_EXTRA) {
		
		// Extra/functions.php
		define( 'EXTRA_LAYOUT_POST_TYPE', 'layout' );
		
		// Make sure we require layout functions provided by Extra
		require __DIR__.'/includes/layouts.php';
		if ( is_admin() ) {
			require __DIR__.'/includes/admin.php';
			require __DIR__.'/includes/category.php';
		}
		
		/*
			Extra source file: includes/core.php, function: extra_add_image_sizes
		*/
		$sizes = array(
			'extra-image-huge'          => array(
				'width'  => 1280,
				'height' => 768,
				'crop'   => true,
			),
			'extra-image-single-post'   => array(
				'width'  => 1280,
				'height' => 640,
				'crop'   => true,
			),
			'extra-image-medium'        => array(
				'width'  => 627,
				'height' => 376,
				'crop'   => true,
			),
			'extra-image-small'         => array(
				'width'  => 440,
				'height' => 264,
				'crop'   => true,
			),
			'extra-image-square-medium' => array(
				'width'  => 440,
				'height' => 440,
				'crop'   => true,
			),
			'extra-image-square-small'  => array(
				'width'  => 150,
				'height' => 150,
				'crop'   => true,
			),
		);

		foreach ( $sizes as $name => $size_info ) {
			add_image_size( $name, $size_info['width'], $size_info['height'], $size_info['crop'] );
		}
		
		$extraLangPath = basename(__DIR__).'/extra-lang/';
		load_plugin_textdomain('extra', false, $extraLangPath);
		load_plugin_textdomain('ds-divi-extras', false, basename( dirname( __FILE__ ) ) . '/languages/' );

	}
	
}
add_action('init', 'agsdcm_init');


function agsdcm_load_modules(){

	if (

	        AGSDCM_has_license_key() &&

            class_exists('ET_Builder_Module')) {
		
		$pluginDir = dirname(__FILE__);

		include($pluginDir.'/includes/modules'.(AGS_DIVI_CAT_MODULES_THEME_IS_EXTRA ? '-extra' : '').'.php');
		
		if (AGS_DIVI_CAT_MODULES_THEME_IS_EXTRA) {
			add_action('wp_enqueue_scripts', 'agsdcm_extra_scripts', 999);
		} else {
			add_action('wp_enqueue_scripts', 'agsdcm_divi_scripts', 999);
		}
		
	}
}

add_action('et_builder_ready', 'agsdcm_load_modules', 99);


function agsdcm_process_module_fields($fields, $element) {
	if (isset($fields['category_id']['renderer_options']['custom_items'])) {
		foreach ($fields['category_id']['renderer_options']['custom_items'] as $i => $customItem) {
			if ((int) $customItem['term_id'] == -1) {
				unset($fields['category_id']['renderer_options']['custom_items'][$i]);
			}
		}
	}
	
	$fields['tags'] = array(
		'label'           => esc_html__( 'Tags', 'ds-divi-extras' ),
		'type'            => 'text',
		'description'     => esc_html__( 'Enter tags separated by commas.', 'ds-divi-extras' ),
		'priority'        => 1,
		'option_category' => 'configuration',
		'toggle_slug'     => 'main_content',
	);
	$fields['posts_filter_any'] = array(
		'label'           => esc_html__( 'Show posts matching any of the above criteria', 'ds-divi-extras' ),
		'type'            => 'yes_no_button',
		'options'         => array(
			'off' => esc_html__( 'No', 'ds-divi-extras' ),
			'on'  => esc_html__( 'Yes', 'ds-divi-extras' ),
		),
		'description'     => esc_html__( 'If unchecked, only posts matching all of the above criteria (categories and tags) will be shown. If multiple categories or tags are specified, posts will only need to match one of each.', 'ds-divi-extras' ),
		'priority'        => 1,
		'option_category' => 'configuration',
		'toggle_slug'     => 'main_content',
	);
	
	// Set defaults on toggle fields
	foreach ($fields as &$field) {
		if (!empty($field['options']) && empty($field['default'])) {
			$field['default'] = key($field['options']);
		}
	}
	
	return $fields;
}

function agsdcm_process_module_query($args, $element) {
	
	if (!empty($element->shortcode_atts['tags'])) {
		$tags = array_map('trim', explode(',', $element->shortcode_atts['tags']));
		if (empty($args['tax_query'])) {
			$args['tax_query'] = array(
				array(
					'taxonomy' => 'post_tag',
					'field' => 'name',
					'terms' => $tags
				)
			);
		} else {
			$args['tax_query'][] = array(
				'taxonomy' => 'post_tag',
				'field' => 'name',
				'terms' => $tags
			);
			$args['tax_query']['relation'] = (empty($element->shortcode_atts['posts_filter_any']) ? 'AND' : 'OR');
		}
	}
	
	return $args;

}

function agsdcm_admin_menu() {
	add_submenu_page( 'et_extra_options', 
		__( 'Divi Extras', 'ds-divi-extras' ),
		__( 'Divi Extras', 'ds-divi-extras' ),
		'manage_options',
		'ds-divi-extras',
		'agsdcm_admin_page'
	);
	add_submenu_page( 'et_divi_options', 
		__( 'Divi Extras', 'ds-divi-extras' ),
		__( 'Divi Extras', 'ds-divi-extras' ),
		'manage_options',
		'ds-divi-extras',
		'agsdcm_admin_page'
	);
}
add_action('admin_menu', 'agsdcm_admin_menu', 100);

function agsdcm_admin_page() {
	if (AGSDCM_has_license_key()) { ?>
		<div id="ds-extras-settings" class="wrap">
            <div id="ags-settings-header">
                <h1><?php echo esc_html__('Divi Extras', 'ds-divi-extras');?></h1>
                    <div id="ags-settings-header-links">
                        <a id="ags-settings-header-link-support" href="https://support.aspengrovestudios.com/article/404-divi-extras"
                           target="_blank"><?php esc_html_e('Support', 'ds-divi-extras') ?></a>
                    </div>
                </div>
                <ul id="ags-settings-tabs">
                    <li class="ags-settings-active">
                        <a href="#about"><?php esc_html_e('About', 'ds-divi-extras') ?></a>
                    </li>
                    <li>
                        <a href="#license"><?php esc_html_e('License Key', 'ds-divi-extras') ?></a>
                    </li>

                </ul>

                <div id="ags-settings-tabs-content">
                    <div id="ags-settings-about">
                        <?php esc_html_e('All the power of the Extra theme in the Divi Builder. Use modules from the Extra theme with the Divi Builder and create unique layouts for pages and posts.', 'ds-divi-extras') ?>
                    </div>
                    <div id="ags-settings-license" class="ags-settings-active">
                        <?php AGSDCM_license_key_box();?>
                    </div>
                </div>
        </div>
        <script>
            var divi_extras_tabs_navigate = function() {
                jQuery('#ags-settings-tabs-content > div, #ags-settings-tabs > li').removeClass('ags-settings-active');
                jQuery('#ags-settings-'+location.hash.substr(1)).addClass('ags-settings-active');
                jQuery('#ags-settings-tabs > li:has(a[href="'+location.hash+'"])').addClass('ags-settings-active');
            };

            if (location.hash) {
                divi_extras_tabs_navigate();
            }

            jQuery(window).on('hashchange', divi_extras_tabs_navigate);
        </script>

        <?php
	} else {
		AGSDCM_activate_page();
	}
}

// Add settings link on plugin page
function agsdcm_plugin_action_links($links) {
  array_unshift($links, '<a href="admin.php?page=ds-divi-extras">'.(AGSDCM_has_license_key() ? esc_html__('License Key Information', 'ds-divi-extras') : esc_html__('Activate License Key', 'ds-divi-extras')) .'</a>');
  return $links;
}

function agsdcm_plugins_php() {
	$plugin = plugin_basename(__FILE__); 
	add_filter('plugin_action_links_'.$plugin, 'agsdcm_plugin_action_links' );
}
add_action('load-plugins.php', 'agsdcm_plugins_php');

function agsdcm_activate_notice() {
	echo('<div class="notice notice-warning"><p>'
			.sprintf(esc_html__('You haven\'t activated your Divi Extras license key yet. Please %sactivate your license key%s to use the Divi Extras modules.', 'ds-divi-extras'),
				'<a href="'.esc_url(admin_url('admin.php?page=ds-divi-extras')).'">',
				'</a>'
			)
		.'</p></div>');
}

