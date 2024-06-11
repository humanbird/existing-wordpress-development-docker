<?php
/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of class_form_controller
 *
 * @author CMSHelplive
 */
class RM_Form_Controller_Addon {
    
    public function manage_cstatus($model,$service, $request, $params, $parent_controller)
    {
       $data= new stdClass();
       $data->forms = RM_Utilities::get_forms_dropdown($service);
       if(!empty($request->req['rm_form_id'])){
           $data->form_id= $request->req['rm_form_id'];
       }else{
           if(!empty($data->forms)){
               $form_ids= array_keys($data->forms);
               if(is_array($form_ids) && isset($form_ids[0])){
                    $data->form_id= $form_ids[0];
               }
           }
       }
       if(empty($data->form_id))
           return;
       
       $form_model= new RM_Forms();
       $form_model->load_from_db($data->form_id);
       $form_options= $form_model->get_form_options();
       
       if(isset($request->req['remove_cstatus']) && is_array($request->req['rm_cstatus_index'])){
           $indexes= $request->req['rm_cstatus_index'];
           foreach($indexes as $index){
               if(isset($form_options->custom_status[$index])){
                   unset($form_options->custom_status[$index]);
                   RM_DBManager::remove_custom_status_from_submissions($data->form_id,$index);
               }
           }
           $form_model->set_form_options($form_options);
           $form_model->update_into_db();
       }
       $data->custom_status= is_array($form_options->custom_status) ? $form_options->custom_status : array();
       $view= $parent_controller->mv_handler->setView('form_cstatus_manager');
       $view->render($data);
    }
    
    public function add_cstatus($model,$service, $request, $params, $parent_controller){
       $data= new stdClass(); 
       $data->form_id= $request->req['rm_form_id'];
       if(empty($data->form_id))
           return;
       
       $form_model= new RM_Forms();
       $form_model->load_from_db($data->form_id);
       $form_options= $form_model->get_form_options();
       
       if($_POST){
           $custom_status= array();
           $custom_status['label']= $request->req['cstatus_label'];
           $custom_status['desc']= $request->req['cstatus_desc'];
           $custom_status['color']= $request->req['cstatus_color'];
           $custom_status['cs_show_frontend']= isset($request->req['cstatus_frontend']) ? 1 : 0;
           $custom_status['cs_action_status_en']= isset($request->req['cs_action_status_en']) ? 1: 0;
           $custom_status['cs_action_status']= $request->req['cs_action_status'];
           $custom_status['cs_email_user_en']= isset($request->req['cs_email_user_en']) ? 1: 0;
           $custom_status['cs_email_user_subject']= $request->req['cs_email_user_subject'];
           $custom_status['cs_email_user_body']= $request->req['cs_email_user_body'];
           $custom_status['cs_email_admin_en']= isset($request->req['cs_email_admin_en']) ? 1: 0;
           $custom_status['cs_email_admin_subject']= $request->req['cs_email_admin_subject'];
           $custom_status['cs_email_admin_body']= $request->req['cs_email_admin_body'];
           $custom_status['cs_action_user_act_en']= isset($request->req['cs_action_user_act_en']) ? 1: 0;
           $custom_status['cs_action_user_act']= isset($request->req['cs_action_user_act']) ? $request->req['cs_action_user_act'] : '';
           $custom_status['cs_note_en']= isset($request->req['cs_note_en']) ? 1: 0;
           $custom_status['cs_note_public']= isset($request->req['cs_note_public']) ? 1:0;
           $custom_status['cs_note_text']= $request->req['cs_note_text'];
           $custom_status['cs_blacklist_en']= isset($request->req['cs_blacklist_en']) ? 1: 0;
           $custom_status['cs_block_email']= isset($request->req['cs_block_email']) ? 1: 0;
           $custom_status['cs_block_ip']= isset($request->req['cs_block_ip']) ? 1:0;
           $custom_status['cs_unblock_email']= isset($request->req['cs_unblock_email']) ? 1: 0;
           $custom_status['cs_unblock_ip']= isset($request->req['cs_unblock_ip']) ? 1:0;
           $custom_status['cs_act_status_specific']= isset($request->req['cs_act_status_specific']) ? $request->req['cs_act_status_specific'] : array();  
           
           
           if(empty($form_options->custom_status)){
               $form_options->custom_status= array();
           }

           $cstatus_id= isset($request->req['cstatus_id']) ? absint($request->req['cstatus_id']) : 0;
           
           if(!empty($form_options->custom_status) && !isset($request->req['cstatus_id'])){
               $form_options->custom_status[]= $custom_status;
           }
           else if($cstatus_id>=0){
               $form_options->custom_status[$cstatus_id]= $custom_status;
           }
           $form_model->set_form_options($form_options);
           $form_model->update_into_db();
           RM_Utilities::redirect ('?page=rm_form_manage_cstatus&rm_form_id='.$data->form_id);
           die;
       }
       $data->new= true;
       if(isset($request->req['cstatus_id'])){
           $cstatus_id= absint($request->req['cstatus_id']);
           $data->new= false;
           if(is_array($form_options->custom_status)){
           foreach($form_options->custom_status as $key=>$custom_status){
              if($cstatus_id==$key){
                  $data->custom_status= $custom_status; 
                  break;
              }  
            } 
          }
       }
       else
           $cstatus_id=0;
       
       $data->cstatus_id= $cstatus_id;
       $data->form_options= $form_options;
       $view= $parent_controller->mv_handler->setView('cstatus_add');
       $view->render($data);
    }
    
    public function metabundle($model, $service, $request, $params, $parent_controller){
        $data= new stdClass();
        $view = $parent_controller->mv_handler->setView('metabundle');
        $view->render($data);
    }
    
}