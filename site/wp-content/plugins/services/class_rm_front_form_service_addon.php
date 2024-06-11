<?php

class RM_Front_Form_Service_Addon {

    public function is_ip_banned($parent_service,$user_ip=null) {
        //return true;
        $banned_ip_formats = $parent_service->get_setting('banned_ip');
        $banned = false;
        if($user_ip==null)
        $user_ip = $parent_service->get_user_ip();
        
        if (!$user_ip)
            return true;
        //if ($user_ip == '::1')
          //  return false;
        
        if((bool)filter_var($user_ip, FILTER_VALIDATE_IP, FILTER_FLAG_IPV6))
                $sanitized_user_ip = $user_ip;
        else
        {
            // Filtering out the multiple IP's
            $ip_as_arr = explode(',', (string)$user_ip);
            if(isset($ip_as_arr[0])){
            $new_ip_as_arr = explode('.', (string)$ip_as_arr[0]);
            if (count($new_ip_as_arr) !== 4)
            return true;
            }

            $sanitized_user_ip = sprintf("%'3s.%'3s.%'3s.%'3s", $new_ip_as_arr[0], $new_ip_as_arr[1], $new_ip_as_arr[2], $new_ip_as_arr[3]);
        }
        
        if (is_array($banned_ip_formats))
            foreach ($banned_ip_formats as $banned_ip_format) {
                if (RM_Utilities::is_banned_ip($sanitized_user_ip, $banned_ip_format)) {
                    $banned = true;
                    break;
                }
            }

        return $banned;
    }

    public function is_email_banned($email,$parent_service) {
        //return true;
        $banned_email_formats = $parent_service->get_setting('banned_email');
        $banned = false;

        if (is_array($banned_email_formats))
            foreach ($banned_email_formats as $banned_email_format) {
                if (RM_Utilities::is_banned_email($email, $banned_email_format)) {
                    $banned = true;
                    break;
                }
            }

        return $banned;
    }

    public function subscribe_to_ccontact($request, $form_options_cc, $parent_service) {
        if (!isset($form_options_cc->cc_relations->email) || !isset($form_options_cc->cc_list))
            return;
        $merge_fields_array = array();
        $cconatct = new RM_Constant_Contact_Service();
        if(isset($request[$form_options_cc->cc_relations->email]))
        {
            $cconatct->subscribe($request,$form_options_cc);
        }
    }
    
    public function subscribe_to_aweber($request, $form_options_aw, $parent_service) {
   
      
        if (!isset($form_options_aw->aw_relations->email) || !isset($form_options_aw->aw_list))
            return;
        $merge_fields_array = array();
            $aweber = new RM_Aweber_Service();
           
        if(isset($request[$form_options_aw->aw_relations->email]))
        {
           
            $aweber->subscribe($request,$form_options_aw);
        }
    }
    
    public function register_user($username, $email, $password, $form_id, $parent_service, $is_paid = true, $user_auto_approval = null) {
        $gopt = new RM_Options();
 
        //No password!! Generate one.
        if (!$password)
            $password = wp_generate_password(8, false, false);
        
        $user_id = wp_create_user($username, $password, $email);
        do_action('rm_new_user_registered',$user_id);
        
        if (is_wp_error($user_id)) {
            foreach ($user_id as $err) {
                foreach ($err as $error) {
                    echo $error[0];
                    die;
                }
            }
        } else {
            $required_params = new stdClass();
            $required_params->email = $email;
            $required_params->username = $username;
            $required_params->password = $password;
            $required_params->form_id= $form_id;
            $rm_service= new RM_Services();
            $password_field= $rm_service->get_primary_field_options('userpassword',$form_id);
            
            if ($parent_service->get_setting('send_password') === 'yes' || empty($password_field)) {
                RM_Email_Service::notify_new_user($required_params,$user_id);
            }


            /*
             * Deactivate the user in case auto approval is off
             */
            
           $check_setting=null;
             
            if($user_auto_approval=='default')
            {
                $check_setting = $gopt->get_value_of('user_auto_approval');
            }
            else
            {
                $check_setting = $user_auto_approval;
            }
            $user_approval = $check_setting;

            if (($is_paid != true) || $user_approval != "yes") {
                $parent_service->user_service->deactivate_user_by_id($user_id);                                
            } else {
                $parent_service->user_service->activate_user_by_id($user_id);
            }
             
            if($user_approval != "yes" && $user_approval != "verify"){
                $link = $parent_service->user_service->create_user_activation_link($user_id);
                $required_params->link = $link;
                $required_params->form_id = $form_id;
                RM_Email_Service::notify_admin_to_activate_user($required_params);
            } 
        }

        return $user_id;
    }
    
