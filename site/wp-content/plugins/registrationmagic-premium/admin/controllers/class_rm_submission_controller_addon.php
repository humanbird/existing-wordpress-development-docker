<?php

/**
 * Class for submissions controller
 * 
 * Manages the submissions related operations in the backend.
 *
 * @author CMSHelplive
 */
class RM_Submission_Controller_Addon
{

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
                    
                
                if (isset($request->req['rm_action']) && isset($_POST['rm_action']) && $request->req['rm_action'] == 'delete')
                {
                    $request->req['rm_form_id'] = $model->get_form_id();
                    $request->req['rm_selected'] = $request->req['rm_submission_id'];
                    $parent_controller->remove($model, $service, $request, $params);
                    unset($request->req['rm_selected']);
                }
                 
                else
                {  
                    $settings = new RM_Options;

                    $data = new stdClass();

                    $data->submission = $model;

                    $data->tax_label = $settings->get_value_of('tax_rename');
                    
                    /*
                     * Change read status of submission
                     */
                    $service->set_model($data->submission);
                    $service->change_read_status(1);
                    
                    
                    if (isset($request->req['rm_user_email']))
                        {
                            $email=$request->req['rm_user_email'];
                            if($data->submission->is_blocked_email($email) && $request->req['rm_action']=='unblock_email')
                            {
                                $data->submission->unblock_email($email);
                            }
                            else if(!$data->submission->is_blocked_email($email) && $request->req['rm_action']=='block_email')
                            {
                                 $data->submission->block_email($email);
                            }
                            else
                            {
                                
                            }
                          
                        } 
                         if (isset($request->req['rm_sub_ip']))
                        {
                            $ip=$request->req['rm_sub_ip'];
                            if($data->submission->is_blocked_ip($ip) && $request->req['rm_action']=='unblock_ip')
                            {
                                $data->submission->unblock_ip($ip);
                            }
                            else if(!$data->submission->is_blocked_ip($ip) && $request->req['rm_action']=='block_ip')
                            {
                                 $data->submission->block_ip($ip);
                            }
                            else
                            {
                                
                            }
                          
                        } 
                    $data->payment = $service->get('PAYPAL_LOGS', array('submission_id' => $service->get_oldest_submission_from_group($model->get_submission_id())), array('%d'), 'row', 0, 99999);

                    if ($data->payment != null)
                    {
                        $data->payment->total_amount = $settings->get_formatted_amount($data->payment->total_amount, $data->payment->currency);
                        $bill = maybe_unserialize($data->payment->bill);
                        if(isset($bill->tax)) {
                            $data->payment->tax = $settings->get_formatted_amount($bill->tax, $data->payment->currency);
                        }

                        if ($data->payment->log)
                        {
                            $data->payment->log = maybe_unserialize($data->payment->log);
                            $data->payment->log['payment_status']= $data->payment->status;
                        }
                            
                    }

                    $data->notes = $service->get_notes($model->get_submission_id());
                    $i = 0;
                    if (is_array($data->notes))
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
                    /*
                     * Check submission type
                     */
                    $form = new RM_Forms();
                    $form->load_from_db($model->get_form_id());
                    $form_type = $form->get_form_type() == "1" ? __('Registration','registrationmagic-addon') : __('Non WP Account','registrationmagic-addon');
                    $data->form_type = $form_type;
                    $data->form_type_status = $form->get_form_type();
                    //$data->form_name = $form->get_form_name();
                    $data->form_is_unique_token = $form->get_form_is_unique_token();
                    $rm_sr=new RM_Services;
                    $related_subs=$rm_sr->get_submissions_by_email($data->submission->get_user_email());
                    if(is_array($related_subs))
                        $data->related=count($related_subs);
                    if($data->related >0)
                    {
                        $data->related=$data->related-1;
                    }
                    else
                        $data->related=0;
         
                   
                  
         /*
                     * User details if form is registration type
                     */
                    if ($form->get_form_type() == "1")
                    {
                        $email = $model->get_user_email();
                        if ($email != "")
                        {
                            $user = get_user_by('email', $email);
                            $data->user = $user;
                        }
                    }
                    //var_dump(maybe_unserialize($data->submission->data));
                    $view = $parent_controller->mv_handler->setView('view_submission');

                    $view->render($data);
                }
            }
        } else
            throw new InvalidArgumentException(RM_UI_Strings::get('MSG_INVALID_SUBMISSION_ID'));
    }
    
    public function print_pdf($model, $service, $request, $params, $parent_controller)
    {
        if (isset($request->req['rm_submission_id']))
        {
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

            $child_id = $model->get_child_id();
            if($child_id != 0){
                $request->req['rm_submission_id'] = $model->get_last_child();
                $parent_controller->print_pdf($model, $service, $request, $params);
                die();
            }
            // Setting PDF file name based on form ID and submission ID
            $form = new RM_Forms();
            $form->load_from_db($model->get_form_id());
            $file_name = $form->get_form_name().'_'.$form->get_form_id().'_Submission_'.$model->get_submission_id().'.pdf';
            $outputconf = array(
                'name' => $file_name,
                'type' => isset($request->req['type']) ? $request->req['type'] : 'D'
            );
            $service->output_pdf_for_submission($model,$outputconf);
            die();

        } else
            throw new InvalidArgumentException(RM_UI_Strings::get('MSG_INVALID_SUBMISSION_ID'));
    }
    
    public function mark_all_read($model, RM_Services $service, $request, $params)
    {
        $form_id= isset($request->req['rm_form_id']) ? $request->req['rm_form_id'] : null;
        
        $options = new stdClass;
        $options->form_id = $form_id;
        $options->read_status = RM_SUB_STATUS_READ;
        $options->filter = null;        
        $service->update_read_status($options);

        RM_Utilities::redirect('?page=rm_submission_manage&rm_form_id='.$form_id);
    
    }

    public function export($model, RM_Services $service, $request, $params)
    {   
        $filter= new RM_Submission_Filter($request,$service);        
        
        $submissions = $service->get_submissions_to_export($filter);
        $csv = $service->create_csv($submissions);

        $service->download_file($csv);

        unlink($csv) or die(__("Can not unlink file",'registrationmagic-addon'));
    }

}