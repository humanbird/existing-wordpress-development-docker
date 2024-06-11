<?php
class RM_Reports_Controller_Addon
{

    public $mv_handler;

    function __construct()
    {
        $this->mv_handler = new RM_Model_View_Handler();
    }
    public function submission_export($model, $service, $request, $params){
        if($_POST && (isset($request->req['rm_filter_date']) || isset($request->req['rm_form_id']))){
            $filter_date = $request->req['rm_filter_date'];
            if($filter_date){
                $date = explode('-',(string)$filter_date);
                $start_date = $date[0];
                $end_date = $date[1];
            }
            $req_data  = new stdClass;
            $req_data->start_date = $start_date;
            $req_data->end_date = $end_date;
            $req_data->filter_date = $filter_date;
            $req_data->form_id = $request->req['rm_form_id'];
            $parameter = $service->generate_reports_data($req_data,0);
            $submission_ids = $service->get_submission($parameter,0);
            if(empty($submission_ids->submissions)) return false;
            $submission_id = array();
            foreach($submission_ids->submissions as $submission){
               $submission_id[] = $submission->submission_id; 
            }
            $submissions = $service->prepare_submission_export_data($req_data,$submission_id);
            $csv = $service->create_csv($submissions,'rm_reports_submissions');

        $service->download_file($csv);

        unlink($csv) or die(__("Can not unlink file",'registrationmagic-addon'));
        }
    }
    public function attachments($model, $service, $request, $params){
        $data = new stdClass;
        $data->forms = RM_Utilities::get_forms_dropdown($service);
        $req_data  = new stdClass;
        if($_GET && (isset($request->req['rm_filter_date']) || isset($request->req['rm_form_id']))){
            $filter_date = $request->req['rm_filter_date'];
            if($filter_date){
                $date = explode('-',(string)$filter_date);
                $start_date = $date[0];
                $end_date = $date[1];
            }
            $req_data->start_date = $start_date;
            $req_data->end_date = $end_date;
            $req_data->filter_date = $filter_date;
            $req_data->form_id = $request->req['rm_form_id'];
            
            $data->req = $req_data;
        }else{

            $req_data->start_date = date('Y-m-d', strtotime(' -6 day'));
            $req_data->end_date = date('Y-m-d');
            $req_data->filter_date = date('Y/m/d', strtotime(' -6 day')).'-'.date('Y/m/d');
            $req_data->form_id = $service->get('FORMS', 1, array('%d'), 'var', 0, 15, $column = 'form_id', null, true);
            $data->req = $req_data;
        }
        $parameter = $service->generate_reports_data($req_data,0);
        $attachment = $service->attachment_manage($parameter);
        $data->attached_files = $attachment;
        $view = $this->mv_handler->setView("reports_attachments");
        $view->render($data);
    }
    
