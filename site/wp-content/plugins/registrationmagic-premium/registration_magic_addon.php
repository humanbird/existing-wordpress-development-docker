<?php
/**
 * The plugin bootstrap file
 *
 * This file is read by WordPress to generate the plugin information in the plugin
 * admin area. This file also includes all of the dependencies used by the plugin,
 * registers the activation and deactivation functions, and defines a function
 * that starts the plugin.
 *
 * @link              http://www.registrationmagic.com
 * @since             3.0.0
 * @package           registration_magic
 *
 * @wordpress-plugin
 * Plugin Name:       RegistrationMagic Premium
 * Plugin URI:        http://www.registrationmagic.com
 * Description:       Unlocks full potential of RegistrationMagic! Adds all extensions and Premium features. Requires RegistrationMagic Standard to work properly.
 * Version:           5.3.3.0
 * Tags:              registration, form, custom, analytics, simple, submissions
 * Requires at least: 3.3.0
 * Requires PHP:      5.6
 * Author:            Metagauss
 * Author URI:        https://profiles.wordpress.org/metagauss/
 * Text Domain:       registrationmagic-addon
 * Domain Path:       /languages
 */
// If this file is called directly, abort.
if(!defined('WPINC')) {
    die;
}

function deactivate_rm_addons_below_5330() {
    if(!function_exists('get_plugins')) {
        require_once ABSPATH.'wp-admin/includes/plugin.php';
    }
    $plugins = get_plugins();
    $installed_premiums = array();
    foreach($plugins as $plugin_dir => $plugin_data) {
        if(strpos($plugin_dir, 'registration_magic_addon.php')) {
            array_push($installed_premiums, array('dir'=>$plugin_dir,'version'=>$plugin_data['Version']));
        }
    }
    if(!empty($installed_premiums)) {
        foreach($installed_premiums as $installed_premium) {
            if(is_plugin_active($installed_premium['dir']) && version_compare($installed_premium['version'],'5.3.3.0','<')) {
                if(!function_exists('deactivate_plugins'))
                    require_once( ABSPATH . '/wp-admin/includes/plugin.php' );
                deactivate_plugins(plugin_basename($installed_premium['dir']));
            }
        }
    }
}

//deactivate_rm_addons_below_5330();

