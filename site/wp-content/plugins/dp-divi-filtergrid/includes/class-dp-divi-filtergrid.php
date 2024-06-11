<?php

/**
 * The file that defines the core plugin class
 *
 * A class definition that includes attributes and functions used across both the
 * public-facing side of the site and the admin area.
 *
 * @link       http://www.diviplugins.com
 * @since      1.0.0
 *
 * @package    Dp_Cpt_Filterable_Module
 * @subpackage Dp_Cpt_Filterable_Module/includes
 */

/**
 * The core plugin class.
 *
 * This is used to define internationalization, admin-specific hooks, and
 * public-facing site hooks.
 *
 * Also maintains the unique identifier of this plugin as well as the current
 * version of the plugin.
 *
 * @since      1.0.0
 * @package    Dp_Cpt_Filterable_Module
 * @subpackage Dp_Cpt_Filterable_Module/includes
 * @author     DiviPlugins <support@diviplugins.com>
 */
class Dp_Divi_FilterGrid {

	/**
	 * The loader that's responsible for maintaining and registering all hooks that power
	 * the plugin.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      Dp_Cpt_Filterable_Module_Loader $loader Maintains and registers all hooks for the plugin.
	 */
	protected $loader;

	/**
	 * The unique identifier of this plugin.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      string $plugin_name The string used to uniquely identify this plugin.
	 */
	protected $plugin_name;

	/**
	 * The current version of the plugin.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      string $version The current version of the plugin.
	 */
	protected $version;

	/**
	 * Define the core functionality of the plugin.
	 *
	 * Set the plugin name and the plugin version that can be used throughout the plugin.
	 * Load the dependencies, define the locale, and set the hooks for the admin area and
	 * the public-facing side of the site.
	 *
	 * @since    1.0.0
	 */
	public function __construct() {
		if ( defined( 'DPDFG_VERSION' ) ) {
			$this->version = DPDFG_VERSION;
		} else {
			$this->version = '1.0.0';
		}
		$this->plugin_name = 'dp-divi-filtergrid';
		$this->load_dependencies();
		$this->set_locale();
		$this->define_admin_hooks();
		$this->define_public_hooks();
	}

