<?php

class RM_Options_Addon
{
    
    public function update_options_name_and_methods($options_name_and_methods)
    {
        $addon_options_name_and_methods = array(
            'include_stripe'=>'sanitize_checkbox',
            'dropbox_notice' => null,
            'wepay_notice' => null,
            'stripe_notice' => null,
            'mailpoet_notice' => null,
            'aw_consumer_key' => null,
            'aw_consumer_secret' => null,
            'aw_access_key' => null,
            'aw_oauth_id'=>null,
            'aw_access_secret' => null,
            'tw_consumer_key' => null,
            'tw_consumer_secret' => null,
            'instagram_client_id' => null,
            'enable_aweber' => 'sanitize_checkbox',
            'enable_gplus' => 'sanitize_checkbox',
            'enable_linked' => 'sanitize_checkbox',
            'enable_window_login' => 'sanitize_checkbox',
            'enable_twitter_login' => 'sanitize_checkbox',
            'enable_instagram_login' => 'sanitize_checkbox',
            'sub_pdf_header_text' => null,
            'sub_pdf_header_img' => null,
            'submission_pdf_font'=>null,
            'user_role_custom_data' =>null,
            'fab_links' =>null,
            'show_tabs' =>null,
            'rm_submission_filters' =>null,
            'admin_notification_includes_pdf' => 'sanitize_checkbox',
            'acc_act_link_expiry'=>'',
            'acc_act_notice'=>null,
            'acc_invalid_act_code'=>null,
            'acc_act_link_exp_notice'=>null,
            'login_error_message'=>null,
            'prov_act_acc'=>'sanitize_checkbox',
            'prov_acc_act_criteria'=>null
        );
        
        return array_merge($options_name_and_methods, $addon_options_name_and_methods);
    }
    
}