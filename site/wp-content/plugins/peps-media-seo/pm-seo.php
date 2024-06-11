<?php
/*
Plugin Name: PEPS Media SEO Simple
Plugin URI: https://pepsmedia.nl/plugins/peps-media-seo/
Description: Add a custom page title, meta description and Open Graph metadata to your page. Admins can also add custom code to the header, after body open and footer sections of the page. Option to disable Attachment Pages, supported from WP version 6.4 and later. Option to disable the 'user' and 'author' sitemaps. Option to include the sitemap of translations into the main sitemap. Option to disable author pages.
Version: 3.11
Author: PEPS Media - Online Technologies
Author URI: https://pepsmedia.nl
License: GPL2
License URI: https://www.gnu.org/licenses/gpl-2.0.html
*/

if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}



function get_page_url() {

    global $wp;

    // Get the home URL without any path, just the domain
    $home_url = set_url_scheme(get_home_url(null, null));

    if (is_null($wp)) {
        // Return a default value or handle the error as appropriate
        return '';
    }

    $path = isset($wp->request) ? $wp->request : '';

    // Avoid adding a slash if the path is empty, which could result in double slashes
    if (trim($path) !== '') {
        $path = '/' . ltrim($path, '/');
    }

    // Construct the full URL without adding a trailing slash to the home_url to avoid double slashes
    $current_url = rtrim($home_url, '/') . $path;

    // Ensure the URL ends with a trailing slash if it's a directory
    $current_url = trailingslashit($current_url);

    // Use esc_url to ensure the URL is safe to display
    return esc_url($current_url);
}


function get_page_url_v2() {
    $current_url = home_url( add_query_arg( null, null ) );
    return esc_url($current_url);
}



// Add settings & review link on plugin page
if ( is_admin() ) {

    // Load admin pages
    function pm_seo_load_admin_pages() {
        include 'pm-seo-post-page.php';
        if (current_user_can('manage_options')) {
            include 'pm-seo-admin-page.php';
        }
    }
    add_action('plugins_loaded', 'pm_seo_load_admin_pages');

    function pm_seo_settings_link($links) {
        if (current_user_can('administrator')) {
            $settings_link = '<a href="' . admin_url('options-general.php?page=peps-media-seo-admin') . '">Settings</a>';
            array_unshift($links, $settings_link);
        }
        $links[] = '<a href="https://wordpress.org/plugins/peps-media-seo/#reviews" target="_blank">Leave review</a>';
        return $links;
    }
    add_filter('plugin_action_links_' . plugin_basename(__FILE__), 'pm_seo_settings_link');

}



// Add some handy shortcodes
function year_short_code($atts) {
    $atts = shortcode_atts(array('monthsahead' => 0), $atts, '');
    $atts['monthsahead'] = round($atts['monthsahead']);
    $year = date('Y', strtotime(" + {$atts['monthsahead']} months"));
    return $year;
}
add_shortcode('year', 'year_short_code');





// Function which displays the title
function pm_seo_get_title() {

    global $post;
    $title = '';

    if ( isset($post) ) {
        $title_custom = get_post_meta($post->ID, 'pm_seo_title', true);
    }
    if ( isset($title_custom) && !empty($title_custom) ) {
        $title = $title_custom;
    }

    $title = do_shortcode( $title );
    $title = trim( $title );
    return $title;
}




// Function which gets the description
function pm_seo_get_description() {

    global $post;

    if ( isset($post) ) {
        $description = get_post_meta($post->ID, 'pm_seo_description', true);

        if (isset($description) && !empty($description)) {
            $description = do_shortcode( $description );
            $description = trim( $description );
            return $description;
        }
    }
}




// Function which gets the image
function pm_seo_get_image_id() {

    global $post;

    if ( isset($post) ) {
        $image_id = get_post_meta( $post->ID, 'pm_seo_image_id', true );
        if ( is_numeric($image_id) && isset($image_id) && !empty($image_id) ) {
            return $image_id;
        }
    }
}




function pm_seo_get_image_meta() {
    if( !empty( pm_seo_get_image_id() ) ) {
        $image_id = pm_seo_get_image_id();
    }
    if ( isset($image_id) && is_numeric($image_id) ) {
        return wp_get_attachment_image_src($image_id, 'full');
    }
}


/*
 * Disable author pages when checked
 */
function disable_author_page() {
    if ( is_author() && get_option('pm-seo-disable-author-pages') === '1' ) {
        // Redirect to homepage, set status to 301 permenant redirect.
        // Function defaults to 302 temporary redirect.
        wp_redirect(get_option('home'), 301);
        exit;
    }
}
add_action('template_redirect', 'disable_author_page');



/*
 *
 * Check if we're on the sitemap url and if the sitemap is disabled or not.
 * If not, allow further sitemap customization settings.
 *
 */
