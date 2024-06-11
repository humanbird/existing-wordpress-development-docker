<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of class_rm_form_settings_controller
 *
 * @author Cmshelplive
 */
class RM_Form_Settings_Controller_Addon {
    
    function access_control($model, $service, $request, $params, $parent_controller) {
        $next_page =  (isset($_GET['rdrto']) && $_GET['rdrto']) ? $_GET['rdrto'] : "rm_form_sett_manage";
        if ($parent_controller->mv_handler->validateForm("form_sett_access_control")) 
            {
           
            //Create access control object from request.
            $access_control = new stdClass;
            
            if(isset($request->req['form_actrl_date_cb']))
            {
                if($request->req['form_actrl_date_type'] == 'date')
                {
                    $ll = trim((string)$request->req['form_actrl_date_ll_date']);
                    $ul = trim((string)$request->req['form_actrl_date_ul_date']);
                }
                else if($request->req['form_actrl_date_type'] == 'diff')
                {
                    $ll = trim((string)$request->req['form_actrl_date_ll_diff']);
                    $ul = trim((string)$request->req['form_actrl_date_ul_diff']);
                }
                $access_control->date = (object)array('question'=>trim((string)$request->req['form_actrl_date_question']),
                        'type'=>$request->req['form_actrl_date_type'],
                        'lowerlimit' => $ll,
                        'upperlimit' => $ul);
            }
            
            if(isset($request->req['form_actrl_pass_cb']))
            {
                $passphrases = trim((string)$request->req['form_actrl_pass_passphrase'], " \t\n\r\0\x0B|");
                //format it nicely :)
                $passphrases = explode("|", (string)$passphrases);
                $passphrases = array_map('trim', $passphrases);
                $passphrases = implode(' | ', $passphrases);
                $access_control->passphrase = (object)array('question'=>trim((string)$request->req['form_actrl_pass_question']),
                        'passphrase'=>$passphrases);
            }

            if(isset($request->req['form_actrl_login']))
            {
                $access_control->login = 1;
            } 
            
            if(isset($request->req['form_actrl_role_cb']))
            {
                $r = isset($request->req['form_actrl_roles'])?$request->req['form_actrl_roles']:null;
                $access_control->roles = (!$r) ? null:$r;
            } 
            
            if(isset($request->req['form_actrl_domain_cb']))
            {
                $r = isset($request->req['form_actrl_domain'])? sanitize_text_field($request->req['form_actrl_domain']):null;
                $access_control->domain = (!$r) ? null:$r;
            }
            
            if(isset($request->req['form_actrl_fail_msg']))
            {
                $access_control->fail_msg = isset($request->req['form_actrl_fail_msg'])?trim((string)$request->req['form_actrl_fail_msg']):'';
            }
            
            $options['access_control'] = $access_control;
            
            if (isset($request->req['rm_form_id'])) {
                $model->load_from_db($request->req['rm_form_id']);
                $model->set($options);
                if ($model->validate_access_control('form_sett_access_control')) {
                    $model->update_into_db();
                
                RM_Utilities::redirect("?page={$next_page}&rm_form_id=".$request->req['rm_form_id']);
                    return;
                } else
                    $visited = true;
            } else {
                echo '<div class="rmnotice">' . RM_UI_Strings::get('MSG_FS_NOT_AUTHORIZED') . '</div>';
                return;
            }
        }
        if (isset($request->req['rm_form_id'])) {
            $data = new stdClass();
            $data->next_page = $next_page;
            $view = $parent_controller->mv_handler->setView('form_access_control_sett');
            if (!isset($visited))
                $model->load_from_db($request->req['rm_form_id']);
            $data->model = $model;
            if(isset($data->model->form_options->access_control))
            {
                $data->access_control = $data->model->form_options->access_control;
            }
            else
            {
                $access_control = new stdClass;
                $access_control->date = null;
                $access_control->passphrase = null;
                $access_control->roles = null;
                $data->access_control = $access_control;
            }
            $data->all_roles = RM_Utilities::user_role_dropdown();
        } else {
            $data = RM_UI_Strings::get('MSG_FS_NOT_AUTHORIZED');
            $view = $parent_controller->mv_handler->setView('show_notice');
        }
        $view->render($data);
    }
    
