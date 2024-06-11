<?php

/**
 * Model class for submissions
 * 
 * @author cmshelplive
 */
class RM_Submissions_Addon
{
    
    public function add_new_filter()
    {
        $url = $_POST['url'];
        $name = sanitize_text_field($_POST['name']);
        $gopts = new RM_Options;
        $filters = $gopts->get_value_of('rm_submission_filters');
        if(!empty($filters))
            $filters = maybe_unserialize($filters);
        else
            $filters = array();
       
       if(isset($filters[$name])){
             echo 'NAME_EXIST' ;die;
       }
       if(in_array($url, $filters))
       {
           echo 'URL_EXIST' ;die;
       }
       $filters[$name]= $url;
       $filters = !is_array($filters) ? array() : $filters;
       $filters[$name]= $url;
       $filters=  maybe_serialize($filters);
       $gopts->set_value_of('rm_submission_filters', $filters);
     
       echo 'saved';die;
      
    }
    
    public function export_submissions(){
        $service = new RM_Services();
        $submissions = $service->get_submissions_to_export(null, explode(",",(string)$_GET['sub_ids']), absint($_GET['form_id']));
        $csv = $service->create_csv($submissions);
        
        $service->download_file($csv);
        unlink($csv) or die(__("Can not unlink file",'registrationmagic-addon'));
    }

    public function sort_saved_filters() {
        $gopts = new RM_Options;
        $new_order = array();
        foreach($_POST['names'] as $key => $value) {
            $new_order[$value] = $_POST['urls'][$key];
        }
        $gopts->set_value_of('rm_submission_filters', $new_order);
        wp_send_json_success();
    }

    public function delete_filter()
    {
       $url=$_POST['url'];
       $gopts= new RM_Options;
       $filters=$gopts->get_value_of('rm_submission_filters');
        
       if($filters != null)
       $filters=  maybe_unserialize($filters);
       if(in_array($url, $filters))
       {
           $filters = array_diff($filters, array($url));
       }
       $filters=  maybe_serialize($filters);
       
       $gopts->set_value_of('rm_submission_filters', $filters);
     
       echo 'Deleted';die;
      
    }
    
    public function get_subs_counts($url)
    {
        $request = new stdClass;
        parse_str($url,$req);

        $request->req=$req;
        $service=new RM_Submission_Service;
        $filter= new RM_Submission_Filter($request,$service);
     
        $total_records = RM_DBManager::get_submissions($filter,$filter->get_form(),"count(*) as count");

        if(count($total_records)>0)
            return $total_records[0]->count;
        else
            return 0;
    }
    
    public function get_note_status($parent_model)
    {
        $return=array('note'=>0,
                      'message'=>0 );
        $service=new RM_Services;
        $parent_sub_id = $service->get_oldest_submission_from_group($parent_model->get_submission_id());
        $note = $service->get('NOTES', array('submission_id' => $parent_sub_id), array('%d'), 'results', 0, 99999, '*', null, true);                
     if(isset($note))
     {
         foreach($note as $nt){
           
             $note_options=  maybe_unserialize ($nt->note_options);
             if(!isset($note_options->type) || $note_options->type == 'note' || $note_options->type == 'notification')
                 $return['note']=1;
             else
                 $return['message']=1;
         }
     }
       return $return;
    }
    
    function get_modified_by($parent_model) {
        return $parent_model->modified_by;
    }
 
    function set_modified_by($modified_by,$parent_model) {
        $parent_model->modified_by = $modified_by;
    }
   
    function get_is_read($parent_model) {
        return $parent_model->is_read;
    }

    function set_is_read($is_read,$parent_model) {
        $parent_model->is_read = $is_read;
    }

}
