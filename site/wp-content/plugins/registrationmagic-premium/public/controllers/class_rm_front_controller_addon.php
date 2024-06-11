<?php

class RM_Front_Controller_Addon {
    
    public function user_list($model, $service, $request, $params, $parent_controller){ 
       $options= $params['attribute'];
       $data= new stdClass();
       
       if(!empty($options['timerange'])):
           $data->timerange= $options['timerange'];
       else:
           $data->timerange= 'all';
       endif;
       
       if(!empty($options['form_id'])):
           $data->form_id= (int) $options['form_id'];
       else:
           $data->form_id= 0;
       endif;
       
       $users= $service->get_users($request,$options);
       $data->users= $users; 
       
       // Check if it is ajax request
        if(isset($request->req['action']) && $request->req['action']=='rm_load_front_users'):
            $response= new stdClass;
            $response->users= $users;
            $response->page_number= (int) $request->req['page_number'] +1;
            echo json_encode($response);
            die;
        endif;
       
       $view = $parent_controller->mv_handler->setView('user_list',true);
       return $view->read($data);
   }
   
   public function get_inbox_data($user_email, $service, $request, $params)
    {
        $data = new stdClass;
        
        //For pagination
        $entries_per_page_inbox = 20;
        $req_page_inbox = (isset($request->req['rm_reqpage_inbox']) && $request->req['rm_reqpage_inbox'] > 0) ? $request->req['rm_reqpage_inbox'] : 1;
        $offset_inbox = ($req_page_inbox - 1) * $entries_per_page_inbox;
        $data->mails = $service->get_emails_by_resp($user_email, $entries_per_page_inbox, $offset_inbox);
        $data->total_entries_inbox = $total_entries_inbox = $service->get_email_count($user_email);
        $data->total_pages_inbox = (int) ($total_entries_inbox / $entries_per_page_inbox) + (($total_entries_inbox % $entries_per_page_inbox) == 0 ? 0 : 1);
        $data->curr_page_inbox = $req_page_inbox;
        $data->total_unread_inbox = $service->get_email_unread_count($user_email);
        return $data;
    }

}