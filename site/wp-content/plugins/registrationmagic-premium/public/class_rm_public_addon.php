<?php

class RM_Public_Addon {
    
    public function rm_front_submissions($attr,$parent_public) {
        $form_prev= isset($_GET['form_prev']) ? absint($_GET['form_prev']) : '';
        if(is_user_logged_in() && class_exists('Profile_Magic') && empty($attr) && empty($form_prev) && !isset($_REQUEST['submission_id']) && empty(get_site_option('rm_option_disable_pg_profile'))){
            return do_shortcode('[PM_Profile]');
        }
        $user_model= new RM_User;
        if(!empty($_GET['resend']) && !empty($_GET['rm_user'])){
            $re_verification_link= RM_Utilities::get_acc_verification_link($_GET['rm_user']);
            echo 'Click here to resend the verification link.'.$re_verification_link;
            return;
        }
        /* User Verification */
        if(!empty($_GET['rm_hash']) && !empty($_GET['rm_user'])){
            
            $user_id= absint($_GET['rm_user']);
            if(empty($user_id))
                return;
            /*
            if(is_user_logged_in())
                return;
            */
            
            $hash= sanitize_text_field($_GET['rm_hash']);
            $user_hash= get_user_meta($user_id, 'rm_activation_hash', true);
            $gopts= new RM_Options();
           
            if(get_user_meta($user_id, 'rm_user_status', true)===0){
                echo $gopts->get_value_of('acc_act_notice');
                echo do_shortcode('[RM_Login]');
                return;
            } else {
                // check for payment status
                $submission_id= get_user_meta($user_id, 'RM_UMETA_SUB_ID', true); // Get submission ID from where it is 
                if(!empty($submission_id)){
                    $submission_model= new RM_Submissions;
                    $submission_model->load_from_db($submission_id);
                    $status= $submission_model->get_payment_status();
                    if(!empty($status) && !in_array(strtolower($status),array('completed','succeeded'))){
                      // Payment not completed.
                      echo RM_UI_Strings::get('LABEL_ACC_NOT_ACTIVATED_PENDING_PAYMENT');
                      return;
                    }
                }

                if(!empty($user_hash)){
                    if($hash==$user_hash){
                        $act_message= $gopts->get_value_of('acc_act_notice');
                        $act_expiry= $gopts->get_value_of('acc_act_link_expiry');
                        if($act_expiry>0){
                            $user_info = get_userdata($user_id);
                            $reg_date= get_user_meta($user_id, 'rm_activation_time', true);
                            $reg_timestamp= strtotime($reg_date);
                            $current_time= current_time('timestamp');
                            $time_diff= $current_time-$reg_timestamp;
                            $seconds_diff= $time_diff/60;
                            $hour_diff= $seconds_diff/60;
                            if($act_expiry>=$hour_diff){
                                $user_model->activate_user($user_id);
                                delete_user_meta($user_id, 'rm_activation_hash');
                                echo $act_message;
                                echo do_shortcode('[RM_Login]');
                                return;
                            } else {
                                $act_expiry_message= $gopts->get_value_of('acc_act_link_exp_notice');
                                $re_verification_link= RM_Utilities::get_acc_verification_link($user_id);
                                $act_expiry_message= str_ireplace('{{send verification email}}', $re_verification_link, $act_expiry_message);
                                $act_expiry_message= str_ireplace('{{SEND_VERIFICATION_EMAIL}}', $re_verification_link, $act_expiry_message);

                                echo '<div class="rm_exp_link_msg">'.$act_expiry_message.'</div>';
                            }
                        } else if($act_expiry==0) {
                            $user_model->activate_user($user_id);
                            delete_user_meta($user_id, 'rm_activation_hash');
                            echo $act_message;
                            echo do_shortcode('[RM_Login]');
                            return;
                        }
                        //delete_user_meta( $user_id, $meta_key, $meta_value )
                    } else {
                        if(isset($_GET['rm_user'])) {
                            $user_id= absint($_GET['rm_user']);
                            if($user_id==0)
                                return;
                            $user= get_userdata($user_id);
                            if(empty($user))
                                return;
                             $invalid_msg= $gopts->get_value_of('acc_invalid_act_code');
                             echo $invalid_msg;
                             echo '<form method="get"><input type="hidden" name="rm_user" value="'.$user_id.'"><input type="text" name="rm_hash" placeholder="Activation Code"><br><input type="submit" value="Submit"></form>';
                        }

                    }
                }
            }
        }
        /* Shows form preview */
        if(!empty($_GET['form_prev']) && !empty($_GET['form_id']) && is_super_admin())
        {  
            $form_id= $_GET['form_id'];
            $form_factory= new RM_Form_Factory_Addon();
            $form= $form_factory->create_form($form_id);
            $form->set_preview(true);
            echo '<script>jQuery(document).ready(function(){jQuery(".entry-header").remove();}); </script>';
            echo '<link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">';
            echo '<div class="rm_embedeed_form">' . $form->render() . '</div>';
            return;
        }
        
        if (RM_Utilities::fatal_errors()) {
            ob_start();
            include_once RM_ADMIN_DIR . 'views/template_rm_cant_continue.php';
            $html = ob_get_clean();
            return $html;
        }
        
        $xml_loader = RM_XML_Loader::getInstance(RM_ADDON_INCLUDES_DIR . 'rm_config.xml');

        $request = new RM_Request($xml_loader);
        if(isset($_POST['rm_slug'])){
            $request->setReqSlug($_POST['rm_slug'], true);
        }
        else{
            $request->setReqSlug('rm_front_submissions', true);
        }
        
        $params = array('request' => $request, 'xml_loader' => $xml_loader,'attr'=>$attr);
        $parent_public->controller = new RM_Main_Controller($params);
        return $parent_public->controller->run();
    }