    public function register_user_on_custom_status($username, $email, $password, $form_id, $parent_service, $is_paid = true, $user_auto_approval = null) {
        $gopt = new RM_Options();
        $user_id = wp_create_user($username, $password, $email);
        do_action('rm_new_user_registered',$user_id);
        
        if (is_wp_error($user_id)) {
            foreach ($user_id as $err) {
                foreach ($err as $error) {
                    echo $error[0];
                    die;
                }
            }
        } else {
            $required_params = new stdClass();
            $required_params->email = $email;
            $required_params->username = $username;
            $required_params->password = $password;
            $required_params->form_id= $form_id;
            RM_Email_Service::notify_new_user($required_params,$user_id);


            /*
             * Deactivate the user in case auto approval is off
             */
            
           $check_setting=null;
             
            if($user_auto_approval=='default')
            {
                $check_setting = $gopt->get_value_of('user_auto_approval');
            }
            else
            {
                $check_setting = $user_auto_approval;
            }
            $user_approval = $check_setting;

            if (($is_paid != true) || $user_approval != "yes") {
                $parent_service->user_service->deactivate_user_by_id($user_id);                                
            }
            else
                $parent_service->user_service->activate_user_by_id($user_id);
             
            if($user_approval != "yes" && $user_approval != "verify"){
                $link = $parent_service->user_service->create_user_activation_link($user_id);
                $required_params->link = $link;
                $required_params->form_id = $form_id;
                RM_Email_Service::notify_admin_to_activate_user($required_params);
            } 
        }

        return $user_id;
    }

    public function save_submission($form_id, $data, $email, $parent_service, $modified_by = null, $unique_token = null) {
        $submission_row = array('form_id' => $form_id, 'data' => $data, 'user_email' => $email, 'modified_by' => $modified_by);
        
        $submissions = new RM_Submissions;
        $submissions->set($submission_row);
        $submission_id = $submissions->insert_into_db($unique_token);
        
        if(!$unique_token)
            $unique_token = $submissions->get_unique_token();
        
        $submission_field = new RM_Submission_Fields;
        $submission_field_row['submission_id'] = $submission_id;
        $submission_field_row['form_id'] = $form_id;

        foreach ($data as $field_id => $field_data) {
            $submission_field_row['field_id'] = $field_id;
            $submission_field_row['value'] = $field_data->value;

            $submission_field->set($submission_field_row);
            $submission_field->insert_into_db(true);
        }

        return (object) array('submission_id' => $submission_id, 'token' => $unique_token);
    }

