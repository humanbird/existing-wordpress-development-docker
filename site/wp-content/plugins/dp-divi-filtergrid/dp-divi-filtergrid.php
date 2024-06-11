<?php

/*
  Plugin Name: Divi FilterGrid
  Plugin URI:  https://diviplugins.com/downloads/divi-filtergrid/
  Description: Create a beautiful grid layout of any post type with full column control. Includes options to display filters, true ajax pagination or load more, and skin options to easily modify the appearance of each grid item with one click.
  Version: 3.0.2
  Author: DiviPlugins
  Author URI:  http://diviplugins.com
  License: GPL2
  License URI: https://www.gnu.org/licenses/gpl-2.0.html
  Text Domain: dpdfg-dp-divi-filtergrid
  Domain Path: /languages
  Update URI: https://diviplugins.com
 */

define( 'DPDFG_VERSION', '3.0.2' );
define( 'DPDFG_URL', plugin_dir_url( __FILE__ ) );
define( 'DPDFG_DIR', plugin_dir_path( __FILE__ ) );
define( 'DPDFG_STORE_URL', 'https://diviplugins.com/' );
define( 'DPDFG_ITEM_NAME', 'Divi FilterGrid' );
define( 'DPDFG_ITEM_ID', '50995' );

require DPDFG_DIR . 'includes/class-dp-divi-filtergrid.php';

/**
 * Begins execution of the plugin.
 *
 * Since everything within the plugin is registered via hooks,
 * then kicking off the plugin from this point in the file does
 * not affect the page life cycle.
 *
 * @since 1.0.0
 */
if ( ! function_exists( 'run_dp_divi_filtergrid' ) ) {
	function run_dp_divi_filtergrid() {
		$plugin = new Dp_Divi_FilterGrid();
		$plugin->run();
	}

	run_dp_divi_filtergrid();
}
