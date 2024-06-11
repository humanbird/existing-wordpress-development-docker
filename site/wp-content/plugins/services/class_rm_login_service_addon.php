<?php

class RM_Login_Service_Addon {
    
    public function get_auth_options($parent_service){
        $options= RM_DBManager::query_login_form('auth');
        if(isset($options[0]))
        {
            $options=  json_decode($options[0]->value,true);
            if(empty($options))
                return array();
            return $options;
        }
        return array();
    }
    
    public function update_auth_options($data,$parent_service){
        $data= json_encode($data);
        RM_DBManager::update_login_form_options('auth',$data);
    }
    
    public function send_2fa_otp($user,$parent_service){
        if(empty($user))
            return false;
        $auth_options= $parent_service->get_auth_options();
        $otp= '';
        $otp_length= empty($auth_options['otp_length']) ? 4 : $auth_options['otp_length'];
        if($auth_options['otp_type']=='alpha_numeric'){
            $otp= wp_generate_password($otp_length,false);
        }
        else {
            $otp= RM_Utilities::random_number($otp_length);
        }
        $template_options= $parent_service->get_template_options();
        $email_service= new RM_Email_Service();

        
        $message= str_replace(array('{{site_name}}','{{OTP_expiry}}','{{OTP}}'),array(get_bloginfo('name'),$auth_options['otp_expiry'],$otp),$template_options['otp_message']);
        $front_users= new RM_Front_Users('f');
        $config= array('otp_code'=>$otp,'email'=>$user->user_email);
        if(!empty($auth_options['otp_expiry'])){
            $expiry= strtotime(RM_Utilities::get_current_time()) + $auth_options['otp_expiry']*60;
            $expiry= RM_Utilities::get_current_time($expiry);
            $config['expiry']= $expiry;
        }
        $front_users->set($config);
        $front_users->insert_into_db();
        $email_service->send_2fa_otp(array('username'=>$user->user_login,'message'=>$message));
    }
    
    public function two_fact_auth_applicable($user,$parent_service){
        $auth_options= $parent_service->get_auth_options();
        if(empty($auth_options['en_two_fa']))
            return false;
        if($user){
            $user_data= get_userdata($user->ID);
            if($auth_options['apply_on']=='all'){
                if($auth_options['disable_two_fa_for_admin']=='1'){
                    $user_roles= $user_data->roles;
                    if(in_array('administrator',$user_roles)){
                        return false;
                    }
                }
                return true;
            }
            else{
                $roles= $auth_options['enable_two_fa_for_roles'];
                if(empty($roles))
                    return false;
                $result=array_intersect($roles,$user_data->roles);
                if(!empty($result))
                    return true;
            }
        }
        return false;
    }
   
   public function ban_ip($args,$parent_service){
       $ip= $_SERVER['REMOTE_ADDR'];
       $results= RM_DBManager::get('LOGIN_LOG', array('ip'=>$ip), array('%s'), 'results', 0, 1, '*','id',true);
       if(empty($results))
           return;
       $row = (array) $results[0];   
       if($row['ban']==1){
           return;
       }
       $row['ban']= 1;
       $v_options= $parent_service->get_validations();
       if($v_options['ban_type']=='temp'){
           $row['ban_til']= RM_Utilities::get_current_time(time() + $v_options['ban_duration'] * 60); 
       }
       
       $rm_submissions= new RM_Submissions();
       $rm_submissions->block_ip($ip);
       RM_DBManager::update_login_log($row);
      
       if(!empty($v_options['notify_admin_on_ban'])){
           RM_Email_Service::notify_admin_on_ip_ban(array('ban_period'=>$v_options['ban_duration'],'ban_trigger'=>$args['failed_count']));
       }
       
   }
    