    //Params is an object containing form_options and form name.
    //Right now this function only redirects, it may have other functionality in future, that is why redirect is just a parameter.
    public function after_submission_proc($params, $parent_service, $prevent_redirection = false) {
        global $wp;
        $form_options = $params->form_options;
        if(!empty($_GET['rm_pproc_id'])){
            $pproc= absint($_GET['rm_pproc_id']);
            $log = RM_DBManager::get_row('PAYPAL_LOGS', $pproc);
            $params->form_id= $log->form_id;
            $params->sub_id= $log->submission_id;
        }
        
        $msg_str = "<div class='rm-post-sub-msg'>";
        if($form_options->auto_login){
        ?>    
          <script>jQuery(document).ready(function(){rm_send_dummy_ajax_request();});</script>
        <?php   
        }
        $msg_str .= $form_options->form_success_message != "" ? apply_filters('rm_form_success_msg',$form_options->form_success_message,$params->form_id,$params->sub_id) : $params->form_name . " ".__('Submitted','registrationmagic-addon');
        if (!$prevent_redirection) {
            if ($form_options->redirection_type) {
                $redir_str = "<br>" . RM_UI_Strings::get("MSG_REDIRECTING_TO") . "<br>";
                //echo "<br>", var_dump(),die;

                if ($form_options->redirection_type === "page") {
                    $page_id = $form_options->redirect_page;
                    $page = get_post($page_id);
                    if($page instanceof WP_Post)
                    {
                        $page_title = $page->post_title ? $page->post_title : '#' . $page_id . ' '.__('(No Title)','registrationmagic-addon');
                        $redir_str .= $page_title;
                        RM_Utilities::redirect(null, true, $page_id, true); 
                       // die();
                    }
                } else {
                    $url = $form_options->redirect_url;
                    $redir_str .= $url;
                    RM_Utilities::redirect($url, false, 0, true);
                    //die();
                }
                return '<div class="rm_form_submit_msg">' . $msg_str . '</div><br><br>' . $redir_str."</div>";
            }
        }

        if($form_options->auto_login && !is_user_logged_in()){
            $global_option = new RM_Options;
            $gauto_approval= $global_option->get_value_of('user_auto_approval');
            $prov_act_acc= $global_option->get_value_of('prov_act_acc');
            $prov_acc_act_criteria= $global_option->get_value_of('prov_acc_act_criteria');
            if($form_options->user_auto_approval=="yes" || (in_array($gauto_approval,array('yes','verify')) && $form_options->user_auto_approval=="default")){
                if(isset($_POST['rm_payment_method']) && $_POST['rm_payment_method']=="offline") {
                    return '<div class="rm_form_submit_msg">' . $msg_str . "</div></div>";
                } elseif(isset($_REQUEST['rm_pproc']) && $_REQUEST['rm_pproc']=="success") {
                    if($form_options->user_auto_approval=="default" && $gauto_approval=='verify' && empty($prov_act_acc)) {
                        return '<div class="rm_form_submit_msg">' . $msg_str . "</div></div>";
                    }
                } elseif(isset($_REQUEST['rm_pproc']) && $_REQUEST['rm_pproc']!="success") {
                    return '<div class="rm_form_submit_msg">' . $msg_str . "</div></div>";
                }
                //elseif(isset($_REQUEST['rm_pproc']) && $_REQUEST['rm_pproc']=="success"){}

                $msg_str .= '<div id="rm_ajax_login">'.RM_UI_Strings::get("MSG_ASYNC_LOGIN").'</div><br><br>';
                
                if(isset($params->form_id)) {
                    $current_url = home_url(add_query_arg(array(),$wp->request));
                    $current_url = add_query_arg( array('rm_success'=>'1','rm_form_id'=>$params->form_id,'rm_sub_id'=>$params->sub_id), $current_url);
                    if(!in_array($gauto_approval,array('verify')) || (in_array($gauto_approval,array('verify')) && !empty($prov_act_acc) && ($prov_acc_act_criteria=='until_user_logsout' || $prov_acc_act_criteria=='until_act_link_expires'))) {
                        RM_Utilities::redirect($current_url, false, 0, true);
                    }
                }
            }
        }
        
        return '<div class="rm_form_submit_msg">' . $msg_str . "</div></div>";
    }

