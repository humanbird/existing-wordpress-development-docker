<?php

/**
 * 
 */

class RM_Analytics_Controller_Addon
{

    public function show_field($model, RM_Analytics_Service $service, $request, $params, $parent_controller)
    {
        $data = new stdClass;
        
        $data->forms = RM_Utilities::get_forms_dropdown($service);

        if(isset($request->req['rm_form_id']))
        {
            $data->current_form_id = $request->req['rm_form_id'];            
        }
        else
        {
            //Get first form's id in this case
             reset($data->forms);
             $data->current_form_id = (string)key($data->forms);
        }
        $failed_submission = (int)$service->count('STATS', array('form_id' => (int)$data->current_form_id, 'submitted_on' => null));
        $total_visit = (int)$service->count('STATS', array('form_id' => (int)$data->current_form_id));
        $data->total_entries =  $total_visit - $failed_submission;
        $data->field_stat = $service->get_field_stats($data->current_form_id);
        $view = $parent_controller->mv_handler->setView("field_analytics");
        $view->render($data);
    }

    public function calculate_form_stats($form_id, $service)
    {
        $data = new stdClass;
        
        $total_entries =  (int)$service->count('STATS', array('form_id' => (int)$form_id));

       //Average and failure rate
        $failed_submission = (int)$service->count('STATS', array('form_id' => (int)$form_id, 'submitted_on' => null));
        
        if($total_entries != 0 )
            $data->failure_rate = round((double)$failed_submission*100.00/(double)$total_entries, 2);
        else
            $data->failure_rate = 0.00;

        $data->avg_filling_time = $service->get_average_filling_time((int)$form_id);
        
        $banned_submission = (int)$service->count('STATS', array('form_id' => (int)$form_id, 'submitted_on' => 'banned'));        
        
        //Total = Successful + Failed + Banned
        $data->total_entries = $total_entries;
        $data->failed_submission = $failed_submission;
        $data->banned_submission = $banned_submission;
        $data->successful_submission = $total_entries - $failed_submission - $banned_submission;

        $browser_stats = $service->get_browser_usage($form_id);  

        //$browser_stats->browser_usage['Other'] = $total_entries - $browser_stats->total_known_browser_usage;
        //$data->browser_usage = $browser_stats->browser_usage;
       
       // $browser_stats->browser_submission['Other'] = $total_entries - $failed_submission - $browser_stats->total_known_browser_submission;
        $data->browsers = $browser_stats->browsers;//browser_submission;
        $data->browsers['Other']->visits = $total_entries - $browser_stats->total_known_browser_usage;
        $data->browsers['Other']->submissions = $total_entries - $failed_submission - $browser_stats->total_known_browser_submission;
        return $data;
    }
    
}