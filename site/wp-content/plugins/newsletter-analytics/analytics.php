<?php

/*
  Plugin Name: Newsletter - Google Analytics
  Plugin URI: https://www.thenewsletterplugin.com/documentation/addons/extended-features/analytics-extension/
  Description: Adds Google Analytics tracking to the newsletter links
  Version: 1.2.3
  Requires at least: 5.1
  Requires PHP: 7.0
  Author: The Newsletter Team
  Author URI: https://www.thenewsletterplugin.com
  Update URI: false
  Disclaimer: Use at your own risk. No warranty expressed or implied is provided.
 */

add_action('newsletter_loaded', function ($version) {
    if ($version < '8.2.1') {
        add_action('admin_notices', function () {
            echo '<div class="notice notice-error"><p>Newsletter plugin upgrade required for Google Analytics Addon.</p></div>';
        });
    } else {
        include_once __DIR__ . '/plugin.php';
        new NewsletterAnalytics('1.2.3');
    }
});

