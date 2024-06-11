<?php

if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}

// Add Admin page - Only show to WordPress admin role
add_action('admin_menu', 'admin_settings_peps_seo');
function admin_settings_peps_seo() {
    // Admin page settings
    add_menu_page('PEPS Media SEO', 'PEPS Media SEO', 'manage_options', 'peps-media-seo-admin', 'admin_settings_page');
}

add_action('admin_init', 'peps_seo_admin_settings');
function peps_seo_admin_settings() {
    register_setting('pm-seo-settings', 'pm-seo-header-code');
    register_setting('pm-seo-settings', 'pm-seo-body-open-code');
    register_setting('pm-seo-settings', 'pm-seo-footer-code');

    // General settings
    register_setting('pm-seo-settings', 'pm-seo-og-site-name');
    register_setting('pm-seo-settings', 'pm-seo-disable-attachment-pages', 'handle_attachment_pages_setting');
    register_setting('pm-seo-settings', 'pm-seo-disable-author-pages');
    register_setting('pm-seo-settings', 'pm-seo-delete-data-on-uninstall');


    // Settings for sitemap
    register_setting('pm-seo-settings', 'pm-seo-disable-sitemap');
    register_setting('pm-seo-settings', 'pm-seo-disable-author-sitemap');
    register_setting('pm-seo-settings', 'pm-seo-disable-user-sitemap');
    register_setting('pm-seo-settings', 'pm-seo-disable-tags-sitemap');
    register_setting('pm-seo-settings', 'pm-seo-disable-category-sitemap');
    register_setting('pm-seo-settings', 'pm-seo-disable-testimonial-sitemap');
    register_setting('pm-seo-settings', 'pm-seo-include-translated-sitemaps');
}


function handle_attachment_pages_setting($input) {
    // Convert the setting to the format expected by WordPress
    $value = $input ? '0' : '1';
    update_option('wp_attachment_pages_enabled', $value);
    return $input;
}






