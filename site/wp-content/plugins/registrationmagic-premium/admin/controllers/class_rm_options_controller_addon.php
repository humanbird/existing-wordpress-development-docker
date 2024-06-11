<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of class_options_controller
 *
 * @author CMSHelplive
 */
class RM_Options_Controller_Addon
{
    
    public function fab(RM_Options $model, RM_Setting_Service $service, $request, $params, $parent_controller)
    {
        if ($parent_controller->mv_handler->validateForm("options_fab"))
        {
            $options = array();
            $options['display_floating_action_btn'] = isset($request->req['display_floating_action_btn']) ? "yes" : null;
            $options['hide_magic_panel_styler'] = isset($request->req['hide_magic_panel_styler']) ? "yes" : null;
            $options['fab_icon'] = $request->req['fab_icon'];
            $link_type1=isset($request->req['fab_link_type1']) ? $request->req['fab_link_type1']: null;
            $link_type2=isset($request->req['fab_link_type2']) ? $request->req['fab_link_type2']: null;
            $link_type3=isset($request->req['fab_link_type3']) ? $request->req['fab_link_type3']: null;
            $fab_links=array(
                "1"=>array(
                    "flag"=>isset($request->req['fab_link1']['0']) ? $request->req['fab_link1']['0']: null,
                    "type"=>$link_type1,
                    "visibility"=>isset($request->req['fab_link_role_'.$link_type1.'1']) ? $request->req['fab_link_role_'.$link_type1.'1'] : null,
                    "link"=>isset($request->req['fab_link_'.$link_type1.'1']) ? $request->req['fab_link_'.$link_type1.'1']: null,
                    "label"=>isset($request->req['fab_link_'.$link_type1.'_label1']) ? $request->req['fab_link_'.$link_type1.'_label1'] : null
                ),
                "2"=>array(
                    "flag"=>isset($request->req['fab_link2']['0']) ? $request->req['fab_link2']['0']: null,
                    "type"=>$link_type2,
                    "visibility"=>isset($request->req['fab_link_role_'.$link_type2.'2']) ? $request->req['fab_link_role_'.$link_type2.'2'] : null,
                    "link"=>isset($request->req['fab_link_'.$link_type2.'2']) ? $request->req['fab_link_'.$link_type2.'2']: null,
                    "label"=>isset($request->req['fab_link_'.$link_type2.'_label2']) ? $request->req['fab_link_'.$link_type2.'_label2'] : null
                ),
                "3"=>array(
                    "flag"=>isset($request->req['fab_link3']['0']) ? $request->req['fab_link3']['0']: null,
                    "type"=>$link_type3,
                    "visibility"=>isset($request->req['fab_link_role_'.$link_type3.'3']) ? $request->req['fab_link_role_'.$link_type3.'3'] : null,
                    "link"=>isset($request->req['fab_link_'.$link_type3.'3']) ? $request->req['fab_link_'.$link_type3.'3']: null,
                    "label"=>isset($request->req['fab_link_'.$link_type3.'_label3']) ? $request->req['fab_link_'.$link_type3.'_label3'] : null
                )
                
            );
            
            
            $options['fab_links']=$fab_links;
            
     // echo "<pre>",var_dump($fab_links);die;
            $options['show_tabs']=array(
            'payment'=>isset($request->req['pay_tab']) ? 1: 0,
            'details'=>isset($request->req['det_tab']) ? 1: 0,
            'submissions'=>isset($request->req['sub_tab']) ?1: 0);
            $service->set_model($model);

            $service->save_options($options);
            RM_Utilities::redirect(admin_url('/admin.php?page=' . $params['xml_loader']->request_tree->success));
        } else
        {
            $view = $parent_controller->mv_handler->setView('options_fab');
            $service->set_model($model);
            $data = $service->get_options();            
            $view->render($data);
        }
    }

