<?php
/**
 *
 * @author CMSHelplive
 */
class RM_Login_Manage_Controller_Addon {
    
    public function two_factor_auth($model, $service, $request, $params, $parent_controller){
        $data= new stdClass();
        if($parent_controller->mv_handler->validateForm("login-auth-options")) {
            $params= array();
            $params['otp_type']= sanitize_text_field($request->req['otp_type']);
            $params['en_two_fa']= isset($request->req['en_two_fa']) ? absint($request->req['en_two_fa']) : 0;
            $params['otp_length']= absint($request->req['otp_length']);
            $params['otp_expiry_action']= sanitize_text_field($request->req['otp_expiry_action']);
            $params['otp_expiry']= absint($request->req['otp_expiry']);
            $params['otp_regen_success_msg']= sanitize_text_field($request->req['otp_regen_success_msg']);
            $params['otp_regen_text']= sanitize_text_field($request->req['otp_regen_text']);
            $params['otp_exp_msg']= sanitize_text_field($request->req['otp_exp_msg']);
            $params['otp_exp_restart_msg']= sanitize_text_field($request->req['otp_exp_restart_msg']);
            $params['otp_field_label']= sanitize_text_field($request->req['otp_field_label']);    
            $params['msg_above_otp']= sanitize_text_field($request->req['msg_above_otp']);    
            $params['en_resend_otp']= isset($request->req['en_resend_otp']) ? absint($request->req['en_resend_otp']) : 0;    
            $params['otp_resend_text']= sanitize_text_field($request->req['otp_resend_text']);
            $params['otp_resent_msg']= sanitize_text_field($request->req['otp_resent_msg']);
            $params['otp_resend_limit']= sanitize_text_field($request->req['otp_resend_limit']);
            $params['allowed_incorrect_attempts']  = absint($request->req['allowed_incorrect_attempts']); 
            $params['invalid_otp_error'] = sanitize_text_field($request->req['invalid_otp_error']);
            $params['apply_on'] = sanitize_text_field($request->req['apply_on']);
            $params['disable_two_fa_for_admin']= isset($request->req['disable_two_fa_for_admin']) ? absint($request->req['disable_two_fa_for_admin']) : 0;
            $params['enable_two_fa_for_roles']= isset($request->req['enable_two_fa_for_roles']) ? $request->req['enable_two_fa_for_roles'] : array();
            $service->update_auth_options($params);
            RM_Utilities::redirect(admin_url('/admin.php?page=rm_login_sett_manage'));
        }
        
        $data->params= $service->get_auth_options();
        $user_service= new RM_User_Services();
        $data->roles= $user_service->get_user_roles();
        $view = $parent_controller->mv_handler->setView("login_two_factor_auth");
        $view->render($data);
    }
   
}