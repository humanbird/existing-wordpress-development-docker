<?php

/*
  Plugin Name: Newsletter - Addons Manager
  Plugin URI: https://www.thenewsletterplugin.com/documentation/extensions-extension
  Description: Manages all premium and free Newsletter addons directly from your blog
  Version: 1.2.1
  Requires at least: 5.1
  Requires PHP: 7.4
  Author: The Newsletter Team
  Author URI: https://www.thenewsletterplugin.com
  Disclaimer: Use at your own risk. No warranty expressed or implied is provided.
 */

add_action('newsletter_loaded', function ($version) {
    if ($version < '8.1.5') {
        add_action('admin_notices', function () {
            echo '<div class="notice notice-error"><p>Newsletter plugin upgrade required for Addons Manager.</p></div>';
        });
    } else {
        include __DIR__ . '/plugin.php';
        new NewsletterExtensions('1.2.1');
    }
});
