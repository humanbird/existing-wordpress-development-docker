<?php
/**
 * Easy Digital Downloads Theme Updater
 * This file contains modified code from and/or based on the Software Licensing addon by Easy Digital Downloads
 * Licensed under the GNU General Public License v2.0 or higher; see license.txt in theme root
 *
 */


if (!defined('ABSPATH')) exit;

define( 'AGSDCM_STORE_URL', 'https://divi.space/' );
define( 'AGSDCM_ITEM_NAME', 'Divi Extras' ); // Needs to exactly match the download name in EDD
define( 'AGSDCM_PLUGIN_PAGE', 'admin.php?page=ds-divi-extras' );

define('AGSDCM_BRAND_NAME', 'Divi Space');

if( !class_exists( 'AGSDCM_Plugin_Updater' ) ) {
	// load our custom updater
	include( dirname( __FILE__ ) . '/EDD_SL_Plugin_Updater.php' );
}

// Load translations
load_plugin_textdomain('ds-divi-extras', false, plugin_basename(dirname(__FILE__).'/lang'));

function AGSDCM_updater() {

	// retrieve our license key from the DB
	$license_key = trim( get_option( 'AGSDCM_license_key' ) );

	// setup the updater
	new AGSDCM_Plugin_Updater( AGSDCM_STORE_URL, AGS_DIVI_CAT_MODULES_FILE, array(
			'version' 	=> AGS_DIVI_CAT_MODULES_VERSION, // current version number
			'license' 	=> $license_key, 		// license key (used get_option above to retrieve from DB)
			'item_name' => AGSDCM_ITEM_NAME, 	// name of this plugin
			'author' 	=> AGSDCM_BRAND_NAME,  // author of this plugin
			'beta'		=> false
		)
	);
	
	// creates our settings in the options table
	register_setting('AGSDCM_license', 'AGSDCM_license_key', 'AGSDCM_sanitize_license' );
	
	if (isset($_POST['AGSDCM_license_key_deactivate'])) {
		require_once(dirname(__FILE__).'/license-key-activation.php');
		$result = AGSDCM_deactivate_license();
		if ($result !== true) {
			define('AGSDCM_DEACTIVATE_ERROR', empty($result) ? __('An unknown error has occurred. Please try again.', 'ds-divi-extras') : $result);
		}
		unset($_POST['AGSDCM_license_key_deactivate']);
	}
}
add_action( 'admin_init', 'AGSDCM_updater', 0 );


function AGSDCM_has_license_key() {
	return (get_option('AGSDCM_license_status') === 'valid');
}

function AGSDCM_activate_page() {
	$license = get_option( 'AGSDCM_license_key' );
	$status  = get_option( 'AGSDCM_license_status' );
	?>
		<div class="wrap" id="AGSDCM_license_key_activation_page">
			<form method="post" action="options.php" id="AGSDCM_license_key_form">
				<div id="AGSDCM_license_key_form_header">
					<a href="https://divi.space/" target="_blank">
						<img src="<?php echo(plugins_url('logo.png', __FILE__)); ?>" alt="<?php echo(AGSDCM_BRAND_NAME); ?>" />
					</a>
				</div>
				
				<div id="AGSDCM_license_key_form_body">
					<div id="AGSDCM_license_key_form_title">
						<h3><?php echo(esc_html(AGSDCM_ITEM_NAME)); ?>
						<small>v<?php echo(AGS_DIVI_CAT_MODULES_VERSION); ?></small></h3>
					</div>
					
					<p> <?php echo sprintf( esc_html__('Thank you for purchasing %s! %s Please enter your license key below.', 'ds-divi-extras' ), htmlspecialchars(AGSDCM_ITEM_NAME),'<br />');?></p>
					
					<?php settings_fields('AGSDCM_license'); ?>
					
					<label>
						<span><?php _e('License Key:', 'ds-divi-extras'); ?></span>
						<input name="AGSDCM_license_key" type="text" class="regular-text"<?php if (!empty($_GET['license_key'])) { ?> value="<?php echo(esc_attr($_GET['license_key'])); ?>"<?php } else if (!empty($license)) { ?> value="<?php echo(esc_attr($license)); ?>"<?php } ?> />
					</label>
					
					<?php
						if (isset($_GET['sl_activation']) && $_GET['sl_activation'] == 'false') {
							echo('<p id="AGSDCM_license_key_form_error">'.(empty($_GET['sl_message']) ? esc_html__('An unknown error has occurred. Please try again.', 'ds-divi-extras') : esc_html($_GET['sl_message'])).'</p>');
						} else if (defined('AGSDCM_DEACTIVATE_ERROR')) {
							// AGSDCM_DEACTIVATE_ERROR is already HTML escaped
							echo('<p id="AGSDCM_license_key_form_error">'.AGSDCM_DEACTIVATE_ERROR.'</p>');
						}
						
						submit_button(esc_html__('Continue', 'ds-divi-extras'));
					?>
				</div>
			</form>
		</div>
	<?php
}

function AGSDCM_license_key_box() {
	$status  = get_option( 'AGSDCM_license_status' );
    $display_license = str_repeat( '*', strlen( esc_html(get_option('AGSDCM_license_key'))) - 4 ) . substr( esc_html(get_option('AGSDCM_license_key')), -4 );
    ?>
		<div id="AGSDCM_license_key_box">
			<form method="post" action="<?php echo(esc_url(AGSDCM_PLUGIN_PAGE)); ?>" id="AGSDCM_license_key_form">
				<div id="AGSDCM_license_key_form_header">
					<a href="https://divi.space/" target="_blank">
						<img src="<?php echo(plugins_url('logo.png', __FILE__)); ?>" alt="<?php echo(AGSDCM_BRAND_NAME); ?>" />
					</a>
				</div>
				
				<div id="AGSDCM_license_key_form_body">
					<div id="AGSDCM_license_key_form_title">
						<h3><?php echo(esc_html(AGSDCM_ITEM_NAME)); ?>
                            <small>v<?php echo(AGS_DIVI_CAT_MODULES_VERSION); ?></small></h3>
					</div>
					
					<label>
						<span><?php _e('License Key:', 'ds-divi-extras'); ?></span>
                        <input type="text" readonly="readonly" value="<?php echo( esc_html( $display_license ) ); ?>" />
					</label>
					
					<?php
						if (defined('AGSDCM_DEACTIVATE_ERROR')) {
							echo('<p id="AGSDCM_license_key_form_error">'.AGSDCM_DEACTIVATE_ERROR.'</p>');
						}
						wp_nonce_field( 'AGSDCM_license_key_deactivate', 'AGSDCM_license_key_deactivate' );
						submit_button(esc_html__('Deactivate License Key', 'ds-divi-extras'));
					?>
				</div>
			</form>
		</div>
	<?php
}

function AGSDCM_sanitize_license( $new ) {
	if (defined('AGSDCM_LICENSE_KEY_VALIDATED')) {
		return $new;
	}
	$old = get_option( 'AGSDCM_license_key' );
	if( $old && $old != $new ) {
		delete_option( 'AGSDCM_license_status' ); // new license has been entered, so must reactivate
	}
	
	// Need to activate license here, only if submitted
	require_once(dirname(__FILE__).'/license-key-activation.php');
	AGSDCM_activate_license($new); // Always redirects
}