    public function attachments_download_all($model, $service, $request, $params)
    {   
        if($_POST && (isset($request->req['rm_filter_date']) || isset($request->req['rm_form_id']))){
            $filter_date = $request->req['rm_filter_date'];
            if($filter_date){
                $date = explode('-',(string)$filter_date);
                $start_date = $date[0];
                $end_date = $date[1];
            }
            $req_data  = new stdClass;
            $req_data->start_date = $start_date;
            $req_data->end_date = $end_date;
            $req_data->filter_date = $filter_date;
            $req_data->form_id = $request->req['rm_form_id'];
        }
        if (isset($request->req['rm_form_id']))
            $form_id = $request->req['rm_form_id'];
        else
            throw new InvalidArgumentException(__('No Form ID is provided to ','registrationmagic-addon') . __CLASS__ . "::" . __FUNCTION__ . ".");
        $parameter = $service->generate_reports_data($req_data,0);
        $attachments = $service->attachment_manage($parameter);
        
        $attachment_service = new RM_Attachment_Service;
        if ($attachments->attachments)
        {
            $zipped_file = $attachment_service->get_zip($attachments->attachments);
        
            if(!$zipped_file)
                return;
        }
        else
            return;

        $download = $service->download_file($zipped_file);
        
        return $download;
    }
    public function payments($model, $service, $request, $params){
        $data = new stdClass;
        $data->forms = RM_Utilities::get_forms_dropdown($service);
        $req_data  = new stdClass;
        if($_GET && (isset($request->req['rm_filter_date']) || isset($request->req['rm_form_id']))){
            $filter_date = $request->req['rm_filter_date'];
            if($filter_date){
                $date = explode('-',(string)$filter_date);
                $start_date = $date[0];
                $end_date = $date[1];
            }
            $req_data->start_date = $start_date;
            $req_data->end_date = $end_date;
            $req_data->filter_date = $filter_date;
            $req_data->form_id = $request->req['rm_form_id'];
        }else{
            $req_data->start_date = date('Y-m-d', strtotime(' -6 day'));
            $req_data->end_date = date('Y-m-d');
            $req_data->filter_date = date('Y/m/d', strtotime(' -6 day')).'-'.date('Y/m/d');
            $req_data->form_id = 'all';
        }
        $data->req = $req_data;
        $parameter = $service->generate_reports_data($req_data,5);
        if(isset($request->req['rm_status'])){
            $data->req->status = $request->req['rm_status'];
        }
        else{
            $data->req->status = 'all';
        }
        $payments_data = $service->get_payments_with_submissions($parameter,$data->req->status,5);
        
        $data->payments = $payments_data->payments;
        $data->payments_count = $payments_data->payments_count;
        $data->payments_chart = $payments_data->payments_chart;
        $view = $this->mv_handler->setView("reports_payments");
        $view->render($data);
    }
    public function payments_download($model, $service, $request, $params){
        $data = new stdClass;
        $data->forms = RM_Utilities::get_forms_dropdown($service);
        $req_data  = new stdClass;
        if($_GET && (isset($request->req['rm_filter_date']) || isset($request->req['rm_form_id']))){
            $filter_date = $request->req['rm_filter_date'];
            if($filter_date){
                $date = explode('-',(string)$filter_date);
                $start_date = $date[0];
                $end_date = $date[1];
            }
            $req_data->start_date = $start_date;
            $req_data->end_date = $end_date;
            $req_data->filter_date = $filter_date;
            $req_data->form_id = $request->req['rm_form_id'];
        }else{
            $req_data->start_date = date('Y-m-d', strtotime(' -6 day'));
            $req_data->end_date = date('Y-m-d');
            $req_data->filter_date = date('Y/m/d', strtotime(' -6 day')).'-'.date('Y/m/d');
            $req_data->form_id = 'all';
        }
        $data->req = $req_data;
        $parameter = $service->generate_reports_data($req_data,0);
        if(isset($request->req['rm_status'])){
            $data->req->status = $request->req['rm_status'];
        }
        else{
            $data->req->status = 'all';
        }
        $payments = $service->get_payments_with_submissions($parameter,$data->req->status,0);
        $payments_data = $service->prepare_payments_export_data($payments);
        $csv = $service->create_csv($payments_data,'rm_reports_payments');
        $service->download_file($csv);

        unlink($csv) or die(__("Can not unlink file",'registrationmagic-addon'));
    }
    