if(!function_exists('get_installed_registration_magics')) {
    function get_installed_registration_magics() {
        if(!function_exists('get_plugins')) {
            require_once ABSPATH.'wp-admin/includes/plugin.php';
        }
        $plugins = get_plugins();
        $installed_basics = array();
        foreach($plugins as $plugin_dir => $plugin_data) {
            if(strpos($plugin_dir, 'registration_magic.php')) {
                $installed_basics[$plugin_dir] = $plugin_data;
            }
        }
        return $installed_basics;
    }

    function rm_addon_plugin_notice() { ?>
    <div class="notice notice-error is-dismissible">
        <p>
            <?php echo sprintf(__("<strong>Dependency Error, Installation Aborted:</strong> The RegistrationMagic Premium Plugin you are trying to activate, <em>requires</em> RegistrationMagic Standard v5.0 Plugin or above. You can download it <a href='%s'>here</a>. For help, <a href='%s' target='_blank'>contact our support</a>.","registrationmagic-addon"),get_admin_url(null,"plugin-install.php?tab=plugin-information&plugin=custom-registration-form-builder-with-submission-manager&TB_iframe=true&width=772&height=732"),"https://metagauss.com/help-and-support/"); ?>
        </p>
    </div>
    <?php
        if (!function_exists('deactivate_plugins')) {
            require_once( ABSPATH . '/wp-admin/includes/plugin.php' );
        }
        deactivate_plugins( plugin_basename(__FILE__ ) );
    }

    function rm_addon_plugin_version_notice() { ?>
    <div class="notice notice-error is-dismissible">
        <p>
            <?php echo sprintf(__("<strong>Version Mismatch Error, Installation Aborted:</strong> The RegistrationMagic Premium Plugin you are trying to activate, <em>requires</em> RegistrationMagic Standard v5.0 Plugin or above. You are <em>currently</em> using an older version, which is v%s. You should first update RegistrationMagic to latest version and then activate Premium. For help, <a href='%s' target='_blank'>contact our support</a>.","registrationmagic-addon"),RM_PLUGIN_VERSION,"https://metagauss.com/help-and-support/"); ?>
        </p>
    </div>
    <?php
        if (!function_exists('deactivate_plugins')) {
            require_once( ABSPATH . '/wp-admin/includes/plugin.php' );
        }
        deactivate_plugins( plugin_basename(__FILE__ ) );
    }

    function rm_addon_standard_activation_notice() { ?>
    <div class="notice notice-info is-dismissible">
        <p>
            <?php echo __("The RegistrationMagic Premium plugin has activated the core RegistrationMagic plugin as it is required to run RegistrationMagic Premium features.","registrationmagic-addon"); ?>
        </p>
    </div>
    <?php }

    function check_rm_addon_requirements() {
        if(is_admin()) {
            $installed_basics = get_installed_registration_magics();
            if(!empty($installed_basics)) {
                foreach($installed_basics as $plugin_dir => $plugin_data) {
                    if(!is_plugin_active($plugin_dir) && version_compare($plugin_data['Version'],'5.0.0.0','>=')) {
                        if(!function_exists('activate_plugin')) {
                            require_once( ABSPATH . '/wp-admin/includes/plugin.php' );
                        }
                        activate_plugin($plugin_dir);
                        add_action('admin_notices', 'rm_addon_standard_activation_notice');
                        return;
                    } elseif(!is_plugin_active($plugin_dir) && version_compare($plugin_data['Version'],'5.0.0.0','<')) {
                        continue;
                    } elseif(is_plugin_active($plugin_dir) && version_compare($plugin_data['Version'],'5.0.0.0','>=')) {
                        return;
                    } elseif(is_plugin_active($plugin_dir) && version_compare($plugin_data['Version'],'5.0.0.0','<')) {
                        add_action('admin_notices', 'rm_addon_plugin_version_notice');
                        return;
                    } else {
                        add_action('admin_notices', 'rm_addon_plugin_notice');
                        return;
                    }
                }
            }
            add_action('admin_notices', 'rm_addon_plugin_notice');
            return;
        }
    }

    check_rm_addon_requirements();
}

if (!defined('REGMAGIC_ADDON')) {
    define('REGMAGIC_ADDON','99');
}

$rmsilver = 'custom-registration-form-builder-with-submission-manager-silver/registration_magic.php';
$rmbasic = 'custom-registration-form-builder-with-submission-manager/registration_magic.php';
$rmgoldi2 = 'registrationmagic-gold-i2/registration_magic.php';

if(defined('REGMAGIC_GOLD_i2')){
    if(!function_exists('deactivate_plugins')){
        require_once( ABSPATH . '/wp-admin/includes/plugin.php' );
    }
    deactivate_plugins($rmgoldi2);
}

if(defined('REGMAGIC_SILVER')){
    if(!function_exists('deactivate_plugins')){
        require_once( ABSPATH . '/wp-admin/includes/plugin.php' );
    }
    deactivate_plugins($rmsilver);    
}

if(defined('REGMAGIC_GOLD')){
    if(!function_exists('deactivate_plugins')){
        require_once( ABSPATH . '/wp-admin/includes/plugin.php' );
    }
    deactivate_plugins(RM_BASE_DIR . 'registration_magic.php');
} else {
    define('REGMAGIC_GOLD','99');
}