    public function security($model, RM_Setting_Service $service, $request, $params, $parent_controller)
    {
        if ($parent_controller->mv_handler->validateForm("options_security"))
        {
            $gopt= new RM_Options;
            $old_banned_ips = array();
            $ip_banned= $gopt->get_value_of('banned_ip');
            if(!empty($ip_banned)){
                $old_banned_ips= $gopt->get_value_of('banned_ip');
            }
            
            $options = array();

            $options['enable_captcha'] = isset($request->req['enable_captcha']) ? "yes" : null;
           // $options['captcha_language'] = $request->req['captcha_language'];
            $options['public_key'] = isset($request->req['public_key']) ? $request->req['public_key'] : null;
            $options['private_key'] = isset($request->req['private_key']) ? $request->req['private_key'] : null;
            $options['public_key3'] = isset($request->req['public_key3']) ? $request->req['public_key3'] : null;
            $options['private_key3'] = isset($request->req['private_key3']) ? $request->req['private_key3'] : null;
            $options['sub_limit_antispam'] = $request->req['sub_limit_antispam'];
            $options['banned_ip'] = $request->req['banned_ip'];
            $options['banned_email'] = $request->req['banned_email'];
            $options['recaptcha_v']= $request->req['recaptcha_v'];
            $options['blacklisted_usernames'] = $request->req['blacklisted_usernames'];
            $options['enable_captcha_under_login'] = isset($request->req['enable_captcha_under_login']) ? "yes" : null;
           // $options['captcha_req_method'] = $request->req['captcha_req_method'];
            $options['enable_custom_pw_rests'] = isset($request->req['enable_custom_pw_rests']) ? "yes" : null;
            $custom_pw_rests = isset($request->req['custom_pw_rests']) ? $request->req['custom_pw_rests'] : null;
             
             if(!$custom_pw_rests)
             {
                 $custom_pw_rests = (object) array('selected_rules' => array(), 'min_len' => $request->req['PWR_MINLEN'], 'max_len' => $request->req['PWR_MAXLEN']);
             }
             else
             {
                 $custom_pw_rests = (object) array('selected_rules' => $custom_pw_rests, 'min_len' => $request->req['PWR_MINLEN'], 'max_len' => $request->req['PWR_MAXLEN']);
             }
             
            
            $service->set_model($model);
            $options['custom_pw_rests'] = $custom_pw_rests;
            $service->save_options($options);
            
            // Identiying deleted IPS
            $recent_banned_ips = array();
            if(!empty($ip_banned)){
                $recent_banned_ips= $gopt->get_value_of('banned_ip');
            }
            $diff= array_diff($old_banned_ips,$recent_banned_ips);
            if(!empty($diff)){
                foreach($diff as $ip){
                    do_action('rm_ip_unblocked',$ip);
                }
            }
           RM_Utilities::redirect(admin_url('/admin.php?page=' . $params['xml_loader']->request_tree->success));
        } else
        {
            $view = $parent_controller->mv_handler->setView('options_security');
            $service->set_model($model);
            $data = $service->get_options();
            $view->render($data);
        }
    }

    public function thirdparty($model, $service, $request, $params, $parent_controller)
    {
        if ($parent_controller->mv_handler->validateForm("options_thirdparty"))
        {
            $options = array();
            $service->set_model($model);
            $data = $service->get_options();
            $options['enable_mailchimp'] = isset($request->req['enable_mailchimp']) ? "yes" : null;
            $options['mailchimp_key'] = $request->req['mailchimp_key'];
            $options['mailchimp_double_optin'] = isset($request->req['mailchimp_double_optin']) ? "yes" : null;
            $options['google_map_key'] = $request->req['google_map_key'];
            $options['enable_ccontact'] = isset($request->req['enable_ccontact']) ? "yes" : null;
            $options['cc_app_key'] = isset($request->req['cc_app_key']) ? $request->req['cc_app_key'] : '';
            $options['cc_access_token'] = isset($request->req['cc_access_token']) ? $request->req['cc_access_token'] : '';
            $options['enable_aweber'] = isset($request->req['enable_aweber']) ? "yes" : null;
            $options['aw_oauth_id'] = isset($request->req['aw_oauth_id']) ? $request->req['aw_oauth_id'] : '';
            if(!empty($options['aw_oauth_id']) && $data['aw_oauth_id']!=$options['aw_oauth_id']){
                try{
                    list($options['aw_consumer_key'],$options['aw_consumer_secret'],$options['aw_access_key'],$options['aw_access_secret']) = AWeberAPI::getDataFromAweberID($options['aw_oauth_id']);
                }
                catch (Exception $exc){
                    list($options['aw_consumer_key'],$options['aw_consumer_secret'],$options['aw_access_key'],$options['aw_access_secret']) = null;
                } 
            }
            //Pass it to extensions
            do_action('rm_gopts_thirdparty_save', $request->req);
            $service->set_model($model);

            $service->save_options($options);
            RM_Utilities::redirect(admin_url('/admin.php?page=' . $params['xml_loader']->request_tree->success));
        } else
        {
            $view = $parent_controller->mv_handler->setView('options_thirdparty');
            $service->set_model($model);
            $data = $service->get_options();
            $data = apply_filters('rm_extend_thirdparty_config',$data);
            $view->render($data);
        }
    }
    
