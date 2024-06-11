<?php
/**
 * The admin-specific functionality of the plugin.
 *
 * @link       http://www.diviplugins.com
 * @since      1.0.0
 *
 * @package    Dp_Cpt_Filterable_Module
 * @subpackage Dp_Cpt_Filterable_Module/admin
 */

/**
 * The admin-specific functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the admin-specific stylesheet and JavaScript.
 *
 * @package    Dp_Cpt_Filterable_Module
 * @subpackage Dp_Cpt_Filterable_Module/admin
 * @author     DiviPlugins <support@diviplugins.com>
 */
class Dp_Dfg_Admin {

	/**
	 * The ID of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string $plugin_name The ID of this plugin.
	 */
	private $plugin_name;

	/**
	 * The version of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string $version The current version of this plugin.
	 */
	private $version;

	/**
	 * Initialize the class and set its properties.
	 *
	 * @param string $plugin_name The name of this plugin.
	 * @param string $version The version of this plugin.
	 *
	 * @since    1.0.0
	 */
	public function __construct( $plugin_name, $version ) {
		$this->plugin_name = $plugin_name;
		$this->version     = $version;
	}

	/**
	 * Register the JavaScript/CSS for the admin area.
	 *
	 * @param $hook
	 *
	 * @since    1.0.0
	 */
	public function enqueue_scripts_styles( $hook ) {
		if ( in_array( $hook, array( 'post.php', 'divi_page_et_theme_builder', 'extra_page_et_theme_builder' ) ) ) {
			Dp_Dfg_Utils::enqueue_and_localize_cpt_modal_script();
		}
	}

	/**
	 * Initialize Divi extension
	 *
	 * @since    1.0.0
	 */
	public function initialize_extension() {
		require_once DPDFG_DIR . 'includes/DpDiviFilterGrid.php';
	}

	/**
	 * @param $links
	 * @param $file
	 *
	 * @return mixed
	 */
	public function add_plugin_row_meta( $links, $file ) {
		if ( plugin_basename( 'dp-divi-filtergrid/dp-divi-filtergrid.php' ) === $file ) {
			$links['license'] = sprintf( '<a href="%s"> %s </a>', admin_url( 'plugins.php?page=dp_divi_plugins_menu' ), __( 'License', 'dpdfg-dp-divi-filtergrid' ) );
			$links['support'] = sprintf( '<a href="%s" target="_blank"> %s </a>', 'https://diviplugins.com/documentation/divi-filtergrid/', __( 'Get support', 'dpdfg-dp-divi-filtergrid' ) );
		}

		return $links;
	}

}
