<?php

/**
 * The admin-specific functionality of the plugin.
 *
 * @link       http://registration_magic.com
 * @since      1.0.0
 *
 * @package    Registraion_Magic
 * @subpackage Registraion_Magic/admin
 */

/**
 * The admin-specific functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the admin-specific stylesheet and JavaScript.
 *
 * @package    Registraion_Magic
 * @subpackage Registraion_Magic/admin
 * @author     CMSHelplive
 */
class RM_Admin_Addon {

    /**
     * Registers menu pages and submenu pages at the admin area.
     *
     * @since    1.0.0
     */
    public function add_menu($parent_admin) {
        if(class_exists('AAM')){
            add_submenu_page("rm_form_manage", RM_UI_Strings::get('ADMIN_MENU_CSTATUS'), RM_UI_Strings::get('ADMIN_MENU_CSTATUS'), "manage_options", "rm_form_manage_cstatus", array($parent_admin->get_controller(), 'run'), 3);
            add_submenu_page("rm_dummy_string", __('Add Custom Status', 'registrationmagic-addon'), __('Add Custom Status', 'registrationmagic-addon'), "manage_options", "rm_form_add_cstatus", array($parent_admin->get_controller(), 'run'));
            add_submenu_page("rm_dummy_string", __('Add Custom Tabs', 'registrationmagic-addon'), __('Add Custom Tabs', 'registrationmagic-addon'), "manage_options", "rm_options_add_ctabs", array($parent_admin->get_controller(), 'run')); 
            add_submenu_page("rm_dummy_string", __('Attachments Reports', 'registrationmagic-addon'), __('Attachments Reports', 'registrationmagic-addon'), "manage_options", "rm_reports_attachments", array($parent_admin->get_controller(), 'run'));
            add_submenu_page("rm_dummy_string", __('Payments Reports', 'registrationmagic-addon'), __('Payments Reports', 'registrationmagic-addon'), "manage_options", "rm_reports_payments", array($parent_admin->get_controller(), 'run'));
            add_submenu_page("rm_dummy_string", __('Compare Form Reports', 'registrationmagic-addon'), __('Compare Form Reports', 'registrationmagic-addon'), "manage_options", "rm_reports_form_compare", array($parent_admin->get_controller(), 'run'));
            add_submenu_page("rm_dummy_string", __('Notifications Reports', 'registrationmagic-addon'), __('Notifications Reports', 'registrationmagic-addon'), "manage_options", "rm_reports_notifications", array($parent_admin->get_controller(), 'run'));
            add_submenu_page("rm_dummy_string", __('Add Notifications Reports', 'registrationmagic-addon'), __('Notifications Reports', 'registrationmagic-addon'), "manage_options", "rm_reports_notification_add", array($parent_admin->get_controller(), 'run'));   
        }
        
        /*
        $pg_ext = false;
        $ep_ext = false;
        if(function_exists('event_magic_instance')) {
            $em = event_magic_instance();
            $em_paid_exts = array_diff($em->extensions, array('analytics','file_import_export_events'));
            if(empty($em_paid_exts)) {
                $ep_ext = true;
            }
        }
        if(class_exists('PM_request') && method_exists('PM_request','pg_get_activate_extensions')) {
            $pmrequest = new PM_request();
            $activated_extensions = $pmrequest->pg_get_activate_extensions();
            if(empty($activated_extensions['paid'])) {
                $pg_ext = true;
            }
        }
        if($pg_ext || $ep_ext) {
            add_submenu_page("rm_form_manage", RM_UI_Strings::get('ADMIN_MENU_METABUNDLE'), RM_UI_Strings::get('ADMIN_MENU_METABUNDLE'), "manage_options", "rm_form_metabundle", array($parent_admin->get_controller(), 'run'), 13);
        }
        */
    }
    
    public function post_feedback(){
        $msg= isset($_POST['msg']) ? $_POST['msg'] : '';
        $feedback= $_POST['feedback'];
        $body= '';
        switch($feedback)
        {
            case 'feature_not_available': $body='Feature not available: '; break;
            case 'feature_not_working': $body='Feature not working: '; break;
            case 'found_a_better_plugin': $body='Found a better plugin: '; break;
            case 'plugin_broke_site': $body='Plugin broke my site.'; break;
            case 'plugin_stopped_working': $body='Plugin stopped working'; break;
            case 'temporary_deactivation': return;
            case 'upgrade':  $body='Upgrading to premium '; break;   
            case 'other': $body='Other: '; break;
            default: return;
        }
        if(trim((string)$feedback)!=''){
            $body .= '<p>'.$msg.'</p>';
            $body .= '<p>RegistrationMagic Premium - version '.RM_PLUGIN_VERSION.'</p>';
            RM_Utilities::quick_email('feedback@registrationmagic.com', 'Uninstallation Feedback', $body,RM_EMAIL_GENERIC,array('do_not_save'=>true));
        }
        wp_die();
    }
    