    public function register_user_old($request, $form, $is_auto_generate, $parent_service, $is_paid = true) {
        $gopt = new RM_Options();
        $username = $request->req['username'];

        if ($is_auto_generate !== "yes")
            $password = $request->req['password'];
        else
            $password = wp_generate_password(8, false, false);

        $primary_emails = $parent_service->get_primary_email_fields($form->form_id);

        $request_keys = array_keys($request->req);
        $emails = array_intersect($request_keys, $primary_emails);

        foreach ($emails as $email) {
            $email_field_name = $email;
            break;
        }

        $email = $request->req[$email_field_name];

        $user_id = wp_create_user($username, $password, $email);
        do_action('rm_new_user_registered',$user_id);
        if (is_wp_error($user_id)) {
            foreach ($user_id as $err) {
                foreach ($err as $error) {
                    echo $error[0];
                    die;
                }
            }
        } else {
            /*
             * User created. Check if details has to send via an email
             */
            

            $required_params = new stdClass();
            $required_params->email = $email;
            $required_params->username = $username;
            $required_params->password = $password;
            $required_params->form_id= $form->form_id;
            if ($parent_service->get_setting('send_password') === 'yes' || $parent_service->get_setting('auto_generated_password') === 'yes') {
                RM_Email_Service::notify_new_user($required_params,$user_id);
            }

            /*
             * Deactivate the user in case auto approval is off
             */


            if (!$is_paid || ($gopt->get_value_of('user_auto_approval') != "yes" && $gopt->get_value_of('user_auto_approval')!='verify')) {

                $parent_service->user_service->deactivate_user_by_id($user_id);
            }

            /*
             * If role is chosen by registrar
             */
            if (isset($request->req['role_as']) && !empty($request->req['role_as'])) {
                $parent_service->user_service->set_user_role($user_id, $request->req['role_as']);
            } else {
                $tmp = $form->get_default_form_user_role();
                if (!empty($tmp)) {
                    /*
                     * Assign user role if configured by default
                     */
                    $parent_service->user_service->set_user_role($user_id, $form->get_default_form_user_role());
                }
            }
        }

        return $user_id;
    }

    public function process_payment($form, $request, $params, $parent_service) {
        if (isset($request->req['rm_payment_method']))
            $payment_method = $request->req['rm_payment_method'];
        else {
            $payment_gateways = $parent_service->get_setting('payment_gateway');

            if (!$payment_gateways || count($payment_gateways) == 0)
                return;

            if (!is_array($payment_gateways))
                $payment_gateways = array($payment_gateways);

            $payment_method = $payment_gateways[0];
        }

        
        // Paypal handling
        if ($payment_method === "paypal") {
            $paypal_service = new RM_Paypal_Service();
            $pricing_details = $form->get_pricing_detail($request->req);
            if($pricing_details === null)
                return;
            $data = new stdClass();
            $data->form_id = $form->get_form_id();
            $data->submission_id = $params['sub_detail']->submission_id;
            $data->user_email = $params['user_email'];
            if ($form->get_form_type() === RM_REG_FORM)
                $data->user_id = $form->get_registered_user_id();

            return $paypal_service->charge($data, $pricing_details);
        } else if ($payment_method === "stripe") {
            $stripe_service = RM_Stripe_Service::get_instance();
            $pricing_details = $form->get_pricing_detail($request->req);
            if($pricing_details === null)
                return;
            $data = new stdClass();
            $data->form_id = $form->get_form_id();
            $data->form_name = $form->get_form_name();
            $data->submission_id = $params['sub_detail']->submission_id;
            $data->user_email = $params['user_email'];
            if ($form->get_form_type() === RM_REG_FORM)
                $data->user_id = $form->get_registered_user_id();
            return $stripe_service->show_card_elements($data,$pricing_details);
        } else { /* pass it on to extensions */
            $payment_done = false;
            $payment_done = apply_filters('rm_process_payment', $payment_done, $form, $request, $params);        
            return $payment_done;
        }
    }

    public function update_user_profile($user_id_or_email, array $profile, $parent_service, $is_email = false) {
        
        $return = true;

        if (!$is_email) {
            $user_id = $user_id_or_email;
        } else {
            $user = get_user_by('email', $user_id_or_email);
            if (!isset($user->ID))
                return false;
            if ((int) $user->ID)
                $user_id = $user->ID;
            else
                return false;
        }

        if(user_can($user_id, 'manage_options') && get_current_user_id() != $user_id) {
            return false;
        }
        
        $name = '';
        foreach ($profile as $type => $pr) {
            if ($type === 'Fname' || $type === 'Lname' || $type === 'BInfo' || $type === 'Nickname' || $type === 'SecEmail' || $type === 'Website'){
                switch ($type) {
                    case 'Fname' :
                        $return = update_user_meta($user_id, 'first_name', $pr);
                        $name .= !empty($pr) ? $pr.' ' : '';
                        break;
                    case 'Lname' :
                        $return = update_user_meta($user_id, 'last_name', $pr);
                        $name .= !empty($pr) ? $pr : '';
                        break;
                    case 'BInfo' :
                        $return = update_user_meta($user_id, 'description', $pr);
                        break;
                    case 'Nickname' :
                        $return = update_user_meta($user_id, 'nickname', $pr);
                        break;
                    case 'SecEmail' :
                        $return = update_user_meta($user_id, 'sec_email', $pr);
                        break;
                    case 'Website' :
                        $return = wp_update_user( array( 'ID' => $user_id, 'user_url' => $pr ) );
                        break;
                }
            } elseif ($type === 'pm_user_avatar' && is_array($pr)) {
                $return = update_user_meta( $user_id, $type, $pr[0] );
            } else {
                $return = update_user_meta( $user_id, $type, $pr );
            }
        }
        if(!empty($name)){
            wp_update_user(array('ID'=>$user_id,'display_name'=>$name));
        }
        return $return;
    }

