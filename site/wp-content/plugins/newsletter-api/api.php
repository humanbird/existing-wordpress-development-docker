<?php

/*
  Plugin Name: Newsletter - API v1 and v2
  Plugin URI: https://www.thenewsletterplugin.com/documentation/developers/newsletter-api-2/
  Description: Access programmatically to the Newsletter Plugin via REST API
  Version: 2.4.1
  Requires PHP: 7.2
  Requires at least: 5.6
  Author: The Newsletter Team
  Author URI: https://www.thenewsletterplugin.com
  Update URI: false
  Disclaimer: Use at your own risk. No warranty expressed or implied is provided.
 */

add_action('newsletter_loaded', function ($version) {
	if (version_compare($version, '8.2.1', '<')) {
        add_action('admin_notices', function () {
            echo '<div class="notice notice-error"><p>Newsletter plugin upgrade required by <strong>Newsletter - API Addon</strong>.</p></div>';
        });
    } else {
        require_once __DIR__ . '/plugin.php';
        new NewsletterApi('2.4.1');
    }
});