    public function rm_user_form_render($attribute,$parent_public) {
        RM_Public::$form_counter++;
        //$parent_public->disable_cache();
        if (RM_Utilities::fatal_errors()) {
            ob_start();
            include_once RM_ADMIN_DIR . 'views/template_rm_cant_continue.php';
            $html = ob_get_clean();
            return $html;
        }
        $xml_loader = RM_XML_Loader::getInstance(RM_ADDON_INCLUDES_DIR . 'rm_config.xml');
        $form_id = isset($attribute['id']) ? absint($attribute['id']) : 1;
        $request = new RM_Request($xml_loader);
        
        if(!RM_Public::$success_form && isset($request->req['rm_success']) && $request->req['rm_success']=="1" && !empty($form_id) && isset($request->req['rm_form_id']) && $form_id==$request->req['rm_form_id']){
            RM_Public::$success_form= true;
            $form = new RM_Forms();
            $form->load_from_db($form_id);
            $form_options= $form->form_options;
            $html = "<div class='rm-post-sub-msg'>";
            $sub_id = isset($request->req['rm_sub_id']) ? absint($request->req['rm_sub_id']) : 0;
            $html .= $form_options->form_success_message != "" ? apply_filters('rm_form_success_msg',$form_options->form_success_message,$form_id,$sub_id) : $form->form_name . " Submitted ";
            $html .= '</div>';
            return $html;
        }
        $request->setReqSlug('rm_user_form_process', true);
        $params = array('request' => $request, 'xml_loader' => $xml_loader, 'form_id' => $form_id,'force_enable_multiform'=>true);
       
        if(isset($attribute['prefill_form']))
            $request->setReqSlug('rm_user_form_edit_sub', true);
        
        
        $parent_public->controller = new RM_Main_Controller($params);
        return $parent_public->controller->run();
    }

    public function render_embed() {
        //Set X-Frame-Options to allow
        //@header('X-Frame-Options: GOFORIT');
        @header('Content-Security-Policy: frame-ancestors ' . get_site_url());
        @header("Content-Security-Policy: default-src 'self' 'unsafe-inline'");
        @header("Content-Security-Policy: script-src 'self' 'unsafe-inline' 'unsafe-eval' http: https:");
        @header("X-Frame-Options: ALLOWALL");
        $id = absint($_GET['form_id']);
        ?>
        <pre class="rm-pre-wrapper-for-script-tags"><script type="text/javascript">
            var ajaxurl = '<?php echo admin_url('admin-ajax.php'); ?>';
        </script></pre>
        <?php
        do_action('wp_head');
        define('RM_AJAX_REQ', true);
        echo '<div class="rm_embedeed_form">' . do_shortcode("[RM_Form id='$id']") . '</div>';
        die;
    }
    