     public function set_properties(stdClass $options, $parent_service) {
        $properties = array();
        if (isset($options->field_placeholder) && null != $options->field_placeholder)
            $properties['placeholder'] = $options->field_placeholder;
        
            $properties['longDesc'] = isset($options->help_text) ? $options->help_text: '';
        if (isset($options->field_css_class) && null != $options->field_css_class)
            $properties['class'] = $options->field_css_class;
        if (isset($options->field_max_length) && null != $options->field_max_length)
            $properties['maxlength'] = $options->field_max_length;
        if (isset($options->field_min_length) && null != $options->field_min_length)
            $properties['minlength'] = $options->field_min_length;
        if (isset($options->field_timezone))
            $properties['field_time_zone'] = $options->field_timezone;
        if (isset($options->field_textarea_columns) && null != $options->field_textarea_columns)
            $properties['cols'] = $options->field_textarea_columns;
        if (isset($options->field_textarea_rows) && null != $options->field_textarea_rows)
            $properties['rows'] = $options->field_textarea_rows;
        if (isset($options->field_is_admin_only) && null != $options->field_is_admin_only)
            $properties['field_is_admin_only'] = $options->field_is_admin_only;
        if (isset($options->field_is_required) && null != $options->field_is_required)
            $properties['required'] = $options->field_is_required;
        if (isset($options->field_is_required_scroll))
            $properties['required_scroll'] = $options->field_is_required_scroll;
        if (isset($options->field_is_required_range))
            $properties['required_range'] = $options->field_is_required_range;
        if (isset($options->field_is_required_max_range))
            $properties['required_max_range'] = $options->field_is_required_max_range;
        if (isset($options->field_is_required_min_range))
            $properties['required_min_range'] = $options->field_is_required_min_range;
        if (isset($options->field_is_show_asterix))
            $properties['show_asterix'] = $options->field_is_show_asterix;
        if (isset($options->field_default_value) && null != $options->field_default_value)
            $properties['value'] = maybe_unserialize($options->field_default_value);
        if (isset($options->field_is_other_option) && null != $options->field_is_other_option)
            $properties['rm_is_other_option'] = $options->field_is_other_option;
         if (isset($options->rm_textbox) && null != $options->rm_textbox)
            $properties['rm_textbox'] = $options->rm_textbox;
        if (isset($options->style_textfield) && null != $options->style_textfield)
            $properties['style'] = $options->style_textfield;
        if (isset($options->style_label) && null != $options->style_label)
            $properties['labelStyle'] = $options->style_label;
        if (isset($options->field_validation))
            $properties['field_validation'] = $options->field_validation;
        if (isset($options->custom_validation))
            $properties['custom_validation'] = $options->custom_validation;
        if (isset($options->field_is_multiline))
            $properties['field_is_multiline'] = $options->field_is_multiline;
        if (isset($options->date_format))
            $properties['date_format'] = $options->date_format;
        if (isset($options->field_is_unique))
            $properties['field_is_unique'] = $options->field_is_unique;
       
        
        return $properties;
    }
    
    public function is_unique_field_value($field_id,$value,$parent_service)
    {   
        $count= RM_DBManager::count("SUBMISSION_FIELDS",array('sub_field_id' => $field_id,'value'=>$value),array('%d','%s'));
        return $count>0?false: true;
    }
   
}