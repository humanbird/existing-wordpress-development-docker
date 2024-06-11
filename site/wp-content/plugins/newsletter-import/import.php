<?php

/*
  Plugin Name: Newsletter - Import
  Plugin URI: https://www.thenewsletterplugin.com/documentation/addons/extended-features/advanced-import/
  Description: Advanced import from CSV with field mapping (please read the documentation)
  Text Domain: newsletter-import
  Domain Path: /languages
  Version: 1.4.6
  Requires at least: 5.1
  Requires PHP: 7.0
  Update URI: false
  Author: The Newsletter Team
  Author URI: https://www.thenewsletterplugin.com
  Disclaimer: Use at your own risk. No warranty expressed or implied is provided.
 */

add_action('newsletter_loaded', function ($version) {
    if (version_compare($version, '8.2.2') < 0) {
        add_action('admin_notices', function () {
            echo '<div class="notice notice-error"><p>Newsletter plugin upgrade required for <strong>Newsletter - Import Addon</strong>.</p></div>';
        });
    } elseif (!function_exists('mb_check_encoding')) {
        add_action('admin_notices', function () {
            echo '<div class="notice notice-error"><p>Newsletter Advanced Import requires the PHP mbstring extension: check with your provider.</p></div>';
        });
    } else {
        include_once __DIR__ . '/plugin.php';
        new NewsletterImport('1.4.6');
    }
});