if (!defined('RM_ADDON_PLUGIN_VERSION')) {
    define('RM_ADDON_PLUGIN_BASENAME', plugin_basename(__FILE__ ));
    define('RM_ADDON_PLUGIN_VERSION', '5.3.3.0');
    
    /*
    //User level on front end
    define('RM_FRONT_OTP_USER', 0x2);
    define('RM_FRONT_NO_USER', 0x1);
    define('RM_FRONT_WP_USER', 0x4);
    */

    define('RM_ADDON_BASE_DIR', plugin_dir_path(__FILE__));
    define('RM_ADDON_BASE_URL', plugin_dir_url(__FILE__));
    define('RM_ADDON_ADMIN_DIR', RM_ADDON_BASE_DIR . "admin/");
    define('RM_ADDON_PUBLIC_DIR', RM_ADDON_BASE_DIR . "public/");
    define('RM_ADDON_IMG_DIR', RM_ADDON_BASE_DIR . "images/");
    define('RM_ADDON_IMG_URL', plugin_dir_url(__FILE__) . 'images/');
    define('RM_ADDON_INCLUDES_DIR', RM_ADDON_BASE_DIR . 'includes/');
    define('RM_ADDON_EXTERNAL_DIR', RM_ADDON_BASE_DIR . 'external/');
    
    //read status
    define('RM_SUB_STATUS_READ', 1);
    define('RM_SUB_STATUS_UNREAD', 0);

    /**
     * registers the plugin autoload
     */
    function registration_magic_addon_register_autoload() {
        require_once plugin_dir_path(__FILE__) . 'includes/class_rm_autoloader_addon.php';

        $autoloader = new RM_Autoloader_Addon();
        $autoloader->register();
    }

    /**
     * includes or initializes all the external libraries used in the plugin
     * 
     * @since 3.0.0
     */
    function registration_magic_addon_include_external_libs() {
        //require_once RM_EXTERNAL_DIR . 'mailchimp/class_rm_mailchimp.php';
        //require_once RM_ADDON_EXTERNAL_DIR . 'AWeber/class_rm_aweber.php';
        //require_once RM_EXTERNAL_DIR . 'cron/cron_helper.php';
        //require_once RM_ADDON_EXTERNAL_DIR . 'plugin-updates/plugin-update-checker.php';
    }
    
    function registration_magic_addon_activate($network_wide) {
        RM_Table_Tech_Addon::create_tables($network_wide);
        update_site_option('rm_premium_addon_active', 1);
        update_site_option('rm_redirect_after_activation', 1);
        if(class_exists('RM_Reports_Service') && method_exists('RM_Reports_Service', 'insert_report_cron_on_activate_plugin')){
            $report_service = new RM_Reports_Service;
            $report_service->insert_report_cron_on_activate_plugin();
        }
    }
    
    function registration_magic_addon_deactivate() {
        update_site_option('rm_premium_addon_active', 0);
        if(class_exists('RM_Reports_Service') && method_exists('RM_Reports_Service', 'delete_report_cron_on_deactivate_plugin')){
            $report_service = new RM_Reports_Service;
            $report_service->delete_report_cron_on_deactivate_plugin();
        }
    }
    
    registration_magic_addon_register_autoload();
    registration_magic_addon_include_external_libs();
    require_once(RM_ADDON_INCLUDES_DIR.'class_registration_magic_addon.php');
    
    register_activation_hook(__FILE__, 'registration_magic_addon_activate');
    register_deactivation_hook(__FILE__, 'registration_magic_addon_deactivate');

}

//Kick extender
if(class_exists('RM_Options')) {
    RM_Extender_Addon::init();
} else {
    $installed_rm_basics = get_installed_registration_magics();
    if(!empty($installed_rm_basics)) {
        foreach($installed_rm_basics as $plugin_dir => $plugin_data) {
            if(is_plugin_active($plugin_dir) && version_compare($plugin_data['Version'],'5.0.0.0','>=')) {
                $rm_basic_path = explode('/',(string)$plugin_dir);
                require_once(WP_PLUGIN_DIR.'/'.$rm_basic_path[0].'/includes/class_rm_ui_strings.php');
                require_once(WP_PLUGIN_DIR.'/'.$rm_basic_path[0].'/admin/models/class_rm_options.php');
                RM_Extender_Addon::init();
            }
        }
    }
}


//Set up update check
if(!class_exists('RM_Licensing_Addon'))
{
    require_once(RM_ADDON_INCLUDES_DIR.'class_rm_licensing_addon.php');
}

$key = 'rm_premium';

