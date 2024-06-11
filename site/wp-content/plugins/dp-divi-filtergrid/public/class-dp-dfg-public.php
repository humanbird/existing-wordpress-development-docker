<?php

/**
 * The public-facing functionality of the plugin.
 *
 * @link       http://www.diviplugins.com
 * @since      1.0.0
 *
 * @package    Dp_Cpt_Filterable_Module
 * @subpackage Dp_Cpt_Filterable_Module/public
 */

/**
 * The public-facing functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the public-facing stylesheet and JavaScript.
 *
 * @package    Dp_Cpt_Filterable_Module
 * @subpackage Dp_Cpt_Filterable_Module/public
 * @author     DiviPlugins <support@diviplugins.com>
 */
class Dp_Dfg_Public {

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
	 * @param string $plugin_name The name of the plugin.
	 * @param string $version The version of this plugin.
	 *
	 * @since    1.0.0
	 */
	public function __construct( $plugin_name, $version ) {
		$this->plugin_name = $plugin_name;
		$this->version     = $version;
	}

	/**
	 * Retrieve the template for the popup feature
	 *
	 * @since    1.0.0
	 */
	public function popup_page_template( $template ) {
		if ( isset( $_GET['_wpnonce'] ) && wp_verify_nonce( sanitize_key( $_GET['_wpnonce'] ), 'dfg_popup_fetch' ) ) {
			if ( is_singular() && isset( $_GET['dp_action'] ) && 'dfg_popup_fetch' === $_GET['dp_action'] ) {
				if ( isset( $_GET['popup_template'] ) && 'default' === $_GET['popup_template'] ) {
					if ( glob( get_stylesheet_directory() . '/dp-divi-filtergrid/dp-dfg-popup-template.php' ) ) {
						$template = get_stylesheet_directory() . '/dp-divi-filtergrid/dp-dfg-popup-template.php';
					} else {
						$template = DPDFG_DIR . 'templates/dp-dfg-popup-template.php';
					}
				} else {
					$template = DPDFG_DIR . 'templates/dp-dfg-popup-template.php';
				}
			}
		}

		return $template;
	}

	/**
	 * Register the JavaScript for the public area.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_scripts() {
		wp_register_script( 'fitvids', DPDFG_URL . 'vendor/jquery.fitvids.min.js', array( 'jquery' ), DPDFG_VERSION, true );
		wp_register_script( 'magnific-popup', DPDFG_URL . 'vendor/jquery.magnific-popup.min.js', array( 'jquery' ), DPDFG_VERSION, true );
		wp_register_script( 'dp-divi-filtergrid-imagesloaded', DPDFG_URL . 'vendor/imagesloaded.pkgd.min.js', array( 'jquery' ), DPDFG_VERSION, true );
		wp_register_script( 'dp-divi-filtergrid-desandro-masonry', DPDFG_URL . 'vendor/masonry.pkgd.min.js', array( 'jquery' ), DPDFG_VERSION, true );
		if ( function_exists( 'et_core_is_fb_enabled' ) && et_core_is_fb_enabled() && ! wp_script_is( 'dp-dfg-admin-cpt-modal' ) ) {
			Dp_Dfg_Utils::enqueue_and_localize_cpt_modal_script();
		}
	}

}
