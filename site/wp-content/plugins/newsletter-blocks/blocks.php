<?php

/*
  Plugin Name: Newsletter - Extended Composer Blocks
  Plugin URI: https://www.thenewsletterplugin.com
  Description: New extended blocks for the composer
  Version: 1.5.2
  Author: The Newsletter Team
  Requires at least: 5.1
  Requires PHP: 7.0
  Author URI: https://www.thenewsletterplugin.com
  Update URI: false
  Disclaimer: Use at your own risk. No warranty expressed or implied is provided.
 */

add_action('newsletter_loaded', function ($version) {
    if (version_compare($version, '8.2.1') < 0) {
        add_action('admin_notices', function () {
            echo '<div class="notice notice-error"><p>Newsletter plugin upgrade required for Extended Blocks.</p></div>';
        });
    } else {
        include_once __DIR__ . '/plugin.php';
        new NewsletterBlocks('1.5.2');
    }
});