$license_status = get_option($key.'_license_status','');
$license_status_premium = get_option('rm_premium_plus_license_status','');
if( ! empty( $license_status ) && $license_status == 'valid' ){
    add_action( 'init','rm_plugin_updater' );
}elseif(! empty( $license_status_premium ) && $license_status_premium == 'valid'){
    add_action( 'init','rm_plugin_updater_premium' );
}
    

function rm_plugin_updater()
{
    $key = 'rm_premium';
    $doing_cron = defined( 'DOING_CRON' ) && DOING_CRON;
    if ( ! current_user_can( 'manage_options' ) && ! $doing_cron ) {
        return;
    }

    // retrieve our license key from the global settings
    $license_key = get_option($key.'_license_key');
    $item_id = get_option($key.'_item_id');
    $site_url = 'https://registrationmagic.com/';
    // setup the updater
    $plugin_updater = new RM_Licensing_Addon(
        $site_url,
        __FILE__,
        array(
            'version' => RM_ADDON_PLUGIN_VERSION,  // current version number
            'license' => $license_key,  // license key
            'item_id' => $item_id,       // ID of the product
            'author'  => 'registrationmagic', // author of this plugin
            'beta'    => false,
        ),
        $key
    );
}

function rm_plugin_updater_premium()
{
    $key = 'rm_premium_plus';
    $doing_cron = defined( 'DOING_CRON' ) && DOING_CRON;
    if ( ! current_user_can( 'manage_options' ) && ! $doing_cron ) {
        return;
    }

    // retrieve our license key from the global settings
    $license_key = get_option($key.'_license_key');
    $item_id = get_option($key.'_item_id');
    $site_url = 'https://registrationmagic.com/';
    // setup the updater
    $plugin_updater = new RM_Licensing_Addon(
        $site_url,
        __FILE__,
        array(
            'version' => RM_ADDON_PLUGIN_VERSION,  // current version number
            'license' => $license_key,  // license key
            'item_id' => $item_id,       // ID of the product
            'author'  => 'registrationmagic', // author of this plugin
            'beta'    => false,
        ),
        $key
    );
}

//get license status
function get_license_status()
{
    $status = 'inactive';
    $license_key = get_option('rm_premium_license_key');
    $license_status = get_option('rm_premium_license_status','');
    if(!empty($license_key) && !empty($license_status) && $license_status=='valid')
    {
        $status = 'active';
    }
    return $status;
}
function get_test_rm_licenses() 
{
        $result = array(
                'label'       => __( 'Your RegistrationMagic extensions are receiving updates.', 'registrationmagic-addon' ),
                'status'      => 'good',
                'badge'       => get_default_badge(),
                'description' => sprintf(
                        '<p>%s</p>',
                        __( 'Your RegistrationMagic extensions license is valid and receiving updates.', 'registrationmagic-addon' )
                ),
                'actions'     => '',
                'test'        => 'get_test_rm_licenses',
        );
        if ( get_license_status()=='active') {
                return $result;
        }

        $result['label']          = __( 'You are not receiving updates for some extensions.', 'registrationmagic-addon' );
        $result['status']         = 'critical';
        $result['badge']['color'] = 'red';
        $result['description']    = sprintf(
                '<p>%s</p>',
                __( 'At least one of your extensions is missing a license key, or the license is expired. Your site may be missing critical software updates.', 'registrationmagic-addon' )
        );
        $result['actions']        = get_licensing_action_links();

        return $result;
}

function get_default_badge() 
{
        return array(
                'label' => __( 'RegistrationMagic', 'registrationmagic-addon' ),
                'color' => 'blue',
        );
}

function get_licensing_action_links() 
{
        $action_links = sprintf(
                        '<a href="%s">%s</a>',
                        esc_url( admin_url('admin.php?page=rm_licensing') ),
                        esc_html('Enter the license key')
                );

        return ! empty( $action_links ) ? $action_links: '';
}

function rm_connect_site_health_test( $tests ) {
        $tests['direct']['rm_test_license'] = array(
			'label'     => __( 'Licensed Extensions', 'registrationmagic-addon' ),
			'test'      => 'get_test_rm_licenses',
			'skip_cron' => true,
		);
        return $tests;
}
add_filter( 'site_status_tests', 'rm_connect_site_health_test',999999999 );



