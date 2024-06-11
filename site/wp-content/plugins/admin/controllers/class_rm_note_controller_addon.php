<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of class_rm_note_controller
 *
 * @author CMSHelplive
 */
class RM_Note_Controller_Addon
{

    public function add($model, $service, $request, $params, $parent_controller)
    {
        if (isset($request->req['rm_submission_id']) && $request->req['rm_submission_id'])
            if ($parent_controller->mv_handler->validateForm("add-note"))
            {
                $model->set($request->req);
               
                if (isset($request->req['rm_note_id']))
                {
                    $service->update($request->req['rm_note_id']);
                    
                } else
                {
                    $service->add();
                }
                $service->notify_users($model);
                if(isset($request->req['rm_redirection']) && $request->req['rm_redirection']!=''){
                    RM_Utilities::redirect('?page='.$request->req['rm_redirection'].'&rm_submission_id=' . $request->req['rm_submission_id']);
                }
                RM_Utilities::redirect('?page=rm_submission_view&rm_submission_id=' . $request->req['rm_submission_id']);
            } else
            {
                if (isset($request->req['rm_note_id']))
                    $model->load_from_db($request->req['rm_note_id']);

                $data = new stdClass();
                $data->model = $model;
                $data->redirectpage ='rm_submission_view'; 
                if(isset($request->req['rm_redirection'])){
                    $data->redirectpage = $request->req['rm_redirection'];
                }
                $data->type=isset($request->req['rm_note_type'])?$request->req['rm_note_type']:null;
                $data->submission_id = $request->req['rm_submission_id'];
                $view = $parent_controller->mv_handler->setView('add_note');
                $view->render($data);
            } else
            throw new InvalidArgumentException("Invalid Submission id in " . __CLASS__ . "::" . __FUNCTION__);
    }

    public function delete($model, $service, $request, $params)
    {
        if (isset($request->req['rm_note_id']))
            $service->remove($request->req['rm_note_id'],'NOTES');
        
        if(isset($request->req['rm_redirection']) && $request->req['rm_redirection']!=''){
            RM_Utilities::redirect('?page='.$request->req['rm_redirection'].'&rm_submission_id=' . $request->req['rm_submission_id']);
        }
        RM_Utilities::redirect('?page=rm_submission_view&rm_submission_id=' . $request->req['rm_submission_id']);
    }

}