    public function payment($model, $service, $request, $params, $parent_controller)
    {
        if ($parent_controller->mv_handler->validateForm("options_payment"))
        {
            $options = array();

            $options['payment_gateway'] = isset($request->req['payment_gateway']) ? $request->req['payment_gateway'] : null;
            $options['paypal_test_mode'] = isset($request->req['paypal_test_mode']) ? "yes" : null;
            $options['paypal_modern_enable'] = isset($request->req['paypal_modern_enable']) ? "yes" : null;
            $options['currency'] = $request->req['currency'];
            $options['currency_symbol_position'] = $request->req['currency_symbol_position'];
            $options['hide_pay_selector'] = isset($request->req['hide_pay_selector']) ? "yes" : null;
            $options['enable_tax'] = isset($request->req['enable_tax']) ? "yes" : null;
            $options['tax_type'] = $request->req['tax_type'];
            $options['tax_fixed'] = $request->req['tax_fixed'] > 0 ? round(floatval($request->req['tax_fixed']),2) : 0;
            $options['tax_percentage'] = $request->req['tax_percentage'] > 0 ? round(floatval($request->req['tax_percentage']),2) : 0;
            $options['tax_rename'] = sanitize_text_field($request->req['tax_rename']);
                       
            if(isset($request->req['paypal_page_style']))
                $options['paypal_page_style'] = $request->req['paypal_page_style'];
            
            if(isset($request->req['paypal_email']))
                $options['paypal_email'] = $request->req['paypal_email'];
            
            if(isset($request->req['stripe_api_key']))
                $options['stripe_api_key'] = $request->req['stripe_api_key'];
            
            if(isset($request->req['stripe_publish_key']))
                $options['stripe_publish_key'] = $request->req['stripe_publish_key'];
            
            if(isset($request->req['paypal_client_id']))
                $options['paypal_client_id'] = $request->req['paypal_client_id'];
            if(isset($request->req['paypal_btn_color']))
                $options['paypal_btn_color'] = $request->req['paypal_btn_color'];
            //Pass it to extensions
            do_action('rm_gopts_payment_save', $request->req);
            
            $service->set_model($model);

            $service->save_options($options);
            RM_Utilities::redirect(admin_url('/admin.php?page=' . $params['xml_loader']->request_tree->success));
        } else
        {

            $view = $parent_controller->mv_handler->setView('options_payment');
            $service->set_model($model);
            $data = $service->get_options();
            
            $options_s_api = array("id" => "rm_s_api_key_tb", "value" => $data['stripe_api_key'], "longDesc" => RM_UI_Strings::get('HELP_OPTIONS_PYMNT_STRP_API_KEY'));
            $options_s_pub = array("id" => "rm_s_publish_key_tb", "value" => $data['stripe_publish_key']);
            $gopts = new RM_Options;
            $include_stripe= $gopts->get_value_of('include_stripe');
            
            if(!RM_Utilities::is_ssl()){
                $options_s_api['disabled']= true;
                $options_s_pub['disabled']= true;
            }
            $options_pp_test_cb = array("id" => "rm_pp_test_cb", "longDesc" => RM_UI_Strings::get('HELP_OPTIONS_PYMNT_TESTMODE'));
            $options_pp_email = array("id" => "rm_pp_email_tb", "value" => $data['paypal_email'], "longDesc" => RM_UI_Strings::get('HELP_OPTIONS_PYMNT_PP_EMAIL'));
            $options_pp_pstyle = array("id" => "rm_pp_style_tb", "value" => $data['paypal_page_style'], "longDesc" => RM_UI_Strings::get('HELP_OPTIONS_PYMNT_PP_PAGESTYLE'));
            $options_pp_modern_enable = array("id"=> "rm_pp_modern_enable", "onclick" => "enable_paypal_modern_popup(this)", "value" => isset($data['paypal_modern_enable']) ? $data['paypal_modern_enable'] : '', "longDesc" => RM_UI_Strings::get('HELP_OPTIONS_PYMNT_PP_MODERN'));
            $options_pp_client_id = array("id"=> "rm_pp_modern_client_id", "value" => isset($data['paypal_client_id']) ? $data['paypal_client_id'] : '', "longDesc" => RM_UI_Strings::get('HELP_OPTIONS_PYMNT_PP_CLIENT_ID'));
            
            $layout_checked_state = array('gold' => null, 'blue' => null, 'silver' => null, 'white'=> null, 'black'=> null);
            $selected_layout = isset($data['paypal_btn_color']) ? $data['paypal_btn_color'] : 'gold';
            if ($selected_layout == 'blue'){
                $layout_checked_state['blue'] = 'checked';
            }
            else if ($selected_layout == 'silver'){
                $layout_checked_state['silver'] = 'checked';
            }
            else if ($selected_layout == 'white'){
                $layout_checked_state['white'] = 'checked';
            }
            else if ($selected_layout == 'black'){
                $layout_checked_state['black'] = 'checked';
            }
            else {
                $layout_checked_state['gold'] = 'checked';
            }


            $paypal_btn_colorhtml = '<div class="rmrow"><div class="rmfield" for="layout_radio"><label>' .
                    RM_UI_Strings::get('LABEL_OPTIONS_PAYPAL_BTN_COLOR') .
                    '</label></div><div class="rminput"><ul class="rmradio">' .
                    '<li><div id="rm_btn_gold"><div class="rmpaypalbtnimage"><img src="' . RM_IMG_URL . '/paypal-gold.png" /></div><input id="layout_radio-1" type="radio" name="paypal_btn_color" value="gold" ' . $layout_checked_state['gold'] . '>' .
                    RM_UI_Strings::get('LABEL_PAYPAL_BTN_COLOR_GOLD') .
                    '</div></li><li><div id="rm_btn_blue"><div class="rmpaypalbtnimage"><img src="' . RM_IMG_URL . '/paypal-blue.png" /></div><input id="layout_radio-2" type="radio" name="paypal_btn_color" value="blue" ' . $layout_checked_state['blue'] . '>' .
                    RM_UI_Strings::get('LABEL_PAYPAL_BTN_COLOR_BLUE') .
                    '</div></li><li><div id="rm_btn_silver"><div class="rmpaypalbtnimage"><img src="' . RM_IMG_URL . '/paypal-silver.png" /></div><input id="layout_radio-3" type="radio" name="paypal_btn_color" value="silver" ' . $layout_checked_state['silver'] . '>' .
                    RM_UI_Strings::get('LABEL_PAYPAL_BTN_COLOR_SILVER') .
                    '</div></li><li><div id="rm_btn_white"><div class="rmpaypalbtnimage"><img src="' . RM_IMG_URL . '/paypal-white.png" /></div><input id="layout_radio-4" type="radio" name="paypal_btn_color" value="white" ' . $layout_checked_state['white'] . '>' .
                    RM_UI_Strings::get('LABEL_PAYPAL_BTN_COLOR_WHITE') .
                    '</div></li><li><div id="rm_btn_black"><div class="rmpaypalbtnimage"><img src="' . RM_IMG_URL . '/paypal-black.png" /></div><input id="layout_radio-5" type="radio" name="paypal_btn_color" value="black" ' . $layout_checked_state['black'] . '>' .
                    RM_UI_Strings::get('LABEL_PAYPAL_BTN_COLOR_BLACK') .
                    '</div></li></ul></div><div class="rmnote"><div class="rmprenote"></div><div class="rmnotecontent">' .
                    RM_UI_Strings::get('HELP_OPTIONS_PAYPAL_BTN_COLOR') .
                    '</div></div></div>';

            if(null != $data['payment_gateway'] && is_array($data['payment_gateway'])){
                foreach($data['payment_gateway'] as $gateway){
                    switch($gateway){
                        case 'paypal' : 
                            unset($options_pp_test_cb['disabled']);
                            unset($options_pp_email['disabled']);
                            unset($options_pp_pstyle['disabled']);
                    }
                }
            }

            if($data['paypal_test_mode'] == 'yes')
                $options_pp_test_cb['value'] = 'yes';
            
            $pay_procs_options = array("paypal" => "<img src='" . RM_IMG_URL . "/paypal-logo.png" . "'>",
                                      "stripe" => "<img src='" . RM_IMG_URL . "/stripe-logo.png" . "'>");
            $enable_modern_paypal = 'display:none;';
            if( isset($data['paypal_modern_enable'] ) && $data['paypal_modern_enable'] != ''){
                $enable_modern_paypal = 'display:block;';
            }
            $pay_procs_configs = array("paypal" => array(
                                            new Element_Checkbox(RM_UI_Strings::get('LABEL_TEST_MODE'), "paypal_test_mode", array("yes" => ''), $options_pp_test_cb),
                                            new Element_Textbox(RM_UI_Strings::get('LABEL_PAYPAL_EMAIL'), "paypal_email", $options_pp_email),
                                            new Element_Textbox(RM_UI_Strings::get('LABEL_PAYPAL_STYLE'), "paypal_page_style", $options_pp_pstyle),
                                            new Element_Checkbox(RM_UI_Strings::get('LABEL_PAYPAL_MODERN_ENABLE'), "paypal_modern_enable", array("yes" => ''), $options_pp_modern_enable),
                                            new Element_HTML('<div class="childfieldsrow" id="rm_pp_modern_enable_childfieldsrow" style="'.$enable_modern_paypal.'">'),
                                            new Element_Textbox(RM_UI_Strings::get('LABEL_PAYPAL_CLIENT_ID'), "paypal_client_id", $options_pp_client_id),
                                            new Element_HTML("<span id='rm_pp_modern_client_error_msg' class='rm_pp_modern_client_error_msg' style='display:none;'>".__('Please fill the required field', 'registrationmagic-addon')."</span>"),
                                            new Element_HTML($paypal_btn_colorhtml),
                                            new Element_HTML('</div>')
                                            ),
                                      "stripe" => array(
                                            new Element_Textbox(RM_UI_Strings::get('LABEL_STRIPE_API_KEY'), "stripe_api_key", $options_s_api),
                                            new Element_Textbox(RM_UI_Strings::get('LABEL_STRIPE_PUBLISH_KEY'), "stripe_publish_key", $options_s_pub)
                                            )
                                     );
            
            $data['pay_procs_options'] = apply_filters('rm_extend_payprocs_options',$pay_procs_options, $data);
            $data['pay_procs_configs'] = apply_filters('rm_extend_payprocs_config',$pay_procs_configs, $data);
            $view->render($data);
        }
    }
    public function manage_ctabs($model, $service, $request, $params, $parent_controller){
        if($_POST){
            if($request->req['remove_ctabs']=='rm_options_ctabs_remove' && $request->req['rm_ctabs_index']){
                foreach($request->req['rm_ctabs_index'] as $tab_id){
                    RM_DBManager_Addon::remove_tabs($tab_id);
                }
            }
        }
        $data= new stdClass();
        $data->ctabs = RM_DBManager_Addon::get_all_tabs();
        $view= $parent_controller->mv_handler->setView('options_ctabs_manager');
        $view->render($data);
    }

