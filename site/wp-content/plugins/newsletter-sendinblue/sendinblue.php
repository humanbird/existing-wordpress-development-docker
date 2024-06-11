<?php

/*
  Plugin Name: Newsletter - Brevo
  Plugin URI: https://www.thenewsletterplugin.com/documentation/addons/delivery-addons/sendinblue-extension/
  Description: Integrates Newsletter with Brevo (formerly Sendinblue) SMTP/API and bounce detection services. Automatic updates available setting the license key on Newsletter configuration panel.
  Version: 1.2.0
  Requires PHP: 7.4
  Requires at least: 5.0
  Author: The Newsletter Team
  Author URI: https://www.thenewsletterplugin.com
  Disclaimer: Use at your own risk. No warranty expressed or implied is provided.
 */

add_action('newsletter_loaded', function ($version) {
    if (version_compare($version, '8.1.2') < 0) {
        add_action('admin_notices', function () {
            echo '<div class="notice notice-error"><p>Newsletter plugin upgrade required by <strong>Newsletter - Brevo Addon</strong>.</p></div>';
        });
    } else {
        require_once __DIR__ . '/plugin.php';
        new NewsletterSendinblue('1.2.0');
    }
});

