<?php

/**
 * Class for payments controller
 * 
 * Manages the payments related operations in the backend.
 *
 * @author CMSHelplive
 */
class RM_Payments_Controller_Addon
{

    public function manage($model, $service, $request, $params, $parent_controller)
    {
        $data = new stdClass();
        
        $filter= new RM_Payments_Filter($request,$service);
        $form_id= $filter->get_form();
        $data->forms = RM_Utilities::get_forms_dropdown($service);
        $data->fields = $service->get_all_form_fields($form_id);
        $data->filter= $filter;
        $data->rm_slug = $request->req['page'];
        $data->payments= $filter->get_records();
        $data->is_filter_active = $filter->is_active();
        $view = $parent_controller->mv_handler->setView('payments_manager');
        $view->render($data);
        
    }
    public function view($model, $service, $request, $params, $parent_controller)
    {   
        if (isset($request->req['rm_submission_id']))
        {
            if (!$model->load_from_db($request->req['rm_submission_id']))
            {
                $view = $parent_controller->mv_handler->setView('show_notice');
                $data = RM_UI_Strings::get('MSG_DO_NOT_HAVE_ACCESS');
                $view->render($data);
            } else
            {
                $child_id = $model->get_child_id();
                if($child_id != 0){
                    $request->req['rm_submission_id'] = $model->get_last_child();
                    return $parent_controller->view($model, $service, $request, $params);
                }
                if (isset($request->req['rm_action']) && $request->req['rm_action'] == 'delete')
                {
                    $request->req['rm_form_id'] = $model->get_form_id();
                    $request->req['rm_selected'] = $request->req['rm_submission_id'];
                    $parent_controller->remove($model, $service, $request, $params);
                    unset($request->req['rm_selected']);
                } elseif (isset($request->req['rm_action']) && $request->req['rm_action'] == 'activate' && isset($request->req['rm_user_id']))
                {
                    $submission_id = $request->req['rm_submission_id'];
                    $user_model = new RM_User;
                    $user_id = $request->req['rm_user_id'];
                    $user_model->activate_user($user_id);
                    RM_Utilities::redirect('?page=rm_payments_view&rm_submission_id='.$submission_id);
                    
                }elseif (isset($request->req['rm_action']) && $request->req['rm_action'] == 'deactivate' && isset($request->req['rm_user_id']))
                {
                    $submission_id = $request->req['rm_submission_id'];
                    $user_model = new RM_User;
                    $user_id = $request->req['rm_user_id'];
                    $user_model->deactivate_user($user_id);
                    RM_Utilities::redirect('?page=rm_payments_view&rm_submission_id='.$submission_id);
                }else
                {
                    $settings = new RM_Options;

                    $data = new stdClass();

                    $data->submission = $model;

                    $data->payment = $service->get('PAYPAL_LOGS', array('submission_id' => $service->get_oldest_submission_from_group($model->get_submission_id())), array('%d'), 'row', 0, 99999);

                    if ($data->payment != null)
                    {
                        $data->payment->total_amount = $settings->get_formatted_amount($data->payment->total_amount, $data->payment->currency);

                        if ($data->payment->log)
                            $data->payment->log = maybe_unserialize($data->payment->log);
                    }
                    $form = new RM_Forms();
                    $form->load_from_db($model->get_form_id());
                    $data->form_id=$model->get_form_id();
                    $fields= $service->get_all_form_fields($model->get_form_id());
                    $data->email_field_id=$fields['0']->field_id;
                    $form_type = $form->get_form_type() == "1" ? __("Registration",'registrationmagic-addon') : __("Non WP Account",'registrationmagic-addon');
                    $data->form_type = $form_type;
                    $data->form_type_status = $form->get_form_type();
                    $data->form_name = $form->get_form_name();
                    $data->form_is_unique_token = $form->get_form_is_unique_token();
                    $data->latest_payments = RM_DBManager::get_recents_payments_by_formid($data->submission->form_id, $data->submission->submission_id);
                    $data->user_payments = RM_DBManager::get_recents_payments_by_email($data->submission->user_email, $data->submission->submission_id);
                    // Life time revenue
                    $data->total_revenue  = RM_DBManager::get_total_revenue_by_user_email($data->submission->user_email);
                    if(!$data->total_revenue){
                        $data->total_revenue = 0;
                    }
                    //Invoice Enable
                    $enable_invoice = get_option('enable_invoice');
                    if( $enable_invoice == 'yes'){
                       $data->enable_invoice = true; 
                    }
                    else{
                       $data->enable_invoice = false;  
                    }
                    
                    // Submissions Notes
                    $submission_service = new RM_Submission_Service;
                    $data->notes = $submission_service->get_notes($model->get_submission_id());
                    $i = 0;
                    if (is_array($data->notes)){
                        foreach ($data->notes as $note)
                        {
                            $udata = get_userdata($note->published_by);
                            
                            if(!$udata) //This means user has been deleted. Place a dummy author name.
                                $data->notes[$i]->author = 'user'; 
                            else
                                $data->notes[$i]->author = $udata->display_name;
                            
                            if ($note->last_edited_by)
                                $data->notes[$i++]->editor = get_userdata($note->last_edited_by)->display_name;
                            else
                                $data->notes[$i++]->editor = null;
                        }
                    }
                    $view = $parent_controller->mv_handler->setView('payments_view');

                    $view->render($data);
                }
            }
        } else
            throw new InvalidArgumentException(RM_UI_Strings::get('MSG_INVALID_SUBMISSION_ID'));
    }
    
    
    public function download_invoice($model, $service, $request, $params, $parent_controller){
        if (isset($request->req['rm_submission_id']))
        {
            $file_name = 'Invoice-submission-'.$request->req['rm_submission_id'].'.pdf';
            if(isset($request->req['invoice_id'])){
                $file_name = 'Invoice-'.$request->req['invoice_id'].'.pdf';
            }
            $model->load_from_db($request->req['rm_submission_id']);
            if(empty($model->get_submission_id())){
                _e('Submission does not exist.','registrationmagic-addon');
                die;
            }
            // Check if submission belongs to current user or user is admin
            if(!current_user_can('manage_options')){
                $user= wp_get_current_user();
                if(empty($user->ID)){
                    _e('You are not allowed to access submission data.','registrationmagic-addon');
                    die;
                }
                $submission_user= get_user_by('email',$model->get_user_email());
                if(empty($submission_user) || $submission_user->ID!=$user->ID){
                    _e('You are not allowed to access submission data.','registrationmagic-addon');
                    die;
                }
            }
            $form = new RM_Forms();
            $form->load_from_db($model->get_form_id());
            $outputconf = array(
                'name' => $file_name,
                'type' => isset($request->req['type']) ? $request->req['type'] : 'D'
            );
            $service->output_pdf_for_invoice($model,$outputconf);
            die();

        } else
            throw new InvalidArgumentException(RM_UI_Strings::get('MSG_INVALID_SUBMISSION_ID'));
    }
    
    public function sent_invoice($model, $service, $request, $params, $parent_controller){
        if(isset($request->req['rm_form_id']) && isset($request->req['rm_payment_id'])){
            
            $form = new RM_Forms();
            $form->load_from_db($request->req['rm_form_id']);
            
            $sent = RM_Email_Service::notify_payment_invoice_to_user($request->req['rm_email_id'],$form, $request->req['rm_submission_id'] );
            if($sent){
                echo 'success';
            }
            else{
                echo 'failed';
            }
        }
        
    }
    
}