    public function disable_notice(){
        
        if(isset($_REQUEST['disable_dpx'])){
            $dpx_options= new RM_Dpx_Options();
            $dpx_options->set_value_of('dpx_notice_shown',1);
        }
       
        wp_die();
    }
    
    public function custom_status_update(){
        $sub_id= absint($_REQUEST['submission_id']);
        $submission= new RM_Submissions();
        $submission->load_from_db($sub_id);
        
        if(isset($_REQUEST['action_type'])){
            $user_model= new RM_User;
            $service = new RM_Services();
            if($_REQUEST['action_type']=='append'){
                $form= new RM_Forms();
                $form->load_from_db($_REQUEST['form_id']);
                $form_options= $form->get_form_options();
                $status_data = $form_options->custom_status[$_REQUEST['status_index']];
                if(isset($status_data['cs_action_status_en']) && $status_data['cs_action_status_en']==1){
                    if($status_data['cs_action_status']=='clear_all'){
                        $service->update_custom_statuses($_REQUEST['status_index'],$_REQUEST['submission_id'],$_REQUEST['form_id'],'clear_all');
                    }else if($status_data['cs_action_status']=='clear_specific'){
                        $service->update_custom_statuses($_REQUEST['status_index'],$_REQUEST['submission_id'],$_REQUEST['form_id'],'clear_specific',$status_data['cs_act_status_specific']);
                    }
                }
                if(isset($status_data['cs_email_user_en']) && $status_data['cs_email_user_en']==1){
                    if($status_data['cs_email_user_body']!=''){
                        $admin_email = get_option('admin_email');
                        $rm_email= new RM_Email();
                        $body= str_replace(array('{{SUB_ID}}','{{UNIQUE_TOKEN}}'), array($sub_id,$submission->get_unique_token()), $status_data['cs_email_user_body']);
                        $rm_email->message($body);
                        $rm_email->subject(trim((string)$status_data['cs_email_user_subject'])!=''?$status_data['cs_email_user_subject']:RM_UI_Strings::get('LABEL_USER_SUBJECT'));
                        $rm_email->from($admin_email);
                        $rm_email->to($_REQUEST['user_email']);
                        $rm_email->send();
                    }
                }
                if(isset($status_data['cs_email_admin_en']) && $status_data['cs_email_admin_en']==1){
                    if($status_data['cs_email_admin_body']!=''){
                        $admin_email = get_option('admin_email');
                        $rm_email= new RM_Email();
                        $body= str_replace(array('{{SUB_ID}}','{{UNIQUE_TOKEN}}'), array($sub_id,$submission->get_unique_token()), $status_data['cs_email_admin_body']);
                        $rm_email->message($body);
                        $rm_email->subject(trim((string)$status_data['cs_email_admin_subject'])!=''?$status_data['cs_email_admin_subject']:RM_UI_Strings::get('LABEL_ADMIN_SUBJECT'));
                        $rm_email->from($admin_email);
                        $rm_email->to($admin_email);
                        $rm_email->send();
                    }
                }
                if(isset($status_data['cs_action_user_act_en']) && $status_data['cs_action_user_act_en']==1){
                    if($status_data['cs_action_user_act']=='create_account'){
                        $user = get_user_by( 'email', $_REQUEST['user_email'] );
                        if(empty($user)){
                            $admin_email = get_option('admin_email');
                            if($user->data->user_email!=$admin_email){
                                $random_password = wp_generate_password( $length=12, $include_standard_special_chars=false );
                                //wp_create_user($_REQUEST['user_email'],$random_password,$_REQUEST['user_email']);
                                $front_service = new RM_Front_Form_Service(); 
                                $registered_user_id= $front_service->register_user_on_custom_status($_REQUEST['user_email'], $_REQUEST['user_email'], $random_password, $_REQUEST['form_id'], false, $form_options->user_auto_approval);
                                if(!empty($registered_user_id)){
                                    update_user_meta($registered_user_id, 'RM_UMETA_FORM_ID', $_REQUEST['form_id']);
                                    update_user_meta($registered_user_id, 'RM_UMETA_SUB_ID', $_REQUEST['submission_id']);
                                    update_user_meta($registered_user_id, 'rm_activation_time', current_time('mysql'));
                                    $opt=new RM_Options;
                                    
                                    if($form_options->user_auto_approval=='default')
                                    {  
                                       do_action('rm_user_registered',$registered_user_id);
                                       $check_setting=$opt->get_value_of('user_auto_approval');
                                       if($check_setting=="verify"){
                                            $front_service->get_user_service()->deactivate_user_by_id($registered_user_id);
                                            wp_die();
                                       }
                                    }
                                    else
                                    {
                                         $check_setting=$form_options->user_auto_approval;
                                    }

                                    if ($check_setting == 'yes' || $check_setting=='verify')
                                    {   
                                        $registered_user_id = $front_service->get_user_service()->activate_user_by_id($registered_user_id);
                                        
                                    }

                                }
                                 
                            }
                        }
                    }else if($status_data['cs_action_user_act']=='deactivate_user'){
                        $user = get_user_by( 'email', $_REQUEST['user_email'] );
                        if(!empty($user)){
                            $admin_email = get_option('admin_email');
                            if($user->data->user_email!=$admin_email){
                                $user_model->deactivate_user($user->ID);
                            }
                        }
                    }else if($status_data['cs_action_user_act']=='activate_user'){
                        $user = get_user_by( 'email', $_REQUEST['user_email'] );
                        if(!empty($user)){
                            //$admin_email = get_option('admin_email');
                            //if($user->data->user_email!=$admin_email){
                                $user_model->activate_user($user->ID);
                            //}
                        }
                    }else if($status_data['cs_action_user_act']=='delete_user'){
                        $user = get_user_by( 'email', $_REQUEST['user_email'] );
                        if(!empty($user)){
                            $admin_email = get_option('admin_email');
                            if($user->data->user_email!=$admin_email){
                                if ( is_multisite() ) 
                                { wpmu_delete_user($user->ID); }
                                else{
                                wp_delete_user( $user->ID);}
                                
                            }
                        }
                    }
                }
                if(isset($status_data['cs_note_en']) && $status_data['cs_note_en']==1){
                    if($status_data['cs_note_text']!=''){
                        $rm_notes= new RM_Notes();
                        $rm_notes->set_initialized(true);
                        $rm_notes->set_submission_id($_REQUEST['submission_id']);
                        $rm_notes->set_notes($status_data['cs_note_text']);
                        $status = $status_data['cs_note_public']==1?'publish':'dtaft';
                        $rm_notes->set_status($status);
                        $rm_notes->set_publication_date(RM_Utilities::get_current_time());
                        $rm_notes->set_published_by(get_current_user_id());
                        
                        $note_options = new stdClass;
                        $note_options->bg_color = 'FFFFFF';
                        $note_options->type = 'note';
                        $rm_notes->set_note_options($note_options);
                        $rm_notes->insert_into_db();
                        
                        if($status_data['cs_note_public']==1){
                            $rm_note_service= new RM_Note_Service();
                            $rm_note_service->notify_users($rm_notes);
                        }
                    }
                }
                if(isset($status_data['cs_blacklist_en']) && $status_data['cs_blacklist_en']==1){
                    $submission_model= new RM_Submissions();
                    $submission_model->load_from_db(absint($_REQUEST['submission_id']));
                    if($status_data['cs_block_email']==1){
                        $submission_model->block_email($_REQUEST['user_email']);
                    }
                    if($status_data['cs_unblock_email']==1){
                        $submission_model->unblock_email($_REQUEST['user_email']);
                    }
                    if($status_data['cs_block_ip']==1){ 
                        $sub_ip = $submission_model->get_submission_ip();
                        if($sub_ip!=''){
                            $submission_model->block_ip($sub_ip);
                        }
                    }
                    if($status_data['cs_unblock_ip']==1){
                        $sub_ip = $submission_model->get_submission_ip();
                        if($sub_ip!=''){
                            $submission_model->unblock_ip($sub_ip);
                        }
                    }
                }
                //echo '<pre>';print_r($status_data);echo '</pre>';die;
            }
            //echo '<pre>';print_r($_REQUEST);echo '</pre>';die;
            echo $service->update_custom_statuses($_REQUEST['status_index'],$_REQUEST['submission_id'],$_REQUEST['form_id'],$_REQUEST['action_type']);
        }
        wp_die();
    }
    
    
    public function admin_notices(){
        /* Showing noticed for WooCommerce and EDD integration */
        $g_opts= new RM_Options();
        if(!empty($_GET['rm_disable_edd_notice'])){
            $g_opts->set_value_of('edd_notice', 0);
        }
        if(!empty($_GET['rm_disable_wc_notice'])){
            $g_opts->set_value_of('wc_notice', 0);
        }
        if(!empty($_GET['rm_disable_php_notice'])){
            $g_opts->set_value_of('php_notice', 0);
        }
        if(!empty($_GET['rm_disable_php_8_notice'])){
            $g_opts->set_value_of('php_8_notice', 0);
        }
        if(!empty($_GET['rm_disable_dropbox_notice'])){
            $g_opts->set_value_of('dropbox_notice', 0);
        }
        if(!empty($_GET['rm_disable_wepay_notice'])){
            $g_opts->set_value_of('wepay_notice', 0);
        }
        if(!empty($_GET['rm_disable_stripe_notice'])){
            $g_opts->set_value_of('stripe_notice', 0);
        }
        if(!empty($_GET['rm_disable_mailpoet_notice'])){
            $g_opts->set_value_of('mailpoet_notice', 0);
        }

        $edd_notice= $g_opts->get_value_of('edd_notice');
        $wc_notice= $g_opts->get_value_of('wc_notice');
        $php_notice= $g_opts->get_value_of('php_notice');
        $php_8_notice= $g_opts->get_value_of('php_8_notice');
        $dropbox_notice= $g_opts->get_value_of('dropbox_notice');
        $wepay_notice= $g_opts->get_value_of('wepay_notice');
        $stripe_notice= $g_opts->get_value_of('stripe_notice');
        $mailpoet_notice= $g_opts->get_value_of('mailpoet_notice');
        $query_string= $_SERVER['QUERY_STRING'];
        if(empty($query_string)){
            $query_string= '?';
        }
        else
        {
            $query_string= '?'.$query_string.'&';
        }

        ?>
        <?php if($php_notice!=0): ?>
            <?php if(version_compare(PHP_VERSION, '5.6.0', '<')): ?>
            <div class="rm_admin_notice rm-notice-banner notice notice-success is-dismissible">
                <p><?php printf(__( 'It seems you are using now obsolete version of PHP. Please note that RegistrationMagic works best with PHP 5.6 or later versions. You may want to upgrade to avoid any potential issues. This is one time warning check and message may not display again once dismissed.','registrationmagic-addon')); ?><a class="rm_dismiss" href="<?php echo $query_string.'rm_disable_php_notice=1' ?>"><img src="<?php echo RM_IMG_URL. '/close-rm.png'; ?>"></a></p>
            </div>
            <?php endif; ?>
        <?php endif; ?>
        <?php /* if($php_8_notice != 0 && isset($_GET['page']) && $_GET['page'] == 'rm_form_manage'):
            if(version_compare(PHP_VERSION, '8.0.0', '>=')): ?>
            <div id="rm-php-notice-warning" class="rm_admin_notice rm-notice-banner notice notice-warning is-dismissible">
                <p><?php _e('You are using PHP 8. RegistrationMagic currently does not supports PHP 8 and you might see some unwanted errors or warnings. We are working on PHP 8 compatibility update and it will be available very soon.','registrationmagic-addon'); ?> <a class="rm_dismiss" href="<?php echo esc_url($query_string).'rm_disable_php_8_notice=1' ?>"><?php _e('Dismiss','registrationmagic-addon'); ?></a></p>
            </div>
            <?php endif;
        endif; */ ?>
        <?php if($edd_notice!=0 &&  class_exists( 'Easy_Digital_Downloads')): ?>
            <div class="rm_admin_notice rm-notice-banner notice notice-success is-dismissible">
                <p><?php printf(__( 'Using RegistrationMagic with Easy Digital Downloads? <a target="__blank" href="%s">Learn how to</a> build intelligent contact forms using RegistrationMagic which display user EDD Order History and Customer details with the form submission, helping you answer support requests faster and better.','registrationmagic-addon'),'https://registrationmagic.com/create-super-intelligent-forms-wordpress/'); ?><a class="rm_dismiss" href="<?php echo $query_string.'rm_disable_edd_notice=1' ?>"><img src="<?php echo RM_IMG_URL. '/close-rm.png'; ?>"></a></p>
            </div>
        <?php endif; ?>

        <?php if($wc_notice!=0 && class_exists( 'WooCommerce' )): ?>
            <div class="rm_admin_notice rm-notice-banner notice notice-success is-dismissible">
                <p><?php printf(__( 'Using RegistrationMagic with WooCommerce? <a target="__blank" href="%s">Learn how to</a> build intelligent contact forms using RegistrationMagic which display user WooCommerce Order history with the form submission, helping you answer support requests faster and better.','registrationmagic-addon'),'https://registrationmagic.com/create-super-intelligent-forms-wordpress/'); ?> <a class="rm_dismiss" href="<?php echo $query_string.'rm_disable_wc_notice=1' ?>"><?php _e('Dismiss','registrationmagic-addon'); ?></a></p>
            </div>
        <?php endif; ?>
        <?php if($dropbox_notice!=0): ?>
            <?php if(version_compare(PHP_VERSION, '5.6.4', '<')): ?>
            <div class="rm_admin_notice rm-notice-banner notice notice-success is-dismissible">
                <p><?php printf(__( 'Dropbox API is available on PHP 5.6.4 or later versions. It looks like your site is using older PHP version and will need to be upgraded for Dropbox integration access.','registrationmagic-addon')); ?><a class="rm_dismiss" href="<?php echo $query_string.'rm_disable_dropbox_notice=1' ?>"><img src="<?php echo RM_IMG_URL. '/close-rm.png'; ?>"></a></p>
            </div>
            <?php endif; ?>
        <?php endif; ?>
        <?php if($wepay_notice!=0): ?>
            <?php if(version_compare(PHP_VERSION, '5.6.0', '<')): ?>
            <div class="rm_admin_notice rm-notice-banner notice notice-success is-dismissible">
                <p><?php printf(__( 'WePay API is available on PHP 5.6 or later versions. It looks like your site is using older PHP version and will need to be upgraded for WePay integration access.','registrationmagic-addon')); ?><a class="rm_dismiss" href="<?php echo $query_string.'rm_disable_wepay_notice=1' ?>"><img src="<?php echo RM_IMG_URL. '/close-rm.png'; ?>"></a></p>
            </div>
            <?php endif; ?>
        <?php endif; ?>
        <?php if($stripe_notice!=0): ?>
            <?php if(version_compare(PHP_VERSION, '5.6.0', '<')): ?>
            <div class="rm_admin_notice rm-notice-banner notice notice-success is-dismissible">
                <p><?php printf(__( 'Stripe API is available on PHP 5.6 or later versions. It looks like your site is using older PHP version and will need to be upgraded for Stripe integration access.','registrationmagic-addon')); ?><a class="rm_dismiss" href="<?php echo $query_string.'rm_disable_stripe_notice=1' ?>"><img src="<?php echo RM_IMG_URL. '/close-rm.png'; ?>"></a></p>
            </div>
            <?php endif; ?>
        <?php endif; ?>
        <?php if($mailpoet_notice!=0): ?>
            <?php if(version_compare(PHP_VERSION, '5.6.0', '<')): ?>
            <div class="rm_admin_notice rm-notice-banner notice notice-success is-dismissible">
                <p><?php printf(__( 'Mailpoet API is available on PHP 5.6 or later versions. It looks like your site is using older PHP version and will need to be upgraded for Mailpoet integration access.','registrationmagic-addon')); ?><a class="rm_dismiss" href="<?php echo $query_string.'rm_disable_mailpoet_notice=1' ?>"><img src="<?php echo RM_IMG_URL. '/close-rm.png'; ?>"></a></p>
            </div>
            <?php endif; ?>
        <?php endif; ?>
       <?php   
            
    }
    public function rm_profile_tabs_add($tabs){
        
        $tabs['rm_inbox_tab'] = array('label'=>RM_UI_Strings::get('LABEL_INBOX'),'icon'=>'mail','id'=>'rm_inbox_tab','class'=>'rmtab-inbox','status'=> 1);
        return $tabs;
    }

    public function rm_profile_tabs_content_add($data, $uid){
        if(defined('REGMAGIC_ADDON')){
            include RM_ADDON_PUBLIC_DIR.'views/template_rm_front_inbox.php';
        }
    } 
}