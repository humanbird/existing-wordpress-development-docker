<?php

/**
 * Define the internationalization functionality
 *
 * Loads and defines the internationalization files for this plugin
 * so that it is ready for translation.
 *
 * @link       http://www.diviplugins.com
 * @since      1.0.0
 *
 * @package    Dp_Cpt_Filterable_Module
 * @subpackage Dp_Cpt_Filterable_Module/includes
 */

/**
 * Define the internationalization functionality.
 *
 * Loads and defines the internationalization files for this plugin
 * so that it is ready for translation.
 *
 * @since      1.0.0
 * @package    Dp_Cpt_Filterable_Module
 * @subpackage Dp_Cpt_Filterable_Module/includes
 * @author     DiviPlugins <support@diviplugins.com>
 */
class Dp_Dfg_i18n {

	/**
	 * Load the plugin text domain for translation.
	 *
	 * @since    1.0.0
	 */
	public function load_plugin_textdomain() {
		load_plugin_textdomain( 'dpdfg-dp-divi-filtergrid', false, basename( DPDFG_DIR ) . '/languages/' );
	}

}