    public function add_ctabs($model, $service, $request, $params, $parent_controller){
        $tabs = get_option('rm_profile_tabs_order_status');
        $all_tabs = array();
        if(!empty($tabs)){
            foreach ($tabs as $key => $value) {
                $all_tabs[]= $key;
            }
        }
        if($_POST){
            
            $data = array(
            'tab_label' => $request->req['ctab_label'],
            'tab_icon' =>  $request->req['ctab_icon'],
            'tab_class' => 'rmtab-custom',
            'tab_status' => 1,
            'tab_content' => $request->req['ctab_desc']
            );
            if(isset($request->req['tab_id'])){
                $result = RM_DBManager_Addon::update_tabs_row($data,array('tab_id'=>$request->req['tab_id']));
                $tab_id = 'rm_ctab_'.$request->req['tab_id'];
                if(!empty($all_tabs)){
                    if(in_array($tab_id, $all_tabs)){
                        $tabs[$tab_id]['label'] = $request->req['ctab_label'];
                        $tabs[$tab_id]['icon'] = $request->req['ctab_icon'];
                    }
                }
                update_option('rm_profile_tabs_order_status',$tabs);
            }
            else{
                $result = RM_DBManager_Addon::insert_tabs_row($data);    
            }
            RM_Utilities::redirect(admin_url('/admin.php?page=' . $params['xml_loader']->request_tree->success));
        }
        $data= new stdClass();
        if(isset($request->req['tab_id'])){
        $data = RM_DBManager_Addon::get_tabs_row($request->req['tab_id']);
        }
        $view= $parent_controller->mv_handler->setView('options_ctabs_add');
        $view->render($data);   
    }
    public function manage_invoice($model, RM_Setting_Service $service, $request, $params, $parent_controller){
        if($_POST)
        {
            $options = array();
            
            $options['enable_invoice'] = isset($request->req['enable_invoice']) ? "yes" : null;
            $options['invoice_company_logo'] = isset($request->req['invoice_company_logo']) ? $request->req['invoice_company_logo'] : null;
            $options['invoice_company_name'] = isset($request->req['invoice_company_name']) ? $request->req['invoice_company_name'] : null;
            $options['invoice_company_address'] = isset($request->req['invoice_company_address']) ? $request->req['invoice_company_address'] : null;
            $options['invoice_company_contact_no'] = isset($request->req['invoice_company_contact_no']) ? $request->req['invoice_company_contact_no'] : null;
            $options['invoice_company_email'] = isset($request->req['invoice_company_email']) ? $request->req['invoice_company_email'] : null;
            $options['invoice_enable_footer'] = isset($request->req['invoice_enable_footer']) ? "yes" : null;
            $options['invoice_footer_text'] = isset($request->req['invoice_footer_text']) ? $request->req['invoice_footer_text'] : null;
            $options['invoice_company_vat'] = isset($request->req['invoice_company_vat']) ? $request->req['invoice_company_vat'] : null;
            $options['invoice_left_margin'] = isset($request->req['invoice_left_margin']) ? $request->req['invoice_left_margin'] : null;
            $options['invoice_top_margin'] = isset($request->req['invoice_top_margin']) ? $request->req['invoice_top_margin'] : null;
            $options['invoice_right_margin'] = isset($request->req['invoice_right_margin']) ? $request->req['invoice_right_margin'] : null;
            $options['enable_user_invoice'] = isset($request->req['enable_user_invoice']) ? "yes" : null;
            $options['enable_email_invoice'] = isset($request->req['enable_email_invoice']) ? "yes" : null;
            $options['invoice_font'] = isset($request->req['invoice_font']) ? $request->req['invoice_font'] : 'helvetica'; 
            if(!empty($options)){
                foreach ($options as $key => $value){
                    //echo $key. '     ---------------------   '.$value;
                    update_option($key,$value, true);
                }
            }
            $service->set_model($model);

            $service->save_options($options);
            RM_Utilities::redirect(admin_url('/admin.php?page=rm_options_manage'));
        
        }
        $view = $parent_controller->mv_handler->setView('options_invoice_manager');
        $service->set_model($model);
        $data = $service->get_options();
        $view->render($data);
        
    }
}