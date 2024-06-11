<?php

/**
 * Fired during plugin activation
 *
 * @link       http://nabillemsieh.com
 * @since      0.1.0
 *
 * @package    Easy_Media_Replace
 * @subpackage Easy_Media_Replace/includes
 */

/**
 * Fired during plugin activation.
 *
 * This class defines all code necessary to run during the plugin's activation.
 *
 * @since      0.1.0
 * @package    Easy_Media_Replace
 * @subpackage Easy_Media_Replace/includes
 * @author     Nabil Lemsieh <contact@nabillemsieh.com>
 */
class Easy_Media_Replace_Activator
{

    /**
     * Short Description. (use period)
     *
     * Long Description.
     *
     * @since    0.1.0
     */
    public static function activate()
    {

        update_option('emr_plugin_version', EMR_VERSION);

    }

}
