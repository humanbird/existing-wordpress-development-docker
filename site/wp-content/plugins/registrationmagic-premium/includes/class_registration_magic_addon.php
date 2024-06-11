<?php

class Registration_Magic_Addon {
    
    public function __construct() {
        add_filter('rm_addon_is_prov_login_active',
                   array($this,'is_prov_login_active')
                  );
        add_filter('rm_addon_login_notice',
                   array($this,'login_notice')
                  );
        $this->set_locale();
        $this->define_public_hooks();
        $this->define_admin_hooks();
    }
    
    public function define_admin_hooks(){
        $rm_admin_addon = new RM_Admin_Addon();
        add_action('rm_profile_tabs_content', array($rm_admin_addon, 'rm_profile_tabs_content_add'),10,2);
        add_filter('rm_profile_tabs', array($rm_admin_addon,'rm_profile_tabs_add'),10,1);
    }
    
    public function define_public_hooks() {
        $rm_addon_public = new RM_Public_Addon();
        add_action('wp_ajax_registrationmagic_embedform', array($rm_addon_public, 'render_embed'));
        add_action('wp_ajax_nopriv_registrationmagic_embedform', array($rm_addon_public, 'render_embed'));
    }
    
    public function is_prov_login_active($user_id) {
        return RM_Utilities::rm_is_prov_login_active($user_id);
    }
    
    public function login_notice($notice) {
        $gopts= new RM_Options();
        $login_err_msg= $gopts->get_value_of('login_error_message');
        $sub_page_id = $gopts->get_value_of('front_sub_page_id');   
        $sub_page_url= get_permalink($sub_page_id);
        if(empty($login_err_msg))
        {
            $login_err_msg= RM_UI_Strings::get('DEFAULT_LOGIN_ERR_MSG_VALUE');
        }
        $user_id= 0;
        if(isset( $_REQUEST['rm_user'])){
            $user_id= absint($_REQUEST['rm_user']);
        }
        $sub_page_url= add_query_arg( array(
                    'rm_user' => $user_id,
                    'resend' => '1'
               ), $sub_page_url );

        $sub_page_url = '<a href="'.$sub_page_url.'">'.__('Click Here','registrationmagic-addon').'</a>';
        $login_err_msg = str_replace('{{send verification email}}',$sub_page_url,$login_err_msg);
        $login_err_msg = str_replace('{{SEND_VERIFICATION_EMAIL}}',$sub_page_url,$login_err_msg);
        if (isset($_GET['is_disabled']) && $_GET['is_disabled'] === '1'){
            $user_auto_approval= $gopts->get_value_of('user_auto_approval'); 
            if($user_auto_approval=='verify'){
                $notice = '<div id="login_error"><strong>'.RM_UI_Strings::get('LABEL_ERROR').':</strong> ' . apply_filters('rm_login_notice', $login_err_msg) . '</div>';
            }else{
                 $notice = '<div id="login_error"><strong>'.RM_UI_Strings::get('LABEL_ERROR').':</strong> ' . apply_filters('rm_login_notice', RM_UI_Strings::get('INCATIVE_ACC_MSG')) . '</div>';
            }
        }elseif(isset($_GET['is_reset']) && $_GET['is_reset'] === '1')
            $notice = '<p id="rm_login_error" class="message">' . apply_filters('rm_login_notice', RM_UI_Strings::get('LOGIN_AGAIN_AFTER_RESET')) . '</p>';
        return $notice;
    }
    
    public function set_locale() {
        $rm_i18n_addon = new RM_i18n_Addon();
        add_action('plugins_loaded', array($rm_i18n_addon, 'load_plugin_textdomain'));
    }
    
}

$Registration_Magic_Addon = new Registration_Magic_Addon();