function admin_settings_page() {

    global $wp_version;
    // Convert the WordPress version to a float for comparison
    $wp_version_float = floatval($wp_version);

    if(get_option('pm-seo-disable-sitemap') !== '1') {
        // Get available locales
        $available_languages = get_all_available_languages();
        $main_locale = get_main_language_locale();

        $locales_array = [];
        foreach ($available_languages as $lang_code => $language_info) {
            if ($language_info['locale'] === $main_locale) {
                // Skip the localized sitemap of the main locale
                continue;
            }
            $locales_array[] = $language_info['url_prefix'];
        }
        $languages_str = implode(', ', $locales_array);
    }

    ?>
    <div class="wrap pm-seo-admin-page">
        <h1>PEPS Media SEO settings and custom code</h1>
        <form method="post" action="options.php">
            <?php settings_fields('pm-seo-settings'); ?>
            <?php do_settings_sections('pm-seo-settings'); ?>
            <table class="form-table">
                <tr style="vertical-align: top;">
                    <th scope="row" colspan="2">
                        <h2>General settings</h2>
                    </th>
                </tr>
                <tr style="vertical-align: top;">
                    <th scope="row">Open Graph Site Name:</th>
                    <td>
                        <input type="text" name="pm-seo-og-site-name" value="<?php echo esc_attr(get_option('pm-seo-og-site-name')); ?>" class="regular-text" />
                        <p class="description">Enter the site name for the Open Graph <code>og:site_name</code> meta tag. Leave blank to use the default site name.</p>
                    </td>
                </tr>
                <?php if ($wp_version_float >= 6.4) { ?>
                <tr style="vertical-align: top;">
                    <th scope="row">Disable Attachment Pages:</th>
                    <td><input type="checkbox" class="pm-seo-checkbox" name="pm-seo-disable-attachment-pages" value="1" <?php checked(0, get_option('wp_attachment_pages_enabled'), true); ?> /> Disable all attachment pages (WP 6.4+).<br><br></td>
                </tr>
                <?php } ?>
                <tr style="vertical-align: top;">
                    <th scope="row">Disable author pages:</th>
                    <td><input type="checkbox" class="pm-seo-checkbox" name="pm-seo-disable-author-pages" value="1" <?php checked(1, get_option('pm-seo-disable-author-pages'), true); ?> /> Disable the author pages (will be 301 redirected to the homepage).<br><br></td>
                </tr>
                <tr style="vertical-align: top;">
                    <th scope="row">Delete data on uninstall:</th>
                    <td><input type="checkbox" class="pm-seo-checkbox" name="pm-seo-delete-data-on-uninstall" value="1" <?php checked(1, get_option('pm-seo-delete-data-on-uninstall'), true); ?> /> Delete all plugin data upon uninstall.<br><br></td>
                </tr>
                <tr style="vertical-align: top;">
                    <th scope="row" colspan="2">
                        <h2>Sitemap settings</h2>
                    </th>
                </tr>
                <tr style="vertical-align: top;">
                    <th scope="row">Disable Sitemap:</th>
                    <td><input type="checkbox" name="pm-seo-disable-sitemap" value="1" <?php checked(1, get_option('pm-seo-disable-sitemap'), true); ?> /> Disable the WordPress sitemap feature.<br><br></td>
                </tr>
                <?php if(get_option('pm-seo-disable-sitemap') !== '1'){ ?>
                <tr style="vertical-align: top;">
                    <th scope="row">Disable Author Sitemap:</th>
                    <td><input type="checkbox" name="pm-seo-disable-author-sitemap" value="1" <?php checked(1, get_option('pm-seo-disable-author-sitemap'), true); ?> /> Disable the author sitemap.<br><br></td>
                </tr>
                <tr style="vertical-align: top;">
                    <th scope="row">Disable User Sitemap:</th>
                    <td><input type="checkbox" name="pm-seo-disable-user-sitemap" value="1" <?php checked(1, get_option('pm-seo-disable-user-sitemap'), true); ?> /> Disable the user sitemap.<br><br></td>
                </tr>
                <tr style="vertical-align: top;">
                    <th scope="row">Disable Tags Sitemap:</th>
                    <td><input type="checkbox" name="pm-seo-disable-tags-sitemap" value="1" <?php checked(1, get_option('pm-seo-disable-tags-sitemap'), true); ?> /> Disable all tag related sitemaps.<br><br></td>
                </tr>
                <tr style="vertical-align: top;">
                    <th scope="row">Disable Category Sitemap:</th>
                    <td><input type="checkbox" name="pm-seo-disable-category-sitemap" value="1" <?php checked(1, get_option('pm-seo-disable-category-sitemap'), true); ?> /> Disable all category related sitemaps.<br><br></td>
                </tr>
                <tr style="vertical-align: top;">
                    <th scope="row">Disable Testimonial Sitemap:</th>
                    <td><input type="checkbox" name="pm-seo-disable-testimonial-sitemap" value="1" <?php checked(1, get_option('pm-seo-disable-testimonial-sitemap'), true); ?> /> Disable the testimonial sitemap.<br><br></td>
                </tr>
                <?php if( !empty($languages_str) ){ ?>
                <tr style="vertical-align: top;">
                    <th scope="row">Include Translated Sitemaps:</th>
                    <td><input type="checkbox" name="pm-seo-include-translated-sitemaps" value="1" <?php checked(1, get_option('pm-seo-include-translated-sitemaps'), true); ?> /> Include translated sitemaps (<?php echo esc_html($languages_str); ?>) in the main sitemap. Tested with WPML. Should also work with Polylang, TranslatePress and Weglot. If not, <a href="https://pepsmedia.nl/contact/" target="_blank">let us know</a>.<br></td>
                </tr>
                <?php } ?>
                <?php } ?>
                <tr style="vertical-align: top;">
                    <th scope="row"></th>
                    <td><br><?php submit_button(); ?></td>
                </tr>
                <tr style="vertical-align: top;">
                    <th scope="row" colspan="2">
                        <h2>Add custom code to website sections</h2>
                        <p>Note: open and close all script and style tags.</p>
                    </th>
                </tr>
                <tr style="vertical-align: top;">
                    <th scope="row">Header code:</th>
                    <td><textarea name="pm-seo-header-code" class="pm-seo-textarea" placeholder="&lt;style&gt;Css code..&lt;/style&gt;&#10&lt;script&gt;Javascript code..&lt;/script&gt;"><?php echo esc_textarea(get_option('pm-seo-header-code')); ?></textarea></td>
                </tr>
                <?php if ($wp_version >= '5.2') { ?>
                    <tr style="vertical-align: top;">
                        <th scope="row">Body open code:<br>(since WP 5.2 and if theme supports it)</th>
                        <td><textarea name="pm-seo-body-open-code" class="pm-seo-textarea"><?php echo esc_textarea(get_option('pm-seo-body-open-code')); ?></textarea></td>
                    </tr>
                <?php } ?>
                <tr style="vertical-align: top;">
                    <th scope="row">Footer code:</th>
                    <td><textarea name="pm-seo-footer-code" class="pm-seo-textarea"><?php echo esc_textarea(get_option('pm-seo-footer-code')); ?></textarea></td>
                </tr>
                <tr style="vertical-align: top;">
                    <th scope="row"></th>
                    <td><?php submit_button(); ?></td>
                </tr>
            </table>
        </form>
    </div>
    <?php
}


