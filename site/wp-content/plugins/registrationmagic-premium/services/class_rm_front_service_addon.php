<?php

class RM_Front_Service_Addon {

    public function get_user_login_name($parent_service) {

        $user_email = null;

        if (is_user_logged_in()) {
            $user = wp_get_current_user();
            $user_email = isset($user->user_login) ? $user->user_login : null;
        }

        return $user_email;
    }
    
    //This function is used for ajaxresponse in magic popup login only.
    //
    public function login($username,$user_key,$parent_service,$remember = false){
        $credentials = array();
        $credentials['user_login'] = $username;
        $credentials['user_password'] = $user_key;
        $credentials['remember'] = $remember;
        $login_service= new RM_Login_Service();
        require_once(ABSPATH . 'wp-load.php');
        require_once(ABSPATH . 'wp-includes/pluggable.php');
        $user = $parent_service->is_user($credentials['user_login']);
        if ($user instanceof WP_user) {
            $prov_acc_act= RM_Utilities::rm_is_prov_login_active($user->ID);
            $is_disabled = (int) get_user_meta($user->ID, 'rm_user_status', true);
            if($is_disabled==1 && !empty($prov_acc_act)){
                $is_disabled= false;
            }
            if(empty($is_disabled)){
                $applicable= $login_service->two_fact_auth_applicable($user);
                if(empty($applicable)){
                    if(wp_check_password($credentials['user_password'],$user->user_pass,$user->ID)){
                        wp_set_auth_cookie($user->ID);
                        wp_set_current_user($user->ID);
                    } else {
                        $user = wp_signon($credentials, is_ssl());
                    }
                }
            } else {
                $user = wp_signon($credentials, is_ssl());
            }
        }
        if(!is_wp_error($user)){
            do_action('rm_user_signon',$user);
        } else {
            do_action('rm_user_signon_failure',$credentials);
        }
        $response = new stdClass();
        if(is_wp_error($user)){
            $response->error = true;
            $response->msg = RM_UI_Strings::get('LOGIN_ERROR');
        } else {
            $response->error = false;
            $response->msg = RM_UI_Strings::get('MSG_LOGIN_SUCCESS');
            $response->redirect = RM_Utilities::after_login_redirect($user);
            $response->reload = false;
            
            if(empty($response->redirect) || $response->redirect == "__current_url" || $response->redirect == get_permalink()) {
                $response->redirect = "";
                $response->reload = true;
            }
        }
        
        return json_encode($response);
    }
    
    public function get_users($request,$params,$parent_service){
        $options= array();
        // Check if it is ajax request
        if(isset($request->req['action']) && $request->req['action']=='rm_load_front_users'){ 
            if(isset($request->req['timerange'])){
                $options['timerange']= sanitize_text_field($request->req['timerange']);
            }
            
            if(isset($request->req['form_id'])){
                $options['form_id']= absint($request->req['form_id']);
            }
            
            $options['page_number']= isset($request->req['page_number']) ? absint($request->req['page_number']) : 1;

        }
       if(!empty($params['form_id'])){
           $options['form_id']= $params['form_id'];
       }
       
       if(!empty($params['timerange'])){
           $options['timerange']= $params['timerange'];
       }
         
        $repo= new RM_User_Repository(); 
        
        $users= $repo->get_users_for_front($options);
        $data= array();
        if(count($users)):
            foreach($users as $user):
                $tmp= new stdClass();
                $tmp->display_name= $user->display_name;
                $tmp->user_email= $user->user_email;
                $tmp->profile= get_avatar($user->ID);
                $data[]= $tmp;
            endforeach;
        endif;
        
        return $data;
     }
     
    public function get_email_count($user_email, $parent_service) {        
        $email_repo = new RM_Email_Repository;        
        
        $types = array(RM_EMAIL_GENERIC, RM_EMAIL_AUTORESP, RM_EMAIL_BATCH, RM_EMAIL_USER_ACTIVATED_USER, RM_EMAIL_NOTE_MSG, RM_EMAIL_NOTE_ADDED);
        
        return $email_repo->get_email_count($user_email, $types);
    }
    
    public function get_email_unread_count($user_email, $parent_service) {        
        $email_repo = new RM_Email_Repository;        
        
        $types = array(RM_EMAIL_GENERIC, RM_EMAIL_AUTORESP, RM_EMAIL_BATCH, RM_EMAIL_USER_ACTIVATED_USER, RM_EMAIL_NOTE_MSG, RM_EMAIL_NOTE_ADDED);
        
        return $email_repo->get_email_unread_count($user_email, $types);
    }
    
    public function get_emails_by_resp($resp_email, $limit, $offset, $parent_service) {
        $email_repo = new RM_Email_Repository;       
        
        $types = array(RM_EMAIL_GENERIC, RM_EMAIL_AUTORESP, RM_EMAIL_BATCH, RM_EMAIL_USER_ACTIVATED_USER, RM_EMAIL_NOTE_MSG, RM_EMAIL_NOTE_ADDED);
        $types = apply_filters('rm_front_get_emails_by_resp_types', $types);
        return $email_repo->get_emails($resp_email, $limit, $offset, $types);        
    }
    
    public function mark_email_read($mail_id,$parent_service)
    {
        //Check user is logged in (via OTP or WP-login)
        $user_email = $parent_service->get_user_email();
        
        if($user_email)
        {
            $email = RM_DBManager::get('SENT_EMAILS', array('mail_id' => $mail_id), array('%d'));
            if(is_array($email))
            {
                //The email should belong to the logged in user.
                if(trim((string)$email[0]->to) == trim((string)$user_email)) {                    
                    $email_repo = new RM_Email_Repository;     
                    $email_repo->mark_email_read($mail_id);
                }
            }
        }
    }
}