	/**
	 * Load the required dependencies for this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function load_dependencies() {
		require_once DPDFG_DIR . 'includes/class-dp-dfg-loader.php';
		require_once DPDFG_DIR . 'includes/class-dp-page.php';
		require_once DPDFG_DIR . 'includes/class-dp-dfg-updater.php';
		require_once DPDFG_DIR . 'includes/class-dp-dfg-license.php';
		require_once DPDFG_DIR . 'includes/class-dp-dfg-i18n.php';
		require_once DPDFG_DIR . 'includes/class-dp-dfg-utils.php';
		require_once DPDFG_DIR . 'admin/class-dp-dfg-admin.php';
		require_once DPDFG_DIR . 'public/class-dp-dfg-public.php';
		$this->loader = new Dp_Dfg_Loader();
	}

	/**
	 * Define the locale for this plugin for internationalization.
	 *
	 * Uses the Dp_Cpt_Filterable_Module_i18n class in order to set the domain and to register the hook
	 * with WordPress.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function set_locale() {
		$plugin_i18n = new Dp_Dfg_i18n();
		$this->loader->add_action( 'plugins_loaded', $plugin_i18n, 'load_plugin_textdomain' );
	}

	/**
	 * Register all of the hooks related to the admin area functionality
	 * of the plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function define_admin_hooks() {
		$dp_page = new DiviPlugins_Menu_Page();
		$this->loader->add_action( 'admin_menu', $dp_page, 'add_dp_page' );
		$plugin_admin = new Dp_Dfg_Admin( $this->get_plugin_name(), $this->get_version() );
		$this->loader->add_action( 'admin_enqueue_scripts', $plugin_admin, 'enqueue_scripts_styles' );
		$this->loader->add_filter( 'plugin_row_meta', $plugin_admin, 'add_plugin_row_meta', 10, 2 );
		$this->loader->add_action( 'divi_extensions_init', $plugin_admin, 'initialize_extension' );
		$license = new Dp_Dfg_License();
		$this->loader->add_action( 'init', $license, 'init_plugin_updater', 0 );
		$this->loader->add_action( 'diviplugins_page_add_license', $license, 'license_html' );
		$this->loader->add_action( 'admin_init', $license, 'register_license_option' );
		$this->loader->add_action( 'admin_init', $license, 'activate_license' );
		$this->loader->add_action( 'admin_init', $license, 'deactivate_license' );
		$this->loader->add_action( 'admin_notices', $license, 'notice_license_activation_result' );
		if ( get_option( 'dpdfg_license_status' ) !== 'valid' ) {
			$this->loader->add_action( 'admin_notices', $license, 'notice_activation_license_require' );
		}
		$plugin_util = new Dp_Dfg_Utils();
		$this->loader->add_action( 'wp_ajax_dpdfg_get_posts_data_action', $plugin_util, 'ajax_get_posts_data' );
		$this->loader->add_action( 'wp_ajax_nopriv_dpdfg_get_posts_data_action', $plugin_util, 'ajax_get_posts_data' );
		$this->loader->add_action( 'wp_ajax_dpdfg_get_cpt_action', $plugin_util, 'ajax_get_cpt' );
		$this->loader->add_action( 'wp_ajax_dpdfg_get_taxonomies_action', $plugin_util, 'ajax_get_taxonomies' );
		$this->loader->add_action( 'wp_ajax_dpdfg_get_taxonomies_terms_action', $plugin_util, 'ajax_get_taxonomies_terms' );
		$this->loader->add_action( 'wp_ajax_dpdfg_get_custom_fields_action', $plugin_util, 'ajax_get_custom_fields' );
		$this->loader->add_action( 'wp_ajax_dpdfg_get_multilevel_tax_action', $plugin_util, 'ajax_get_multilevel_tax' );
		$this->loader->add_action( 'wp_ajax_dpdfg_get_layouts_action', $plugin_util, 'ajax_get_library_items' );
		$this->loader->add_filter( 'et_builder_load_actions', $plugin_util, 'add_our_custom_action' );
	}

	/**
	 * The name of the plugin used to uniquely identify it within the context of
	 * WordPress and to define internationalization functionality.
	 *
	 * @return    string    The name of the plugin.
	 * @since     1.0.0
	 */
	public function get_plugin_name() {
		return $this->plugin_name;
	}

	/**
	 * Retrieve the version number of the plugin.
	 *
	 * @return    string    The version number of the plugin.
	 * @since     1.0.0
	 */
	public function get_version() {
		return $this->version;
	}

	/**
	 * Register all of the hooks related to the public-facing functionality
	 * of the plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function define_public_hooks() {
		$plugin_public = new Dp_Dfg_Public( $this->get_plugin_name(), $this->get_version() );
		$this->loader->add_filter( 'template_include', $plugin_public, 'popup_page_template', 99 );
		$this->loader->add_action( 'wp_enqueue_scripts', $plugin_public, 'enqueue_scripts', 20 );
		$plugin_util = new Dp_Dfg_Utils();
		$this->loader->add_action( 'wp_ajax_dpdfg_add_to_cart_action', $plugin_util, 'ajax_add_to_cart' );
		$this->loader->add_action( 'wp_ajax_nopriv_dpdfg_add_to_cart_action', $plugin_util, 'ajax_add_to_cart' );
	}

	/**
	 * Run the loader to execute all of the hooks with WordPress.
	 *
	 * @since    1.0.0
	 */
	public function run() {
		$this->loader->run();
	}

	/**
	 * The reference to the class that orchestrates the hooks with the plugin.
	 *
	 * @return    Dp_Cpt_Filterable_Module_Loader    Orchestrates the hooks of the plugin.
	 * @since     1.0.0
	 */
	public function get_loader() {
		return $this->loader;
	}

}
