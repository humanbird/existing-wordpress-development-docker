<?php

if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}


// Add SEO fields to a page / post
function pm_seo_add_metabox_page() {
    add_meta_box(
        'pm_seo', // Unique ID
        'PEPS Media SEO Area', // Box title
        'pm_seo_metabox_page',  // Content callback
        array('page', 'post'), // Post type
        'normal', // Context where the box will appear ('normal', 'side', 'advanced')
        'high'); // Priority within the context where the boxes should show ('high', 'low')
}
add_action('add_meta_boxes', 'pm_seo_add_metabox_page');


// Load style sheets
function pm_seo_backend_start() {
    wp_register_style('pm-seo-style', plugins_url('/css/pm-seo-css.css?202311072027', __FILE__));
    wp_enqueue_style('pm-seo-style');
}
add_action('admin_menu', 'pm_seo_backend_start');


// Build the PEPS Media SEO fields
function pm_seo_metabox_page(){

    global $post;

    if ( get_post_type() === 'page' )
        $page = "page";
    else
        $page = "post";

    // Check for nonces before saving the data
    wp_nonce_field('pm_seo_save_meta_box_data', 'pm_seo_meta_box_nonce');

    $title          = get_post_meta($post->ID, 'pm_seo_title', true);
    $description    = get_post_meta($post->ID, 'pm_seo_description', true);
    $image_id       = get_post_meta($post->ID, 'pm_seo_image_id', true);
    $image          = intval($image_id) > 0 ? wp_get_attachment_image($image_id, 'medium', false, array('id' => 'pm-seo-social-image')) : '<div><img id="pm-seo-social-image" src="" /></div><br/>';
    $sp_exclude     = get_post_meta($post->ID, 'pm_seo_sitemap_exclude', true);
    $noindex        = get_post_meta($post->ID, 'pm_seo_noindex', true);

    $year1          = date( 'Y' );
    $year2          = date( 'Y', strtotime(' + 14 months') );
    ?>
	<p class="pm_seo_p">Handy short code: [year] = current year (<?php echo $year1; ?>), [year monthsahead='14'] = current year + 14 months (<?php echo $year2; ?>).</p>
	
    <hr class="pm_seo_hr" />

	<p class="pm_seo_p"><?php echo ucfirst($page); ?> title tag (Google)</p>
	<input type="text" name="pm_seo_title" id="pm_seo_title" onkeyup="countCharsTitle(this);" value="<?php echo esc_attr($title); ?>" />
    <span name="titleLength" id="titleLength"><?php echo 80 - strlen($title); ?> characters left</span>
	
    <hr class="pm_seo_hr" />

	<p class="pm_seo_p"><?php echo ucfirst($page); ?> meta description tag</p>
	<textarea name="pm_seo_description" id="pm_seo_description" onkeyup="countCharsDesc(this);"><?php echo esc_attr($description); ?></textarea>
    <span name="descLength" id="descLength"><?php echo 320 - strlen($description); ?> characters left</span>

    <hr class="pm_seo_hr" />

	<p class="pm_seo_p">Select <?php echo $page; ?> (social) share image</p>
	<span>This image will be shown when the <?php echo $page; ?> is shared.</span>

	<?php
    if( isset($image) ) {

        $image_data = pm_seo_get_image_meta();

		if ( isset($image_data) ) {
			echo "<br><br>
				Width: ". esc_attr($image_data[1]) ."px<br>
				Height: ". esc_attr($image_data[2]) ."px<br><br> 
			";
		}

        echo "<div>". $image ."</div>";
        
    }
    ?>

 	<input type="hidden" name="pm-seo-image-id" id="pm-seo-image-id" value="<?php echo esc_attr( $image_id ); ?>" class="regular-text" />
 	<input type="button" class="button-primary" value="<?php esc_attr_e( 'Select an image', 'pm_seo' ); ?>" id="pm-seo-media-manager"/>

    <hr class="pm_seo_hr" />

	<p class="pm_seo_p">Under the hood markup</p>
	<span>The plugin automatically adds Open Graph,
		Twitter card and meta description markup.
		These tags are generated using the values
		you entered above. You can check them by 
		viewing the page source.</span>

    <hr class="pm_seo_hr" />

    <h4>Page specific settings</h4>

    <p class="pm_seo_p">
        Exclude <?php echo $page; ?> from WP sitemap
    </p>
    <label for="pm_seo_sitemap_exclude">
        <?php // <input type="hidden" value="0" name="pm_seo_sitemap_exclude"> ?>
        <input type="checkbox" class="pm-seo-checkbox-page" name="pm_seo_sitemap_exclude" value="1"  <?php checked(1, $sp_exclude, true); ?> />
        <?php esc_html_e('Exclude from Sitemap', 'pm_seo'); ?>
    </label>

	<p class="pm_seo_p">
        Set <?php echo $page; ?> to noindex. Search engines will not index this <?php echo $page; ?> when checked.
    </p>
    <label name="pm_seo_noindex">
        <input type="hidden" value="0" name="pm_seo_noindex">
        <input type="checkbox" class="pm-seo-checkbox-page" name="pm_seo_noindex" value="1" <?php checked(1, $noindex, true); ?> />
        <span>Hide <u>this</u> page from search engines. (This does not affect the indexability of other pages).</span>
    </label>

	<?php 
}




