<?php

if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}

// Set the plugin actions
add_action('init', 'pm_seo_start');


function pm_seo_start() {

    global $wp_version;

    add_filter('wp_title', 'pm_seo_get_title', 20);
    add_filter('wpseo_title', 'pm_seo_get_title', 20); // Override Yoast title if set
    add_filter('pre_get_document_title', 'pm_seo_get_title', 20);

    add_action('wp_head', 'pm_seo_show_description');
    add_action('wp_head', 'pm_seo_show_noindex');
    add_action('wp_head', 'pm_seo_show_og');
    add_action('wp_head', 'pm_seo_header_code');

    if ($wp_version >= '5.2') {
        add_action('wp_body_open', 'pm_seo_body_open_code');
    }

    add_action('wp_footer', 'pm_seo_footer_code');
}




// Function which displays the description
function pm_seo_show_description() {
    $description = pm_seo_get_description();
    if (!empty($description)) {
        echo "\r\n" . '<meta name="description" content="' . esc_attr($description) . '" />' . "\r\n";
    }
}



// Function which displays og meta
function pm_seo_show_og( $title ) {

    global $post;

    if( !empty( pm_seo_get_image_id() ) ) {
        
        $image_id   = pm_seo_get_image_id();
        $image_data = wp_get_attachment_image_src( $image_id, "full" );
    
    }
    
    $og = array();
    $twitter = array();

	if( !empty( get_option('pm-seo-og-site-name') ) ) {
        $og_site_name = esc_html( do_shortcode( get_option('pm-seo-og-site-name') ) );
    }elseif( !empty( get_bloginfo( 'name' ) ) ) {
        $og_site_name = esc_html( get_bloginfo( 'name' ) );
	}

    if( isset($og_site_name))
        $og[] = '<meta property="og:site_name" content="'. esc_attr($og_site_name) .'" />';

    if( !empty( pm_seo_get_title() ) ) {
        $og[] = '<meta property="og:title" content="'. esc_attr( pm_seo_get_title() ) .'" />';
        $twitter[] = '<meta name="twitter:title" content="'. esc_attr( pm_seo_get_title() ) .'" />';
    }

    if( !empty( pm_seo_get_description() ) ) {
        $og[] = '<meta property="og:description" content="'. esc_attr( pm_seo_get_description() ) .'" />';
        $twitter[] = '<meta name="twitter:description" content="'. esc_attr( pm_seo_get_description() ) .'" />';
    }

    if ( ! empty( $post->ID ) && get_permalink( $post->ID ) !== false && get_permalink( $post->ID ) != 'null' ) {
        if ( is_single() ) {
            $og[] = '<meta property="og:type" content="article" />';
        } else {
            $og[] = '<meta property="og:type" content="website" />';
        }

        $og[] = '<meta property="og:url" content="' . esc_attr( get_page_url() ) . '" />';

        $twitter[] = '<meta name="twitter:url" content="'. esc_attr( get_page_url() ) .'" />';
    }

    if( !empty($image_data) ){
        $og[] = "<meta property='og:image' content='$image_data[0]' />";
        $og[] = "<meta property='og:image:width' content='$image_data[1]' />";
        $og[] = "<meta property='og:image:height' content='$image_data[2]' />";

        $twitter[] = '<meta name="twitter:image" content="'. esc_attr( esc_url($image_data[0]) ) .'" />';

        if( !empty( pm_seo_get_title() ) )	{
            $og[] = '<meta property="og:image:alt" content="'. esc_attr( pm_seo_get_title() ) .'" />';
            $twitter[] = '<meta name="twitter:image:alt" content="'. esc_attr( pm_seo_get_title() ) .'" />';
        }

    }

    // If twitter not empty, the following meta tag is added at the beginning
    if (!empty( pm_seo_get_title() ) || !empty( pm_seo_get_description() ) || !empty($image_data) ) {
        array_unshift($twitter, '<meta name="twitter:card" content="summary" />');
        echo "\r\n" . implode("\r\n", $og) . "\r\n";
        echo "\r\n" . implode("\r\n", $twitter) . "\r\n";
    }

    echo "\r\n" .'<meta name="robots" content="max-snippet:-1, max-image-preview:large, max-video-preview:-1"/>'. "\r\n";

}





// Function which sets a page/post to no-index
function pm_seo_show_noindex() {

    global $post;
    
	if ( isset($post) ) {
		
		$noindex = get_post_meta( $post->ID, 'pm_seo_noindex', true );

		if ( $noindex == '1' )
			echo "\r\n" .'<meta name="robots" content="noindex" />'. "\r\n";
		
	}

}






// Adds custom code to page/post header, after body open, footer
function pm_seo_header_code (){

    echo "\r\n" . get_option( 'pm-seo-header-code' ) . "\r\n";

}

function pm_seo_body_open_code (){

    echo "\r\n" . get_option( 'pm-seo-body-open-code' ) . "\r\n";

}

function pm_seo_footer_code (){

    echo "\r\n" . get_option( 'pm-seo-footer-code' ) . "\r\n";

}
