<?php

/**
 * Form model for the plugin
 * 
 * @link       http://registration_magic.com
 * @since      1.0.0
 *
 * @package    Registraion_Magic
 * @subpackage Registraion_Magic/admin/models
 */

/**
 * Class which conatains all the model operations of form object
 *
 * class conatains various data related operation for the forms.
 * validation methods for form also included.
 * 
 * @author cmshelplive
 */
class RM_Forms_Addon
{

    public $valid_options = array('hide_username','form_is_opt_in_checkbox','mailchimp_relations', 'form_opt_in_text', 'form_should_user_pick', 'form_is_unique_token', 'form_description', 'form_user_field_label', 'form_custom_text', 'form_success_message', 'form_email_subject', 'form_email_content', 'form_submit_btn_label', 'form_submit_btn_color', 'form_submit_btn_bck_color', 'form_expired_by', 'form_submissions_limit', 'form_expiry_date', 'form_message_after_expiry', 'mailchimp_list', 'mailchimp_mapped_email', 'mailchimp_mapped_first_name', 'mailchimp_mapped_last_name', 'should_export_submissions', 'export_submissions_to_url', 'form_pages', 'access_control', 'style_btnfield', 'style_form', 'style_textfield', 'auto_login','cc_relations','cc_list','form_opt_in_text_cc','form_is_opt_in_checkbox_cc','aw_relations','aw_list','form_opt_in_text_aw','form_is_opt_in_checkbox_aw','enable_captcha','enable_mailchimp','enable_aweber','display_progress_bar','sub_limit_antispam','placeholder_css','btn_hover_color','field_bg_focus_color','text_focus_color','style_section','style_label', 'post_expiry_action', 'post_expiry_form_id', 'no_prev_button','user_auto_approval','form_opt_in_default_state','form_opt_in_default_state_cc','form_opt_in_default_state_aw', 'ordered_form_pages','show_total_price','form_nu_notification','form_user_activated_notification','form_activate_user_notification','form_admin_ns_notification','admin_notification','admin_email','enable_dpx','enable_mailpoet','mailpoet_form','mailpoet_field_mappings','form_is_opt_in_checkbox_mp','form_opt_in_text_mp','form_opt_in_default_state_mp','enable_newsletter','newsletter_list_id','form_is_opt_in_checkbox_nl','form_opt_in_text_nl','form_opt_in_default_state_nl','newsletter_field_mappings','sub_limit_ind_user','form_next_btn_label','form_prev_btn_label','form_btn_align','act_link_message','form_nu_notification_sub','act_link_sub','form_user_activated_notification_sub','form_activate_user_notification_sub','form_admin_ns_notification_sub','custom_status','form_limit_by_cs','cs_action_user_act','cs_action_user_act_en');
    
    public function get_form_pages($parent_model) {
        $pages = new stdClass;
        if (!$parent_model->form_options->form_pages) {
            $pages->count = 1;
            $pages->data = array('Page 1');
            $pages->order = array(0);
        } else {
            $pages->count = count($parent_model->form_options->form_pages);
            $pages->data = $parent_model->form_options->form_pages;
            if (!$parent_model->form_options->ordered_form_pages)
            {
                $pages->order = array_keys($form_pages);
            }
            else 
                $pages->order = $parent_model->form_options->ordered_form_pages;
        }
        
        return $pages;
    }
    
    public function set_sub_limit_ind_user($value,$parent_model){
        $parent_model->form_options->sub_limit_ind_user = $value;
    }
    
    public function set_hide_username($value,$parent_model){
        $parent_model->form_options->hide_username = $value;
    }

    /****validations*****/

    public function validate_access_control($form_builder_id,$parent_model)
    {
        $valid = true;
        $access_control = $parent_model->form_options->access_control;
        
        if (isset($access_control->date))
        {
            if (!isset($access_control->date->type))
            {
                RM_PFBC_Form::setError($form_builder_id, RM_UI_Strings::get('MSG_INVALID_ACTRL_DATE_TYPE'));
                $valid = false;
            }
            if (!isset($access_control->date->upperlimit) && !isset($access_control->date->lowerlimit))
            {
                RM_PFBC_Form::setError($form_builder_id, RM_UI_Strings::get('MSG_INVALID_ACTRL_DATE_LIMIT'));
                $valid = false;
            }
            if (isset($access_control->date->upperlimit) && $access_control->date->upperlimit == '' &&
                isset($access_control->date->lowerlimit) && $access_control->date->lowerlimit == '')
            {
                RM_PFBC_Form::setError($form_builder_id, RM_UI_Strings::get('MSG_INVALID_ACTRL_DATE_LIMIT'));
                $valid = false;
            }
        }
        
        if (isset($access_control->passphrase))
        {
            if (!isset($access_control->passphrase->passphrase) || trim((string)$access_control->passphrase->passphrase) == '')
            {
                RM_PFBC_Form::setError($form_builder_id, RM_UI_Strings::get('MSG_INVALID_ACTRL_PASS_PASS'));
                $valid = false;
            }
        }
        
        if (isset($access_control->roles))
        {
            if(!is_array($access_control->roles) || $access_control->roles == '' || count($access_control->roles) < 1)
            {
                RM_PFBC_Form::setError($form_builder_id, RM_UI_Strings::get('MSG_INVALID_ACTRL_ROLES'));
                $valid = false;
            }
        }
        
        return $valid;
    }

    public function validate_accounts($form_builder_id,$parent_model)
    {
        $valid = true;
        if (isset($parent_model->form_type) && $parent_model->form_type == "1")
        {
            if ($parent_model->default_form_user_role == "" && empty($parent_model->form_options->form_should_user_pick) == 0)
            {
                RM_PFBC_Form::setError($form_builder_id, RM_UI_Strings::get('MSG_AUTO_USER_ROLE_INVALID'));
                $valid = false;
            }

            if (isset($parent_model->form_options->form_should_user_pick) && count($parent_model->form_options->form_should_user_pick) > 0)
            {
                if ($parent_model->form_options->form_user_field_label == "")
                {
                    RM_PFBC_Form::setError($form_builder_id, RM_UI_Strings::get('MSG_WP_ROLE_LABEL_INVALID'));
                    $valid = false;
                }

                if (count($parent_model->get_form_user_role()) == 0)
                {
                    RM_PFBC_Form::setError($form_builder_id, RM_UI_Strings::get('MSG_ALLOWED_ROLES_INVALID'));
                    $valid = false;
                }
            }
        }
        return $valid;
    }
    
}
