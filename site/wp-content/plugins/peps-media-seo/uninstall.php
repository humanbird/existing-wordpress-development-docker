<?php

if (!defined('WP_UNINSTALL_PLUGIN')) {
    exit();
}

// Check if user chose to delete all data
$delete_data = get_option('pm-seo-delete-data-on-uninstall', false);

if ($delete_data !== '1') {
    // Exit if user chose not to delete data
    return;
}

// List of meta keys to delete
$meta_keys = array(
    'pm_seo_title',
    'pm_seo_description',
    'pm_seo_noindex',
    'pm_seo_image_id',
    'pm_image_id',
    'pm_seo_sitemap_exclude'
);

// Fetch all posts and pages
$allposts = get_posts(array(
    'numberposts' => -1,
    'post_type' => array('post', 'page'),
    'post_status' => 'any'
));

// Delete meta for each post/page
foreach ($allposts as $postinfo) {
    foreach ($meta_keys as $meta_key) {
        delete_post_meta($postinfo->ID, $meta_key);
    }
}

// List of plugin settings (options) to delete
$option_keys = array(
    'pm-seo-header-code',
    'pm-seo-body-open-code',
    'pm-seo-footer-code',
    'pm-seo-og-site-name',
    'pm-seo-disable-attachment-pages',
    'pm-seo-disable-author-pages',
    'pm-seo-delete-data-on-uninstall',
    'pm-seo-disable-author-sitemap',
    'pm-seo-disable-user-sitemap',
    'pm-seo-include-translated-sitemaps'
);

// Delete plugin settings
foreach ($option_keys as $option_key) {
    delete_option($option_key);
}