     public function rm_user_list($attribute,$parent_public){ 
        if (RM_Utilities::fatal_errors()) {
            ob_start();
            include_once RM_ADMIN_DIR . 'views/template_rm_cant_continue.php';
            $html = ob_get_clean();
            return $html;
        }
        $xml_loader = RM_XML_Loader::getInstance(RM_ADDON_INCLUDES_DIR . 'rm_config.xml');
 
        $request = new RM_Request($xml_loader);
        $request->setReqSlug('rm_front_user_list', true);
        
        $params = array('request' => $request, 'xml_loader' => $xml_loader,'attribute'=>$attribute);
        
        $parent_public->controller = new RM_Main_Controller($params);
        return $parent_public->controller->run();
    }
    
    public function rm_mark_email_read() { 
        //Safety check that it is indeed invoked by WP ajax call
        if (defined('DOING_AJAX') && DOING_AJAX) {
            if(isset($_POST['action'], $_POST['rm_email_id']) && $_POST['action'] == 'rm_mark_email_read') {
                $email_id = $_POST['rm_email_id'];
                $front_service = new RM_Front_Service;        
                $front_service->mark_email_read($email_id);
            }
            wp_die();
        }
    }
    
    public function unique_field_value_check()
    { 
       if(empty($_POST['value']) || empty($_POST['field_name']))
       {
            echo json_encode(array('status'=> 'valid'));
       }
       
       $service= new RM_Front_Form_Service();  
       $field= explode('_', (string)$_POST['field_name']);
       
       
       if($service->is_unique_field_value($field[1], $_POST['value']))
       {
            echo json_encode(array('status'=> 'valid')); 
            wp_die();
       }
       $field_model= new RM_Fields();
       $field_model->load_from_db($field[1]); 
       
       $msg= ucwords($field_model->field_label).' '.RM_UI_Strings::get("ERROR_UNIQUE");
       if($field_model->field_options->field_is_unique==1)
           $msg= $field_model->field_options->un_err_msg;
       echo json_encode(array('status'=> 'invalid','msg'=> $msg)); 
       wp_die();
    }
    
    public function remove_expired_otp(){
        //RM_DBManager::delete_expired_otp();
        RM_DBManager::remove_expired_bans();  // Removes expired IP bans
    } 
    
    public function generate_fa_otp(){
        $response= array('status'=>true);
        $username= sanitize_text_field($_POST['username']);
        if(empty($username)){
            $response['status']= false;
        }
        $login_service= new RM_Login_Service();
        $auth_options= $login_service->get_auth_options();
        $user= null;
        if(email_exists($username)){
            $user= get_user_by('email', $username);
        }
        if(empty($user)){
            $user= get_user_by('login',$username);
        }
        $login_service->send_2fa_otp($user);
        $expired= isset($_POST['expired']) ? absint($_POST['expired']) : 0;
        if(empty($expired)){
            $response['msg']= $auth_options['otp_resent_msg'];   
        }
        else
        {
         $response['msg']= $auth_options['otp_regen_success_msg'];   
        }
        
        echo json_encode($response);
        die;
        
    }
    
    public function paypal_ipn(){
        if($_SERVER['REQUEST_METHOD'] === 'POST'){
            $paypal_service = new RM_Paypal_Service();
            $resp = $paypal_service->callback('ipn',null,null);
        }
        die;
    }
    
