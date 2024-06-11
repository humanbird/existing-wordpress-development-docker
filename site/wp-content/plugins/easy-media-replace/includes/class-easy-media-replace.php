<?php

/**
 * The file that defines the core plugin class
 *
 * A class definition that includes attributes and functions used across both the
 * public-facing side of the site and the admin area.
 *
 * @link       http://nabillemsieh.com
 * @since      0.1.0
 *
 * @package    Easy_Media_Replace
 * @subpackage Easy_Media_Replace/includes
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
 * @since      0.1.0
 * @package    Easy_Media_Replace
 * @subpackage Easy_Media_Replace/includes
 * @author     Nabil Lemsieh <contact@nabillemsieh.com>
 */
class Easy_Media_Replace
{

    /**
     * The loader that's responsible for maintaining and registering all hooks that power
     * the plugin.
     *
     * @since    0.1.0
     * @access   protected
     * @var      Easy_Media_Replace_Loader    $loader    Maintains and registers all hooks for the plugin.
     */
    protected $loader;

    /**
     * The unique identifier of this plugin.
     *
     * @since    0.1.0
     * @access   protected
     * @var      string    $plugin_name    The string used to uniquely identify this plugin.
     */
    protected $plugin_name;

    /**
     * The current version of the plugin.
     *
     * @since    0.1.0
     * @access   protected
     * @var      string    $version    The current version of the plugin.
     */
    protected $version;

    /**
     * Define the core functionality of the plugin.
     *
     * Set the plugin name and the plugin version that can be used throughout the plugin.
     * Load the dependencies, define the locale, and set the hooks for the admin area and
     * the public-facing side of the site.
     *
     * @since    0.1.0
     */
    public function __construct()
    {
        if (defined('PLUGIN_NAME_VERSION')) {
            $this->version = PLUGIN_NAME_VERSION;
        } else {
            $this->version = '0.1.0';
        }
        $this->plugin_name = 'easy-media-replace';

        $this->load_dependencies();
        $this->set_locale();
        $this->define_admin_hooks();
        $this->define_public_hooks();

    }

    /**
     * Load the required dependencies for this plugin.
     *
     * Include the following files that make up the plugin:
     *
     * - Easy_Media_Replace_Loader. Orchestrates the hooks of the plugin.
     * - Easy_Media_Replace_i18n. Defines internationalization functionality.
     * - Easy_Media_Replace_Admin. Defines all hooks for the admin area.
     * - Easy_Media_Replace_Public. Defines all hooks for the public side of the site.
     *
     * Create an instance of the loader which will be used to register the hooks
     * with WordPress.
     *
     * @since    0.1.0
     * @access   private
     */
    private function load_dependencies()
    {

        require_once plugin_dir_path(dirname(__FILE__)) . 'includes/class-easy-media-replace-helper.php';
        /**
         * The class responsible for orchestrating the actions and filters of the
         * core plugin.
         */
        require_once plugin_dir_path(dirname(__FILE__)) . 'includes/class-easy-media-replace-loader.php';

        /**
         * The class responsible for defining internationalization functionality
         * of the plugin.
         */
        require_once plugin_dir_path(dirname(__FILE__)) . 'includes/class-easy-media-replace-i18n.php';

        /**
         * The class responsible for defining all actions that occur in the admin area.
         */
        require_once plugin_dir_path(dirname(__FILE__)) . 'admin/class-easy-media-replace-admin.php';

        /**
         * The class responsible for defining all actions that occur in the public-facing
         * side of the site.
         */
        require_once plugin_dir_path(dirname(__FILE__)) . 'public/class-easy-media-replace-public.php';

        $this->loader = new Easy_Media_Replace_Loader();

    }

    /**
     * Define the locale for this plugin for internationalization.
     *
     * Uses the Easy_Media_Replace_i18n class in order to set the domain and to register the hook
     * with WordPress.
     *
     * @since    0.1.0
     * @access   private
     */
    private function set_locale()
    {

        $plugin_i18n = new Easy_Media_Replace_i18n();

        $this->loader->add_action('plugins_loaded', $plugin_i18n, 'load_plugin_textdomain');

    }

    /**
     * Register all of the hooks related to the admin area functionality
     * of the plugin.
     *
     * @since    0.1.0
     * @access   private
     */
    private function define_admin_hooks()
    {

        $plugin_admin = new Easy_Media_Replace_Admin($this->get_plugin_name(), $this->get_version());

        $this->loader->add_action('admin_enqueue_scripts', $plugin_admin, 'enqueue_styles');
        $this->loader->add_action('admin_enqueue_scripts', $plugin_admin, 'enqueue_scripts');

        $this->loader->add_filter('attachment_fields_to_edit', $plugin_admin, 'add_replace_link_to_media_dialog', 10, 2);
        $this->loader->add_filter('attachment_submitbox_misc_actions', $plugin_admin, 'add_replace_link_to_attachment_misc_actions', 10, 2);
        $this->loader->add_filter('media_row_actions', $plugin_admin, 'add_replace_link_to_media_row', 10, 2);
        $this->loader->add_action('wp_ajax_emr:dialog', $plugin_admin, 'dialog');
        $this->loader->add_action('wp_ajax_emr:upload', $plugin_admin, 'upload');
        $this->loader->add_action('wp_ajax_emr:replace', $plugin_admin, 'replace');
        $this->loader->add_action('wp_ajax_emr:remove', $plugin_admin, 'remove');

    }

    /**
     * Register all of the hooks related to the public-facing functionality
     * of the plugin.
     *
     * @since    0.1.0
     * @access   private
     */
    private function define_public_hooks()
    {

        $plugin_public = new Easy_Media_Replace_Public($this->get_plugin_name(), $this->get_version());

        //$this->loader->add_action( 'wp_enqueue_scripts', $plugin_public, 'enqueue_styles' );
        //$this->loader->add_action( 'wp_enqueue_scripts', $plugin_public, 'enqueue_scripts' );

    }

    /**
     * Run the loader to execute all of the hooks with WordPress.
     *
     * @since    0.1.0
     */
    public function run()
    {
        $this->loader->run();
    }

    /**
     * The name of the plugin used to uniquely identify it within the context of
     * WordPress and to define internationalization functionality.
     *
     * @since     0.1.0
     * @return    string    The name of the plugin.
     */
    public function get_plugin_name()
    {
        return $this->plugin_name;
    }

    /**
     * The reference to the class that orchestrates the hooks with the plugin.
     *
     * @since     0.1.0
     * @return    Easy_Media_Replace_Loader    Orchestrates the hooks of the plugin.
     */
    public function get_loader()
    {
        return $this->loader;
    }

    /**
     * Retrieve the version number of the plugin.
     *
     * @since     0.1.0
     * @return    string    The version number of the plugin.
     */
    public function get_version()
    {
        return $this->version;
    }

}