    public function get_logs_to_export($parent_service) {
        
        $export_data = array();
        $export_data[0]['l_id']= __('Log ID','registrationmagic-addon');
        $export_data[0]['l_time']= __('Login Time','registrationmagic-addon');
        $export_data[0]['l_email']= __('Login Email','registrationmagic-addon');
        $export_data[0]['l_ip']= __('Login IP','registrationmagic-addon');
        $export_data[0]['l_browser']= __('Browser','registrationmagic-addon');        
        $export_data[0]['l_type']= __('Login Type','registrationmagic-addon');
        $export_data[0]['l_result']= __('Login Result','registrationmagic-addon');
        $export_data[0]['l_final_result']= __('Final Result','registrationmagic-addon');
        $export_data[0]['l_ip_ban']= __('IP Ban (Yes/No)','registrationmagic-addon');
        $export_data[0]['l_login_from']= __('Login From (page URL','registrationmagic-addon');
        
        $login_logs = RM_DBManager::get_login_log();
        
        foreach($login_logs as $login_log){
            $export_data[$login_log->id]['l_id'] = $login_log->id;
            $export_data[$login_log->id]['l_time'] = date('j M Y, h:i a', strtotime($login_log->time));
            $export_data[$login_log->id]['l_email'] = ($login_log->username_used!='')?$login_log->username_used:$login_log->email;
            $export_data[$login_log->id]['l_ip'] = $login_log->ip;
            $export_data[$login_log->id]['l_browser'] = $login_log->browser;
            
            if($login_log->type=='2fa'){
                $login_type = __('2FA','registrationmagic-addon');
            }else if($login_log->type=='otp'){
                $login_type = __('OTP','registrationmagic-addon');
            }else{
                $login_type = ucfirst($login_log->type);
            }
            $export_data[$login_log->id]['l_type'] = $login_type;
            
            if($login_log->status==1){
                $login_result = __('Success','registrationmagic-addon');
            }else if($login_log->failure_reason=='incorrect_reCAPCTCHA'){
                $login_result = __('Incorrect reCaptcha','registrationmagic-addon');
            }else if($login_log->failure_reason=='incorrect_otp'){
                $login_result = __('Incorrect OTP','registrationmagic-addon');
            }else if($login_log->failure_reason=='expired_otp'){
                $login_result = __('Expired OTP','registrationmagic-addon');
            }else{
                $login_result = ucwords(str_replace('_', ' ', $login_log->failure_reason));
            }            
            $export_data[$login_log->id]['l_result'] = $login_result;
            $export_data[$login_log->id]['l_final_result'] = ucfirst($login_log->result);
            $export_data[$login_log->id]['l_ip_ban'] = ($login_log->ban==1)?'Yes':'No';
            $export_data[$login_log->id]['l_login_from'] = $login_log->login_url;            
        }
        return $export_data;
    }
    
    public function validate_password($pass,$parent_service){
        $gopts = new RM_Options;
        $pass_rule_en= $gopts->get_value_of('enable_custom_pw_rests');
        if($pass_rule_en!='yes')
            return;
        $pw_error_msg = array('PWR_UC' => RM_UI_Strings::get('LABEL_PW_RESTS_PWR_UC'),
        'PWR_NUM' => RM_UI_Strings::get('LABEL_PW_RESTS_PWR_NUM'),
        'PWR_SC' => RM_UI_Strings::get('LABEL_PW_RESTS_PWR_SC'),
        'PWR_MINLEN' => RM_UI_Strings::get('LABEL_PW_MINLEN_ERR'),
        'PWR_MAXLEN' => RM_UI_Strings::get('LABEL_PW_MAXLEN_ERR'));
        $pw_rests = $gopts->get_value_of('custom_pw_rests');
        $patt_regex = RM_Utilities::get_password_regex($pw_rests);
        $error_str = RM_UI_Strings::get('ERR_TITLE_CSTM_PW');
        if(!empty($pw_rests->selected_rules)){
                foreach ($pw_rests->selected_rules as $rule) {
                    if ($rule == 'PWR_MINLEN') {
                        $x = sprintf($pw_error_msg['PWR_MINLEN'], $pw_rests->min_len);
                        $error_str .= '<br> -' . $x;
                    } elseif ($rule == 'PWR_MAXLEN') {
                        $x = sprintf($pw_error_msg['PWR_MAXLEN'], $pw_rests->max_len);
                        $error_str .= '<br> -' . $x;
                    } else
                        $error_str .= '<br> -' . $pw_error_msg[$rule];
                }
        } 
        
        if(!preg_match('/'.$patt_regex.'/',$pass)){
            return $error_str;
        }
        return;
    }
}