    function aweber($model, $service, $request, $params, $parent_controller) {
        $next_page =  (isset($_GET['rdrto']) && $_GET['rdrto']) ? $_GET['rdrto'] : "rm_form_sett_manage";
        if ($parent_controller->mv_handler->validateForm("form_sett_aweber")) {
            $options = array();
            $options['aw_list'] = $request->req['aw_list'];
            $options['aw_relations'] = $service->get_aw_mapping($request->req);
            $options['form_is_opt_in_checkbox_aw'] = isset($request->req['form_is_opt_in_checkbox_aw']) ? $request->req['form_is_opt_in_checkbox_aw'] : null;
            $options['form_opt_in_text_aw'] = isset($request->req['form_opt_in_text_aw']) ? $request->req['form_opt_in_text_aw'] : null;
           $options['enable_aweber'] = isset($request->req['enable_aweber']) ? $request->req['enable_aweber'] : null;
             $options['form_opt_in_default_state_aw'] = isset($request->req['form_opt_in_default_state_aw']) ? $request->req['form_opt_in_default_state_aw'] : null;
           
            if (isset($request->req['rm_form_id']) && (int)$request->req['rm_form_id']) {
                $model->load_from_db($request->req['rm_form_id']);
                $model->set($options);
                $model->update_into_db();
                //$parent_controller->manage($model, $service, $request);
                RM_Utilities::redirect("?page={$next_page}&rm_form_id=".$request->req['rm_form_id']);
                return;
            } else {
               
                echo '<div class="rmnotice">' . RM_UI_Strings::get('MSG_FS_NOT_AUTHORIZED') . '</div>';
                return;
            }
        }

        if (isset($request->req['rm_form_id']) && (int)$request->req['rm_form_id']) {
            $data = new stdClass();
            $data->next_page = $next_page;
            $data->form_id = $request->req['rm_form_id'];
            $model->load_from_db($request->req['rm_form_id']);
            $data->aw_form_list = $model->form_options->aw_list;
             try
             {
            $data->field_array = $service->aw_field_mapping($data->form_id, $model->form_options);
            $data->model = $model;
             $data->error=null;
             $aw_list = $service->get_list();
             if($aw_list==null)
              $data->error= RM_UI_Strings::get('AW_ERROR');   
             }
             catch(Exception $e)
             {
                 $data->aw_list=null;
                 $data->error= RM_UI_Strings::get('AW_ERROR');
             } 
            
            $data->aw_list[''] = RM_UI_Strings::get('SELECT_LIST');
            if(isset($aw_list))
            {
                foreach ($aw_list as $l=>$date_list) {
                    $data->aw_list[$date_list['id']] = $date_list['name'];
                }
            }
           
            $view = $parent_controller->mv_handler->setView('form_sett_aw');
        }else{
            
            $data = RM_UI_Strings::get('MSG_FS_NOT_AUTHORIZED');
            $view = $parent_controller->mv_handler->setView('show_notice');
        }


        $view->render($data);
    }
    
    function override($model, $service, $request, $parent_controller) {
         $next_page =  (isset($_GET['rdrto']) && $_GET['rdrto']) ? $_GET['rdrto'] : "rm_form_sett_manage";
         if ($parent_controller->mv_handler->validateForm("form_sett_general")) {
            $options = array();
            $options['display_progress_bar'] = isset( $request->req['display_progress_bar']) ? $request->req['display_progress_bar'] : null;
            $options['user_auto_approval'] = isset($request->req['user_auto_approval']) ? $request->req['user_auto_approval'] : null;
            $options['enable_captcha'] = isset($request->req['enable_captcha']) ? $request->req['enable_captcha'] : null;
            $options['sub_limit_antispam'] = isset( $request->req['sub_limit_antispam']) ? $request->req['sub_limit_antispam'] : null;
            $options['admin_notification'] = isset($request->req['admin_notification']) ? "yes" : null;
            if (isset($request->req['resp_emails']))
                $options['admin_email'] = implode(",", $request->req['resp_emails']);        
                
                $model->load_from_db($request->req['rm_form_id']);
                $model->set($options);
                $model->update_into_db();
                RM_Utilities::redirect('?page='.$next_page.'&rm_form_id='.$request->req['rm_form_id']);
                return;
           
        }
         
         $data = new stdClass();
         $data->next_page = $next_page;
        $view = $parent_controller->mv_handler->setView('form_sett_override');
        if (isset($request->req['rm_form_id']) && (int)$request->req['rm_form_id'])
            $model->load_from_db($request->req['rm_form_id']);
        $data->model = $model;
        $view->render($data);
    }
    
}