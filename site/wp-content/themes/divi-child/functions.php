<?php
/*
function my_theme_enqueue_styles() { 
		wp_enqueue_style( 'parent-style', get_template_directory_uri() . '/style.css' );
}
add_action( 'wp_enqueue_scripts', 'my_theme_enqueue_styles' );
*/
/* Prevent Google Fonts from loading
=============================================================== */
function prevent_loading_fonts() {
	remove_action( 'wp_enqueue_scripts', 'et_divi_load_fonts' );
}
add_action( 'init', 'prevent_loading_fonts', 20 );

function et_builder_get_google_fonts() {
	return array();
}
function et_get_google_fonts() {
	return array();
}

function my_custom_mime_types( $mimes ) {

	// New allowed mime types.
	$mimes['svg']  = 'image/svg+xml';
	$mimes['svgz'] = 'image/svg+xml';
	$mimes['ttf']  = 'font/ttf';

	// Optional. Remove a mime type.
	unset( $mimes['exe'] );

	return $mimes;
}

add_filter( 'upload_mimes', 'my_custom_mime_types' );

add_filter( 'send_password_change_email', '__return_false' );



function aktuelles() {
	$user_wp    = wp_get_current_user();
	$user_id    = $user_wp->data->ID;
	$user_email = $user_wp->user_email;

	$content = '<form method="post" action="/?na=s">
    <input type="hidden" name="nlang" value="">
    <input type="hidden" name="ny" value="1">
    <input type="hidden" name="ne" value="' . $user_email . '">
    <input class="tnp-submit" type="submit" value="Ich bin einverstanden" >
    </form>';

	return $content;
}
//register the Shortcode handler
add_shortcode( 'aktuelles', 'aktuelles' );

function typeform() {
	ob_start();
	//include the specified file
	include ( $_SERVER['DOCUMENT_ROOT'] . '/typeform.php' );
	//assign the file output to $content variable and clean buffer
	$content = ob_get_clean();
	//return the $content
	//return is important for the output to appear at the correct position
	//in the content
	return $content;
}
//register the Shortcode handler
add_shortcode( 'typeform', 'typeform' );


function dpdfg_custom_loader() {
	ob_start();
	?>
	<div class="dp-dfg-loader">
		<div class="my_custom_loader"></div>
	</div>
	<style>
		.dp-dfg-loader {
			position: absolute;
			top: 50%;
			left: 50%;
			margin-top: -30px;
			margin-left: -30px;
		}

		.my_custom_loader {
			border: 8px solid #f3f3f3;
			border-top: 8px solid #00708D;
			border-radius: 50%;
			width: 60px;
			height: 60px;
			animation: spin 2s linear infinite;
			margin: 0 auto;
		}

		@keyframes spin {
			0% {
				transform: rotate(0deg);
			}

			100% {
				transform: rotate(360deg);
			}
		}
	</style>
	<?php
	return ob_get_clean();
}

add_filter( 'dpdfg_custom_loader', 'dpdfg_custom_loader' );

function dpdfg_custom_action( $default_action, $props, $post_id ) {

	$post = get_post( $post_id );

	if ( $post->post_content == "" ) return "none";
	else return $default_action;

}

add_filter( 'dpdfg_custom_action', 'dpdfg_custom_action', 10, 3 );

function filter_wpseo_breadcrumb_separator( $this_options_breadcrumbs_sep ) {
	return '<i class="far fa-chevron-left" style="margin-left:10px;margin-right:10px;color:#00708d"></i>';
}
;

// add the filter
add_filter( 'wpseo_breadcrumb_separator', 'filter_wpseo_breadcrumb_separator', 10, 1 );


add_filter( 'wpseo_breadcrumb_links', 'yoast_seo_breadcrumb_append_link' );

function yoast_seo_breadcrumb_append_link( $links ) {
	global $post;


	if ( 'faq' == get_post_type() && is_single() ) {
		$breadcrumb[] = array(
			'url' => site_url( '/faq/' ),
			'text' => 'FAQ',
		);
		array_splice( $links, 1, 1, $breadcrumb );
	}

	if ( 'video' == get_post_type() && is_single() ) {
		$breadcrumb[] = array(
			'url' => site_url( '/videos/' ),
			'text' => 'Videos',
		);
		array_splice( $links, 1, 1, $breadcrumb );
	}


	return $links;
}

// Beiträge umbennnen
// 
function change_post_object_label() {
	global $wp_post_types;

	$labels                = &$wp_post_types['post']->labels;
	$labels->name          = 'Aktuelles';
	$labels->singular_name = 'Aktuelles';

}
add_action( 'init', 'change_post_object_label' );

function prefix_query_args( $query_args, $grid_id ) {

	//if ( 2 === $grid_id ) {
	$query_args['ignore_sticky_posts'] = true;
	//}

	return $query_args;

}

add_filter( 'wp_grid_builder/grid/query_args', 'prefix_query_args', 10, 2 );


/**
 * Capture user login and add it as timestamp in user meta data
 *
 */

function user_last_login( $user_login, $user ) {
	update_user_meta( $user->ID, 'last_login', time() );
}
add_action( 'wp_login', 'user_last_login', 10, 2 );


add_shortcode( 'vimeo', 'vimeo_shortcode' );


function vimeo_shortcode( $atts, $content = null ) {
	extract( shortcode_atts( array(
		'id' => ''
	), $atts ) );

	if ( empty( $id ) ) return 'Vimeo: Bitte Vimeo ID eingeben!';

	return "<style>.embed-container { position: relative; padding-bottom: 56.25%; height: 0; overflow: hidden; max-width: 100%; } .embed-container iframe, .embed-container object, .embed-container embed { position: absolute; top: 0; left: 0; width: 100%; height: 100%; }</style><div class='embed-container'><iframe src='https://player.vimeo.com/video/" . $id . "' frameborder='0' webkitAllowFullScreen mozallowfullscreen allowFullScreen></iframe></div>";
}