    public function form_compare($model, $service, $request, $params){
        $data = new stdClass();
        $data->forms = RM_Utilities::get_forms_dropdown($service);
        if(count($data->forms) < 2){
            $view = $this->mv_handler->setView("reports_compare_notice");
            $view->render($data);
            return;
        }
        $req_data  = new stdClass;
        if($_GET && (isset($request->req['rm_filter_date']) || ( isset($request->req['rm_form_id_1']) && isset($request->req['rm_form_id_2']) ) ) ){
            $filter_date = $request->req['rm_filter_date'];
            if($filter_date){
                $date = explode('-',(string)$filter_date);
                $start_date = $date[0];
                $end_date = $date[1];
            }
            $req_data->start_date = $start_date;
            $req_data->end_date = $end_date;
            $req_data->filter_date = $filter_date;
            $req_data->forms_ids = $request->req['forms_ids'];
            $data->req = $req_data;
            
        }else{
            $req_data->start_date = date('Y-m-d', strtotime(' -6 day'));
            $req_data->end_date = date('Y-m-d');
            $req_data->filter_date = date('Y/m/d', strtotime(' -6 day')).'-'.date('Y/m/d');
            $req_data->forms_ids = $service->default_selected_forms($service, 2);
            $data->req = $req_data;
            
            
        }
        $para = $service->generate_reports_data_compare_multiselect($data->req);
        $data->submissions = $service->get_submission_multiple_compare($para);
        $data->forms_ids = $req_data->forms_ids;
        
        $view = $this->mv_handler->setView("reports_compare");
        $view->render($data);
    }
    public function login_dwonload($model, $service, $request, $params){
        $data = new stdClass;
        $data->forms = RM_Utilities::get_forms_dropdown($service);
        $req_data  = new stdClass;
        if($_GET && (isset($request->req['rm_filter_date']) || isset($request->req['rm_login_status']))){
            $filter_date = $request->req['rm_filter_date'];
            if($filter_date){
                $date = explode('-',(string)$filter_date);
                $start_date = $date[0];
                $end_date = $date[1];
            }
            $req_data->start_date = $start_date;
            $req_data->end_date = $end_date;
            $req_data->filter_date = $filter_date;
            $req_data->status = $request->req['rm_login_status'];
            $data->req = $req_data;
            
        }else{
            $req_data->start_date = date('Y-m-d', strtotime(' -6 day'));
            $req_data->end_date = date('Y-m-d');
            $req_data->filter_date = date('Y/m/d', strtotime(' -6 day')).'-'.date('Y/m/d');
            $req_data->status = 'all';
            $data->req = $req_data;
            
        }
        $parameter = $service->generate_login_parameter($req_data);
        $login_data = $service->get_logins($parameter,0); 
        $export_data = $service->prepare_login_export_data($login_data);
        $csv = $service->create_csv($export_data, 'rm_reports_login');
        $service->download_file($csv);
    }
    public function notifications($model, $service, $request, $params, $parent_controller){
        $data = new stdClass();
        if($_POST){
            if($request->req['notification_remove']=='rm_reports_notification_remove' && $request->req['rm_notification_index']){
                foreach($request->req['rm_notification_index'] as $notification_id){
                    $service->update_edit_delete_cron($notification_id, 'delete');
                    RM_DBManager::remove_row("REPORTS_NOTIFICATIONS", $notification_id);
                    
                }
            }
        }
        $data->notifications = RM_DBManager::get_reports();
        $view = $this->mv_handler->setView("reports_notifications");
        $view->render($data);
    }
    public function notification_add($model, $service, $request, $params, $parent_controller){
        $data = new stdClass();
        $data->forms = RM_Utilities::get_forms_dropdown($service);
        $data->login_status = array('all'=> 'All','success'=>'Success','failure'=>'Failure');
        $data->payment_status = array('all'=> 'All','Completed'=>'Completed','Pending'=>'Pending','Canceled'=>'Canceled','Refunded'=>'Refunded');
        $data->schedule = array('hourly'=> __('Hourly','registrationmagic-addon'),'twicedaily'=>__('Twice daily','registrationmagic-addon'),'daily'=>__('Daily','registrationmagic-addon'),'weekly'=>__('Weekly','registrationmagic-addon'), 'rm_monthly'=>__('Monthly','registrationmagic-addon'));
        $sent_to = isset($request->req['sent_to']) && $request->req['sent_to']!= null ? $request->req['sent_to'] : 'me';
        //$last_exe = isset($request->req['last_exe']) &&  $request->req['last_exe'] != null ? $request->req['last_exe'] : time();
        $last_exe = '';
        $admin_id = get_current_user_id();
        $receivers = null;
        if($sent_to == 'individual'){
            $receivers = isset($request->req['individual_receiver'])? $request->req['individual_receiver'] : null;
        }else{
            $receivers = null;
        }
        
        
        if($_POST){
            $form_data = array(
              'id'=> isset($request->req['id']) ? $request->req['id'] : '',
              'notification_title'=>$request->req['notification_title'],
              'notification_type'=>$request->req['notification_type'],
              'form_id'=>isset($request->req['form_id']) ? $request->req['form_id'] : null,
              'login_status'=>isset($request->req['login_status']) ? $request->req['login_status'] : null,
              'payment_status'=>isset($request->req['payment_status']) ? $request->req['payment_status'] : null,
              'sent_to'=> $sent_to,
              'admin_id' => $admin_id,
              'receivers'=> $receivers,
              'cron_type'=>$request->req['cron_type'],
              'first_exe'=>strtotime(get_gmt_from_date($request->req['first_exe'])),
              'last_exe'=>$last_exe,
              'enable'=> isset($request->req['enable']) ? $request->req['enable'] : 1,
              'email_subject'=>$request->req['email_subject'],
              'email_content'=>$request->req['email_content']
            );
            if(isset($request->req['id'])){
                RM_DBManager::update_row('REPORTS_NOTIFICATIONS',$request->req['id'] , $form_data, $form_data);
                $service->update_edit_delete_cron($request->req['id'], 'edit');
                //RM_Utilities::redirect(admin_url('/admin.php?page=rm_reports_notification_add&rm_notification_id='.$request->req['id']));
                RM_Utilities::redirect(admin_url('/admin.php?page=rm_reports_notifications'));
            }
            else{
                $id = RM_DBManager::insert_row('REPORTS_NOTIFICATIONS', $form_data, $form_data);
                $service->update_edit_delete_cron($id, 'add');
                RM_Utilities::redirect(admin_url('/admin.php?page=rm_reports_notifications'));
            }
            
        }
        
        $notification = new stdClass();
        if(isset($request->req['rm_notification_id'])){
           $id = $request->req['rm_notification_id'];
           $notification = RM_DBManager::get_row('REPORTS_NOTIFICATIONS', $id );
        }
        
        $data->notification = $notification;
        $view = $this->mv_handler->setView("reports_notification_add");
        $view->render($data);
    }
     public function notification_enable_disable($model, $service, $request, $params, $parent_controller){
        if(isset($request->req['task_ids']) && is_array($request->req['task_ids'])){
            if($request->req['state'] =='disable'){
                $state = 0;
            }
            else{
                $state = 1;
            }
            if(!empty($request->req['task_ids'])){
                foreach($request->req['task_ids'] as $task_ids){
                    $form_data = array('id' => $task_ids, 'enable' =>$state);
                    RM_DBManager::update_row('REPORTS_NOTIFICATIONS',$task_ids , $form_data, $form_data);
                    $service->update_edit_delete_cron($task_ids, 'edit');
                }
                
            }
        }
        return;
     }
}