// Save the PEPS Media SEO field input on page save
function pm_seo_save_values($post_id) {

    // Check if this is an autosave.
    if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE)
        return;

    // Check if our nonce is set because this saves only when our custom metabox is submitted.
    if (!isset($_POST['pm_seo_meta_box_nonce']) || !wp_verify_nonce($_POST['pm_seo_meta_box_nonce'], 'pm_seo_save_meta_box_data'))
        return;

    // Check the user's permissions.
    if (isset($_POST['post_type']) && 'page' == $_POST['post_type']) {
        if (!current_user_can('edit_page', $post_id))
            return;
    } else {
        if (!current_user_can('edit_post', $post_id))
            return;
    }

    // Now we can save the data
    if (isset($_POST['pm_seo_title'])) {
        update_post_meta($post_id, 'pm_seo_title', sanitize_text_field($_POST['pm_seo_title']));
    }
    if (isset($_POST['pm_seo_description'])) {
        update_post_meta($post_id, 'pm_seo_description', sanitize_text_field($_POST['pm_seo_description']));
    }
    if (isset($_POST['pm-seo-image-id'])) {
        update_post_meta($post_id, 'pm_seo_image_id', intval($_POST['pm-seo-image-id']));
    }
    if (isset($_POST['pm_seo_sitemap_exclude']) && $_POST['pm_seo_sitemap_exclude'] == '1') {
        update_post_meta($post_id, 'pm_seo_sitemap_exclude', '1');
    } else {
        // If 'pm_seo_sitemap_exclude' is not set to '1', delete the meta key
        delete_post_meta($post_id, 'pm_seo_sitemap_exclude');
    }
    if (isset($_POST['pm_seo_noindex'])) {
        $no_index = $_POST['pm_seo_noindex'] == '1' ? '1' : '0';
        update_post_meta($post_id, 'pm_seo_noindex', $no_index);
    }
}
add_action('save_post', 'pm_seo_save_values');



// Enqueue media scripts
function load_wp_media_files($page) {
    if (in_array(get_post_type(), array('page', 'post'))) {
        wp_enqueue_media();
        wp_enqueue_script('pm_seo_script', plugins_url('/js/javascript.js', __FILE__), array('jquery'), '0.1', true);
    }
}
add_action('admin_enqueue_scripts', 'load_wp_media_files');




// Ajax action to refresh the user image
add_action('wp_ajax_pm_seo_get_image', 'pm_seo_get_image');
function pm_seo_get_image() {
    if (isset($_GET['id']) && intval($_GET['id'])) {
        $image_id = intval($_GET['id']);
        $image = wp_get_attachment_image($image_id, 'medium', false, array('id' => 'pm-seo-social-image'));
        wp_send_json_success(array('image' => $image));
    } else {
        wp_send_json_error();
    }
}