    public function payment_completed_response($response,$submission,$form_id,$payment_status){
        $gopt=new RM_Options;
        $form = new RM_Forms();
        $form->load_from_db($form_id);
        $user_service = new RM_User_Services();
        $is_logging_in= false;
        // Payment completed
        if(!empty($form->form_type) && $payment_status && !is_user_logged_in()){
            //Check for user activation
            $activate_user = $form->form_options->user_auto_approval=='default' ? $gopt->get_value_of('user_auto_approval'): $form->form_options->user_auto_approval;
            if($activate_user=='yes'){
                $user = get_user_by('email',$submission->get_user_email());
                $user_service->activate_user_by_id($user->ID);
            }
            
            // Login after registration
            if(!empty($form->form_options->auto_login)){
                RM_Utilities::login_user_by_id($user->ID);
                $is_logging_in= true;
            }
        }
        
        // Success message
        $response['msg'] .= '<div id="rmform">';
        $response['msg'] .= "<br><br><div class='rm-post-sub-msg'>";
        $response['msg'] .= $form->form_options->form_success_message != "" ? apply_filters('rm_form_success_msg',$form->form_options->form_success_message,$form_id,$submission->get_submission_id()) : $form->get_form_name() . " ". __('Submitted','registrationmagic-addon');
        $response['msg'] .= '</div>';
        
            
        // After submission redirection
        $response['redirect']= RM_Utilities::get_form_redirection_url($form);
        $redirection_page='';
        if(!empty($response['redirect'])){
            $redirection_type = $form->get_form_redirect();
            if ($redirection_type=== "page") {
                $page_id = $form->get_form_redirect_to_page();
                $page = get_post($page_id);
                if($page instanceof WP_Post)
                    $redirection_page = $page->post_title ? $page->post_title : '#' . $page_id . ' '.__('(No Title)','registrationmagic-addon');
            } else if($redirection_type==='url') {
                    $redirection_page = $form->get_form_redirect_to_url();
            }
            if(!empty($redirection_page)){
                $response['msg'] .= '<br><span>'.RM_UI_Strings::get("MSG_REDIRECTING_TO").' '.$redirection_page.'</span>';
            }
        }
        
        if($is_logging_in && empty($redirection_page)){
            $response['msg'] .= '<br><span>'.RM_UI_Strings::get("MSG_ASYNC_LOGIN").'</span>';
            if(empty($response['redirect'])){
                $response['reload_params'] = "?rm_success=1&rm_form_id=$form_id&rm_sub_id=".$submission->id;
            }
        }
        $response['msg'] .= '</div>';
        return $response;
    }
    
    public function intercept_login(){
        $slug= isset($_POST['rm_slug']) ? sanitize_text_field($_POST['rm_slug']) : '';
        if($slug!='rm_login_form')
            return;
        
        $username = sanitize_text_field($_POST['username']);
        $login_service = new RM_Login_Service();
        $login_form= json_decode($login_service->get_form(),true);
        $user= $login_service->get_user($username);
        $auth_otp = isset($_POST['auth_otp']) ? sanitize_text_field($_POST['auth_otp']) : false;
        if(empty($auth_otp)){
            $password = sanitize_text_field($_POST['pwd']);
        }
        if(empty($user))
            return;
        $user_service= new RM_User_Services();
        if(!empty($auth_otp)){
            if($login_service->check_otp($auth_otp,$user)){
                $auth_options= $login_service->get_auth_options();
                if(!(!empty($auth_options['otp_expiry']) && $login_service->is_otp_expired($auth_otp,$user))){
                     $user_service->auto_login_by_id($user->ID);
                }
            }
            return;
        }
        
        
        $prov_acc_act= RM_Utilities::rm_is_prov_login_active($user->ID);
        $is_disabled = (int) get_user_meta($user->ID, 'rm_user_status', true);
        if($is_disabled==1 && !empty($prov_acc_act)){
            $is_disabled= false;
        }
        
        if(empty($is_disabled)){
            $applicable= $login_service->two_fact_auth_applicable($user);
            if(empty($applicable)){
                //$user = wp_signon(array('user_login'=>$user->user_login,'user_password'=>$password));
                if(wp_check_password($password,$user->user_pass,$user->ID)){
                    wp_set_auth_cookie($user->ID);
                    wp_set_current_user($user->ID);
                    do_action('rm_user_signon',$user);
                }
            }
        }
    }
}