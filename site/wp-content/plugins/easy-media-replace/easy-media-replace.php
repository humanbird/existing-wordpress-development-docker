<?php

/**
 * The plugin bootstrap file
 *
 * This file is read by WordPress to generate the plugin information in the plugin
 * admin area. This file also includes all of the dependencies used by the plugin,
 * registers the activation and deactivation functions, and defines a function
 * that starts the plugin.
 *
 * @link              http://nabillemsieh.com
 * @since             0.1.0
 * @package           Easy_Media_Replace
 *
 * @wordpress-plugin
 * Plugin Name:       Easy Media Replace
 * Plugin URI:        https://wordpress.org/plugins/easy-media-replace/
 * Description:       Replace Images and Media Files in WordPress Easily.
 * Version:           0.2.0
 * Author:            Nabil Lemsieh
 * Author URI:        http://nabillemsieh.com
 * License:           GPLv3
 * License URI:       http://www.gnu.org/licenses/gpl.html
 * Text Domain:       easy-media-replace
 * Domain Path:       /languages
 */

// If this file is called directly, abort.
if (!defined('WPINC')) {
    die;
}

define('EMR_VERSION', '0.2.0');
define('EMR_TEXT_DOMAIN', 'easy-media-replace');
define('EMR_DIR', plugin_dir_path(__FILE__));
define('EMR_DIR_URL', plugin_dir_url(__FILE__));
/**
 * The code that runs during plugin activation.
 * This action is documented in includes/class-easy-media-replace-activator.php
 */
function activate_easy_media_replace()
{
    require_once plugin_dir_path(__FILE__) . 'includes/class-easy-media-replace-activator.php';
    Easy_Media_Replace_Activator::activate();
}

/**
 * The code that runs during plugin deactivation.
 * This action is documented in includes/class-easy-media-replace-deactivator.php
 */
function deactivate_easy_media_replace()
{
    require_once plugin_dir_path(__FILE__) . 'includes/class-easy-media-replace-deactivator.php';
    Easy_Media_Replace_Deactivator::deactivate();
}

register_activation_hook(__FILE__, 'activate_easy_media_replace');
register_deactivation_hook(__FILE__, 'deactivate_easy_media_replace');

/**
 * The core plugin class that is used to define internationalization,
 * admin-specific hooks, and public-facing site hooks.
 */
require plugin_dir_path(__FILE__) . 'includes/class-easy-media-replace.php';

/**
 * Begins execution of the plugin.
 *
 * Since everything within the plugin is registered via hooks,
 * then kicking off the plugin from this point in the file does
 * not affect the page life cycle.
 *
 * @since    0.1.0
 */
function run_easy_media_replace()
{

    $plugin = new Easy_Media_Replace();
    $plugin->run();
}
run_easy_media_replace();