if ( get_option('pm-seo-disable-sitemap') === '1' && strpos( get_page_url_v2(), 'wp-sitemap') !== false ) {

    add_filter( 'wp_sitemaps_enabled', '__return_false' );

}else {

    if (strpos( get_page_url_v2(), '.xml') !== false) {
        // Disable specific sitemaps

        // Function to disable sitemap providers for taxonomies and post types.
        function disable_specific_sitemap_providers($provider, $name) {
            // Array of sitemap types and the corresponding options that control them.
            $sitemap_options = [
                'users' => 'pm-seo-disable-user-sitemap',
                'authors' => 'pm-seo-disable-author-sitemap',
            ];

            // Check if the current sitemap provider should be disabled based on its option & name.
            if (array_key_exists($name, $sitemap_options) && get_option($sitemap_options[$name]) === '1') {
                return false; // Return false to disable this sitemap provider.
            }

            return $provider; // Return the original provider if it's not being disabled.
        }
        add_filter('wp_sitemaps_add_provider', 'disable_specific_sitemap_providers', 10, 2);

        // Disable specific taxonomy sitemaps
        add_filter('wp_sitemaps_taxonomies', function ($taxonomies) {
            if (get_option('pm-seo-disable-tags-sitemap') === '1') {
                unset($taxonomies['post_tag']); // Assuming 'post_tag' is the name for tags
            }
            if (get_option('pm-seo-disable-category-sitemap') === '1') {
                unset($taxonomies['category']); // Assuming 'category' is the name for categories
            }
            if (get_option('pm-seo-disable-testimonial-sitemap') === '1') {
                unset($taxonomies['testimonial_category']);
            }
            return $taxonomies;
        });

        // Disable specific post type sitemaps
        add_filter('wp_sitemaps_post_types', function ($post_types) {
            if (get_option('pm-seo-disable-testimonial-sitemap') === '1') {
                unset($post_types['testimonial']);
            }
            return $post_types;
        });

        // This function checks the plugin settings and adds the 'blocked_sitemap_settings' function to the 'template_redirect' action hook if necessary.
        function disable_specific_sitemaps() {
            $current_url = get_page_url_v2(); // Function that gets the current URL.

            // Check if the current URL contains any of the sitemap slugs we want to disable.
            $blocked_sitemaps = [
                'author' => get_option('pm-seo-disable-author-sitemap'),
                'user' => get_option('pm-seo-disable-user-sitemap'),
                'tags' => get_option('pm-seo-disable-tags-sitemap'),
                'category' => get_option('pm-seo-disable-category-sitemap'),
                'testimonial' => get_option('pm-seo-disable-testimonial-sitemap'),
                'localized' => get_option('pm-seo-include-translated-sitemaps')
            ];

            foreach ($blocked_sitemaps as $slug => $is_disabled) {
                if ($is_disabled === '1' && strpos($current_url, $slug) !== false && $slug != 'localized') {
                    handle_blocked_sitemap();
                    break; // No need to continue checking once we've found a match.
                }
                if ($is_disabled !== '1' && strpos($current_url, $slug) !== false && $slug == 'localized') {
                    handle_blocked_sitemap();
                    break; // No need to continue checking once we've found a match.
                }
            }
        }
        add_action('init', 'disable_specific_sitemaps');

        // This function sends a 404 status, clears the cache headers, and includes the 404 template.
        function handle_blocked_sitemap() {
            status_header(404);
            nocache_headers();
            include(get_query_template('404'));
            die();
        }


    } // Close if, disable specific sitemaps


    // If option 'pm-seo-include-translated-sitemaps' is enabled, add the localized sitemaps to the main WP sitemap.
    class Localized_Sitemap_Provider extends WP_Sitemaps_Provider {

        public function __construct() {
            $this->name = 'localized';
            $this->object_type = 'localized';
        }

        
        public function get_url_list($page_num, $post_type = '', $term_id = 0) {

            $sitemap_entries = [];
            $available_languages = get_all_available_languages();

            $home_url = home_url();

            $main_locale = get_main_language_locale(); // /en_US/
            $main_locale_short = substr($main_locale, 0, 2); // /en/

            foreach ($available_languages as $lang_code => $language_info) {
                if ($language_info['locale'] === $main_locale) {
                    // Skip the localized sitemap of the main locale
                    continue;
                }

                $url_prefix = $language_info['url_prefix'];

                // Remove the main locale from the home URL if present
                if (strpos($home_url, "/{$main_locale}/") !== false) {
                    $home_url = str_replace("/{$main_locale}/", '', $home_url);
                }elseif (strpos($home_url, "/{$main_locale_short}/") !== false) {
                    $home_url = str_replace("/{$main_locale_short}/", '', $home_url);
                }

                // Construct localized sitemap URL
                $sitemap_url = !empty($url_prefix) ? "{$home_url}/{$url_prefix}/wp-sitemap.xml" : "{$home_url}/wp-sitemap.xml";

                // Ensure that there are no double slashes except for the protocol part
                $sitemap_url = preg_replace('#([^:])//+#', '\\1/', $sitemap_url);

                $sitemap_entries[] = array(
                    'loc' => $sitemap_url,
                );
            }

            return $sitemap_entries;
        }


        // Required method
        public function get_max_num_pages($post_type = '', $term_id = 0) {
            return 1;
        }

    }


    function get_all_available_languages() {
        $languages = array();

        if (function_exists('icl_object_id')) {
            // WPML is active
            $wpml_languages = apply_filters('wpml_active_languages', NULL);
            if (!empty($wpml_languages)) {
                foreach ($wpml_languages as $lang_code => $lang_info) {
                    $languages[$lang_code] = array(
                        'locale' => $lang_info['default_locale'],
                        'url_prefix' => $lang_info['tag'], // URL locale prefix
                    );
                }
            }
        } elseif (function_exists('pll_languages_list')) {
            // Polylang is active
            $polylang_languages = pll_languages_list(array('fields' => 'slug,locale'));
            foreach ($polylang_languages as $language) {
                $languages[$language->slug] = array(
                    'locale' => $language->locale,
                    'url_prefix' => $language->slug, // URL locale prefix
                );
            }
        } elseif (function_exists('trp_get_languages')) {
            // TranslatePress is active
            $translatepress_languages = trp_get_languages();
            foreach ($translatepress_languages as $locale => $language) {
                $languages[$locale] = array(
                    'locale' => $locale,
                    'url_prefix' => $language['url_slug'], // URL locale prefix
                );
            }
        } elseif (function_exists('weglot_get_languages')) {
            // Weglot is active
            $weglot_languages = weglot_get_languages();
            foreach ($weglot_languages as $language) {
                $languages[$language->getIso639()] = array(
                    'locale' => $language->getLocalName(),
                    'url_prefix' => $language->getIso639(), // URL locale prefix
                );
            }
        } else {
            // No translation plugin detected, using WordPress default locale
            $default_locale = get_locale();
            $languages[$default_locale] = array(
                'locale' => $default_locale,
                'url_prefix' => '', // No URL locale prefix for default language
            );
        }

        return $languages;
    }


    function get_main_language_locale() {
        if (function_exists('pll_default_language')) {
            // Polylang is active
            $default_language = pll_default_language();
            return pll_get_locale($default_language);
        } elseif (function_exists('icl_object_id')) {
            // WPML is active
            $default_language = apply_filters('wpml_default_language', NULL);
            $active_languages = apply_filters('wpml_active_languages', NULL, 'orderby=id&order=desc');

            if (!empty($active_languages) && !empty($default_language) && isset($active_languages[$default_language])) {
                return isset($active_languages[$default_language]['default_locale']) ? $active_languages[$default_language]['default_locale'] : get_locale();
            }
        } elseif (function_exists('trp_get_languages')) {
            // TranslatePress is active
            $translatepress_languages = trp_get_languages();
            $default_language = trp_get_default_language();

            if (!empty($translatepress_languages) && !empty($default_language) && isset($translatepress_languages[$default_language])) {
                return $translatepress_languages[$default_language];
            }
        } elseif (function_exists('weglot_get_default_language')) {
            // Weglot is active
            $default_language = weglot_get_default_language();
            return $default_language->getLocale();
        } else {
            // No multilingual plugin, or not recognized
            return get_locale();
        }
    }


    // Add or remove the wp-sitemap-localized-1.xml to the main WP sitemap
    add_action('init', function () {
        if (function_exists('wp_register_sitemap_provider') && get_option('pm-seo-include-translated-sitemaps') === '1') {
            $provider = new Localized_Sitemap_Provider();
            wp_register_sitemap_provider('localized', $provider);
        }
    });


    // Prevent the wp-sitemap-localized-1.xml from also being added to the main sitemaps of each locale.
    add_filter('wp_sitemaps_add_provider', function ($provider, $name) {

        $main_locale = get_main_language_locale();
        $current_locale = get_locale();

        if ('localized' === $name && $main_locale !== $current_locale) {
            return false;
        }

        return $provider;
    }, 10, 2);



    if ( strpos( get_page_url_v2(), 'wp-sitemap-localized-1.xml') !== false ) {
        add_action('template_redirect', function () {
            ob_start(function ($buffer) {
                if (false !== strpos($buffer, '<urlset')) {
                    $buffer = str_replace('wp-sitemap.xsl', 'wp-sitemap-index.xsl', $buffer);

                    // Replace urlset with sitemapindex
                    $buffer = str_replace('<urlset', '<sitemapindex', $buffer);
                    $buffer = str_replace('</urlset>', '</sitemapindex>', $buffer);

                    // Replace url with sitemap
                    $buffer = str_replace('<url>', '<sitemap>', $buffer);
                    $buffer = str_replace('</url>', '</sitemap>', $buffer);
                }
                return $buffer;
            });
        });
    }




    function exclude_from_sitemap_by_post_meta( $args ) {
        $args['meta_query'] = array(
            array(
                'key'     => 'pm_seo_sitemap_exclude',
                'value'   => '1',
                'compare' => 'NOT EXISTS', //NOT EXISTS
            ),
        );

        return $args;
    }

    add_filter( 'wp_sitemaps_posts_query_args', 'exclude_from_sitemap_by_post_meta', 5, 2 );
    add_filter( 'wp_sitemaps_pages_query_args', 'exclude_from_sitemap_by_post_meta', 5, 2 );


} // Close if




if ( ! is_admin() ) {
    include 'pm-seo-front-end-output.php';
}
